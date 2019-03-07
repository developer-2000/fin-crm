<?php

namespace App\Repositories;

use App\Models\CollectorLog;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\OrderProduct;
use App\Models\ProcStatus;
use App\Models\TargetValue;
use Illuminate\Support\Facades\DB;

class CollectingRepository
{
    public static function filterCollectorOrders($result, $filter)
    {
        if (!empty($filter['id'])) {
            $result = $result->where('orders.id', $filter['id'])
                ->where('orders.id', '>', 0);
        }
        if (!empty($filter['surname'])) {
            $result->where('name_last', 'like', $filter['surname'] . '%');
        }
        if (!empty($filter['phone'])) {
            $result = $result->where('phone', 'like', '%' . $filter['phone']);
        }
        if (!empty($filter['oid'])) {
            $result = $result->where('partner_oid', $filter['oid'])
                ->where('partner_oid', '>', 0);
        }
        if (!empty($filter['country'])) {
            $country = is_array($filter['country']) ? $filter['country'] : explode(',', $filter['country']);
            $result = $result->whereIn('geo', $country);
        }
        if (!empty($filter['status'])) {
            $statuses = is_array($filter['status']) ? $filter['status'] : explode(',', $filter['status']);
            $result = $result->whereIn('proc_status', $statuses);
        }
        if (!empty($filter['project']) && !empty($filter['project'][0])) {
            $project = [];
            if (is_array($filter['project'])) {
                $project = $filter['project'];
            } elseif (is_string($filter['project'])) {
                $project = explode(',', $filter['project']);
            }

            $result = $result->whereIn('orders.project_id', $project);
        }
        if (!empty($filter['sub_project']) && !empty($filter['sub_project'][0])) {
            $subProject = [];
            if (is_array($filter['sub_project'])) {
                $subProject = $filter['sub_project'];
            } elseif (is_string($filter['sub_project'])) {
                $subProject = explode(',', $filter['sub_project']);
            }
            $result = $result->whereIn('orders.subproject_id', $subProject);
        }
 
        if (is_numeric($filter['processing_count']) || $filter['processing_count'] == "no_processed") {
            $filter['processing_count'] = $filter['processing_count'] == "no_processed" ? "0" : $filter['processing_count'];

            $result = $result->havingRaw(DB::raw('(SELECT count(id) from collector_logs where collector_logs.order_id = orders.id) ='. $filter['processing_count']));
        }

        if (!empty($filter['date-type']) && !empty($filter['date_start']) && !empty($filter['date_end']) && ($filter['date_start'] <= $filter['date_end'])) {
            $filter['date_start'] = Carbon::parse($filter['date_start']);
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
            switch ($filter['date-type']) {
                case 1:
                    $date = 'time_created';
                    break;
                case 2:
                    $date = 'time_changed';
                    break;
                case 3:
                    $date = 'time_modified';
                    break;
            }
            if (isset($date)) {
                $result->whereBetween($date, [$filter['date_start'], $filter['date_end']]);
            }
        }
    }

    public static function addOrderToPbx($orders)
    {
        return (new Order)->addCollectorsCalls($orders);
    }
}