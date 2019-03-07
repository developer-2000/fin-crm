<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class CollectorLog extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'processed',
        'user_id'
    ];

    const TYPE_AUTO = 'auto';
    const TYPE_HAND = 'hand';

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeProcessed($query)
    {
        return $query->where('processed', 1);
    }

    public function scopeNoProcessed($query)
    {
        return $query->where('processed', 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public static function saveProcessed($orderId, $userId)
    {
        $log = self::where([
            ['order_id', $orderId],
            ['processed', 0]
        ])->first();

        if ($log) {
            $log->executor_id = Auth::user()->id;
            $log->processed = 1;
        } else {
            $log = new self();
            $log->type = self::TYPE_HAND;
            $log->order_id = $orderId;
            $log->user_id = $userId;
            $log->executor_id = Auth::user()->id;
            $log->processed = 1;
        }

        return $log->save();
    }

    public static function addCollectorLogsAuto($orderIds)
    {
        $data = [];

        if ($orderIds) {
            foreach ($orderIds as $orderId) {
                $data[] = [
                    'type' => self::TYPE_AUTO,
                    'order_id' => $orderId,
                    'user_id'  => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        return $data ? self::insert($data) : false;
    }

    public static function updateOrCreateAutoLog($orderId)
    {
        return self::updateOrCreate([
            'order_id' => $orderId,
            'type' => self::TYPE_AUTO,
            'user_id' => 0,
            'processed' => 0,
        ], [
            'user_id' => Auth::user()->id
        ]);
    }

    public static function deleteLogs($ids)
    {
        return self::whereIn('order_id', $ids)
            ->noProcessed()
            ->delete();
    }

    public static function logsToday($filter, $all = false)
    {
        $query = self::with('order', 'user', 'order.procStatus')
            ->processed()
            ->whereBetween('updated_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]);

        if (!empty($filter['id'])) {
            $query->whereHas('order', function ($q) use ($filter) {
               $q->where('orders.id', $filter['id'])
                   ->where('id', '>', 0);
            });
        }
        if (!empty($filter['surname'])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $q->where('name_last', 'like', $filter['surname'] . '%');
            });
        }
        if (!empty($filter['phone'])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $q->where('phone', 'like', '%' . $filter['phone']);
            });
        }
        if (!empty($filter['oid'])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $q->where('partner_oid', $filter['oid'])
                    ->where('partner_oid', '>', 0);
            });
        }
        if (!empty($filter['country'])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $country = is_array($filter['country']) ? $filter['country'] : explode(',', $filter['country']);
                $q->whereIn('geo', $country);
            });

        }
        if (!empty($filter['status'])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $statuses = is_array($filter['status']) ? $filter['status'] : explode(',', $filter['status']);
                $q->whereIn('proc_status', $statuses);
            });

        }
        if (!empty($filter['project']) && !empty($filter['project'][0])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $project = explode(',', $filter['project']);
                $q->whereIn('orders.project_id', $project);
            });

        }
        if (!empty($filter['sub_project'])  && !empty($filter['project'][0])) {
            $query->whereHas('order', function ($q) use ($filter) {
                $subProject = explode(',', $filter['sub_project']);
                $q->whereIn('orders.subproject_id', $subProject);
            });
        }

        if ($all) {
            return $query->get();
        }

        return  $query->orderBy('updated_at', 'desc')->paginate(100)->appends(Input::except('page'));
    }
}
