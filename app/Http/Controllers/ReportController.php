<?php

namespace App\Http\Controllers;

use App\Exports\Reports\SalesExport;
use App\Models\Api\NovaposhtaKey;
use App\Models\Company;
use App\Models\CallProgressLog;
use App\Models\Offer;
use App\Models\OrdersOpened;
use App\Models\ProcStatus;
use App\Models\Redemption;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\UsersTime;
use App\Repositories\FilterRepository;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use App\Repositories\UserRepository;
use Doctrine\Common\Annotations\Annotation\Target;
use Illuminate\Http\Request;
use \App\Models\User;
use App\Models\Country;
use App\Models\Product;
use App\Models\Order;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends BaseController
{
    public function index(
        Request $request,
        Order $orderModel,
        User $authModel,
        Product $offerModel,
        Country $countryModel
    )
    {
        $data = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'date_type' => $request->input('date_type'),
            'group' => $request->input('group'),
            'user' => $request->input('user'),
            'offer' => $request->input('offer'),
            'project' => $request->input('project'),
            'country' => $request->input('country'),
            'learning' => $request->input('learning'),

            'source' => $request->input('source'),
            'medium' => $request->input('medium'),
            'content' => $request->input('content'),
            'campaign' => $request->input('campaign'),
            'term' => $request->input('term'),
            'company' => $request->input('company'),
            'display_hp' => $request->input('display_hp'),

        ];

        if ($request->isMethod('post')) {
            if ($request->request->get('offer') == null) {
                $data['offer'] = '';
            }
            header('Location: ' . route('reports-main') . $this->getFilterUrl($data), true, 303);
            exit;
        }
        return view('reports.main', [
            'orders' => $orderModel->getAuditAll($data),
            'users' => $authModel->getOperatorName(auth()->user()->company_id),
            'projects' => collect(Project::where('parent_id', 0)->get())->keyBy('id'),
            'offers_filter' => $offerModel->getOffersNameNoParent(),
            'country' => $countryModel->getAllCountryArray(),
            'companies' => Company::all(),
            'source' => [],
            'medium' => [],
            'content' => [],
            'campaign' => [],
            'term' => [],
        ]);
    }

    public function timeLoginLogout( Request $request, UsersTime $usersTimeModel )
    {
        $filter = [
            'id' => $request->get('id'),
            'surname' => $request->get('surname'),
            'name' => $request->get('name'),
            'date_start' => $request->get('date_start'),
            'date_end' => $request->get('date_end'),
            'detail' => $request->get('detail'),
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('reports-time-login-logout') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $data['times'] = $usersTimeModel->getAllTime($filter);

        return view('reports.timeLoginLogout', $data);
    }

    public function talkTime(
        Request $request,
        User $authModel,
        Country $countriesModel,
        Company $companiesModel,
        CallProgressLog $callProgressLogModel
    )
    {
        $filter = [
            'company' => $request->get('company'),
            'user' => $request->get('user'),
            'trunk' => $request->get('trunk'),
            'country' => $request->get('country'),
            'date_start' => $request->get('date_start') ? $request->get('date_start') : date('d.m.Y'),
            'date_end' => $request->get('date_end') ? $request->get('date_end') : date('d.m.Y'),
            'group' => $request->get('group') ? $request->get('group') : 'company',
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('reports-talk-time') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $nameGroup = '';
        switch ($filter['group']) {
            case 'company' :
                {
                    $nameGroup = trans('general.company');
                    break;
                }
            case 'trunk' :
                {
                    $nameGroup = trans('general.trunk');
                    break;
                }
            case 'user' :
                {
                    $nameGroup = trans('general.operator');
                    break;
                }
            case 'country' :
                {
                    $nameGroup = trans('general.country');
                    break;
                }
        }

        $data['group'] = $nameGroup;
        $data['data'] = $callProgressLogModel->getAccountTalkTime($filter);
        $data['users'] = $authModel->getOperatorName(auth()->user()->company_id);
        $data['countries'] = $countriesModel->getAllCountryArray();
        $data['companies'] = Company::all();
        $data['trunks'] = $callProgressLogModel->getTrunks();

        return view('reports.talk-time', $data);
    }

    /*detailing orders opened by operator*/
    public function ordersOpened( Request $request )
    {

        $filter = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'country' => $request->input('country'),
            'status' => $request->input('status'),
            'proc_status' => $request->input('proc_status'),
            'target' => $request->input('target'),
            'id' => $request->input('id'),
            'user' => $request->input('user'),
            'oid' => $request->input('oid'),
            'company' => $request->input('company'),
            'operator_assigned' => $request->input('operator_assigned'),
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('reports-orders-opened') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $ordersOpened = (new OrdersOpened)->getOrdersOpened($filter);
        $filterByCountry = '';
        $filterByCompany = '';
        $filterByUser = '';
        $filterByDate = '';
        $filterByStatus = '';
        $filterByAssignement = '';

        if ($filter['country']) {
            $countries = explode(',', $filter['country']);
            foreach ($countries as $country) {
                $newArray[] = "'" . $country . "'";
            }
            $countries = implode(',', $newArray);
            $filterByCountry = (!empty($filter['country']) ? "AND o.geo in (" . $countries . ")" : '');
        }
        if ($filter['company']) {
            $filterByCompany = (!empty($filter['company']) ? "AND u.company_id in (" . $filter['company'] . ")" : '');
        }
        if ($filter['user']) {
            $filterByUser = (!empty($filter['user']) ? "AND u.id in (" . $filter['user'] . ")" : '');
        }

        if ($filter['date_start'] && $filter['date_end']) {
            $filter['date_start'] = Carbon::parse($filter['date_start']);
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
            $filterByDate = "AND date_opening BETWEEN '" . $filter['date_start'] . "' AND '" . $filter['date_end'] . "'";
        }

        if ($filter['status']) {
            $filterByStatus = $filter['status'] ? "AND callback in (" . $filter['status'] . ")" : '';
        }

        if ($filter['operator_assigned']) {
            $filterByAssignement = $filter['operator_assigned'] ? " count(CASE WHEN (oo.target_status = 1) AND (oo.user_id = o.target_user) THEN 1 END) AS ordersOpenedApprovedQuantityAssigned," : '';
        }

        $ordersOpenedQuantity = collect(DB::select("SELECT count(oo.id) AS ordersOpenedQuantity,
                    count(CASE WHEN (oo.target_status = 1) THEN 1 END) AS ordersOpenedApprovedQuantity,
                    " . $filterByAssignement . "
                    count(CASE WHEN (oo.target_status = 2) THEN 1 END) AS ordersOpenedRejectedQuantity,
                    count(CASE WHEN (oo.target_status = 3) THEN 1 END) AS ordersOpenedCanceledQuantity,
                    count(CASE WHEN (oo.target_status  = 0) AND (oo.callback = 0) THEN 1 END) AS ordersWithoutTargetQuantity,
                    count(CASE WHEN (oo.callback is NOT NULL) and  (oo.callback <> '') THEN 1 END) AS ordersCallBackTargetQuantity
                    FROM orders_opened AS oo
                    LEFT JOIN orders AS o ON o.id = oo.order_id
                    LEFT JOIN users AS u ON u.id = oo.user_id
                    WHERE oo.id <> 0
                    " . $filterByCountry . "
                    " . $filterByCompany . "
                    " . $filterByUser . "
                    " . $filterByDate . "
                    " . $filterByStatus . "
                    "))->first();

        $orderIds = [];
        if ($ordersOpened) {
            foreach ($ordersOpened as $orderOpened) {
                $orderIds[] = $orderOpened->order_id;
            }
        }

        $targetValue = DB::table('targets_final')
            ->select('value', 'order_id')
            ->whereIn('status', [2, 3, 4, 5])
            ->whereIn('order_id', $orderIds)
            ->get();
        $cause = [];
        if ($targetValue) {
            foreach ($targetValue as $value) {
                $cause[$value->order_id] = (new Order)->getValueStatus($value->value);
            }
        }

        return view('reports.orders-opened', [
            'ordersOpened' => $ordersOpened->appends(Input::except('page')),
            'ordersOpenedQuantity' => $ordersOpenedQuantity,
            'countries' => Country::all(),
            'cause' => $cause,
            'companies' => Company::all(),
            'users' => User::all()
        ]);
    }

    public function sales( Request $request )
    {
        $filter = [
            'date_start' => isset($request['export']) && isset($request['filters']['date_start']) ? $request['filters']['date_start'] : $request->date_start,
            'date_end' => isset($request['export']) && isset($request['filters']['date_end']) ? $request['filters']['date_end'] : $request->date_end,
            'product' => isset($request['export']) && isset($request['filters']['product']) ? $request['filters']['product'] : $request->product,
            'project' => isset($request['export']) && isset($request['filters']['project']) ? $request['filters']['project'] : $request->project,
            'sub_project' => isset($request['export']) && isset($request['filters']['sub_project']) ? $request['filters']['sub_project'] : $request->sub_project,
//            'date_template'            => isset($request['export']) && isset($request['filters']['date_template']) ? $request['filters']['date_template'] : $request->date_template,
            'status' => isset($request['export']) && isset($request['filters']['status']) ? $request['filters']['status'] : $request->status,
        ];
        $dataFilters = FilterRepository::processFilterData($filter);

        if ($request->isMethod('post')) {

            header('Location: ' . route('sales') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $data = (new Product())->getProductsByFilters($filter);

        $statuses = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)
            ->statusesUser()
            ->whereIn('action', ['paid_up', 'sent', 'at_department', 'received'])
            ->get()
            ->keyBy('action')->toArray();

        $redemptionPercents = Redemption::all();
        $data['dataFilters'] = $dataFilters;

        return view('reports.sales', $data, [
            'statuses' => $statuses,
            'redemptionPercents' => $redemptionPercents,
            'filters' => $filter
        ]);
    }

    public function statuses( Request $request )
    {
        $data = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'date_type' => $request->input('date_type'),
            'country' => $request->input('country'),
            'project' => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'division' => $request->input('division'),
            'proc_status' => $request->input('proc_status'),
            'result' => $request->input('result'),
            'offers' => $request->input('offers'),
            'product' => $request->input('product'),
            'delivery' => $request->input('delivery'),
        ];
        $dataFilters = FilterRepository::processFilterData($data);
        if ($request->isMethod('post')) {
            header('Location: ' . route('report-statuses') . $this->getFilterUrl($data), true, 303);
            exit;
        }

        //todo костыль 17.11
        if (Auth::user()->sub_project_id) {
            $data['sub_project'] = Auth::user()->sub_project_id;
        }
        $senderProcStatuses = ProcStatus::with('project')->senderStatuses()->get();
        $senderProcStatusesByKey = $senderProcStatuses->keyBy('id');

        return view('reports.statuses', [
            'byStatus' => Order::reportByStatuses($data),
            'productsStat' => $data['sub_project'] ? Product::productsForStatuses($data) : collect(),
            'projects' => Project::all()->keyBy('id'),
            'offers' => (new Product())->getOffersNameNoParent(),
            'countries' => (new Country())->getAllCountryArray(),
            'proc_statuses' => $senderProcStatuses,
            'procStatusesByKey' => $senderProcStatusesByKey,
            'products' => Product::with('project')->get(),
        ], $dataFilters);
    }

    public function collectings( Request $request )
    {
        $data = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'date_type' => $request->input('date_type'),
            'country' => $request->input('country'),
            'project' => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'proc_status' => $request->input('proc_status'),
            'result' => $request->input('result'),
        ];

        $dataFilters = FilterRepository::processFilterData($data);
        //  dd($data);
        if ($request->isMethod('post')) {
            header('Location: ' . route('report-collectings') . $this->getFilterUrl($data), true, 303);
            exit;
        }

        $countries = (new Country)->getAllCounties()->keyBy('code');
        $procStatuses = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)
            ->statusesUser()->whereIn('action_alias', ['sent', 'at_department'])
            ->get()
            ->keyBy('id');
        $collectings = CollectingController::getReportsData($data);

        return view('reports.collectings', compact('collectings', 'countries', 'procStatuses', 'total'), $dataFilters);
    }

    public function moderators( Request $request )
    {
        $filter = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'date_type' => $request->input('date_type'),
            'country' => $request->input('country'),
            'project' => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'moderator' => $request->input('moderator')
        ];
        $dataFilters = FilterRepository::processFilterData($filter);
        $moderators = User::all();
        $moderatorsData = UserRepository::getModeratorsStatistics($filter);

        if ($request->isMethod('post')) {
            header('Location: ' . route('report-moderators') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $countries = (new Country)->getAllCounties()->keyBy('code');
        $moderatorsById = $moderators->keyBy('id');

        return view('reports.moderators', compact('countries', 'moderatorsData', 'moderatorsById'), $dataFilters);
    }

    public function getSalesReportExport( Request $request )
    {
        $request['export'] = true;
        $dataForExcel = $this->sales($request);

        if (!empty($dataForExcel) && $dataForExcel->getData()) {
            $fileName = 'sales_report_' . date("d.m.Y_H:i:s", time());
            return Excel::download(new SalesExport($dataForExcel->getData(), $request), $fileName . '.xlsx');
        }
    }

    public function operatorsReport( Request $request )
    {
        $filter = [
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'date_type' => $request->input('date_type'),
            'country' => $request->input('country'),
            'project' => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'operator' => $request->input('operator'),
            'company' => $request->input('company')
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('report-operators') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $dataFilters = FilterRepository::processFilterData($filter);
        $moderators = User::all();

        $operators = (new User())->where('role_id', 1);

        if ($filter['company']) {
            $operators = $operators->where('company_id', $filter['company']);
        }
        $operators = $operators->get()->keyBy('id');

        $companies = Company::all();
        $operatorsData = UserRepository::getOperatorsStatistics($filter, $operators);


        $countries = (new Country)->getAllCounties()->keyBy('code');

        $operatorsById = $moderators->keyBy('id');
        return view('reports.operators', compact('operators', 'companies', 'operatorsData', 'operatorsById'), $dataFilters);
    }

    public function verificationOrdersOperators( Request $request )
    {
        $data = [
            'order_ids' => $request->input('order_ids'),
        ];

        if ($request->isMethod('post')) {
            $data['order_ids'] = str_replace("\n", ",", str_replace("\r", "", $data['order_ids']));
            // header('Location: ' . route('verification-orders--operators') . $this->getFilterUrl($data), true, 303);
            // exit;
        }

        $ids = explode(",", $data['order_ids']);

        $orders = collect();
        $subtotal = collect();

        if ($ids) {
            $orders = Order::with('targetUser')
                ->whereIn('id', $ids)
                ->orderBy('time_modified', 'ASC')
                ->get();

            if ($orders->isNotEmpty()) {
                $count_users = ['orders' => false, 'users' => false];
                $total_orders = count($orders);

                foreach ($orders as $order) {
                    $count_users['users'][$order->target_user] = !empty($order->targetUser) ? $order->targetUser->name . ' ' . $order->targetUser->surname : 'n/a';
                    if (!empty($count_users['orders'][$order->target_user])) {
                        $count_users['orders'][$order->target_user] = $count_users['orders'][$order->target_user] + 1;
                    } else {
                        $count_users['orders'][$order->target_user] = 1;
                    }
                }

                if (!empty($count_users['orders'])) {
                    foreach ($count_users['orders'] as $user_id => $count_orders) {
                        $subtotal[$count_users['users'][$user_id]] = $count_orders;
                    }
                }
            }
        }

        return view('reports.verification-orders-operators', compact('orders', 'subtotal'));
    }

    public function reportByCity( Request $request )
    {
        $data = [
            'country' => $request->input('country'),
            'project' => $request->input('project'),
            'sub_project' => $request->input('sub_project'),
            'product' => $request->input('product'),
            'date_start' => $request->input('date_start'),
            'date_end' => $request->input('date_end'),
            'date_type' => $request->input('date_type'),
        ];

        if ($request->isMethod('post')) {
            header('Location: ' . route('report-by-city') . $this->getFilterUrl($data), true, 303);
            exit;
        }

        $dataFilters = FilterRepository::processFilterData($data);
        $result = Order::getOrdersByCountry($data);
        $countries = (new Country())->getAllCountryArray();

        return view('reports.by_city', compact('result', 'countries'), $dataFilters);
    }

    public function reportByCounterparties( Request $request )
    {
        $filters = [
            'date_start'  => $request->input('date_start'),
            'date_end'    => $request->input('date_end'),
            'date_type'   => $request->input('date_type'),
            'proc_status' => $request->input('proc_status'),
            'project' => $request->input('project'),
        ];

        $dataFilters = FilterRepository::processFilterData($filters);
        if ($request->isMethod('post')) {
            header('Location: ' . route('report-by-counterparties') . $this->getFilterUrl($filters), true, 303);
            exit;
        }

        $novaposhtaData = OrderRepository::getCounterpartiesData($filters);

        $novaposhtaSenders = NovaposhtaKey::all()->keyBy('id')->toArray();
        $procStatuses = ProcStatus::all()->keyBy('id')->toArray();
        $senderProcStatuses = ProcStatus::with('project')->whereIn('action', ['sent', 'paid-up',
            'received', 'returned', 'refused', 'at_department', 'paid_up'])->senderStatuses()->get();

        return view('reports.by-counterparties', compact('novaposhtaSenders', 'novaposhtaData',
            'procStatuses', 'senderProcStatuses', 'dataFilters'));
    }
}
