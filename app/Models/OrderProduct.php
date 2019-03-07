<?php

namespace App\Models;

use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderProduct extends BaseModel
{
    protected $table = 'order_products';
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'product_id',
        'disabled',
        'price',
        'type',
        'comment',
        'product_option_id',
        'cost',
        'cost_actual'
    ];

    public static function tableName()
    {
        return with(new static)->getTable();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Получаем офферов для данного заказа
     * @param int $orderId ID заказа
     * @return object
     */
    function getProductsByOrderId($orderId, $subProjectId)
    {
        $products = DB::table($this->table . ' AS o')
            ->select('o.id AS ooid', 'of.id', 'of.title', 'o.disabled',
                'o.type', 'o.price', 'o.comment', 'of.weight', 'po.value AS option', 'of.type as productType')
            ->join('products AS of', 'o.product_id', '=', 'of.id')
            ->leftJoin('product_options AS po', 'o.product_option_id', '=', 'po.id')
            ->where('o.order_id', $orderId)
            ->get();

        if ($products->isNotEmpty()) {
            $negative = Project::where('negative', 1)
                ->where('id', $subProjectId)
                ->exists();

            if (!$negative && $subProjectId) {
                $storage = StorageContent::select(DB::raw('COUNT(id) AS amount'), 'product_id')
                    ->whereIn('product_id', $products->pluck('id'))
                    ->where('project_id', $subProjectId)
                    ->where('amount', '>', 0)
                    ->groupBy('product_id')
                    ->get()
                    ->keyBy('product_id');

                $count = DB::table(Order::tableName() . ' AS o')
                    ->select(DB::raw('COUNT(o.id) AS amount'), 'op.product_id')
                    ->leftJoin('order_products AS op', 'op.order_id', '=', 'o.id')
                    ->where('o.moderation_id', '>', 0)
                    ->where('o.target_status', 1)
                    ->where('o.final_target', 0)
                    ->whereIn('op.product_id', $products->pluck('id'))
                    ->get()
                    ->keyBy('product_id');
            }

            foreach ($products as $product) {
                $storageAmount = $storage[$product->id]->amount ?? 0;
                $countOrders = $count[$product->id]->amount ?? 0;
                $product->storageAmount = $storageAmount - $countOrders;

                if ($negative) {//если можно уходить в минус выводим что товары есть на складе
                    $product->storageAmount = 10;
                }
            }
        }

        return $products;
    }

    /**
     * @param $productId
     * @param $subProjectId
     * @param $price
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object
     */
    function getProductsByOrderIdLocked($productId, $subProjectId, $price)
    {
        $product = DB::table(Product::tableName() . ' AS p')
            ->select('p.id', 'p.title')->where('id', $productId)
            ->first();

        if ($product) {
            $negative = Project::where('negative', 1)
                ->where('id', $subProjectId)
                ->exists();

            if (!$negative && $subProjectId) {
                $storage = StorageContent::select(DB::raw('COUNT(id) AS amount'), 'product_id')
                    ->where('product_id', $productId)
                    ->where('project_id', $subProjectId)
                    ->where('amount', '>', 0)
                    ->groupBy('product_id')
                    ->get()
                    ->keyBy('product_id');
            }

            $storageAmount = $storage[$product->id]->amount ?? 0;
            $countOrders = $count[$product->id]->amount ?? 0;
            $product->storageAmount = $storageAmount - $countOrders;
            $product->price = $price;

            if ($negative) {//если можно уходить в минус выводим что товары есть на складе
                $product->storageAmount = 10;
            }
        }

        return $product;
    }

    /**
     * Добавляем дополнительные товары
     * @param int $orderId ID заказа
     * @param int $offerId ID offer
     * @param int $price цена
     * @return bool
     */
    function addOrderOffers($orderId, $productId, $price)
    {
        $price = (int)$price;
        if ($price <= 0) {
            exit;
        }


        $type = DB::table('offers_products AS op')
            ->leftJoin('orders AS o', 'o.offer_id', '=', 'op.offer_id')
            ->where('op.product_id', $productId)
            ->where('o.id', $orderId)
            ->value('op.type');

        DB::table($this->table)->insert([
            'order_id'   => $orderId,
            'product_id' => $productId,
            'price'      => $price,
            'type'       => $type ? $type : 3,
        ]);
        return DB::table($this->table)->where('order_id', $orderId)
            ->where('disabled', 0)
            ->sum('price');
    }

    /**
     * удаляем товар
     * id = order_products.id
     */
    public function deleteProductFromOrder($id, $orderModel, $orderLogModel)
    {
        $product = DB::table($this->table)
            ->where('id', $id)
            ->first();

        $res['order'] = Order::select('id', 'moderation_id', 'target_user', 'target_status')->find($product->order_id);

        if (!$product->type || $res['order']->moderation_id) {
            DB::table($this->table)->where('id', $id)->update(['disabled' => 1]);
            $res['disabled'] = true;
        } else {
            DB::table($this->table)->where('id', $id)->delete();
            $res['success'] = true;
        }

        $res['price'] = DB::table($this->table)->where('order_id', $product->order_id)
            ->where('disabled', 0)
            ->sum('price');

        $orderModel->changeAllPriceAndDateChange($product->order_id, $res['price']);
        $name = DB::table('products')
            ->where('id', $product->product_id)
            ->value('title');
        $orderLogModel->addOrderLog($product->order_id, 'Товар ' . $name . " был удален");
        $res['message'] = trans('alerts.record-successfully-deleted');
        return $res;
    }

    /**
     * сохраняем товары для заказа
     */
    public function saveProducts($data, $orderId, $ordersLogModel)
    {
        if ($data) {
            $ids = [];
            foreach ($data as $datum) {
                $ids[] = $datum['id'];
            }
            $oldProducts = collect(DB::table($this->table . ' AS oo')
                ->select('oo.id', 'oo.type', 'oo.order_id', 'p.title', 'oo.price')
                ->leftJoin('products AS p', 'oo.product_id', '=', 'p.id')
                ->whereIn('oo.id', $ids)
                ->where('oo.order_id', $orderId)
                ->get())->keyBy('id');
            $log = '';
            foreach ($data as $product) {
                $updateData = [];
                if (isset($product['price'])) {
                    $updateData['price'] = $product['price'];
                }
                if (isset($product['up1'])) {
                    $updateData['type'] = 1;//up sell
                } elseif (isset($product['up2'])) {
                    $updateData['type'] = 2;//up sell2
                } elseif (isset($product['cross'])) {
                    $updateData['type'] = 4;//cross sell
                } elseif (isset($product['cross2'])) {
                    $updateData['type'] = 5;//cross sell 2
                }
                if (isset($oldProducts[$product['id']])) {
                    if (!$oldProducts[$product['id']]->type) {//для товара с которым заказ зашел
                        $updateData['type'] = 0;
                    } else if ($oldProducts[$product['id']]->type != 3 && !(isset($updateData['type']))) {// если у доп. товара убрали up
                        $updateData['type'] = 3;//товар без type
                    }
                }
                if ($updateData) {
                    DB::table($this->table)
                        ->where('id', $product['id'])
                        ->update($updateData);

                    if (isset($oldProducts[$product['id']])) {
                        if (isset($updateData['type'])) {
                            $status = $this->getStatus($updateData['type']);
                            if ($status) {
                                $log .= $oldProducts[$product['id']]->title . ' -> ' . $status . '<br>';
                            }
                            if ($updateData['type'] == 3) {
                                $log .= $oldProducts[$product['id']]->title . ' убрали ' . $this->getStatus($oldProducts[$product['id']]->type) . '<br>';
                            }
                        }
                        if (isset($updateData['price']) && $updateData['price'] != $oldProducts[$product['id']]->price) {
                            $log .= $oldProducts[$product['id']]->title . ' новая цена -> ' . $updateData['price'] . '<br>';
                        }
                    }
                }
            }

            if ($log) {
                $ordersLogModel->addOrderLog($orderId, $log);
            }

        }

        return DB::table($this->table)->where('order_id', $orderId)
            ->where('disabled', 0)
            ->sum('price');
    }

    /**
     * сохраняем товары для заказа
     */
    public function saveSendingProducts($data, $orderId, $ordersLogModel)
    {
        if ($data) {
            $ids = [];
            foreach ($data as $datum) {
                $ids[] = $datum['id'];
            }
            $oldProducts = collect(DB::table($this->table . ' AS oo')
                ->select('oo.id', 'oo.type', 'oo.order_id', 'p.title', 'oo.price')
                ->leftJoin('products AS p', 'oo.product_id', '=', 'p.id')
                ->whereIn('oo.id', $ids)
                ->where('oo.order_id', $orderId)
                ->get())->keyBy('id');
            $log = '';
            foreach ($data as $product) {
                $updateData = [];
                if (isset($product['price'])) {
                    $updateData['price'] = $product['price'];
                }
                if ($updateData) {
                    DB::table($this->table)
                        ->where('id', $product['id'])
                        ->update($updateData);

                    if (isset($oldProducts[$product['id']])) {
//                        if (isset($updateData['type'])) {
//                            $status = $this->getStatus($updateData['type']);
//                            if ($status) {
//                                $log .= $oldProducts[$product['id']]->title . ' -> ' . $status . '<br>';
//                            }
//                            if ($updateData['type'] == 3) {
//                                $log .= $oldProducts[$product['id']]->title . ' убрали ' . $this->getStatus($oldProducts[$product['id']]->type) . '<br>';
//                            }
//                        }
                        if (isset($updateData['price']) && $updateData['price'] != $oldProducts[$product['id']]->price) {
                            $log .= $oldProducts[$product['id']]->title . ' новая цена -> ' . $updateData['price'] . '<br>';
                        }
                    }
                }
            }

            if ($log) {
                $ordersLogModel->addOrderLog($orderId, $log);
            }

        }

        return DB::table($this->table)->where('order_id', $orderId)
            ->where('disabled', 0)
            ->sum('price');
    }

    private function getStatus($type)
    {
        $res = '';
        switch ($type) {
            case 1:
                $res = 'Up Sell';
                break;
            case 2:
                $res = 'Up Sell 2';
                break;
            case 4:
                $res = 'Cross Sell';
                break;
            case 5:
                $res = 'Cross Sell 2';
                break;
        }
        return $res;
    }

    public function addComment($id, $text)
    {
        if (!$id) {
            return false;
        }

        return DB::table($this->table)
            ->where('id', $id)
            ->update(['comment' => $text]);
    }

    /**
     * @param $id integer
     * @param $type string: up_sell, up_sell_2, cross_sell
     * @param $type integer: 1, 2, 4
     */
    public function changeProductType($id, $type, $value = 0)
    {
        $newType = 3;
        $product = $this::find($id);
        if (!$product) {
            return false;
        }
        if ($value) {
            switch ($type) {
                case 'up_sell' :
                    {
                        $newType = 1;
                        break;
                    }
                case 'up_sell_2' :
                    {
                        $newType = 2;
                        break;
                    }
                case 'cross_sell' :
                    {
                        $newType = 4;
                        break;
                    }
                case 'cross_sell_2' :
                    {
                        $newType = 5;
                        break;
                    }
                case '1' :
                    {
                        $newType = 1;
                        break;
                    }
                case '2' :
                    {
                        $newType = 2;
                        break;
                    }
                case '4' :
                    {
                        $newType = 4;
                        break;
                    }
                case '5' :
                    {
                        $newType = 5;
                        break;
                    }
            }
        }
        $result = DB::table($this->table)->where('id', $id)->update(['type' => $newType]);
        if ($result) {
            $orderLogModel = new OrdersLog();
            $productName = Product::find($product->product_id)->title;
            $orderLogModel->addOrderLog($product->order_id, $productName . ' тип был изменен');
        }
        return $result;
    }

    function getOrderOffersById($orderId)
    {

        $result = DB::table($this->table . ' AS o')->select('of.product_id', 'o.price', 'of.title', 'o.comment')
            ->leftJoin('products AS of', 'o.product_id', '=', 'of.id')
            ->where('o.order_id', $orderId)
            ->where('o.disabled', 0)
            ->get();
        $data = [];
        if ($result) {
            foreach ($result as $r) {
                $data[] = [
                    'id'      => $r->product_id,
                    'title'   => $r->title,
                    'price'   => $r->price,
                    'comment' => $r->comment
                ];
            }
        }

        return $data;
    }

    public static function getCountProductByOrder($productIds = [])
    {
        return DB::table(self::tableName() . ' AS op')
            ->select(DB::raw('COUNT(op.id) AS count'), 'op.product_id')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->where('o.final_target', 1)
            ->whereIn('op.product_id', $productIds)
            ->groupBy('op.product_id')
            ->get();
    }

    public static function addProductsByOrder($orderId, $products)
    {
        $price = 0;

        if ($products) {
            $insert = [];

            foreach ($products as $product) {
                $insert[] = [
                    'order_id'   => $orderId,
                    'product_id' => $product['product_id'],
                    'disabled'   => 0,
                    'price'      => $product['product_price'],
                    'type'       => 0
                ];
                $price += $product['product_price'];
            }

            DB::table('order_products')->insert($insert);
        }

        return $price;
    }

    public static function divideOrderCostsAndPrices($request, $price, $order = NULL)
    {
        try {
            $order = isset($request['order_id']) ? Order::find($request['order_id']) : $order;
            $targetValues = json_decode(TargetValue::where('order_id', $order->id)->first()->values);
            $orderProducts = OrderProduct::with('product')->where('order_id', $order->id)->where('disabled', 0)->get();
            //если цена за заказ не совпадает с ценой всего за товары то распределяем разницу пропорционально по товарам
            $orderProductsPrice = OrderRepository::sumOrderProducts($order->id);
            if ($price && $price != $orderProductsPrice && $price > $orderProductsPrice) {
                $diff = $price - $orderProductsPrice;
                $log = '';
                foreach (OrderProduct::where('order_id', $order->id)->where('disabled', 0)->get() as $orderProduct) {
                    $productsCoeff = $orderProduct->price / $orderProductsPrice;
                    $oldPrice = $orderProduct->price;

                    $orderProduct->update(['price' => round($orderProduct->price + $diff * $productsCoeff, 2)]);
                    if ($oldPrice && $oldPrice != $orderProduct->price) {
                        $log .= $orderProduct->product->title . ' новая цена -> ' . $orderProduct->price . '<br>';
                    }
                }
                (new OrdersLog)->addOrderLog($order->id, $log);

            } elseif ($price && $price != $orderProductsPrice && $price < $orderProductsPrice) {
                $diff = $orderProductsPrice - $price;
                $log = '';
                foreach ($orderProducts as $orderProduct) {
                    $productsCoeff = $orderProduct->price / $orderProductsPrice;
                    $oldPrice = $orderProduct->price;

                    $orderProduct->update(['price' => round($orderProduct->price - $diff * $productsCoeff, 2)]);
                    if ($oldPrice && $oldPrice != $orderProduct->price) {
                        $log .= $orderProduct->product->title . ' новая цена -> ' . $orderProduct->price . '<br>';
                    }
                }
                (new OrdersLog)->addOrderLog($order->id, $log);
            }

            isset($request['approve']['cost']) ? ($cost = $request['approve']['cost']) : (isset($request['cost']) ? $cost = $request['cost'] : $cost = 0);
            isset($request['approve']['cost_actual']) ? ($costActual = $request['approve']['cost_actual']) : (isset($request['cost_actual']) ? $costActual = $request['cost_actual'] : $costActual = 0);

            if (isset($targetValues->cost) && $cost && $targetValues->cost != $cost) {
                foreach ($orderProducts as $orderProduct) {
                    $productsCoeff = $orderProduct->price / $orderProductsPrice;
                    $orderProduct->update(['cost' => round($cost * $productsCoeff, 2)]);
                }
            }
            if (isset($targetValues->cost) && $costActual && $targetValues->cost_actual != $costActual) {
                foreach ($orderProducts as $orderProduct) {
                    $productsCoeff = $orderProduct->price / $orderProductsPrice;
                    $orderProduct->update(['cost_actual' => round($costActual * $productsCoeff, 2)]);
                }
            }
        } catch (\Exception $exception) {
        }
    }
}
