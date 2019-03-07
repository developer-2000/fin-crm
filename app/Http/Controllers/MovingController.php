<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Moving;
use App\Models\MovingProduct;
use App\Models\MovingProductPart;
use App\Models\ProductProject;
use App\Models\Project;
use App\Models\Product;

use App\Models\Projects_new;
use App\Models\StorageContent;
use App\Models\StorageTransaction;
use Carbon\Carbon;
use DebugBar\DebugBar;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use SebastianBergmann\Environment\Console;
use tests\Mockery\Generator\ClassWithDebugInfo;
use Throwable;
use Illuminate\Http\JsonResponse;


class MovingController extends BaseController
{


    /**
     * @param Request $request
     * @return Factory|View
     */
//    ===================================================
//    ===================================================
//    показывает стр всех перемещений ===================
    public function movings(Request $request)
    {
        $begin_query = Moving::searchQuery($request);
        $query = Moving::with('movingProducts.product', 'movingProducts.parts.user', 'user')
            ->searchQuery($request)
            ->searchSelect()
            ->setSearchWhere($request)
            ->setSearchSort($request);

        // костыль для space
        if (Auth::user()->role_id == 15) {
            $query->where('sender_id', 0);
        }

        $movings = $query->paginate(15);
        $sortLinks = (new Moving)->sortLinks($request);
        $appendPage = (new Moving)->appendPage($request);
        $statuses = Moving::langStatuses();
        $senders = $begin_query
            ->select(DB::raw('sender_id, IF(sender_id, concat_ws(\' / \', sd_papa.name, sd.name), \''
                . '--space--' . '\') as sender'))
            ->groupBy('sd.id')->pluck('sender', 'sender_id');
        $receivers = $begin_query
            ->select(DB::raw('receiver_id, IF(receiver_id, concat_ws(\' / \', rc_papa.name, rc.name), \''
                . '--space--' . '\') as receiver'))
            ->groupBy('rc.id')->pluck('receiver', 'receiver_id');
        $date_start_name = \Request::get('received_date_start', null)
            ? 'received_date_start'
            : (\Request::get('send_date_start', null) ? 'send_date_start' : 'created_at_start');
        $date_end_name = \Request::get('received_date_end', null)
            ? 'received_date_end'
            : (\Request::get('send_date_end') ? 'send_date_end' : 'created_at_end');

        return view('movings.all', compact(
            'movings', 'sortLinks', 'appendPage', 'statuses', 'senders', 'receivers',
            'date_start_name', 'date_end_name'
        ));
    }


//    ===================================================
//    ===================================================
//    ===================================================


    /**
     * @param int $id
     * @return Factory|View
     */
//    ===================================================
//    ===================================================
//    раскрывает отдельный moving =======================
    public function one(int $id)
    {

//        moving , склад получатель , подпроект это склада
        $moving = Moving::with('sender.parent', 'receiver.parent')
            ->where(['id' => $id])->firstOrFail();


// EXIT // могут сомтреть отправители, получатели и нолики
        if (!auth()->user()->isSenderHandling($moving) && !auth()->user()->isReceiverHandling($moving)) {
            abort(403);
        }


//        array:13 [▼
//  "id" => 293
//  "user_id" => 123457
//  "sender_id" => 0
//  "receiver_id" => 44
//  "send_date" => null
//  "received_date" => null
//  "status" => 0
//  "created_at" => "2019-02-15 17:33:10"
//  "sender" => null
//  "receiver" => array:12 [▶]
//  "moving_products" => array:1 [▼
//    0 => array:6 [▼
//      "id" => 1316
//      "product_id" => 1628
//      "amount" => "4294967295"
//      "moving_id" => 293
//      "product" => array:12 [▼
//        "id" => 1628
//        "sku" => "0"
//        "project_id" => 2
//        "sub_project_id" => 5
//        "product_id" => 42
//        "category_id" => 0
//        "title" => "Patek Philippe Geneve"
//        "title_alias" => null
//        "weight" => 0
//        "price_cost" => 0.0
//        "status" => "on"
//        "type" => "0"
//      ]
//      "parts" => []
//    ]
//  ]
//  "comments" => []
//  "user" => array:28 [▶]
//]

        $moving = $moving->load('movingProducts.product', 'movingProducts.parts.user', 'comments.user', 'user');

//        dd($moving->toArray());

//        array:1 [▼
//  1628 => {#4435 ▼
//        +"id": 1628
//        +"title": "Patek Philippe Geneve"
//        +"amount": 0
//        +"hold": 0
//        +"project_id": 2
//        +"takenamount": "4294967295"
//  }
//]

        $movingProducts = ($moving->status == Moving::STATUS_NEW)
            ? Product::getMovingProducts($moving)
            : false;

//        dd($movingProducts->toArray());

        $mpInSelect = ($moving->status == Moving::STATUS_NEW)
            ? Product::getStorageProducts($moving->receiver->parent_id ?? 0, $moving->sender_id,
                $moving->movingProducts->pluck('product_id'))
            : false;


        $arrivedProducts = (in_array($moving->status, [
            Moving::STATUS_SENT,
            Moving::STATUS_RECEIVED,
            Moving::STATUS_CLOSED
        ]))
            ? Product::getArrivedProducts($moving)
            : false;


        $history = [];
        if ($moving->movingProducts->isNotEmpty()) {
            foreach ($moving->movingProducts as $mp) {
                if ($mp->parts->isNotEmpty()) {
                    foreach ($mp->parts as $part) {
                        $history[] = [
                            'id'           => $part->id,
                            'mp_id'        => $part->mp_id,
                            'amount'       => $part->amount,
                            'status'       => $part->status,
                            'created_at'   => $part->created_at,
                            'product_id'   => $mp->product_id,
                            'product_name' => $mp->product->title,
                            'user_id'      => $part->user_id,
                            'user_name'    => $part->user_id ? ($part->user->name . ' ' . $part->user->surname) : ''
                        ];
                    }
                }
            }
        }
        if (!empty($history)) {
            $history = array_reverse(
                collect($history)->groupBy(function ($row) {
                    return $row['created_at'] . ' ' . $row['user_id'];
                })->toArray()
            );
        }


        $user_sender = false;
        $send_date = false;
        if (($moving->status == Moving::STATUS_NEW) && (($moving->status == Moving::STATUS_SENT) && !$moving->sender_id)) {
        }
        else {
            $st = StorageTransaction::where('moving_id', $moving->id)
                ->select(DB::raw('min(created_at) as created_at, user_id'))
                ->with('user')
                ->first();
            if ($st) {
                $user_sender = $st->user ? ($st->user->name . ' ' . $st->user->surname) : '';
                $send_date = $st->created_at; // реальная дата отправки по транзакции
            }
        }

        return view('movings.one', compact('moving', 'movingProducts', 'mpInSelect', 'arrivedProducts',
            'history', 'user_sender', 'send_date'));
    }


//    ===================================================
//    ===================================================
//    ===================================================


    /**
     * @return Factory|View
     */
// выбирает проекты
    public function create()
    {
        // where (`id` = 11 OR `id` = 15) and `type` = 1
        $projects = Projects_new::CheckProj()->where('type', 1)->orderBy('id', 'asc')->get();
        return view('movings.create', compact('projects'));
    }

    /**
     * @param Request $request
     * @return array
     * @throws Throwable
     */


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из moving_creat.js select подпроекты
    public function getStorages(Request $request) {

       if (!$request->ajax()) { abort(403); }

        // мой юзер id проекта
        $users_project_id = (int)auth()->user()->project_id;
        // subпроект юзера
        $users_subproject_id = auth()->user()->sub_project_id;
        // пришел id выбранного проекта
        $project_id = (int)$request->post('project_id', 0);

// EXIT (моего юзера id_проект не совпадает с пришедшим id_проектом)
        if ($users_project_id && ($users_project_id != $project_id)) { abort(403); }

// 1 выбираю subproject для отображения в select ( id и имя - Object { 0: "имя subproject", 50: "TransitSub" } )
        // если нет у юзера подпроекта
        if (!$users_subproject_id){
            $subproj = Projects_new::projW3('parent_id', $project_id, 'id');
        }
        else{
            $subproj = Projects_new::projW1('id', $users_subproject_id)->pluck('name', 'id');
        }

        //костыль для китайца //space
//        if (Auth::user()->role_id == 15) { $storageSpace = collect('--space--'); }
        // еще один костыль
        //удаление "космоса" из списка
//        if (!isset($this->permissions['storage_space'])) { $subproj->forget(0); }

        return [ 'subproj_html' =>
            view('movings.storages', [ 'subproj' => $subproj, 'forma'  => 'subproj' ])
                ->render()
        ];

    }


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из moving_creat.js select мои склады
    public function getMyStorages(Request $request) {

        if (!$request->ajax()) { abort(403); }

        // subпроект юзера
        $users_subproject_id = auth()->user()->sub_project_id;
        // пришел id выбранного подпроекта
        $id_sub = (int)$request->post('id_sub', 0);
        $array_sub[] = $id_sub;
// EXIT (моего юзера id_проект не совпадает с пришедшим id_проектом)
        if ($users_subproject_id && ($users_subproject_id != $id_sub)) { abort(403); }


// 1 выбираю склады с которых отправляю  ( id и имя - Object { 0: "--space--", 50: "TransitSub" } )
        $insert_storages = Projects_new::projW4($array_sub, 'id')
            ->prepend('--space--', 0);


        return [ 'my_storage_html' =>
            view('movings.storages', [ 'storages' => $insert_storages, 'forma'  => 'my_storage' ])
                ->render()
        ];


    }


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из moving_creat.js select на каакие склады
    public function getToStorages(Request $request) {

// EXIT
        if (!$request->ajax()) { abort(403); }

        // subпроект юзера
        $users_subproject_id = auth()->user()->sub_project_id;
        // пришел id выбранного проекта
        $project_id = (int)$request->post('project_id', 0);
        // пришел id выбранного подпроекта
        $id_sub = (int)$request->post('id_sub', 0);
        // пришел id выбранного моего склада
        $my_storage_id = (int)$request->post('my_storage_id', 0);
        $array_sub[] = $my_storage_id;

// EXIT (моего юзера id_проект не совпадает с пришедшим id_проектом)
        if ($users_subproject_id && ($users_subproject_id != $id_sub)) { abort(403); }


        // выбран в моих складах не space И доступ отправки на все склады в базе
        if ((int)$my_storage_id && Auth::user()->can('select_all_storages')){

             $all_storages = Projects_new::whereNotIn('id', $array_sub)
                 ->projW3('type', 3, 'id')
                 ->prepend('--space--', 0);
        }
        // выбран в моих складах space ИЛИ нет доступа отправки на все склады в базе
        // шлю только на склады компании
        elseif (!(int)$my_storage_id || !Auth::user()->can('select_all_storages')){

            $array_proj[] = $project_id;

            // если при этом выбран склад - от куда слать
            // могу посылать товар в космос
            $all_storages = (int)$my_storage_id ?
                Projects_new::projW4(Projects_new::projW5($array_proj), 'id')
                    ->prepend('--space--', 0)
                :
                Projects_new::projW4(Projects_new::projW5($array_proj), 'id');
        }

        return [ 'all_storages_html' =>
            view('movings.storages', [ 'storages' => $all_storages, 'forma'  => 'receiver_id' ])
                ->render()
        ];
    }
//    ===================================================
//    ===================================================
//    ===================================================


    /**
     * @param Request $request
     * @return array
     * @throws Throwable
     */
// выборка ajax из moving_creat.js отображая select поиска товара
    public function getProducts(Request $request)
    {

        return [
//            'product_html' => view('movings.products2'/*, ['products' => $products]*/)->render(),
            'product_html' => view('movings.products2')->render(),
            'button_html'  => view('movings.button', ['type' => 0])->render()
        ];
    }


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из moving_creat.js совершает поиск и возвращает искомый товар
    public function getProductsList(Request $request)
    {
//return $request;

//my_storage: "51"
//​my_sub_project: "15"
//​project_id: "11"
//​sender_id: "43"
//​word: "20"


// ВЫХОД
        if (!$request->ajax()) { abort(403); }
// id моего проекта
        $users_project_id = auth()->user()->project_id;
//переданный - из какого проекта
        $project_id = (int)$request->post('project_id', 0);
//переданный - из какого подпроекта
        $my_sub_project = (int)$request->post('my_sub_project', 0);
//переданный - из какого склада
        $my_storage = (int)$request->post('my_storage', 0);
//переданный - поисковое слово
        $word = $request->post('word', '');
// Object { 2001: "3", 3185: "123" } перечень до этого выбраных товаров (id и кол-во перемещаемых)
// содержит ключи масива 2001, 3185
        $ids = array_keys($request->get('products', []));

// ВЫХОД если у меня есть проект и он не соответствует присланному
        if ($users_project_id && ($users_project_id != $project_id)){
            return \Request::json([]);
        }

// не из космоса
        if ($my_storage) {

            // если можно уходить компании в минус
            if ((int)Projects_new::CheckValNeg($my_storage) !== 0){
            // смотрю на negative склада
            // ищу товар на product_project сверяя subproject_id с id подпроекта от которого выбран склад
                $query = $this->yesMinusStorage($my_sub_project, $ids);
            }
            // если нельзя уходить складу в минус
            else{
            // ищу товар на storage_content сверяя id склада с ячейкой project_id
                $query = $this->noMinusStorage($my_storage, $ids);
            }

        }
//        из космоса
        else {
            $query = DB::table('products')->whereNotIn('id', $ids)->select('id', 'title');
        }


        $products = $query
            ->where('title', 'like', '%' . $word . '%')
            ->orderBy('id', 'asc')->get()->keyBy('id');


// отображение строк найденных товаров по запросу
        $answer = [];
        if ($products && $products->isNotEmpty()) {


                if ($my_storage) {
                    foreach ($products as $product) {
                        $answer[] = [
                            'id' => $product->id,
                            'text' => $product->title . (($product->amount || $product->hold)
                                    ? (' (' . trans('general.count') . ':' . $product->amount . ')') : '')
                        ];
                    }

                }
                else{
                    foreach ($products as $product) {
                        $answer[] = [
                            'id' => $product->id,
                            'text' => $product->title
                        ];
                    }
                }

        }

    return \Response::json($answer);
    }



//    ===================================================
//    ===================================================
//    ===================================================
    /**
     * @param Request $request
     * @return array
     * @throws Throwable
     */
// выборка ajax из moving_creat.js возвращает выбраный товар
    public function plusProduct(Request $request)
    {

//        return $request;

// project_id: "11"
// product_id: "3185"
//​ sender_id: "51"

//переданный - из какого проекта
        $project_id = (int)$request->post('project_id', 0);
// с какого склада
        $my_storage = (int)$request->get('sender_id', 0);
// id выбранного продукта
        $product_id = (int)$request->get('product_id');

// EXIT
        if (!$request->ajax()) { abort(403); }
// EXIT
        $us_proj_id = (int)auth()->user()->project_id;
        if ($us_proj_id && ($us_proj_id != $project_id)){
            abort(403);
        }


        // не из космоса
        if ($my_storage) {

            // если можно уходить компании в минус
            if ((int)Projects_new::CheckValNeg($my_storage) !== 0){
                $product = DB::table('products')
                    ->where('id', $product_id)
                    ->select('id', 'title')
                    ->first();
            }
            // если нельзя уходить складу в минус
            else{
                $SC = StorageContent::tableName();
                $P = Product::tableName();
                // ищу товар на storage_content сверяя id склада с ячейкой project_id
                $product = StorageContent::leftJoin($P . ' as p', $SC . '.product_id', '=', 'p.id')
                    ->where($SC . '.project_id', $my_storage)
                    ->where($SC . '.product_id', '=', $product_id)
                    ->select(DB::raw('p.id id, p.title title, ' . $SC . '.amount amount, ' . $SC . '.hold hold'))
                    ->first();
            }

        }
//        из космоса
        else {
            $product = DB::table('products')
                ->where('id', $product_id)
                ->select('id', 'title')
                ->first();
        }

//return json_encode($product);
//return json_encode(view('movings.new-product', ['product' => $product])->render());


        return [
            'new_product_html' => view('movings.new-product', ['product' => $product])->render(),
            'product_html'     => view('movings.products2')->render()
        ];
    }


//    ===================================================
//    ===================================================
//    ===================================================
    /**
     * @param Request $request
     * @return array
     * @throws Throwable
     */
    public function minusProduct(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }
        $sender_id = (int)$request->get('sender_id', 0);
        $product_id = (int)$request->get('product_id');

        $product = Product::getStorageProduct($product_id, $sender_id);

        $users_project_id = (int)auth()->user()->project_id;
        if ($users_project_id && ($users_project_id != $product->project_id))
            abort(403);

        return [
            'new_product_html' => view('movings.new-product', ['product' => $product])->render(),
            'product_html'     => view('movings.products2'/*, ['products' => $products]*/)->render()
        ];
    }



    /**
     * @param Request $request
     * @return array
     */
//    ===================================================
//    ===================================================
//    ===================================================
//     создание передвижения товара =====================
    public function store(Request $request)
    {

// products: Object { 3185: "3" }
// receiver_id: "4"
// sender_id: "15"
// пришел масив продуктов
        $products = $request->get('products', []);
// с какого склада переброс
        $sender_id = (int)$request->get('sender_id', 0);
// на какой склад переброс
        $receiver_id = (int)$request->get('receiver_id', 0);

// Exit
        if (!$request->ajax()) { abort(403); }

// Exit
        if (empty($products)) {
            return ['errors' => [trans('alerts.select-products')]];
        }


        $projectIds = DB::table('products as p')
            //после обьединения товаров пересмотреть
       //     ->leftJoin('product_projects as pp', 'pp.product_id', '=', 'p.id')
            ->whereIn('p.id', array_keys($products))
            ->groupBy('p.project_id')
            ->pluck('p.project_id');

        $checkAmount = 'df';

        // склад неможет уходить в минус
        if (!DB::table('projects_new') ->where('id', $sender_id) ->value('negative')){

            // выбрать данные в базе по наличию товара
            $checkAmount = DB::table('storage_contents as sc')
                ->where('project_id', $sender_id)
                ->whereIn('product_id', array_keys($products))->get();


            foreach ($checkAmount as $elem => $obj){
            $amount_zakaza = (int)$products[$obj->product_id];
            $amount_baza = (int)$obj->amount;

                // если на складе меньше чем требует заказ - заменить amount в заказе на кол-во на складе
                if ($amount_zakaza > $amount_baza){
                    $products[$obj->product_id] = $amount_baza;
                }
            }
        }


// проверка для Exit
        $users_project_id = (int)auth()->user()->project_id;
        if ($users_project_id && ($users_project_id != $projectIds[0])) {
            return ['errors' => [trans('alerts.havent-right-with-project')]]; //У вас нет прав на работу с проектом
        }
// проверка для Exit
        foreach ($products as $id => $amount) {
            if ($amount <= 0) {
                return ['errors' => [trans('alerts.indicate-number-products')]]; //Пожалуйста, укажите количество продуктов в каждой строке или удалите лишние
            }
        }
// проверка для Exit
        if (auth()->user()->sub_project_id && (auth()->user()->sub_project_id != $sender_id)) {
            return ['errors' => [trans('alerts.cant-be-sender-this-subpoject')]]; //Вы не можете быть отправителем из этого подпроекта
        }
// проверка для Exit
        if (!$sender_id && !$receiver_id) {
            return ['errors' => [trans('alerts.sender-receiver-not-listed')]]; //Отправитель и получатель не указаны
        }


// проверка для Exit - отправитель и получатель из разных проектов
//        возможно только с доступом
        if ($sender_id && $receiver_id) {
            $projectIds = Project::whereIn('id', [$sender_id, $receiver_id])
                ->groupBy('parent_id')
                ->pluck('parent_id');
            if (count($projectIds) > 1 && !Auth::user()->can('select_all_storages')) {
                return ['errors' => [trans('alerts.sender-receiver-belong-different-projects')]]; //Отправитель и получатель принадлежат к различным проектам
                // возможно, стоит занести в логи
            }
        }

// проверка для Exit
        $count = Product::whereIn('id', array_keys($products))->count();
        if ($count < count($products)) {
            return ['errors' => [trans('alerts.products-noexist-in-project')]]; //Некоторые продукты больше не существуют в проекте
        }

// проверка для Exit
//        if ($sender_id) {
//
//            $result = StorageContent::productsExistenceCheck($sender_id, $products);
//            if (is_array($result)) {
//                return ['errors' => [$result['errors']]];
//            }
//        }

        // добавить движение (movings)
        $moving = Moving::create([
            'sender_id'   => $sender_id,
            'receiver_id' => $receiver_id,
            'user_id'     => auth()->user()->id,
            //'send_date' => Carbon::now(),
            'status'      => Moving::STATUS_NEW,
        ]);
        foreach ($products as $id => $amount) {
            // добавить в движение (moving_product)
            MovingProduct::create([
                'moving_id'  => $moving->id,
                'product_id' => $id,
                'amount'     => $amount
            ]);
        }
        return [
            'message' => trans('alerts.data-successfully-saved'),
            'link'    => route('moving', $moving->id) // "http://crm.lara/storages/movings/283"
        ];
    }


//    ===================================================
//    ===================================================
//    ===================================================
    /**
     * @param Request $request
     * @return array
     */
    public function move(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }

        $products = $request->has('products') && is_array($request->post('products'))
            ? $request->post('products')
            : false;
        if (empty($products)) {
            return ['errors' => [trans('alerts.product-list-not-received')]];
        }
        foreach ($products as $amount) if ($amount <= 0) {
            return ['errors' => [trans('alerts.no-zero-amounts')]]; //there should be no zero amounts
        }

        $moving_id = $request->post('moving_id', 0);
        $moving = Moving::with('movingProducts', 'sender.parent', 'receiver.parent')
            ->where('id', $moving_id)->first();
        $users_project_id = (int)auth()->user()->project_id;
        if (!auth()->user()->isSenderHandling($moving)) {
            abort(403);
        }
        if ($moving->status != Moving::STATUS_NEW) {
            return [
                'errors' => [
                    trans('alerts.moving-cant-be-sent',
                        ['status' => Moving::langStatuses()[$moving->status]])
                ]
            ];
        }

        $moving->movingProducts()->delete();

        foreach ($products as $id => $amount) {
            MovingProduct::create([
                'moving_id'  => $moving->id,
                'product_id' => $id,
                'amount'     => $amount
            ]);
        }

        $moving = $moving->load('movingProducts.product');

        if ($moving->sender_id) {

            $result = StorageContent::productsExistenceCheck($moving->sender_id, $products);
            if (is_array($result)) {
                return ['errors' => [$result['errors']]];
            } else {
                $storageContents = $result;
            }

            foreach ($moving->movingProducts as $mp) {
                // если не космос: убавить на складе amounts, нарастить holds
                // если не космос: добавить транзакции
                $storageContents[$mp->product_id]->increment('hold', $mp->amount);
                $storageContents[$mp->product_id]->decrement('amount', $mp->amount);
                StorageTransaction::create([
                    'product_id' => $mp->product_id,
                    'project_id' => $moving->sender_id,
                    'moving_id'  => $moving->id,
                    'user_id'    => auth()->user()->id,
                    'amount1'    => $storageContents[$mp->product_id]->amount + $mp->amount,
                    'amount2'    => $storageContents[$mp->product_id]->amount,
                    'hold1'      => $storageContents[$mp->product_id]->hold - $mp->amount,
                    'hold2'      => $storageContents[$mp->product_id]->hold,
                    'type'       => StorageTransaction::TYPE_SYSTEM_SENT
                ]);
            }
        }

        $moving->status = Moving::STATUS_SENT;
        $moving->send_date = now();
        $moving->save();

        return [
            'message' => 'Движение в пути',
            'changes' => ['status' => Moving::STATUS_SENT],
        ];
    }


//    ===================================================
//    ===================================================
//    ===================================================
    // подтверждение о прибытии движения
    public function arrived(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }

        $id = $request->post('moving_id', 0);
        $moving = Moving::with('sender', 'receiver', 'movingProducts')
            ->where('id', $id)->firstOrFail();
        if (!auth()->user()->isReceiverHandling($moving)) {
            return [
                'errors'   => ['Нет полномочий'],
                'btn_hide' => true,
            ];
        }

        if ($moving->status != Moving::STATUS_SENT) {
            return [
                'errors'   => [trans('warehouses.moving-closed-earlier')], // This moving was closed earlier
                'btn_hide' => true,
            ];
        }

        $products = $request->post('products', false);
        if (empty($products)) {
            return ['errors' => [trans('alerts.field-required')]];
        }
        $tf = false;
        foreach ($products as $key => $product) {
            $products[$key]['arrived'] = (int)($product['arrived'] ?? 0);
            $products[$key]['shortfall'] = (int)($product['shortfall'] ?? 0);
            if ($products[$key]['shortfall'] || $products[$key]['arrived']) {
                $tf = true;
            }
        }
        if (!$tf) {
            return ['errors' => [trans('alerts.field-required')]];
        }

        foreach ($products as $key => $product) {
            if (($product['arrived'] < 0) || ($product['shortfall'] < 0)) {
                return ['errors' => [trans('alerts.value-cant-be-zero')]]; //Values can not be less than zero
            }

            if (!$moving->movingProducts->pluck('product_id')->contains($product['id'])) {
                $p = Product::where('id', $product['id'])->first();
                return [
                    'errors' => [
                        trans('alerts.product-not-moving')
                    ]
                ];
                // log?
            }
            foreach ($moving->movingProducts as $mp) {
                if ($mp->product_id == $product['id']) {
                    $products[$key]['mp_id'] = $mp->id;
                    $back_shortfall = $mp->parts->where('status', MovingProductPart::STATUS_SHORTFALL)
                        ->pluck('amount')->sum();
                    $back_arrived = $mp->parts->where('status', MovingProductPart::STATUS_ARRIVED)
                        ->pluck('amount')->sum();
                    if (($product['shortfall'] + $back_shortfall + $product['arrived'] + $back_arrived) > $mp->amount) {
                        $p = Product::where('id', $mp->product_id)->first();
                        return [
                            'errors' => [
                                trans('alerts.server-error')
                            ]
                        ];
                    }
                }
            }
        }

        // склады, которым нужно уменьшить холд
        $storageContents0 = StorageContent::where('project_id', $moving->sender_id)
            ->whereIn('product_id', (new Collection($products))->pluck('id'))
            ->get()->keyBy('product_id');

        // склады, которым нужно увеличить amount
        $storageContents = StorageContent::where('project_id', $moving->receiver_id)
            ->whereIn('product_id', (new Collection($products))->pluck('id'))
            ->get()->keyBy('product_id');

        $mpp_array = [];
        $sc_array = [];
        $st_array = [];

        foreach ($products as $product) {
            if ($product['shortfall']) {
                $mpp_array[] = [
                    'mp_id'   => $product['mp_id'],
                    'amount'  => $product['shortfall'],
                    'status'  => MovingProductPart::STATUS_SHORTFALL,
                    'user_id' => auth()->user()->id
                ];
            }
            if ($product['arrived']) {
                $mpp_array[] = [
                    'mp_id'   => $product['mp_id'],
                    'amount'  => $product['arrived'],
                    'status'  => MovingProductPart::STATUS_ARRIVED,
                    'user_id' => auth()->user()->id
                ];
                $st_array[] = [
                    'product_id' => $product['id'],
                    'project_id' => $moving->receiver_id,
                    'moving_id'  => $moving->id,
                    'user_id'    => auth()->user()->id,
                    'amount1'    => isset($storageContents[$product['id']])
                        ? $storageContents[$product['id']]->amount
                        : 0,
                    'amount2'    => isset($storageContents[$product['id']])
                        ? ($storageContents[$product['id']]->amount + $product['arrived'])
                        : $product['arrived'],
                    'hold1'      => isset($storageContents[$product['id']])
                        ? $storageContents[$product['id']]->hold
                        : 0,
                    'hold2'      => isset($storageContents[$product['id']])
                        ? $storageContents[$product['id']]->hold
                        : 0,
                    'type'       => StorageTransaction::TYPE_SYSTEM_RECEIVED,
                ];
                if ($moving->sender_id && isset($storageContents0[$product['id']])) {
                    $st_array[] = [
                        'product_id' => $product['id'],
                        'project_id' => $moving->sender_id,
                        'moving_id'  => $moving->id,
                        'user_id'    => auth()->user()->id,
                        'amount1'    => $storageContents0[$product['id']]->amount,
                        'amount2'    => $storageContents0[$product['id']]->amount,
                        'hold1'      => $storageContents0[$product['id']]->hold,
                        'hold2'      => $storageContents0[$product['id']]->hold - $product['arrived'],
                        'type'       => StorageTransaction::TYPE_SYSTEM_RELEASED,
                    ];
                    $storageContents0[$product['id']]->decrement('hold', $product['arrived']);
                }
                if (isset($storageContents[$product['id']])) {
                    $storageContents[$product['id']]->increment('amount', $product['arrived']);
                } else {
                    $sc_array[] = [
                        'project_id' => $moving->receiver_id,
                        'product_id' => $product['id'],
                        'amount'     => $product['arrived'],
                        'hold'       => 0
                    ];
                }
            }
        }

        if (!empty($mpp_array)) {
            MovingProductPart::insert($mpp_array);
        }
        if (!empty($sc_array)) {
            StorageContent::insert($sc_array);
        }
        if (!empty($st_array)) {
            StorageTransaction::insert($st_array);
        }

        $received = true;
        $moving->load('movingProducts.parts');
        foreach ($moving->movingProducts as $mp) {
            if ($mp->parts->pluck('amount')->sum() < $mp->amount) {
                $received = false;
                break;
            }
        }
        if ($received) {
            $moving->status = Moving::STATUS_RECEIVED;
            $moving->received_date = now();
            $moving->save();
        }

        return [
            'message' => trans('warehouses.arrival-noted'),
            'reload'  => true
        ];
    }


    //    ===================================================
//    ===================================================
//    ===================================================
    /**
     * @param Request $request
     * @return array|JsonResponse
     * @throws Throwable
     */
    public function comment(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }
        $r = $request->only(['moving_id', 'text']);
        $validator = Validator::make($r, [
            'moving_id' => ['required', 'exists:movings,id'],
            'text'      => ['required', 'max:10000']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $moving = Moving::with('sender', 'receiver')->where('id', $r['moving_id'])->firstOrFail();
        if (!auth()->user()->isSenderHandling($moving) && !auth()->user()->isReceiverHandling($moving)) {
            return ['errors' => ['text' => [trans('alerts.not-associated-moving')]]]; 
        }

        $comment = Comment::create([
            'commentable_id'   => $r['moving_id'],
            'commentable_type' => Moving::class,
            'entity' => 'moving',
            'user_id' => auth()->user()->id,
            'text' => $r['text'],
            'date' => now()
        ]);
        $comment->load('user');
        return [
            'comment_html' => view('movings.comment', ['comment' => $comment->load('user')])->render()
        ];
    }


    //    ===================================================
//    ===================================================
//    ===================================================
    public function close(Request $request, $id)
    {
        $moving = Moving::with('receiver.parent')->where('id', $id)->firstOrFail();

        /*if (auth()->user()->isReceiverHandling($moving)) {
            abort(403);
        }*/

        // когда в user добавится subproject_id, заменить этот код комментом выше
        if (auth()->user()->project_id && (auth()->user()->project_id != $moving->receiver->parent_id)) {
            abort(403);
        }

        if ($moving->status != Moving::STATUS_RECEIVED) {
            return redirect()->back()
                ->with([
                    'error' => trans('validation.cant-be-closed',
                        ['status' => Moving::langStatuses()[$moving->status]])
                ]);
        }

        $moving->status = Moving::STATUS_CLOSED;
        $moving->save();

        return redirect()->route('moving', $id)->with(['message' => trans('alerts.data-not-changed')]);
    }


    //    ===================================================
//    ===================================================
//    ===================================================
    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    // - кто и при каких условиях её может менять? (М)
    // - не знаю (К)
    public function changeDate(Request $request, $id)
    {
        if (!$request->ajax()) {
            abort(403);
        }

        $query = Moving::with('sender.parent', 'receiver.parent')->where('id', $id);
        $users_project_id = (int)auth()->user()->project_id;
        if ($users_project_id) {
            $query
                ->whereHas('sender', function ($query) use ($users_project_id) {
                    $query->where('parent_id', $users_project_id);
                })
                ->orWhereHas('receiver', function ($query) use ($users_project_id) {
                    $query->where('parent_id', $users_project_id);
                });
        }
        $moving = $query->first();
        if (empty($moving)) {
            return ['errors' => [trans('validation.no-authority')]];
        }
        $r = $request->only('type', 'date');
        $validator = Validator::make($r, [
            'type' => ['required', 'in:send,received'],
            'date' => ['required', 'date_format:Y-m-d H:i:s']
        ]);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        if ($r['type'] == 'send') {
            $moving->send_date = $r['date'];
        } else {
            $moving->received_date = $r['date'];
        }
        $moving->save();

        return [
            'message' => trans('alerts.data-not-changed')
        ];
    }

//    ===================================================
//    ===================================================
//    ===================================================

// можно складууходить в минус
    public function yesMinusStorage($my_sub_project, $ids) {

        $PP = ProductProject::tableName();
        $P = Product::tableName();

        return ProductProject::leftJoin($P . ' as p', $PP . '.product_id', '=', 'p.id')
            ->where($PP . '.subproject_id', $my_sub_project)
            ->whereNotIn('p.id', $ids)
            ->select(DB::raw('p.id id, p.title title'));
    }

//    ===================================================
//    ===================================================
//    ===================================================
// нельзя складууходить в минус
    public function noMinusStorage($my_storage, $ids) {

        $SC = StorageContent::tableName();
        $P = Product::tableName();

        return StorageContent::leftJoin($P . ' as p', $SC . '.product_id', '=', 'p.id')
            ->where($SC . '.amount', '>', 0)
            ->where($SC . '.project_id', $my_storage)
            ->whereNotIn('p.id', $ids)
            ->select(DB::raw('p.id id, p.title title, ' . $SC . '.amount amount, ' . $SC . '.hold hold'));
    }

//    ===================================================
//    ===================================================
//    ===================================================










}
