<?php

namespace App\Http\Controllers;

use App\Models\CollectorLog;
use App\Models\Company;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\Partner;
use App\Models\ProcStatus;
use App\Models\Product;
use App\Models\Project;
use App\Models\TargetConfig;
use App\Models\User;
use App\Repositories\CollectingRepository;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class CollectingController extends BaseController
{
    /**
     * @param Request $request
     * @return array
     */
    protected static function filterInputs(Request $request): array
    {
        $filter = [
            'id'               => $request->input('id'),
            'surname'          => $request->input('surname'),
            'phone'            => $request->input('phone'),
            'oid'              => $request->input('oid'),
            'country'          => $request->input('country'),
            'project'          => $request->input('project'),
            'sub_project'      => $request->input('sub_project'),
            'status'           => $request->input('status'),
            'date-type'        => $request->input('date-type'),
            'date_start'       => $request->input('date_start'),
            'date_end'         => $request->input('date_end'),
            'processing_count' => $request->input('processing_count') === "0" ? "no_processed" :  $request->processing_count,
            'order_cell'       => $request->input('order_cell'),
            'order_sort'       => $request->input('order_sort'),
        ];
//        if(\auth()->user()->id == 987593){
//            dd($filter);
//        }
        return $filter;
    }

    /**
     * @param Country $countryModel
     * @param $data
     * @return mixed
     */
    public static function dataForFilters($data)
    {
        $data['statuses'] = ProcStatus::where('type', ProcStatus::TYPE_SENDERS)
            ->statusesUser()->whereIn('action_alias', ['sent', 'at_department'])
            ->get()
            ->keyBy('id');
        $data['country'] = (new Country)->getAllCounties()->keyBy('code');
        return $data;
    }

    function index(
        Request $request,
        Order $orderModel
    ) {
        $filter = self::filterInputs($request);

        $dataFilters = [];
        $formatted_projects = [];
        $formatted_subProjects = [];

        if ($filter['project']) {
            $projectsIds = is_array($filter['project']) ? $filter['project'] : explode(',', $filter['project']);

            $projects = Project::whereIn('id', $projectsIds)->where('parent_id', 0)->get();
            foreach ($projects as $key => $project) {
                $formatted_projects[] = ['id' => $project->id, 'text' => $project->name];
            }
            $dataFilters['dataProject'] = json_encode($formatted_projects);
            $dataFilters['dataProjectIds'] = $filter['project'];
        }

        if ($filter['sub_project']) {
            $subProjectsIds = is_array($filter['sub_project']) ? $filter['sub_project'] : explode(',', $filter['sub_project']);

            $subProjects = Project::whereIn('id', $subProjectsIds)->where('parent_id', '!=', 0)->get();
            foreach ($subProjects as $key => $subProject) {
                $formatted_subProjects[] = ['id' => $subProject->id, 'text' => $subProject->name];
            }
            $dataFilters['dataSubProject'] = json_encode($formatted_subProjects);
            $dataFilters['dataSubProjectIds'] = $filter['sub_project'];
        }

        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }

            header('Location: ' . route('collectings') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $data = $orderModel->ordersForCollectors($filter);
        $data = self::dataForFilters($data);

        $data['collectors'] = User::where('role_id', 14)->get(['id', 'name', 'surname']);
        $data['amount'] = $this->amountOrdersByPage($filter);
        return view('collectings.index', $data, $dataFilters);
    }

    public function collectorProcessed($orderId)
    {
        $result = CollectorLog::saveProcessed($orderId, Auth::user()->id);

        if ($result) {
            (new OrdersLog())->addOrderLog($orderId, 'Заказ обработан коллектором');
            return redirect()->route('collectings-hand-processing');
        }

        return redirect()->route('order-sending', $orderId);
    }

    public function processing(Request $request)
    {
        $filter = self::filterInputs($request);

        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('collectings-processing') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $logs = CollectorLog::logsToday($filter);
        $countOrder = CollectorLog::logsToday($filter, true)->keyBy('order_id')->count();
        $collectors = User::withCollectorRole()->get(['id', 'name', 'surname']);
        $amount = $this->amountOrdersByPage($filter);
        return view('collectings.processing', compact(
            'logs',
            'countOrder',
            'collectors',
            'amount'
        ), self::dataForFilters([]));
    }

    public function handProcessing(Request $request)
    {
        $filter = self::filterInputs($request);

        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('collectings-hand-processing') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $orders = Order::orderCollectors($filter, CollectorLog::TYPE_HAND, isset($this->permissions['collectors_show_all_hand_orders']));
        $countOrder = $orders->total();
        $collectors = User::withCollectorRole()->get(['id', 'name', 'surname']);
        $amount = $this->amountOrdersByPage($filter);

        return view('collectings.handProcessing', compact(
            'orders',
            'countOrder',
            'collectors',
            'amount'
        ), self::dataForFilters([]));
    }

    public function autoProcessing(Request $request)
    {
        $filter = self::filterInputs($request);

        if ($request->isMethod('post')) {
            if (!$filter['date_start'] || !$filter['date_end']) {
                $filter['date-type'] = false;
            }
            header('Location: ' . route('collectings-auto-processing') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $orders = Order::orderCollectors($filter, CollectorLog::TYPE_AUTO, true);
        $countOrder = $orders->total();
        $collectors = User::withCollectorRole()->get(['id', 'name', 'surname']);
        $amount = $this->amountOrdersByPage($filter);
        return view('collectings.autoProcessing', compact(
            'orders',
            'countOrder',
            'collectors',
            'amount'
        ), self::dataForFilters([]));
    }

    public function shareCollectorOrder(Request $request)
    {
        $filter = self::filterInputs($request);
        $collectors = empty($request->collectors) ? User::withCollectorRole()->get() : User::withCollectorRole()
            ->whereIn('id', $request->collectors)->get();

        if (!count($collectors)) {
            return response()->json([
                'message' => 'Нет коллекторов',
                'error'   => true,
            ]);
        }

        if ($request->shareAllOrders) {
            switch (strtok(\URL::previous(), '?')) {
                case route('collectings'):
                    $data = (new  Order)->ordersForCollectors($filter, true);
                    break;
                case route('collectings-processing'):
                    $logs = CollectorLog::logsToday($filter, true);
                    $data['ordersAll'] = $logs->pluck('order')->keyBy('id');
                    break;
                case route('collectings-hand-processing'):
                    $data['ordersAll'] = Order::orderCollectors($filter, CollectorLog::TYPE_HAND, isset($this->permissions['collectors_show_all_hand_orders']), true);
                    break;
                case route('collectings-auto-processing'):
                    $data['ordersAll'] = Order::orderCollectors($filter, CollectorLog::TYPE_AUTO, true, true);
                    break;
                default :
                    $data = collect();
                    break;
            }
            $data['res'] = $data['ordersAll']->split(count($collectors));
        } else {
            $data['res'] = Order::whereIn('id', $request->orders)->get()->split(count($collectors));
        }

        foreach ($data['res']->collapse()->chunk(500) as $orders) {
            $ids = $orders->pluck('id')->toArray();
            (new Order())->deleteCallsByIds($ids);
            CollectorLog::deleteLogs($ids);
        }

        $result = false;
        switch ($request->type) {
            case CollectorLog::TYPE_HAND :
                $array = [];
                $logs = [];
                foreach ($collectors as $key => $collector) {
                    if (isset($data['res'][$key])) {
                        $logs[$collector->id] = [
                            'text' => 'Заказ назначен коллектору ' . implode(' ', [
                                    $collector->surname,
                                    $collector->name
                                ]),
                            'ids'  => []
                        ];
                        foreach ($data['res'][$key] as $order) {
                            $array[] = [
                                'type'       => CollectorLog::TYPE_HAND,
                                'user_id'    => $collector->id,
                                'order_id'   => $order->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];
                            $logs[$collector->id]['ids'][] = $order->id;
                        }
                    }
                }
                $result = CollectorLog::insert($array);

                if ($result && $logs) {
                    foreach ($logs as $log) {
                        OrdersLog::addOrdersLog($log['ids'], $log['text']);
                    }
                }

                break;

            case CollectorLog::TYPE_AUTO :
                $result = CollectingRepository::addOrderToPbx($data['res']->collapse());

                break;
            case 'cancel' :
                $result = true;
                OrdersLog::addOrdersLog($data['res']->collapse()->pluck('id')
                    ->toArray(), 'Удален из обработки для коллекторов');
                break;
        }

        return response()->json([
            'success' => $result,
        ]);

    }

    protected function amountOrdersByPage()
    {
        try {
            $logs = CollectorLog::logsToday([], true);
            $processed = $logs->keyBy('order_id')->count();
            return [
                'all'       => (new  Order)->ordersForCollectors([])['countOrder'],
                'processed' => $processed,
                'hand'      => Order::orderCollectors([], CollectorLog::TYPE_HAND, isset($this->permissions['collectors_show_all_hand_orders']))
                    ->total(),
                'auto'      => Order::orderCollectors([], CollectorLog::TYPE_AUTO, isset($this->permissions['collectors_show_all_hand_orders']))
                    ->total(),
            ];
        } catch (\Exception $exception) {
            return [
                'all'       => 0,
                'processed' => 0,
                'hand'      => 0,
                'auto'      => 0,
            ];
        }
    }

    public static function getReportsData($filter)
    {
        //Collectors logs by Sub project
        $query = $result = CollectorLog::
        select('orders.subproject_id', 'projects.name as subproject',
            \DB::raw('count(collector_logs.id) as countColLogs'),
            \DB::raw('count(distinct orders.id) as countOrders'),
            \DB::raw('count(CASE WHEN (collector_logs.type = "hand") THEN 1 END) AS hand'),
            \DB::raw('count(CASE WHEN (collector_logs.type = "auto") THEN 1 END) AS auto'),
            \DB::raw('count(CASE WHEN ( collector_logs.processed = 1 ) THEN 1 END) AS processed'),
            \DB::raw('count(CASE WHEN ( collector_logs.processed = 0 ) THEN 1 END) AS noProcessed')
        )
            ->leftJoin('orders', 'orders.id', '=', 'collector_logs.order_id')
            ->leftJoin('projects', 'orders.subproject_id', '=', 'projects.id')
            ->where('projects.parent_id', '!=', 0)
            ->whereHas('order', function ($query) {
                $query->where('moderation_id', '>', 0)
                    ->where('target_status', 1)
                    ->where('service', '!=', 'call_center');
            });

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $filter['date_start'] = Carbon::parse($filter['date_start']);
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
            $query = $query->whereBetween('collector_logs.updated_at', [$filter['date_start'], $filter['date_end']]);
        }


        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $query = $query->whereIn('orders.geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $query = $query->whereIn('orders.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $query = $query->whereIn('orders.subproject_id', $subProject);
        }
        if ($filter['proc_status']) {
            $procStatuses = explode(',', $filter['proc_status']);
            $query = $query->whereIn('orders.proc_status', $procStatuses);
        }

        $resultCollectorLogs = $query
            ->groupBy('orders.subproject_id')
            ->get()->keyBy('subproject_id');

        $query = Order:: select(\DB::raw('count(id) as ordersCount'), 'subproject_id'
        )->where(function ($query) {
            $query->where('moderation_id', '>', 0)
                ->where('target_status', 1)
                ->where('service', '!=', 'call_center');
        })
            ->whereHas('collectorLogs')
            ->withCount('collectorLogs')
            ->withCount('collectorLogs')
            ->groupBy('subproject_id', 'collector_logs_count');

        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $query = $query->whereIn('geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $query = $query->whereIn('project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $query = $query->whereIn('subproject_id', $subProject);
        }
        if ($filter['proc_status']) {
            $procStatuses = explode(',', $filter['proc_status']);
            $query = $query->whereIn('proc_status', $procStatuses);
        }

        $result = $query->get();
        foreach ($result as $item) {
            $data[$item->subproject_id][$item->collector_logs_count] = $item->ordersCount;
        }
        $resultData = [];
        foreach ($resultCollectorLogs as $key => $item) {
            $item->dataCollected = $data[$key];
            $resultData[$key] = $item;
        }

        //Collectors Logs by Collectors(Users)
        $query = CollectorLog::
        select('users.name', 'users.surname', 'users.login',
            \DB::raw('count(collector_logs.id) as countColLogsByUser'),
            \DB::raw('count(distinct collector_logs.order_id) as countUsersOrders'),
            \DB::raw('count(CASE WHEN (collector_logs.type = "hand") THEN 1 END) AS handUsers'),
            \DB::raw('count(CASE WHEN (collector_logs.type = "auto") THEN 1 END) AS autoUsers'),
            \DB::raw('count(CASE WHEN ( collector_logs.processed = 1 AND collector_logs.executor_id = collector_logs.user_id ) THEN 1 END) AS processedUsers'),
            \DB::raw('count(CASE WHEN ( collector_logs.processed = 0 ) THEN 1 END) AS noProcessedUsers')
        )
            ->leftJoin('users', 'users.id', '=', 'collector_logs.user_id')
            ->leftJoin('orders as o', 'o.id', '=', 'collector_logs.order_id')
            ->whereHas('order', function ($query) {
                $query->where('moderation_id', '>', 0)
                    ->where('target_status', 1)
                    ->where('service', '!=', 'call_center');
            });

        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $query = $query->whereIn('o.geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $query = $query->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $query = $query->whereIn('o.subproject_id', $subProject);
        }
        if ($filter['proc_status']) {
            $procStatuses = explode(',', $filter['proc_status']);
            $query = $query->whereIn('o.proc_status', $procStatuses);
        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $query = $query->whereBetween('collector_logs.updated_at', [
                Carbon::parse($filter['date_start']),
                Carbon::parse($filter['date_end'])->endOfDay()
            ]);
        }

        $resultByUsers = $query
            ->groupBy('collector_logs.user_id')
            ->get();

        //Collectors Logs by Statuses
        $query = CollectorLog::select('o.proc_status',
            \DB::raw('count(collector_logs.id) as countColLogs'),
            \DB::raw('count(distinct collector_logs.order_id) as countOrders')
        )
            ->leftJoin('orders as o', 'o.id', '=', 'collector_logs.order_id')
            ->whereHas('order', function ($query) {
                $query->where('moderation_id', '>', 0)
                    ->where('target_status', 1)
                    ->where('service', '!=', 'call_center');
            })
            ->with('order');

        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $query = $query->whereIn('o.geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $query = $query->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $query = $query->whereIn('o.subproject_id', $subProject);
        }
        if ($filter['proc_status']) {
            $procStatuses = explode(',', $filter['proc_status']);
            $query = $query->whereIn('o.proc_status', $procStatuses);
        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $query = $query->addSelect(
                \DB::raw('count(CASE WHEN (o.proc_status = 1 AND collector_logs.created_at between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countProcessing'),
                \DB::raw('count(CASE WHEN (o.proc_status = 20 AND o.time_paid_up between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countPaidUp'),
                \DB::raw('count(CASE WHEN (o.proc_status = 21 AND o.time_refused between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countRefused'),
                \DB::raw('count(CASE WHEN (o.proc_status = 24 AND o.time_sent between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countSent'),
                \DB::raw('count(CASE WHEN (o.proc_status = 25 AND o.time_at_department between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countAtDepartment'),
                \DB::raw('count(CASE WHEN (o.proc_status = 26 AND o.time_received between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countReceived'),
                \DB::raw('count(CASE WHEN (o.proc_status = 27 AND o.time_returned between "' . Carbon::parse($filter['date_start']) . '" and "' . Carbon::parse($filter['date_end'])->endOfDay() . '") THEN 1 END) AS countReturned')
            );

            $query = $query->whereBetween('collector_logs.updated_at', [
                Carbon::parse($filter['date_start']),
                Carbon::parse($filter['date_end'])->endOfDay()
            ]);
        }

        $resultByStatus = $query
            ->groupBy('o.proc_status')
            ->get()->keyBy('proc_status');

        $resultDataByStatuses = [];

        $resultDataByStatuses['allCounts'] = [];

        foreach ($resultByStatus as $key => $resultByStatus) {
            $procStatus = ProcStatus::find($key);
//            if ($procStatus && in_array($procStatus->action, ['sent', 'at_department'])) {
//                if($resultByStatus->countSent && $resultByStatus->countSent != 0){
//                    $resultDataByStatuses['sent'][] = $resultByStatus->countSent;
//                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countSent;
//                }elseif ($resultByStatus->countAtDepartment && $resultByStatus->countAtDepartment != 0){
//                    $resultDataByStatuses['sent'][] =$resultByStatus->countAtDepartment;
//                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countAtDepartment;
//                }else{
//                    $resultDataByStatuses['sent'][]=$resultByStatus->countOrders;
//                    $resultDataByStatuses['allCounts'][] =$resultByStatus->countOrders;
//                }
//            }

            if ($procStatus && in_array($procStatus->action, ['paid_up', 'received'])) {
                if ($resultByStatus->countPaidUp && $resultByStatus->countPaidUp != 0) {
                    $resultDataByStatuses['received'][] = $resultByStatus->countPaidUp;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countPaidUp;
                } elseif ($resultByStatus->countAtDepartment && $resultByStatus->countAtDepartment != 0) {
                    $resultDataByStatuses['received'][] = $resultByStatus->countAtDepartment;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countAtDepartment;
                } else {
                    $resultDataByStatuses['received'][] = $resultByStatus->countOrders;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countOrders;
                }
            }

            if ($procStatus && in_array($procStatus->action, ['refused', 'returned'])) {
                if ($resultByStatus->countRefused && $resultByStatus->countRefused != 0) {
                    $resultDataByStatuses['returned'][] = $resultByStatus->countRefused;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countRefused;
                } elseif ($resultByStatus->countReturned && $resultByStatus->countReturned != 0) {
                    $resultDataByStatuses['returned'][] = $resultByStatus->countReturned;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countReturned;
                } else {
                    $resultDataByStatuses['returned'][] = $resultByStatus->countOrders;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countOrders;
                }
            }
            if ($key == 1) {
                if ($resultByStatus->countProcessing && $resultByStatus->countProcessing != 0) {
                    $resultDataByStatuses['processing'][] = $resultByStatus->countProcessing;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countProcessing;
                } else {
                    $resultDataByStatuses['processing'][] = $resultByStatus->countOrders;
                    $resultDataByStatuses['allCounts'][] = $resultByStatus->countOrders;
                }
            }
        }
        $sentAtddepartmentOrders = Order::where('moderation_id', '>', 0)
            ->where('target_status', 1)
            ->where('service', '!=', 'call_center');

        if (!$filter['date_start']) {
            $sentAtddepartmentOrders = $sentAtddepartmentOrders->whereHas('procStatus',
                function ($query) {
                    $query->whereIn('action', ['sent', 'at_department']);
                });
        }

        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $sentAtddepartmentOrders = $sentAtddepartmentOrders->whereIn('geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $sentAtddepartmentOrders = $sentAtddepartmentOrders->whereIn('project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $sentAtddepartmentOrders = $sentAtddepartmentOrders->whereIn('subproject_id', $subProject);
        }
        if ($filter['proc_status']) {
            $procStatuses = explode(',', $filter['proc_status']);
            $sentAtddepartmentOrders = $sentAtddepartmentOrders->whereIn('proc_status', $procStatuses);
        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $sentAtddepartmentOrders = $sentAtddepartmentOrders->where(function ($queryByStatus) use ($filter) {
                $queryByStatus->whereBetween('time_sent', [
                    Carbon::parse($filter['date_start']),
                    Carbon::parse($filter['date_end'])->endOfDay()
                ])->orWhereBetween('time_at_department', [
                    Carbon::parse($filter['date_start']),
                    Carbon::parse($filter['date_end'])->endOfDay()
                ]);
            });

        }

        $sentAtddepartmentOrders = $sentAtddepartmentOrders->get();
        $resultDataByStatuses['sent'][] = !empty($sentAtddepartmentOrders) ? $sentAtddepartmentOrders->count() : 0;

        $resultAll = [];
        $resultAll['resultByUsers'] = $resultByUsers;
        $resultAll['resultData'] = $resultData;
        $resultAll['resultDataByStatuses'] = $resultDataByStatuses;

        return $resultAll;
    }
}
