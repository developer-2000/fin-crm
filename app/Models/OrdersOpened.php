<?php

namespace App\Models;

use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Facades\DB;
use \App\Models\User;
use Illuminate\Support\Facades\Input;

class OrdersOpened extends BaseModel
{
    protected $table = 'orders_opened';

    protected $fillable = [
        'order_id',
        'user_id',
        'proc_status',
        'callback',
        'target_status',
        'target_status_type',
        'target_status_time',
        'moderation_id',
        'moderation_time',
        'target',
        'callback_status',
        'verified_uid',
        'unique_id',
        'status',
        'learning',
        'date_opening',
        'date_closed'
    ];
    public $timestamps = false;

    public function add($data)
    {
        DB::table($this->table)->insert($data);
    }

    /*get order for order_opened*/
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /*get user for order_opened*/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*get user for order_opened*/
    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function addFinal($id, $data)
    {
        $data['date_closed'] = now();
        $data['result_status'] = 1;
        if (isset($data['callback_status']) && $data['callback_status'] == 3) {
            $data['result_status'] = 0;
        }

        DB::table($this->table)->where('order_id', $id)
            ->where('user_id', auth()->user()->id)
            ->whereNull('date_closed')
            ->orderBy('id', 'desc')
            ->update($data);
    }

    public function getOrderWithBadConnection($filter)
    {
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $countOnePage = 100;
        $orders = DB::table($this->table . ' AS oo')
            ->select(
                'oo.id',
                'oo.order_id',
                'oo.user_id',
                'oo.callback_status',
                'oo.verified_uid',
                'cpl.file',
                'cpl.date',
                'u.name',
                'u.surname',
                'u.company_id'
            )
            ->leftjoin('call_progress_log AS cpl', 'cpl.unique_id', '=', 'oo.unique_id')
            ->leftJoin('users AS u', 'oo.user_id', '=', 'u.id')
            ->where('oo.unique_id', '>', 0)
            ->where('cpl.entity', 'order')
            ->where('cpl.user_id', DB::raw('oo.user_id'))
            ->where('cpl.order_id', DB::raw('oo.order_id'))
            ->whereBetween('oo.date_opening', [$filter['date_start'], $filter['date_end']]);
        if (auth()->user()->company_id) {
            $orders = $orders->where('u.company_id', auth()->user()->company_id);
        }
        if ($filter['company']) {
            $companies = explode(',', $filter['company']);
            $orders = $orders->whereIn('u.company_id', $companies);
        }
        if ($filter['id']) {
            $orders = $orders->where('oo.id', $filter['id']);
        }
        if ($filter['oid']) {
            $orders = $orders->where('oo.order_id', $filter['oid']);
        }
        if ($filter['cause']) {
            $orders = $orders->where('oo.callback_status', $filter['cause']);
        } else {
            $orders = $orders->whereIn('oo.callback_status', [1, 2, 4]);
        }
        if ($filter['status']) {
            if ($filter['status'] == 2) {
                $orders = $orders->where('oo.verified_uid', 0);
            }
            if ($filter['status'] == 1) {
                $orders = $orders->where('oo.verified_uid', '>', 0);
            }

        }
        if ($filter['user']) {
            $users = explode(',', $filter['user']);
            $orders = $orders->whereIn('oo.user_id', $users);
        }

        $result = $orders->groupBy('oo.id')
            ->orderBy('oo.date_opening', 'desc')
            ->paginate($countOnePage);

        return [
            'orders' => $result->appends(Input::except('page')),
            'count'  => $result->total(),
        ];
    }

    public function setVerifiedUid($id, $data = [])
    {
        $data['verified_uid'] = auth()->user()->id;
        return DB::table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Получаем все заказы
     * @param string $page Страница заказа
     * @param array $filter Фильтр заказа
     * @return array
     */
    function getOrdersOpened($filter)
    {
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();

        $result = OrdersOpened::select('*')->with('order', 'user', 'user.company');

        if ($filter['id']) {
            $result->where('id', $filter['id']);
        }
        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result->whereHas('order', function ($query) use ($country) {
                $query->whereIn('geo', $country);
            });
        }
        if ($filter['company']) {
            $company = explode(',', $filter['company']);
            $result->whereHas('user', function ($query) use ($company) {
                $query->whereIn('company_id', $company);
            });
        }

        if ($filter['oid']) {
            $oID = $filter['oid'];
            $result->where('order_id', $oID);
        }
        if ($filter['user']) {
            $user = explode(',', $filter['user']);
            $result->whereIn('user_id', $user);
        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $result->whereBetween('date_opening', [$filter['date_start'], $filter['date_end']]);
        }

        if ($filter['status']) {
            $statuses = explode(',', $filter['status']);
            $result->whereIn('callback', $statuses);
        }

        if ($filter['target']) {
            $targets = explode(',', $filter['target']);
            if (count($targets) == 1 && $targets[0] == 10) {

            } elseif (count($targets) > 1) {
                if (in_array(10, $targets)) {
                    foreach (array_keys($targets, "10", true) as $key) {
                        unset($targets[$key]);
                    }

                    $result->where(function ($q) use ($targets) {
                        $q->whereIn('target_status', $targets)
                            ->orWhere(function ($query) {
                                $query->where('target_status', 0)->where('callback', 0);
                            });
                    });
                }
            } else {
                $result->whereIn('target_status', $targets);
            }
        }

        if ($filter['operator_assigned']) {
            $result->whereHas(
                'order', function ($query) {
                $query->whereRaw('target_user = orders_opened.user_id');
            })->where('target_status', 1);
        }

        $result = $result
            ->orderBy('id', 'desc')
            ->paginate(100);
        return $result;
    }

    public function updateAllOrderData($request, $ordersOpenedData, $procStatusId, $operatorOrderOpened, $targetType)
    {
        $ordersOpenedData['proc_status'] = $procStatusId;

        $ordersOpenedData['callback'] = !empty($request->get('proc_status')) ? $request->get('proc_status') : NULL;

        $ordersOpenedData['target_status'] = !empty($request->get('target_status')) ? $request->get('target_status') : NULL;
        $ordersOpenedData['target_status_time'] = !empty($request->get('target_status')) ? now() : NULL;

        $ordersOpenedData['target_status_type'] = $request->get($targetType)['cause'] ?? NULL;

        if (!empty($operatorOrderOpened->id)) {
            $orderOpened = OrdersOpened::where('id', $operatorOrderOpened->id)
                ->whereNull('date_closed')
                ->where('user_id', auth()->user()->id)
                ->orderBy('id', 'desc')->first();
            if ($orderOpened) {
                $orderOpened->update($ordersOpenedData);
            }
        }
    }
}