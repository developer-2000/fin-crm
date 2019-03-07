<?php

namespace App\Models;

use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class Product extends BaseModel
{
    protected $table = 'products';

    protected $searchableColumns = ['title'];
    public $timestamps = false;

    public function productProjects()
    {
        return $this->hasMany(ProductProject::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'title',
        'product_id',
        'category_id',
        'title',
        'weight',
        'price_cost',
        'status',
        'type'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_products');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

    /**
     * @return Collection
     */
    function getOffersNameNoParent()
    {
        return collect(
            DB::table('offers')->select('id', 'name')
                //->where('parent_id', 0)
                ->orderBy('name')
                ->get()
        )->keyBy('id');
    }

    /**
     * Поиск offers
     * @param int $orderId ID заказа
     * @param int $countryId ID страны
     * @param int $companyId ID компании
     * @param string $search Поиск
     * @return mix
     */
    function search( $projectId, $search, $subProjectId )
    {
        if (strlen(trim($search)) <= 2) {
            return collect();
        }

        $subProjectIds = Project::where('parent_id', $projectId)
            ->pluck('id')
            ->toArray();
        $products = DB::table($this->table . ' AS p')->select('p.id', 'p.title')
            ->where('pp.project_id', $projectId)
            ->where('pp.subproject_id', $subProjectId)
            ->where('p.title', 'like', '%' . $search . '%')
            ->where('pp.status', 1)
            ->leftJoin('product_projects as pp', 'pp.product_id', '=', 'p.id')
            ->groupBy('p.id')
            ->get();

        if ($products->isNotEmpty()) {
            foreach ($products as $product) {
                $product->storage = StorageContent::checkAmountProduct($product->id, $subProjectIds);
            }
        }

        return $products;

//        return DB::table($this->table . ' AS p')->select('p.id', 'p.title')
//            ->leftJoin(StorageContent::tableName() . ' AS sc', 'sc.project_id', '=', 'p.sub_project_id')
//            ->where('p.project_id', $projectId)
////            ->whereIn('p.sub_project_id', $subProjectIds)
////            ->where('sc.amount', '>', 0)
//            ->where('p.title', 'like', $search . '%')
//            ->where('status', 'on')
//            ->get();
    }

    /**
     * Получаем оффера с parent_id = 0
     */
    function getOffersAll()
    {
        return DB::table('offers')->get();
    }

    /**
     * Получаем одного оффера
     * @param int $id ID оффера
     * @return array
     */
    function getOneOffer( $id )
    {
        $result = DB::table('offers AS o')->select('o.id', 'o.name', 'o.offer_id', 'o.partner_id')
            ->where('o.id', $id)
            ->first();
//        if ($result) {
//            $result->products = DB::table('offers_products AS rp')->select('p.id', 'p.title', 'rp.type', 'rp.price')
//                ->leftJoin('products AS p', 'rp.product_id', '=', 'p.id')
//                ->where('rp.offer_id', $id)
//                ->get();
        return $result;
        //   }
        abort(404);
    }

    /**
     * Получаем offer по коду (Dev)
     * @param string $code Код
     * @return int
     */
    function getOfferByVendorCode( $code )
    {
        return DB::table($this->table)->where('vendor_code', $code)
            ->value('id');
    }

    /* Проверяем к той ли компании принадлежит предложение */
    function checkOffersCompany( $ids, $idCompany )
    {
        $result = DB::table($this->table)->select('id')
            ->whereIn('id', $ids)
            ->where('id_company', $idCompany)
            ->get();
        if (count($result) != count($ids)) return false;
        return true;
    }

    public function getOffersFromOffers()
    {
        return DB::table('offers')
            ->select('id', 'name', 'partner_id', 'offer_id')
            ->get();
    }

    public function changeOffers( $id, $data )
    {
        $result = [
            'errors' => 0,
            'status' => 0
        ];
        $validator = \Validator::make($data, [
            'name' => 'required|max:255|min:2',
            'project_id' => 'required|numeric',
            'offer_id' => 'required|numeric'
        ]);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            return $result;
        }

        $result['status'] = DB::table('offers')
            ->where('id', $id)
            ->update($data);
        return $result;
    }

    public function searchProduct( $offerId, $search )
    {
        if (!$search || !$offerId) {
            return false;
        }
        $projectId = DB::table('offers')
            ->where('id', $offerId)
            ->value('project_id');
        return DB::table($this->table . ' AS p')
            ->where('pp.project_id', $projectId)
            ->where('p.title', 'like', $search . '%')
            ->leftJoin('product_projects as pp', 'pp.id', '=', 'p.id')
            ->get();
    }

    /**
     * получаем рекомендованные товары оффера сгруппированые по типу
     */
    public function getRecommendedProductsGroupByType( $offerId, $geo )
    {
        $products = DB::table('offers_products AS rp')
            ->select('rp.price', 'rp.type', 'rp.offer_id', 'rp.product_id', 'p.title AS name')
            ->leftJoin('products AS p', 'p.id', '=', 'rp.product_id')
            ->where('rp.offer_id', $offerId)
            ->where('rp.geo', $geo)
            ->orderBy('rp.type', 'asc')
            ->get();

        $result = [];
        if ($products) {
            foreach ($products as $product) {
                $result[$product->type][] = $product;
            }
        }
        return $result;
    }

    function getAllOffersByFilters( $filter, $page )
    {
        $skip = 0;
        $countOnePage = 100;
        if ($page) {
            $skip = ($page - 1) * $countOnePage;
        }
        $offers = DB::table('offers AS o')->select('o.id', 'o.name', 'o.offer_id', 'c.name AS project')
            ->leftJoin('partners AS c', 'o.partner_id', '=', 'c.id');

        if (auth()->user()->company_id) {
            $offers->where('company_id', auth()->user()->company_id);
        }

        if ($filter['name']) {
            $offers = $offers->where('o.name', 'like', $filter['name'] . '%');
        }
        if ($filter['partner']) {
            $offers = $offers->where('o.partner_id', $filter['partner']);
        }
        $count = $offers->count();
        $offers = collect($offers->skip($skip)
            ->take($countOnePage)
            ->get())->keyBy('id');

        $ids = [];
        if ($offers) {
            foreach ($offers as $offer) {
                $ids[] = $offer->id;
            }
        }

        $upCross = DB::table('offers_products')
            ->select(
                DB::raw('COUNT(type) as count'),
                'type',
                'offer_id'
            )
            ->whereIn('offer_id', $ids)
            ->groupBy('type')
            ->groupBy('offer_id')
            ->get();

        if ($upCross) {
            foreach ($upCross as $value) {
                if (isset($offers[$value->offer_id])) {
                    switch ($value->type) {
                        case 1:
                            {
                                $offers[$value->offer_id]->up_sell = $value->count;
                                break;
                            }
                        case 2:
                            {
                                $offers[$value->offer_id]->up_sell_2 = $value->count;
                                break;
                            }
                        case 4:
                            {
                                $offers[$value->offer_id]->cross_sell = $value->count;
                                break;
                            }
                    }
                }
            }
        }

        $paginationModle = new Pagination;
        return [
            'data' => $offers,
            'pagination' => $paginationModle->getPagination($page, $count, $countOnePage),
        ];
    }

    function getAllOffersByFiltersColdCalls( $filter )
    {
        $userWithoutCompany = DB::table('users')->where([['company_id', 0], ['id', auth()->user()->id]])->get();
        if (!empty($userWithoutCompany)) {
            $offers = DB::table('offers AS o')
                ->select('o.id', 'o.partner_id','co.name as company_name', 'o.name', 'u.company_id', 'o.company_id', 'o.offer_id', 'c.name AS project', 'o.offer_id', 'o.status')
                ->leftJoin('partners AS c', 'o.partner_id', '=', 'c.id')
                ->leftJoin('companies AS co', 'co.id', '=', 'o.company_id')
                ->leftJoin('users AS u', 'u.company_id', '=', 'o.company_id');
        } else {
            $offers = DB::table('offers AS o')
                ->select('o.id', 'o.partner_id', 'co.name as company_name', 'o.name', 'u.company_id', 'o.company_id', 'o.offer_id', 'c.name AS project', 'o.offer_id', 'o.status')
                ->leftJoin('partners AS c', 'o.partner_id', '=', 'c.id')
                ->leftJoin('companies AS co', 'co.id', '=', 'o.company_id')
                ->leftJoin('users AS u', 'u.company_id', '=', 'o.company_id')->where('u.id', auth()->user()->id);
        }

        if ($filter['name']) {
            $offers = $offers->where('o.name', 'like', $filter['name'] . '%');
        }

        if ($filter['project']) {
            $offers = $offers->where('o.partner_id', $filter['project']);
        }
        $offers = $offers->groupBy('o.id')->paginate(20);

        foreach ($offers as $offer) {
            $offer->count_types = collect(DB::select("SELECT  COUNT(IF( type = 0, 1, NULL)) AS product, 
            COUNT(IF( type = 1, 1, NULL)) AS up_1, COUNT(IF( type = 2, 1, NULL)) AS up_2, COUNT(IF( type = 4, 1, NULL)) 
            AS cross_sell 
            FROM offers_products
            WHERE offer_id = " . $offer->id))->first();
        }
        return $offers;
    }

    public function addProductOffers( $data, $offerId )
    {
        $result = [
            'errors' => false,
            'success' => false,
        ];
        $validator = \Validator::make($data, [
            'product' => 'required|numeric|min:0',
            'type' => 'numeric|min:0',
            'geo' => 'required|max:5',
            'price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            return $result;
        }
        $insert = [
            'offer_id' => $offerId,
            'type' => $data['type'],
            'geo' => mb_strtolower($data['geo']),
            'product_id' => $data['product'],
            'price' => $data['price'],
        ];
        $oldProducts = DB::table('offers_products')
            ->where([
                'offer_id' => $offerId,
                'type' => $data['type'],
                'geo' => mb_strtolower($data['geo']),
                'product_id' => $data['product'],
            ])
            ->first();
        if ($oldProducts) {
            $result['success'] = DB::table('offers_products')
                ->where('id', $oldProducts->id)
                ->update($insert);
        } else {
            $result['success'] = DB::table('offers_products')
                ->insert($insert);
        }

        return $result;
    }

    public function addProductsOffersColdCall( $data, $offerId )
    {
        $geo = Offer::where('id', $offerId)->value('geo');

        $products = explode(',', $data['products-select2']);

        $result = [
            'errors' => false,
            'success' => false,
        ];
        $validator = \Validator::make($data, [
            'products-select2' => 'required|min:1',
            'type' => 'numeric|min:0',
            'price' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            return $result;
        }
        $inserts = [];
        foreach ($products as $product) {

            $inserts[] = [
                'offer_id' => $offerId,
                'type' => $data['type'],
                'geo' => $geo,
                'product_id' => $product,
                'price' => $data['price'],
            ];
        }

        $addData = [$offerId, $data['type'], $geo, $products];
        $oldProducts = NULL;
        $oldProducts = DB::table('offers_products')
            ->where(function ( $q ) use ( $addData ) {
                $q->where([
                    'offer_id' => $addData[0],
                    'type' => $addData[1],
                    'geo' => $addData[2],

                ])->whereIn('product_id', $addData[3]);
            })->get();

        if ($oldProducts->count()) {
            foreach ($oldProducts as $key => $oldProduct) {
                $result['success'] = DB::table('offers_products')
                    ->where('id', $oldProduct->id)
                    ->update($inserts[$key]);
            }
        } else {

            foreach ($inserts as $insert) {
                $result['success'] = DB::table('offers_products')
                    ->insert($insert);
            }
        }

        return $result;
    }

    public function getAllProductsOfferGroupByType( $id )
    {
        $products = DB::table('offers_products AS op')
            ->select('op.price', 'op.id', 'op.geo', 'p.title AS product', 'op.type')
            ->leftJoin($this->table . ' AS p', 'p.id', '=', 'op.product_id')
            ->where('op.offer_id', $id)
            ->orderBy('op.type')
            ->get();
        $res = [];
        if ($products) {
            foreach ($products as $product) {
                $res[$product->type][] = $product;
            }
        }
        return $res;
    }

    public function deleteProductFromOffer( $id )
    {
        return DB::table('offers_products')
            ->where('id', $id)
            ->delete();
    }


    public function searchOfferByWord( $term, $partnerId = [] )
    {
        $offers = DB::table('offers')
            ->select('id', 'name', 'partner_id')
            ->where('name', 'LIKE', '%' . $term . '%');
        $partnerIds = [];
        if ($partnerId) {
            if (is_array($partnerId)) {
                $projectString = trim(implode(' ', $partnerId));
                if (!empty($projectString)) {
                    $offers->whereIn('partner_id', $partnerId);
                }
            } elseif (auth()->user()->project_id && !$partnerIds) {
                $offers->where('partner_id', auth()->user()->project_id);
            } else {
                $offers->where('partner_id', $partnerIds);
            }
        }
        return $offers->get();
    }

    /**
     * плиск по всем товарам
     */
    public function searchAllProduct( $search )
    {
        if (!$search) {
            return false;
        }
        return DB::table($this->table . ' AS o')->select('o.id', 'o.title', 'p.price_1 AS price')
            ->leftJoin('price AS p', 'o.id', '=', 'p.offer_id')
            ->where('o.title', 'like', $search . '%')
            ->get();
    }

    public function getAllProducts()
    {
        return DB::table($this->table . ' AS p')
            ->select('p.title', 'pr.name AS project', 'p.id', 'pr.alias AS project_alias', 'pr.id AS project_id')
            ->leftJoin('product_projects AS pp', 'pp.product_id', '=', 'p.id')
            ->leftJoin('projects AS pr', 'pp.project_id', '=', 'pr.id')
            ->get();
    }

    public function getOneProduct( $id )
    {
        return DB::table($this->table)->where('id', $id)->first();
    }

    public function searchProductByWord( $term, $projectId = [], $exceptProduct = NULL, $subProjectId = [] )
    {
        $products = DB::table('products as p')
            ->select('p.id', 'title', 'pp.project_id', 'proj.alias as projectAlias')
            ->leftJoin('product_projects as pp', 'pp.product_id', '=', 'p.id')
            ->leftJoin('projects as proj', 'proj.id', '=', 'p.project_id')//было pp.project_id
            ->where('title', 'LIKE', '%' . $term . '%');

        if (auth()->user()->project_id) {
            $products->where('pp.project_id', auth()->user()->project_id);
        }
        if ($projectId) {

            if (is_array($projectId) && array_sum($projectId) > 0) {
                //$projectString = trim(implode(' ', $projectId));
                //  if (!empty($projectString)) {

                $products->whereIn('pp.project_id', $projectId);
                // }
            } else if (!is_array($projectId)) {
                $products->where('pp.project_id', $projectId);
            }
        }
        if ($subProjectId) {
            if (is_array($subProjectId) && array_sum($subProjectId) > 0) {
                //$projectString = trim(implode(' ', $projectId));
                //  if (!empty($projectString)) {
                $products->whereIn('pp.subproject_id', $subProjectId);
                // }
            } else if (!is_array($subProjectId)) {
                $products->where('pp.subproject_id', $subProjectId);
            }
        }

        if ($exceptProduct) {
            $products->whereNotIn('p.id', [$exceptProduct]);
        }

        return $products->groupBy('p.id')->get();
    }

    /**
     * @return HasMany
     */
    // на каких складах и сколько есть этого продукта
    public function storagecontents()
    {
        return $this->hasMany(StorageContent::class);
    }


    /**
     * @param $product_id
     * @param int $sender_id
     * @return Product|Model|Builder|null|object
     */
    public static function getStorageProduct( $product_id, $sender_id = 0 )
    {

// не из космоса
        if ($sender_id) {
            $query = DB::table(static::tableName() . ' as p')
                ->leftJoin(StorageContent::tableName() . ' as sc', 'sc.product_id', '=', 'p.id')
                ->leftJoin(ProductProject::tableName() . ' as pp', 'pp' . '.product_id', '=', 'p.id')
                ->where('p.id', $product_id)
                ->where('pp.subproject_id', $sender_id)
                //->where('sc.project_id', $sender_id)
                ->select(DB::raw('p.id id, p.title title, sc.amount amount, sc.hold hold, pp.project_id project_id'));
            $sender = Project::find($sender_id);
            if ($sender && !$sender->negative) {
                $query->where('sc.project_id', $sender_id);
            }
        } else {
            $query = static::where('products.id', $product_id)
                ->leftJoin(ProductProject::tableName() . ' as pp', 'pp.product_id', '=', 'products.id')
                ->select(DB::raw('products.id, title, 0 amount, 0 hold, pp.project_id'));
        }

        return $query->first();
    }

    /**
     * @param $project_id
     * @param int $sender_id
     * @param array $ids
     * @return \Illuminate\Support\Collection
     */
    public static function getStorageProducts( $project_id, $sender_id = 0, $ids = [] )
    {
        if ($sender_id) {
            $query = DB::table(StorageContent::tableName() . ' as sc')
                ->join(Product::tableName() . ' as p', 'sc.product_id', '=', 'p.id')
                ->join(ProductProject::tableName() . ' as pp', 'pp.product_id', '=', 'p.id')
                ->where('sc.project_id', $sender_id)
                ->where('amount', '>', 0)
                ->select(DB::raw('p.id id, p.title title, sc.amount amount, ' . 'sc.hold hold, pp.project_id project_id'))
                ->whereNotIn('p.id', $ids);
        } else {

            $query = Product::with('productProjects')
                ->whereHas('productProjects', function ( $query ) use ( $project_id ) {
                    $query->where('project_id', $project_id);
                })
                ->select(DB::raw('id, title, 0 amount, 0 hold'))
                ->whereNotIn('id', $ids);
        }
        return $query->orderBy('id', 'asc')
            ->get()->keyBy('id');
    }

    /**
     * @param $moving
     * @return array|Collection
     */
    public static function getMovingProducts( $moving )
    {
        if ($moving->sender_id) {
            return DB::table(Product::tableName() . ' as p')
                ->join(StorageContent::tableName() . ' as sc', 'sc.product_id', '=', 'p.id')
                ->where('sc.project_id', $moving->sender_id)
                ->join(MovingProduct::tableName() . ' as mp', 'mp.product_id', '=', 'p.id')
                ->join(ProductProject::tableName() . ' as pp', 'pp.product_id', '=', 'p.id')
                ->where('mp.moving_id', $moving->id)
                ->select(DB::raw('p.id id, p.title title, mp.amount takenamount, sc.amount amount, sc.hold hold, pp.project_id project_id'))
                ->get()->keyBy('id');
        } else {
            $movingProducts = new Collection([]);
            foreach ($moving->movingProducts as $mp) {
                $movingProducts[$mp->product->id] = (object)[
                    'id' => $mp->product->id,
                    'title' => $mp->product->title,
                    'amount' => 0,
                    'hold' => 0,
                    'project_id' => $mp->product->project_id,
                    'takenamount' => $mp->amount
                ];
            }
            return $movingProducts;
        }
    }

    /**
     * @param $moving
     * @return array|Collection
     */
    public static function getArrivedProducts( $moving )
    {
        $arrivedProducts = new Collection([]);
        foreach ($moving->movingProducts as $mp) {
            $arrived = 0;
            $shortfall = 0;
            if ($mp->parts->isNotEmpty()) {
                $arrived = $mp->parts->where('status', MovingProductPart::STATUS_ARRIVED)->pluck('amount')->sum();
                $shortfall = $mp->parts->where('status', MovingProductPart::STATUS_SHORTFALL)->pluck('amount')->sum();
            }
            $arrivedProducts[] = (object)[
                'id' => $mp->product_id,
                'title' => $mp->product->title,
                'amount' => $mp->amount,
                'arrived' => $arrived,
                'shortfall' => $shortfall
            ];
        }
        return $arrivedProducts;
    }

    public function getProductsByFilters( $filters )
    {
        //dd($filters);
        DB::enableQueryLog();

        if (!$filters['date_start'] || !$filters['date_end']) {
            $filters['date_start'] = Carbon::today();
            $filters['date_end'] = Carbon::today()->endOfDay();
        }else{
            $filters['date_start'] = Carbon::parse($filters['date_start']);
            $filters['date_end'] = Carbon::parse($filters['date_end'])->endOfDay();
        }

        if (!$filters['status']) {
            $filters['status'] = 'paid_up';
        }
        $procStatusId = ProcStatus::where('action', $filters['status'])->first()->id;
        $products = Product::select('products.id', 'products.project_id', 'products.sub_project_id', 'products.title',
            'products.price_cost')
            ->whereHas('orderProducts', function ( $query ) {
                $query->where('disabled', 0);
            })
            ->whereHas('orders', function ( $addQuery ) use ( $filters, $procStatusId ) {
                $addQuery->where('target_status', 1);
                $addQuery->where('proc_status', $procStatusId);

                if ($filters['product']) {
                    $addQuery->where('products.id', $filters['product']);
                }
                if ($filters['project']) {
                    $projects = explode(',', $filters['project']);
                    $addQuery->whereIn('project_id', $projects);
                }
                if ($filters['sub_project']) {
                    $addQuery->where('subproject_id', $filters['sub_project']);
                }
                if (isset($filters['date_start']) && isset($filters['date_end']) && ($filters['date_start'] <= $filters['date_start'])) {
                    $addQuery->whereBetween('time_' . $filters['status'], [
                        $filters['date_start'],
                        $filters['date_end']
                    ]);
                }
            })
            ->withCount([
                'orders AS paidUpOrdersCount' => function ( $q ) use ( $filters, $procStatusId ) {
                    $q->select(DB::raw('COUNT( distinct orders.id)'))
                        ->where('proc_status', $procStatusId)
                        ->where('target_status', 1)
                        ->whereHas('orderProducts', function ( $query ) {
                            $query->where('disabled', 0)
                                ->whereRaw('products.id = order_products.product_id');
                        });

                    $q->where('proc_status', $procStatusId);

                    if ($filters['product']) {
                        $q->where('products.id', $filters['product']);
                    }
                    if ($filters['project']) {
                        $projects = explode(',', $filters['project']);
                        $q->whereIn('project_id', $projects);
                    }
                    if ($filters['sub_project']) {
                        $q->where('subproject_id', $filters['sub_project']);
                    }
                    if (isset($filters['date_start']) && isset($filters['date_end']) && ($filters['date_start'] <= $filters['date_end'])) {
                        $q->whereBetween('time_' . $filters['status'], [
                            $filters['date_start'],
                            $filters['date_end']
                        ]);
                    }
                }
            ])
            ->withCount([
                'orders AS approveOrdersCount' => function ( $q ) use ( $filters, $procStatusId ) {
                    $q->select(DB::raw('COUNT( distinct orders.id)'))
                        ->where('target_status', 1)
                        ->whereHas('orderProducts', function ( $query ) {
                            $query->where('disabled', 0)
                                ->whereRaw('products.id = order_products.product_id');
                        });

                    $q->where('proc_status', $procStatusId);

                    if ($filters['product']) {
                        $q->where('products.id', $filters['product']);
                    }
                    if ($filters['project']) {
                        $projects = explode(',', $filters['project']);
                        $q->whereIn('project_id', $projects);
                    }
                    if ($filters['sub_project']) {
                        $q->where('subproject_id', $filters['sub_project']);
                    }
                    if (isset($filters['date_start']) && isset($filters['date_end']) && ($filters['date_start'] <= $filters['date_end'])) {
                        $q->whereBetween('time_' . $filters['status'], [
                            $filters['date_start'],
                            $filters['date_end']
                        ]);
                    }
                }
            ])
            ->withCount([
                'orderProducts AS paidUpProductsCount' => function ( $q ) use ( $filters, $procStatusId ) {
                    $q->select(DB::raw('COUNT(order_products.id)'))
                        ->where('disabled', 0)->whereHas('order', function ( $orderQuery ) use ( $filters, $procStatusId ) {
                            $orderQuery->where('proc_status', $procStatusId)
                                ->where('target_status', 1);
                            $orderQuery->where('proc_status', $procStatusId);

                            if ($filters['product']) {
                                $orderQuery->where('products.id', $filters['product']);
                            }
                            if ($filters['project']) {
                                $projects = explode(',', $filters['project']);
                                $orderQuery->whereIn('project_id', $projects);
                            }
                            if ($filters['sub_project']) {
                                $orderQuery->where('subproject_id', $filters['sub_project']);
                            }
                            if (isset($filters['date_start']) && isset($filters['date_end']) && ($filters['date_start'] <= $filters['date_end'])) {
                                $orderQuery->whereBetween('time_' . $filters['status'], [
                                    $filters['date_start'],
                                    $filters['date_end']
                                ]);
                            }
                        });
                },
            ])
            ->orderBy('products.title', 'asc');

        $data['products'] = $products->get();

        //get exchange rate
        if ($filters['sub_project']) {
            $subProject = Project::find($filters['sub_project']);
            $country = Country::find($subProject->country_id);
            $data['country'] = $country;
        }

        if (!isset($filters['product'])) {
            $orderProductsData = OrderProduct::
            select('product_id',
                DB::raw('SUM(price) as total_price'),
                DB::raw('SUM(cost) as cost'),
                DB::raw('SUM(cost_actual) as cost_actual')
            )
                ->whereHas('order', function ( $addQuery ) use ( $filters, $procStatusId ) {
                    $addQuery->where('target_status', 1);

                    if (isset($filters['status'])) {
                        $addQuery->where('proc_status', $procStatusId);
                    } else {
                        $addQuery->where('final_target', 1);
                    }
                    if ($filters['product']) {
                        $addQuery->where('p.id', $filters['product']);
                    }
                    if ($filters['project']) {
                        $projects = explode(',', $filters['project']);
                        $addQuery->whereIn('project_id', $projects);
                    }
                    if ($filters['sub_project']) {
                        $addQuery->where('subproject_id', $filters['sub_project']);
                    }
                    if (isset($filters['date_start']) && isset($filters['date_end']) && ($filters['date_start'] <= $filters['date_end'])) {
                        $addQuery->whereBetween('time_' . $filters['status'], [
                            $filters['date_start'],
                            $filters['date_end']
                        ]);
                    }
                })
                ->where('disabled', 0)
                ->groupBy('product_id')
                ->get()
                ->keyBy('product_id');

            $data['orderProductsData'] = $orderProductsData;
        }

        // if exist filter product get all orders data for list
        $ordersForProducts = new Collection();
        if ($filters['product']) {
            $ordersForProductsQuery = Order::
            select('id', 'name_last', 'name_first', 'final_target', 'price_total')
                ->whereHas('orderProducts', function ( $q ) use ( $filters ) {
                    $q->where('product_id', $filters['product'])->where('disabled', 0);
                })
                ->withCount([
                    'orderProducts' => function ( $q ) use ( $filters, $procStatusId ) {
                        $q->where('disabled', 0)
                            ->where('product_id', $filters['product'])
                            ->whereHas('order', function ( $orderQuery ) use ( $filters, $procStatusId ) {
                                $orderQuery->where('proc_status', $procStatusId)
                                    ->where('target_status', 1);
                            });
                    },
                ])
                ->where('proc_status', $procStatusId)
                ->where('target_status', 1);

            $ordersForProductsQuery->where('proc_status', $procStatusId);
            $ordersForProducts = $ordersForProductsQuery->get();

            $data['ordersForProducts'] = $ordersForProducts;
        }

        return $data;
    }

    public static function productsForStatuses( $filter )
    {
        $filter['date_type'] = $filter['date_type'] ? 'time_modified' : 'time_created';
        if (!$filter['date_start'] || !$filter['date_end']) {
            $filter['date_start'] = Carbon::today();
            $filter['date_end'] = Carbon::today()->endOfDay();
        } else {
            $filter['date_start'] = Carbon::parse($filter['date_start']);
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        }

        $query = self::select(
            self::tableName() . '.*',
            DB::raw('COUNT(DISTINCT(o.id)) AS count_orders'),
            DB::raw('o.price_total'),
            DB::raw("COUNT(
                DISTINCT(
                    CASE 
                        WHEN ps.action='sent'
                            THEN o.id
                        WHEN ps.action='at_department'
                            THEN o.id
                    END  
                    )
                ) AS hold"),
            DB::raw("COUNT(
                DISTINCT(
                    CASE 
                        WHEN o.final_target=1
                           THEN o.id
                        WHEN ps.action='paid_up' 
                           THEN o.id  
                    END    
                    )
                ) AS paid_up"),
            DB::raw("COUNT(
                DISTINCT(
                    CASE 
                        WHEN ps.action='received'
                           THEN o.id
                    END    
                    )
                ) AS received")
        )
            ->leftJoin(OrderProduct::tableName() . ' AS op', self::tableName() . '.id', '=', 'op.product_id')
            ->leftJoin(Order::tableName() . ' AS o', 'op.order_id', '=', 'o.id')
            ->leftJoin(ProcStatus::tableName() . ' AS ps', 'o.proc_status', '=', 'ps.id')
            ->where(function ( $q ) {
                $q->where('ps.type', ProcStatus::TYPE_SENDERS)
                    ->orWhere('o.proc_status', 3);
            })
            ->where('o.moderation_id', '>', 0)
            ->where('o.target_status', 1)
            ->where('products.status', 'on')
            ->where('op.disabled', 0);

        if ($filter['country']) {
            $query->where('o.geo', $filter['country']);
        }
        if ($filter['project']) {
            $query->where('o.project_id', $filter['project']);
        }
        if ($filter['sub_project']) {
            $query->where('o.subproject_id', $filter['sub_project']);
        }
        if ($filter['division']) {
            $query->where('o.division_id', $filter['division']);
        }
        if ($filter['proc_status']) {
            $statuses = explode(',', $filter['proc_status']);
            $query->whereIn('o.proc_status', $statuses);
        }
        if ($filter['result']) {
            $filter['result'] = $filter['result'] == 5 ? 0 : $filter['result'];
            $query->where('o.final_target', $filter['result']);
        }
        if ($filter['offers']) {
            $query->where('o.offer_id', $filter['offers']);
        }
        if ($filter['delivery']) {
            $query->where('o.target_approve', $filter['delivery']);
        }
        if ($filter['product']) {
            $query->where('op.product_id', $filter['product']);
        }
        if ($filter['date_start'] && $filter['date_end']) {
            $query->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
        }
        $data = $query->groupBy('op.product_id', 'o.id')->get();

        $res = collect();

        foreach ($data as $datum) {
            if (isset($res[$datum->id])) {
                $res[$datum->id]->count_orders += $datum->count_orders;
                $res[$datum->id]->price_total += $datum->price_total;
                $res[$datum->id]->hold += $datum->hold;
                $res[$datum->id]->paid_up += $datum->paid_up;
                $res[$datum->id]->received += $datum->received;
            } else {
                $res[$datum->id] = $datum;
            }
        }

        return $res;
    }

    public static function mergeProducts( $request )
    {
        $updated = false;

        try {
            DB::table('storage_contents')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);
            DB::table('storage_transactions')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);

            try {
                $productProjects = ProductProject::where('product_id', $request['product_to_merge'])->get();

                foreach ($productProjects as $productProject) {
                    $existProductProject = ProductProject::where('product_id', $request['productId'])
                        ->where('subproject_id', $productProject->subproject_id)
                        ->where('project_id', $productProject->project_id)
                        ->first();

                    if ($existProductProject) {
                        $productProject->delete();
                    }
                }
            } catch (\Exception $exception) {
            }

            DB::table('product_projects')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);
            DB::table('product_options')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);
            DB::table('order_products')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);
            DB::table('offers_products')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);
            DB::table('moving_product')->where('product_id', $request['product_to_merge'])->update([
                'product_id' => $request['productId']
            ]);
            $updated = true;
        } catch (\Exception $exception) {
            return ['success' => false, 'error_message' => $exception->getMessage()];
        }
        if ($updated) {
            $productToDelete = Product::find($request['product_to_merge']);
            if ($productToDelete) {
                $productToDelete->delete();
            }
            $productsProjectToDelete = ProductProject::where('product_id', $request['product_to_merge'])->get();
            foreach ($productsProjectToDelete as $item) {
                $item->delete();
            }

            return ['success' => true];
        }
    }






//    public function OrdOrder()
//    {
//        return $this->belongsToMany(Product::class, 'ord_products');
//    }


}
