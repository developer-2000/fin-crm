<?php

namespace App\Http\Controllers;

use App\Models\Api\Kazpost\KazpostSender;
use App\Models\Api\Measoft\MeasoftSender;
use App\Models\Api\Ninjaxpress\NinjaxpressKey;
use App\Models\Api\NovaposhtaKey;
use App\Models\Api\CdekKey;
use App\Models\Api\Russianpost\RussianpostSender;
use App\Models\Api\ViettelKey;
use App\Models\Api\WeFast\WeFastCounterparty;
use App\Models\ColdCallList;
use App\Models\CollectorLog;
use App\Models\Company;
use App\Models\Country;
use App\Models\Feedback;
use App\Models\Offer;
use App\Models\Operation;
use App\Models\OrdersPass;
use App\Models\OrdOrder;
use App\Models\Partner;
use App\Models\Pass;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Project;
use App\Models\Projects_new;
use App\Models\Script;
use App\Models\Tag;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\Template;
use App\Models\Transaction;
use App\Models\OperatorMistake;
use App\Models\ViettelSender;
use App\Repositories\DeliveryRepository;
use App\Repositories\FilterRepository;
use App\Services\PhoneCorrection\PhoneCorrectionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Comment;
use App\Models\OrderProduct;
use App\Models\Campaign;
use App\Models\NP;
use App\Models\CallProgressLog;
use App\Models\Pagination;
use App\Models\OrdersLog;
use App\Models\OrdersOpened;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psr\Log\NullLogger;
use Psy\Util\Json;
use App\Models\OffersScript;
use App\Models\ProcStatus;
use App\Http\Requests\CdekSendOrderRequest;

class DeliveryController extends BaseController
{

    protected $repDelivery;

    public function __construct(User $auth, Permission $permissionsModel, DeliveryRepository $repDelivery)
    {
        $this->repDelivery = $repDelivery;

        parent::__construct($auth, $permissionsModel);
    }


//    ===================================================
//    ===================================================
//    ===================================================
// все поставки
    public function all_orders(Request $request) {
        return $this->repDelivery::all_orders($request);
    }


//    ===================================================
//    ===================================================
//    ===================================================
// стр создания поставки
    public function create() {

        $projects = Projects_new::CheckProj()->where('type', 1)->orderBy('id', 'asc')->get();
        $subProjects = Projects_new::CheckSub()->where('type', 2)->orderBy('id', 'asc')->get();

//        dd($subProjects->toArray());
        return view('delivery.create', compact('projects', 'subProjects'));
    }


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из creat_order.js совершает поиск и возвращает список товара
    public function ProductsList(Request $request) {
        return \Response::json($this->repDelivery::ProductsList($request));
    }


//    ===================================================
//    ===================================================
//    ===================================================
// выборка ajax из creat_order.js возвращает выбраный товар
    public function plusProduct(Request $request) {
        return $this->repDelivery::plusProduct($request);
    }


//    ===================================================
//    ===================================================
//    ===================================================
// вставка ajax из creat_order.js создает заказ в поставках
    public function addOrder(Request $request) {

        return $this->repDelivery::addOrder($request);
    }


//    ===================================================
//    ===================================================
//    ===================================================
// стр редактиования заказа
    public function oneOrder(int $id) {

        $order = OrdOrder::with('ordProduct', 'ordComent')
            ->where(['id' => $id])->firstOrFail();

        return $order;
    }


//    ===================================================
//    ===================================================
//    ===================================================





}
