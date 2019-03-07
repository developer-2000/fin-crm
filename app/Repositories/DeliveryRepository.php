<?php

namespace App\Repositories;

use App\Models\OrdComent;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\OrdOrder;
use App\Models\OrdProduct;
use App\Models\Permission;
use App\Models\ProcStatus;
use App\Models\Product;
use \App\Models\User;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryRepository
{

//    ===================================================
//    ===================================================
//    ===================================================
// отображение всех заказов
    public static function all_orders($request) {

//    "id" => 24
//    "order_user" => 988446
//    "executor_user" => 0
//    "project_id" => 1
//    "subproject_id" => 4
//    "amount" => 1
//    "status_moderation" => 0
//    "status_financial" => 0
//    "country" => 0
//    "creat_order" => "2019-02-22 10:01:25"
//    "payment_order" => null
//    "shipment_order" => null
//    "created_at" => "2019-02-22 10:01:25"
//    "updated_at" => "2019-02-22 10:01:25"
//    "project_name" => "Univer-Mag"
//    "sub_name" => "Univer-Mag"
//    "products" => array:1 [▼
//      0 => array:3 [▼
//        "prod_id" => 688
//        "prod_title" => "Calvin Klein Euphoria Men Intense 100ml"
//        "pivot" => array:5 [▼
//          "order_id" => 24
//          "product_id" => 688
//          "id" => 12
//          "color_id" => 2
//          "color_amount" => 1
//        ]
//      ]
//    ]
//    "ord_coment" => array:1 [▼
//      0 => array:6 [▼
//        "id" => 3
//        "order_id" => 24
//        "user_id" => 988446
//        "text" => "qwe"
//        "created_at" => "2019-02-22 10:01:25"
//        "updated_at" => "2019-02-22 10:01:25"
//      ]
//    ]
//  ]


    $query = OrdOrder::with(
        'products:products.id as prod_id,products.title as prod_title',
        'ordComent',
        'user')->changeData($request)->orderBy('id', 'desc');

//        dd($query->get()->toArray());

//    dd(OrdOrder::getSetingsColor()[1]);
//    dd($query->get()->toArray());


    $all_count = $query->count();
    $paginate = $query->paginate(5);

    return view('delivery.all_orders', compact( 'query', 'paginate', 'all_count' ))->render();
    }

//    ===================================================
//    ===================================================
//    ===================================================

    public static function ProductsList($request) {

// ВЫХОД
        if (!$request->ajax()) { abort(403); }
// id моего проекта
        $users_project_id = auth()->user()->project_id;
//переданный - из какого проекта
        $project_id = (int)$request->post('project_id', 0);
//переданный - из какого подпроекта
        $sub_project = (int)$request->post('sub_project', 0);
//переданный - поисковое слово
        $word = $request->post('word', '');
// Object { 2001: "3", 3185: "123" } перечень до этого выбраных товаров (id и кол-во перемещаемых)
// содержит ключи масива 2001, 3185
//        $ids = array_keys($request->get('products', []));

// ВЫХОД если у меня есть проект и он не соответствует присланному
        if ($users_project_id && ($users_project_id != $project_id)){ return \Request::json([]); }


        $products = Product::where('sub_project_id', $sub_project)
            ->select('id', 'title')
            ->where('title', 'like', '%' . $word . '%')
            ->orderBy('id', 'asc')
            ->get()
            ->keyBy('id');


// отображение строк найденных товаров по запросу
        $answer = [];
        if ($products && $products->isNotEmpty()) {
            foreach ($products as $product) {
                $answer[] = [
                    'id' => $product->id,
                    'text' => $product->title
                ];
            }
        }

        return $answer;
    }


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из creat_order.js возвращает выбраный товар
    public static function plusProduct($request) {

//переданный - из какого проекта
        $project_id = (int)$request->post('project_id', 0);
// с какого sub-проекта
//        $sub_id = (int)$request->get('sender_id', 0);
// id выбранного продукта
        $product_id = (int)$request->get('product_id');
// проект юзера
        $us_proj_id = (int)auth()->user()->project_id;

// EXIT
        if (!$request->ajax()) { abort(403); }
// EXIT
        if ($us_proj_id && ($us_proj_id != $project_id)){ abort(403); }


        $product = Product::where('id', $product_id) ->select('id', 'title') ->first();

// количество выбраных продуктов на странице пользователя
        $kol_vo = !(int)$request->get('count_product') ? 1 : ((int)$request->get('count_product') + 1);

        return [
            'new_product_html' => view('delivery.new-product', [
                'product' => $product,
                'count_product' => $kol_vo,
                'color_array' => OrdOrder::getSetingsColor()
                ])->render(),
            'kol_vo' => $kol_vo,
        ];
    }

//    ===================================================
//    ===================================================
//    ===================================================
// создает заказ в поставках
    public static function addOrder($request) {

        $arrayProduct = [];
        $project_id = (int)$request->post('project_id', 0);
        $sub_project_id = (int)$request->post('sub_project', 0);
        $products = $request->post('products', 0);

        if ($products){

            // перебор разных продуктов
            foreach ($products as $product){

// 1 создать заказ в ord_orders
                $flight = OrdOrder::firstOrCreate([
                    'order_user' => Auth::user()->id,
                    'project_id' => $project_id,
                    'subproject_id' => $sub_project_id,
                    'amount' => $product['all_count'],
                    'creat_order' => Carbon::now(),
                    'payment_order' => null,
                    'shipment_order' => null,
                ]);

                // перебор позиций цвета в отдельном товаре
                foreach ($product['option'] as $color){
                    array_push($arrayProduct,  new OrdProduct([ 'product_id' => $product['product_id'], 'color_amount' => $color['count'], 'color_id' => $color['color']]) );
                }

// 1 создать заказ в ord_products , ord_coments
            $a = $flight->ordProduct()->saveMany($arrayProduct);
            $a2 = $flight->ordComent()->save(new OrdComent([ 'user_id' => Auth::user()->id, 'text' => $product['description'] ]));


            $arrayProduct = [];

            }

        }



        return json_encode($products);
//        return json_encode($flight->all());

    }


//    ===================================================
//    ===================================================
//    ===================================================



}