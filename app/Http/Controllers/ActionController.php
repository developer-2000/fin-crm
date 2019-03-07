<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\OrdersPass;
use App\Models\Pass;
use App\Models\StorageContent;
use App\Models\StorageTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ProcStatus;

class ActionController extends Controller
{
    /**
     * @param Request $request
     */
    public function runActionAjax(Request $request)
    {
        $this->validate($request, [
            'action' => 'required|string',
            'orders' => 'required',
            'status' => 'required|int'
        ]);


        if (!empty($request->action)) {
            switch ($request->action) {
                case 'sent': //отправлен
                    $orders = self::runSentAction($request->all());
                    if (!empty($orders)) {
                        return response()->json(['success' => true, 'orders' => $orders]);
                    } else {
                        return response()->json(['success' => false]);
                    }
                    break;
                case 'to_print':
                    $status = self::runToPrintAction($request->all());
                    if (isset($status['exist_in_pass'])) {
                        return [
                            'exist_in_pass' => true,
                            'message'       => trans('alerts.order-already-pass-queue') 
                        ];
                    }
                    return response()->json([
                        'success'        => true,
                        'procStatusName' => ProcStatus::find($status)->name,
                        'message'        => trans('alerts.orders-added-queue-print') //Orders has been added in queue to print.
                    ]);
                    break;
                case  'paid_up': //выкуп
                    self::runPaidUp($request->all());
                    break;
                case  'refused': //не выкуп
                    self::runRefused($request->all());
                    break;
                case  'rejected': //отклонен
                    break;
                case  'at_department': //на отделении
                    self::runAtDepartment($request->all());
                    break;
                case  'received': //забран
                    self::runReceived($request->all());
                    break;
                case  'returned': //возврат
                    self::runReturned($request->all());
                    break;
                case  'operation_manual': //корректировка заказа
                    self::operationManual($request->all());
                    break;
                case  'reversal': //корректировка заказа
                    self::runReversal($request->all());
                    break;
                case  'search':
                    self::runSearch($request->all());
                    break;
                case  'claim':
                    self::runClaim($request->all());
                    break;
                case  'dispute':
                    self::runDispute($request->all());
                    break;
            }
        } else {
            self::updateOrderProcStatus($request->orders, $request->status);
        }
    }

    public static function updateOrderProcStatus($orders, $status)
    {
        if ($orders) {
            if ($status instanceof ProcStatus) {
                $newStatusName = $status->name;
                $status = $status->id;
            } else {
                $newStatusName = ProcStatus::find($status)->name ?? $status;
            }

            foreach ($orders as $item) {
                $order = $item;

                if (is_int($item)) {
                    $order = Order::find($item);
                }

                if ($order instanceof Order && $order->proc_status != $status) {
                    $oldStatusName = $order->procStatus->name ?? $order->proc_status;
                    $statusInfo = ['status_id' => $status, 'status_name' => $newStatusName];
                    $order->proc_status = $status;
                    $order->time_status_updated = Carbon::now();
                    if ($order->save()) {
                        (new Order)->getProcessingStatusOrderApi($order->id);
                        (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус был изменен c "' . $oldStatusName . '" на "' . $newStatusName, $statusInfo);
                    }
                }
            }
        }
    }

    // SENT ACTION + Save time_sent to order
    public static function runSentAction($data)
    {
        $newStatus = $data['status'];

        if (!$data['status'] instanceof ProcStatus) {
            $newStatus = ProcStatus::find($data['status']);
        }

        if (!empty($data['orders'])) {
            $orders = Order::whereIn('id', $data['orders'])->get();
            foreach ($orders as $order) {
                $oldProcStatusToPrint = ProcStatus::find($order->proc_status);
                $products = $order->products()->where('disabled', 0)->get();

                if ($products->count()) {
                    foreach ($products as $product) {

                        $positiveQuantity = StorageContent::checkAmountProduct($product->id, $order->subproject_id);
                        if ($positiveQuantity) {
                            $ordersToSent[$product->id] = 1;
                        } else {
                            $ordersToSent[$product->id] = 0;
                        }
                    }
                }

                if (!in_array(0, $ordersToSent)) {

                    foreach ($products as $product) {
                        $sc = StorageContent::where('project_id', $order->subproject_id)
                            ->where('product_id', $product->id)->first();

                        if (!$sc) {
                            $sc = new StorageContent();
                            $sc->project_id = $order->subproject_id;
                            $sc->product_id = $product->id;
                            $sc->hold = 0;
                            $sc->amount = 0;
                        }

                        if ($sc) {
                            $sc->amount += -1; //т.к. в заказе каждого товара по 1 шт
                            $sc->hold += 1;
                            if ($sc->save()) {
                                try {
                                    StorageTransaction::create([
                                        'product_id' => $product->id,
                                        'project_id' => $order->subproject_id,
                                        'user_id'    => auth()->user()->id ?? 0,
                                        'amount1'    => $sc->amount + 1,
                                        'amount2'    => $sc->amount,
                                        'hold1'      => $sc->hold - 1,
                                        'hold2'      => $sc->hold,
                                        'type'       => StorageTransaction::TYPE_SENT,
                                        'moving_id'  => 0,
                                        'order_id'   => $order->id ?? 0
                                    ]);
                                } catch (\Exception $exception) {
                                    (new OrdersLog())->addOrderLog($order->id, $exception->getMessage() . '(' . $product->id . ')');
                                }
                            }
                        }
                    }
                    $arrayOfOrders[$order->id] = ['success' => true];
                    $arrayOfIdsSuccess['success'][] = $order->id;

                    //modify order status
                    $order->proc_status = $data['status'];
                    $order->locked = 1;
                    $order->proc_status_2 = 0;
                    $order->time_sent = Carbon::now();
                    $order->time_status_updated = Carbon::now();
                    if ($order->save()) {
                        (new Order)->getProcessingStatusOrderApi($order->id);
                        $statusInfo = [
                            'status_id'   => $data['status'],
                            'status_name' => $newStatus->name
                        ];
                        (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusToPrint->name . ' на ' . $newStatus->name . '', $statusInfo);
                    }
                } else {
                    $arrayOfOrders[$order->id] = ['success' => false];
                    (new OrdersLog)->addOrderLog($order, 'Статус не был изменен на "' . $newStatus->name . '"');
                }
            }

            //создать проводку если сраница в очереди на печать
            switch (strtok(\URL::previous(), '?')) {
                case route('orders-print'):
                    $ordersToUpdate = Order::whereIn('id', $arrayOfIdsSuccess['success'])->where('pass_send_id', 0)->get();
                    if ($ordersToUpdate->count()) {
                        Pass::where([
                            ['type', 'to_print'],
                            ['user_id', auth()->user()->id],
                            ['active', 1],
                            ['sub_project_id', auth()->user()->sub_project_id]
                        ])
                            ->update(['active' => 0]);

                        $newSendingPass = Pass::create([
                            'type'           => 'sending',
                            'user_id'        => auth()->user()->id,
                            'active'         => 0,
                            'sub_project_id' => auth()->user()->sub_project_id,
                            'comment'        => 'via "In the print queue"'
                        ]);
                        if ($newSendingPass) {
                            foreach ($ordersToUpdate as $orderToUpdate) {
                                $orderToUpdate->pass_send_id = $newSendingPass->id;

                                if ($orderToUpdate->save()) {
                                    OrdersPass::create([
                                        'order_id' => $orderToUpdate->id,
                                        'pass_id'  => $newSendingPass->id
                                    ]);
                                }
                            }
                        }
                    }
                    break;
            }
            return $arrayOfOrders;
        }

    }

    public static function runToPrintAction($data)
    {
        if (!empty($data)) {
            $pass = Pass::where([
                ['active', 1],
                ['type', 'to_print'],
                ['sub_project_id', auth()->user()->sub_project_id],
                ['user_id', auth()->user()->id]
            ])->first();
            if (empty($pass)) {
                $pass = Pass::create([
                    'active'         => 1,
                    'type'           => 'to_print',
                    'sub_project_id' => auth()->user()->sub_project_id,
                    'user_id'        => auth()->user()->id
                ]);
            }
            $orders = Order::whereIn('id', $data['orders'])->get();
            foreach ($orders as $order) {
                // check if order exist in passes
                $existInPass = Pass::where('id', $order->pass_send_id)
                    ->orWhere('id', $order->pass_id)
                    ->orWhere('id', $order->print_id)->first();
                if ($existInPass) {
                    return ['exist_in_pass' => true];
                }

                $oldProcStatusName = $order->procStatus->name;
                $newProcStatusName = ProcStatus::find($data['status'])->name;
                $statusInfo = [
                    'status_id'   => $data['status'],
                    'status_name' => $newProcStatusName
                ];
                $order->proc_status = $data['status'];
                $order->time_status_updated = Carbon::now();
                if ($pass) {
                    $order->print_id = $pass->id;
                }
                $order->print_id = $pass->id;
                if ($order->save()) {
                    (new Order)->getProcessingStatusOrderApi($order->id);
                    (new OrdersLog)->addOrderLog($order->id, 'Процессинг статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
                }
                return $data['status'];
            }
        }
    }

    //PaidUp ACTION + Save time_paid_up to order
    public static function runPaidUp($data)
    {
        if (isset($data['orders'])) {
            foreach ($data['orders'] as $order) {
                if (is_int($order)) {
                    $order = Order::find($order);
                }

                if ($order instanceof Order) {
                    $products = $order->products()->where('disabled', 0)->get();
                    if ($products->isNotEmpty()) {
                        foreach ($products as $product) {
                            $storageContent = StorageContent::where([
                                ['project_id', $order->subproject_id],
                                ['product_id', $product->id]
                            ])->first();
                            if ($storageContent) {
                                $storageContent->hold -= 1;
                                if ($storageContent->save()) {
                                    StorageTransaction::create([
                                        'product_id' => $product->id,
                                        'project_id' => $order->subproject_id,
                                        'user_id'    => auth()->user()->id,
                                        'amount1'    => $storageContent->amount,
                                        'amount2'    => $storageContent->amount,
                                        'hold1'      => $storageContent->hold + 1,
                                        'hold2'      => $storageContent->hold,
                                        'type'       => StorageTransaction::TYPE_RECEIVED,
                                        'moving_id'  => 0,
                                        'order_id'   => $order->id ?? 0
                                    ]);
                                }
                            } else {
                                (new OrdersLog())->addOrderLog($order->id, 'Нет товара (' . $product->title . ') на складе');
                            }
                        }
                        $oldProcStatusName = $order->ProcStatus->name;
                        $newProcStatus = ProcStatus::senderStatuses()->statusesUser()->where('action', 'paid_up')
                            ->first();
                        $order->proc_status = $newProcStatus->id ?? $order->proc_status;
                        $order->locked = 1;
                        $order->final_target = 1;//выкуп
                        $order->time_paid_up = Carbon::now();
                        $order->time_status_updated = Carbon::now();
                        $statusInfo = ['status_id' => $newProcStatus->id, 'status_name' => $newProcStatus->name];
                        if ($order->save()) {
                            (new Order)->getProcessingStatusOrderApi($order->id);
                            (new OrdersLog)->addOrderLog($order->id, 'Процессинг статус был изменен c ' . $oldProcStatusName . ' на ' . $order->procStatus->name . '', $statusInfo);
                            (new OrdersLog())->addOrderLog($order->id, 'Цель "Выкуп"');
                        }
                    }
                }
            }
        }
    }

    //Refused ACTION + Save time_refused to order
    public static function runRefused($data)
    {
        if (isset($data['orders'])) {
            foreach ($data['orders'] as $order) {
                if (is_int($order)) {
                    $order = Order::find($order);
                }

                if ($order instanceof Order) {
                    $products = $order->products()->where('disabled', 0)->get();
                    if ($products->isNotEmpty()) {
                        foreach ($products as $product) {
                            $storageContent = StorageContent::where([
                                ['project_id', $order->subproject_id],
                                ['product_id', $product->id]
                            ])->first();
                            if ($storageContent) {
                                $storageContent->hold -= 1;
                                $storageContent->amount += 1;
                                if ($storageContent->save()) {
                                    StorageTransaction::create([
                                        'product_id' => $product->id,
                                        'project_id' => $order->subproject_id,
                                        'user_id'    => auth()->user()->id,
                                        'amount1'    => $storageContent->amount - 1,
                                        'amount2'    => $storageContent->amount,
                                        'hold1'      => $storageContent->hold + 1,
                                        'hold2'      => $storageContent->hold,
                                        'type'       => StorageTransaction::TYPE_RETURN,
                                        'moving_id'  => 0,
                                        'order_id'   => $order->id ?? 0
                                    ]);
                                }
                            } else {
                                (new OrdersLog())->addOrderLog($order->id, 'Нет товара (' . $product->title . ') на складе');
                            }
                        }
                        $oldProcStatusName = $order->procStatus->name;
                        $newProcStatus = ProcStatus::senderStatuses()->statusesUser()->where('action', 'refused')
                            ->first();
                        $order->proc_status = $newProcStatus->id ?? $order->proc_status;
                        $order->locked = 1;
                        $order->final_target = 2;//не выкуп
                        $order->time_refused = Carbon::now();
                        $order->time_status_updated = Carbon::now();
                        if ($order->save()) {
                            (new Order)->getProcessingStatusOrderApi($order->id);
                            (new OrdersLog())->addOrderLog($order->id, 'Цель "Не выкуп"');
                            $statusInfo = [
                                'status_id'   => $newProcStatus,
                                'status_name' => $newProcStatus->name
                            ];
                            (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatus->name . '', $statusInfo);
                        }
                    }
                }
            }
        }
    }

    public static function runCancelSend($orders)
    {
        $res = [];
        if ($orders) {
            $stage = ProcStatus::senderStatuses()
                ->statusesUser()
                ->where('action', 'sent')
                ->value('stage');

            foreach ($orders as $order) {
                if (is_int(+$order)) {
                    $res[$order] = false;
                    $order = Order::find($order);
                }

                if ($order instanceof Order) {
                    $res[$order->id] = false;
                    $orderStage = $order->procStatus->stage ?? 0;

                    if ($stage > $orderStage) {
                        continue;
                    }

                    $transaction = new Transaction();
                    $transaction->createCancelSendTransaction($order);
                    if ($transaction->save()) {
                        (new OrdersLog())->addOrderLog($order->id, 'Транзакция создана ' . $transaction->id);
                    }

                    $products = $order->products()->where('disabled', 0)->get();
                    if ($products->isNotEmpty()) {
                        foreach ($products as $product) {
                            $storageContent = StorageContent::where([
                                ['project_id', $order->subproject_id],
                                ['product_id', $product->id]
                            ])->first();

                            if (!$storageContent) {
                                $storageContent = new StorageContent();
                                $storageContent->project_id = $order->subproject_id;
                                $storageContent->product_id = $product->id;
                                $storageContent->hold = 0;
                                $storageContent->amount = 0;
                            }

                            if ($storageContent) {
                                $storageContent->hold -= 1;
                                $storageContent->amount += 1;
                                if ($storageContent->save()) {
                                    StorageTransaction::create([
                                        'product_id' => $product->id,
                                        'project_id' => $order->subproject_id,
                                        'user_id'    => auth()->user()->id ?? 0,
                                        'amount1'    => $storageContent->amount - 1,
                                        'amount2'    => $storageContent->amount,
                                        'hold1'      => $storageContent->hold + 1,
                                        'hold2'      => $storageContent->hold,
                                        'type'       => StorageTransaction::TYPE_CANCEL,
                                        'moving_id'  => 0,
                                        'order_id'   => $order->id ?? 0
                                    ]);
                                }
                            }
                        }
                        $oldProcStatusName = $order->procStatus->name;
                        $newProcStatus = ProcStatus::senderStatuses()->statusesUser()
                            ->where('action', 'cancel_send')->first();
                        $order->proc_status = $newProcStatus->id ?? $order->proc_status;
                        $order->locked = 0;
                        $order->pass_send_id = 0;
                        $order->print_id = 0;
                        $order->time_status_updated = Carbon::now();
                        OrdersPass::whereHas('pass', function ($q) {
                            $q->where('type', Pass::TYPE_SENDING);
                        })->where('order_id', $order->id)->delete();

                        $res[$order->id] = $order->save();
                        if ($res[$order->id]) {
                            (new Order)->getProcessingStatusOrderApi($order->id);
                            (new OrdersLog())->addOrderLog($order->id, 'Отменена отправка');
                            $statusInfo = [
                                'status_id'   => $newProcStatus->id,
                                'status_name' => $newProcStatus->name
                            ];
                            (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatus->name . '', $statusInfo);
                        }
                    }
                }
            }
        }

        return $res;
    }

    //At Department Action + save time_at_department to orders
    public static function runAtDepartment($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_at_department = Carbon::now();
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }

    //Received Action + save time_received to orders
    public static function runReceived($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_received = Carbon::now();
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }

    //Search Action
    public static function runSearch($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }

    //Claim Action
    public static function runClaim($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }
    //Dispute Action
    public static function runDispute($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }

    //Returned Action + save time_returned to orders
    public static function runReturned($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_returned = Carbon::now();
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }

    //Operation Manual Action
    public static function operationManual($request)
    {
        foreach ($request['orders'] as $item) {
            $order = Order::find($item);
            $oldProcStatusName = $order->procStatus->name;
            $newProcStatusName = ProcStatus::find($request['status'])->name;
            $order->proc_status = $request['status'];
            $order->time_returned = Carbon::now();
            $order->time_status_updated = Carbon::now();
            if ($order->save()) {
                $statusInfo = [
                    'status_id'   => $request['status'],
                    'status_name' => $newProcStatusName
                ];
                (new Order)->getProcessingStatusOrderApi($order->id);
                (new OrdersLog)->addOrderLog($order->id, 'Статус был изменен c ' . $oldProcStatusName . ' на ' . $newProcStatusName . '', $statusInfo);
            }
        }
    }

    //Reversal Action
    public static function runReversal($request)
    {
        $ordersIds = [];

        foreach ($request['orders'] as $order) {
            $orderProducts = OrderProduct::where('order_id', $order->id)->where('disabled', 0)->get()->toArray();
            $oldProcStatusName = $order->ProcStatus->name;
            $newProcStatus = ProcStatus::senderStatuses()->statusesUser()->where('action', 'paid_up')
                ->first();
            $statusInfo = ['status_id' => $newProcStatus->id, 'status_name' => $newProcStatus->name];
            if ($request['pass_type'] == 'redemption') {
                array_map(function ($product) use ($order, $oldProcStatusName, $statusInfo, $request) {
                    //reversal (сторно) storage
                    $storageContent = StorageContent::where([
                        ['project_id', $order->subproject_id],
                        ['product_id', $product['product_id']]
                    ])->first();

                    if ($storageContent) {
                        $storageContent->hold += 1;
                        if ($storageContent->save()) {
                            StorageTransaction::create([
                                'product_id' => $product['product_id'],
                                'project_id' => $order->subproject_id,
                                'user_id'    => auth()->user()->id,
                                'amount1'    => $storageContent->amount,
                                'amount2'    => $storageContent->amount,
                                'hold1'      => $storageContent->hold - 1,
                                'hold2'      => $storageContent->hold,
                                'type'       => StorageTransaction::TYPE_REVERSAL,
                                'moving_id'  => 0,
                                'order_id'   => $order->id ?? 0
                            ]);
                            $order->pass_id = 0;
                            $order->final_target = 0;
                            $order->proc_status = $request['status'];
                            if ($order->save()) {
                                (new Order)->getProcessingStatusOrderApi($order->id);
                                (new OrdersLog)->addOrderLog($order->id, 'Процессинг статус был изменен c ' . $oldProcStatusName . ' на ' . $order->procStatus->name . '', $statusInfo);
                            }
                        }
                    }
                }, $orderProducts);
            }

            if ($request['pass_type'] == 'no-redemption') {
                array_map(function ($product) use ($order, $oldProcStatusName, $statusInfo, $request) {
                    //reversal (сторно) storage
                    $storageContent = StorageContent::where([
                        ['project_id', $order->subproject_id],
                        ['product_id', $product['product_id']]
                    ])->first();

                    if (!$storageContent) {
                        $storageContent = new StorageContent();
                        $storageContent->project_id = $order->subproject_id;
                        $storageContent->product_id = $product['product_id'];
                        $storageContent->hold = 0;
                        $storageContent->amount = 0;
                    }

                    if ($storageContent) {
                        $storageContent->amount -= 1; //т.к. в заказе каждого товара по 1 шт
                        $storageContent->hold += 1;
                        if ($storageContent->save()) {
                            StorageTransaction::create([
                                'product_id' => $product['product_id'],
                                'project_id' => $order->subproject_id,
                                'user_id'    => auth()->user()->id,
                                'amount1'    => $storageContent->amount + 1,
                                'amount2'    => $storageContent->amount,
                                'hold1'      => $storageContent->hold - 1,
                                'hold2'      => $storageContent->hold,
                                'type'       => StorageTransaction::TYPE_REVERSAL,
                                'moving_id'  => 0,
                                'order_id'   => $order->id ?? 0
                            ]);
                            $order->pass_id = 0;
                            $order->proc_status = $request['status'];
                            if ($order->save()) {
                                (new Order)->getProcessingStatusOrderApi($order->id);
                                (new OrdersLog)->addOrderLog($order->id, 'Процессинг статус был изменен c ' . $oldProcStatusName . ' на ' . $order->procStatus->name . '', $statusInfo);
                            }
                        }
                    }
                }, $orderProducts);
            }
            $ordersIds[] = $order->id;
        }

        if (count($ordersIds)) {
            $newReversalPass = Pass::create([
                'type'           => 'reversal',
                'origin_id'      => $request['pk'],
                'user_id'        => auth()->user()->id,
                'active'         => 0,
                'sub_project_id' => auth()->user()->sub_project_id
            ]);

            if ($newReversalPass) {
                foreach ($ordersIds as $ordersId) {
                    $orderPass = new OrdersPass();
                    $orderPass->pass_id = $newReversalPass->id;
                    $orderPass->order_id = $ordersId;
                    $orderPass->save();
                }
            }
            OrdersLog::addOrdersLog($ordersIds, '<a href="' . route('pass-one', $newReversalPass->id) . '"> Проводка Сторно ' . $newReversalPass->id . '</a>');
        }
    }
}
