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

class OrderController extends BaseController
{

    public function __construct(User $auth, Permission $permissionsModel)
    {
        parent::__construct($auth, $permissionsModel);
    }

    /**
     * @param Request $request
     * @param $oldStatus
     * @return bool
     */
    public static function checkIfToUpdateStatus(Request $request, $oldStatus): bool // check if proc status
    {
        if ($request->procStatus != $oldStatus->id) {
            return true;
        } else {
            return false;
        }
    }

    function index( Request $request, Order $orderModel, Product $productModel, Country $countryModel ) {

//        dd(Projects_new::StatusName(2));


        $filter = [
            'id'               => $request->input('id'),
            'surname'          => $request->input('surname'),
            'phone'            => $request->input('phone'),
            'ip'               => $request->input('ip'),
            'oid'              => $request->input('oid'),
            'country'          => $request->input('country'),
            'project'          => $request->input('project'),
            'sub_project'      => $request->input('sub_project'),
            'division'         => $request->input('division'),
            'status'           => $request->input('status'),
            'sub_status'       => $request->input('sub_status'),
            'target'           => $request->input('target'),
            'partners'         => $request->input('partners'),
            'offers'           => $request->input('offers'),
            'product'          => $request->input('product'),
            'date-type'        => $request->input('date-type'),
            'date_start'       => $request->input('date_start'),
            'date_end'         => $request->input('date_end'),
            'deliveries'       => $request->input('deliveries'),
            'track'            => $request->input('track'),
            'grouping'         => $request->input('grouping'),
            'display_products' => $request->input('display_products'),
            'entity'           => $request->input('entity'),
            'products_count'   => $request->input('products_count'),
            'track_filter'     => $request->input('track_filter'),
            'order_cell'       => $request->input('order_cell'),
            'order_sort'       => $request->input('order_sort'),
            'initiator'        => $request->input('initiator'),
            'initiatorName'    => $request->input('initiatorName'),
            'tag_source'       => $request->input('tag_source'),
            'tag_medium'       => $request->input('tag_medium'),
            'tag_content'      => $request->input('tag_content'),
            'tag_campaign'     => $request->input('tag_campaign'),
            'tag_term'         => $request->input('tag_term'),
        ];
        $dataFilters = FilterRepository::processFilterData($filter);

        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('orders') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $data = $orderModel->getOrders($filter);

        $data['statuses'] = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)
            ->statusesUser()
            ->get()
            ->keyBy('id');

        $data['statusesGrouping'] = collect();

        $data['companies'] = Company::all()->keyBy('id');
        $data['projects'] = Project::all()->keyBy('id');
        $data['partners'] = Partner::all()->keyBy('id');
        $data['products'] = $productModel->getAllProducts();
        $data['offers'] = collect($productModel->getOffersFromOffers())->keyBy('id');
        $data['country'] = collect($countryModel->getAllCounties())->keyBy('code');
        $data['deliveries'] = TargetConfig::where('target_type', 'approve')->get(['id', 'name']);

//        $data['ordersToPrintCollections'] = collect(Order::where('proc_status', 24)->get())->groupBy('project_id');
//        foreach ($data['ordersToPrintCollections'] as $key => $collection) {
//            $procStatuses = ProcStatus::where([['project_id', $key], ['action', 'sent']])
//                ->first(['id', 'name']);
//            $collection->procStatus = !empty($procStatuses) ? $procStatuses :
//                ProcStatus::where('action', 'sent')->first(['id', 'name']);
//        }
        return view('orders.index', $data, $dataFilters);
    }

    /**
     * Страница всех заказов
     * @param Request $request
     * @param Order $orderModel
     * @param Company $companyModel
     * @param Pagination $paginationModel
     * @param OrderProduct $ordersOffersModel
     * @param Comment $commentsModel
     * @param CallProgressLog $callProgressLogModel
     * @param Product $productModel
     * @param Country $countryModel
     * @param User $authModel
     * @param null $tab
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    function requests( Request $request, Order $orderModel, Country $countryModel, User $userModel ) {
        $filter = [
            'date-type'     => $request->input('date-type'),
            'date_start'    => $request->input('date_start'),
            'date_end'      => $request->input('date_end'),
            'owner'         => $request->input('owner'),
            'country'       => $request->input('country'),
            'product'       => $request->input('product'),
            'offers'        => $request->input('offers'),
            'status'        => $request->get('status'),
            'target'        => $request->input('target'),
            'id'            => $request->input('id'),
            'name'          => $request->input('name'),
            'surname'       => $request->input('surname'),
            'middle'        => $request->input('middle'),
            'phone'         => $request->input('phone'),
            'ip'            => $request->input('ip'),
            'group'         => $request->input('group'),
            'user'          => $request->input('user'),
            'oid'           => $request->input('oid'),
            'company'       => $request->input('company'),
            'project'       => $request->input('project'),
            'partners'      => $request->input('partners'),
            'sub_project'   => $request->input('sub_project'),
            'division'      => $request->input('division'),
            'entity'        => $request->input('entity'),
            'cause_cancel'  => $request->input('cause_cancel'),
            'not_available' => $request->input('not_available'),
            'tag_source'    => $request->input('tag_source'),
            'tag_medium'    => $request->input('tag_medium'),
            'tag_content'   => $request->input('tag_content'),
            'tag_campaign'  => $request->input('tag_campaign'),
            'tag_term'      => $request->input('tag_term'),
        ];

        $dataFilters = FilterRepository::processFilterData($filter);
        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('requests') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $data = $orderModel->getOrdersRequests($filter);

        //костыль
        $target = TargetConfig::where('alias', 'cancel')->value('options');
        $data['cause_cancel'] = (json_decode($target ?? '', true))['cause']['options'] ?? [];

        $data['company_elastix'] = Campaign::orderBy('position', 'asc')->get(['id', 'name', 'company_id', 'position'])
            ->keyBy('id');
        $data['companies'] = Company::get(['id', 'name'])->keyBy('id');
        $data['projects'] = Project::project()->get(['id', 'name'])->keyBy('id');
        $data['partners'] = Partner::all()->keyBy('id');
        $data['offers'] = Offer::get(['id', 'partner_id', 'name', 'offer_id'])->keyBy('id');
        $data['countries'] = collect($countryModel->getAllCounties())->keyBy('code');
        $data['users'] = collect($userModel->getOperators(auth()->user()->company_id))->keyBy('id');
        $data['dataStatus'] = ProcStatus::callCenterStatuses()->get()->keyBy('id');

        return view('orders.requests', $data, $dataFilters);
    }

    /**
     * @param Request $request
     * @param Order $orderModel
     * @return \Illuminate\Http\JsonResponse
     */
    function changeElastixCompanyOrdersAjax(Request $request, Order $orderModel)
    {
        $result = [
            'success' => false,
            'message' => '',
        ];

        if ($orderModel->changeCompanyElastix($request->input('id'), $request->input('data'))) {
            $result['success'] = true;
            $result['message'] = trans('alerts.data-successfully-changed');
        }

        if ($request->isMethod('post')) {
            return response()->json([
                'status' => $orderModel->changeCompanyElastix($request->input('id'), $request->input('data'))
            ]);
        }
        abort(404);
    }

    /**
     * Страница одного заказа
     * @param $orderId
     * @param Order $orderModel
     * @param Comment $commentsModel
     * @param CallProgressLog $callProgressLogModel
     * @param OrderProduct $ordersOffersModel
     * @param Country $countryModel
     * @param NP $npModel
     * @param Company $companyModel
     * @param Request $request
     * @param OrdersOpened $orderOpenedModel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    function edit(
        $orderId,
        Order $orderModel,
        Comment $commentsModel,
        CallProgressLog $callProgressLogModel,
        OrderProduct $ordersOffersModel,
        Country $countryModel,
        Request $request,
        OrdersOpened $orderOpenedModel,
        Product $productModel,
        TargetValue $targetValuesModel
    ) {
        //todo переделать
        $data['orderOne'] = $orderModel->getOneOrder($orderId);
        $data['orderStatus'] = ProcStatus::find($data['orderOne']->proc_status);
        $orderProject = isset(Order::find($orderId)->project->name) ? Order::find($orderId)->project->name : '';
        $orderSubProject = isset(Order::find($orderId)->subProject->name) ? Order::find($orderId)->subProject->name : '';
        $data['orderProject'] = !empty($orderProject) ? $orderProject : NULL;
        $data['orderSubProject'] = !empty($orderSubProject) ? $orderSubProject : NULL;

        $data['offers'] = $ordersOffersModel->getProductsByOrderId($orderId, $data['orderOne']->subproject_id ?? 0);

        $flag = $request->input('flag');
        $ordersLogModel = new OrdersLog;

        if ($flag) {
            $result = $orderModel->setUserForOrders($orderId);
            if ($result) {
                $ordersLogModel->addOrderLog($orderId, 'Заказ открылся оператору');
            }
            $learningStatus = DB::table('company_elastix')->where('id', $data['orderOne']->proc_campaign)
                ->value('learning');
            $orderOpenedModel->add([
                'order_id'     => $orderId,
                'user_id'      => auth()->user()->id,
                'date_opening' => now(),
                'unique_id'    => $flag,
                'learning'     => $learningStatus
            ]);
            return redirect()->route('order', $orderId);
        } else {
            $ordersLogModel->addOrderLog($orderId, $_SERVER['REMOTE_ADDR'] . " открыл заказ");
        }

        $data['userCalls'] = '';
        $list = ColdCallList::where('order_id', $orderId)->first();
        if (!empty($list)) {
            $data['userCalls'] = $callProgressLogModel->getCallProgressLogById($list->id, $entity = 'cold_call');
            $orderColdCallLog = $callProgressLogModel->getCallProgressLogById($orderId, $entity = 'cold_call');
            if (!empty($orderColdCallLog)) {
                foreach ($orderColdCallLog as $k => $v) {
                    $data['userCalls'][] = $v;
                }
            }
        } else {
            $data['userCalls'] = $callProgressLogModel->getCallProgressLogById($orderId, $entity = 'order');
        }

        $data['log'] = $ordersLogModel->getOrderLogById($orderId);
        $data['samePhone'] = $orderModel->getCountOrdersByPhone($orderId, $data['orderOne']->phone_input, $data['orderOne']->host);
        $data['comments'] = $commentsModel->getComments($orderId, 'order', 'comment');
        $data['smsComments'] = $commentsModel->getComments($orderId, 'order', 'sms');
        $data['suspicious_comment'] = $commentsModel->getLastComment($orderId, $data['orderOne']->entity, 'suspicious');
        $data['country'] = collect($countryModel->getAllCounties())->keyBy('code');
        $data['recommended_products'] = $productModel->getRecommendedProductsGroupByType($data['orderOne']->offer_id, $data['orderOne']->geo);
        $data['processingStatus'] = $orderModel->getOrderFromProcessing($orderId);
        $data['target_value'] = $targetValuesModel->getTargetValue($orderId);
        $data['templates'] = Template::all();

        $data['targets_approve'] = TargetConfig::getConfigsByTarget('approve', $data['orderOne']);
        $data['targets_refuse'] = TargetConfig::getConfigsByTarget('refuse', $data['orderOne']);
        $data['targets_cancel'] = TargetConfig::getConfigsByTarget('cancel', $data['orderOne']);

        $data['target_option']['approve'] = TargetConfig::where('id', $data['orderOne']->target_approve)
            ->where('active', 1)->first();
        $data['target_option']['refuse'] = TargetConfig::where('id', $data['orderOne']->target_refuse)
            ->where('active', 1)->first();
        $data['target_option']['cancel'] = TargetConfig::where('id', $data['orderOne']->target_cancel)
            ->where('active', 1)->first();

        $dataGrouped = DB::table('orders_opened as oop')->where('oop.order_id', $orderId)->select('*')->get();
        $script = '';
        if (!empty($data['orderOne']->offer_id)) {
            $scriptId = OffersScript::where('offer_id', $data['orderOne']->offer_id)->pluck('script_id')->first();
            $script = Script::with([
                'scriptDetails' => function ($query) use ($data) {
                    $query->whereIn('geo', ['', NULL, $data['orderOne']->geo])
                        ->orWhereNull('geo')
                        ->orderBy('position');
                }
            ])
                ->where([['id', $scriptId], ['status', 'active']])
                ->first();
        };

        $orderUsers = [];
        if ($dataGrouped) {
            foreach ($dataGrouped as $item) {
                $item->comments = Comment::where([['order_id', $item->order_id], ['user_id', $item->user_id]])->get();
                $item->call_progress_log = CallProgressLog::where([
                    ['order_id', $item->order_id],
                    ['user_id', $item->user_id]
                ])
                    ->get();
                $item->logs = OrdersLog::where([['order_id', $item->order_id], ['user_id', $item->user_id]])->get();
                $item->user = User::with('company')->where('id', $item->user_id)->first();
                $orderUsers[] = $item->user;
                $item->feedback = Feedback::where([
                    ['moderator_id', auth()->user()->id],
                    ['order_id', $orderId],
                    ['user_id', $item->user_id],
                    ['orders_opened_id', $item->id]
                ])
                    ->first();
            }
        }
        $data['dataGrouped'] = $dataGrouped;
        $data['script'] = $script;
        $data['operatorMistakes'] = OperatorMistake::all();

        if ($data['orderOne']->locked) {
            return view('orders.edit-locked', $data);
        }

        return view('orders.edit', $data);
    }

    function orderSending(
        Request $request,
        $orderId,
        Comment $commentsModel,
        CallProgressLog $callProgressLogModel,
        OrderProduct $ordersOffersModel,
        Country $countryModel,
        TargetValue $targetValuesModel
    ) {
        $data['orderOne'] = Order::with([
            'project',
            'subProject',
            'procStatus',
            'collectorLogs' => function ($q) {
                $q->noProcessed();
            }
        ])
            ->moderated()
            ->checkAuth()
            ->targetApprove()
            ->serviceNotCallCenter()
            ->findOrFail($orderId);

        $data['target_value'] = $targetValuesModel->getTargetValue($orderId);
        $data['offers'] = $ordersOffersModel->getProductsByOrderId($orderId, $data['orderOne']->subproject_id ?? 0);

        $ordersLogModel = new OrdersLog;

        if ($request->input('flag')) {
            $collectorLog = CollectorLog::updateOrCreateAutoLog($orderId);
            if ($collectorLog) {
                $ordersLogModel->addOrderLog($orderId, 'Заказ открылся коллектору');
            }
            return redirect()->route('order-sending', $orderId);
        }

        $ordersLogModel->addOrderLog($orderId, $_SERVER['REMOTE_ADDR'] . " открыл заказ");

        $data['userCalls'] = [];
        $list = ColdCallList::where('order_id', $orderId)->first();
        if (!empty($list)) {
            $data['userCalls'] = $callProgressLogModel->getCallProgressLogById($list->id, $entity = 'cold_call');
            $orderColdCallLog = $callProgressLogModel->getCallProgressLogById($orderId, $entity = 'cold_call');
            if (!empty($orderColdCallLog)) {
                foreach ($orderColdCallLog as $k => $v) {
                    $data['userCalls'][] = $v;
                }
            }
        } else {
            $data['userCalls'] = $callProgressLogModel->getCallProgressLogById($orderId, $entity = 'order');
        }

        $data['log'] = $ordersLogModel->getOrderLogById($orderId);
//        if(\auth()->user()->id = 987593){
//            dd( $data['log']);
//        }
        $data['comments'] = $commentsModel->getComments($orderId, 'order', 'comment');
        $data['smsComments'] = $commentsModel->getComments($orderId, 'order', 'sms');
        $data['country'] = collect($countryModel->getAllCounties())->keyBy('code');
        $data['target_value'] = $targetValuesModel->getTargetValue($orderId);

        $data['divisions'] = Project::division($data['orderOne']->subproject_id)->get();
        $data['targets_approve'] = TargetConfig::getConfigsByTarget('approve', $data['orderOne']);
        $data['target_option']['approve'] = TargetConfig::where('id', $data['orderOne']->target_approve)
            ->where('active', 1)->first();

        //   $integrationClassVars = get_class_vars(\App\Http\Controllers\Api\IntegrationController::$modelNameSpace . studly_case($data['target_option']['approve']->alias));
        $data['dataGrouped'] = DB::table('orders_opened as oop')->where('oop.order_id', $orderId)->select('*')->get();
        $orderUsers = [];
        foreach ($data['dataGrouped'] as $item) {
            $item->comments = Comment::where([['order_id', $item->order_id], ['user_id', $item->user_id]])->get();
            $item->call_progress_log = CallProgressLog::where([
                ['order_id', $item->order_id],
                ['user_id', $item->user_id]
            ])
                ->get();
            $item->logs = OrdersLog::where([['order_id', $item->order_id], ['user_id', $item->user_id]])->get();
            $item->user = User::with('company')->where('id', $item->user_id)->first();
            $orderUsers[] = $item->user;
            $item->feedback = Feedback::where([
                ['moderator_id', auth()->user()->id],
                ['order_id', $orderId],
                ['user_id', $item->user_id],
                ['orders_opened_id', $item->id]
            ])->first();
        }

        $procStatussesQuery = ProcStatus::senderStatuses()
            ->status();
//            ProcStatus::where([['project_id', $data['orderOne']->project_id], ['parent_id', 0], ['type', 'senders']]);
        $data['procStatuses'] = $procStatussesQuery->checkProject()->orderBy('priority')->get();
        $data['procStatusesSystem'] = ProcStatus::where([
            ['project_id', 0],
            ['parent_id', 0],
            ['type', 'senders']
        ])->get();
        $procStatusesIds = $data['procStatuses']->pluck('id');
        $data['procStatuses2'] = ProcStatus::whereIn('parent_id', $procStatusesIds)->get();

        $data['templates'] = Template::all();
        $data['operatorMistakes'] = OperatorMistake::all();

        $data['otherParams'] = $this->getParamsForOtherFields($data['target_option']['approve'], $data);
        $data['documentTracks'] = !empty($data['target_value']) ? $data['target_value']->novaposhtaTracks : NULL;//todo переделать0

        $data['operations'] = Operation::with('log')->where('order_id', $data['orderOne']->id)->get();
        $data['statuses'] = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)
//            ->statusesUser()
                ->where(function ($q) use ($data) {
                    $q->where('project_id', $data['orderOne']->project_id)
                        ->orWhere('project_id', 0);
            })
            ->whereNotIn('action', [
                'paid_up',
                'refused',
                'sent',
                'received',
                'returned',
                'at_department',
                'cancel_send',
                'search',
                'claim',
                'dispute',
                'reversal'
            ])//костыль
            ->get()
            ->keyBy('id');

        //переделать когда сделаем функционал для редактирования заказа
        if ($data['orderOne']->locked) {
            $data['statuses'] = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)
                ->where(function ($q) use ($data) {
                    $q->where('project_id', $data['orderOne']->project_id)
                        ->orWhere('project_id', 0);
                })
                ->where('stage', 0)
                ->whereIn('action_alias', [
                    'returned',
                    'received',
                    'at_department',
                    'search',
                    'claim',
                    'dispute'
                ])
                ->get()
                ->keyBy('id');
            return view('orders.sending-locked', $data);
        }

        return view('orders.sending', $data);
    }

    public function ordersPrint(
        Country $countryModel
    ) {
        $data['companies'] = Company::all()->keyBy('id');
        $data['projects'] = Project::all()->keyBy('id');
        $data['country'] = collect($countryModel->getAllCounties())->keyBy('code');
        $data['deliveries'] = TargetConfig::where('target_type', 'approve')->get(['id', 'name']);
        $data['ordersCount'] = 0;
        $data['status'] = 0;

        if (auth()->user()->project_id) {
            $data['status'] = ProcStatus::where('project_id', auth()->user()->project_id)->where('action', 'to_print')
                ->first(['id', 'name', 'action']);
            if (!$data['status']) {
                $data['status'] = ProcStatus::where('action', 'to_print')
                    ->first(['id', 'name', 'action']);
            }
        } else {
            $data['status'] = ProcStatus::where([['action', 'to_print'], ['project_id', 0]])
                ->first(['id', 'name', 'action']);
        }
        $ordersBy = Order::where('proc_status', $data['status']->id)->whereHas('passPrint', function ($q) {
            $q->where([['user_id', auth()->user()->id], ['active', 1], ['type', 'to_print']]);
        })->get()->groupBy('target_approve');
        $data['ordersCount'] = $ordersBy->count();
        foreach ($ordersBy as $key => $orders) {
            $orders->tracks = TargetValue::whereIn('order_id', $orders->pluck('id'))
                ->pluck('track')
                ->toArray();
            foreach ($orders as $order) {
                $sentStatus = ProcStatus::where([['project_id', $order->project_id], ['action', 'sent']])
                    ->first(['id', 'name']);
                $order->procStatus = !empty($sentStatus) ? $sentStatus :
                    ProcStatus::where('action', 'sent')->first(['id', 'name']);
            }
        }

        $data['orders'] = $ordersBy;

        return view('orders.print', $data);
    }

    private function getParamsForOtherFields($target, $data)
    {
        $res = [];

        if (isset($target->alias)) {
            switch ($target->alias) {
                case 'novaposhta' :
                    {
                        $products = DB::table('order_products' . ' AS o')
                            ->select('o.id AS ooid', 'o.product_id', 'of.title', 'o.comment', \DB::raw('COUNT(o.id ) countProducts'))
                            ->join('products AS of', 'o.product_id', '=', 'of.id')
                            ->where('o.order_id', $data['orderOne']->id)
                            ->where('o.disabled', '!=', 1)
                            ->groupBy('o.product_id')
                            ->get();
                        $res = [
                            'order'              => $data['orderOne'] ?? null,
                            'offers'             => $data['offers'] ?? null,
                            'integrationKeys'    => NovaposhtaKey::where([
                                ['target_id', $data['orderOne']->target_approve],
                                // ['active', 1],
                                ['subproject_id', $data['orderOne']->subproject_id]
                            ])->get(),
                            'target_value'       => $data['target_value'] ?? null,
                            'target_option'      => $data['target_option'] ?? null,
                            'procStatuses'       => $data['procStatuses'] ?? null,
                            'procStatusesSystem' => $data['procStatusesSystem'] ?? null,
                            'field_values'       => isset($data['target_value']->values) ? json_decode($data['target_value']->values) : null,
                            'products'           => $products ?? null
                        ];
                        break;
                    }
                case 'wefast' :
                    {
                        $res = [
                            'senders'       => WeFastCounterparty::active()
                                ->orderSubProject($data['orderOne'] ?? null)
                                ->get(),
                            'total'         => $data['orderOne']->price_total ?? 0,
                            'order'         => $data['orderOne'] ?? NULL,
                            'target_value'  => $data['target_value'] ?? null,
                            'target_option' => $data['target_option'] ?? null,
                            'procStatuses'  => $data['procStatuses'] ?? null,
                        ];
                        break;
                    }
                case 'kazpost' :
                    {
                        $res = [
                            'target_value' => $data['target_value'] ?? null,
                            'senders'      => KazpostSender::orderSubProject($data['orderOne'] ?? null)->get(),
                            'order'        => $data['orderOne'] ?? NULL,
                            'procStatuses' => $data['procStatuses'] ?? null,
                        ];
                        break;
                    }
                case 'russianpost' :
                    {
                        $res = [
                            'target_value' => $data['target_value'] ?? null,
                            'senders'      => RussianpostSender::orderSubProject($data['orderOne'] ?? null)->get(),
                            'order'        => $data['orderOne'] ?? NULL,
                            'procStatuses' => $data['procStatuses'] ?? null,
                        ];
                        break;
                    }
                case 'measoft' :
                    {
                        $res = [
                            'target_value' => $data['target_value'] ?? null,
                            'senders'      => MeasoftSender::orderSubProject($data['orderOne'] ?? null)->get(),
                            'order'        => $data['orderOne'] ?? NULL,
                            'procStatuses' => $data['procStatuses'] ?? null,
                        ];
                        break;
                    }
                case 'viettel' :
                    {
                        $products = DB::table('order_products' . ' AS o')
                            ->select('o.id AS ooid', 'o.product_id', 'of.title', 'o.comment', \DB::raw('COUNT(o.id ) countProducts'))
                            ->join('products AS of', 'o.product_id', '=', 'of.id')
                            ->where('o.order_id', $data['orderOne']->id)
                            ->where('o.disabled', '!=', 1)
                            ->groupBy('o.product_id')
                            ->get();
                        $senders = ViettelSender::whereHas('key', function ($query) use ($data) {
                            $query->where('subproject_id', $data['orderOne']->subproject_id);
                        })->get();
                        $res = [
                            'offers'          => $data['offers'] ?? null,
                            'senders'         => $senders ?? null,
                            'products'        => $products ?? null,
                            'integrationKeys' => ViettelKey::with('senders')->where([
                                ['active', 1],
                                ['subproject_id', $data['orderOne']->subproject_id]
                            ])->get(),
                            'target_value'    => $data['target_value'] ?? null,
                            'field_values'    => isset($data['target_value']->values) ? json_decode($data['target_value']->values) : null,
                            'order'           => $data['orderOne'] ?? NULL,
                            'procStatuses'    => $data['procStatuses'] ?? null,
                            'target_option'   => $data['target_option'] ?? null,
                        ];
                        break;
                    }
                case 'cdek' :
                    {
                        $products = DB::table('order_products' . ' AS o')
                            ->select('o.id AS ooid', 'o.product_id', 'of.title', 'o.comment', \DB::raw('COUNT(o.id ) countProducts'))
                            ->join('products AS of', 'o.product_id', '=', 'of.id')
                            ->where('o.order_id', $data['orderOne']->id)
                            ->where('o.disabled', '!=', 1)
                            ->groupBy('o.product_id')
                            ->get();

                        $res = [
                            'offers'          => $data['offers'] ?? null,
                            'senders'         => $senders ?? null,
                            'products'        => $products ?? null,
                            'integrationKeys' => CdekKey::where('subproject_id', auth()->user()->sub_project_id)->get(),
                            'target_value'    => $data['target_value'] ?? null,
                            'field_values'    => isset($data['target_value']) ? json_decode($data['target_value']->values) : null,
                            'order'           => $data['orderOne'] ?? NULL,
                            'procStatuses'    => $data['procStatuses'] ?? null,
                            'target_option'   => $data['target_option'] ?? null,
                        ];
                        break;
                    }
                case 'ninjaxpress' :
                    {
                        $keys = NinjaxpressKey::where('subproject_id', $data['orderOne']->subproject_id)->get();
                        $res = [
                            'offers'        => $data['offers'] ?? null,
                            'keys'          => $keys ?? null,
                            'target_value'  => $data['target_value'] ?? null,
                            'field_values'  => isset($data['target_value']->values) ? json_decode($data['target_value']->values) : null,
                            'order'         => $data['orderOne'] ?? NULL,
                            'procStatuses'  => $data['procStatuses'] ?? null,
                            'target_option' => $data['target_option'] ?? null,
                        ];
                        break;
                    }
            }

            $target = isset($data['target_option']) ? array_shift($data['target_option']) : null;

            if ($target) {
                $fieldValues = ['target_option' => $target->options ? json_decode($target->options ?? '') : null];
                $res = array_merge($fieldValues, $res);
            }
        }

        return $res;
    }

    /**
     * Страница одного заказа
     * @param $orderId
     * @param Order $orderModel
     * @param Comment $commentsModel
     * @param CallProgressLog $callProgressLogModel
     * @param OrderProduct $ordersOffersModel
     * @param Country $countryModel
     * @param Request $request
     * @param OrdersOpened $orderOpenedModel
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    function orderOneManage(
        $orderId,
        Order $orderModel,
        Comment $commentsModel,
        CallProgressLog $callProgressLogModel,
        OrderProduct $ordersOffersModel,
        Country $countryModel,
        Request $request,
        OrdersOpened $orderOpenedModel,
        Product $productModel,
        TargetValue $targetValuesModel
    ) {

        $data['orderOne'] = $orderModel->getOneOrder($orderId);
        $data['orderOne']->campaign = Campaign::find($data['orderOne']->proc_campaign);

        $data['offers'] = $ordersOffersModel->getProductsByOrderId($orderId, $data['orderOne']->subproject_id ?? 0);

        $flag = $request->input('flag');
        $ordersLogModel = new OrdersLog;

        if ($flag) {
            $result = $orderModel->setUserForOrders($orderId);
            if ($result) {
                $ordersLogModel->addOrderLog($orderId, 'Заказ открылся оператору');
            }
            $orderOpenedModel->add([
                'order_id'     => $orderId,
                'user_id'      => auth()->user()->id,
                'date_opening' => now(),
                'unique_id'    => $flag
            ]);
            return redirect()->route('order', $orderId);
        } else {
            $ordersLogModel->addOrderLog($orderId, $_SERVER['REMOTE_ADDR'] . " открыл заказ");
        }
        $data['userCalls'] = '';
        $list = ColdCallList::where('order_id', $orderId)->first();
        if (!empty($list)) {
            $data['userCalls'] = $callProgressLogModel->getCallProgressLogById($list->id, $entity = 'cold_call');
            $orderColdCallLog = $callProgressLogModel->getCallProgressLogById($orderId, $entity = 'cold_call');
            if (!empty($orderColdCallLog)) {
                foreach ($orderColdCallLog as $k => $v) {
                    $data['userCalls'][] = $v;
                }
            }
        } else {
            $data['userCalls'] = $callProgressLogModel->getCallProgressLogById($orderId, $entity = 'order');
        }

        $data['log'] = '';
        $data['log'] = $ordersLogModel->getOrderLogById($orderId);
        $data['samePhone'] = $orderModel->getCountOrdersByPhone($orderId, $data['orderOne']->phone, $data['orderOne']->host);
        $data['comments'] = $commentsModel->getComments($orderId, 'order', 'comment');
        $data['suspicious_comment'] = $commentsModel->getLastComment($orderId, 'order', 'suspicious');
        $data['country'] = collect($countryModel->getAllCounties())->keyBy('code');
        $data['recommended_products'] = $productModel->getRecommendedProductsGroupByType($data['orderOne']->offer_id, $data['orderOne']->geo);
        $data['processingStatus'] = $orderModel->getOrderFromProcessing($orderId);
        $data['target_value'] = $targetValuesModel->getTargetValue($orderId);

        $data['targets_approve'] = TargetConfig::getConfigsByTarget('approve', $data['orderOne']);
        $data['targets_refuse'] = TargetConfig::getConfigsByTarget('refuse', $data['orderOne']);
        $data['targets_cancel'] = TargetConfig::getConfigsByTarget('cancel', $data['orderOne']);

        $data['target_option']['approve'] = TargetConfig::where('id', $data['orderOne']->target_approve)
            ->where('active', 1)->first();
        $data['target_option']['refuse'] = TargetConfig::where('id', $data['orderOne']->target_refuse)
            ->where('active', 1)->first();
        $data['target_option']['cancel'] = TargetConfig::where('id', $data['orderOne']->target_cancel)
            ->where('active', 1)->first();

        $dataGrouped = DB::table('orders_opened as oop')->where('oop.order_id', $orderId)->select('*')->get();
        $script = '';
        if (!empty($data['orderOne']->offer_id)) {
            $scriptId = OffersScript::where('offer_id', $data['orderOne']->offer_id)->pluck('script_id')->first();
            $script = Script::
            with([
                'scriptDetails' => function ($query) use ($data) {
                    $query->whereIn('geo', ['', NULL, $data['orderOne']->geo])
                        ->orWhereNull('geo')
                        ->orderBy('position');;
                }
            ])
                ->where([['id', $scriptId], ['status', 'active']])
                ->first();
        };
        $orderUsers = [];
        foreach ($dataGrouped as $item) {
            $item->comments = Comment::where([['order_id', $item->order_id], ['user_id', $item->user_id]])->get();
            $item->call_progress_log = CallProgressLog::where([
                ['order_id', $item->order_id],
                ['user_id', $item->user_id]
            ])
                ->get();
            $item->logs = OrdersLog::where([['order_id', $item->order_id], ['user_id', $item->user_id]])->get();
            $item->user = User::with('company')->where('id', $item->user_id)->first();
            $orderUsers[] = $item->user;
            $item->feedback = Feedback::where([
                ['moderator_id', auth()->user()->id],
                ['order_id', $orderId],
                ['user_id', $item->user_id],
                ['orders_opened_id', $item->id]
            ])->first();
        }

        $transactions = Transaction::where([['order_id', $orderId], ['active', 1]])->get();
        return view('orders.order-one-manage', $data, [
            'operators'        => User::where('campaign_id', $data['orderOne']->proc_campaign)->get(),
            'orderUsers'       => $orderUsers,
            'dataGrouped'      => $dataGrouped,
            'script'           => $script,
            'operatorMistakes' => OperatorMistake::all(),
            'campaigns'        => Campaign::all(),
            'transactions'     => $transactions
        ]);
    }

    public function saveModeratorChanges(Request $request, Order $orderModel)
    {
        return (new Order)->saveModeratorChanges($request);
    }

    /**
     * Поиск offer
     */
    function orderSearchOffersAjax(Request $request, Product $productModel, Order $orderModel)
    {
        if ($request->isMethod('post')) {
            $orderId = $request->input('orderId');
            $order = Order::find($orderId);
            $ccc = $orderModel->getCountryCompanyCurrency($orderId);
            if ($ccc == NULL) {
                exit;
            }

            $data = $productModel->search($ccc->project_id, $request->input('search'), $order->subproject_id);

            $html = view('orders.ajax.order_one_search_ajax', [
                'data'     => $data,
                'currency' => $ccc->currency,
            ])->render();
            return response()->json(['html' => $html]);
        }
        abort(404);
    }

    /**
     * Поиск offer
     */
    function orderSearchOffersLockedAjax(Request $request, Product $productModel, Order $orderModel)
    {
        if ($request->isMethod('post')) {
            $orderId = $request->input('orderId');
            $order = Order::find($orderId);
            $ccc = $orderModel->getCountryCompanyCurrency($orderId);
            if ($ccc == NULL) {
                exit;
            }

            $data = $productModel->search($ccc->project_id, $request->input('search'), $order->subproject_id);

            $html = view('orders.ajax.order_one_search_for_locked_ajax', [
                'data'     => $data,
                'currency' => $ccc->currency,
            ])->render();
            return response()->json(['html' => $html]);
        }
        abort(404);
    }

    public function saveOrderLockedChanges(Request $request)
    {
        $result = Order::saveOrderLockedChanges($request->all());
        if (!empty($result)) {
            return response()->json($result);
        } else {
            return response()->json(['errors' => 'Произошла ошибка на сервере!']);
        }
    }

    /**
     * Добавляем комментарий к заказу
     */
    function addCommentAjax(Request $request, Comment $commentsModel, Order $orderModel)
    {
        if ($request->isMethod('post')) {

            $orderId = $request->input('orderId');
            if (!$data = $orderModel->existOrder($orderId)) {
                exit;
            }

            $commentsModel->addComment($orderId, $request->input('comment'));
            $html = view('orders.ajax.order_one_comments_ajax', [
                'comments' => $commentsModel->getComments($orderId, 'order', 'comment')
            ])->render();
            $orderModel->getProcessingStatusOrderApi($orderId);
            return response()->json([
                'html'       => $html,
                'dateChange' => $orderModel->changeDateChange($orderId),
            ]);
        }
        abort(404);
    }

    /**
     * Получаем все города НП
     */
    function getCityNPAjax(Request $request, NP $npModel)
    {
        if ($request->isMethod('post')) {
            return response()->json(['data' => $npModel->getCity()]);
        }
        abort(404);
    }

    /**
     * Получаем все склады НП
     */
    function getWarehouseNPAjax(Request $request, NP $npModel)
    {
        if ($request->isMethod('post')) {
            return response()->json(['data' => $npModel->getWarehouse($request->input('id'))]);
        }
        abort(404);
    }

    function getStatusOrder2(
        Request $request,
        Order $orderModel,
        Comment $commentsModel,
        OrderProduct $ordersOffersModel
    ) {
        if ($request->input('key') != '2sNYIn8RUlxNQRgUDYqH') {
            die('Incorrect key');
        }
        echo $orderModel->getStatusOrderApi2($request->input('id'), $commentsModel, $ordersOffersModel);
    }

    function getStatusOrder4(Request $request, Order $orderModel)
    {
        if ($request->input('key') != '2sNYIn8RUlxNQRgUDYqH') {
            die('Incorrect key');
        }
        echo $orderModel->getStatusOrderApi4($request->input('ids'));
    }


    function setNotCallsCallbackAjax(
        Request $request,
        Order $orderModel,
        Pagination $paginationModel,
        OrderProduct $ordersOffersModel,
        CallProgressLog $callProgressLogModel,
        OrdersLog $ordersLogModel
    ) {
        if ($request->isMethod('post')) {
            $orderModel->setNotCallsCallback($request->input('id'));
            $ordersLogModel->addOrderLog($request->input('id'), 'Заказ закинут в прозвон');
            list($page, $filter) = $orderModel->getFilterModeration();
            $data = $orderModel->doNotCalls($page, $filter, $paginationModel, $ordersOffersModel, $callProgressLogModel);
            return response()->json([
                'html'    => view('orders.ajax.order_tab_do_not_calls', $data)->render(),
                'count'   => $data['countOrder'],
                'message' => trans('alerts.order-successfully-processed')
            ]);
        }
        abort(404);
    }

    /**
     * Шаблоны фильтров дат
     */
    function dateFilterTemplateAjax(Request $request, Order $orderModel)
    {
        if ($request->isMethod('post')) {
            $data = $orderModel->dateFilterTemplate($request->input('type'));
            return response()->json($data);
        }
        abort(404);
    }

    public function addNewProductAjax(
        Request $request,
        Order $orderModel,
        OrderProduct $ordersOffersModel,
        OrdersLog $ordersLogModel,
        Product $productModel,
        Transaction $transactionModel
    ) {
        if ($request->isMethod('post')) {
            $order = $orderModel->getOneOrder($request->input('orderId'));

            if (!$order/* || !StorageContent::checkAmountProduct($request->input('productId'), $order->subproject_id)*/) {
                abort(404);
            }
            $sum = $ordersOffersModel->addOrderOffers($request->input('orderId'), $request->input('productId'), $request->input('price'));
            if ($sum) {
                $orderModel->changeAllPriceAndDateChange($request->input('orderId'), $sum);
                $data['offers'] = $ordersOffersModel->getProductsByOrderId($order->id, $order->subproject_id);
                $orderModel->getProcessingStatusOrderApi($request->input('orderId'));
                if ($order && auth()->user()->role_id == 1) {
                    if ($order->target_status == 1) {
                        $transactionModel->setInActiveTransaction($request->input('orderId'), $order->target_user);
                        $transactionModel->createOrUpdateTransaction($request->input('orderId'), 'approve');
                    }
                }
                $log = 'Добавлен товар ' . $productModel->getOneProduct($request->input('productId'))->title . " с ценой " . $request->input('price');
                $ordersLogModel->addOrderLog($request->input('orderId'), $log);
                $data['price'] = $sum;
                $data['order'] = $order;
                $data['currency'] = $orderModel->getCountryCompanyCurrency($request->input('orderId'))->currency;

                $productsActive = DB::table('order_products' . ' AS o')
                    ->select('o.id AS ooid', 'o.product_id', 'of.title', 'o.comment', \DB::raw('COUNT(o.id ) countProducts'))
                    ->join('products AS of', 'o.product_id', '=', 'of.id')
                    ->where('o.order_id', $order->id)
                    ->where('o.disabled', '!=', 1)
                    ->groupBy('o.product_id')
                    ->get();
                $productsActiveList = '';
                // $productsActive = (new OrderProduct())->getProductsByOrderId($resultData['order']->id, $resultData['order']->subproject_id);
                if (!empty($productsActive)) {
                    foreach ($productsActive as $product) {
                        $productsActiveList .= $product->title . '(' . $product->countProducts . '), ';
                    }
                }

                return response()->json([
                    'success' => [
                        'price'              => $sum,
                        'productsActiveList' => $productsActiveList,
                        'html'               => view('orders.ajax.order_one_offers_ajax', $data)->render(),
                    ],
                    'message' => trans('alerts.product-added')
                ]);
            }
            return response()->json([
                'error'   => true,
                'message' => trans('alerts.product-not-added')
            ]);
        }
        abort(404);
    }

    public function addNewProductLockedAjax(
        Request $request,
        Order $orderModel,
        OrderProduct $ordersOffersModel
    ) {
        $order = $orderModel->getOneOrder($request->input('orderId'));

        $data['product'] = $ordersOffersModel->getProductsByOrderIdLocked($request->input('productId'), $order->subproject_id, $request->price);
        $data['currency'] = $orderModel->getCountryCompanyCurrency($request->input('orderId'))->currency;
        return response()->json([
            'success' => [
                //'price' => $sum,
                'html' => view('orders.ajax.order_one_append_product_locked', $data)->render(),
            ],
        ]);
        return response()->json([
            'error' => true
        ]);
        abort(404);
    }

    public function deleteProductFromOrder(
        Request $request,
        OrderProduct $ordersOffersModel,
        Order $orderModel,
        OrdersLog $ordersLogModel,
        Transaction $transactionModel
    ) {
        if ($request->isMethod('POST')) {
            $resultData = $ordersOffersModel->deleteProductFromOrder($request->get('productId'), $orderModel, $ordersLogModel);
            if (isset($resultData['order'])) {
                if ($resultData['order']) {
                    if ($resultData['order']->target_status == 1) {
                        $transactionModel->setInActiveTransaction($resultData['order']->id, $resultData['order']->target_user);
                        $transactionModel->createOrUpdateTransaction($resultData['order']->id, 'approve');
                    }
                }
            }

            $productsActive = DB::table('order_products' . ' AS o')
                ->select('o.id AS ooid', 'o.product_id', 'of.title', 'o.comment', \DB::raw('COUNT(o.id ) countProducts'))
                ->join('products AS of', 'o.product_id', '=', 'of.id')
                ->where('o.order_id', $resultData['order']->id)
                ->where('o.disabled', '!=', 1)
                ->groupBy('o.product_id')
                ->get();
            $resultData['productsActiveList'] = '';
            // $productsActive = (new OrderProduct())->getProductsByOrderId($resultData['order']->id, $resultData['order']->subproject_id);
            if (!empty($productsActive)) {
                foreach ($productsActive as $product) {
                    $resultData['productsActiveList'] .= $product->title . '(' . $product->countProducts . '), ';
                }
            }
            return response()->json($resultData);
        }
    }


    public function saveChangedProductsAjax(
        Request $request,
        OrderProduct $ordersOffersModel,
        Order $orderModel,
        $id,
        OrdersLog $ordersLogModel,
        Transaction $transactionModel
    ) {
        if ($request->isMethod('POST')) {
            $price = $ordersOffersModel->saveChangedProducts($request->get('data'), $id, $ordersLogModel);
            $orderModel->changeAllPriceAndDateChange($id, $price);
            $orderModel->getProcessingStatusOrderApi($id);
            $order = $orderModel->getOneOrder($id);
            if ($order) {
                if ($order->target_status == 1) {
                    $transactionModel->setInActiveTransaction($id, auth()->user()->id);
                    $transactionModel->createOrUpdateTransaction($id, 'approve');
                }
            }
            return response()->json([
                'success' => true,
                'price'   => $price
            ]);
        }
    }

    public function moderation(Request $request, Order $orderModel, Country $countriesModel)
    {
        $filter = [
            'page'        => $request->get('page'),
            'grouping'    => $request->get('grouping'),
            'country'     => $request->get('country'),
            'project'     => $request->get('project'),
            'sub_project' => $request->get('sub_project'),
            'offer'       => $request->get('offer'),
            'company'     => $request->get('company'),
            'id'          => $request->get('id'),
            'date_start'  => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end'    => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('moderation') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $countries = collect($countriesModel->getAllCounties())->keyBy('code');
        list($orders, $page, $count, $targets) = $orderModel->moderationOrder($filter);
        return view('moderation.moderation', [
            'projects'         => collect(Project::project()->get())->keyBy('id'),
            'subProjects'      => collect(Project::subProject()->get())->keyBy('id'),
            'offers_filter'    => (new Product)->getOffersNameNoParent(),
            'country'          => (new Country)->getAllCountryArray(),
            'companies'        => Company::all(),
            'campaigns'        => collect((new Campaign)->getAllCompanyElastix())->keyBy('id'),
            'orders'           => $orders,
            'pagination'       => $page,
            'countries'        => $countries,
            'count'            => $count,
            'targets'          => $targets,
            'operatorMistakes' => OperatorMistake::all()
        ]);
    }

    public function preModeration(Request $request, Order $orderModel, Country $countriesModel)
    {
        $filter = [
            'page'        => $request->get('page'),
            'grouping'    => $request->get('grouping'),
            'country'     => $request->get('country'),
            'project'     => $request->get('project'),
            'sub_project' => $request->get('sub_project'),
            'offer'       => $request->get('offer'),
            'company'     => $request->get('company'),
            'id'          => $request->get('id'),
            'date_start'  => $request->get('date_start') ? $request->get('date_start') : date("d.m.Y"),
            'date_end'    => $request->get('date_end') ? $request->get('date_end') : date("d.m.Y"),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('pre-moderation') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $countries = collect($countriesModel->getAllCounties())->keyBy('code');
        list($orders, $page, $count, $statuses) = $orderModel->preModerationOrder($filter);
        return view('moderation.pre-moderation', [
            'projects'         => collect(Project::project()->get())->keyBy('id'),
            'subProjects'      => collect(Project::subProject()->get())->keyBy('id'),
            'offers_filter'    => (new Product)->getOffersNameNoParent(),
            'country'          => (new Country)->getAllCountryArray(),
            'companies'        => Company::all(),
            'campaigns'        => collect((new Campaign)->getAllCompanyElastix())->keyBy('id'),
            'orders'           => $orders,
            'pagination'       => $page,
            'countries'        => $countries,
            'count'            => $count,
            'operatorMistakes' => OperatorMistake::all(),
            'statuses'         => $statuses,
        ]);
    }

    public function badConnection(
        Request $request,
        Order $orderModel,
        OrdersOpened $ordersOpenedModel,
        User $authModel,
        Company $companiesModel
    ) {
        $filter = [
            'grouping'   => $request->get('grouping'),
            'id'         => $request->get('id'),
            'oid'        => $request->get('oid'),
            'cause'      => $request->get('cause'),
            'user'       => $request->get('user'),
            'company'    => $request->get('company'),
            'status'     => $request->get('status'),
            'date_start' => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end'   => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('bad-connection') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $data = $ordersOpenedModel->getOrderWithBadConnection($filter);
        $data['users'] = collect($authModel->getOperatorName(auth()->user()->company_id))->keyBy('id');
        $data['companies'] = Company::all()->keyBy('id');
        return view('moderation.bad_connection', $data);
    }

    public function suspiciousOrders(
        Request $request,
        Order $orderModel,
        User $authModel,
        Country $countriesModel,
        Comment $commentsModel
    ) {
        $filter = [
            'id'         => $request->get('id'),
            'user'       => $request->get('user'),
            'company'    => $request->get('company'),
            'countries'  => $request->get('countries'),
            'date_start' => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end'   => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('suspicious-orders') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $data = $orderModel->getSuspiciousOrders($filter);

        if ($data['orders']) {
            foreach ($data['orders'] AS &$order) {
                $comment = $commentsModel->getLastComment($order->id, $order->entity, 'suspicious');
                $order->text = $comment->text;
            }
        }

        $data['countries'] = $countriesModel->getAllCounties();
        $data['users'] = collect($authModel->getOperatorName(auth()->user()->company_id))->keyBy('id');
        $data['companies'] = Company::all()->keyBy('id');

        return view('moderation.suspicious_orders', $data);
    }

    public function moderationChangePhoneAndCountryAjax(
        Request $request,
        Order $orderModel,
        OrdersLog $ordersLogModel,
        PhoneCorrectionService $phoneCorrectionService
    ) {
        if ($request->isMethod('post')) {
            $result = $orderModel->moderationChangePhoneAndCountry($request->all(), $phoneCorrectionService);
            if ($result['success']) {
                $ordersLogModel->addOrderLog($request->get('id'), 'Номер был изменен на ' . $result['success']);
                $ordersLogModel->addOrderLog($request->get('id'), 'Страна была изменена на ' . $request->get('country'));
            }

            return $result;
        }
        abort(404);
    }

    public function cancelAsRepeatAjax(Request $request, Order $orderModel, TargetValue $targetValueModel)
    {
        if ($request->isMethod('post') && $request->get('ids')) {
            $res['orders'] = $orderModel->cancelAsRepeatAndSetLog($request->get('ids'));
            $res['target'] = $targetValueModel->setTargetAsRepeat($request->get('ids'));
            foreach ($request->get('ids') as $id) {
                $orderModel->deleteCallsForElastix($id);
                $orderModel->getProcessingStatusOrderApi($id);
            }
            $res['success'] = true;
            $res['message'] = trans_choice('alerts.order-successfully-processed', count($request->get('ids')));
            if (!$res['orders'] || !$res['target']) {
                $res['success'] = false;
                $res['message'] = trans('alerts.validation-error');
            }
            return response()->json($res);
        }
        abort(404);
    }

    /**
     * @param Request $request
     * @param Order $orderModel
     * @param TargetsFinal $targetsFinalModel
     * аннулируем заказ и модерируем его
     */
    public function cancelAndModeration(Request $request, Order $orderModel, OrdersLog $ordersLogModel)
    {
        $order = Order::where('id', $request->get('order_id'))->first();
        $orderModel->deleteCallsForElastix($order->id);
        if ($request->isMethod('post') && $order) {
            $options = TargetConfig::where('id', $order->target_cancel)->value('options');
            if ($options) {
                $fields = json_decode($options);
                $rules = ['order_id' => 'required|numeric'];
                foreach ($fields as $field) {
                    if ($field->field_required) {
                        $rules[$field->field_name] = 'required|min:1|max:255';
                    }
                }
                $this->validate($request, $rules);

                foreach ($fields as $field) {
                    if ($request->get($field->field_name)) {
                        $field->field_value = $request->get($field->field_name);
                    }
                }

                $res['orders'] = $orderModel->cancelOneOrder($request->get('order_id'));
                $res['message'] = trans('alerts.order-not-moderated');

                if ($res['orders']) {
                    $targetValue = new TargetValue;
                    $targetValue->deleteValue($request->get('order_id'));
                    $targetValue->order_id = $request->get('order_id');
                    $targetValue->target_id = $order->target_cancel;
                    $targetValue->values = json_encode($fields);
                    $targetValue->time_created = now();
                    $res['target'] = $targetValue->save();
                }

                if ($res['orders'] && $res['target']) {
                    $ordersLogModel->addOrderLog($request->get('order_id'), "Аннулирован и промодерирован");
                    $orderModel->getProcessingStatusOrderApi($request->get('order_id'));
                    $res['message'] = trans('alerts.order-cenceled-and-modereted');
                }

                return response()->json($res);
            }
        }
        abort(404);
    }

    /**
     * получаем view всех похожих заказов на стрнице заказа
     */
    public function repeatOrdersInOrderAjax(Request $request, Order $orderModel)
    {
        if ($request->isMethod('post')) {
            $data = $orderModel->getOrderByPhone($request->get('id'));
            return response()->json([
                'html' => view('orders.repeat-orders-in-order', $data)->render()
            ]);
        }
        abort(404);
    }

    public function moderationOrderAjax(Request $request, $id, Order $orderModel, OrdersLog $ordersLogModel)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'assigned_operator' => 'nullable',
            ]);

            $order = Order::find($id);
            $result = $orderModel->setModeration($order);
            $result['message'] = trans('alerts.order-not-moderated');
            if (isset($result['success']) && $result['success']) {
                if ($request->storage && $request->storage != $order->subproject_id) {

                    $text = "Склад был изменен ";
                    $text .= $order->subProject ? 'c ' . $order->subProject->name : '';

                    $order->subproject_id = $request->storage;
                    $order->save();

                    $newSubProject = Project::find($request->storage);
                    $text .= $newSubProject ? ' на ' . $newSubProject->name : '';

                    $ordersLogModel->addOrderLog($id, $text);
                }
                if ($request->assigned_operator && $request->assigned_operator != $order->target_user) {

                    $text = 'Заказ закреплен за оператором: ';
                    $order->target_user = $request->assigned_operator;
                    $order->save();

                    $text .= isset($order->targetUser->name) && isset($order->targetUser->surname)
                        ? $order->targetUser->name . ' ' . $order->targetUser->surname . '(' . $order->targetUser->id . ')' : '';

                    $ordersLogModel->addOrderLog($id, $text);
                } elseif (!$request->assigned_operator && !$order->target_user) {
                    $text = 'Заказ закреплен за модератором: ';
                    $order->target_user = Auth::user()->id;
                    $order->save();

                    $text .= isset($order->targetUser->name) && isset($order->targetUser->surname)
                        ? $order->targetUser->name . ' ' . $order->targetUser->surname . '(' . $order->targetUser->id . ')' : '';

                    $ordersLogModel->addOrderLog($id, $text);
                }

                try {
                    $orderOpened = OrdersOpened::where('user_id', $request->assigned_operator)
                        ->where('order_id', $order->id)
                        ->whereNotNull('date_closed')
                        ->where('target_status', $order->target_status)->orderBy('id', 'desc')->first();
                    if($orderOpened){
                        $orderOpened->moderation_id = auth()->user()->id;
                        $orderOpened->moderation_time = now();
                        $orderOpened->save();
                    }
                } catch (\Exception $exception) {
                }

                $orderModel->getProcessingStatusOrderApi($id);
                $ordersLogModel->addOrderLog($id, 'Заказ промодерирован');
                $result['message'] = trans('alerts.order-successfully-moderated');
            }
            return response()->json($result);
        }
        abort(404);
    }

    /**
     * отмечаем "повтор" как "не повтор"
     */
    public function goToPbxAjax(Request $request, Order $orderModel, OrdersLog $ordersLogModel)
    {
        if ($request->isMethod('post')) {

            $result['success'] = $orderModel->repeatAsNotRepeat($request->get('ids'));

            $result['message'] = trans_choice('alerts.order-not-processed', count($request->get('ids')));
            if ($result['success']) {
                foreach ($request->get('ids') as $id) {
                    $ordersLogModel->addOrderLog($id, 'Повтор заброшен в прозвон. Установлен Процессинг статус - В обработке',
                        ['status_id' => 1, 'status_name' => 'В обработке']);
                }
                $result['message'] = trans_choice('alerts.order-successfully-processed', count($request->get('ids')));
            }

            return response()->json($result);
        }
        abort(404);
    }

    /**
     * подтверждаем звонок как плохая связь
     */
    public function confirmBadConnectionAjax(Request $request, $id, OrdersOpened $ordersOpenedModel)
    {
        if ($request->isMethod('post')) {
            $result = [
                'success' => $ordersOpenedModel->setVerifiedUid($id),
                'message' => trans('alerts.record-not-verified'),
                'html'    => trans('general.verified-by') . ' : <br>' . auth()->user()->surname . ' ' . auth()->user()->name
            ];

            if ($result['success']) {
                $result['message'] = trans('alerts.record-successfully-verified');
            }

            return response()->json($result);
        }
        abort(404);
    }

    /**
     * ложный автоотыетчик/плохая связь
     */
    public function cancelBadConnectionAjax(Request $request, $id, OrdersOpened $ordersOpenedModel)
    {
        if ($request->isMethod('post')) {
            $result = [
                'success' => $ordersOpenedModel->setVerifiedUid($id, ['callback_status' => 4]), //4 - врунишки
                'html'    => trans('orders.verified-by') . ' : <br>' . auth()->user()->surname . ' ' . auth()->user()->name,
                'message' => trans('alerts.record-not-verified'),
                'text'    => trans('orders.marked-as') . ' ' . trans('orders.not-answerphone') . '/' . trans('orders.bad-connection'),
            ];

            if ($result['success']) {
                $result['message'] = trans('alerts.record-successfully-verified');
            }
            return response()->json($result);
        }
        abort(404);
    }

    /**
     *
     */
    public function incomingCall($phone = null, Order $orderModel)
    {
        if (!$phone) {
            abort(404);
        }
        $orders = $orderModel->getOrdersByPhone($phone);
        return view('incoming_line.orders_by_phone', [
            'orders' => $orders,
            'phone'  => $phone
        ]);
    }

    /**
     * старница создание заказа для входящей линии
     */
    public function incomingCallCreateOrder(Country $countryModel)
    {
        $data['countries'] = $countryModel->getAllCounties();
        return view('incoming_line.create_order', $data);
    }

    /**
     * создание заказа
     */
    public function incomingCallCreateOrderAjax(Request $request, Order $orderModel, NP $npModel)
    {
        $result = $orderModel->incomingCallCreateOrder($request->all());
        if ($result['status'] && $result['target_id'] && $result['geo']) {
            $data['orderTargetId'] = $result['target_id'];
            $result['html'] = view('incoming_line.target_final_ajax', $data)->render();
        }

        return response()->json($result);
    }

    public function incomingCallCreateOrderSearchProductAjax(Request $request, Order $orderModel, Product $productModel)
    {
        $orderId = $request->get('orderId');
        $ccc = $orderModel->getCountryCompanyCurrency($orderId);
        if ($ccc == NULL) {
            exit;
        }

        $data = $productModel->searchAllProduct($request->input('search'));

        $html = view('orders.ajax.order_one_search_ajax', [
            'data'     => $data,
            'currency' => $ccc->currency,
        ])->render();
        return response()->json(['html' => $html]);
    }

    /**
     * получаем цели
     */
    public function incomingCallCreateOrderGetTargetAjax(Request $request, Order $orderModel)
    {
        $order = $orderModel->getOneOrder($request->get('orderId'));
        if ($order) {
            $result['status'] = $orderModel->changeTarget($order->id, $request->input('targetId'));
            $data['orderTargetId'] = $request->input('targetId');
            $result['html'] = view('incoming_line.target_final_ajax', $data)->render();
            return response()->json($result);
        }
        abort(404);
    }

    /**
     * поиск по номер телефона на странице взодящей линии
     */
    public function incomingCallSearchOrdersByPhoneAjax(Request $request, Order $orderModel)
    {
        $phone = $request->get('phone');
        if (strlen($phone) < 6 || strlen($phone) > 25) {
            $phone = $request->get('incoming_phone');
        }
        $orders = $orderModel->getOrdersByPhone($phone);
        return response()->json([
            'html' => view('incoming_line.orders_by_phone_ajax', [
                'orders' => $orders,
                'phone'  => $phone
            ])->render()
        ]);
    }

    public function saveOrderDataAjax(
        Request $request,
        $id,
        Order $orderModel,
        PhoneCorrectionService $phoneCorrectionService,
        OrderProduct $ordersOffersModel,
        OrdersLog $ordersLogModel,
        Transaction $transactionModel,
        Comment $commentsModel,
        OrdersOpened $ordersOpenedModel
    ) {
        $errorMessage = [
            'approve.required_if'            => trans('validation.fill-details-confirm'),
            'refuse.required_if'             => trans('validation.fill-details-refuse'),
            'cancel.required_if'             => trans('validation.fill-details-cancel'),
            'proc_status.required'           => trans('validation.fill-result-call'), //Fill in the result of the call.
            'suspicious_comment.required_if' => trans('validation.fill-comment-suspicious-order'),
            'suspicious_comment.min'         => trans('validation.suspicious-must-be-more'),
            'callback_time.required_if'      => trans('validation.select-time'),
            'now.required_if'                => trans('validation.select-time'),
            'products.required'              => trans('validation.cant-confirm-order-products'),
            'products.*.price.min'           => trans('validation.price-must-be-more'),
            'products.*.price.numeric'       => trans('validation.price-must-be-numeric'),
            'products.*.price.required_if'   => trans('validation.price-required'),
            'target_approve.required_if'     => trans('validation.cant-confirm-order-target'),
            'target_refuse.required_if'      => trans('validation.cant-refuse-order-target'),
            'target_cancel.required_if'      => trans('validation.cant-cancel-order-target'),
        ];

        $phone = $request->get('phone');
        $order = Order::where('id', $id)->first();

        $ordersOpenedData = [];
        $procStatusId = 3;

        $operatorOrderOpened = OrdersOpened::where('order_id', $id)
            ->where('user_id', auth()->user()->id)
            ->whereNull('date_closed')
            ->orderBy('id', 'desc')->first();

        if (!$order) {
            abort(404);
        }

        if ($order->moderation_id) {
            return response()->json([
                'errors' => [
                    'moderation' => [trans('validation.cant-change-modera')] //You can not change moderated order.
                ]
            ])->withHeaders(['status' => 422]);
        }
        switch ($request->get('target_status')) {
            case 1 :
                {//approve
                    $newTarget['target_approve'] = $request->get('target_approve');
                    $target = TargetConfig::where('id', $newTarget['target_approve'])->first();
                    $oldTarget = TargetConfig::where('id', $order->target_approve)->first();
                    $targetType = 'approve';
                    break;
                }
            case 2 :
                {//refuse
                    $newTarget['target_refuse'] = $request->get('target_refuse');
                    $target = TargetConfig::where('id', $newTarget['target_refuse'])->first();
                    $oldTarget = TargetConfig::where('id', $order->target_refuse)->first();
                    $targetType = 'refuse';
                    break;
                }
            case 3 :
                {//cancel
                    $newTarget['target_cancel'] = $request->get('target_cancel');
                    $target = TargetConfig::where('id', $newTarget['target_cancel'])->first();
                    $oldTarget = TargetConfig::where('id', $order->target_cancel)->first();
                    $targetType = 'cancel';
                    break;
                }
            default :
                {
                    $target = '';
                    $targetType = '';
                    $newTarget = [];
                    $oldTarget = '';
                }
        }
        $targetFields = '';
        if ($target) {
            $targetFields = json_decode($target->options, true);
        }
        $proc_status = $order->proc_status;

        //Validation

        //Validate  suspicious order
        if ($request->get('suspicious')) {

            $this->validateSuspicious($request, $errorMessage);
        } elseif ($request->get('target_status')) {
            if ($request->get('target_status') == 1) {

                //validate Approve
                $this->validateApprove($request, $errorMessage, $targetFields, $targetType, $id);
            } else {

                //validate refusal, annulled
                $this->validateCancelOrRefuse($request, $errorMessage, $targetFields, $targetType);
            }

            list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($request->get('country'), $phone);
        } else {

            //validate callback and other
            $this->validateCallBack($request, $errorMessage, $order->proc_status);
            list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($request->get('country'), $request->get('phone'));
            if (!$phoneError && $order->proc_status == 6) {//если был не корректный номер
                $proc_status = 3;
                $procStatusId = 3;
            }
        }

        //delete order from pbx
        $orderModel->deleteCallsForElastix($order->id, $order->entity);

        //save client data
        $dataClient = [
            'name_first'   => $request->get('name'),
            'name_last'    => $request->get('surname'),
            'name_middle'  => $request->get('middle'),
            'phone'        => $phone,
            'geo'          => $request->get('country'),
            'age'          => $request->get('age'),
            'gender'       => $request->get('gender'),
            'proc_status'  => $proc_status,
            'time_changed' => now(),

            'division_id'  => $request->get('division_id') ? $request->get('division_id') : $order->division_id,
        ];

        if ($order->proc_status != $proc_status) {
            $dataClient['time_status_updated'] = Carbon::now();
            (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус был изменен c "Не корректный номер" на "Контакт"', [
                'status_id'   => 3,
                'status_name' => 'Контакт'
            ]);
        }

        $dataClient = array_merge($dataClient, $newTarget);

        try {
            $result['contactData'] = $orderModel->saveContactData($dataClient, $id);
            $this->addLogsForData($order, $dataClient);
            if ($oldTarget && $target) {
                if ($oldTarget->id != $target->id) {
                    $ordersLogModel->addOrderLog($order->id, 'Цель была изменена c "' . $oldTarget->name . '" на "' . $target->name . '"');
                }
            }
            $result['messages']['success'][] = trans('alerts.data-block') . ' "' . trans('general.client-data') . '" ' . trans('alerts.successfully-saved');
        } catch (\Exception $exception) {
            $result['contactData'] = false;
            $result['messages']['errors'][] = trans('alerts.data-block') . ' "' . trans('general.target') . '" ' . trans('alerts.data-not-added');
        }

        //save products
        if ($request->get('products')) {
            $result['products'] = false;
            if (Order::checkProducts($order) || $order->subproject_id == 0) {
                $price = $ordersOffersModel->saveProducts($request->get('products'), $id, $ordersLogModel);

                if ($price != $request->get('order-price') && $request->get('order-price') > 0) {
                    $price = $request->get('order-price');
                }

                $result['products'] = $orderModel->changeAllPriceAndDateChange($id, $price);

                if ($price != $order->price_total) {
                    $ordersLogModel->addOrderLog($id, "Стоимость товара была изменена " . $order->price_total . ' -> ' . $price);
                }

                try {
                    //распределяем product price, cost, cost_actual
                    OrderProduct::divideOrderCostsAndPrices($request->all(), $price, $order);
                } catch (\Exception $exception) {
                }
                $result['messages']['success'][] = trans('alerts.data-block') . ' "' . trans('general.product') . '" ' . trans('alerts.successfully-saved');
            } else {
                $result['storage'] = false;
                $result['messages']['errors'][] = trans('alerts.product-out-stock');
            }

            if (!$result['products']) {
                $result['messages']['errors'][] = trans('alerts.data-block') . ' "' . trans('general.product') . '" ' . trans('alerts.data-not-saved');
            }
        }

        //set order target
        if ($request->get('target_status')) {
            try {
                $result['target'] = $this->saveTarget($request->get('target_status'), $order, $targetFields, $request->get($targetType), $request->get('target_user'));

                $ordersOpenedModel->addFinal($id, ['target' => 0]); //сохранение date_closed

                if ($result['target']) {
                    $result['messages']['success'][] = transSaved('general.target');
                } else {
                    $result['messages']['errors'][] = transNotSaved('general.target');
                }
            } catch (\Exception $exception) {
                $result['target'] = false;
                $result['messages']['errors'][] = transNotSaved('general.target');
            }
        }

        if ($request->get('target_status') == 0) {
            try {
                if ($request->get('proc_status') == 5) {
                    $result['callback'] = $orderModel->addStatusCallAnotherLanguage($id);
                    $ordersLogModel->addOrderLog($id, 'Поставлен статус "Говорит на другом языке"',
                        ['status_id' => 7, 'status_name' => 'Говорит на другом языке']);
                    $ordersOpenedModel->addFinal($id, ['target' => 5]);//хз надо или нет
                    $procStatusId = 7;
                } else {
                    $result['callback'] = $orderModel->callBack($id, $request->get('callback_time'), $request->get('proc_status'), $request->get('now'));
                    $ordersOpenedModel->addFinal($id, ['callback_status' => $request->get('proc_status')]);
                    $log = '';
                    switch ($request->get('proc_status')) {
                        case 1 :
                            {
                                $log = 'Перезвонить "Автоответчик"';
                                break;
                            }
                        case 2 :
                            {
                                $log = 'Перезвонить ';
                                if ($request->get('now') == 1) {
                                    $log .= '"Сейчас"';
                                } else {
                                    $log .= '"Ближайшее время"';
                                }
                                break;
                            }
                        case 3 :
                            {
                                $log = "Просит перезвонить на " . $request->get('callback_time') . " (" . Carbon::now(Auth::user()->time_zone)->format('P') . '::' . Auth::user()->time_zone . ')';
                                break;
                            }
                    }
                    $procStatusId = 1;
                    if ($log) {
                        $ordersLogModel->addOrderLog($id, $log, [
                            'status_id'   => 1,
                            'status_name' => ProcStatus::find(1)->name
                        ]);
                    }
                }
                $result['callback'] = true;
                $result['messages']['success'][] = transSaved('general.call-back');
            } catch (\Exception $exception) {
                $result['callback'] = false;
                $result['messages']['errors'][] = transNotSaved('general.call-back');
            }
        }

        //mark order as suspicious
        if ($request->get('suspicious')) {
            $result['suspicious'] = $orderModel->setStatus(10, $id); //10 -  подозрительный
            $commentsModel->addComment($id, $request->get('suspicious_comment'), $order->entity, 'suspicious');
            $ordersLogModel->addOrderLog($id, 'Заказ отмечен как подозрительный', [
                'status_id'   => 10,
                'status_name' => ProcStatus::find(10)->name
            ]);
            $procStatusId = 10;
            $result['messages']['success'][] = transSaved('general.suspicious-order');
        }

        $transactionModel->setInActiveTransaction($id);
        if (auth()->user()->role_id == 1 && !$request->get('suspicious') && $request->get('target_status') == 1) {//добавление транзакции
            $transactionModel = new Transaction();
            $transactionModel->createOrUpdateTransaction($id, 'approve');
        }

        $orderModel->getProcessingStatusOrderApi($id);

        //update orders opened data
        (new OrdersOpened)->updateAllOrderData($request, $ordersOpenedData, $procStatusId, $operatorOrderOpened, $targetType);

        return response()->json($result);
    }

    public function saveOrderSendingDataAjax(
        Request $request,
        $id,
        Order $orderModel,
        OrderProduct $ordersOffersModel,
        OrdersLog $ordersLogModel
    ) {
        $errorMessage = [
            'approve.required_if'            => trans('validation.fill-details-confirm'),
            'refuse.required_if'             => trans('validation.fill-details-refuse'),
            'cancel.required_if'             => trans('validation.fill-details-cancel'),
            'suspicious_comment.required_if' => trans('validation.fill-comment-suspicious-order'),
            'suspicious_comment.min'         => trans('validation.suspicious-must-be-more'),
            'callback_time.required_if'      => trans('validation.select-time'),
            'now.required_if'                => trans('validation.select-time'),
            'products.*.price.min'           => trans('validation.price-must-be-more'),
            'products.*.price.numeric'       => trans('validation.price-must-be-numeric'),
            'products.*.price.required_if'   => trans('validation.price-required'),
            'products.required_if'           => trans('validation.cant-confirm-order-products'),
        ];

        $phone = $request->get('phone');
        $order = Order::findOrFail($id);

        $newTarget['target_approve'] = $request->get('target_approve');
        $target = TargetConfig::where('id', $newTarget['target_approve'])->first();
        $oldTarget = TargetConfig::where('id', $order->target_approve)->first();
        $targetType = 'approve';

        $targetFields = '';
        if ($target) {
            $targetFields = json_decode($target->options, true);
        }

        //валидация для подтверждения
        $this->validateApprove($request, $errorMessage, $targetFields, $targetType, $id);

        //сохраняем данные клиента
        $dataClient = [
            'name_first'   => $request->get('name'),
            'name_last'    => $request->get('surname'),
            'name_middle'  => $request->get('middle'),
            'phone'        => $phone,
            'geo'          => $request->get('country'),
            'age'          => $request->get('age'),
            'gender'       => $request->get('gender'),
            //            'proc_status'   => intval($proc_status),
            //            'proc_status_2' => $proc_status2,
            'time_changed' => now(),

            'division_id'  => $request->get('division_id') ? $request->get('division_id') : $order->division_id,
        ];

        $dataClient = array_merge($dataClient, $newTarget);
        try {
            $result['contactData'] = $orderModel->saveContactData($dataClient, $id);

            $this->addLogsForData($order, $dataClient);
            if ($oldTarget && $target) {
                if ($oldTarget->id != $target->id) {
                    $ordersLogModel->addOrderLog($order->id, 'Цель была изменена c "' . $oldTarget->name . '" на "' . $target->name . '"');
                }
            }
            $result['messages']['success'][] = transSaved('general.target');
        } catch (\Exception $exception) {
            $result['contactData'] = false;
            $result['messages']['errors'][] = transNotSaved('general.target');
        }
        $oldStatus = $order->procStatus;
        $oldStatus2 = !empty($order->procStatus2) ? $order->procStatus2 : NULL;

        try {
            $statuses['procStatus'] = isset($request->proc_status) ? $request->proc_status : $order->proc_status;
            $statuses['procStatus2'] = isset($request->proc_status2) ? $request->proc_status2 : $order->proc_status_2;

            if ($request->proc_status != $oldStatus->id) {
                //обнуляем print_id для возможности повторного добавления на печать
                if (isset($oldStatus->action) && $oldStatus->action == 'to_print') {
                    Order::where('id', $order->id)->update(['print_id' => 0]);
                }
                $saveProcStatusResult = $orderModel->saveProcStatuses($order, $statuses, $request);
            }

            if (isset($request->proc_status2) && $request->proc_status2 != $oldStatus2->id) {
                if (empty($oldStatus2)) {
                    $ordersLogModel->addOrderLog($order->id, 'Процессинг статус 2 был изменен c "' . $oldStatus2->name . '" на "' . ProcStatus::where('id', $request->proc_status2)
                            ->first()->name . '"');
                } else {
                    $ordersLogModel->addOrderLog($order->id, 'Установлен Процессинг статус 2 - "' . ProcStatus::where('id', $request->proc_status2)
                            ->first()->name . '"');
                }
            }
            $result['proсStatuses'] = true;

            if (isset($saveProcStatusResult) && !($saveProcStatusResult instanceof JsonResponse) && isset($saveProcStatusResult['exist_in_pass'])) {
                $result['proсStatuses'] = false;
                $result['existInPass'] = false;
                $result['messages']['errors'][] = transNotSaved('general.processing-status');
                $result['messages']['errors'][] = trans('alerts.order-already-pass-queue');
            }

            if ($result['proсStatuses']) {
                $result['messages']['success'][] = transSaved('general.processing-status');
            }
        } catch (\Exception $exception) {
            $result['proсStatuses'] = false;
            $result['messages']['errors'][] = transNotSaved('general.processing-status');
        }

        //сохранение товаров
        if ($request->get('products')) {
            $price = $ordersOffersModel->saveSendingProducts($request->get('products'), $id, $ordersLogModel);

            if ($price != $request->get('order-price') && $request->get('order-price') > 0) {
                $price = $request->get('order-price');
            }

            try {
                //распределяем product price, cost, cost_actual
                OrderProduct::divideOrderCostsAndPrices($request->all(), $price, $order);
            } catch (\Exception $exception) {
            }


            $result['products'] = $orderModel->changeAllPriceAndDateChange($id, $price);

            $ordersLogModel->addOrderLog($id, "Стоимость заказа была изменена " . $order->price_total . ' -> ' . $price);

            if ($result['products']) {
                $result['messages']['success'][] = transSaved('general.product');
            } else {
                $result['messages']['errors'][] = transNotSaved('general.product');
            }
        }

        //ставим цель
        try {
            $result['target'] = $this->saveTargetSending(1, $order, $targetFields, $request->get($targetType), $request->get('target_user'), $request);

            if ($result['target']) {
                $result['messages']['success'][] = transSaved('general.target');
            } else {
                $result['messages']['errors'][] = transNotSaved('general.target');
            }
        } catch (\Exception $exception) {
            $result['target'] = false;
            $result['messages']['errors'][] = tranNotsSaved('general.target');
        }

        $orderModel->getProcessingStatusOrderApi($id);

        return response()->json($result);
    }


    /**
     * @param $request Request object
     * @param $errorMessage array
     * @param $targetFields json
     * @param $targetType string
     */
    public function validateApprove($request, $errorMessage, $targetFields, $targetType, $orderId)
    {

        $inputLast = $request->input('surname');
        \Validator::extend('banned_words', function ($attr, $value) {
            // Banned words
            $words = 'Заказ';
            if (strpos($words, $value) !== false) {
                return false;
            }
            return true;
        }, trans('validation.attribute-field-invalid') . ' "' . $inputLast . '" ' . trans('alert.is-banned.'));
        \Validator::extend('check_phone', function ($attr, $value) use ($request) {
            $phoneCorrectionService = new PhoneCorrectionService();
            list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($request->get('country'), $value);
            if ($phoneError) {
                return false;
            }
            return true;
        }, trans('validation.attribute-field-invalid'));
        \Validator::extend('check_price', function ($attr, $value) use ($orderId) {
            $price = Order::find($orderId)->price_total;
            if (!$price) {
                return false;
            }
            return true;
        }, trans('validation.cant-confirm-order-price'));//
        \Validator::extend('check_product', function ($attr, $value) use ($orderId) {
            return OrderProduct::where([
                ['order_id', $orderId],
                ['disabled', 0]
            ])->exists();

        }, trans('validation.cant-confirm-order-products')); //You can not confirm an order without products

        $rules = [
            'surname'             => 'required|max:255|min:2|banned_words',
            'name'                => 'required|max:255|min:2',
            'middle'              => 'max:255|min:1',
            'phone'               => 'required|numeric|check_phone',
            'country'             => 'required|max:5',
            'age'                 => 'numeric',
            'gender'              => 'numeric',
            'target_status'       => 'required|numeric|check_price',
            'target_approve'      => 'required_if:target_status,1|numeric',
            'target_refuse'       => 'required_if:target_status,2|numeric',
            'target_cancel'       => 'required_if:target_status,3|numeric',
            'target_user'         => 'numeric|min:1',
            'suspicious'          => 'min:1|max:5',
            'suspicious_comment'  => 'required_if:suspicious,on|min:2|max:255',
            'products'            => 'required|array|check_product',
            'products.*.id'       => 'required|numeric',
            'products.*.disabled' => 'required|boolean',
            'products.*.price'    => 'required_if:products.*.disabled,0|min:1|numeric',
            'products.*.up1'      => 'min:1|numeric',
            'products.*.up2'      => 'min:2|numeric',
            'products.*.cross'    => 'min:4|numeric',
            'approve'             => 'required_if:target_status,1|array|min:1',
            'approve.*'           => 'min:1|max:255',
            'refuse'              => 'required_if:target_status,2|array|min:1',
            'refuse.*'            => 'min:1|max:255',
            'cancel'              => 'required_if:target_status,3|array|min:1',
            'cancel.*'            => 'min:1|max:255',
            'order-price'         => 'nullable|min:1|numeric',
        ];

        //временный костыль-проверка для Новой почты
        $cOrder = Order::find($orderId);
        if (isset($cOrder->getTargetValue->target_id) && isset($request->sender) && $cOrder->getTargetValue->target_id == 1 && $cOrder->getTargetValue->sender_id != $request->sender && !empty($cOrder->getTargetValue->track)) {
            $rules['sender'] = 'check_sender_track';
            \Validator::extend('check_sender_track', function ($attr, $value) {
                return false;
            }, 'Для смены отправителя сначала удалите Track!');
        }

        if (isset($request->target_approve) && ($request->target_approve == 2 || $request->target_approve == 4)) {
            $rules['approve.cost'] = 'numeric';
        }

        //добавление правила для обязательных полей
        if ($targetType && $targetFields) {
            foreach ($targetFields as $field) {
                if ($field['field_required']) {
                    $rules[$targetType . '.' . $field['field_name']] = 'required';
                }
            }
        }
        /////////// Validate CDEK  /////
//        if ($request->target_approve == 21) {
//            if ($request->approve['delivery_mode'] == 137) {
//                $rules['approve.house'] = "required|string|min:1|max:20";
//                $rules['approve.street'] = "required|string|min:1|max:50";
//            } else {
//                $rules['approve.warehouse'] = "required|string|min:1|max:50";
//            }
//        }

        $this->validate($request, $rules, $errorMessage);
    }

    /**
     * @param $request Request object
     * @param $errorMessage array
     * @param $targetFields json
     * @param $targetType string
     */
    public function validateCancelOrRefuse($request, $errorMessage, $targetFields, $targetType)
    {
        $rules = [
            'surname'             => 'nullable|max:255|min:2',
            'name'                => 'nullable|max:255|min:2',
            'middle'              => 'nullable|min:1',
            'phone'               => 'numeric',
            'country'             => 'max:5',
            'age'                 => 'nullable|numeric',
            'gender'              => 'nullable|numeric',
            'target_status'       => 'required|numeric',
            'target_refuse'       => 'required_if:target_status,2|numeric',
            'target_cancel'       => 'required_if:target_status,3|numeric',
            'target_user'         => 'nullable|numeric|min:1',
            'target_id'           => 'numeric',
            'suspicious'          => 'nullable|min:1|max:5',
            'suspicious_comment'  => 'nullable|min:2|max:255',
            'products'            => 'nullable|array',
            'products.*.id'       => 'required|numeric',
            'products.*.disabled' => 'required|boolean',
            'products.*.price'    => 'nullable|numeric',
            'products.*.up1'      => 'nullable|min:1|numeric',
            'products.*.up2'      => 'nullable|min:2|numeric',
            'products.*.cross'    => 'nullable|min:4|numeric',
            'approve'             => 'nullable|array',
            'approve.*'           => 'nullable|min:1|max:255',
            'refuse'              => 'required_if:target_status,2|array|min:1',
            'refuse.*'            => 'nullable|min:1|max:255',
            'cancel'              => 'required_if:target_status,3|array|min:1',
            'cancel.*'            => 'nullable|min:1|max:255',
            'order-price'         => 'nullable|min:1|numeric',
        ];
        //добавление правила для обязательных полей
        if ($targetType && $targetFields) {
            foreach ($targetFields as $field) {
                if ($field['field_required']) {
                    $rules[$targetType . '.' . $field['field_name']] = 'required';
                }
            }
        }

        $this->validate($request, $rules, $errorMessage);
    }

    /**
     * @param $request Request object
     * @param $errorMessage array
     */
    public function validateSuspicious($request, $errorMessage)
    {
        $this->validate($request, [
            'surname'             => 'nullable|max:255|min:1',
            'name'                => 'nullable|max:255|min:1',
            'middle'              => 'nullable|max:255|min:1',
            'phone'               => 'required|numeric',
            'country'             => 'required|max:5',
            'age'                 => 'nullable|numeric',
            'gender'              => 'nullable|numeric',
            'target_status'       => 'nullable|numeric',
            'target_approve'      => 'nullable|numeric',
            'target_refuse'       => 'nullable|numeric',
            'target_cancel'       => 'nullable|numeric',
            'suspicious'          => 'required|min:1|max:5',
            'suspicious_comment'  => 'required_if:suspicious,on|min:2|max:255',
            'products'            => 'nullable|array',
            'products.*.id'       => 'required|numeric',
            'products.*.disabled' => 'required|boolean',
            'products.*.price'    => 'nullable|numeric|min:1',
            'products.*.up1'      => 'nullable|min:1|numeric',
            'products.*.up2'      => 'nullable|min:2|numeric',
            'products.*.cross'    => 'nullable|min:4|numeric',
            'approve'             => 'nullable|array',
            'approve.*'           => 'nullable|min:1|max:255',
            'refuse'              => 'nullable|array',
            'refuse.*'            => 'nullable|min:1|max:255',
            'cancel'              => 'nullable|array',
            'cancel.*'            => 'nullable|min:1|max:255',
            'order-price'         => 'nullable|min:1|numeric'
        ], $errorMessage);
    }

    /**
     * @param $request Request object
     * @param $errorMessage array
     * @param $proc_status integer
     */
    protected function validateCallBack($request, $errorMessage, $proc_status)
    {
        \Validator::extend('callback_time', function ($attr, $value) {
            // Banned words
            if (strtotime($value) < time()) {
                return false;
            }
            return true;
        }, trans('validation.time-must-be-more-current'));
        // Callback time must be more current time
        \Validator::extend('noCorrectNumber', function ($attr, $value) use ($proc_status, $request) {
            $phoneCorrectionService = new PhoneCorrectionService();
            list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($request->get('country'), $request->get('phone'));
            return !$phoneError;
        }, trans('validation.phone-invalid'));
        $this->validate($request, [
            'surname'             => 'nullable|max:255|min:1',
            'name'                => 'nullable|max:255|min:1',
            'middle'              => 'nullable|max:255|min:1',
            'phone'               => 'required|numeric|noCorrectNumber',
            'country'             => 'required|max:5',
            'age'                 => 'nullable|numeric',
            'gender'              => 'nullable|numeric',
            'target_status'       => 'nullable|numeric',
            'target_approve'      => 'nullable|numeric',
            'target_refuse'       => 'nullable|numeric',
            'target_cancel'       => 'nullable|numeric',
            'suspicious'          => 'nullable|min:1|max:5',
            'suspicious_comment'  => 'required_if:suspicious,on|min:2|max:255',
            'products'            => 'nullable|array',
            'products.*.id'       => 'required|numeric',
            'products.*.disabled' => 'required|boolean',
            'products.*.price'    => 'nullable|numeric|min:1',
            'products.*.up1'      => 'nullable|min:1|numeric',
            'products.*.up2'      => 'nullable|min:2|numeric',
            'products.*.cross'    => 'nullable|min:4|numeric',
            'proc_status'         => 'required|numeric',
            'callback_time'       => 'required_if:proc_status,3|max:20|callback_time',
            'now'                 => 'required_if:proc_status,2|numeric',
            'order-price'         => 'nullable|min:1|numeric'
        ], $errorMessage);

    }

    /**
     * @param $target integer 1,2,3
     * @param $order Order object
     * @param $targetConfig array
     * @param $targetData array
     * @return bool
     */
    public function saveTarget($target, $order, $targetConfig, $targetData, $targetUser)
    {
        $orderModel = new Order;
        $ordersLogModel = new OrdersLog;
        $targetValueModel = new TargetValue;
        $orderId = $order->id;
        $targetValue = [];
        if ($targetConfig) {
            foreach ($targetConfig as $targetField) {
                if (isset($targetData[$targetField['field_name']])) {
                    $targetField['field_value'] = $targetData[$targetField['field_name']];
                }
                $targetValue[$targetField['field_name']] = $targetField;
            }
        }

        if ($order->proc_status == 1 || $order->proc_status == 2) {
            if (!$orderModel->deleteCallsForElastix($orderId, $order->entity)) {
                if (!$orderModel->changeStatusInProcessing($orderId)) {
                }
            }
        }

        $learningStatus = DB::table('company_elastix')->where('id', $order->proc_campaign)->value('learning');

        $orderModel->changeStatus($orderId, $target, $targetUser, $learningStatus);
//saving track if filled up
        $targetValueRow = TargetValue::where('order_id', $orderId)->first();

        if (isset($targetValue['track']) && $targetValueRow && !$targetValueRow->track) {
            $track = $targetValue['track']['field_value'];
        } else {
            $track = !empty($targetValueRow) ? $targetValueRow->track : 0;
        }

        $cost = !empty($targetValue['cost']) ? $targetValue['cost']['field_value'] : 0;
        $data = [
            'order_id'     => $order->id,
            'track'        => $track,
            'values'       => json_encode($targetValue),
            'time_created' => now(),
            'cost'         => $cost
        ];

        //todo костыль
        Order::where('id', $order->id)->update(['reset_call' => 0]);

        $newOrder = Order::find($order->id);
        if ($target == 1) {
            $data['target_id'] = $newOrder->target_approve;
        } elseif ($target == 2) {
            $data['target_id'] = $newOrder->target_refuse;
        } elseif ($target == 3) {
            $data['target_id'] = $newOrder->target_cancel;
        }

        try {
            $targetValueModel->deleteValue($order->id);
            $result = $targetValueModel->addData($data);
        } catch (\Exception $exception) {
            return $result = false;
        }

        if ($result) {
            if ($target == 1) {
                $ordersLogModel->addOrderLog($orderId, 'Цель - Подтвержден');
            } elseif ($target == 2) {
                $ordersLogModel->addOrderLog($orderId, 'Цель - Отказ');
            } elseif ($target == 3) {
                $ordersLogModel->addOrderLog($orderId, 'Цель - Аннулирован');
            }
        }

        return $result;
    }

    /**
     * @param $target integer 1,2,3
     * @param $order Order object
     * @param $targetConfig array
     * @param $targetData array
     * @return bool
     */
    public function saveTargetSending($target, $order, $targetConfig, $targetData, $targetUser, $request = [])
    {
        $targetValueModel = new TargetValue;
        $targetValue = [];
        if ($targetConfig) {
            foreach ($targetConfig as $targetField) {
                if (isset($targetData[$targetField['field_name']])) {
                    $targetField['field_value'] = $targetData[$targetField['field_name']];
                }
                $targetValue[$targetField['field_name']] = $targetField;
            }
        }

        $targetValueRow = TargetValue::where('order_id', $order->id)->first();

        if (isset($targetValue['track']) && $targetValueRow && !$targetValueRow->track) {
            $track = $targetValue['track']['field_value'];
        } else {
            $track = !empty($targetValueRow) ? $targetValueRow->track : 0;
        }

        $track2 = !empty($targetValueRow) ? $targetValueRow->track2 : 0;
        $sender = !empty($request->sender) ? $request->sender : $targetValueRow->sender_id;
        $cost = !empty($targetValue['cost']) ? $targetValue['cost']['field_value'] : 0;
        $costActual = !empty($targetValue['cost_actual']) ? $targetValue['cost_actual']['field_value'] : 0;

        $data = [
            'order_id'     => $order->id,
            'values'       => json_encode($targetValue),
            'track'        => $track,
            'track2'       => $track2,
            'sender_id'    => $sender,
            'time_created' => time(),
            'cost'         => $cost,
            'cost_actual'  => $costActual,
        ];


        $newOrder = Order::find($order->id);
        $data['target_id'] = $newOrder->target_approve;

        try {
            $targetValueModel->deleteValue($order->id);
            $result = $targetValueModel->addData($data);

            (new OrdersLog)->addOrderLog($order->id, 'Цель обновлена');
        } catch (\Exception $exception) {
            return $result = false;
        }
        return $result;
    }

    public function addLogsForData($oldData, $newData)
    {

        $orderLogModel = new OrdersLog();
        $log = '';

        if (($oldData->name_last || $newData['name_last']) && $oldData->name_last != $newData['name_last']) {
            $log .= 'Фамилия';
            if (!$oldData->name_last) {
                $log .= ' изменена на ' . $newData['name_last'] . "<br>";
            } else if (!$newData['name_last']) {
                $log .= " была удалена<br>";
            } else {
                $log .= ' изменена c ' . $oldData->name_last . ' на ' . $newData['name_last'] . "<br>";
            }
        }
        if (($oldData->name_first || $newData['name_first']) && $oldData->name_first != $newData['name_first']) {
            $log .= 'Имя';
            if (!$oldData->name_first) {
                $log .= ' изменено на ' . $newData['name_first'] . "<br>";
            } else if (!$newData['name_first']) {
                $log .= " было удалено<br>";
            } else {
                $log .= ' изменено c ' . $oldData->name_first . ' на ' . $newData['name_first'] . "<br>";
            }
        }
        if (($oldData->name_middle || $newData['name_middle']) && $oldData->name_middle != $newData['name_middle']) {
            $log .= 'Отчество';
            if (!$oldData->name_middle) {
                $log .= ' изменено на ' . $newData['name_middle'] . "<br>";
            } else if (!$newData['name_middle']) {
                $log .= " было удалено<br>";
            } else {
                $log .= ' изменено c ' . $oldData->name_middle . ' на ' . $newData['name_middle'] . "<br>";
            }
        }
        if (($oldData->phone || $newData['phone']) && $oldData->phone != $newData['phone']) {
            $log .= 'Телефон';
            if (!$oldData->phone) {
                $log .= ' изменен на ' . $newData['phone'] . "<br>";
            } else if (!$newData['phone']) {
                $log .= " был удален<br>";
            } else {
                $log .= ' изменен c ' . $oldData->phone . ' на ' . $newData['phone'] . "<br>";
            }
        }

        if (($oldData->geo || $newData['geo']) && $oldData->geo != $newData['geo']) {
            $log .= 'Страна';
            if (!$oldData->geo) {
                $log .= ' изменена на ' . $newData['geo'] . "<br>";
            } else if (!$newData['geo']) {
                $log .= " была удалена<br>";
            } else {
                $log .= ' изменена c ' . $oldData->geo . ' на ' . $newData['geo'] . "<br>";
            }
        }

        if ($log) {
            $orderLogModel->addOrderLog($oldData->id, $log);
        }
    }

    public function changeCampaign(Request $request, $id, Order $orderModel, OrdersLog $ordersLogModel)
    {
        $campaign = $request->get('campaign');
        $result['success'] = $orderModel->changeCampaign($id, $campaign);
        $result['message'] = trans('alerts.data-not-changed');
        if ($result['success']) {
            $result['message'] = transChanged('general.queue');
            $ordersLogModel->addOrderLog($id, 'Очередь изменена', ['status_id' => 1, 'status_name' => 'В обработке']);
        }
        return response()->json($result);
    }

    public function countOrdersOnModerationAjax(Request $request, Order $orderModel)
    {
        $filter = [
            'page'       => $request->get('page'),
            'grouping'   => $request->get('grouping'),
            'country'    => $request->get('country'),
            'project'    => $request->get('project'),
            'offer'      => $request->get('offer'),
            'company'    => $request->get('company'),
            'id'         => $request->get('id'),
            'date_start' => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end'   => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
        ];
        return response()->json($orderModel->getCountOrderModeration($filter));
    }

    /**
     * @param Request $request
     * @param $id из таблици order_products
     * @param OrderProduct $ordersOffersModel
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCommentForProductAjax(Request $request, OrderProduct $ordersOffersModel)
    {
        $id = $request->get('pk');
        $comment = $request->get('value');

        $result = [
            'success' => false,
            'message' => trans('alerts.data-not-added')
        ];

        if ($ordersOffersModel->addComment($id, $comment)) {
            $result['success'] = true;
            $result['message'] = trans('alerts.data-not-added');
        }
        return response()->json($result);
    }

    public function orderChangeTargetInOrderAjax(Request $request)
    {
        $this->validate($request, [
            'targetId' => 'required|numeric|min:1',
        ]);

        try {
            $target = TargetConfig::where('id', $request->get('targetId'))->firstOrFail();
            $result['success'] = true;
            $result['message'] = transChanged('general.target');
            $result['html'] = view('targets.target-ajax', ['target' => $target])->render();
            if ($request->sending) {
                $data['orderOne'] = Order::find($request->orderId);
                if ($target->alias == 'novaposhta' || $target->alias == 'wefast' || $target->alias == 'viettel') {
                    $data['offers'] = (new OrderProduct())->getProductsByOrderId($request->orderId, $data['orderOne']->subproject_id ?? 0);
                }
                $data['target_option']['approve'] = $target;
                $params = $this->getParamsForOtherFields($target, $data);
                $result['html2'] = integrationOtherFields($target->alias, $params);
            }
        } catch (\Exception $exception) {
            $result['success'] = false;
            $result['message'] = trans('alerts.server-error') . '. Message : ' . $exception->getMessage();
        }
        return response()->json($result);
    }

    /*find order by id*/
    public function findById(Request $request, Order $orderModel)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $orders = $orderModel->searchOrderById($term);
        $formatted_orders = [];

        foreach ($orders as $order) {
            $formatted_orders[] = ['id' => $order->id, 'text' => $order->id];
        }

        return \Response::json($formatted_orders);

    }

    public function changeOperatorsOptions(Request $request)
    {
        if (!empty($request->campaignId)) {
            $html = view('ajax.order.change_operators_options', [
                'operators' => User::where('campaign_id', $request->input('campaign'))->get(),
            ])->render();
            return response()->json(['html' => $html]);
        }
    }

    public function create($id = null)
    {
        $data['order'] = null;

        if ($id) {
            $data['order'] = Order::with([
                'products:' . OrderProduct::tableName() . '.price,' . Product::tableName() . '.id,title',
                'offer',
                'subProject',
                'project',
                'getTargetApprove',
                'getTargetValue'
            ])->findOrFail($id);
        }

        $data['countries'] = Country::all();
        $data['partners'] = Partner::all();
        return view('orders.create', $data);
    }

    public function createAjax(Request $request, OrdersLog $ordersLogModel)
    {
//        \Validator::extend('checkProject', function ( $attribute, $value, $parameters, $validator ) {
//            try {
//                $partnerId = $validator->getData()['partner_id'];
////                return Project::where([
////                    ['id', $value],
////                    ['partner_id', $partnerId]
////                ])->exists();
//            } catch (\Exception $exception) {
//                return false;
//            }
//        }, 'Проект не принадлежит партнеру');

        \Validator::extend('checkSubProject', function ($attribute, $value, $parameters, $validator) {
            try {
                //   $partnerId = $validator->getData()['partner_id'];
                $projectId = $validator->getData()['project_id'];
                return Project::where([
                    ['id', $value],
                    //                    ['partner_id', $partnerId],
                    ['parent_id', $projectId]
                ])->exists();
            } catch (\Exception $exception) {
                return false;
            }
        }, trans('validation.subproject-not-belong-project')); //Sub project does not belong to the project.

//        \Validator::extend('checkOffer', function ($attribute, $value, $parameters, $validator) use ($request) {
//
//            return Offer::where([
//                ['partner_id', $request->partner_id],
//                ['id', $value]
//            ])->exists();
//
//        }, trans('validation.offer-not-belong-partner')); //

        \Validator::extend('checkPhone', function ($attr, $value) use ($request) {
            $phoneCorrectionService = new PhoneCorrectionService();
            list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($request->get('country'), $value);

            if ($phoneError) {
                return false;
            }
            return true;
        }, trans('validation.attribute-field-invalid'));

        $rules = [
            'name_first'               => 'required|string|min:2|max:255',
            'name_last'                => 'required|string|min:2|max:255',
            'name_middle'              => 'string|min:2|max:255',
            'country'                  => 'required|string|exists:countries,code',
            'phone'                    => 'required|string|min:8|checkPhone',
            'age'                      => 'string|max:255',
            'gender'                   => 'string|max:255',
            'comment'                  => 'string|max:255',
            //  'partner_id'               => 'required|int|exists:partners,id',
            'project_id'               => 'required|int',
            'sub_project_id'           => 'required|int|checkSubProject',
           // 'offer_id'                 => 'int|checkOffer',
            'products'                 => 'required|array|min:1',
            'products.*.product_id'    => 'required|int|min:1',
            'products.*.product_price' => 'required|int|min:1',
            'target_id'                => 'required|int|exists:target_configs,id',
        ];

        $target = TargetConfig::find($request->target_id);

        if ($target) {
            $targetFields = json_decode($target->options, true);
            if ($targetFields) {
                foreach ($targetFields as $field) {
                    if ($field['field_required']) {
                        $rules['approve.' . $field['field_name']] = 'required|string';
                    } else {
                        $rules['approve.' . $field['field_name']] = 'string';
                    }
                }
            }
        }

        $this->validate($request, $rules, [
            'products.required'                 => trans('validation.cant-confirm-order-products'),
            'products.*.product_price.required' => trans('validation.price-required'),
            'products.*.product_price.integer'  => trans('validation.price-must-be-numeric'),
            'products.*.product_price.min'      => trans('validation.price-must-be-more'),
        ]);

        $phoneCorrectionService = new PhoneCorrectionService();
        list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($request->country, $request->phone);

        $order = new Order();
        $order->entity = Order::ENTITY_ORDER;
        $order->service = Order::SERVICE_SENDING;
        $order->name_first = $request->name_first;
        $order->name_last = $request->name_last;
        $order->name_middle = $request->name_middle;
        $order->geo = $request->country;
        $order->phone = $phone;
        $order->age = $request->age;
        $order->gender = $request->gender;
        $order->comments = $request->comment;
       // $order->partner_id = $request->partner_id;
        $order->project_id = $request->project_id;
        $order->subproject_id = $request->sub_project_id;
     //   $order->offer_id = $request->offer_id;
        $order->proc_status = 3;
        $order->handmade = 1;

        $order->target_approve = $request->target_id;
        $order->target_status = 1;
        $order->target_user = Auth::user()->id;
        $order->time_created = now();
        $order->time_changed = now();
        $order->time_modified = now();

        $order->moderation_id = Auth::user()->id;
        $order->moderation_time = now();

        $order->save();

        $price = OrderProduct::addProductsByOrder($order->id, $request->products);

        $order->price_total = $price;

        $order->save();

        if ($order->id) {
            $text = 'Заказ был создан';
            if ($request->parent_order_id) {
                $text = "Заказ был склонирован с <a href=" . route('order-sending', $request->parent_order_id) . ">" . $request->parent_order_id . "</a>" . " Процессинг статус - Контакт";
            }

            $ordersLogModel->addOrderLog($order->id, $text, ['status_id' => 3, 'status_name' => 'Контакт']);
        }

        try {
            //save target
            $result['target'] = TargetValue::setTargetValues($target, $request->approve, $order->id);
        } catch (\Exception $exception) {
            $result['target'] = false;
        }

        $result['order'] = $order->id;
        $result['alert'] = trans('alerts.order-created-successfully');

        $result['message'] = view('orders.ajax.order_create_message', [
            'order' => $order,
        ])->render();

        return response()->json($result);
    }

    public function printRegister(Request $request)
    {
        if (!empty($request->ordersNumbers)) {
            $tracksNumbers = [];
            $orders = Order::whereIn('id', explode(',', $request->ordersNumbers))
                ->with('products:title', 'getTargetValue')
                ->get();

            foreach ($orders as $order) {
                $order->currency = Country::where('code', strtoupper($order->geo))->first();
                $tracksNumbers[] = $order->getTargetValue->track;
            }
            $tracks = implode(',', $tracksNumbers);
        } else {
            abort(404);
        }
        $procStatuses = ProcStatus::where([['type', ProcStatus::TYPE_SENDERS], ['project_id', $orders[0]->project_id]])
            ->get()->keyBy('id');
        if (!$procStatuses->count()) {
            $procStatuses = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)->get()->keyBy('id');
        }

        return view('orders.print-register', [
            'orders'       => $orders,
            'ordersIds'    => $orders->pluck('id'),
            'tracks'       => $tracks,
            'targetConfig' => TargetConfig::find($orders[0]->target_approve),
            'procStatuses' => $procStatuses
        ]);
    }

    public function updateSendingStatuses(Request $request)
    {
        $order = Order::findOrFail($request->orderId);
        if (isset($request->action)) {
            $procStatuses = ProcStatus::where([['project_id', $request->project_id], ['action', 'to_print']])
                ->first(['id']);
            $status = !empty($procStatuses) ? $procStatuses :
                ProcStatus::where('action', 'to_print')->first(['id']);

            $order->proc_status = $status->id;
            if ($order->save()) {
                return response()->json([
                    'success' => true,
                    'message' => trans('alerts.data-block') . ' "' . trans('general.processing-status') . '" ' . trans('alerts.successfully-saved')
                ]);
            }
        }

        $procStatus = !empty($request->procStatus) ? $request->procStatus : $order->proc_status;
        $procStatus2 = $request->procStatus2;
        $order->proc_status = $procStatus;
        $order->proc_status_2 = $procStatus2;
        if ($order->save()) {
            return response()->json([
                'success' => true,
                'message' => trans('alerts.data-block') . ' "' .
                    trans('general.processing-status') . ' 2" ' .
                    trans('alerts.successfully-saved')
            ]);
        }
    }

    public function pass(Request $request)
    {
//        approx => доставка(озвучил оператор), refund-> при не выкупе, actual->сколько денег заплатил отправитель за доставку
        $filter = [
            'pass_id'     => $request->input('pass_id'),
            'initiator'   => $request->input('initiator'),
            'project'     => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'date_start'  => $request->input('date_start') ? $request->input('date_start') : 0,
            'date_end'    => $request->input('date_end') ? $request->input('date_end') : 0,
        ];
        $dataFilters = FilterRepository::processFilterData($filter);
        if ($request->isMethod('post')) {
            header('Location: ' . route('pass') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $data['passes'] = Pass::getPassByFilter($filter);
        $data['statistics'] = /*OrdersPass::getFinanceSubProject($filter)*/
            collect();
        $data['projects'] = Project::checkAuth()->get()->keyBy('id');

        return view('orders.pass.pass', $data, $dataFilters);
    }

    public function passRedemption()
    {
        $pass = Pass::with(
            'ordersPass',
            'orders',
            'orders.procStatus',
            'orders.country',
            'orders.subProject',
            'orders.getTargetValue'
        )
            ->active()
            ->redemption()
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$pass) {
            $pass = new Pass();
            $pass->active = 1;
            $pass->type = Pass::TYPE_REDEMPTION;
            $pass->user_id = Auth::user()->id;
            $pass->sub_project_id = Auth::user()->sub_project_id ?? 0;
            $pass->save();
        }

        $data['pass'] = $pass;

        return view('orders.pass.redemption', $data);
    }

    public function passNoRedemption()
    {
        $pass = Pass::with(
            'ordersPass',
            'orders',
            'orders.procStatus',
            'orders.country',
            'orders.subProject',
            'orders.getTargetValue'
        )
            ->active()
            ->noRedemption()
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$pass) {
            $pass = new Pass();
            $pass->active = 1;
            $pass->type = Pass::TYPE_NO_REDEMPTION;
            $pass->user_id = Auth::user()->id;
            $pass->sub_project_id = Auth::user()->sub_project_id ?? 0;
            $pass->save();
        }

        $data['pass'] = $pass;

        return view('orders.pass.no-redemption', $data);
    }

    public function passSending()
    {
        $pass = Pass::with('ordersPass.order',
            'ordersPass.order.procStatus',
            'ordersPass.order.country',
            'ordersPass.order.subProject',
            'ordersPass.order.getTargetValue')
            ->active()
            ->sending()
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$pass) {
            $pass = new Pass();
            $pass->active = 1;
            $pass->type = Pass::TYPE_SENDING;
            $pass->user_id = Auth::user()->id;
            $pass->sub_project_id = Auth::user()->sub_project_id ?? 0;
            $pass->save();
        }

        $data['pass'] = $pass;

        return view('orders.pass.sending', $data);
    }

    public function addOrderByTrackAjax(
        Request $request
    ) {
        $this->validate($request, [
            'pass_id' => 'required',
        ]);
        $pass = Pass::checkSubProject()
            ->active()
            ->where([
                ['user_id', Auth::user()->id],
                ['id', $request->pass_id]
            ])
            ->first();
        $orders = Order::findOrderByTrack([
            'order_id' => $request->id,
            'track'    => $request->track,
            'pass'     => $request->pass,
        ], $request->type_send);

        $res['success'] = false;
        $res['message'] = trans('alerts.order-not-pass');

        if ($pass && $orders) {
            foreach ($orders as $order) {
                if ($request->type_send) {
                    $order->pass_send_id = $pass->id;
                } else {
                    $order->pass_id = $pass->id;
                }
                $res['success'] = $order->save();

                if ($res['success']) {
                    $orderPass = new OrdersPass();
                    $orderPass->pass_id = $pass->id;
                    $orderPass->order_id = $order->id;
                    $orderPass->save();
                }
            }

            $pass = Pass::with(
                'ordersPass',
                'orders',
                'orders.procStatus',
                'orders.country',
                'orders.subProject',
                'orders.getTargetValue'
            )
                ->active()
                ->find($pass->id);

            $res['html'] = view('orders.pass.' . $pass->type . '-table', [
                'pass' => $pass
            ])->render();
            if ($res['success']) {
                $res['message'] = trans('alerts.successfully-added-in-pass'); //Order has been added in the pass successfully
            }
        }
        return response()->json($res);
    }

    public function passSave(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'cost_return.*' => 'numeric|min:0'
        ]);

        $pass = Pass::withCount('orders', 'ordersPass')
            ->withCount('transactions')
            ->checkSubProject()
            ->findOrFail($id);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if ($pass->active) {
            switch ($pass->type) {
                case Pass::TYPE_REDEMPTION :
                    {
                        ActionController::runPaidUp(['orders' => $pass->orders]);
                        Transaction::createProjectTransactions($pass, $request);
                        OrdersLog::addOrdersLog($pass->orders->pluck('id'), '<a href="' . route('pass-one', $pass->id) . '">Проводка ' . $pass->id . '</a>');
                        break;
                    }
                case Pass::TYPE_NO_REDEMPTION :
                    {
                        if ($request->cost_return) {
                            foreach ($request->cost_return as $orderId => $price) {
                                OrdersPass::where([
                                    ['order_id', $orderId],
                                    ['pass_id', $pass->id]
                                ])->update(['cost_return' => $price]);

                                TargetValue::where('order_id', $orderId)->update(['cost_return' => $price]);
                            }
                        }

                        ActionController::runRefused(['orders' => $pass->orders]);
                        Transaction::createProjectTransactions($pass, $request);

                        OrdersLog::addOrdersLog($pass->orders->pluck('id'), '<a href="' . route('pass-one', $pass->id) . '">Проводка ' . $pass->id . '</a>');
                        break;
                    }
                case Pass::TYPE_SENDING :
                    {
                        $status = ProcStatus::where('action', 'sent');
                        if (Auth::user()->project_id) {
                            $status->where('project_id', Auth::user()->project_id);
                        }

                        $status = $status->first();

                        $logModel = new OrdersLog();
                        if ($pass->ordersPass->isNotEmpty()) {
                            foreach ($pass->ordersPass as $ordersPass) {//обновляем цель заказа
                                if (isset($request->orders[$ordersPass->order_id])) {
                                    $target = TargetValue::where('order_id', $ordersPass->order_id)->first();
                                    if ($target) {
                                        $targetValue = json_decode($target->values, true);
                                        if (isset($targetValue['cost_actual'])) {
                                            $targetValue['cost_actual']['field_value'] = $request->orders[$ordersPass->order_id]['cost_actual'] ?? '';
                                        }
                                        if (isset($targetValue['track'])) {
                                            $targetValue['track']['field_value'] = $request->orders[$ordersPass->order_id]['track'] ?? '';
                                        }
                                        $target->values = json_encode($targetValue);
                                        $oldTrack = $target->track;
                                        $target->track = $request->orders[$ordersPass->order_id]['track'] ?? $target->track;
                                        if (isset($request->orders[$ordersPass->order_id]['cost_actual']) && $request->orders[$ordersPass->order_id]['cost_actual'] != $target->cost_actual) {
                                            $target->cost_actual = $request->orders[$ordersPass->order_id]['cost_actual'];
                                        }
                                        if ($target->save()) {
                                            // try{
                                            $data['cost_actual'] = $request->orders[$ordersPass->order_id]['cost_actual'];
                                            $data['order_id'] = $target->order_id;
                                            OrderProduct::divideOrderCostsAndPrices($data, NULL);
//                                            }catch (\Exception $exception){
//
//                                            }

                                            $log = '';
                                            $log .= $target->track ? 'Track -> ' . $target->track : '';
                                            $log .= $log ? '<br>' : '';
                                            $log .= 'Cost actual -> ' . $target->cost_actual . '<br>';
                                            $log .= $oldTrack ? 'Old track -> ' . $oldTrack . '<br>' : '';
                                            $log .= '<a href="' . route('pass-one', $pass->id) . '">Проводка ' . $pass->id . '</a>';

                                            if ($log) {
                                                $logModel->addOrderLog($ordersPass->order_id, $log);
                                            }
                                        }
                                    }
                                }
                            }

                            $data['status'] = $status->id ?? 24;
                            $data['orders'] = $pass->ordersPass->pluck('order_id')->toArray();
                            ActionController::runSentAction($data);
                            Transaction::createProjectTransactions($pass, $request);
                        }
                        break;
                    }
            }
        }

        $pass->comment = $request->comment ?? '';
        $pass->active = 0;
        if ($pass->save()) {
            return redirect()->route('pass-one', $id);
        }
        return redirect()->back();
    }

    public function passOne($id)
    {
        $pass = Pass::with('ordersPass.order',
            'ordersPass.order.procStatus',
            'ordersPass.order.country',
            'ordersPass.order.subProject',
            'ordersPass.order.getTargetValue')
            ->findOrFail($id);

        $data['pass'] = $pass;

        return view('orders.pass.' . $pass->type, $data);
    }

    public function passOrderDeleteAjax(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|exists:' . Order::tableName() . ',id',
        ]);

        $order = Order::whereHas('pass', function ($q) {
            $q->active();
        })
            ->find($request->pk);
        $res['success'] = false;
        $res['message'] = trans('alerts.order-deleted-pass');
        if ($order) {
            OrdersPass::where([
                ['pass_id', $order->pass_id],
                ['order_id', $order->id]
            ])->delete();

            $pass = Pass::with('ordersPass.order',
                'ordersPass.order.procStatus',
                'ordersPass.order.country',
                'ordersPass.order.subProject',
                'ordersPass.order.getTargetValue')
                ->find($order->pass_id);
            $order->pass_id = 0;
            $res['success'] = $order->save();
            $res['html'] = view('orders.pass.' . $pass->type . '-table', [
                'pass' => $pass
            ])->render();
            $res['id'] = $request->pk;
            $res['message'] = trans('alerts.order-deleted-successfully');
        }
        return response()->json($res);
    }

    public function passSendOrderDeleteAjax(Request $request)
    {
        $this->validate($request, [
            'pk' => 'required|exists:' . Order::tableName() . ',id',
        ]);

        $order = Order::whereHas('passSend', function ($q) {
            $q->active();
        })
            ->find($request->pk);
        $res['success'] = false;
        $res['message'] = trans('alerts.order-deleted-pass');
        if ($order) {
            OrdersPass::where([
                ['pass_id', $order->pass_send_id],
                ['order_id', $order->id]
            ])->delete();

            $pass = Pass::with('ordersPass.order',
                'ordersPass.order.procStatus',
                'ordersPass.order.country',
                'ordersPass.order.subProject',
                'ordersPass.order.getTargetValue')
                ->find($order->pass_send_id);
            $order->pass_send_id = 0;
            $res['success'] = $order->save();
            $res['html'] = view('orders.pass.' . $pass->type . '-table', [
                'pass' => $pass
            ])->render();
            $res['id'] = $request->pk;
            $res['message'] = trans('alerts.order-deleted-successfully');
        }
        return response()->json($res);
    }

    public function passOrderAddAjax(Request $request)
    {
        $this->validate($request, [
            'id'      => 'required|exists:' . Order::tableName(),
            'pass_id' => 'required|exists:' . Pass::tableName() . ',id',
        ]);

        $order = Order::moderated()
            ->checkAuth()
            ->where('pass_id', 0)
            ->find($request->id);
        $pass = Pass::active()
            ->find($request->pass_id);
        $res['success'] = false;
        $res['message'] = trans('alerts.order-not-pass');
        if ($order && $pass) {
            $order->pass_id = $request->pass_id;
            $res['success'] = $order->save();

            if ($res['success']) {
                $orderPass = new OrdersPass();
                $orderPass->pass_id = $order->pass_id;
                $orderPass->order_id = $order->id;
                $orderPass->save();
            }
            $pass = Pass::with('ordersPass.order',
                'ordersPass.order.procStatus',
                'ordersPass.order.country',
                'ordersPass.order.subProject',
                'ordersPass.order.getTargetValue')
                ->active()
                ->find($request->pass_id);
            $res['html'] = view('orders.pass.' . $pass->type . '-table', [
                'pass' => $pass
            ])->render();
            $res['message'] = trans('alerts.successfully-added-in-pass');
        }

        return response()->json($res);
    }

    public function passSendingOrderAddAjax(Request $request)
    {
        $this->validate($request, [
            'id'          => 'required|exists:' . Order::tableName(),
            'pass_id'     => 'required|exists:' . Pass::tableName() . ',id',
            'cost_actual' => 'string',
            'track'       => 'string',
        ]);

        $order = Order::moderated()
            ->checkAuth()
            ->where('pass_send_id', 0)
            ->find($request->id);
        $pass = Pass::active()
            ->find($request->pass_id);
        $res['success'] = false;
        $res['message'] = trans('alerts.order-not-pass');

        if ($order && $pass) {
            $order->pass_send_id = $request->pass_id;
            $res['success'] = $order->save();

            if ($res['success']) {
                $orderPass = new OrdersPass();
                $orderPass->pass_id = $request->pass_id;
                $orderPass->order_id = $order->id;
                $orderPass->cost_actual = $request->cost_actual ?? 0;
                $orderPass->track = $request->track ?? '';
                $orderPass->save();
                $res['message'] = trans('alerts.successfully-added-in-pass');
            }

            $pass = Pass::with('ordersPass.order',
                'ordersPass.order.procStatus',
                'ordersPass.order.country',
                'ordersPass.order.subProject',
                'ordersPass.order.getTargetValue')
                ->active()
                ->find($request->pass_id);

            $res['html'] = view('orders.pass.' . $pass->type . '-table', [
                'pass' => $pass
            ])->render();
        }

        return response()->json($res);
    }

    public function passOrdersSearchAjax(Request $request)
    {
        $filter = [
            'id'      => $request->input('id'),
            'surname' => $request->input('surname'),
            'phone'   => $request->input('phone'),
            'track'   => $request->input('track'),
            'type'    => $request->input('type'),
            'index'   => $request->input('index'),
        ];

        if ((view()->exists('orders.pass.' . $filter['type'] . '-search'))) {
            $data['searchOrders'] = Order::searchOrderRedemption($filter);
            $targetValue = \DB::table('target_values as tv')
                ->select('tv.values', 'tv.order_id', 'tc.alias', 'tc.name')
                ->leftJoin('target_configs as tc', 'tc.id', '=', 'tv.target_id')
                ->whereIn('tv.order_id', $data['searchOrders']->pluck('id'))
                ->get();
            $data['targets'] = (new Order())->getTargetValueForAllOrders($targetValue);
            return response()->json([
                'html' => view('orders.pass.' . $filter['type'] . '-search', $data)->render()
            ]);
        }
    }

    public function passOrdersChangeAjax(Request $request)
    {
        $this->validate($request, [
            'type'    => 'required|in:cost_return,track,cost_actual',
            'pass_id' => 'required|int|min:1',
            'id'      => 'required|int|min:1',
            'value'   => 'required'
        ]);
        $result['success'] = false;

        $result['success'] = OrdersPass::where([
            ['order_id', $request->id],
            ['pass_id', $request->pass_id]
        ])->update([$request->type => $request->value]);
        $result['message'] = transChanged('general.pass');

        return response()->json($result);
    }

    public function changeProcStatus(Request $request)
    {
        if (!empty($request->orders_ids)) {
            $orders = Order::whereIn('id', json_decode($request->orders_ids, true))
                ->update(['proc_status' => $request->proc_status]);
            if ($orders) {
                return response()->json([
                    'success'        => true,
                    'procStatusName' => ProcStatus::find($request->proc_status)->name
                ]);
            } elseif ($orders == 0) {
                return response()->json(['orders_updated' => true]);
            }
        }

        $order = Order::findOrFail($request->order_id);
        $order->proc_status = $request->proc_status;
        if ($order->save()) {
            return response()->json([
                'success'        => true,
                'procStatusName' => ProcStatus::find($request->proc_status)->name
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function changeProject($orderId)
    {
        return view('orders.change-project', [
            'order'        => Order::with([
                'products:' . OrderProduct::tableName() . '.price,' . Product::tableName() . '.id,title',
                'offer',
                'subProject',
                'project'
            ])->find($orderId),
            'procStatuses' => ProcStatus::callCenterStatuses()->get(),
        ]);
    }

    public function changeProjectAjax(Request $request)
    {
//        \Validator::extend('checkProject', function ( $attribute, $value, $parameters, $validator ) {
//            try {
//                $partnerId = $validator->getData()['partner_id'];
//                return Project::where([
//                    ['id', $value],
//                    ['partner_id', $partnerId]
//                ])->exists();
//            } catch (\Exception $exception) {
//                return false;
//            }
//        }, 'Проект не принадлежит партнеру');

        \Validator::extend('checkSubProject', function ($attribute, $value, $parameters, $validator) {
            try {
                //$partnerId = $validator->getData()['partner_id'];
                $projectId = $validator->getData()['project_id'];
                return Project::where([
                    ['id', $value],
                    //  ['partner_id', $partnerId],
                    ['parent_id', $projectId]
                ])->exists();
            } catch (\Exception $exception) {
                return false;
            }
        }, trans('validation.sub_project_invalid')); //Под проект не принадлежит проекту

        \Validator::extend('checkOffer', function ($attribute, $value, $parameters, $validator) use ($request) {

            return Offer::where([
                ['partner_id', $request->partner_id],
                ['id', $value]
            ])->exists();

        }, trans('validation.offer_invalid'));//Оффер не принадлежит проекту

        $this->validate($request, [
            'id'                       => 'required|exists:' . Order::tableName(),
//            'partner_id'               => 'required|exists:' . Order::tableName(),
            // 'project_id' => 'required|checkProject',
            'sub_project_id'           => 'required|checkSubProject',
            'proc_status'              => 'int|min:1',
            'products'                 => 'required|array',
            'products.*.product_id'    => 'required|int',
            'products.*.product_price' => 'required|numeric|min:0'

        ], [
            'products.required' => 'Веберите товар',
        ]);

        $order = Order::find($request->id);
        if ($order->proc_status == 11) {
            $order->pre_moderation_uid = auth()->user()->id;
            $order->pre_moderation_type = 11;
            $order->pre_moderation_time = now();
        }
        $orderBefore = $order->replicate();
        $order->proc_status = $request->proc_status ? $request->proc_status : $order->proc_status;
        $order->deleteAllProducts();
        $order->saveProducts($request->products);
        $order->setProcStatus();
        $order->project_id = $request->project_id;
        $order->subproject_id = $request->sub_project_id;

        $res = $order->save();
        OrdersLog::changeProject($orderBefore, $order);

        (new Order())->getProcessingStatusOrderApi($order->id);
        return response()->json([
            'success' => $res
        ]);
    }

    public function countOrdersByStatusAjax(Request $request)
    {
        $filter = [
            'id'               => $request->input('id'),
            'surname'          => $request->input('surname'),
            'phone'            => $request->input('phone'),
            'ip'               => $request->input('ip'),
            'oid'              => $request->input('oid'),
            'country'          => $request->input('country'),
            'project'          => $request->input('project'),
            'sub_project'      => $request->input('sub_project'),
            'status'           => $request->input('status'),
            'sub_status'       => $request->input('sub_status'),
            'target'           => $request->input('target'),
            'partners'         => $request->input('partners'),
            'offers'           => $request->input('offers'),
            'product'          => $request->input('product'),
            'date-type'        => $request->input('date-type'),
            'date_start'       => $request->input('date_start'),
            'date_end'         => $request->input('date_end'),
            'deliveries'       => $request->input('deliveries'),
            'track'            => $request->input('track'),
            'grouping'         => $request->input('grouping'),
            'display_products' => $request->input('display_products'),
            'products_count'   => $request->input('products_count'),
            'track_filter'     => $request->input('track_filter'),
            'order_cell'       => $request->input('order_cell'),
            'order_sort'       => $request->input('order_sort'),
            'initiator'        => $request->input('initiator'),
            'initiatorName'    => $request->input('initiatorName'),
            'tag_source'       => $request->input('tag_source'),
            'tag_medium'       => $request->input('tag_medium'),
            'tag_content'      => $request->input('tag_content'),
            'tag_campaign'     => $request->input('tag_campaign'),
            'tag_term'         => $request->input('tag_term'),
        ];

        if (!$filter['date_start'] || !$filter['date_end']) {
            $filter['date-type'] = false;
        }

        return response()->json(Order::countOrderByStatus($filter));
    }

    public function cancelSendAjax(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:' . Order::tableName()
        ]);

        $res = ActionController::runCancelSend([$request->id])[$request->id] ?? false;

        return response()->json([
            'success' => $res,
        ]);
    }

    public function saveStatusForLockedOrder(Request $request)
    {
        $this->validate($request, [
            'orderId'      => 'required|int',
            'procStatusId' => 'required|int',
        ]);
        $order = Order::where('id', $request->orderId)->first();
        if ($order->proc_status != $request->procStatusId) {
            $procStatus = ProcStatus::find($request->procStatusId);
            $request['action'] = $procStatus->action;
            $request['status'] = $request->procStatusId;
            $request['orders'] = [$request->orderId];

            (new ActionController)->runActionAjax($request);
            return response()->json(['success' => true]);
        } else {
            return response()->json(['current_status' => true]);
        }
    }

    public function annulModeration($id)
    {
        $order = Order::moderated()->findOrFail($id);

        $order->moderation_id = 0;
        $order->moderation_time = null;

        (new OrdersLog())->addOrderLog($order->id, 'Модерация аннулированна');

        $result = [
            'message' => trans('alerts.moderation-not-annulled'),
            'success' => false,
        ];

        if ($order->save()) {
            $result = [
                'message' => trans('alerts.moderation-annulled'),
                'success' => true,
            ];
        }

        return response()->json($result);
    }

    //todo костыль
    public function resetProcStage(Request $request)
    {
        $filter = [
            'date-type'     => $request->input('date-type'),
            'date_start'    => $request->input('date_start'),
            'date_end'      => $request->input('date_end'),
            'owner'         => $request->input('owner'),
            'country'       => $request->input('country'),
            'product'       => $request->input('product'),
            'offers'        => $request->input('offers'),
            'status'        => $request->get('status'),
            'target'        => $request->input('target'),
            'id'            => $request->input('id'),
            'name'          => $request->input('name'),
            'surname'       => $request->input('surname'),
            'middle'        => $request->input('middle'),
            'phone'         => $request->input('phone'),
            'ip'            => $request->input('ip'),
            'group'         => $request->input('group'),
            'user'          => $request->input('user'),
            'oid'           => $request->input('oid'),
            'company'       => $request->input('company'),
            'project'       => $request->input('project'),
            'partners'      => $request->input('partners'),
            'sub_project'   => $request->input('sub_project'),
            'entity'        => $request->input('entity'),
            'cause_cancel'  => $request->input('cause_cancel'),
            'not_available' => $request->input('not_available'),
        ];

        $priority = $request->get('priority');
        $all = $request->get('allOrders');
        $ordersIds = $request->get('orders');
        $res = [
            'message' => '',
            'success' => false,
        ];

        if ($priority < 1) {
            $res['message'] = trans('validation.priority-must-be-more');
            return $res;
        }

        $orders = collect();
        if ($all) {
            $orders = Order::ordersForReset($filter);
        } else if ($ordersIds) {
            $orders = Order::whereIn('id', $ordersIds)->get();
        }

        if ($orders->isNotEmpty()) {
            foreach ($orders->chunk(500) as $chunkOrder) {
                $ids = $chunkOrder->pluck('id')->toArray();
                (new Order())->deleteCallsByIds($ids);
            }

            if (Order::resetProcStage($orders, $priority)) {
                $res['success'] = true;
                $res['message'] = trans('alerts.data-successfully-added');
            } else {
                $res['message'] = trans('alerts.data-not-added');
            }
        } else {
            $res['message'] = trans('general.order-not-found');
        }

        return $res;
    }
}
