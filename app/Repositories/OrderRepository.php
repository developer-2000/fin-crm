<?php

namespace App\Repositories;

use App\Models\OrderProduct;
use App\Models\ProcStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public static function sumOrderProducts($id)
    {
        return OrderProduct::where('order_id', $id)->where('disabled', 0)->sum('price');
    }

    public static function getCounterpartiesData($filter)
    {
        if ($filter['proc_status']) {
            $statussesActionsArray = [];
            $allProcStatuses = ProcStatus::all()->keyBy('id')->toArray();
            $statussesArray = explode(',', $filter['proc_status']);

            foreach ($statussesArray as $item) {
                if (in_array($allProcStatuses[$item]['action'], [
                    'sent',
                    'at_department',
                    'received',
                    'returned',
                    'paid_up',
                    'refused'
                ])) {
                    $statussesActionsArray[] = $allProcStatuses[$item]['action'];
                }
            }
        }else{
            $statussesActionsArray = [
                'sent',
                'at_department',
                'received',
                'returned',
                'paid_up',
                'refused'
            ];
        }

        $reportData = DB::table("target_values")
            ->select(DB::raw('COUNT(*) as total'), 'target_values.target_id', 'sender_id', 'proc_status')
            ->leftJoin('orders', 'orders.id', '=', 'target_values.order_id')
            ->where('target_values.target_id', 1)
            ->where('orders.moderation_id', '>', 0)
            ->where('orders.target_status', 1)
            ->where('target_values.sender_id', '!=', 0)
            ->groupBy('target_values.target_id', 'sender_id', 'proc_status');
        if (\auth()->user()->project_id) {
            $reportData->where('project_id', \auth()->user()->project_id);
        }
        if (\auth()->user()->subproject_id) {
            $reportData->where('subproject_id',\auth()->user()->subproject_id);
        }
        if ($filter['date_start'] && $filter['date_end']) {
            $filter['date_start'] = Carbon::parse($filter['date_start'] . ' 00:00:00');
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
            $reportData->where(
                function ($q) use ($filter, $statussesActionsArray) {
                    $q->whereBetween('orders.time_' . $statussesActionsArray[0], [
                        $filter['date_start'],
                        $filter['date_end']
                    ]);
                    if (count($statussesActionsArray) > 1) {
                        unset($statussesActionsArray[0]);
                        foreach ($statussesActionsArray as $value) {
                            $q->orWhereBetween('orders.time_' . $value, [
                                $filter['date_start'],
                                $filter['date_end']
                            ]);
                        }
                    }
                }
            );
        }


        $reportData = $reportData->get()->groupBy('sender_id');
        return $reportData;
    }
}