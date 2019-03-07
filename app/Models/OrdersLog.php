<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersLog extends Model
{
    protected $table = 'orders_log';
    public $timestamps = false;
    public $fillable = ['order_id', 'user_id', 'text', 'date', 'status_id', 'status_name'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

   public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function operation()
    {
        return $this->hasOne(Operation::class);
    }

    function addOrderLog($orderId, $text, $status = [])
    {
        return DB::table($this->table)->insertGetId([
            'order_id'    => $orderId,
            'user_id'     => auth()->user()->id ?? 0,
            'text'        => $text,
            'date'        => now(),
            'status_id'   => isset($status['status_id']) ? $status['status_id'] : 0,
            'status_name' => isset($status['status_name']) ? $status['status_name'] : NULL,
            'ip_address'  => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 0 ,
            'user_agent'  => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 0
        ]);
    }

    function getOrderLogById($orderId)
    {
        return DB::table($this->table . ' AS ol')
            ->select('ol.text', 'ol.user_id', 'u.name', 'u.surname', 'ol.date','ol.status_id', 'ol.status_name', 'c.name AS company')
            ->leftJoin('users AS u', 'ol.user_id', '=', 'u.id')
            ->leftJoin('companies AS c', 'c.id', '=', 'u.company_id')
            ->where('ol.order_id', $orderId)
            ->get();
    }

    public static function changeProject(Order $before, Order $after)
    {
        $log = '';
        $properties = [
            'partner_id'    => [
                'relation' => 'partner',
                'title'    => 'Партнер',
            ],
            'project_id'    => [
                'relation' => 'project',
                'title'    => 'Проект',
            ],
            'subproject_id' => [
                'relation' => 'subProject',
                'title'    => 'Под проект',
            ],
            'offer_id'      => [
                'relation' => 'offer',
                'title'    => 'Оффер',
            ],
            'proc_status'   => [
                'relation' => 'procStatus',
                'title'    => 'Статус',
            ],
        ];

        foreach ($properties as $property => $values) {
            if ($before->$property != $after->$property) {
                $relation = $values['relation'];
                $log .= $values['title'] . ' был изменен';
                $beforeValue = $before->$relation ? $before->$relation->name : $before->$property;
                $log .= $beforeValue ? ' с "' . $beforeValue . '"' : '';
                $afterValue = $after->$relation ? $after->$relation->name : $after->$property;
                $log .= $afterValue ? ' на "' . $afterValue . '"' : '';
                if($after->proc_status == 6){
                    $log .= 'Установлен процессинг статус Некорректный номер';
                }
                $log .= "<br>";
            }
        }


        $res = false;
        $newProcStatus = !empty( $after->proc_status) ? ProcStatus::find($after->proc_status) : 0;
        if ($log) {
            $res = self::create([
                'order_id' => $after->id,
                'user_id'  => auth()->user()->id ?? 0,
                'text'     => $log,
                'status_id' => !empty($after->proc_status) ? $after->proc_status : 0,
                'status_name' => !empty($newProcStatus) ? $newProcStatus->name : NULL,
                'date'     => now(),
            ]);
        }

        return $res;
    }

    public static function addOrdersLog($orderIds, $text)
    {
        $logs = [];

        if ($orderIds) {
            foreach ($orderIds as $orderId) {
                $logs[] = [
                    'order_id' => $orderId,
                    'user_id'  => Auth::user()->id ?? 0,
                    'text'     => $text,
                    'date'     => now(),
                ];
            }
        }

        return $logs ? OrdersLog::insert($logs) : false;
    }

    public static function addLogForChangeCampaign(Collection $orders, Campaign $campaign)
    {
        if ($orders->isNotEmpty()) {
            $campaignOrders = Campaign::whereIn('id', $orders->keyBy('proc_campaign')->pluck('proc_campaign'))->get()->keyBy('id');
            $logs = [];
            foreach ($orders as $order) {
                if ($order->proc_campaign != $campaign->id) {
                    $text = 'Очередь была изменена ';
                    if (isset($campaignOrders[$order->proc_campaign])) {
                        $text .= 'c "' . $campaignOrders[$order->proc_campaign]->name . '" ';
                    }
                    $text .= 'на "' . $campaign->name . '"';
                    $logs[] = [
                        'order_id' => $order->id,
                        'user_id'  => Auth::user()->id ?? 0,
                        'text'     => $text
                    ];
                }
            }

            return OrdersLog::insert($logs);
        }

        return false;
    }
}
