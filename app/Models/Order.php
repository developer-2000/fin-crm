<?php

namespace App\Models;

use App\Http\Controllers\ActionController;
use App\Http\Requests\Request;
use App\Repositories\CollectingRepository;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PhoneCorrection\PhoneCorrectionService;
use Illuminate\Support\Facades\Input;
use \Log;
use Psy\Input\CodeArgument;

class Order extends BaseModel
{
    protected $table = 'orders';

    public $timestamps = FALSE;

    protected $fillable = ['call_status'];

    const SERVICE_CALL_CENTER = 'call_center';
    const SERVICE_SENDING = 'sending';
    const SERVICE_ALL = 'all';

    const ENTITY_ORDER = 'order';
    const ENTITY_COLD_CALL = 'cold_call';

    /*get order_opened for order*/
    public function orderOpened()
    {
        return $this->hasMany(OrdersOpened::class);
    }

    /*get campaign for order*/
    public function campaign()
    {
        return $this->belongsTo('App\Models\Campaign');
    }

    /*get feedback*/
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    /*get feedback*/
    public function storageTransactions()
    {
        return $this->hasMany(StorageTransaction::class);
    }

    /*get offer*/
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function getTargetApprove()
    {
        return $this->belongsTo(TargetConfig::class, 'target_approve');
    }

    public function getTargetCancel()
    {
        return $this->belongsTo(TargetConfig::class, 'target_cancel');
    }

    public function getTargetRefuse()
    {
        return $this->belongsTo(TargetConfig::class, 'target_refuse');
    }

    public function getTargetValue()
    {
        return $this->hasOne(TargetValue::class);
    }

    /*get list for order*/
    public function coldCallList()
    {
        return $this->hasOne('App\Models\ColdCallList');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user');
    }

    public function subProject()
    {
        return $this->belongsTo(Project::class, 'subproject_id');
    }

    public function procStatus()
    {
        return $this->belongsTo(ProcStatus::class, 'proc_status');
    }

    public function procStatus2()
    {
        return $this->belongsTo(ProcStatus::class, 'proc_status_2');
    }

    public function orderProducts()
    {

        return $this->hasMany(OrderProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->withPivot('id', 'price', 'type', 'disabled', 'comment');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'geo', 'code');
    }

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }

    public function orderPass()
    {
        return $this->hasMany(OrdersPass::class);
    }

    public function passPrint()
    {
        return $this->belongsTo(Pass::class, 'print_id', 'id');
    }

    public function passSend()
    {
        return $this->belongsTo(Pass::class, 'pass_send_id');
    }

    public function collectorLogs()
    {
        return $this->hasMany(CollectorLog::class);
    }

    public function logs()
    {
        return $this->hasMany(OrdersLog::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function scopeServiceNotCallCenter($query)
    {
        return $query->where('service', '!=', self::SERVICE_CALL_CENTER);
    }

    public function scopeModerated($query)
    {
        return $query->where('moderation_id', '>', 0);
    }

    public function scopeCheckAuth($query)
    {
        if (Auth::user()->project_id) {
            $query->where('project_id', Auth::user()->project_id);
        }

        if (Auth::user()->sub_project_id) {
            $query->where('subproject_id', Auth::user()->sub_project_id);
        }

        return $query;
    }

    public function scopeTargetApprove($query)
    {
        return $query->where('target_status', 1);
    }

    public function scopeWithoutTargetFinal($query)
    {
        return $query->where('final_target', 0);
    }

    public function getAuditAll($filter)
    {
        if ($filter['offer']) {
            $filter['offer'] = explode(',', $filter['offer']);
        }
        $filter['date_type'] = $filter['date_type'] ? 'time_modified' : 'time_created';
        if (!$filter['date_start'] || !$filter['date_end']) {
            $filter['date_start'] = Carbon::parse('now 00:00:00');
            $filter['date_end'] = Carbon::parse('now 23:59:59');
        } else {
            $filter['date_start'] = Carbon::parse($filter['date_start'] . ' 00:00:00');
            $filter['date_end'] = Carbon::parse($filter['date_end'] . ' 23:59:59');
        }

        $orders = [];
        if ($filter['group'] == 'date' || !$filter['group']) {
            $orders = $this->getAuditAllForGroupDate($filter);
        }

        if ($filter['group'] == 'user') {
            $orders = $this->getAuditAllForGroupUser($filter);
        }

        if ($filter['group'] == 'offer') {
            $orders = $this->getAuditAllForGroupOffer($filter);
        }

        if ($filter['group'] == 'source') {
            $orders = $this->getAuditAllForGroupSource($filter);
        }

        if ($filter['group'] == 'country') {
            $orders = $this->getAuditAllForGroupCountry($filter);
        }

        if ($filter['group'] == 'approveOffer') {
            $orders = $this->getAuditAllForGroupApproveOffer($filter);
        }

        return $orders;
    }

    private function getAuditAllForGroupDate($filter)
    {
        $orders = DB::table($this->table)->select(
            'target_status',
            DB::raw('date(' . $filter['date_type'] . ') AS date'),
            DB::raw('COUNT(target_status) AS count')
        )->where($filter['date_type'], '>=', $filter['date_start'])
            ->where($filter['date_type'], '<=', $filter['date_end'])
            ->where('service', '!=', self::SERVICE_SENDING);

        $types = DB::table('order_products AS op')
            ->select('op.type', DB::raw('COUNT(op.type) AS count'), DB::raw('date(FROM_UNIXTIME(o.' . $filter['date_type'] . ')) as date'))
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->whereIn('type', [1, 2, 4, 5])
            ->where('o.service', '!=', self::SERVICE_SENDING);


        $time = DB::table('call_progress_log AS cpl')->select(
            DB::raw('SUM(cpl.talk_time) AS time'),
            DB::raw('date(date) as date')
        )
            ->leftJoin('orders AS o', 'o.id', '=', 'cpl.order_id')
            ->where('cpl.entity', 'order')
            ->where('cpl.date', '>=', $filter['date_start'])
            ->where('cpl.date', '<=', $filter['date_end'])
            ->where('o.service', '!=', self::SERVICE_SENDING);

        $reportTime = collect(
            DB::table('report_time')->select(
                DB::raw('date(date) as date'),
                DB::raw('SUM(login_time_elastix) AS login_time_elastix'),
                DB::raw('SUM(login_time_crm) AS login_time_crm'),
                DB::raw('SUM(pause_time) AS pause_time'),
                DB::raw('SUM(order_time) AS order_time')
            )
                ->where('date', '>=', $filter['date_start'])
                ->where('date', '<=', $filter['date_end'])
                ->groupBy(DB::raw('date(date)'))
                ->get()
        )->keyBy('date');

        $newTimes = DB::table('users_time AS ut')
            ->select(
                'ut.type',
                DB::raw('SUM(ut.duration) as sum'),
                DB::raw('date(datetime_start) as date')
            )
            ->leftJoin('users as u', 'u.id', '=', 'ut.user_id')
            ->where('ut.datetime_start', '>=', $filter['date_start'])
            ->where('ut.datetime_start', '<=', $filter['date_end']);
        if (auth()->user()->company_id) {
            $newTimes = $newTimes->where('u.company_id', auth()->user()->company_id);
        }
        if ($filter['company']) {
            $newTimes = $newTimes->where('u.company_id', $filter['company']);
        }
        $newTimes = $newTimes->groupBy(DB::raw('date(datetime_start)'))
            ->groupBy('ut.type')
            ->get();

        $newDateTime = [];
        if ($newTimes) {
            foreach ($newTimes as $ntime) {
                if (!isset($newDateTime[$ntime->date])) {
                    $newDateTime[$ntime->date] = [
                        'crm' => 0,
                        'pbx' => 0
                    ];
                }
                $newDateTime[$ntime->date][$ntime->type] = $ntime->sum;
            }
        }

        if ($filter['date_start'] && $filter['date_end']) {
            $types = $types->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
        }

        if ($filter['project']) {
            $orders = $orders->where('project_id', $filter['project']);
            $types = $types->where('o.project_id', $filter['project']);
            $time = $time->where('o.project_id', $filter['project']);
        }

        if ($filter['display_hp']) {
            $orders = $orders->where('entity', 'cold_call');
            $types = $types->where('o.entity', 'cold_call');
            $time = $time->where('o.entity', 'cold_call');
        }

        if ($filter['offer']) {
            $orders = $orders->whereIn('offer_id', $filter['offer']);
            $types = $types->whereIn('o.offer_id', $filter['offer']);
            $time = $time->whereIn('o.offer_id', $filter['offer']);
        }
        if ($filter['learning']) {
            $status = $filter['learning'] == 2 ? 0 : 1;
            $orders = $orders->where('target_learning', $status);
            $types = $types->where('o.target_learning', $status);
            $time = $time->where('o.target_learning', $status);
        }

        if ($filter['user']) {
            $orders = $orders->where('target_user', $filter['user']);
            $types = $types->where('o.target_user', $filter['user']);
            $time = $time->where('cpl.user_id', $filter['user']);
        }

        if ($filter['company'] || auth()->user()->company_id) {
            $companyId = $filter['company'];
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
            }
            $orders = $orders->leftJoin('users', 'users.id', '=', 'orders.target_user')
                ->where('users.company_id', $companyId);
            $types = $types->leftJoin('users', 'users.id', '=', 'o.target_user')->where('users.company_id', $companyId);
            $time = $time->leftJoin('users', 'users.id', '=', 'cpl.user_id')->where('users.company_id', $companyId);
        }

        if ($filter['source']) {
            $orders = $orders->where('tag_source', $filter['source']);
            $types = $types->where('o.tag_source', $filter['source']);
            $time = $time->where('o.tag_source', $filter['source']);
        }

        if ($filter['country']) {
            $orders = $orders->where('geo', $filter['country']);
            $types = $types->where('o.geo', $filter['country']);
            $time = $time->where('o.geo', $filter['country']);
        }

        if ($filter['medium']) {
            $orders = $orders->where('tag_medium', $filter['medium']);
            $types = $types->where('o.tag_medium', $filter['medium']);
            $time = $time->where('o.tag_medium', $filter['medium']);
        }

        if ($filter['content']) {
            $orders = $orders->where('tag_content', $filter['content']);
            $types = $types->where('o.tag_content', $filter['content']);
            $time = $time->where('o.tag_content', $filter['content']);
        }

        if ($filter['campaign']) {
            $orders = $orders->where('tag_campaign', $filter['campaign']);
            $types = $types->where('o.tag_campaign', $filter['campaign']);
            $time = $time->where('o.tag_campaign', $filter['campaign']);
        }

        if ($filter['term']) {
            $orders = $orders->where('tag_term', $filter['term']);
            $types = $types->where('o.tag_term', $filter['term']);
            $time = $time->where('o.tag_term', $filter['term']);
        }

        $orders = $orders->groupBy('date')
            ->groupBy('target_status')
            ->get();
        $types = $types->groupBy('date')
            ->groupBy('op.type')
            ->get();
        $time = collect($time->groupBy('date')
            ->get())->keyBy('date');
        $data = [];
        if ($orders) {
            foreach ($orders as $order) {
                if (isset($data[$order->date])) {
                    if ($order->target_status == 1) {
                        $data[$order->date]['approve'] = $order->count;
                    } elseif ($order->target_status == 2) {
                        $data[$order->date]['failure'] = $order->count;
                    } elseif ($order->target_status == 3) {
                        $data[$order->date]['fake'] = $order->count;
                    }
                    if ($filter['date_type'] == 'time_created') {
                        if (!$order->target_status || $order->target_status == 4) {
                            $data[$order->date]['processing'] = $order->count;
                        }
                    }
                } else {
                    $data[$order->date] = [
                        'date'               => $order->date,
                        'approve'            => 0,
                        'failure'            => 0,
                        'fake'               => 0,
                        'processing'         => 0,
                        'up_sell'            => 0,
                        'up_sell_2'          => 0,
                        'cross_sell'         => 0,
                        'cross_sell_2'       => 0,
                        'login_time_elastix' => 0,
                        'login_time_crm'     => 0,
                        'talk_time'          => 0,
                        'pause_time'         => 0,
                        'order_time'         => 0,

                    ];
                    if ($order->target_status == 1) {
                        $data[$order->date]['approve'] = $order->count;
                    } elseif ($order->target_status == 2) {
                        $data[$order->date]['failure'] = $order->count;
                    } elseif ($order->target_status == 3) {
                        $data[$order->date]['fake'] = $order->count;
                    }
                    if ($filter['date_type'] == 'time_created') {
                        if (!$order->target_status || $order->target_status == 4) {
                            $data[$order->date]['processing'] = $order->count;
                        }
                    }
                }
                if (isset($time[$order->date])) {
                    $data[$order->date]['talk_time'] = $time[$order->date]->time;
                }
                if (isset($reportTime[$order->date])) {
                    $data[$order->date]['pause_time'] = $reportTime[$order->date]->pause_time;
                    $data[$order->date]['order_time'] = $reportTime[$order->date]->order_time;
                }

                if (isset($newDateTime[$order->date])) {
                    $data[$order->date]['login_time_elastix'] = $newDateTime[$order->date]['pbx'];
                    $data[$order->date]['login_time_crm'] = $newDateTime[$order->date]['crm'];
                }
            }
            if ($types) {
                foreach ($types as $type) {
                    if (isset($data[$type->date])) {
                        if ($type->type == 1) {
                            $data[$type->date]['up_sell'] = $type->count;
                        }
                        if ($type->type == 2) {
                            $data[$type->date]['up_sell_2'] = $type->count;
                        }
                        if ($type->type == 4) {
                            $data[$type->date]['cross_sell'] = $type->count;
                        }
                        if ($type->type == 5) {
                            $data[$type->date]['cross_sell_2'] = $type->count;
                        }
                    }
                }
            }
            return $data;
        }
    }

    private function getAuditAllForGroupUser($filter)
    {
        $orders = DB::table($this->table)
            ->select('target_user', 'target_status', DB::raw('COUNT(target_status) AS count'))
            ->where($filter['date_type'], '>=', $filter['date_start'])
            ->where($filter['date_type'], '<=', $filter['date_end'])
            ->where('target_user', '!=', 0)
            ->where('target_status', '!=', 0)
            ->where('service', '!=', self::SERVICE_SENDING);

        $types = DB::table('order_products AS op')
            ->select('op.type', DB::raw('COUNT(op.type) AS count'), 'o.target_user')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->whereIn('type', [1, 2, 4, 5]);
        $time = DB::table('call_progress_log AS cpl')->select(
            DB::raw('SUM(cpl.talk_time) AS time'),
            'cpl.user_id'
        )
            ->leftJoin('orders AS o', 'o.id', '=', 'cpl.order_id')
            ->where('cpl.entity', 'order')
            ->where('cpl.date', '>=', $filter['date_start'])
            ->where('cpl.date', '<=', $filter['date_end']);

        $opened = DB::table('orders_opened AS oo')->select(
            'oo.user_id',
            'oo.callback_status',
            DB::raw('COUNT(oo.id) AS count')
        )
            ->leftJoin('orders AS o', 'o.id', '=', 'oo.order_id')
            ->where('oo.date_opening', '>=', $filter['date_start'])
            ->where('oo.date_opening', '<=', $filter['date_end']);

        $newTimes = DB::table('users_time')
            ->select(
                DB::raw('SUM(duration) AS sum'),
                'type',
                'user_id'
            )
            ->whereBetween('datetime_start', [$filter['date_start'], $filter['date_end']])
            ->groupBy('user_id')
            ->groupBy('type')
            ->get();


        if ($filter['date_start'] && $filter['date_end']) {
            $types = $types->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
        }

        if ($filter['project']) {
            $orders = $orders->where('orders.project_id', $filter['project']);
            $types = $types->where('o.project_id', $filter['project']);
            $time = $time->where('o.project_id', $filter['project']);
            $opened = $opened->where('o.project_id', $filter['project']);
        }

        if ($filter['learning']) {
            $status = $filter['learning'] == 2 ? 0 : 1;
            $orders = $orders->where('target_learning', $status);
            $types = $types->where('o.target_learning', $status);
            $time = $time->where('o.target_learning', $status);
            $opened = $opened->where('o.target_learning', $status);
        }

        if ($filter['offer']) {
            $orders = $orders->whereIn('offer_id', $filter['offer']);
            $types = $types->whereIn('o.offer_id', $filter['offer']);
            $time = $time->whereIn('o.offer_id', $filter['offer']);
            $opened = $opened->whereIn('o.offer_id', $filter['offer']);
        }
        if ($filter['display_hp']) {
            $orders = $orders->where('entity', 'cold_call');
            $types = $types->where('o.entity', 'cold_call');
            $time = $time->where('o.entity', 'cold_call');
            $opened = $opened->where('o.entity', 'cold_call');
        }

        if ($filter['user']) {
            $orders = $orders->where('target_user', $filter['user']);
            $types = $types->where('o.target_user', $filter['user']);
            $time = $time->where('cpl.user_id', $filter['user']);
            $opened = $opened->where('oo.user_id', $filter['user']);
        }

        if ($filter['company'] || auth()->user()->company_id) {
            $companyId = $filter['company'];
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
            }
            $orders = $orders->leftJoin('users', 'users.id', '=', 'orders.target_user')
                ->where('users.company_id', $companyId);
            $types = $types->leftJoin('users', 'users.id', '=', 'o.target_user')->where('users.company_id', $companyId);
            $time = $time->leftJoin('users', 'users.id', '=', 'cpl.user_id')->where('users.company_id', $companyId);
            $opened = $opened->leftJoin('users', 'users.id', '=', 'oo.user_id')->where('users.company_id', $companyId);
        }

        if ($filter['source']) {
            $orders = $orders->where('tag_source', $filter['source']);
            $types = $types->where('o.tag_source', $filter['source']);
            $time = $time->where('o.tag_source', $filter['source']);
            $opened = $opened->where('o.tag_source', $filter['source']);
        }

        if ($filter['country']) {
            $orders = $orders->where('geo', $filter['country']);
            $types = $types->where('o.geo', $filter['country']);
            $time = $time->where('o.geo', $filter['country']);
            $opened = $opened->where('o.geo', $filter['country']);
        }

        if ($filter['medium']) {
            $orders = $orders->where('tag_medium', $filter['medium']);
            $types = $types->where('o.tag_medium', $filter['medium']);
            $time = $time->where('o.tag_medium', $filter['medium']);
            $opened = $opened->where('o.tag_medium', $filter['medium']);
        }

        if ($filter['content']) {
            $orders = $orders->where('tag_content', $filter['content']);
            $types = $types->where('o.tag_content', $filter['content']);
            $time = $time->where('o.tag_content', $filter['content']);
            $opened = $opened->where('o.tag_content', $filter['content']);
        }

        if ($filter['campaign']) {
            $orders = $orders->where('tag_campaign', $filter['campaign']);
            $types = $types->where('o.tag_campaign', $filter['campaign']);
            $time = $time->where('o.tag_campaign', $filter['campaign']);
            $opened = $opened->where('o.tag_campaign', $filter['campaign']);
        }

        if ($filter['term']) {
            $orders = $orders->where('tag_term', $filter['term']);
            $types = $types->where('o.tag_term', $filter['term']);
            $time = $time->where('o.tag_term', $filter['term']);
            $opened = $opened->where('o.tag_term', $filter['term']);
        }

        $orders = $orders->groupBy('target_status', 'target_user')->get();
        $types = $types->groupBy('op.type', 'o.target_user')->get();
        $time = collect($time->groupBy('cpl.user_id')->get())->keyBy('user_id');
        $opened = $opened->groupBy('oo.user_id')->groupBy('oo.callback_status')->get();

        $newUserTime = [];
        if ($newTimes) {
            foreach ($newTimes as $ntime) {
                if (!isset($newUserTime[$ntime->user_id])) {
                    $newUserTime[$ntime->user_id] = [
                        'crm' => 0,
                        'pbx' => 0
                    ];
                }
                $newUserTime[$ntime->user_id][$ntime->type] = $ntime->sum;
            }
        }

        $ordersOpened = [];
        if ($opened) {
            foreach ($opened as $open) {
                if (isset($ordersOpened[$open->user_id])) {
                    $ordersOpened[$open->user_id]['opened'] += $open->count;
                } else {
                    $ordersOpened[$open->user_id]['opened'] = $open->count;
                    $ordersOpened[$open->user_id]['avto'] = 0;
                    $ordersOpened[$open->user_id]['bad_con'] = 0;
                    $ordersOpened[$open->user_id]['false'] = 0;
                }

                switch ($open->callback_status) {
                    case (1) :
                        $ordersOpened[$open->user_id]['avto'] = $open->count;
                        break;
                    case (2) :
                        $ordersOpened[$open->user_id]['bad_con'] = $open->count;
                        break;
                    case (4) :
                        $ordersOpened[$open->user_id]['false'] = $open->count;
                        break;
                }
            }
        }

        $data = [];
        foreach ($orders as $order) {
            if (isset($data[$order->target_user])) {
                if ($order->target_status == 1) {
                    $data[$order->target_user]['approve'] = $order->count;
                } elseif ($order->target_status == 2) {
                    $data[$order->target_user]['failure'] = $order->count;
                } elseif ($order->target_status == 3) {
                    $data[$order->target_user]['fake'] = $order->count;
                }
                if ($filter['date_type'] == 'time_created') {
                    if (!$order->target_status || $order->target_status == 4) {
                        $data[$order->target_user]['processing'] += $order->count;
                    }
                }
            } else {
                $data[$order->target_user]['user_id'] = $order->target_user;
                $data[$order->target_user]['processing'] = 0;
                $data[$order->target_user]['approve'] = 0;
                $data[$order->target_user]['fake'] = 0;
                $data[$order->target_user]['failure'] = 0;
                $data[$order->target_user]['up_sell'] = 0;
                $data[$order->target_user]['up_sell_2'] = 0;
                $data[$order->target_user]['cross_sell'] = 0;
                $data[$order->target_user]['cross_sell_2'] = 0;
                $data[$order->target_user]['login_time_elastix'] = 0;
                $data[$order->target_user]['login_time_crm'] = 0;
                $data[$order->target_user]['talk_time'] = 0;
                $data[$order->target_user]['pause_time'] = 0;
                $data[$order->target_user]['order_time'] = 0;
                $data[$order->target_user]['opened'] = 0;
                $data[$order->target_user]['avto'] = 0;
                $data[$order->target_user]['bad_con'] = 0;
                $data[$order->target_user]['new_crm'] = 0;
                $data[$order->target_user]['new_pbx'] = 0;
                $data[$order->target_user]['false'] = 0;
                if ($order->target_status == 1) {
                    $data[$order->target_user]['approve'] = $order->count;
                } elseif ($order->target_status == 2) {
                    $data[$order->target_user]['failure'] = $order->count;
                } elseif ($order->target_status == 3) {
                    $data[$order->target_user]['fake'] = $order->count;
                }
                if ($filter['date_type'] == 'time_created') {
                    if (!$order->target_status || $order->target_status == 4) {
                        $data[$order->target_user]['processing'] += $order->count;
                    }
                }
            }
            if (isset($time[$order->target_user])) {
                $data[$order->target_user]['talk_time'] = $time[$order->target_user]->time;
            }
            if (isset($ordersOpened[$order->target_user])) {
                $data[$order->target_user]['opened'] = $ordersOpened[$order->target_user]['opened'];
                $data[$order->target_user]['avto'] = $ordersOpened[$order->target_user]['avto'];
                $data[$order->target_user]['bad_con'] = $ordersOpened[$order->target_user]['bad_con'];
                $data[$order->target_user]['false'] = $ordersOpened[$order->target_user]['false'];
            }
            if (isset($newUserTime[$order->target_user])) {
                $data[$order->target_user]['new_crm'] = $newUserTime[$order->target_user]['crm'];
                $data[$order->target_user]['new_pbx'] = $newUserTime[$order->target_user]['pbx'];
            }
        }

        if ($types) {
            foreach ($types as $type) {
                if (isset($data[$type->target_user])) {
                    if ($type->type == 1) {
                        $data[$type->target_user]['up_sell'] = $type->count;
                    }
                    if ($type->type == 2) {
                        $data[$type->target_user]['up_sell_2'] = $type->count;
                    }
                    if ($type->type == 4) {
                        $data[$type->target_user]['cross_sell'] = $type->count;
                    }
                    if ($type->type == 5) {
                        $data[$type->target_user]['cross_sell_2'] = $type->count;
                    }
                }
            }
        }

        $reportTime = DB::table('report_time')->where('date', '>=', $filter['date_start'])
            ->where('date', '<=', $filter['date_end'])
            ->where('date', '>=', $filter['date_start']);
        if ($filter['user']) {
            $reportTime = $reportTime->where('user_id', $filter['user']);
        }
        $reportTime = $reportTime->get();

        if (!($filter['offer'] || $filter['user'] || $filter['source'] || $filter['country']) || ($filter['user'] && !$filter['country']) && $reportTime) {

            foreach ($reportTime as $t) {
                if (isset($data[$t->user_id])) {
                    $data[$t->user_id]['login_time_elastix'] += $t->login_time_elastix;
                    $data[$t->user_id]['login_time_crm'] += $t->login_time_crm;
                    $data[$t->user_id]['pause_time'] += $t->pause_time;
                    $data[$t->user_id]['order_time'] += $t->order_time;
                }

            }
        }

        return $data;
    }

    private function getAuditAllForGroupOffer($filter)
    {
        $orders = DB::table($this->table)->select('offer_id', 'target_status', DB::raw('COUNT(target_status) AS count'))
            ->where($filter['date_type'], '>=', $filter['date_start'])
            ->where($filter['date_type'], '<=', $filter['date_end']);
        $types = DB::table('order_products AS op')
            ->select('op.type', DB::raw('COUNT(op.type) AS count'), 'o.offer_id')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->whereIn('type', [1, 2, 4, 5]);
        $time = DB::table('call_progress_log AS cpl')->select(
            DB::raw('SUM(cpl.talk_time) AS time'),
            'o.offer_id'
        )
            ->leftJoin('orders AS o', 'o.id', '=', 'cpl.order_id')
            ->where('cpl.entity', 'order')
            ->where('date', '>=', $filter['date_start'])
            ->where('date', '<=', $filter['date_end']);

        if ($filter['date_start'] && $filter['date_end']) {
            $types = $types->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
        }

        if ($filter['project']) {
            $orders = $orders->where('project_id', $filter['project']);
            $types = $types->where('o.project_id', $filter['project']);
            $time = $time->where('o.project_id', $filter['project']);
        }


        if ($filter['learning']) {
            $status = $filter['learning'] == 2 ? 0 : 1;
            $orders = $orders->where('target_learning', $status);
            $types = $types->where('o.target_learning', $status);
            $time = $time->where('o.target_learning', $status);
        }

        if ($filter['offer']) {
            $orders = $orders->whereIn('offer_id', $filter['offer']);
            $types = $types->whereIn('o.offer_id', $filter['offer']);
            $time = $time->whereIn('o.offer_id', $filter['offer']);
        }

        if ($filter['user']) {
            $orders = $orders->where('target_user', $filter['user']);
            $types = $types->where('o.target_user', $filter['user']);
            $time = $time->where('o.target_user', $filter['user']);
        }

        if ($filter['company'] || auth()->user()->company_id) {
            $companyId = $filter['company'];
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
            }
            $orders = $orders->leftJoin('users', 'users.id', '=', 'orders.target_user')
                ->where('users.company_id', $companyId);
            $types = $types->leftJoin('users', 'users.id', '=', 'o.target_user')->where('users.company_id', $companyId);
            $time = $time->leftJoin('users', 'users.id', '=', 'o.target_user')->where('users.company_id', $companyId);
        }

        if ($filter['source']) {
            $orders = $orders->where('tag_source', $filter['source']);
            $types = $types->where('o.tag_source', $filter['source']);
            $time = $time->where('o.tag_source', $filter['source']);
        }

        if ($filter['country']) {
            $orders = $orders->where('geo', $filter['country']);
            $types = $types->where('o.geo', $filter['country']);
            $time = $time->where('o.geo', $filter['country']);
        }

        if ($filter['display_hp']) {
            $orders = $orders->where('entity', 'cold_call');
            $types = $types->where('o.entity', 'cold_call');
            $time = $time->where('o.entity', 'cold_call');
        }

        if ($filter['medium']) {
            $orders = $orders->where('tag_medium', $filter['medium']);
            $types = $types->where('o.tag_medium', $filter['medium']);
            $time = $time->where('o.tag_medium', $filter['medium']);
        }

        if ($filter['content']) {
            $orders = $orders->where('tag_content', $filter['content']);
            $types = $types->where('o.tag_content', $filter['content']);
            $time = $time->where('o.tag_content', $filter['content']);
        }

        if ($filter['campaign']) {
            $orders = $orders->where('tag_campaign', $filter['campaign']);
            $types = $types->where('o.tag_campaign', $filter['campaign']);
            $time = $time->where('o.tag_campaign', $filter['campaign']);
        }

        if ($filter['term']) {
            $orders = $orders->where('tag_term', $filter['term']);
            $types = $types->where('o.tag_term', $filter['term']);
            $time = $time->where('o.tag_term', $filter['term']);
        }

        $orders = $orders->groupBy('target_status', 'offer_id')->get();
        $types = $types->groupBy('op.type', 'o.offer_id')->get();
        $time = $time->groupBy('o.offer_id')->get();

        $data = [];
        foreach ($orders as $order) {
            if (isset($data[$order->offer_id])) {
                if ($order->target_status == 1) {
                    $data[$order->offer_id]['approve'] = $order->count;
                } elseif ($order->target_status == 2) {
                    $data[$order->offer_id]['failure'] = $order->count;
                } elseif ($order->target_status == 3) {
                    $data[$order->offer_id]['fake'] = $order->count;
                }
                if ($filter['date_type'] == 'time_created') {
                    if (!$order->target_status || $order->target_status == 4) {
                        $data[$order->offer_id]['processing'] += $order->count;
                    }
                }
            } else {
                $data[$order->offer_id]['offer_id'] = $order->offer_id;
                $data[$order->offer_id]['processing'] = 0;
                $data[$order->offer_id]['approve'] = 0;
                $data[$order->offer_id]['fake'] = 0;
                $data[$order->offer_id]['failure'] = 0;
                $data[$order->offer_id]['up_sell'] = 0;
                $data[$order->offer_id]['up_sell_2'] = 0;
                $data[$order->offer_id]['cross_sell'] = 0;
                $data[$order->offer_id]['cross_sell_2'] = 0;
                $data[$order->offer_id]['time'] = 0;
                if ($order->target_status == 1) {
                    $data[$order->offer_id]['approve'] = $order->count;
                } elseif ($order->target_status == 2) {
                    $data[$order->offer_id]['failure'] = $order->count;
                } elseif ($order->target_status == 3) {
                    $data[$order->offer_id]['fake'] = $order->count;
                }
                if ($filter['date_type'] == 'time_created') {
                    if (!$order->target_status || $order->target_status == 4) {
                        $data[$order->offer_id]['processing'] += $order->count;
                    }
                }
            }
        }
        if ($types) {
            foreach ($types as $type) {
                if (isset($data[$type->offer_id])) {
                    if ($type->type == 1) {
                        $data[$type->offer_id]['up_sell'] = $type->count;
                    }
                    if ($type->type == 2) {
                        $data[$type->offer_id]['up_sell_2'] = $type->count;
                    }
                    if ($type->type == 4) {
                        $data[$type->offer_id]['cross_sell'] = $type->count;
                    }
                    if ($type->type == 5) {
                        $data[$type->offer_id]['cross_sell_2'] = $type->count;
                    }
                }
            }
        }
        if ($time) {
            foreach ($time as $t) {
                if (isset($data[$t->offer_id])) {
                    $data[$t->offer_id]['time'] = $t->time;
                }
            }
        }

        return $data;
    }

    private function getAuditAllForGroupApproveOffer($filter)
    {
        $orders = DB::table($this->table . ' AS o')
            ->select('o.offer_id', 'o.target_status', 'o.geo', 'of.name', DB::raw('COUNT(o.id) AS count'))
            ->leftJoin('offers as of', 'of.id', '=', 'o.offer_id')
            ->where('o.offer_id', '>', 0);
        $products = DB::table($this->table . ' AS o')
            ->select('o.offer_id', 'op.type', 'o.geo', DB::raw('COUNT(op.id) AS count'))
            ->leftJoin('order_products AS op', 'op.order_id', '=', 'o.id')
            ->where('op.disabled', 0)
            ->where('o.target_status', 1)
            ->where('o.offer_id', '>', 0);

        if ($filter['date_start'] && $filter['date_end']) {
            $orders = $orders->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
            $products = $products->whereBetween('o.' . $filter['date_type'], [
                $filter['date_start'],
                $filter['date_end']
            ]);
        }

        if ($filter['project']) {
            $orders = $orders->where('o.project_id', $filter['project']);
            $products = $products->where('o.project_id', $filter['project']);
        }

        if ($filter['learning']) {
            $status = $filter['learning'] == 2 ? 0 : 1;
            $orders = $orders->where('o.target_learning', $status);
            $products = $products->where('o.target_learning', $status);
        }

        if ($filter['offer']) {
            $orders = $orders->whereIn('o.offer_id', $filter['offer']);
            $products = $products->whereIn('o.offer_id', $filter['offer']);
        }

        if ($filter['user']) {
            $orders = $orders->where('o.target_user', $filter['user']);
            $products = $products->where('o.target_user', $filter['user']);
        }

        if ($filter['country']) {
            $orders = $orders->where('o.geo', mb_strtolower($filter['country']));
            $products = $products->where('o.geo', mb_strtolower($filter['country']));
        }

        if ($filter['display_hp']) {
            $orders = $orders->where('entity', 'cold_call');
            $products = $products->where('o.entity', 'cold_call');
        }

        if ($filter['company'] || auth()->user()->company_id) {
            $companyId = $filter['company'];
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
            }
            $orders = $orders->leftJoin('users', 'users.id', '=', 'o.target_user')
                ->where('users.company_id', $companyId);
            $products = $products->leftJoin('users', 'users.id', '=', 'o.target_user')
                ->where('users.company_id', $companyId);
        }
        $orders = $orders->groupBy('o.offer_id', 'o.geo', 'o.target_status')->orderBy('count', 'desc')->get();
        $products = $products->groupBy('o.offer_id', 'o.geo', 'op.type')->get();

        $dataOffer = [];
        $offerIds = [];
        $countries = [];
        if ($orders) {
            foreach ($orders as $order) {
                $offerIds[$order->offer_id] = $order->offer_id;
                if (isset($dataOffer[$order->offer_id])) {
                    $dataOffer[$order->offer_id]['all'] += $order->target_status != 3 ? $order->count : 0;
                    $dataOffer[$order->offer_id]['all_approve'] += $order->target_status == 1 ? $order->count : 0;
                    if (isset($dataOffer[$order->offer_id][$order->geo])) {
                        $dataOffer[$order->offer_id][$order->geo] += $order->target_status == 1 ? $order->count : 0;
                    } else {
                        $dataOffer[$order->offer_id][$order->geo] = $order->target_status == 1 ? $order->count : 0;
                    }
                } else {
                    $dataOffer[$order->offer_id]['name'] = $order->name;
                    $dataOffer[$order->offer_id]['offer_id'] = $order->offer_id;
                    $dataOffer[$order->offer_id]['all'] = $order->target_status != 3 ? $order->count : 0;
                    $dataOffer[$order->offer_id]['all_approve'] = $order->target_status == 1 ? $order->count : 0;
                    $dataOffer[$order->offer_id]['all_products'] = 0;
                    $dataOffer[$order->offer_id]['all_up_cross'] = 0;
                    $dataOffer[$order->offer_id][$order->geo] = $order->target_status == 1 ? $order->count : 0;
                }
                if ($order->target_status == 1) {
                    $countries[$order->geo] = $order->geo;
                }
            }
        }

        if ($products) {
            foreach ($products as $product) {
                if (isset($dataOffer[$product->offer_id])) {
                    $dataOffer[$product->offer_id]['all_products'] += $product->count;
                    if ($product->type == 1 || $product->type == 2 || $product->type == 4) {
                        $dataOffer[$product->offer_id]['all_up_cross'] += $product->count;
                        if (isset($dataOffer[$product->offer_id]['up_' . $product->geo])) {
                            $dataOffer[$product->offer_id]['up_' . $product->geo] += $product->count;
                        } else {
                            $dataOffer[$product->offer_id]['up_' . $product->geo] = $product->count;
                        }
                    }
                }
            }
        }

        $rates = PlanRateOffer::with('planRate')->whereIn('offer_id', $offerIds)->get();

        if ($rates) {
            foreach ($rates AS $rate) {
                if ($rate->planRate) {
                    $data = json_decode($rate->planRate->data);
                    if ($data) {
                        foreach ($data as $oneRate) {
                            $dataOffer[$rate->offer_id][$oneRate->geo . '_rate'] = $oneRate;
                        }
                    }
                }

            }
        }
        return ['orders' => $dataOffer, 'countries' => $countries];
    }

    private function getAuditAllForGroupSource($filter)
    {
        $orders = DB::table($this->table)
            ->select($this->table . '.project_id', 'target_status', DB::raw('COUNT(target_status) AS count'));
        $types = DB::table('order_products AS op')
            ->select('op.type', DB::raw('COUNT(op.type) AS count'), 'o.project_id')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->whereIn('type', [1, 2, 4, 5]);
        $time = DB::table('call_progress_log AS cpl')->select(
            DB::raw('SUM(cpl.talk_time) AS time'),
            'o.project_id'
        )
            ->leftJoin('orders AS o', 'o.id', '=', 'cpl.order_id')
            ->where('cpl.entity', 'order')
            ->where('date', '>=', $filter['date_start'])
            ->where('date', '<=', $filter['date_end']);

        if ($filter['date_start'] && $filter['date_end']) {
            $orders = $orders->whereBetween($filter['date_type'], [$filter['date_start'], $filter['date_end']]);
            $types = $types->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
        }

        if ($filter['project']) {
            $orders = $orders->where($this->table . '.project_id', $filter['project']);
            $types = $types->where('o.project_id', $filter['project']);
            $time = $time->where('o.project_id', $filter['project']);
        }

        if ($filter['offer']) {
            $orders = $orders->whereIn('offer_id', $filter['offer']);
            $types = $types->whereIn('o.offer_id', $filter['offer']);
            $time = $time->whereIn('o.offer_id', $filter['offer']);
        }

        if ($filter['user']) {
            $orders = $orders->where('target_user', $filter['user']);
            $types = $types->where('o.target_user', $filter['user']);
            $time = $time->where('cpl.user_id', $filter['user']);
        }

        if ($filter['company'] || auth()->user()->company_id) {
            $companyId = $filter['company'];
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
            }
            $orders = $orders->leftJoin('users', 'users.id', '=', 'orders.target_user')
                ->where('users.company_id', $companyId);
            $types = $types->leftJoin('users', 'users.id', '=', 'o.target_user')->where('users.company_id', $companyId);
            $time = $time->leftJoin('users', 'users.id', '=', 'cpl.user_id')->where('users.company_id', $companyId);
        }

        if ($filter['source']) {
            $orders = $orders->where('tag_source', $filter['source']);
            $types = $types->where('o.tag_source', $filter['source']);
            $time = $time->where('o.tag_source', $filter['source']);
        }

        if ($filter['learning']) {
            $status = $filter['learning'] == 2 ? 0 : 1;
            $orders = $orders->where('target_learning', $status);
            $types = $types->where('o.target_learning', $status);
            $time = $time->where('o.target_learning', $status);
        }


        if ($filter['country']) {
            $orders = $orders->where('geo', $filter['country']);
            $types = $types->where('o.geo', $filter['country']);
            $time = $time->where('o.geo', $filter['country']);
        }

        if ($filter['medium']) {
            $orders = $orders->where('tag_medium', $filter['medium']);
            $types = $types->where('o.tag_medium', $filter['medium']);
            $time = $time->where('o.tag_medium', $filter['medium']);
        }

        if ($filter['content']) {
            $orders = $orders->where('tag_content', $filter['content']);
            $types = $types->where('o.tag_content', $filter['content']);
            $time = $time->where('o.tag_content', $filter['content']);
        }

        if ($filter['campaign']) {
            $orders = $orders->where('tag_campaign', $filter['campaign']);
            $types = $types->where('o.tag_campaign', $filter['campaign']);
            $time = $time->where('o.tag_campaign', $filter['campaign']);
        }

        if ($filter['display_hp']) {
            $orders = $orders->where('entity', 'cold_call');
            $types = $types->where('o.entity', 'cold_call');
            $time = $time->where('o.entity', 'cold_call');
        }

        if ($filter['term']) {
            $orders = $orders->where('tag_term', $filter['term']);
            $types = $types->where('o.tag_term', $filter['term']);
            $time = $time->where('o.tag_term', $filter['term']);
        }

        $orders = $orders->groupBy('target_status', $this->table . '.project_id')->get();
        $types = $types->groupBy('op.type', 'o.project_id')->get();
        $time = $time->groupBy('o.project_id')->get();

        $data = [];
        foreach ($orders as $order) {
            if (isset($data[$order->project_id])) {
                if ($order->target_status == 1) {
                    $data[$order->project_id]['approve'] = $order->count;
                } elseif ($order->target_status == 2) {
                    $data[$order->project_id]['failure'] = $order->count;
                } elseif ($order->target_status == 3) {
                    $data[$order->project_id]['fake'] = $order->count;
                }
                if ($filter['date_type'] == 'time_created') {
                    if (!$order->target_status || $order->target_status == 4) {
                        $data[$order->project_id]['processing'] += $order->count;
                    }
                }
            } else {
                $data[$order->project_id]['project_id'] = $order->project_id;
                $data[$order->project_id]['processing'] = 0;
                $data[$order->project_id]['approve'] = 0;
                $data[$order->project_id]['fake'] = 0;
                $data[$order->project_id]['failure'] = 0;
                $data[$order->project_id]['up_sell'] = 0;
                $data[$order->project_id]['up_sell_2'] = 0;
                $data[$order->project_id]['cross_sell'] = 0;
                $data[$order->project_id]['cross_sell_2'] = 0;
                $data[$order->project_id]['time'] = 0;

                if ($order->target_status == 1) {
                    $data[$order->project_id]['approve'] = $order->count;
                } elseif ($order->target_status == 2) {
                    $data[$order->project_id]['failure'] = $order->count;
                } elseif ($order->target_status == 3) {
                    $data[$order->project_id]['fake'] = $order->count;
                }
                if ($filter['date_type'] == 'time_created') {
                    if (!$order->target_status || $order->target_status == 4) {
                        $data[$order->project_id]['processing'] += $order->count;
                    }
                }
            }
        }
        if ($types) {
            foreach ($types as $type) {
                if (isset($data[$type->project_id])) {
                    if ($type->type == 1) {
                        $data[$type->project_id]['up_sell'] = $type->count;
                    }
                    if ($type->type == 2) {
                        $data[$type->project_id]['up_sell_2'] = $type->count;
                    }
                    if ($type->type == 4) {
                        $data[$type->project_id]['cross_sell'] = $type->count;
                    }
                    if ($type->type == 5) {
                        $data[$type->project_id]['cross_sell_2'] = $type->count;
                    }
                }
            }
        }

        if ($time) {
            foreach ($time as $t) {
                if (isset($data[$t->project_id])) {
                    $data[$t->project_id]['time'] = $t->time;
                }
            }
        }
        return $data;
    }

    private function getAuditAllForGroupCountry($filter)
    {
        $orders = DB::table($this->table)->select('geo', 'target_status', DB::raw('COUNT(target_status) AS count'));
        $types = DB::table('order_products AS op')
            ->select('op.type', DB::raw('COUNT(op.type) AS count'), 'o.geo')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->whereIn('type', [1, 2, 4, 5]);
        if ($filter['date_start'] && $filter['date_end']) {
            $orders = $orders->whereBetween($filter['date_type'], [$filter['date_start'], $filter['date_end']]);
            $types = $types->whereBetween('o.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
        }

        if ($filter['project']) {
            $orders = $orders->where('project_id', $filter['project']);
            $types = $types->where('o.project_id', $filter['project']);
        }

        if ($filter['learning']) {
            $status = $filter['learning'] == 2 ? 0 : 1;
            $orders = $orders->where('target_learning', $status);
            $types = $types->where('o.target_learning', $status);
        }

        if ($filter['offer']) {
            $orders = $orders->whereIn('offer_id', $filter['offer']);
            $types = $types->whereIn('o.offer_id', $filter['offer']);
        }

        if ($filter['user']) {
            $orders = $orders->where('target_user', $filter['user']);
            $types = $types->where('o.target_user', $filter['user']);
        }

        if ($filter['display_hp']) {
            $orders = $orders->where('entity', 'cold_call');
            $types = $types->where('o.entity', 'cold_call');
        }

        if ($filter['company'] || auth()->user()->company_id) {
            $companyId = $filter['company'];
            if (auth()->user()->company_id) {
                $companyId = auth()->user()->company_id;
            }
            $orders = $orders->leftJoin('users', 'users.id', '=', 'orders.target_user')
                ->where('users.company_id', $companyId);
            $types = $types->leftJoin('users', 'users.id', '=', 'o.target_user')->where('users.company_id', $companyId);
        }

        if ($filter['source']) {
            $orders = $orders->where('tag_source', $filter['source']);
            $types = $types->where('o.tag_source', $filter['source']);
        }

        if ($filter['country']) {
            $orders = $orders->where('geo', $filter['country']);
            $types = $types->where('o.geo', $filter['country']);
        }

        if ($filter['medium']) {
            $orders = $orders->where('tag_medium', $filter['medium']);
            $types = $types->where('o.tag_medium', $filter['medium']);
        }

        if ($filter['content']) {
            $orders = $orders->where('tag_content', $filter['content']);
            $types = $types->where('o.tag_content', $filter['content']);
        }

        if ($filter['campaign']) {
            $orders = $orders->where('tag_campaign', $filter['campaign']);
            $types = $types->where('o.tag_campaign', $filter['campaign']);
        }

        if ($filter['term']) {
            $orders = $orders->where('tag_term', $filter['term']);
            $types = $types->where('o.tag_term', $filter['term']);
        }

        $orders = $orders->groupBy('target_status', 'geo')->get();
        $types = $types->groupBy('op.type', 'o.geo')->get();

        $data = [];
        if ($orders) {
            foreach ($orders as $order) {
                if (isset($data[$order->geo])) {
                    if ($order->target_status == 1) {
                        $data[$order->geo]['approve'] = $order->count;
                    } elseif ($order->target_status == 2) {
                        $data[$order->geo]['failure'] = $order->count;
                    } elseif ($order->target_status == 3) {
                        $data[$order->geo]['fake'] = $order->count;
                    }
                    if ($filter['date_type'] == 'time_created') {
                        if (!$order->target_status || $order->target_status == 4) {
                            $data[$order->geo]['processing'] += $order->count;
                        }
                    }
                } else {
                    $data[$order->geo]['country'] = $order->geo;
                    $data[$order->geo]['approve'] = 0;
                    $data[$order->geo]['fake'] = 0;
                    $data[$order->geo]['failure'] = 0;
                    $data[$order->geo]['processing'] = 0;
                    $data[$order->geo]['up_sell'] = 0;
                    $data[$order->geo]['up_sell_2'] = 0;
                    $data[$order->geo]['cross_sell'] = 0;
                    $data[$order->geo]['cross_sell_2'] = 0;
                    if ($order->target_status == 1) {
                        $data[$order->geo]['approve'] = $order->count;
                    } elseif ($order->target_status == 2) {
                        $data[$order->geo]['failure'] = $order->count;
                    } elseif ($order->target_status == 3) {
                        $data[$order->geo]['fake'] = $order->count;
                    }
                    if ($filter['date_type'] == 'time_created') {
                        if (!$order->target_status || $order->target_status == 4) {
                            $data[$order->geo]['processing'] += $order->count;
                        }
                    }
                }
            }
        }

        if ($types) {
            foreach ($types as $type) {
                if (isset($data[$type->geo])) {
                    if ($type->type == 1) {
                        $data[$type->geo]['up_sell'] = $type->count;
                    }
                    if ($type->type == 2) {
                        $data[$type->geo]['up_sell_2'] = $type->count;
                    }
                    if ($type->type == 4) {
                        $data[$type->geo]['cross_sell'] = $type->count;
                    }
                    if ($type->type == 5) {
                        $data[$type->geo]['cross_sell_2'] = $type->count;
                    }
                }
            }
        }

        return $data;
    }

    function addStatusCallAnotherLanguage($id)
    {
        (new OrdersLog())->addOrderLog($id, 'Установлен процессинг статус - Другой язык', [
            'status_id'   => 7,
            'status_name' => 'Другой язык'
        ]);
        return DB::table($this->table)->where('id', $id)
            ->update([
                'proc_status'         => 7,
                'time_status_updated' => Carbon::now()
            ]);

    }

    /**
     * Получаем один заказа
     * @param int $id ID заказа
     * @return object
     */
    function getOneOrder($id)
    {
        $result = DB::table($this->table . ' AS o')->select(
            'o.id', 'o.phone_input', 'o.phone', 'o.price_total',
            'o.price_input', 'o.host', 'o.time_created', 'o.time_changed',
            'o.time_modified', 'o.proc_status', 'o.proc_stage', 'o.proc_callback_time',
            'o.comments', 'o.target_id', 'o.target_user', 'o.target_status', 'co.currency',
            'o.name_first AS name', 'o.name_last AS surname', 'o.name_middle AS middle',
            'o.input_data', 'co.name AS country', 'o.geo', 'o.proc_campaign', 'of.name AS offer_name',
            'o.offer_id', 'o.age', 'o.gender', 'o.proc_priority', 'o.target_approve', 'o.target_refuse',
            'o.target_cancel', 'o.entity', 'o.source_url', 'o.moderation_id', 'o.subproject_id', 'o.locked'
        )
            ->leftJoin('countries AS co', 'o.geo', '=', 'co.code')
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->where('o.id', $id)
            ->first();

        if ($result) {
            return $result;
        }
        abort(404);
    }

    /**
     * Получаем один заказа
     * @param int $id ID заказа
     * @return object
     */
    function getOneOrderColdCall($id)
    {

        $result = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.phone_input', 'o.phone', 'o.price_total', 'o.host', 'o.time_created',
                'o.time_changed', 'o.time_modified', 'o.proc_status', 'o.proc_stage', 'o.proc_callback_time',
                'o.comments', 'o.target_id', 'o.target_user', 'o.target_status', 'co.currency', 'o.name_first AS name',
                'o.name_last AS surname', 'o.name_middle AS middle', 'o.input_data', 'co.name AS country', 'o.geo',
                'o.proc_campaign', 'o.offer_id', 'o.age', 'o.gender', 'o.proc_priority',
                'o.target_approve', 'o.target_refuse', 'o.target_cancel', 'o.entity')
            ->leftJoin('countries AS co', 'o.geo', '=', 'co.code')
            ->where('o.id', '=', $id)
            ->first();

        if ($result) {
            return $result;
        }
        abort(404);
    }

    function changeTarget($id, $targetId)
    {
        $log = [
            'order_id' => $id,
            'user_id'  => auth()->user()->id,
            'text'     => 'Цель изменена',
            'date'     => now(),
        ];
        DB::table('orders_log')->insert($log);

        return DB::table($this->table)->where('id', $id)
            ->update(['target_id' => $targetId]);
    }

    /**
     * Получаем повторяющие номера телефонов
     * @param int $id ID заказа
     * @param object $targetsFinalModel
     * @return array
     */
    function getOrderByPhone($id, $phone = null, $ip = null)
    {
        if (!$phone) {
            $order = self::find($id);
            $phone = $order->phone_input ?? null;
            $ip = $order->host ?? null;
        }
        $data = [];
        if ($phone) {
            $orders = self::with(
                'offer',
                'project',
                'country',
                'campaign',
                'targetUser',
                'products',
                'getTargetValue',
                'procStatus'
            )->where(function ($q) use ($phone, $ip) {
                $q->where('phone_input', $phone)
                    ->orWhere('host', $ip);
            })
                ->where('id', '!=', $id)
                ->orderBy('id', 'desc')
                ->get()
                ->keyBy('id');

            $targetValues = TargetValue::whereIn('order_id', $orders->where('target_status', '!=', 1)->pluck('id'))
                ->get();

            $data['targets'] = $this->getTargetValueForAllOrders($targetValues);
            $data['orders'] = $orders;
        }
        return $data;
    }

    /**
     * количество заказов с одинаковым номером
     */
    public function getCountOrdersByPhone($id, $phone, $ip = null)
    {
        return DB::table($this->table)
            ->where('phone_input', $phone)
            ->whereNotNull('phone_input')
//            ->where('host', $ip)
            ->where('id', '!=', $id)
            ->count();
    }

    /**
     * Проверяем существует ли такой заказ
     * @param int $id ID заказа
     * @return bool
     */
    function existOrder($id)
    {
        return DB::table($this->table)->select('id', 'target_user', 'proc_status')
            ->where('id', $id)
            ->first();
    }

    function changeCompanyElastix($CompanyElastixId, $ids)
    {
        $campaign = Campaign::find($CompanyElastixId);
        $orders = DB::table($this->table)->whereIn('id', $ids)->get();

        if ($orders) {
            foreach ($orders as $order) {
                if ($order->proc_status == 2) {
                    $this->apiElastixProcessing2('changeQueueOneOrder', [
                        'id'     => $order->id,
                        'entity' => 'order',
                        'queue'  => $CompanyElastixId
                    ]);
                }
            }
        }

        //write logs
        OrdersLog::addLogForChangeCampaign($orders, $campaign);

        return DB::table($this->table)->whereIn('id', $ids)
            ->update(['proc_campaign' => $CompanyElastixId]);
    }

    public function deleteOrdersAndChangeStatus(Collection $orders, $status = 1)//1 - в обработке
    {
        if ($orders->isNotEmpty()) {
            $ids = $orders->pluck('id')->toArray();
            $this->deleteCallsByIds($ids);
            OrdersLog::addOrdersLog($ids, 'Заказ был удален из PBX');
            ActionController::updateOrderProcStatus($orders, $status);
            return true;
        }
        return false;
    }

    /**
     * Редактируем дату изменения
     * @param int $id ID заказа
     * @return string
     */
    function changeDateChange($id)
    {
        $time = now();
        DB::table($this->table)->where('id', $id)
            ->update(['time_changed' => $time]);
        return $time;
    }

    /**
     * Изменяем общую стоимость и время редактированния заказа
     * @param int $id ID заказа
     * @param double $allPrice Цена
     * @return string
     */
    function changeAllPriceAndDateChange($id, $allPrice)
    {
        $time = now();
        $sumProducts = OrderProduct::where('order_id', $id)->where('disabled', 0)->sum('price');
        DB::table($this->table)->where('id', $id)
            ->update([
                'price_total'    => $allPrice,
                'time_changed'   => $time,
                'price_products' => $sumProducts,
            ]);
        return $time;
    }

    /**
     * Получаем Страну и Компанию
     * @param int $id ID заказа
     * @return array
     */
    function getCountryCompanyCurrency($id)
    {
        return DB::table($this->table . ' AS o')->select('co.id AS country_id', 'o.project_id', 'co.currency')
            ->leftJoin('countries AS co', 'o.geo', '=', 'co.code')
            ->where('o.id', $id)
            ->first();
    }

    /**
     * Время перезвона
     * @param int $id ID заказа
     * @param string $date Дата перезвона
     * @param $status для перезвонить сейчас/автоответчик
     * @return bool
     */
    function callBack($id, $date, $callBackStatus = 0, $status = 0)
    {
        switch ($callBackStatus) {
            case 1 :
                {
                    //автоответчик
                    $callCount = DB::table($this->table)
                        ->where('id', $id)
                        ->value('proc_stage');
                    $priority = $callCount + 2;
                    $callback = now();
                    break;
                }
            case 2 :
                {
                    //плохая связь
                    if ($status == 1) {
                        //перезвонить сейчас
                        $priority = -1;
                        $callback = now();
                    } else {
                        //ближайшее время
                        $priority = DB::table($this->table)
                            ->where('id', $id)
                            ->value('proc_stage');
                        $callback = now();
                    }
                    break;
                }
            case 3 :
                {
                    //перезвонить на назначеное время
                    $priority = -1;
                    $callback = Carbon::parse($date);
                    break;
                }
            default :
                {
                    $priority = 0;
                    $callback = now();
                }
        }

        return DB::table($this->table)->where('id', $id)->update([
            'proc_status'         => 1,
            'proc_callback_time'  => $callback,
            'proc_time'           => $callback,
            'proc_priority'       => $priority,
            'time_changed'        => now(),
            'time_status_updated' => Carbon::now(),
        ]);
    }


    function changeStatusInProcessing($id)
    {
        return DB::table($this->table)->where('id', $id)
            ->whereIn('proc_status', [1, 2, 3])
            ->update([
                'proc_status'        => 1,
                'time_modified'      => now(),
                'proc_callback_time' => null,
            ]);
    }

    function deleteCallsForElastix($id, $entity = 'order')
    {
        return $this->apiElastixProcessing2('deleteCall', ['id' => $id, 'entity' => $entity]);
    }

    /**
     * Устанавливаем статус
     * @param int $id ID заказа
     * @param int $status Статус заказа
     */
    function changeStatus($id, $status, $targetUser = 0, $learningStatus = 0)
    {
        $orderRequest = \request();

        if (!$targetUser) {
            $targetUser = auth()->user()->id;
        }

        if (isset($orderRequest->new_target_user) && !empty($orderRequest->new_target_user)) {
            $targetUser = $orderRequest->new_target_user;
            $newTargetUser = User::find($targetUser);
            (new OrdersLog)->addOrderLog($id, 'Заказ закреплен за оператором: ' . $newTargetUser->name . ' ' . $newTargetUser->surname . ' id:' . $newTargetUser->id);
        }

        return DB::table($this->table)->where('id', $id)
            ->update([
                'proc_status'        => 3,
                'target_status'      => $status,
                'time_modified'      => now(),
                'proc_callback_time' => null,
                'target_user'        => $targetUser,
                'target_learning'    => $learningStatus
            ]);
    }

    /**
     * добавляем заказы в elastix (cron)
     */
    function addCallsInElastix()
    {
        $operatorCount = 30;
        $agents = $this->apiElastixProcessing('getCountAgents');
        echo date('H:i:s d/m/y') . " getCountAgents\n";
        if ($agents) {
            $nightTimeStart = Carbon::parse('now 21:00:00');
            $nightTimeEnd = Carbon::parse('now 07:00:00');
            $time = now();
            $arrayLog = [];
            foreach ($agents as $ag) {
                $cronStatus = DB::table('company_elastix')->where('id', $ag->queue)
                    ->where('learning', 0)
                    ->where('auto_call', 1)
                    ->value('cron_status');
                if (!$cronStatus || $ag->queue == 10 || $ag->queue == 52) {
                    continue;
                }
                if ($time >= $nightTimeStart && $time <= $nightTimeEnd) {
                    $result = DB::table($this->table)
                        ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time', 'proc_priority', 'proc_stage')
                        ->limit($ag->count * $operatorCount)
                        ->where('proc_time', '<=', $time)
                        ->where('proc_time', '>=', $time)
                        ->where('proc_status', 1)
                        ->where('moderation_id', 0)
                        ->where('entity', 'order')
                        ->where('proc_stage', '<=', 2)
                        ->where('proc_campaign', $ag->queue)
                        ->where(function ($query) {
                            $query->where('target_status', 0)
                                ->orWhere('proc_callback_time', '>', 0)
                                ->orWhere(function ($q) {
                                    $q->where('target_status', '>', 0)
                                        ->where('reset_call', '>', 0);
                                });
                        })
                        ->orderBy('id', 'desc')
                        ->orderBy('proc_stage')
                        ->get();
                } else {
                    $result = DB::table($this->table)
                        ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time', 'proc_priority', 'proc_stage')
                        ->limit($ag->count * $operatorCount)
                        ->where('proc_time', '<=', $time)
                        ->where('proc_status', 1)
                        ->where('entity', 'order')
                        ->where('proc_campaign', $ag->queue)
                        ->where('moderation_id', 0)
                        ->where(function ($query) {
                            $query->where('target_status', 0)
                                ->orWhere('proc_callback_time', '>', 0)
                                ->orWhere(function ($q) {
                                    $q->where('target_status', '>', 0)
                                        ->where('reset_call', '>', 0);
                                });
                        })
                        ->orderBy('id', 'proc_callback_time')
                        ->orderBy('id', 'desc')
                        ->orderBy('proc_stage')
                        ->get();
                }
                echo date('H:i:s d/m/y') . " get orders in queue" . $ag->queue . "\n";
                $arrayLog[$ag->queue] = [
                    'group'       => $ag->queue,
                    'time'        => date('Y.m.d H:i.s'),
                    'all_agents'  => $ag->count,
                    'count_order' => count($result),
                    'success'     => 0,
                    'error'       => 0
                ];
                if ($result) {
                    $data = [];
                    $arrayIds = [];
                    $ordersLog = [];
                    foreach ($result as $kr => $vr) {
                        echo $vr->id . ",\n";
                        $arrayIds[] = $vr->id;
                        $data[$kr]['id'] = $vr->id;
                        $data[$kr]['phone'] = $vr->phone;
                        $data[$kr]['before'] = 0;
                        $data[$kr]['company'] = $vr->proc_campaign;
                        $data[$kr]['weight'] = $vr->proc_stage;
                        $data[$kr]['entity'] = 'order';

                        $ordersLog[$vr->id] = [
                            'order_id'    => $vr->id,
                            'user_id'     => 0,
                            'text'        => 'Установлен процессинг статус "В наборе"',
                            'date'        => now(),
                            'status_id'   => 2,
                            'status_name' => 'В наборе'
                        ];

                        if ($vr->proc_callback_time && $vr->proc_priority) {
                            $data[$kr]['weight'] = $vr->proc_priority;
                        }
                    }

                    echo date('H:i:s d/m/y') . " created array data \n";

                    DB::table($this->table)->whereIn('id', $arrayIds)->update(['proc_status' => 13]);
                    echo date('H:i:s d/m/y') . " updated status 13 \n";
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                    echo date('H:i:s d/m/y') . " added calls to pbx \n";
                    if ($resultApi) {
                        if ($resultApi->status == 200) {
                            $arrayLog[$ag->queue]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status'         => 2,
                                //'proc_callback_time'  => null,
                                'proc_priority'       => 0,
                                'time_status_updated' => Carbon::now(),
                            ];
                            DB::table($this->table)->whereIn('id', $resultApi->data)
                                ->update($changeStatus);
                            if (!empty($ordersLog)) {
                                DB::table(OrdersLog::tableName())->insert($ordersLog);
                            }
                            echo date('H:i:s d/m/y') . " updated status 2 \n";
                        }
                    }

                    $arrayLog[$ag->queue]['error'] = $arrayLog[$ag->queue]['count_order'] - $arrayLog[$ag->queue]['success'];

                }
            }

            echo json_encode($arrayLog) . "\n";
        }
    }

    /**
     * Получаем результыта звонка (cron)
     */
    function getResultCallsElastix($сallProgressLogModel, $entity)
    {
        if (empty($entity)) {
            $entity = ['entity' => 'order'];
        } else {
            $entity = ['entity' => $entity];
        }
        $result = $this->apiElastixProcessing('getCalls', $entity);
        if ($result) {
            echo "---------Get calls--------\n";
            $arrayLog = [];
            $company = [];
            foreach ($result as $r) {
                $order = Order::find($r->crm_id);
                $oldStatusId = $order->proc_status;
                $company[$r->id_campaign] = $r->id_campaign;
                $arrayLog[] = [
                    'order_id'    => $r->crm_id,
                    'elasctix_id' => $r->id,
                    'call_status' => $r->status
                ];

                if (!$order) {
                    echo $r->crm_id . " заказ не найден \n";
                    continue;
                }

                if ($r->status == 'Failure' || $r->status == 'NoAnswer' || $r->status == 'Abandoned') {

                    $statusIfFailed = 1;
                    if ($order->target_status && $order->proc_callback_time) {
                        $statusIfFailed = 3;
                    }
                    if ($order->moderation_id > 0 && $oldStatusId == 2) {
                        $statusIfFailed = 3;
                    }

                    if ($order->moderation_id > 0 && $oldStatusId != 2) {
                        $statusIfFailed = $oldStatusId;
                    }

                    if ($order->proc_status == 5) {
                        $statusIfFailed = 5;
                    }

                    if ($order->reset_call > 0) {
                        $callMaxCount = DB::table('company_elastix')->where('id', $order->proc_campaign)->value('call_count');
                        if($order->proc_stage >= $callMaxCount){
                            $statusIfFailed = 3;
                        }
                    }

                    $order->proc_stage += 1;
                    $order->proc_call_id = $r->id;
//                    DB::table($this->table)->where('id', $r->crm_id)
//                        ->where('moderation_id', 0)
//                        ->increment('proc_stage', 1, ['proc_status' => 1, 'proc_call_id' => $r->id]);
//
//
//                    $callCount = DB::table($this->table)->where('id', $r->crm_id)->value('proc_stage');
                    $callCount = $order->proc_stage;
                    $settings = DB::table('company_elastix')->where('id', $r->id_campaign)->value('call_time');
                    $settings = json_decode($settings, true);
                    if (isset($settings[$callCount]) && $r->id_campaign != 22) {
                        $time = now()->addMinute($settings[$callCount]);
//                        DB::table($this->table)->where('id', $r->crm_id)->update(['proc_time' => $time]);
                        $order->proc_time = $time;
                    }

                    if ($r->id_campaign == 22) {
                        $time = now()->subSeconds(10);
//                        DB::table($this->table)->where('id', $r->crm_id)->update(['proc_time' => $time]);
                        $order->proc_time = $time;

                    }
                    ActionController::updateOrderProcStatus([$order], $statusIfFailed);


                    try {
                        $flag = CollectorLog::where('order_id', $r->crm_id)
                            ->where('type', CollectorLog::TYPE_AUTO)
                            ->where('user_id', 0)
                            ->noProcessed()
                            ->update(['processed' => 1]);
                        if ($flag) {
                            $log = new OrdersLog();
                            $log->order_id = $r->crm_id;
                            $log->user_id = 0;
                            $log->text = 'Коллектор не дозвонился';
                            $log->date = now();
                            $log->save();
                        }
//                        if (!$flag) {
//                            (new OrdersLog)->addOrderLog($r->crm_id, 'Установлен процессинг статус - Недозвон', ['status_id'   => 5, 'status_name' => 'Недозвон']);
//                        }
                    } catch (\Exception $exception) {
                        echo $exception->getMessage() . "\n";
                    }
                }

                $order->proc_callback_time = null;
                $order->save();

                $userId = null;
                if ($r->number) {
                    $userId = DB::table('users')
                        ->where('login_sip', $r->number)
                        ->value('id');
                }

                $сallProgressLogModel->addCallProgressLog($r->crm_id, $r->status, $r->recordingfile, $userId, $r->duration, $r->trunk, $r->start_time, $r->uniqueid, $r->entity);
                if ($entity['entity'] == 'order') {
                    $this->getProcessingStatusOrderApi($r->crm_id, 'order');
                }
            }

            foreach ($company as $c) {
                if ($c != 22) {
                    $callMaxCount = DB::table('company_elastix')->where('id', $c)->value('call_count');//AND target_status=0
                    DB::update('UPDATE `' . $this->table . '` SET `proc_status` = ?, `time_status_updated` = ? WHERE `proc_stage` >= ' . $callMaxCount . ' AND moderation_id=0 AND entity="order" AND target_status=0 AND proc_campaign=' . $c, [
                        5,
                        Carbon::now()
                    ]);
                }
            }
            echo json_encode($arrayLog) . "\n";
            #todo переделать запрос
        }
    }

    /**
     * Получаем результыта звонка (cron) |для холодных продаж
     */
    function getResultCallsElastixColdCalls($сallProgressLogModel, $entity)
    {
        $result = $this->apiElastixProcessing('getCalls', ['entity' => 'cold_call']);

        if ($result) {
            echo "---------Get calls--------\n";
            $arrayLog = [];
            $company = [];
            foreach ($result as $r) {

                $company[$r->id_campaign] = $r->id_campaign;
                $arrayLog[] = [
                    'order_id'    => $r->crm_id,
                    'elasctix_id' => $r->id,
                    'call_status' => $r->status
                ];
                //хп
                if ($r->status == 'Failure' || $r->status == 'NoAnswer' || $r->status == 'Abandoned') {

                    $orderExist = DB::table('orders')->where([['id', $r->crm_id], ['entity', 'cold_call']])->first();

                    if (!empty($orderExist)) {
                        $orderExist = DB::table('orders')->where([['id', $r->crm_id], ['entity', 'cold_call']])
                            ->update(['proc_status' => 5]);
                        DB::table('cold_call_lists')->where('order_id', $r->crm_id)
                            ->update(['proc_status' => 4]);
                    } else {
                        DB::table('cold_call_lists')->where('id', $r->crm_id)
                            ->update(['proc_status' => 4]);
                    }

                    $listResult = ColdCallResult::where('cold_call_list_id', $r->crm_id)
                        ->where('call_status', $r->status)->first();

                    if (!$listResult) {
                        //если отсутствует запись с входящим статусом в таблице результатов по записи листа создаем новую запись
                        ColdCallResult::create([
                            'cold_call_list_id' => $r->crm_id,
                            'call_status'       => $r->status,
                            'count_status'      => 1
                        ]);
                    } else {
                        //если статус существует увеличиваем количество такого статуса на 1
                        DB::table('cold_call_results')->where('call_status', $r->status)
                            ->increment('count_status', 1);
                    }
                }

                if ($r->status == 'ShortCall' || $r->status == 'Success') {

                    $orderExist = DB::table('orders')->where([['id', $r->crm_id], ['entity', 'cold_call']])->first();

                    if (!empty($orderExist)) {
                        $orderExist = DB::table('orders')->where([['id', $r->crm_id], ['entity', 'cold_call']])
                            ->update(['proc_status' => 3]);
                    }

                    DB::table('cold_call_lists')->where('id', $r->crm_id)
                        ->update(['proc_status' => 3]);
                    $listResult = ColdCallResult::where('cold_call_list_id', $r->crm_id)
                        ->where('call_status', $r->status)->first();

                    if (!$listResult) {
                        //если отсутствует запись с входящим статусом в таблице результатов по записи листа создаем новую запись
                        ColdCallResult::create([
                            'cold_call_list_id' => $r->crm_id,
                            'call_status'       => $r->status,
                            'count_status'      => 1
                        ]);
                    } else {
                        //если статус существует увеличиваем количество такого статуса на 1
                        DB::table('cold_call_results')->where('call_status', $r->status)
                            ->increment('count_status', 1);
                    }
                }

                $userId = null;
                if ($r->number) {
                    $userId = DB::table('users')
                        ->where('login_sip', $r->number)
                        ->value('id');
                }


                $сallProgressLogModel->addCallProgressLog($r->crm_id, $r->status, $r->recordingfile, $userId, $r->duration, $r->trunk, $r->start_time, $r->uniqueid, $r->entity);
            }


//            foreach ($company as $c) {
//                if ($c != 22) {
//                    $callMaxCount = DB::table('company_elastix')->where('id', $c)->value('call_count');
//                    DB::update('UPDATE `' . $this->table . '` SET `proc_status` = ? WHERE `proc_stage` >= ' . $callMaxCount . ' AND proc_campaign=' . $c, [5]);
//                }
//            }

            echo json_encode($arrayLog) . "\n";
        }
    }

    function getProcessingStatusOrderApi($id)
    {
//        $commentModel = new Comment;
//        $ordersOffersModel = new OrderProduct;
//        $data = $this->getStatusOrderApi2($id, $commentModel, $ordersOffersModel, $entity);
        $data = $this->getOrderForApi($id);
        return DB::insert('INSERT INTO orders_api (`order_id`, `data`, `date`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `data`=?, `date` =?', [
            $id,
            $data,
            now(),
            $data,
            now()
        ]);
    }

    function getStatusOrderApi2($id, $commentModel, $ordersOffersModel, $entity = '')
    {
        $result = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.name_first AS name', 'o.name_last AS surname', 'o.name_middle AS middle', 'o.target_status', 'cpl.status AS statusCall', 'cpl.date', 'o.target_id', 'o.phone', 'o.geo')
            ->leftJoin('call_progress_log as cpl', 'o.id', '=', 'cpl.order_id')
            ->orderBy('cpl.date');
        if ($entity) {
            $result = $result->where('cpl.entity', $entity);
        }
        $result = $result->where('o.id', $id)
            ->get();
        if (!$result) {
            return json_encode(['error' => 'incorrect ID']);
        }

        $data = [
            'id'      => $result[0]->id,
            'name'    => '',
            'surname' => '',
            'middle'  => '',
            'phone'   => $result[0]->phone,
            'status'  => $result[0]->target_status,
            //            'status_ext' => $result[0]->status_ext,
            'country' => $result[0]->geo,
            'comment' => $commentModel->getLastComment($result[0]->id),
            'calls'   => [],
            'offers'  => [],
            'targets' => [],
            'source'  => [],
        ];

        $targetValues = new TargetValue();

        switch ($result[0]->target_status) {
            case 0:
            case 4:
                if ($result[0]->statusCall !== NULL) {
                    foreach ($result as $r) {
                        $data['calls'][] = [
                            'status' => $r->statusCall,
                            'date'   => $r->date,
                        ];
                    }
                }
                break;
            case 1:
            case 2:
            case 3:
                list($data['targets'], $data['source']) = $targetValues->getValuesForApi($result[0]->id);

                if ($result[0]->target_status == 1) {
                    $data['offers'] = $ordersOffersModel->getOrderOffersById($result[0]->id);
                    $data['name'] = $result[0]->name;
                    $data['surname'] = $result[0]->surname;
                    $data['middle'] = $result[0]->middle;
                }
                break;
        }

        return json_encode($data);
    }

    public function getOrderForApi($orderId)
    {
        $targetValues = new TargetValue();
        $order = self::find($orderId);

        $data = [
            'id'               => $orderId,
            'name'             => null,
            'surname'          => null,
            'middle'           => null,
            'phone'            => null,
            'geo'              => null,
            'products'         => null,
            'offer_id'         => null,
            'proc_status'      => null,
            'proc_status_name' => null,
            'target_status'    => null,
            'target'           => null,
            'source'           => null,
            'final_target'     => null,
            'comments'         => null,
            'records'          => null,
            'moderation'       => null,
        ];

        if ($order) {
            $data['geo'] = $order->geo;
            $data['proc_status'] = $order->proc_status;
            $data['proc_status_name'] = $order->procStatus ? $order->procStatus->name : null;

            if ($order->proc_status != 10) {//10 - подозрительный

                $data['target_status'] = $order->target_status;
                $data['moderation'] = $order->moderation_id ? true : false;
                list($data['target'], $data['source']) = $targetValues->getValuesForApi($order->id);

                if ($order->target_status == 1) {
                    $allData = [
                        'name'          => $order->name_first,
                        'surname'       => $order->name_last,
                        'middle'        => $order->name_middle,
                        'phone'         => $order->phone,
                        'products'      => $this->getArrayProducts($order),
                        'offer_id'      => $order->offer ? $order->offer->offer_id : null,
                        'target_status' => $order->target_status,
                        'final_target'  => $order->final_target,
                        'comments'      => $this->getArrayComments($orderId),
                        'records'       => $this->getArrayRecords($order)
                    ];
                    $data = array_merge($data, $allData);
                }
            }
        }

        return json_encode($data);
    }

    private function getArrayProducts(Order $order)
    {
        $dataProducts = [];
        $orderProducts = OrderProduct::where('order_id', $order->id)->get();
        if ($orderProducts) {
            $products = $order->products->keyBy('id');

            foreach ($orderProducts as $product) {
                if (isset($products[$product->product_id])) {
                    $dataProducts[] = [
                        'id'      => $products[$product->product_id]->product_id,
                        'title'   => $products[$product->product_id]->title,
                        'removed' => $product->disabled,
                        'comment' => $product->comment,
                        'price'   => $product->price
                    ];
                }
            }
        }

        return $dataProducts;
    }

    private function getArrayComments($orderId)
    {
        $commentModel = new Comment();
        $comments = $commentModel->getComments($orderId, self::ENTITY_ORDER, 'comment');

        $dataComments = [];
        if ($comments->isNotEmpty()) {

            foreach ($comments as $comment) {
                $dataComments[] = [
                    'name'    => $comment->name,
                    'surname' => $comment->surname,
                    'login'   => $comment->login,
                    'text'    => $comment->text,
                    'date'    => $comment->date,
                ];
            }
        }

        return $dataComments;
    }

    private function getArrayRecords(Order $order)
    {
        $dataRecords = [];

        $records = CallProgressLog::with('user')->where('order_id', $order->id)->get();

        if ($order->entity == self::ENTITY_COLD_CALL) {
            $coldId = ColdCallList::where('order_id', $order->id)->value('id');
            $records = $records->merge(CallProgressLog::with('user')->where('order_id', $coldId)->get());
        }

        if ($records->isNotEmpty()) {
            $records = $records->sortBy('date');

            foreach ($records as $record) {
                $dataRecords[] = [
                    'date'     => $record->date,
                    'name'     => $record->user ? $record->user->name : null,
                    'surname'  => $record->user ? $record->user->surname : null,
                    'login'    => $record->user ? $record->user->login : null,
                    'status'   => $record->status,
                    'fileName' => $record->file,
                ];
            }
        }

        return $dataRecords;
    }

    public function getResultOrder($ids)
    {
        $result = [];

        $ids = json_decode($ids, true);

        if ($ids) {
            $orders = DB::table('orders_api')
                ->whereIn('order_id', $ids)
                ->pluck('data')->toArray();

            if ($orders) {
                foreach ($orders as $order) {
                    $result[] = json_decode($order, true);
                }
            }
        }

        return $result;
    }

    function getStatusOrderApi4($ids)
    {
        $ids = json_decode($ids);
        $array = [];
        if ($ids) {
            $result = DB::table('orders_api AS oa')->select('oa.data', 'oa.order_id')
                ->leftJoin('orders As o', 'o.id', '=', 'oa.order_id')
                ->whereIn('oa.order_id', $ids)
                ->where('o.proc_status', '!=', 10)//10 - подозрительный
                ->where('oa.data', '!=', '{"error":"incorrect ID"}')
                ->get();
            if ($result) {
                $comments = DB::table('comments')
                    ->select('text', 'order_id')
                    ->whereIn('order_id', $ids)
                    ->get();
                $comment = [];
                if ($comments) {
                    foreach ($comments as $com) {
                        $comment[$com->order_id][] = $com->text;
                    }
                }
                $i = 0;
                foreach ($result as $r) {
                    $array[$i] = json_decode($r->data);
                    $array[$i]->comments = [];
                    if (isset($comment[$array[$i]->id])) {
                        $array[$i]->comments = $comment[$array[$i]->id];
                    }
                    $i++;
                }
            }
        }
        return json_encode($array);
    }

    function getFilterModeration()
    {
        $filter = [
            'date-type'   => '',
            'date-start'  => '',
            'date-end'    => '',
            'owner'       => '',
            'country'     => '',
            'offers'      => '',
            'product'     => '',
            'status'      => '',
            'target'      => '',
            'phone-error' => '',
            'id'          => '',
            'name'        => '',
            'surname'     => '',
            'middle'      => '',
            'phone'       => '',
            'ip'          => '',
        ];
        $page = 0;
        $get = stristr($_SERVER['HTTP_REFERER'], '?');
        if ($get) {
            $get = str_replace('?', '', $get);
            $gets = explode('&', $get);

            if ($gets) {
                foreach ($gets as $g) {
                    $g = explode('=', $g);
                    if (isset($filter[$g[0]])) {
                        $filter[$g[0]] = $g[1];
                    }
                    if ($g[0] == 'page') {
                        $page = $g[1];
                    }
                }
            }
        }
        return [$page, $filter];
    }

    /**
     * Получаем все заказы
     * @param string $page Страница заказа
     * @param array $filter Фильтр заказа
     * @return array
     */
    function getOrders($filter)
    {
        $result = DB::table($this->table . ' AS o')
            ->select(
                'o.id', 'o.time_created', 'o.time_modified',
                'o.phone', 'o.proc_status', 'o.geo', 'o.partner_id',
                'o.partner_oid', 'o.offer_id', 'o.price_total AS price',
                'o.final_target', 'o.proc_status_2', 'o.final_target_user',
                'o.project_id', 'o.subproject_id', 'o.final_target_user', 'o.target_approve', 'o.entity',
                'o.target_user',
                //'tv.target_id', 'tv.track',
                //'u.name as userName', 'u.surname as userSurname',
                'o.time_status_updated'
            )
            ->where('o.moderation_id', '>', 0)
            ->where('target_status', 1)
            ->where('service', '!=', self::SERVICE_CALL_CENTER);

        // ->leftJoin('target_values as tv', 'tv.order_id', '=', 'o.id')
        // ->leftJoin('users as u', 'o.target_user', '=', 'u.id');

        if (auth()->user()->sub_project_id) {
            $result = $result->where('o.subproject_id', auth()->user()->sub_project_id);
        }

        if (auth()->user()->project_id) {
            $result = $result->where('o.project_id', auth()->user()->project_id);
        }

//        if (Auth::user()->project_id) {
//            $result = $result->where('o.project_id', Auth::user()->project_id);
//        }
//
//        if (Auth::user()->sub_project_id) {
//            $result = $result->where('o.subproject_id', Auth::user()->sub_project_id);
//        }

        if ($filter['product']) {
            $result = $result->leftJoin('order_products AS op', 'op.order_id', '=', 'o.id');
        }
        if ($filter['id']) {
            $result = $result->where('o.id', $filter['id'])
                ->where('o.id', '>', 0);
        }
        if ($filter['surname']) {
            $result->where('o.name_last', 'like', $filter['surname'] . '%');
        }
        if ($filter['phone']) {
            $result = $result->where('o.phone', 'like', '%' . $filter['phone']);
        }
        if ($filter['ip']) {
            $result = $result->where('o.host', 'like', $filter['ip'] . '%');
        }
        if ($filter['oid']) {
            $result = $result->where('o.partner_oid', $filter['oid'])
                ->where('o.partner_oid', '>', 0);
        }
        if ($filter['entity']) {
            $result = $result->where('o.entity', $filter['entity']);
        }
        if ($filter['initiator']) {
            $initiators = explode(',', $filter['initiator']);
            $result = $result->whereIn('o.target_user', $initiators)->where('handmade', 1);
        }
        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result = $result->whereIn('o.geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $result = $result->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $result = $result->whereIn('o.subproject_id', $subProject);
        }
        if ($filter['status']) {
            $statuses = explode(',', $filter['status']);
            $result = $result->whereIn('o.proc_status', $statuses);
        }
        if ($filter['grouping']) {
            $result = $result->where('o.proc_status', $filter['grouping']);
        }
        if ($filter['sub_status']) {
            $subStatuses = explode(',', $filter['sub_status']);
            $result = $result->whereIn('o.proc_status_2', $subStatuses);
        }
        if ($filter['target']) {
            $filter['target'] = str_replace(['5'], 0, $filter['target']);
            $target = explode(',', $filter['target']);
            $result = $result->whereIn('o.final_target', $target);
        }
        if (isset($filter['deliveries'])) {
            $targetsApprove = explode(',', $filter['deliveries']);
            $result = $result->whereIn('o.target_approve', $targetsApprove);
        }
//        if (isset($filter['track'])) {
//            $result = $result->where('tv.track', '!=', 0);
//        }
        if (isset($filter['track_filter'])) {
            $tracks = explode(',', $filter['track_filter']);
            $result = $result->addSelect('tv.track');
            $result->leftJoin('target_values as tv', 'tv.order_id', '=', 'o.id');
            $result = $result->whereIn('tv.track', $tracks);
        }
        if ($filter['partners']) {
            $partners = explode(',', $filter['partners']);
            $result = $result->whereIn('o.partner_id', $partners);
        }
        if ($filter['product']) {
            $offer = explode(',', $filter['product']);
            $result = $result->whereIn('op.product_id', $offer)
                ->where('op.disabled', 0);
        }
        if ($filter['products_count']) {
            $result = $result->where(DB::raw("(select count(order_products.id) from order_products where order_products.order_id = o.id and order_products.disabled = 0)"), $filter['products_count']);
        }
        if ($filter['offers']) {
            $offer = explode(',', $filter['offers']);
            $result = $result->whereIn('o.offer_id', $offer);

        }

        if ($filter['division']) {
            $divisions = explode(',', $filter['division']);
            $result = $result->whereIn('o.division_id', $divisions);

        }
        if ($filter['tag_source']) {
            $tags = explode(',', $filter['tag_source']);
            $result = $result->whereIn('o.tag_source', $tags);

        }
        if ($filter['tag_medium']) {
            $tags = explode(',', $filter['tag_medium']);
            $result = $result->whereIn('o.tag_medium', $tags);

        }
        if ($filter['tag_content']) {
            $tags = explode(',', $filter['tag_content']);
            $result = $result->whereIn('o.tag_content', $tags);

        }
        if ($filter['tag_campaign']) {
            $tags = explode(',', $filter['tag_campaign']);
            $result = $result->whereIn('o.tag_campaign', $tags);

        }
        if ($filter['tag_term']) {
            $tags = explode(',', $filter['tag_term']);
            $result = $result->whereIn('o.tag_term', $tags);

        }

        if ($filter['date-type'] && $filter['date_start'] && $filter['date_end']) {

            $filter['date_start'] = Carbon::parse($filter['date_start']);
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
            if ($filter['date_start'] <= $filter['date_end']) {
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
                    case 4:
                        $date = 'date';
                        break;
                    case 5:
                        $date = 'time_status_updated';
                        break;
                    case 6:
                        $date = 'date';
                        break;
                }
                if (isset($date) && ($filter['date-type'] == 1 || $filter['date-type'] == 3)) {
                    $result = $result->whereBetween('o.' . $date, [$filter['date_start'], $filter['date_end']]);
                }
                if (isset($date) && $filter['date-type'] == 5) {
                    $result = $result->whereBetween('o.' . $date, [$filter['date_start'], $filter['date_end']]);
                }

                if (isset($date) && $filter['date-type'] == 4) {
                    $result->whereBetween(DB::raw("(select max(comments.date) from comments where comments.order_id = o.id and comments.type = 'comment')"),
                        [$filter['date_start'], $filter['date_end']]);
                }
                if (isset($date) && $filter['date-type'] == 6) {
                    $result->whereBetween(DB::raw("(select max(comments.date) from comments where comments.order_id = o.id and comments.type = 'sms')"),
                        [$filter['date_start'], $filter['date_end']]);
                }
            }
        }
        $column = $filter['order_cell'] ? $filter['order_cell'] : 'id';
        $direction = $filter['order_sort'] ? $filter['order_sort'] : 'desc';
        if ($filter['order_cell'] == 'time_comment_added') {
            $result->addSelect(DB::raw("(select max(comments.date) from comments where comments.order_id = o.id and comments.type = 'comment') as max_comment"));
            $result = $result->orderBy('max_comment', $direction)->groupBy('o.id')->paginate(50);
        } elseif ($filter['order_cell'] == 'time_sms_send') {
            $result->addSelect(DB::raw("(select max(comments.date) from comments where comments.order_id = o.id and comments.type = 'sms') as max_sms"));
            $result = $result->orderBy('max_sms', $direction)->groupBy('o.id')->paginate(50);
        } else {
            $result = $result->orderBy('o.' . $column, $direction)->groupBy('o.id')->paginate(50);
        }


        $orderIds = [];
        $targetIds = [];
        $usersIds = [];
        if ($result) {
            foreach ($result as $order) {
                $orderIds[] = $order->id;
                $statuses[$order->proc_status] = $order->proc_status;
                $targetIds[$order->target_approve] = $order->target_approve;
                $usersIds[] = $order->target_user;
            }
        }
        $usersTarget = User::whereIn('id', $usersIds)->get(['id', 'name', 'surname'])->keyBy('id');

        $orderProducts = DB::table('order_products AS op')
            ->select('op.type', 'p.title', 'op.order_id')
            ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
            ->whereIn('op.order_id', $orderIds)
            ->where('op.disabled', 0);
        if (isset($filter['display_products'])) {
            $orderProducts->addSelect('op.price');
        }
        $orderProducts = $orderProducts->get();

        $targetValues = TargetValue::whereIn('order_id', $orderIds)->get([
            'order_id',
            'target_id',
            'track'
        ])->keyBy('order_id');

        $products = [];
        if ($orderProducts) {
            foreach ($orderProducts as $product) {
                $infoProduct = [
                    'name' => $product->title,
                    'type' => $product->type
                ];
                if (isset($filter['display_products'])) {
                    $infoProduct['price'] = $product->price;
                }
                $products[$product->order_id][] = $infoProduct;
            }
        }

        $targets = TargetConfig::whereIn('id', $targetIds)->get()->keyBy('id');

        $comments = DB::table('comments')
            ->select('order_id', DB::raw('MAX(date) as date'))
            ->where('type', 'comment')
            ->whereIn('order_id', $orderIds)
            ->groupBy('order_id')
            ->get()->keyBy('order_id');

        $smsMessages = DB::table('comments')
            ->select('order_id', DB::raw('MAX(date) as date'))
            ->where('type', 'sms')
            ->whereIn('order_id', $orderIds)
            ->groupBy('order_id')
            ->get()->keyBy('order_id');

        return [
            'comments'      => $comments,
            'smsMessages'   => $smsMessages,
            'orders'        => $result->appends(Input::except('page')),
            'orderProducts' => $products,
            'countOrder'    => $result->total(),
            'targets'       => $targets,
            'targetValues'  => $targetValues,
            'usersTarget'   => $usersTarget
        ];
    }

    function ordersForCollectors($filter, $all = false)
    {
        $result = Order::
        select(
            'orders.id', 'time_created', 'time_modified', 'name_last', 'name_first', 'name_middle', 'partner_oid',
            'phone', 'proc_status', 'geo', 'moderation_id', 'pr.name as projectName', 'spr.name as subProjectName')
            ->where('moderation_id', '>', 0)
            ->whereHas('procStatus', function ($query) {
                $query->whereIn('action', ['sent', 'at_department']);
            })
            ->leftJoin('projects AS pr', 'pr.id', '=', 'orders.project_id')
            ->leftJoin('projects AS spr', 'spr.id', '=', 'orders.subproject_id')
            ->where('target_status', 1)
            ->where('service', '!=', self::SERVICE_CALL_CENTER)
            ->where(function ($query) {
                $query->whereDoesntHave('collectorLogs', function ($query) {
                    $query->noProcessed();
                })
                    ->orWhereDoesntHave('collectorLogs');
            })
            ->with([
                'collectorLogs' => function ($q) {
                    $q->orderBy('updated_at', 'asc');
                },
                'comments'      => function ($q) {
                    $q->where('type', 'sms');
                    $q->select('*', DB::raw('FROM_UNIXTIME(date) AS updated_at'));
                },
                'collectorLogs.user',
                'comments.user',
                'procStatus'
            ]);

        CollectingRepository::filterCollectorOrders($result, $filter);
        $column = !empty($filter['order_cell']) ? $filter['order_cell'] : 'orders.id';
        $direction = !empty($filter['order_sort']) ? $filter['order_sort'] : 'desc';

        $orders = $all ? $result->get('orders.id') : collect();
        $result = $result->orderBy($column, $direction)->groupBy('orders.id')
            ->paginate(100);

        return [
            'orders'     => $result->appends(Input::except('page')),
            'ordersAll'  => $orders,
            'ordersIds'  => collect($result->items())->pluck('orders.id'),
            'countOrder' => $result->total(),
        ];
    }

    /**
     * Получаем все заказы дял страницы печати заказов
     * @param string $page Страница заказа
     * @param array $filter Фильтр заказа
     * @return array
     */
    function getOrdersForPrint($filter)
    {
        $result = DB::table($this->table . ' AS o')
            ->select(
                'o.id', 'o.time_created', 'o.time_modified',
                'o.phone', 'o.proc_status', 'o.geo', 'o.partner_id',
                'o.partner_oid', 'o.offer_id', 'o.price_total AS price',
                'o.final_target', 'o.proc_status_2', 'o.final_target_user',
                'o.project_id', 'o.subproject_id', 'o.final_target_user', 'o.target_approve')
            ->where('o.moderation_id', '>', 0)
            ->where('target_status', 1)
            ->where('service', '!=', self::SERVICE_CALL_CENTER);

        if (Auth::user()->project_id) {
            $result = $result->where('o.project_id', Auth::user()->project_id);
            $data['status'] = ProcStatus::where('project_id', auth()->user()->project_id)->where('action', 'to_print')
                ->first(['id']);
            $result->where('proc_status', $data['status']->id);
        } else {
            $data['status'] = ProcStatus::where('action', 'to_print')
                ->first(['id']);
            $result->where('proc_status', $data['status']->id);
        }

        if (Auth::user()->sub_project_id) {
            $result = $result->where('o.subproject_id', Auth::user()->sub_project_id);
        }

        if ($filter['id']) {
            $result = $result->where('o.id', $filter['id'])
                ->where('o.id', '>', 0);
        }

        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result = $result->whereIn('o.geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $result = $result->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $result = $result->whereIn('o.subproject_id', $subProject);
        }
        if ($filter['status']) {
            $statuses = explode(',', $filter['status']);
            $result = $result->whereIn('o.proc_status', $statuses);
        }
        if ($filter['sub_status']) {
            $subStatuses = explode(',', $filter['sub_status']);
            $result = $result->whereIn('o.proc_status_2', $subStatuses);
        }
        if ($filter['target']) {
            $target = explode(',', $filter['target']);
            $result = $result->whereIn('o.final_target', $target);
        }
        if ($filter['deliveries']) {
            $targetsApprove = explode(',', $filter['deliveries']);
            $result = $result->whereIn('o.target_approve', $targetsApprove);
        }
        if ($filter['track']) {
            $result = $result->where('tv.track', '!=', 0);
        }
        if (isset($filter['partners'])) {
            $partners = explode(',', $filter['partners']);
            $result = $result->whereIn('o.partner_id', $partners);
        }

        if ($filter['date-type'] && $filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
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
                $result = $result->whereBetween($date, [
                    date('Y-m-d H:i:s', $filter['date_start']),
                    date('Y-m-d H:i:s', $filter['date_end'])
                ]);//todo date
            }
        }
        $result = $result->orderBy('o.id', 'desc')
            ->paginate(50);

        return $result;
//            [
//                'orders'     => $result->appends(Input::except('page')),
//                'countOrder' => $result->total(),
//            ];
    }

    function getOrdersRequests($filter)
    {
        $countOnePage = 50;
        $result = DB::table($this->table . ' AS o')
            ->select(
                DB::raw('DISTINCT(o.id) AS id'), 'o.time_created', 'o.time_changed', 'o.time_modified', 'o.host',
                'o.phone', 'o.proc_status', 'o.proc_stage', 'o.target_status', 'o.proc_callback_time', 'o.geo',
                'o.partner_oid', 'o.proc_campaign', 'o.offer_id', 'o.price_total AS price', 'o.proc_time', 'o.target_user',
                'o.entity', 'o.name_first', 'o.name_last', 'o.moderation_time', 'o.moderation_id')
            ->where('service', '!=', self::SERVICE_SENDING);

        if ($filter['product']) {
            $result = $result->leftJoin('order_products AS op', 'op.order_id', '=', 'o.id');
        }
        //todo костыль
        if ($filter['company'] || Auth()->user()->company_id || Auth()->user()->campaign_id) {
            if ($filter['target']) {
                $result = $result->leftJoin('users AS u', 'u.id', '=', 'o.target_user');
            } else {
                $result = $result->leftJoin('orders_opened AS oo', "oo.order_id", '=', 'o.id')
                    ->leftJoin('users AS u', 'u.id', '=', 'oo.user_id')
                    ->groupBy('o.id');
            }

            if (auth()->user()->company_id && auth()->user()->campaign_id != 10 && auth()->user()->campaign_id != 66 && auth()->user()->company_id != 16 && auth()->user()->company_id != 15) {
                $result = $result->where('u.company_id', auth()->user()->company_id);
            }

            if (auth()->user()->company_id && auth()->user()->company_id == 15) {
                $result = $result->where('o.geo', 'es');
            }

            if (auth()->user()->company_id && auth()->user()->campaign_id == 66) {
                $result = $result->where('o.geo', 'id');
            }
            if (auth()->user()->campaign_id) {
                $result = $result->where('o.proc_campaign', auth()->user()->campaign_id);
            }

            if (auth()->user()->company_id == 16) {
                $result = $result->where('o.proc_campaign', '=', 71);
            }
        }
        if (auth()->user()->sub_project_id) {
            $result = $result->where('o.subproject_id', auth()->user()->sub_project_id);
        }
        if ($filter['id']) {
            $result = $result->where('o.id', $filter['id'])
                ->where('o.id', '>', 0);
        }
        if ($filter['name']) {
            $result->where('o.name_first', 'like', $filter['name'] . '%');
        }
        if ($filter['surname']) {
            $result->where('o.name_last', 'like', $filter['surname'] . '%');
        }
        if ($filter['middle']) {
            $result->where('o.name_middle', 'like', $filter['middle'] . '%');
        }
        if ($filter['target']) {
            if ($filter['target'] == 5) {
                $filter['target'] = 0;
            }
            $result = $result->where('o.target_status', $filter['target']);
        }

        if ($filter['product']) {
            $offer = explode(',', $filter['product']);
            $result = $result->whereIn('op.product_id', $offer);
        }
        if ($filter['offers']) {
            $offer = explode(',', $filter['offers']);
            $result = $result->whereIn('o.offer_id', $offer);

        }
        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result = $result->whereIn('o.geo', $country);
        }
        if ($filter['company']) {
            $company = explode(',', $filter['company']);
            $result = $result->whereIn('u.company_id', $company);
        }
        if ($filter['partners']) {
            $partners = explode(',', $filter['partners']);
            $result = $result->whereIn('o.partner_id', $partners);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $result = $result->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $project = explode(',', $filter['sub_project']);
            $result = $result->whereIn('o.subproject_id', $project);
        }
        if ($filter['group']) {
            $group = explode(',', $filter['group']);
            $result = $result->whereIn('o.proc_campaign', $group);
        }
        if ($filter['status']) {
            $status = explode(',', $filter['status']);
            $result = $result->whereIn('o.proc_status', $status);
        }
        if ($filter['phone']) {
            $result = $result->where('o.phone', 'like', $filter['phone'] . '%');
        }
        if ($filter['ip']) {
            $result = $result->where('o.host', 'like', $filter['ip'] . '%');
        }
        if ($filter['entity']) {
            $result = $result->where('o.entity', $filter['entity']);
        }
        if ($filter['oid']) {
            $result = $result->where('o.partner_oid', $filter['oid'])
                ->where('o.partner_oid', '>', 0);
        }
        if ($filter['user']) {
            $user = explode(',', $filter['user']);
            $result = $result->whereIn('o.target_user', $user);
        }
        if ($filter['not_available']) {
            $statuses = ProcStatus::where('action_alias', 'not-available')->pluck('id');
            $result = $result->whereIn('o.proc_status', $statuses);
        }
        if ($filter['cause_cancel']) {
            $result = $result->leftJoin(TargetValue::tableName() . ' AS tv', 'o.id', '=', 'tv.order_id')
                ->where('tv.values', 'like', '%{"cause":{"field_title"%"field_value":"' . $filter['cause_cancel'] . '",%')
                ->groupBy('o.id');

        }

        if ($filter['division']) {
            $division = explode(',', $filter['division']);
            $result = $result->whereIn('o.division_id', $division);

        }
        if ($filter['tag_source']) {
            $tags = explode(',', $filter['tag_source']);
            $result = $result->whereIn('o.tag_source', $tags);

        }
        if ($filter['tag_medium']) {
            $tags = explode(',', $filter['tag_medium']);
            $result = $result->whereIn('o.tag_medium', $tags);

        }
        if ($filter['tag_content']) {
            $tags = explode(',', $filter['tag_content']);
            $result = $result->whereIn('o.tag_content', $tags);

        }
        if ($filter['tag_campaign']) {
            $tags = explode(',', $filter['tag_campaign']);
            $result = $result->whereIn('o.tag_campaign', $tags);

        }
        if ($filter['tag_term']) {
            $tags = explode(',', $filter['tag_term']);
            $result = $result->whereIn('o.tag_term', $tags);

        }

        if ($filter['date-type'] && $filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
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
                $result = $result->whereBetween('o.' . $date, [$filter['date_start'], $filter['date_end']]);
            }
        }
        $result = $result->orderBy('o.id', 'desc')
            ->paginate($countOnePage);

        $orderIds = [];
        $orderIdsRequests = [];
        if ($result) {
            foreach ($result as $order) {
                if ($order->target_status != 1) {
                    $orderIds[] = $order->id;
                }
                $orderIdsRequests[] = $order->id;
            }
        }
        $orderProducts = DB::table('order_products AS op')
            ->select('op.type', 'p.title', 'op.order_id')
            ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
            ->whereIn('op.order_id', $orderIdsRequests)
            ->get();
        $targetValue = DB::table('target_values as tv')
            ->select('tv.values', 'tv.order_id', 'tc.alias', 'tc.name')
            ->leftJoin('target_configs as tc', 'tc.id', '=', 'tv.target_id')
            ->whereIn('tv.order_id', $orderIds)
            ->get();

        $products = [];
        if ($orderProducts) {
            foreach ($orderProducts as $product) {
                $infoProduct = [
                    'name' => $product->title,
                    'type' => $product->type
                ];
                $products[$product->order_id][] = $infoProduct;
            }
        }

        $cause = $this->getTargetValueForAllOrders($targetValue);

        return [
            'orders'        => $result->appends(Input::except('page')),
            'orderProducts' => $products,
            'cause'         => $cause,
            'countOrder'    => $result->total(),
        ];
    }

    public function getTargetValueForAllOrders($targetValue, $ordersOld = [])
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


    function doNotCalls($page, $filter, $paginationModel, $ordersOffersModel, $callProgressLogModel)
    {
        $skip = 0;
        $countOnePage = 100;
        if ($page) {
            $skip = ($page - 1) * $countOnePage;
        }
        $result = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.time_created', 'o.host', 'o.phone', 'co.name AS country')
            ->leftJoin('countries AS co', 'o.geo', '=', 'co.code');
        if ($filter['offers']) {
            $result = $result->leftJoin('orders_offers AS oo', 'o.id', '=', 'oo.order_id');
        }
        $result = $result->where('o.proc_status', 5);
        if ($filter['id']) {
            $result = $result->where('o.id', $filter['id']);
        }
        if ($filter['date-type'] && $filter['date-start'] && $filter['date-end'] && ($filter['date-start'] <= $filter['date-end'])) {
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
                $result = $result->whereBetween($date, [
                    date('Y-m-d H:i:s', $filter['date-start']),
                    date('Y-m-d H:i:s', $filter['date-end'])
                ]);//todo date
            }
        }
        if ($filter['offers']) {
            $offer = explode(',', $filter['offers']);
            $result = $result->whereIn('oo.offer_id', $offer);
        }
        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result = $result->whereIn('o.geo', $country);
        }
        if ($filter['phone']) {
            $result = $result->where('o.phone', 'like', $filter['phone'] . '%');
        }
        if ($filter['ip']) {
            $result = $result->where('o.host', 'like', $filter['ip'] . '%');
        }
        if ($filter['offers']) {
            $result = $result->groupBy('o.id');
        }
        $count = $result->count();
        $result = $result->skip($skip)
            ->take($countOnePage)
            ->orderBy('o.id', 'desc')
            ->get();
        $data = [];
        if ($result) {
            $ids = [];
            foreach ($result as $r) {
                $ids[] = $r->id;
            }
            $data['orders'] = $result;
        }
        return [
            'data'       => $data,
            'countOrder' => $count,
            'pagination' => $paginationModel->getPagination($page, $count, $countOnePage),
        ];
    }

    function setNotCallsCallback($id)
    {
        DB::table($this->table)->where('id', $id)
            ->where('proc_status', 5)
            ->update([
                'proc_status'         => 1,
                'proc_stage'          => 0,
                'pre_moderation_uid'  => auth()->user()->id,
                'pre_moderation_type' => 5,
                'pre_moderation_time' => now(),
                'time_status_updated' => Carbon::now()
            ]);
        (new OrdersLog)->addOrderLog($id, 'Установлен Процессинг статус В обработке', [
            'status_id'   => 1,
            'status_name' => 'В обработке'
        ]);
    }

    /**
     * Шаблоны фильтров дат
     * @param int $type Тип шаблона
     * @return array
     */
    function dateFilterTemplate($type)
    {//todo переделать все!
        if ($type) {
            $time = time();
            $date = date('Y-m-d', $time);
            switch ($type) {
                case 1:
                    {
                        $newDate = $this->differenceTime($date, 0);
                        $start = $newDate;
                        $end = $newDate;
                        break;
                    }
                case 2:
                    {
                        $start = new Carbon('first day of last month');
                        $start = $start->startOfDay()->format('d.m.Y');
                        $end = new Carbon('last day of last month');
                        $end = $end->endOfDay()->format('d.m.Y');
                        break;

                    }
                case 3:
                    {
                        $start = Carbon::now()->subMonth(3)->firstOfMonth()->format('d.m.Y');
                        $end = Carbon::parse('last day of last month')->format('d.m.Y');
                        break;
                    }
                case 4:
                    {

                    }
                case 5:
                    {
                        $start = Carbon::yesterday()->format('d.m.Y');
                        $end = Carbon::yesterday()->endOfDay()->format('d.m.Y');
                        break;
                    }
                case 6:
                    {

                    }
                case 7:
                    {

                    }
                case 8:
                    {

                    }
                case 9:
                    {

                        $start = (new Carbon())->startOfWeek()->format('d.m.Y');
                        $end = (new Carbon())->endOfDay()->format('d.m.Y');
                        break;
                    }
                case 10:
                    {
                        $start = (new Carbon())->startOfMonth()->format('d.m.Y');
                        $end = (new Carbon())->endOfDay()->format('d.m.Y');
                        break;
                    }
                case 11:
                    {
                        break;
                    }
            }
            return [
                'start' => $start,
                'end'   => $end,
            ];
        } else {
            exit;
        }
    }

    /**
     * Сичтаем разницу между днями
     * @param string $date Текущее время
     * @param int $diffTime Разница в секундах
     * @return string
     */
    private function differenceTime($date, $diffTime)
    {
        $date = $date . ' ' . '00:00';
        $d = new \DateTime($date);
        $timestamp = $d->getTimestamp();
        return date('d.m.Y', $timestamp - $diffTime);
    }

    /**
     * специально для модеров
     * заказ в роцесинге или нет
     * @param $id
     */
    public function getOrderFromProcessing($id)
    {
        return DB::table($this->table . ' AS o')->select('ce.name')
            ->leftJoin('company_elastix AS ce', 'ce.id', '=', 'o.proc_campaign')
            ->where([
                ['o.id', $id],
                ['o.proc_status', 2]
            ])->first();
    }

    public function setUserForOrders($id)
    {
        (new OrdersLog)->addOrderLog($id, 'Установлен процессинг статус - Контакт', [
            'status_id'   => 3,
            'status_name' => 'Контакт'
        ]);
        return DB::table($this->table)->where('id', $id)
            ->increment('proc_stage', 1, [
                'proc_status'         => 3,
                'time_changed'        => now(),
                'time_status_updated' => Carbon::now()
            ]);
    }

    public function statisticOneOperator($id, $filter)
    {
        if (!$filter['date_start']) {
            $filter['date_start'] = Carbon::now()->startOfMonth();
        } else {
            $filter['date_start'] = Carbon::parse($filter['date_start']);
        }

        if (!$filter['date_end']) {
            $filter['date_end'] = Carbon::today()->endOfDay();
        } else {
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        }
        $orders = DB::table($this->table)->select('target_status', DB::raw('COUNT(target_status) AS count'))
            ->where('time_created', '>=', $filter['date_start'])
            ->where('time_created', '<=', $filter['date_end'])
            ->where('target_user', $id);

        $types = DB::table('order_products AS op')
            ->select('op.type', DB::raw('COUNT(op.type) AS count'))
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->where('o.target_user', $id)
            ->whereIn('type', [1, 2, 4]);
        $time = DB::table('call_progress_log AS cpl')->select(
            DB::raw('SUM(cpl.talk_time) AS time')
        )
            ->where('cpl.user_id', $id)
            ->where('cpl.entity', 'order')
            ->where('date', '>=', $filter['date_start'])
            ->where('date', '<=', $filter['date_end']);

        $opened = DB::table('orders_opened AS oo')->select(
            DB::raw('COUNT(oo.id) AS count')
        )
            ->where('oo.user_id', $id)
            ->where('oo.date_opening', '>=', $filter['date_start'])
            ->where('oo.date_opening', '<=', $filter['date_end']);
        if ($filter['date_start'] && $filter['date_end']) {
            $types = $types->whereBetween('o.time_created', [$filter['date_start'], $filter['date_end']]);
        }
        $reportTime = DB::table('report_time')
            ->select(
                DB::raw('SUM(login_time_elastix) AS login_time_elastix'),
                DB::raw('SUM(login_time_crm) AS login_time_crm'),
                DB::raw('SUM(pause_time) AS pause_time'),
                DB::raw('SUM(order_time) AS order_time')
            )
            ->where('date', '<=', $filter['date_end'])
            ->where('date', '>=', $filter['date_start'])
            ->where('user_id', $id)
            ->first();

        $orders = collect($orders->groupBy('target_status')->get())->keyBy('target_status');
        $types = collect($types->groupBy('op.type')->get())->keyBy('type');
        $time = $time->first();
        $opened = $opened->first();
        $data = [
            'opened'             => $opened->count,
            'approve'            => isset($orders[1]) ? $orders[1]->count : 0,
            'failure'            => isset($orders[2]) ? $orders[2]->count : 0,
            'fake'               => isset($orders[3]) ? $orders[3]->count : 0,
            'up_sell'            => isset($types[1]) ? $types[1]->count : 0,
            'up_sell_2'          => isset($types[2]) ? $types[2]->count : 0,
            'cross_sell'         => isset($types[4]) ? $types[4]->count : 0,
            'login_time_elastix' => $reportTime->login_time_elastix,
            'login_time_crm'     => $reportTime->login_time_crm,
            'talk_time'          => $time->time,
            'pause_time'         => $reportTime->pause_time,
            'order_time'         => $reportTime->order_time
        ];
        return $data;
    }

    /**
     * @param $targetsFinalModel
     * @param $targetsModel
     * @param $npModel
     */
    public function ordersForMonitoring($targetsFinalModel, $targetsModel, $npModel, $post = false)
    {
        $time = Carbon::now()->subMinute('15');
        if ($post) {
            $time = Carbon::now()->subSeconds(10);
        }
        $orders = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.geo', 'o.target_id', 'o.time_modified', 'u.name', 'u.surname', 'o.target_status',
                'o.proc_callback_time', 'o.proc_status', 'c.currency', 'of.name AS offer')
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->leftJoin('users AS u', 'o.target_user', '=', 'u.id')
            ->leftJoin('countries AS c', 'o.geo', '=', DB::raw('LOWER(c.code)'))
            ->where('o.time_modified', '>=', $time)
            ->where('o.target_user', '>', 0)
            ->limit(100);

        if ($post) {
            $orders = collect($orders->orderBy('o.time_modified', 'asc')
                ->get())->keyBy('id');
        } else {
            $orders = collect($orders->orderBy('o.time_modified', 'desc')
                ->get())->keyBy('id');
        }

        $orderIds = [];
        if ($orders) {
            foreach ($orders as $order) {
                $orderIds[] = $order->id;
            }
        }


        $products = DB::table('order_products AS op')
            ->select('p.title', 'op.order_id', 'op.type', 'op.price')
            ->leftJoin('products AS p', 'op.product_id', '=', 'p.id')
            ->whereIn('op.order_id', $orderIds)
            ->get();

        if ($products) {
            foreach ($products as $product) {
                $type = '';
                switch ($product->type) {
                    case 1:
                        {
                            $type = '(Up sell)';
                            break;
                        }
                    case 2:
                        {
                            $type = '(Up sell 2)';
                            break;
                        }
                    case 4:
                        {
                            $type = '(Cross sell)';
                            break;
                        }
                }
                $currency = '';
                if (isset($orders[$product->order_id])) {
                    $currency = $orders[$product->order_id]->currency;
                }
                $orders[$product->order_id]->products[] = $product->title . ' - ' . $product->price . ' ' . $currency . ' ' . $type;
            }
        }

        foreach ($orders as &$order) {
            $order->target_final = $targetsFinalModel->getTargetFinal($order->id, $order->target_id, $targetsModel, $npModel);
        }
        return $orders->toArray();
    }

    /**
     * сохраняем данные по заказу
     */
    public function saveContactData($data, $orderId)
    {
        return DB::table($this->table)
            ->where('id', $orderId)
            ->update($data);
    }

    function getValueStatus($value)
    {
        switch ($value) {
            case 5:
                $value = 'Повтор';
                break;
            case 6:
                $value = 'Не корректный телефон';
                break;
            case 7:
                $value = 'Не корректные данные';
                break;
            case 8:
                $value = 'Сервис';
                break;
            case 9:
                $value = 'Нет в наличие';
                break;
            case 10:
                $value = 'Доставка не возможна';
                break;
            case 11:
                $value = 'Сопровождение';
                break;
            case 12:
                $value = 'Недозвон';
                break;
            case 13:
                $value = 'Не заказывал';
                break;
        }
        return $value;
    }

    public function moderationOrder($filter)
    {
        $skip = 0;
        $countOnePage = 20;
        if ($filter['page']) {
            $skip = ($filter['page'] - 1) * $countOnePage;
        }
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $orders = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.input_data', 'of.name AS offer', 'o.price_total AS price', 'o.host',
                'o.name_first AS name', 'o.name_last AS surname', 'o.name_middle AS middle', 'u.id as operator_id',
                'u.name AS operName', 'u.surname AS operSurname', 'o.time_created', 'o.time_modified', 'o.geo',
                'o.proc_status', 'o.target_status', 'o.repeat_id', 'o.phone', 'o.proc_campaign', 'o.target_cancel',
                'tc.options', 'u.company_id', 'comp.name as company_name', 'f.id as feedback_id',
                'f.created_at as feedback_created_at', 'o.project_id', 'o.subproject_id', 'o.target_user'
            )
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->leftJoin('users AS u', 'o.target_user', '=', 'u.id')
            ->leftJoin('target_configs AS tc', 'o.target_cancel', '=', 'tc.id')
            ->leftJoin('companies AS comp', 'comp.id', '=', 'u.company_id')
            ->leftJoin('feedback AS f', 'f.order_id', '=', 'o.id')
            ->where([['o.moderation_id', 0], ['o.entity', 'order']])
            ->where('o.project_id', '!=', 2)//todo костыль
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
            $orders = $orders->skip($skip)
                ->take($countOnePage)
                ->orderBy('o.id', 'desc')
                ->get();
        } else {
            $count = $orders->whereIn('target_status', [1, 2, 3])
                ->count();
            $orders = $orders->whereIn('o.target_status', [1, 2, 3])
                ->skip($skip)
                ->take($countOnePage)
                ->orderBy('o.id', 'desc')
                ->get();
        }
        $ids = [];
        if ($orders) {
            foreach ($orders as &$order) {
                $ids[] = $order->id;
//                $ordersOpened = DB::select('select oo.target_status_time, oo.user_id, u.name, u.surname from orders_opened as oo
//                                                left join orders as o on oo.order_id=o.id
//                                                left join users as u on u.id=oo.user_id
//                                                where oo.order_id = '. $order->id .'
//                                                group by oo.user_id');
//                $order->ordersOpened = $ordersOpened;
                if ($order->target_status == 1) {
                    $records = DB::table('call_progress_log AS cpl')
                        ->select('cpl.file', 'u.name', 'u.surname')
                        ->leftJoin('users AS u', 'cpl.user_id', '=', 'u.id')
                        ->where('cpl.entity', 'order')
                        ->where('cpl.order_id', $order->id)
                        ->get();
                    $order->records = $records;

                    $products = DB::table('order_products AS op')
                        ->select('p.title', 'op.price', 'op.type', 'op.comment', 'op.disabled', 'op.id')
                        ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
                        ->where('op.order_id', $order->id)
                        ->get();
                    $order->products = $products;

                    $order->storages = Project::where('parent_id', $order->project_id)
                        ->get();

                } elseif ($order->target_status == 2) {
                    $records = DB::table('call_progress_log AS cpl')
                        ->select('cpl.file', 'u.name', 'u.surname')
                        ->leftJoin('users AS u', 'cpl.user_id', '=', 'u.id')
                        ->where('cpl.entity', 'order')
                        ->where('cpl.order_id', $order->id)
                        ->get();
                    $order->records = $records;
                } elseif ($order->target_status == 3) {
                    $records = DB::table('call_progress_log AS cpl')
                        ->select('cpl.file', 'u.name', 'u.surname')
                        ->leftJoin('users AS u', 'cpl.user_id', '=', 'u.id')
                        ->where('cpl.entity', 'order')
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

        $causes = $this->getTargetValueForAllOrders($targetValue);

        $paginationModle = new Pagination();
        return [
            collect($orders)->keyBy('id'),
            $paginationModle->getPagination($filter['page'], $count, $countOnePage),
            $count,
            $causes
        ];
    }

    public function preModerationOrder($filter)
    {
        $skip = 0;
        $countOnePage = 10;
        if ($filter['page']) {
            $skip = ($filter['page'] - 1) * $countOnePage;
        }
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $orders = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.input_data', 'of.name AS offer', 'o.price_total AS price', 'o.host',
                'o.name_first AS name', 'o.name_last AS surname', 'o.name_middle AS middle', 'u.id as operator_id',
                'u.name AS operName', 'u.surname AS operSurname', 'o.time_created', 'o.time_modified', 'o.geo',
                'o.proc_status', 'o.target_status', 'o.repeat_id', 'o.phone', 'o.proc_campaign', 'o.target_cancel',
                'tc.options', 'u.company_id', 'comp.name as company_name', 'f.id as feedback_id',
                'f.created_at as feedback_created_at', 'o.project_id', 'o.subproject_id', 'pr.name AS project_name', 'spr.name AS sub_project_name'
            )
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->leftJoin('users AS u', 'o.target_user', '=', 'u.id')
            ->leftJoin('projects AS pr', 'pr.id', '=', 'o.project_id')
            ->leftJoin('projects AS spr', 'spr.id', '=', 'o.subproject_id')
            ->leftJoin('target_configs AS tc', 'o.target_cancel', '=', 'tc.id')
            ->leftJoin('companies AS comp', 'comp.id', '=', 'u.company_id')
            ->leftJoin('feedback AS f', 'f.order_id', '=', 'o.id')
            ->where([['o.moderation_id', 0], ['o.entity', 'order']])
            ->whereIn('proc_status', [4, 5, 6, 7, 11])
            ->where('o.target_status', 0)
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
                case 'repeat':
                    {
                        $orders = $orders->where('o.proc_status', 4);
                        break;
                    }
                case 'not-call':
                    {
                        $orders = $orders->where('o.proc_status', 5);
                        break;
                    }
                case 'not-data':
                    {
                        $orders = $orders->where('o.proc_status', 6);
                        break;
                    }
                case 'other-language':
                    {
                        $orders = $orders->where('o.proc_status', 7);
                        break;
                    }
                case 'incorrect_project' :
                    {
                        $orders = $orders->where('o.proc_status', 11);
                        break;
                    }
            }

        }

        $count = $orders->count();
        $orders = $orders->skip($skip)
            ->take($countOnePage)
            ->orderBy('o.id', 'desc')
            ->get();

        $statusIds = [];
        if ($orders) {
            foreach ($orders as &$order) {
                $statusIds[] = $order->proc_status;
                if ($order->proc_status == 4) {//повтор
                    $parent = $this->searchParentOrder($order->phone, $order->host);
                    if ($parent) {
                        $statusIds[] = $parent->proc_status;
                        $order->orderParent = $parent;
                        $order->orderParent->children = $this->searchAllChildren($order->phone, $order->host);
                    }
                } elseif ($order->proc_status == 5) {
                    $calls = DB::table('call_progress_log')->select('order_id', 'status', 'date')
                        ->where('entity', 'order')
                        ->where('order_id', $order->id)
                        ->get();
                    $data = [];
                    $callCount = 0;
                    if ($calls) {
                        foreach ($calls as $c) {
                            $callCount++;
                            $data[$c->status][] = $c->status . ' (' . $c->date . ')';
                        }
                    }
                    $order->callCount = $callCount;
                    $order->calls = $data;
                }
            }
        }
        $paginationModle = new Pagination();
        return [
            collect($orders)->keyBy('id'),
            $paginationModle->getPagination($filter['page'], $count, $countOnePage),
            $count,
            ProcStatus::whereIn('id', $statusIds)->get()->keyBy('id'),
        ];
    }

    protected function searchParentOrder($phone, $ip)
    {
        return DB::table($this->table . ' AS o')
            ->select('o.id', 'o.input_data', 'of.name AS offer', 'o.price_total AS price', 'o.host', 'o.name_first AS name',
                'o.name_last AS surname', 'o.name_middle AS middle', 'u.name AS operName', 'u.surname AS operSurname',
                'o.time_created', 'o.time_modified', 'o.geo', 'o.proc_status', 'o.target_status', 'o.repeat_id', 'o.phone'
            )
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->leftJoin('users AS u', 'o.target_user', '=', 'u.id')
            ->where('o.phone', $phone)
            ->where('o.repeat_id', 0)
            ->orderBy('o.id', 'desc')
            ->first();
    }

    protected function searchAllChildren($phone, $ip)
    {
        $orders = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.input_data', 'o.host', 'o.name_first AS name', 'o.name_last AS surname',
                'o.name_middle AS middle', 'o.time_created', 'o.time_modified', 'o.geo',
                'o.proc_status', 'o.target_status', 'o.repeat_id', 'o.phone'
            )
            ->where('o.target_status', 0)
            ->where('o.proc_status', 4)
            ->where('o.phone', $phone)
            ->where('o.repeat_id', '>', 0)
            ->get();
        return $orders;
    }

    public function moderationChangePhoneAndCountry($data, $phoneCorrectionService)
    {
        $result = [
            'errors'  => 0,
            'success' => 0
        ];
        $validator = \Validator::make($data, [
            'id'      => 'required|numeric',
            'phone'   => 'required|numeric',
            'country' => 'required|max:5',
            'price'   => 'required|numeric|min:1'
        ]);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            $result['message'] = trans('alerts.validation-error');
            return $result;
        }
        list($phone, $phoneError) = $phoneCorrectionService->customCorrectionForCountry($data['country'], $data['phone']);
        if ($phoneError) {
            $result['errors'] = ['phone' => true];
            $result['message'] = trans('alerts.phone-invalid');
            return $result;
        }

        $products = DB::table('order_products')->where('order_id', $data['id'])->where('disabled', 0)->get();
        if (count($products) == 1) {
            DB::table('order_products')->where('order_id', $data['id'])->update(['price' => $data['price']]);
        }

        DB::table($this->table)
            ->where('id', $data['id'])
            ->update([
                'phone'               => $phone,
                'geo'                 => mb_strtolower($data['country']),
                'time_changed'        => now(),
                'proc_status'         => 1,
                'pre_moderation_uid'  => auth()->user()->id,
                'pre_moderation_type' => 6,
                'pre_moderation_time' => now(),
                'price_total'         => $data['price']
            ]);
        $result['success'] = $phone;
        $result['message'] = trans('alerts.order-successfully-processed');
        $this->getProcessingStatusOrderApi($data['id']);
        return $result;
    }

    public function cancelAsRepeatAndSetLog($ids)
    {
        if ($ids) {
            $result = DB::table($this->table)
                ->whereIn('id', $ids)
                ->update([
                    'proc_status'         => 3,
                    'target_status'       => 3,
                    'time_modified'       => now(),
                    'moderation_id'       => auth()->user()->id,
                    'moderation_time'     => now(),
                    'pre_moderation_uid'  => auth()->user()->id,
                    'pre_moderation_type' => 4,
                    'pre_moderation_time' => now(),
                    'time_status_updated' => Carbon::now()
                ]);
            if ($result) {
                $log = [];
                foreach ($ids as $id) {
                    $log[] = [
                        'order_id'    => $id,
                        'user_id'     => auth()->user()->id,
                        'text'        => 'Промодерирован.Цель - аннулирован(групповое аннулирование)',
                        'status_id'   => 3,
                        'status_name' => 'Контакт',
                        'date'        => now(),
                    ];
                }
                if ($log) {
                    DB::table('orders_log')->insert($log);
                }
                return true;
            }
        }
        return false;
    }

    public function cancelOneOrder($id)
    {
        (new OrdersLog)->addOrderLog($id, 'Установлен Процессинг статус - Контакт', [
            'status_id'   => 3,
            'status_name' => 'Контакт'
        ]);
        $procStatus = Order::find($id)->proc_status ?? 0;
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'proc_status'         => 3,
                'target_status'       => 3,
                'time_modified'       => now(),
                'moderation_id'       => auth()->user()->id,
                'moderation_time'     => now(),
                'time_status_updated' => Carbon::now(),
                'pre_moderation_uid'  => auth()->user()->id,
                'pre_moderation_type' => $procStatus,
                'pre_moderation_time' => now()
            ]);
    }

    public function getCountriesOrders($data)
    {
        if ($data) {
            $ids = [];
            foreach ($data as $order) {
                if ($order['status'] == 'Success') {
                    $ids[] = $order['crm_id'];
                }
            }
            $countries = collect(DB::table($this->table)
                ->select('id', 'geo')
                ->whereIn('id', $ids)
                ->get())->keyBy('id');
            return $countries;
        }
    }

    public function setModeration(Order $order)
    {
        if ($order->target_status == 1 && !self::checkProducts($order)) {
            return [
                'success' => false,
                'error'   => trans('alerts.product-out-stock'),
            ];
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

    public static function checkProducts(Order $order)
    {
        if ($order->products->isNotEmpty()) {
            foreach ($order->products as $product) {
                $res = StorageContent::checkAmountProduct($product->id, $order->subproject_id);

                if (!$res) {
                    return false;
                }
            }
        }

        return true;
    }

    public function repeatAsNotRepeat($ids)
    {
        return DB::table($this->table)
            ->whereIn('id', $ids)
            ->update([
                'proc_status'         => 1,
                'time_status_updated' => Carbon::now(),
                'pre_moderation_uid'  => auth()->user()->id,
                'pre_moderation_type' => 4,
                'pre_moderation_time' => now(),
            ]);
    }

    public function getOrdersByWeight($ordersWeight)
    {
        $data = [];
        if ($ordersWeight) {
            foreach ($ordersWeight as $orders) {
                $data[$orders['weight']][$orders['id_campaign']] = [
                    'campaign' => $orders['id_campaign'],
                    'count'    => $orders['quantity'],
                ];
            }
        }
        ksort($data);
        return $data;
    }

    public function getOrdersInProcessing()
    {
        $orders = DB::table($this->table)
            ->select(
                DB::raw('COUNT(id) as count'),
                'proc_campaign AS id_campaign'
            )
            ->whereIn('proc_status', [1, 2])
            ->where('target_status', 0)
            ->groupBy('proc_campaign')
            ->get();


        return $orders;
    }

    public function getOrderToday()
    {
        $time = Carbon::today();
        $orders = DB::table($this->table)
            ->select(
                DB::raw('COUNT(id) as count'),
                'proc_campaign AS id_campaign'
            )
            ->where('time_created', '>=', $time)
            ->groupBy('proc_campaign')
            ->get();


        return $orders;
    }

    public function getOrdersByPhone($phone)
    {
        $result = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.partner_oid', 'o.phone', 'o.price_total', 'o.proc_status', 'o.proc_stage',
                'o.proc_callback_time', 'o.target_id', 'o.target_user', 'o.target_status', 'co.currency',
                'o.name_first AS name', 'o.name_last AS surname', 'o.name_middle AS middle', 'co.name AS country',
                'o.geo', 'o.proc_campaign', 'of.name AS offer_name', 'o.offer_id', 'o.time_created', 'o.time_modified',
                'u.name AS operName', 'u.surname AS operSurname', 'camp.name AS campaign', 'o.proc_time', 'o.proc_callback_time')
            ->leftJoin('users AS u', 'u.id', '=', 'o.target_user')
            ->leftJoin('company_elastix AS camp', 'camp.id', '=', 'o.proc_campaign')
            ->leftJoin('countries AS co', 'o.geo', '=', 'co.code')
            ->leftJoin('offers AS of', 'o.offer_id', '=', 'of.id')
            ->where('o.phone', 'like', $phone . '%')
            ->limit(50)
            ->get();
        if ($result) {
            foreach ($result as &$order) {
                $order->products = DB::table('order_products AS op')
                    ->select('op.type', 'p.title AS name')
                    ->leftJoin('products AS p', 'p.id', '=', 'op.product_id')
                    ->where('op.order_id', $order->id)
                    ->get();
                $targetValue = DB::table('targets_final')
                    ->select('value', 'order_id')
                    ->whereIn('status', [2, 3])
                    ->where('order_id', $order->id)
                    ->first();
                if ($targetValue) {
                    $order->cause = $this->getValueStatus($targetValue->value);
                } else {
                    $order->cause = '';
                }

            }
        }
        return $result;
    }

    /**
     * создание заказа от входящей линии
     */
    public function incomingCallCreateOrder($data)
    {

        $result['status'] = false;
        $result['target_id'] = false;
        $result['geo'] = false;
        $validator = \Validator::make($data, [
            'name'    => 'required|max:100|min:2',
            'surname' => 'required|max:100|min:2',
            'middle'  => 'max:100|min:2',
            'country' => 'required|max:5|min:2',
            'phone'   => 'required|numeric|digits_between:5,25',
        ]);
        if ($validator->fails()) {
            $result['errors'] = $validator->errors();
            return $result;
        }

        list($phone, $phoneError) = (new PhoneCorrectionService())->customCorrectionForCountry($data['country'], $data['phone']);

        if ($phoneError) {
            $result['errors'] = [
                'phone'   => ['Номер не соответствует стране'],
                'country' => ['Страна не соответствует номеру']
            ];
            return $result;
        }


        $insertData = [
            'geo'           => $data['country'],
            'name_first'    => $data['name'],
            'name_last'     => $data['surname'],
            'name_middle'   => $data['middle'],
            'age'           => $data['age'],
            'gender'        => $data['gender'],
            'phone'         => $phone,
            'phone_input'   => $data['phone'],
            'price_total'   => 0,
            'price_input'   => 0,
            'time_created'  => now(),
            'time_modified' => now(),
            'time_changed'  => now(),
            'proc_status'   => 3,
            'proc_campaign' => auth()->user()->campaign_id,
            'target_id'     => 0,
            'target_user'   => auth()->user()->id,
            'entity'        => 'order'
        ];

        $result['status'] = \DB::table($this->table)->insertGetId($insertData);
        $result['target_id'] = $insertData['target_id'];
        $result['geo'] = $insertData['geo'];
        return $result;
    }

    public function setStatus($status, $orderId)
    {
        return DB::table($this->table)
            ->where('id', $orderId)
            ->update(['proc_status' => $status, 'time_changed' => now(), 'time_status_updated' => Carbon::now()]);
    }

    public function getSuspiciousOrders($filter)
    {
        $countOnePage = 100;
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $orders = DB::table($this->table . ' AS o')
            ->select('o.id', 'o.geo', 'o.entity', 'u.name', 'u.surname', 'comp.name AS company', 'o.target_status', 'u.login_sip', 'o.time_modified')
            ->leftJoin('comments AS c', 'o.id', '=', 'c.order_id')
            ->leftJoin('users AS u', 'u.id', '=', 'c.user_id')
            ->leftJoin('companies AS comp', 'u.company_id', '=', 'comp.id')
            ->where('o.proc_status', 10)//10 - подозрительный
            ->where('c.type', 'suspicious')
            ->whereBetween('o.time_modified', [$filter['date_start'], $filter['date_end']]);


        if (auth()->user()->company_id) {
            $orders = $orders->where('u.company_id', auth()->user()->company_id);
        }
        if ($filter['id']) {
            $orders = $orders->where('o.id', $filter['id']);
        }
        if ($filter['user']) {
            $users = explode(',', $filter['user']);
            $orders = $orders->whereIn('u.id', $users);
        }
        if ($filter['company']) {
            $company = explode(',', $filter['company']);
            $orders = $orders->whereIn('u.company_id', $company);
        }
        if ($filter['countries']) {
            $countries = explode(',', $filter['countries']);
            $orders = $orders->whereIn('o.geo', $countries);
        }
        $result = $orders
            ->orderBy('o.time_modified', 'desc')
            ->groupBy('o.id')
            ->paginate($countOnePage);

        return [
            'orders' => $result->appends(Input::except('page')),
            'count'  => $result->total(),
        ];
    }

    public function changeCampaign($id, $campaign)
    {
        if (!$campaign) {
            return false;
        }
        return DB::table($this->table)
            ->where('id', $id)
            ->update([
                'proc_status'         => 1,
                'proc_campaign'       => $campaign,
                'time_status_updated' => Carbon::now(),
                'pre_moderation_uid'  => auth()->user()->id,
                'pre_moderation_type' => 7,
                'pre_moderation_time' => now()
            ]);
    }

    public function getCountOrderModeration($filter)
    {
        $filter['date_start'] = Carbon::parse($filter['date_start']);
        $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        $procStatus = DB::table($this->table . ' AS o')
            ->select('o.proc_status', DB::raw('COUNT(o.proc_status) AS count'))
            ->leftJoin('users AS u', 'u.id', '=', 'o.target_user')
            ->whereIn('o.proc_status', [4, 5, 6, 7, 11])
            ->where('o.target_status', 0)
            ->where('o.moderation_id', 0)
            ->where('o.entity', 'order')
            ->whereBetween('o.time_modified', [$filter['date_start'], $filter['date_end']]);
        $targetStatus = DB::table($this->table . ' AS o')
            ->select('o.target_status', DB::raw('COUNT(o.target_status) AS count'))
            ->leftJoin('users AS u', 'u.id', '=', 'o.target_user')
            ->whereIn('o.target_status', [1, 2, 3])
            ->where('o.project_id', '!=', 2)//todo костыль
            ->where('o.moderation_id', 0)
            ->where('o.entity', 'order')
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

    public function saveModeratorChanges($request) //todo добавить логи и время изменения статуса
    {

        $order = NULL;
        if (!empty($request->input('orderId'))) {
            $order = Order::find(intval($request->input('orderId')));
        }

        if (!empty($order)) {
            if (!empty($request->input('action'))) {

                if ($request->input('action') == 'stop') {
                    $order->proc_status = 11;
                    $result = $this->apiElastixProcessing2('deleteCall', [
                        'id'     => $order->id,
                        'entity' => $order->entity
                    ]);
                    if ($result->status == 200 && $order->save()) {
                        $html = view('ajax.order.upload-call')->render();
                        return response()->json(['html' => $html, 'callStopped' => true]);
                    }
                }
                if ($request->input('action') == 'add_call') {
                    if ($order->entity == 'cold_call') {
                        $order_phone = "11" . $order->phone;
                    }
                    $result = $this->apiElastixProcessing2('addCallOne', [
                        'id'      => $order->id,
                        'company' => $order->proc_campaign,
                        'phone'   => !empty($order_phone) ? $order_phone : $order->phone,
                        'weight'  => $order->proc_priority,
                        'entity'  => $order->entity,
                    ]);

                    if ($result->status == 200) {
                        $order->proc_status = 2;
                        $html = view('ajax.order.cancel-call')->render();

                        if ($order->save()) {
                            return response()->json(['html' => $html, 'call_added' => true]);
                        }
                    }
                }
            }
            if (!empty($request->input('campaign'))) {
                $result = NULL;
                if ($order->proc_status == 2) {
                    $result = $this->apiElastixProcessing2('changeQueueOneOrder', [
                        'id'     => $order->id,
                        'entity' => $order->entity,
                        'queue'  => intval($request->input('campaign'))
                    ]);
                }
                $order->proc_campaign = $request->input('campaign');
                if ($order->save()) {

                    $html = view('ajax.order.change_operators_options', [
                        'operators' => User::where('campaign_id', $request->input('campaign'))->get(),
                    ])->render();
                    return response()->json([
                        'html'             => $html,
                        'campaign_changed' => true,
                        'status'           => ['pbx_status' => $result]
                    ]);
                }
            }

            if (!empty($request->input('priority'))) {

                // $order->proc_priority = intval($request->input('priority'));
                if ($order->proc_status == 2) {
                    $result = $this->apiElastixProcessing2('changeCallOnePriority', [
                        'id'       => $order->id,
                        'entity'   => $order->entity,
                        'priority' => intval($request->input('priority'))
                    ]);

                    if ($result->status == 200) {
                        return response()->json(['changed_priority' => true]);
                    }
                } else {
                    $order->proc_priority = $request->input('priority');
                    $order->save();
                    return response()->json(['changed_priority' => true]);
                }
            }


            if (!empty($request->input('callback_date')) || !empty($request->input('add_call_now')) || !empty($request->input('operator'))) {

                $resultStatus = [];
                if (!empty($request->input('operator')) && !empty($request->input('callback_date'))) {
                    if ($order->proc_status == 2) {

                        $result = $this->apiElastixProcessing2('deleteCall', [
                            'id'     => $order->id,
                            'entity' => $order->entity
                        ]);

                        if ($result->status == 200) {
                            $result2 = $this->apiElastixProcessing2('addCallWithUserAndCallBack', [
                                'id'        => $order->id,
                                'entity'    => $order->entity,
                                'operator'  => $request->input('operator'),
                                'company'   => $order->proc_campaign,
                                'phone'     => $order->phone,
                                'date_time' => !empty($request->input('callback_date')) ? strtotime($request->input('callback_date')) : $request->input('add_call_now'),
                            ]);

                            if ($result2->status == 200) {
                                $resultStatus = ['operator_callback_changed' => true];
                                return response()->json(['status' => $resultStatus]);
                            }
                        }

                    } else {
                        //  var_dump($request->input('callback_date')).die();
                        $result = $this->apiElastixProcessing2('addCallWithUserAndCallBack', [
                            'id'        => $order->id,
                            'entity'    => $order->entity,
                            'operator'  => $request->input('operator'),
                            'company'   => $order->proc_campaign,
                            'phone'     => $order->phone,
                            'date_time' => !empty($request->input('callback_date')) ? $request->input('callback_date') : $request->input('add_call_now'),
                        ]);


                        $order->proc_status = 2;
                        if ($result->status == 200 && $order->save()) {
                            $resultStatus = ['operator_callback_changed' => true];
                            return response()->json(['status' => $resultStatus]);
                        }
                    }
                }

                if (!empty($request->input('operator')) && !empty($request->input('add_call_now'))) {
                    if ($order->proc_status == 2) {

                        $result = $this->apiElastixProcessing2('deleteCall', [
                            'id'     => $order->id,
                            'entity' => $order->entity
                        ]);

                        if ($result->status == 200) {
                            $result2 = $this->apiElastixProcessing2('addCallWithUserAndCallBack', [
                                'id'        => $order->id,
                                'entity'    => $order->entity,
                                'operator'  => $request->input('operator'),
                                'company'   => $order->proc_campaign,
                                'phone'     => $order->phone,
                                'date_time' => time(),
                            ]);

                            if ($result2->status == 200) {
                                $resultStatus = ['operator_callback_changed' => true];
                                $html = view('ajax.order.cancel-call')->render();
                                return response()->json(['status' => $resultStatus, 'html' => $html]);
                            }
                        }

                    } else {
                        $result = $this->apiElastixProcessing2('addCallWithUserAndCallBack', [
                            'id'        => $order->id,
                            'entity'    => $order->entity,
                            'operator'  => $request->input('operator'),
                            'company'   => $order->proc_campaign,
                            'phone'     => $order->phone,
                            'date_time' => time(),
                        ]);

                        $order->proc_status = 2;
                        if ($result->status == 200 && $order->save()) {
                            $resultStatus = ['operator_callback_changed' => true];
                            $html = view('ajax.order.cancel-call')->render();
                            return response()->json(['status' => $resultStatus, 'html' => $html]);
                        }
                    }
                }


                if (!empty($request->input('operator')) && $order->proc_status == 2) {
                    $result = NULL;
                    if ($order->proc_status == 2) {
                        $result = $this->apiElastixProcessing2('changeCallOperator', [
                            'id'       => $order->id,
                            'entity'   => $order->entity,
                            'operator' => intval($request->input('operator'))
                        ]);
                        if ($result->status == 200) {
                            $resultStatus = ['operator_changed' => true];

                        }
                    }
                }
                if (!empty($request->input('operator')) && $order->proc_status !== 2) {

                    $resultStatus = ['order_not_in_processing' => true];
                }

                if (!empty($request->input('callback_date'))) {

                    if ($order->proc_status == 2) {
                        $result = $this->apiElastixProcessing2('deleteCall', [
                            'id'     => $order->id,
                            'entity' => $order->entity
                        ]);
                        if ($result->status == 200) {
                            $resultStatus = ['call_back_time_changed' => true];
                        }
                    } else {
                        $order->proc_callback_time = time($request->input('callback_date'));
                        $order->proc_time = time($request->input('callback_date'));
                        if ($order->save()) {
                            $resultStatus = ['call_back_time_changed' => true];
                        }

                    }
                }
                if (!empty($request->input('add_call_now'))) {
                    if ($order->proc_status == 2) {
                        $result = $this->apiElastixProcessing2('deleteCall', [
                            'id'     => $order->id,
                            'entity' => $order->entity
                        ]);
                        if ($result->status == 200) {
                            $result = $this->apiElastixProcessing2('addCallNowWithoutUser', [
                                'id'      => $order->id,
                                'company' => $order->proc_campaign,
                                'phone'   => $order->phone,
                            ]);
                            if ($result->status == 200) {
                                $html = view('ajax.order.cancel-call')->render();
                                $resultStatus = ['call_added_now' => true, 'html' => $html];
                            }
                        }
                    } else {
                        $result = $this->apiElastixProcessing2('addCallNowWithoutUser', [
                            'id'      => $order->id,
                            'company' => $order->proc_campaign,
                            'phone'   => $order->phone,
                            'entity'  => $order->entity,
                        ]);
                        $order->proc_status = 2;
                        $order->save();
                        if ($result->status == 200) {
                            $html = view('ajax.order.cancel-call')->render();
                            $resultStatus = ['call_added_now' => true, 'html' => $html];
                        }
                    }
                }

                return response()->json(['status' => $resultStatus]);
            }

            /*change call logic*/
            if (!empty($request->input('stage'))) {
                $order->proc_stage = intval($request->input('stage'));
                if ($order->save()) {
                    return response()->json(['changed_stage' => true]);
                }
            }
        }
    }

    public function searchOrderById($id)
    {
        $order = DB::table('orders')
            ->select('id')
            ->where('id', 'LIKE', '%' . $id . '%')->get();
        return $order;
    }

    public static function searchOrderRedemption($filter)
    {
        $i = 0;
        foreach ($filter as $item) {
            if (!$item) {
                $i++;
            }
        }
        if ($i == count($filter) - 1) {
            return [];
        }

        $query = self::with('procStatus', 'country', 'subProject', 'getTargetValue')
            ->moderated()
            ->targetApprove()
            ->checkAuth()
            ->where('pass_id', 0);

        if ($filter['track']) {
            $query->whereHas('getTargetValue', function ($q) use ($filter) {
                $q->where('track', 'like', '%' . $filter['track'] . '%');
            });
        }
        if ($filter['id']) {
            $query->where('id', 'like', '%' . $filter['id'] . '%');
        }
        if ($filter['phone']) {
            $query->where('phone', 'like', '%' . $filter['phone'] . '%');
        }
        if ($filter['surname']) {
            $query->where('name_last', 'like', '%' . $filter['surname'] . '%');
        }
        if ($filter['index']) {
            $query->whereHas('getTargetValue', function ($q) use ($filter) {
                $q->whereRaw("JSON_CONTAINS(json_extract(`values`, \"$.postal_code\"), '\"" . $filter['index'] . "\"', \"$.field_value\") ");
            });
        }
        if ($filter['type'] == 'sending') {
            $query->where('pass_send_id', 0);
        } else {
            $query->where('pass_send_id', '!=', 0);
            $query->whereHas('passSend', function ($q) {
                $q->where("active", 0);
            });
        }

        return $query->limit(10)->get();
    }

    public static function findOrderByTrack($filter, $send = false)
    {
        $i = 0;
        foreach ($filter as $item) {
            if (!$item) {
                $i++;
            }
        }
        if ($i == count($filter)) {
            return null;
        }

        $query = self::moderated()
            ->checkAuth()
            ->targetApprove()
            ->where('pass_id', 0);
        if ($send) {
            $query->where('pass_send_id', 0);
        } else {
            $query->where('pass_send_id', '!=', 0);
        }
        if ($filter['order_id']) {
            $query->where('id', $filter['order_id']);
        }
        if ($filter['track']) {
            $query->whereHas('getTargetValue', function ($q) use ($filter) {
                $q->where('track', $filter['track']);
            });
        }
        if ($filter['pass']) {
            $query->whereHas('orderPass', function ($q) use ($filter) {
                $q->where('pass_id', $filter['pass']);
            });
        }
        return $query->get();
    }

    public function saveProcStatuses($order, $statuses, $request)
    {
        $procStatus = ProcStatus::find($request->proc_status);
        $request['action'] = $procStatus->action;
        $request['status'] = $request->proc_status;
        $request['orders'] = [$order->id];

        return (new ActionController)->runActionAjax($request);

    }

    public function deleteAllProducts()
    {
//        try {
        $orderProductModel = new OrderProduct();
        if ($this->products->isNotEmpty()) {
            foreach ($this->products as $product) {
                if (!$product->pivot->disabled) {
                    $orderProductModel->deleteProductFromOrder($product->pivot->id, new Order(), new OrdersLog());
                }
            }
        }
        return true;
//        } catch (\Exception $exception) {
//            return false;
//        }
    }

    public function saveProducts($products)
    {
        if ($products) {
            $orderLog = new OrdersLog();
            foreach ($products as $product) {
                $newProduct = new OrderProduct();
                $newProduct->order_id = $this->id;
                $newProduct->product_id = $product['product_id'];
                $newProduct->price = $product['product_price'];
                $newProduct->type = 3;
                $newProduct->save();

                $productName = $newProduct->product ? $newProduct->product->title : $product['product_id'];

                $orderLog->addOrderLog($this->id, 'Товар ' . $productName . ' был добавлен');
            }

            $price = DB::table(OrderProduct::tableName())->where('order_id', $this->id)
                ->where('disabled', 0)
                ->sum('price');

            self::where('id', $this->id)
                ->update([
                    'price_total'    => $price,
                    'price_products' => $price
                ]);
            $orderLog->addOrderLog($this->id, 'Стоимость заказа ' . $price);
        }
    }

    public function setProcStatus()
    {
        list($phone, $phoneError) = (new PhoneCorrectionService())->customCorrectionForCountry($this->geo, $this->phone);
        $this->phone = $phone;
        if ($phoneError) {
            $this->proc_status = 6;
        }
    }

    public static function countOrderByStatus($filter)
    {
        $result = DB::table(self::tableName() . ' AS o')
            ->select(DB::raw("COUNT(DISTINCT(o.id)) AS count"), 'o.proc_status')
            ->where('o.moderation_id', '>', 0)
            ->where('o.target_status', 1)
            ->where('o.service', '!=', self::SERVICE_CALL_CENTER)
            ->leftJoin('target_values as tv', 'tv.order_id', '=', 'o.id');

        if (Auth::user()->project_id) {
            $result = $result->where('o.project_id', Auth::user()->project_id);
        }

        if (Auth::user()->sub_project_id) {
            $result = $result->where('o.subproject_id', Auth::user()->sub_project_id);
        }

        if ($filter['product']) {
            $result = $result->leftJoin('order_products AS op', 'op.order_id', '=', 'o.id');
        }
        if ($filter['id']) {
            $result = $result->where('o.id', $filter['id'])
                ->where('o.id', '>', 0);
        }
        if ($filter['surname']) {
            $result->where('o.name_last', 'like', $filter['surname'] . '%');
        }
        if ($filter['phone']) {
            $result = $result->where('o.phone', 'like', '%' . $filter['phone']);
        }
        if ($filter['ip']) {
            $result = $result->where('o.host', 'like', $filter['ip'] . '%');
        }
        if ($filter['oid']) {
            $result = $result->where('o.partner_oid', $filter['oid'])
                ->where('o.partner_oid', '>', 0);
        }
        if ($filter['initiator']) {
            $initiators = explode(',', $filter['initiator']);
            $result = $result->whereIn('o.target_user', $initiators)->where('handmade', 1);
        }
        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result = $result->whereIn('o.geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $result = $result->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $result = $result->whereIn('o.subproject_id', $subProject);
        }
        if ($filter['status']) {
            $statuses = explode(',', $filter['status']);
            $result = $result->whereIn('o.proc_status', $statuses);
        }
        if ($filter['sub_status']) {
            $subStatuses = explode(',', $filter['sub_status']);
            $result = $result->whereIn('o.proc_status_2', $subStatuses);
        }
        if ($filter['target']) {
            $target = explode(',', $filter['target']);
            $result = $result->whereIn('o.final_target', $target);
        }
        if (isset($filter['deliveries'])) {
            $targetsApprove = explode(',', $filter['deliveries']);
            $result = $result->whereIn('o.target_approve', $targetsApprove);
        }
        if (isset($filter['track'])) {
            $result = $result->where('tv.track', '!=', 0);
        }
        if (isset($filter['track_filter'])) {
            $tracks = explode(',', $filter['track_filter']);
            $result = $result->whereIn('tv.track', $tracks);
        }
        if ($filter['partners']) {
            $partners = explode(',', $filter['partners']);
            $result = $result->whereIn('o.partner_id', $partners);
        }
        if ($filter['product']) {
            $offer = explode(',', $filter['product']);
            $result = $result->whereIn('op.product_id', $offer)
                ->where('op.disabled', 0);
        }
        if ($filter['products_count']) {
            $result = $result->where(DB::raw("(select count(order_products.id) from order_products where order_products.order_id = o.id and order_products.disabled = 0)"), $filter['products_count']);
        }
        if ($filter['offers']) {
            $offer = explode(',', $filter['offers']);
            $result = $result->whereIn('o.offer_id', $offer);

        }
        if ($filter['tag_source']) {
            $tags = explode(',', $filter['tag_source']);
            $result = $result->whereIn('o.tag_source', $tags);

        }
        if ($filter['tag_medium']) {
            $tags = explode(',', $filter['tag_medium']);
            $result = $result->whereIn('o.tag_medium', $tags);

        }
        if ($filter['tag_content']) {
            $tags = explode(',', $filter['tag_content']);
            $result = $result->whereIn('o.tag_content', $tags);

        }
        if ($filter['tag_campaign']) {
            $tags = explode(',', $filter['tag_campaign']);
            $result = $result->whereIn('o.tag_campaign', $tags);

        }
        if ($filter['tag_term']) {
            $tags = explode(',', $filter['tag_term']);
            $result = $result->whereIn('o.tag_term', $tags);

        }

        if ($filter['date-type'] && $filter['date_start'] && $filter['date_end']) {
            $filter['date_start'] = Carbon::parse($filter['date_start']);
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
            if ($filter['date_start'] <= $filter['date_end']) {
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
                    case 4:
                        $date = 'date';
                        break;
                    case 5:
                        $date = 'time_status_updated';
                        break;
                }
                if (isset($date) && ($filter['date-type'] == 1 || $filter['date-type'] == 3)) {
                    $result = $result->whereBetween('o.' . $date, [$filter['date_start'], $filter['date_end']]);
                }
                if (isset($date) && $filter['date-type'] == 5) {
                    $result = $result->whereBetween('o.' . $date, [$filter['date_start'], $filter['date_end']]);
                }

                if (isset($date) && $filter['date-type'] == 4) {
                    $result->whereBetween(DB::raw("(select max(comments.date) from comments where comments.order_id = o.id and comments.type = 'comment')"),
                        [
                            $filter['date_start'],
                            $filter['date_end']
                        ]);
                }
            }

        }


        return $result
            ->groupBy('o.proc_status')
            ->get();
    }

    public static function changeProcStatuses($oldStatusId, $newStatusId)
    {
        $status = ProcStatus::checkProject()->find($newStatusId);
        $oldStatus = ProcStatus::find($oldStatusId);
        $orders = self::checkAuth()
            ->moderated()
            ->targetApprove()
            ->withoutTargetFinal()
            ->where('proc_status', $oldStatusId)
            ->get();
        $result = [
            'name'   => 'Статус "' . ($oldStatus->name ?? '-') . '" не переопределен',
            'result' => false
        ];
        $logModel = new OrdersLog();
        if ($status && $orders->isNotEmpty()) {
            foreach ($orders as $order) {
                $log = 'Статус переопределен ';
                $log .= $oldStatus ? 'c "' . $oldStatus->name . '"' : $oldStatusId;
                if ($order->proc_status != $newStatusId) {
                    $order->proc_status_2 = 0;
                }
                $order->proc_status = $newStatusId;
                $log .= ' на "' . $status->name . '"';
                if ($order->save()) {
                    $logModel->addOrderLog($order->id, $log);
                }
            }
            $result = [
                'name'   => 'Статус "' . ($status->name ?? '-') . '" был переопределен',
                'result' => true
            ];
        }

        return $result;
    }

    public static function reportByStatuses($filter)
    {
        //$filter['date_type'] = $filter['date_type'] ? 'time_modified' : 'time_created';

        if (!$filter['date_type']) {
            $filter['date_type'] = 'time_created';
        } elseif ($filter['date_type'] && $filter['date_type'] == 2 && $filter['proc_status']) {
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
                    $statussesActionsArray[] = $allProcStatuses[$item]['action'] ?? 'time_created';
                }
            }
        } elseif ($filter['date_type'] && $filter['date_type'] == 2 && !$filter['proc_status']) {
            $filter['date_type'] = 'time_created';
        } elseif ($filter['date_type'] && $filter['date_type'] == 1) {
            $filter['date_type'] = 'time_modified';
        }

        if (!$filter['date_start'] || !$filter['date_end']) {
            $filter['date_start'] = Carbon::parse('now 00:00:00');
            $filter['date_end'] = Carbon::parse('now 23:59:59');
        } else {
            $filter['date_start'] = Carbon::parse($filter['date_start'] . ' 00:00:00');
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        }

        $query = self::with('procStatus', 'country')
            ->select(
                DB::raw('COUNT(orders.id) AS orders'),
                DB::raw('SUM(price_products) AS price_products'),
                DB::raw('SUM(price_total) AS price_total'),
                'proc_status',
                'geo',
                DB::raw('SUM(tv.cost_actual) AS cost_actual'),
                DB::raw('SUM(tv.cost) AS cost')
            )
            ->leftJoin(TargetValue::tableName() . ' AS tv', 'tv.order_id', '=', 'orders.id')
            ->leftJoin(ProcStatus::tableName() . ' AS ps', 'ps.id', '=', 'orders.proc_status')
            ->where(function ($q) {
                $q->where('ps.type', ProcStatus::TYPE_SENDERS)
                    ->orWhere('orders.proc_status', 3);
            })
            ->where('orders.moderation_id', '>', 0)
            ->where('orders.target_status', 1);

        $products = self::select(
            DB::raw('COUNT(op.id) AS count'),
            'proc_status',
            'geo'
        )
            ->leftJoin(OrderProduct::tableName() . ' AS op', 'op.order_id', '=', 'orders.id')
            ->leftJoin(ProcStatus::tableName() . ' AS ps', 'ps.id', '=', 'orders.proc_status')
            ->where(function ($q) {
                $q->where('ps.type', ProcStatus::TYPE_SENDERS)
                    ->orWhere('orders.proc_status', 3);
            })
            ->where('op.disabled', 0)
            ->where('orders.moderation_id', '>', 0)
            ->where('orders.target_status', 1);

        if ($filter['country']) {
            $query->where('orders.geo', $filter['country']);
            $products->where('orders.geo', $filter['country']);
        }
        if ($filter['project']) {
            $query->where('orders.project_id', $filter['project']);
            $products->where('orders.project_id', $filter['project']);
        }
        if ($filter['sub_project']) {
            $query->where('orders.subproject_id', $filter['sub_project']);
            $products->where('orders.subproject_id', $filter['sub_project']);
        }
        if ($filter['division']) {
            $query->where('orders.division_id', $filter['division']);
            $products->where('orders.division_id', $filter['division']);
        }
        if ($filter['proc_status'] && ($filter['date_type'] == 'time_created' || $filter['date_type'] == 'time_modified')) {

            $statuses = explode(',', $filter['proc_status']);

            $query->whereIn('orders.proc_status', $statuses);
            $products->whereIn('orders.proc_status', $statuses);
        }
        if ($filter['result']) {
            $filter['result'] = $filter['result'] == 5 ? 0 : $filter['result'];
            $query->where('orders.final_target', $filter['result']);
            $products->where('orders.final_target', $filter['result']);
        }
        if ($filter['offers']) {
            $query->where('orders.offer_id', $filter['offers']);
            $products->where('orders.offer_id', $filter['offers']);
        }
        if ($filter['delivery']) {
            $query->where('orders.target_approve', $filter['delivery']);
            $products->where('orders.target_approve', $filter['delivery']);
        }
        if ($filter['product']) {
            $query->whereExists(function ($q) use ($filter) {
                $q->select('id')
                    ->from(OrderProduct::tableName() . ' AS op')
                    ->whereRaw('orders.id = op.order_id')
                    ->where('op.product_id', $filter['product']);
            });
            $products->where('op.product_id', $filter['product']);
        }
        if ($filter['date_start'] && $filter['date_end']) {

            if ($filter['date_type'] == 'time_created' || $filter['date_type'] == 'time_modified') {

                $query->whereBetween('orders.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
                $products->whereBetween('orders.' . $filter['date_type'], [$filter['date_start'], $filter['date_end']]);
            } elseif ($filter['date_type'] != 'time_created' && $filter['date_type'] != 'time_modified' && $filter['proc_status']
                && !empty($statussesActionsArray)
            ) {
                $query->where(
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
                $products->where(
                    function ($qProducts) use ($filter, $statussesActionsArray) {
                        $qProducts->whereBetween('orders.time_' . $statussesActionsArray[0], [
                            $filter['date_start'],
                            $filter['date_end']
                        ]);
                        if (count($statussesActionsArray) > 1) {
                            unset($statussesActionsArray[0]);
                            foreach ($statussesActionsArray as $value) {
                                $qProducts->orWhereBetween('orders.time_' . $value, [
                                    $filter['date_start'],
                                    $filter['date_end']
                                ]);
                            }
                        }
                    }
                );
            }
        }

        $products = $products->groupBy('proc_status', 'geo')->get()->groupBy(['geo', 'proc_status']);
        $byCountry = $query->groupBy('proc_status', 'geo')->get()->groupBy('geo');

        if ($byCountry->isNotEmpty()) {
            foreach ($byCountry as $geo => $orders) {
                if ($orders->isNotEmpty()) {
                    foreach ($orders as $order) {
                        $order->products_count = isset($products[$geo][$order->proc_status]) ? $products[$geo][$order->proc_status]->first()->count : 0;
                    }
                }
            }

        }

        return $byCountry;
    }

    public static function getStatisticsOrder($filter, $type = Pass::TYPE_NO_REDEMPTION)
    {
        $relation = $type == Pass::TYPE_SENDING ? 'pass_send_id' : 'pass_id';
        $query = DB::table(self::tableName() . ' AS o')
            ->select(
                'o.subproject_id AS sub_project_id',
                DB::raw('COUNT(o.id) AS orders'),
                DB::raw('SUM(o.price_total) AS price_total'),
                DB::raw('SUM(o.price_products) AS price_products'),
                DB::raw('SUM(tv.cost) AS cost'),
                DB::raw('SUM(tv.cost_actual) AS cost_actual'),
                DB::raw('SUM(tv.cost_return) AS cost_return')
            )->leftJoin(TargetValue::tableName() . ' AS tv', 'tv.order_id', '=', 'o.id')
            ->leftJoin(Pass::tableName() . ' AS p', 'p.id', '=', 'o.' . $relation)
            ->where('p.active', 0)
            ->where('p.type', $type)
            ->where('o.subproject_id', '!=', 0);

        //todo переделать проверку на подпроект/проект
        if (Auth::user()->project_id) {
            $query->where('o.project_id', Auth::user()->project_id);
        }
        if (Auth::user()->sub_project_id) {
            $query->where('o.subproject_id', Auth::user()->sub_project_id);
        }
        if (!empty($filter['project'])) {
            $query->where('o.project_id', $filter['project']);
        }
        if (!empty($filter['sub_project'])) {
            $query->where('o.subproject_id', $filter['sub_project']);
        }
        if (!empty($filter['date_start']) && !empty($filter['date_end'])) {
            $start = date('Y-m-d 00:00:00', strtotime($filter['date_start']));
            $end = date('Y-m-d 23:59:59', strtotime($filter['date_end']));
            $query->whereBetween('p.updated_at', [$start, $end]);
        }

        return $query->groupBy('o.subproject_id')->get()->keyBy('sub_project_id');
    }

    public static function orderCollectors($filter, $type, bool $currentUser, $allOrders = false)
    {
        $query = self::with([
            'procStatus',
            'collectorLogs.user',
            'collectorLogs' => function ($q) use ($type) {
                $q->noProcessed();
            }
        ])
            ->withCount([
                'collectorLogs' => function ($q) use ($type) {
                    $q->processed()
                        ->byType($type);
                }
            ])
            ->whereHas('collectorLogs', function ($q) use ($type, $currentUser) {
                $q->noProcessed()
                    ->byType($type);
                if (!$currentUser) {
                    $q->where('user_id', Auth::user()->id);
                }
            });

        CollectingRepository::filterCollectorOrders($query, $filter);

        $column = !empty($filter['order_cell']) ? $filter['order_cell'] : 'id';
        $direction = !empty($filter['order_sort']) ? $filter['order_sort'] : 'desc';

        if ($allOrders) {
            return $query->orderBy('orders.' . $column, $direction)->get();
        }

        return $query->orderBy('orders.' . $column, $direction)->paginate(100)->appends(Input::except('page'));
    }

    public function addCollectorsCalls($orders)
    {
        if (count($orders)) {
            foreach ($orders->chunk(500) as $ordersChunk) {
                $data = [];
                $ids = [];
                foreach ($ordersChunk as $order) {
                    if ($order->geo == 'vn') {
                        $data[] = [
                            'id'      => $order->id,
                            'phone'   => $order->phone,
                            'before'  => 0,
                            'company' => 68, //дожим Вьетнама
                            'weight'  => 1,
                            'entity'  => Order::ENTITY_ORDER,
                        ];
                        $ids[] = $order->id;
                    } else {
                        $data[] = [
                            'id'      => $order->id,
                            'phone'   => $order->phone,
                            'before'  => 0,
                            'company' => 67, //дожим
                            'weight'  => 1,
                            'entity'  => Order::ENTITY_ORDER,
                        ];
                        $ids[] = $order->id;
                    }
                }

                CollectorLog::addCollectorLogsAuto($ids);

                $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);

                if ($resultApi && $resultApi->status == 200) {
                    OrdersLog::addOrdersLog($resultApi->data, 'Заказ был заброшен в автообзвон для дожима');

                } else {
                    CollectorLog::whereIn('order_id', $ids)
                        ->where([
                            ['type', CollectorLog::TYPE_AUTO],
                            ['user_id', 0]
                        ])
                        ->noProcessed()
                        ->delete();

                    OrdersLog::addOrdersLog($ids, 'Произошла ошибка.Заказ не попал в PBX');
                }
            }
            return true;
        }
        return false;
    }

    public function deleteCallsByIds($ids, $entity = 'order')
    {
        return $this->apiElastixProcessing2('deleteCallsByIds', [
            'listsToDelete' => implode(',', $ids),
            'entity'        => $entity
        ]);
    }

    public function setTimeByStatus($status)
    {
        if (!($status instanceof ProcStatus) && is_int($status)) {
            $status = ProcStatus::find($status);
        } else {
            return false;
        }

        //todo $this->proc_status = $status->id;

        switch ($status->action) {
            case 'sent':
                $this->time_sent = Carbon::now();
                break;
            case 'at_department':
                $this->time_at_department = Carbon::now();
                break;
            case 'received':
                $this->time_received = Carbon::now();
                break;
            case 'returned':
                $this->time_returned = Carbon::now();
                break;
            case 'paid_up':
                $this->time_paid_up = Carbon::now();
                break;
            case 'refused':
                $this->time_refused = Carbon::now();
                break;
        }
        //update status time
        $this->time_status_updated = Carbon::now();

        return true;
    }

    public static function ordersForReset($filter)
    {
        $result = DB::table(self::tableName() . ' AS o')
            ->select(
                DB::raw('DISTINCT(o.id) AS id'), 'o.time_created', 'o.time_changed', 'o.time_modified', 'o.host',
                'o.phone', 'o.proc_status', 'o.proc_stage', 'o.target_status', 'o.proc_callback_time', 'o.geo',
                'o.partner_oid', 'o.proc_campaign', 'o.offer_id', 'o.price_total AS price', 'o.proc_time', 'o.target_user',
                'o.entity')
            ->where('service', '!=', self::SERVICE_SENDING);

        if ($filter['product']) {
            $result = $result->leftJoin('order_products AS op', 'op.order_id', '=', 'o.id');
        }
        if ($filter['company'] || Auth()->user()->company_id || Auth()->user()->campaign_id) {
            if ($filter['target']) {
                $result = $result->leftJoin('users AS u', 'u.id', '=', 'o.target_user');
            } else {
                $result = $result->leftJoin('orders_opened AS oo', "oo.order_id", '=', 'o.id')
                    ->leftJoin('users AS u', 'u.id', '=', 'oo.user_id')
                    ->groupBy('o.id');
            }

            if (auth()->user()->company_id && auth()->user()->campaign_id != 10 && auth()->user()->campaign_id != 66) {
                $result = $result->where('u.company_id', auth()->user()->company_id);
            }

            if (auth()->user()->company_id && auth()->user()->campaign_id == 66) {
                $result = $result->where('o.geo', 'id');
            }
            if (auth()->user()->campaign_id) {
                $result = $result->where('o.proc_campaign', auth()->user()->campaign_id);
            }
        }
        if (auth()->user()->sub_project_id) {
            $result = $result->where('o.subproject_id', auth()->user()->sub_project_id);
        }
        if ($filter['id']) {
            $result = $result->where('o.id', $filter['id'])
                ->where('o.id', '>', 0);
        }
        if ($filter['name']) {
            $result->where('o.name_first', 'like', $filter['name'] . '%');
        }
        if ($filter['surname']) {
            $result->where('o.name_last', 'like', $filter['surname'] . '%');
        }
        if ($filter['middle']) {
            $result->where('o.name_middle', 'like', $filter['middle'] . '%');
        }
        if ($filter['target']) {
            if ($filter['target'] == 5) {
                $filter['target'] = 0;
            }
            $result = $result->where('o.target_status', $filter['target']);
        }

        if ($filter['product']) {
            $offer = explode(',', $filter['product']);
            $result = $result->whereIn('op.product_id', $offer);
        }
        if ($filter['offers']) {
            $offer = explode(',', $filter['offers']);
            $result = $result->whereIn('o.offer_id', $offer);

        }
        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $result = $result->whereIn('o.geo', $country);
        }
        if ($filter['company']) {
            $company = explode(',', $filter['company']);
            $result = $result->whereIn('u.company_id', $company);
        }
        if ($filter['partners']) {
            $partners = explode(',', $filter['partners']);
            $result = $result->whereIn('o.partner_id', $partners);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $result = $result->whereIn('o.project_id', $project);
        }
        if ($filter['sub_project']) {
            $project = explode(',', $filter['sub_project']);
            $result = $result->whereIn('o.subproject_id', $project);
        }
        if ($filter['group']) {
            $group = explode(',', $filter['group']);
            $result = $result->whereIn('o.proc_campaign', $group);
        }
        if ($filter['status']) {
            $status = explode(',', $filter['status']);
            $result = $result->whereIn('o.proc_status', $status);
        }
        if ($filter['phone']) {
            $result = $result->where('o.phone', 'like', $filter['phone'] . '%');
        }
        if ($filter['ip']) {
            $result = $result->where('o.host', 'like', $filter['ip'] . '%');
        }
        if ($filter['entity']) {
            $result = $result->where('o.entity', $filter['entity']);
        }
        if ($filter['oid']) {
            $result = $result->where('o.partner_oid', $filter['oid'])
                ->where('o.partner_oid', '>', 0);
        }
        if ($filter['user']) {
            $user = explode(',', $filter['user']);
            $result = $result->where('o.target_user', $user);
        }
        if ($filter['not_available']) {
            $statuses = ProcStatus::where('action_alias', 'not-available')->pluck('id');
            $result = $result->whereIn('o.proc_status', $statuses);
        }
        if ($filter['cause_cancel']) {
            $result = $result->leftJoin(TargetValue::tableName() . ' AS tv', 'o.id', '=', 'tv.order_id')
                ->where('tv.values', 'like', '%{"cause":{"field_title"%"field_value":"' . $filter['cause_cancel'] . '",%')
                ->groupBy('o.id');
        }
        if ($filter['date-type'] && $filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
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
                $result = $result->whereBetween($date, [$filter['date_start'], $filter['date_end']]);
            }
        }

        return $result->orderBy('o.id', 'desc')->get();
    }

    public static function resetProcStage($orders, $priority)
    {
        if ($orders) {
            foreach ($orders as $order) {
                DB::table(self::tableName())->where('id', $order->id)
                    ->increment('reset_call', 1, [
                        'moderation_id'   => 0,
                        'moderation_time' => 0,
                        'proc_stage'      => $priority
                    ]);
                (new OrdersLog)->addOrderLog($order->id, 'Модерация аннулирована.Установлен приоритет ' . $priority);
            }
            $status = ProcStatus::find(1);
            ActionController::updateOrderProcStatus($orders, $status);
            return true;
        }
        return false;
    }

    /**
     * @param $data
     */
    public static function saveOrderLockedChanges($data)
    {
        if ($data['total_price_locked'] <= 0) {
            return ['errors' => 'Нельзя сохранить заказ без товара!'];
        }

        $order = Order::find($data['order_id']);
        $orderProducts = OrderProduct::where('order_id', $data['order_id'])->get()->keyBy('id');
        $products = Product::whereIn('id', $orderProducts->pluck('product_id'))->get()->keyBy('id');
        $operationLog = '';
        $logs = [];

        //update or create products/
        if (isset($data['products'])) {
            $logs = array_map(function ($product) use ($orderProducts, $operationLog, $products, $order) {
                if (!$product['disabled'] && $orderProducts[$product['id']]->price != $product['price']) {
                    $orderProducts[$product['id']]->update($product);
                    return $products[$orderProducts[$product['id']]->product_id]->title . ' новая цена ' .
                        $product['price'] . "<br>";
                }
                if (!$orderProducts[$product['id']]->disabled && $product['disabled']) {

                    //if product is deleted/added modify transaction and storage
                    //reversal (сторно) storage
                    $storageContent = StorageContent::where([
                        ['project_id', $order->subproject_id],
                        ['product_id', $orderProducts[$product['id']]->product_id]
                    ])->first();

                    if ($storageContent) {
                        $storageContent->hold -= 1;
                        $storageContent->amount += 1;
                        if ($storageContent->save()) {
                            StorageTransaction::create([
                                'product_id' => $orderProducts[$product['id']]->product_id,
                                'project_id' => $order->subproject_id,
                                'user_id'    => auth()->user()->id,
                                'amount1'    => $storageContent->amount - 1,
                                'amount2'    => $storageContent->amount,
                                'hold1'      => $storageContent->hold + 1,
                                'hold2'      => $storageContent->hold,
                                'type'       => StorageTransaction::TYPE_MANUAL,
                                'moving_id'  => 0,
                                'order_id'   => $order->id ?? 0
                            ]);
                        }
                    }
                    $orderProducts[$product['id']]->update($product);
                    return $products[$orderProducts[$product['id']]->product_id]->title . ' товар был удален<br>';
                }
                return false;
            }, $data['products']);
        }

        $operationLog = implode('', $logs);

        if (isset($data['products_new'])) {
            array_map(function ($k, $v) use ($order, $data) {
                $newOrderProduct = OrderProduct::create([
                    'order_id'   => $data['order_id'],
                    'product_id' => $k,
                    'price'      => $v['price']
                ]);

                $positiveQuantity = StorageContent::checkAmountProduct($k, $order->subproject_id);
                if ($positiveQuantity) {
                    $sc = StorageContent::where('project_id', $order->subproject_id)
                        ->where('product_id', $k)->first();

                    if (!$sc) {
                        $sc = new StorageContent();
                        $sc->project_id = $order->subproject_id;
                        $sc->product_id = $k;
                        $sc->hold = 0;
                        $sc->amount = 0;
                    }

                    if ($sc) {
                        $sc->amount -= 1; //т.к. в заказе каждого товара по 1 шт
                        $sc->hold += 1;
                        if ($sc->save()) {
                            $st = StorageTransaction::create([
                                'product_id' => $k,
                                'project_id' => $order->subproject_id,
                                'user_id'    => auth()->user()->id,
                                'amount1'    => $sc->amount + 1,
                                'amount2'    => $sc->amount,
                                'hold1'      => $sc->hold - 1,
                                'hold2'      => $sc->hold,
                                'type'       => StorageTransaction::TYPE_MANUAL,
                                'moving_id'  => 0,
                                'order_id'   => $order->id ?? 0
                            ]);
                        }
                    }
                }
            }, array_keys($data['products_new']), $data['products_new']);
        }

        if ($order->price_total != $data['total_price_locked']) {
            $operationLog .= 'Стомость заказа была изменена ' . $order->price_total . ' новая стоимость ->'
                . $data['total_price_locked'] . '<br>';
        }

        if ($order->getTargetValue->cost != $data['cost']) {
            $values = TargetValue::where('order_id', $data['order_id'])->get();
            if ($values) {
                foreach ($values as $row) {
                    $val = json_decode($row->values, true);
                    if (isset($val['cost'])) {
                        $val['cost']['field_value'] = $data['cost'];
                        $row->values = json_encode($val);
                        $row->cost = $data['cost'];
                        if ($row->save()) {
                            $operationLog .= ' Новая стоимость прих. доставки -> '
                                . $data['cost'];
                        }
                    }
                }
            }
        }

        //in case total order price diff with order_products price divide total price
        OrderProduct::divideOrderCostsAndPrices($data, $data['total_price_locked']);

        //write general order log
        $orderLogId = (new OrdersLog)->addOrderLog($data['order_id'], $operationLog);

        //update order price columns
        DB::table(Order::tableName())->where('id', $data['order_id'])->update([
            'price_total'    => $data['total_price_locked'],
            'price_products' => OrderRepository::sumOrderProducts($data['order_id'])
        ]);

        //save manual operation
        Operation::create([
            'type'         => Operation::TYPE_MANUAL,
            'user_id'      => \auth()->user()->id,
            'order_id'     => $data['order_id'],
            'order_log_id' => $orderLogId,
            'comment'      => $data['operation_comment']
        ]);

        return ['success' => true];
    }

    public static function getOrdersByCountry($filter)
    {
        $orders = self::with('getTargetValue', 'getTargetValue.getTargetConfig')
            ->targetApprove()
            ->moderated()
            ->where('entity', self::ENTITY_ORDER)
            ->whereIn('geo', ['vn', 'ua', 'kz', 'ru']);
        
        if (!empty($filter['country'])) {
            $orders->where('geo', $filter['country']);
        }

        if (!empty($filter['project'])) {
            $orders->where('project_id', $filter['project']);
        }

        if (!empty($filter['sub_project'])) {
            $orders->where('subproject_id', $filter['sub_project']);
        }

        if (!empty($filter['product'])) {
            $orders->whereHas('products' , function ($q) use($filter) {
                    $q->where('products.id', $filter['product']);
                });
        }

        $timeColumn = $filter['date_type'] ?? 'time_created';
        $timeStart = !empty($filter['date_start']) ? Carbon::parse($filter['date_start'])->startOfDay() : Carbon::today()->startOfDay();
        $timeEnd = !empty($filter['date_end']) ? Carbon::parse($filter['date_end'])->endOfDay() : Carbon::today()->endOfDay();

        $orders = $orders->where($timeColumn, '>=', $timeStart)
            ->where($timeColumn, '<=', $timeEnd)
            ->get();

        $result = [];

        if ($orders->isNotEmpty()) {
            foreach ($orders as $order) {
                $target = json_decode( $order->getTargetValue->values ?? '', true);
                $city = '';

                if ($order->getTargetValue) {
                    $field = '';
                    if ($order->getTargetValue->target_id == 17 || $order->getTargetValue->target_id == 20) {
                        $field = 'warehouse';
                    } else if ($order->getTargetValue->target_id == 16) {
                        $field = 'region';
                    } else if ($order->getTargetValue->target_id == 1) {
                        $field = 'city';
                    } else if ($order->getTargetValue->target_id == 4) {
                        $field = 'city';
                    } else if ($order->getTargetValue->target_id == 2) {
                        $field = 'locality';
                    } else if ($order->getTargetValue->target_id == 18) {
                        $field = 'city';
                    } else if ($order->getTargetValue->target_id == 3) {
                        $field = 'city';
                    }

                    $city = $target[$field]['field_value'] ?? 'undefined city';
                }

                $result[$order->geo]['all'] = ($result[$order->geo]['all'] ?? 0) + 1;
                $result[$order->geo]['cities'][$city] = [
                    'count' => ($result[$order->geo]['cities'][$city]['count'] ?? 0) + 1,
                    'name' => $city,
                    'post_name' => $order->getTargetValue->getTargetConfig->alias ?? ''
                ];
            }
        }

        return $result;
    }
}