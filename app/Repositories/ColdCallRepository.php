<?php

namespace App\Repositories;

use App\Http\Controllers\ActionController;
use App\Models\ColdCallFile;
use App\Models\ColdCallList;
use App\Models\Order;
use App\Models\ProcStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ColdCallRepository
{
    public function all()
    {
        if (!auth()->user()->company_id) {
            $coldCalls = ColdCallFile::with('country', 'company', 'campaign', 'campaign.users')
            ->sort()->paginate(20);
        } else {
            $coldCalls = ColdCallFile::with('country', 'company', 'campaign', 'campaign.users')
                  ->where('company_id', auth()->user()->company_id)
                  ->sort()
                  ->paginate(20);
        }

        return $this->serialize($coldCalls);
    }

    private function countList($ccIds)
    {
        $lists = ColdCallList::select(
            DB::raw("count(id) as count_list"),
       'cold_call_file_id as ccfile_id'
        )
            ->whereIn('cold_call_file_id', $ccIds)
            ->groupBy('cold_call_file_id')
            ->get();

        return $lists;
    }

    private function countListProcess($ccIds)
    {
        $lists = ColdCallList::select(
          DB::raw("count(id) as count_list_process"),
          'cold_call_file_id as ccfile_id'
      )
          ->whereIn('cold_call_file_id', $ccIds)
          ->whereIn('proc_status', [3,4])
          ->groupBy('cold_call_file_id')
          ->get();

        return $lists;
    }

    public function serialize($coldCalls)
    {
        $ccIds = $coldCalls->pluck('id');
        $objList = $this->countList($ccIds);
        $objListProcess = $this->countListProcess($ccIds);

        foreach ($coldCalls as $coldCall) {
            $coldCall->countList = $objList->where('ccfile_id', $coldCall->id)
                              ->first()
                              ->count_list??null;
            $coldCall->countListProcess = $objListProcess->where('ccfile_id', $coldCall->id)
                              ->first()
                              ->count_list_process??null;
        }

        return $coldCalls;
    }

    public function moderationOrder($filter)
    {
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();

        $orders = DB::table('orders AS o')
            ->select('o.id', 'o.input_data', 'of.name AS offer', 'o.price_total AS price', 'o.host',
                'o.name_first AS name', 'o.name_last AS surname', 'o.name_middle AS middle', 'u.id as operator_id',
                'u.name AS operName', 'u.surname AS operSurname', 'o.time_created', 'o.time_modified', 'o.geo',
                'o.proc_status', 'o.target_status', 'o.repeat_id', 'o.phone', 'o.proc_campaign', 'o.target_cancel',
                'tc.options', 'u.company_id', 'comp.name as company_name', 'f.id as feedback_id',
                'f.created_at as feedback_created_at', 'o.project_id', 'o.subproject_id'
            )
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->leftJoin('users AS u', 'o.target_user', '=', 'u.id')
            ->leftJoin('target_configs AS tc', 'o.target_cancel', '=', 'tc.id')
            ->leftJoin('companies AS comp', 'comp.id', '=', 'u.company_id')
            ->leftJoin('feedback AS f', 'f.order_id', '=', 'o.id')
            ->where([['o.moderation_id', 0], ['o.entity', 'cold_call']])
            ->where('proc_status', '!=', 10)//подозрительный
            ->whereBetween('o.time_modified', [$filter['date_start'], $filter['date_end']]);
        if (auth()->user()->company_id) {
            $orders = $orders->where('u.company_id', auth()->user()->company_id);
        }

        if ($filter['country']) {
            $orders = $orders->where('o.geo', mb_strtolower($filter['country']));
        }
        if ($filter['project']) {
            $orders = $orders->where('o.project_id', $filter['project']);
        }
        if ($filter['sub_project']) {
            $orders = $orders->where('o.subproject_id', $filter['sub_project']);
        }
        if ($filter['offer']) {
            $orders = $orders->where('o.offer_id', $filter['offer']);
        }
        if ($filter['company']) {
            $orders = $orders->where('u.company_id', $filter['company']);
        }
        if ($filter['id']) {
            $orders = $orders->where('o.id', $filter['id']);
        }

        if ($filter['grouping']) {
            switch ($filter['grouping']) {
                case 'approve':
                    {
                        $orders = $orders->where('o.target_status', 1);
                        break;
                    }
                case 'failure':
                    {
                        $orders = $orders->where('o.target_status', 2);
                        break;
                    }
                case 'cancel':
                    {
                        $orders = $orders->where('o.target_status', 3);
                        break;
                    }
            }
            $count = $orders->count();
            $orders = $orders
                ->orderBy('o.id', 'desc')
                ->paginate(10);
        } else {
            $count = $orders->whereIn('target_status', [1, 2, 3])
                ->count();
            $orders = $orders->whereIn('o.target_status', [1, 2, 3])
                ->orderBy('o.id', 'desc')
                ->paginate(10);
        }
        $ids = [];
        if ($orders) {
            foreach ($orders as &$order) {
                $ids[] = $order->id;
                if ($order->target_status == 1) {
                    $records = DB::table('call_progress_log AS cpl')
                        ->select('cpl.file', 'u.name', 'u.surname')
                        ->leftJoin('users AS u', 'cpl.user_id', '=', 'u.id')
                        ->where('cpl.entity', 'cold_call')
                        ->where('cpl.order_id', $order->id)
                        ->get();
                    $order->records = $records;

                    $products = DB::table('order_products AS op')
                        ->select('p.title', 'op.price', 'op.type', 'op.comment', 'op.disabled', 'op.id')
                        ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
                        ->where('op.order_id', $order->id)
                        ->get();
                    $order->products = $products;

                } elseif ($order->target_status == 2) {
                    $records = DB::table('call_progress_log AS cpl')
                        ->select('cpl.file', 'u.name', 'u.surname')
                        ->leftJoin('users AS u', 'cpl.user_id', '=', 'u.id')
                        ->where('cpl.entity', 'cold_call')
                        ->where('cpl.order_id', $order->id)
                        ->get();
                    $order->records = $records;
                } elseif ($order->target_status == 3) {
                    $records = DB::table('call_progress_log AS cpl')
                        ->select('cpl.file', 'u.name', 'u.surname')
                        ->leftJoin('users AS u', 'cpl.user_id', '=', 'u.id')
                        ->where('cpl.entity', 'cold_call')
                        ->where('cpl.order_id', $order->id)
                        ->get();
                    $order->records = $records;
                }
            }
        }
        $targetValue = DB::table('target_values as tv')
            ->select('tv.values', 'tv.order_id', 'tc.alias', 'tc.name')
            ->leftJoin('target_configs as tc', 'tc.id', '=', 'tv.target_id')
            ->whereIn('tv.order_id', $ids)
            ->get();

        $causes = $this->getTargetValueForColdCallOrders($targetValue);

        return [$orders->appends(Input::except('page')), $count, $causes];
    }

    public function getTargetValueForColdCallOrders($targetValue, $ordersOld = [])
    {
        $result = [];
        if ($targetValue) {
            foreach ($targetValue as $value) {
                $functionName = 'getTitleValues' . mb_strtoupper(substr($value->alias, 0, 1)) . substr($value->alias, 1);
                if (function_exists($functionName)) {
                    $result[$value->order_id]['name'] = $value->name;
                    $result[$value->order_id] = $functionName(json_decode($value->values), $ordersOld[$value->order_id] ?? NULL);
                    continue;
                }
                if ($value->values) {
                    $fields = json_decode($value->values);
                    foreach ($fields as $field) {
                        if ($field->field_show_result && $field->field_value) {//нужно ли показывать результат этого поля на странице
                            $fieldValue = [];
                            if ($field->options) {
                                if (is_object($field->field_value)) {//для checkbox
                                    foreach ($field->field_value as $v) {
                                        if (isset($field->options->$v)) {
                                            $fieldValue[] = $field->options->$v;
                                        }
                                    }
                                } else {//для select,radio
                                    $val = $field->field_value;
                                    if (isset($field->options->$val)) {
                                        $fieldValue[] = $field->options->$val;
                                    }
                                }
                            } else {//для input, textarea
                                $fieldValue[] = $field->field_value;
                            }
                            $result[$value->order_id]['name'] = $value->name;
                            $result[$value->order_id][$field->field_name] = [
                                'title' => $field->field_title,
                                'value' => $fieldValue,
                            ];
                        }
                    }
                }
            }
        }
        return $result;
    }

    public static function getCountOrderModeration($filter)
    {
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $procStatus = DB::table('orders AS o')
            ->select('o.proc_status', DB::raw('COUNT(o.proc_status) AS count'))
            ->leftJoin('users AS u', 'u.id', '=', 'o.target_user')
            ->whereIn('o.proc_status', [4, 5, 6, 7, 11])
            ->where('o.target_status', 0)
            ->where('o.moderation_id', 0)
            ->where('o.entity', 'order')
            ->whereBetween('o.time_modified', [$filter['date_start'], $filter['date_end']]);
        $targetStatus = DB::table('orders AS o')
            ->select('o.target_status', DB::raw('COUNT(o.target_status) AS count'))
            ->leftJoin('users AS u', 'u.id', '=', 'o.target_user')
            ->whereIn('o.target_status', [1, 2, 3])
            ->where('o.project_id', '!=', 2)//todo костыль
            ->where('o.moderation_id', 0)
            ->where('o.entity', 'cold_call')
            ->whereBetween('o.time_modified', [$filter['date_start'], $filter['date_end']]);

        if (auth()->user()->company_id) {
            $procStatus = $procStatus->where('u.company_id', auth()->user()->company_id);
            $targetStatus = $targetStatus->where('u.company_id', auth()->user()->company_id);
        }

        if ($filter['country']) {
            $procStatus = $procStatus->where('o.geo', mb_strtolower($filter['country']));
            $targetStatus = $targetStatus->where('o.geo', mb_strtolower($filter['country']));
        }
        if ($filter['project']) {
            $procStatus = $procStatus->where('o.project_id', $filter['project']);
            $targetStatus = $targetStatus->where('o.project_id', $filter['project']);
        }
        if ($filter['offer']) {
            $procStatus = $procStatus->where('o.offer_id', $filter['offer']);
            $targetStatus = $targetStatus->where('o.offer_id', $filter['offer']);
        }
        if ($filter['company']) {
            $procStatus = $procStatus->where('u.company_id', $filter['company']);
            $targetStatus = $targetStatus->where('u.company_id', $filter['company']);
        }
        if ($filter['id']) {
            $procStatus = $procStatus->where('o.id', $filter['id']);
            $targetStatus = $targetStatus->where('o.id', $filter['id']);
        }

        $procStatus = $procStatus->groupBy('proc_status')
            ->get();
        $targetStatus = $targetStatus->groupBy('target_status')
            ->get();


        $result = [];

        if ($procStatus) {
            foreach ($procStatus AS $status) {
                $result[$status->proc_status] = $status->count;
            }
        }
        if ($targetStatus) {
            foreach ($targetStatus AS $status) {
                $result[$status->target_status] = $status->count;
            }
        }

        return $result;
    }

    public static function setColdCallsModeration(Order $order)
    {
        if ($order->target_status == 1 && !Order::checkProducts($order)) {
            return [
                'success' => false,
                'error'   => trans('alerts.product-out-stock'),
            ];
        }
        if($order->target_status == 1){
            $order->proc_status = 3;
        }
   
        $order->moderation_id = auth()->user()->id;
        $order->moderation_time = now();

        if($order->target_status == 1){
            $order->proc_status = 3 ;
            $procStatus = ProcStatus::find(3);
            ActionController::updateOrderProcStatus([$order->id], $procStatus);
        }

        return [
            'success' => $order->save(),
        ];
    }
}
