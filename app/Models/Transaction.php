<?php

namespace App\Models;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\newTransactionEvent;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends BaseModel
{
    protected $table = 'finance_transaction';
    public $timestamps = false;

    const ENTITY_SUB_PROJECT = 'sub_project';
    const ENTITY_ORDER = 'user';
    const ENTITY_COMPANY = 'company';

    const TYPE_APPROVE = 'approve';
    const TYPE_BONUS = 'bonus';
    const TYPE_RETENTION = 'retention';
    const TYPE_FINE = 'fine';
    const TYPE_RESIDUE = 'residue';
    const TYPE_DEBT = 'debt';
    const TYPE_CUSTOM = 'custom';
    const TYPE_WEEK = 'week';
    const TYPE_REDEMPTION = 'redemption';
    const TYPE_NO_REDEMPTION = 'no-redemption';
    const TYPE_SENDING = 'sending';
    const TYPE_CANCEL_SEND = 'cancel_send';

    /*получить план к транзакции*/
    public function plan()
    {
        return $this->belongsTo('App\Models\Plan');
    }

    /**
     * @return BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'geo', 'code');
    }

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }

    public function createOrUpdateTransaction($orderId, $type, $data = [], $qa = false)
    {
        $newCompanyTransaction = NULL;
        $newUserTransaction = NULL;
        $order = null;
        DB::table($this->table)
            ->whereIn('entity', ['company', 'user'])
            ->where('order_id', $orderId)
            ->update(['active'=>0]);

        if ($orderId == 0) {//todo надо ли?
            $user = DB::table('users AS u')
                ->select('u.id','u.role_id_id', 'c.prices', 'c.type', 'c.billing', 'c.billing_type', 'u.company_id', 'u.rank_id')
                ->leftJoin('companies AS c', 'u.company_id', '=', 'c.id')
                ->where('u.id', $data['user_id'])
                ->first();
        } else {
            $order = DB::table('orders')
                ->select('id', 'offer_id', 'target_user', 'geo')
                ->where('id', $orderId)
                ->first();
            $user = DB::table('users AS u')
                ->select('u.id', 'u.role_id', 'c.prices', 'c.type', 'c.billing', 'c.billing_type', 'u.company_id', 'u.rank_id')
                ->leftJoin('companies AS c', 'u.company_id', '=', 'c.id')
                ->where('u.id', $order->target_user)
                ->first();
        }
        if ($user && $order) {
            $usCross = collect(DB::table('order_products')
                ->select(DB::raw('COUNT(type) AS count'), 'type')
                ->where('order_id', $orderId)
                ->whereIn('type', [1, 2, 4, 5])
                ->groupBy('type')
                ->get())->keyBy('type');

            $count['up1'] = isset($usCross[1]) ? $usCross[1]->count : 0;
            $count['up2'] = isset($usCross[2]) ? $usCross[2]->count : 0;
            $count['cross'] = isset($usCross[4]) ? $usCross[4]->count : 0;
            $count['cross2'] = isset($usCross[5]) ? $usCross[5]->count : 0;
            $prices = json_decode($user->prices, true);
            $billing = json_decode($user->billing, true);
            $optionType = 'lead';
            $userTransaction = [];
            $companyTransaction = [];

            if ($user->role_id == 1) {
                //транзакция для компании
                if (isset($prices['users'][$user->id]) && $prices['users'][$user->id]['type'] == $optionType) {//для опеределнного пользователя
                    $options = $prices['users'][$user->id];
                    $companyTransaction = $this->getArrayTransaction($order, $user, $type, $options, $count, 'company');
                } elseif (isset($prices['ranks'][$user->rank_id]) && $prices['ranks'][$user->rank_id]['type'] == $optionType) {//для ранга
                    $options = $prices['ranks'][$user->rank_id];
                    $companyTransaction = $this->getArrayTransaction($order, $user, $type, $options, $count, 'company');
                } elseif (isset($prices['global']) && $prices['global']['type'] == $optionType) {//для всех
                    $options = $prices['global'];
                    $companyTransaction = $this->getArrayTransaction($order, $user, $type, $options, $count, 'company');
                }
                //транзакция для пользователя
                if (isset($billing['users'][$user->id]) && $billing['users'][$user->id]['type'] == $optionType) {//для опеределнного пользователя
                    $options = $billing['users'][$user->id];
                    $userTransaction = $this->getArrayTransaction($order, $user, $type, $options, $count, 'user');
                } elseif (isset($billing['ranks'][$user->rank_id]) && $billing['ranks'][$user->rank_id]['type'] == $optionType) {//для ранга
                    $options = $billing['ranks'][$user->rank_id];
                    $userTransaction = $this->getArrayTransaction($order, $user, $type, $options, $count, 'user');
                } elseif (isset($billing['global']) && $billing['global']['type'] == $optionType) {//для всех
                    $options = $billing['global'];
                    $userTransaction = $this->getArrayTransaction($order, $user, $type, $options, $count, 'user');
                }

                if ($companyTransaction) {
                    DB::table($this->table)->insert($companyTransaction);
                }

                if ($userTransaction) {
                    DB::table($this->table)->insert($userTransaction);
                }

            }


        }
        if (isset($newCompanyTransaction) && $newCompanyTransaction->type == 'approve') {
            /*Ивент который отслеживает новую транзакцию*/
            event(new newTransactionEvent($newCompanyTransaction));
        }

        if (isset($newUserTransaction) && $newUserTransaction->type == 'approve') {
            /*Ивент который отслеживает новую транзакцию*/
            event(new newTransactionEvent($newUserTransaction));
        }
    }

    /**
     * @param $order Order
     * @param $user Auth
     * @param $type string
     * @param $options array
     * @param $countUp array required key:up1, up2, cross
     * @param $entity string
     * @return array
     */
    protected function getArrayTransaction($order, $user, $type, $options, $countUp, $entity)
    {
        $balance = $options['approve'] + $countUp['up1'] * $options['up_sell'] + $countUp['up2'] * $options['up_sell_2'] + $countUp['cross'] * $options['cross_sell'] + $countUp['cross2'] * $options['cross_sell_2'];

        return [
            'type' => $type,
            'order_id' => $order->id,
            'company_id' => $user->company_id,
            'entity' => $entity,
            'user_id' => $order->target_user,
            'initiator' => 'System',
            'offer_id' => $order->offer_id,
            'geo' => $order->geo,
            'balance' => $balance,
            'payout_id' => 0,
            'plan_id' => 0,
            'approve' =>  $options['approve'],
            'up1' => $options['up_sell'],
            'count_up1' => $countUp['up1'],
            'up2' => $options['up_sell_2'],
            'count_up2' => $countUp['up2'],
            'cross' => $options['cross_sell'],
            'count_cross' => $countUp['cross'],
            'cross2' => $options['cross_sell_2'],
            'count_cross2' => $countUp['cross2'],
            'result' => 0,
            'qa' => 0,
            'time_created' => now(),
            'time_modified' => now(),
        ];
    }

    public function getOrdersOneOperator($id, $filter, $page)
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
        $skip = 0;
        $countOnePage = 100;
        if ($page) {
            $page = (int)str_replace('page-', '', $page);
            $skip = ($page - 1) * $countOnePage;
        }
        $orders = DB::table($this->table . ' AS t')
            ->select('t.id', 't.order_id', 'of.name AS offer', 't.initiator', 't.type', 't.approve', 't.up1', 't.up2', 't.cross',
                't.count_up1', 't.count_up2', 't.count_cross', 't.balance', 't.time_created', 't.offer_id', 't.comment',
                'c.currency', 't.geo', 't.payout_id', 'comp.type AS cType', 't.time_system_crm as time_crm',
                't.time_system_pbx as time_pbx', 't.time_talk as talk_time')
            ->leftJoin('offers AS of', 't.offer_id', '=', 'of.id')
            ->leftJoin('countries AS c', 'c.code', '=', 't.geo')
            ->leftJoin('companies AS comp', 'comp.id', '=', 't.company_id')
            ->whereBetween('t.time_created', [$filter['date_start'], $filter['date_end']])
            ->where('t.user_id', $id)
            ->where('t.active', 1)
            ->where('t.entity', 'user');

        $count = $orders->count();
        $orders = collect($orders->skip($skip)
            ->take($countOnePage)
            ->orderBy('t.id', 'desc')
            ->get())->keyBy('order_id');
        $orderIds = [];
        if ($orders) {
            foreach ($orders as $order) {
                $orderIds[] = $order->order_id;
            }
        }

        $products = DB::table('order_products AS op')
            ->select('p.title', 'op.order_id', 'op.type', 'op.price')
            ->leftJoin('products AS p', 'op.product_id', '=', 'p.id')
            ->whereIn('op.order_id', $orderIds)
            ->get();

        $orderProducts = [];
        if ($products) {
            $i = 0;
            foreach ($products as $product) {
                $currency = '';
                if (isset($orders[$product->order_id])) {
                    $currency = $orders[$product->order_id]->currency;
                }
                $orderProducts[$product->order_id][$i]['title'] = $product->title;
                $orderProducts[$product->order_id][$i]['price'] = $product->price;
                $orderProducts[$product->order_id][$i]['currency'] = $currency;
                $orderProducts[$product->order_id][$i]['type'] = $product->type;
                $i++;
            }
        }
        $paginationModle = new Pagination;
        return [
            'orders' => $orders,
            'products' => $orderProducts,
            'pagination' => $paginationModle->getPagination($page, $count, $countOnePage),
        ];
    }

    public function getBalance($userId)
    {
        $startMonth = Carbon::now()->startOfMonth();
        $startWeek = Carbon::now()->startOfWeek();
        $startYesterday = Carbon::yesterday();
        $today = Carbon::today();
        $data = [
            'all' => 0,
            'today' => 0,
            'yesterday' => 0,
            'week' => 0,
            'month' => 0,
        ];
        $orders = DB::table($this->table)
            ->select(
                DB::raw('SUM(balance) AS sum'),
                'time_created'
            )
            ->where('user_id', $userId)
            ->where('entity', 'user')
            ->where('payout_id', '=', 0)
            ->where('active', 1)
            ->groupBy('time_created')
            ->get();
        if ($orders) {
            foreach ($orders as $order) {
                $data['all'] += $order->sum;
                if ($order->time_created >= $startMonth) {
                    $data['month'] += $order->sum;
                }
                if ($order->time_created >= $startWeek) {
                    $data['week'] += $order->sum;
                }
                if ($order->time_created >= $startYesterday) {
                    $data['yesterday'] += $order->sum;
                }
                if ($order->time_created >= $today) {
                    $data['today'] += $order->sum;
                }
            }
        }
        return $data;
    }

    public function fineOperator($receiver, $data, $initiator)
    {
        if (!(float)$data['price']) {
            return true;
        }
        $fine = [
            'initiator' => $initiator,
            'receiver_id' => $receiver,
            'type' => 'fine',
            'sum_total' => $data['price'] * -1,
            'text' => $data['comment'],
            'time_created' => now(),
            'time_modified' => now(),
        ];
        return !DB::table($this->table)->insert($fine);
    }

    public function setAllTransaction($orderId)
    {
        $order = DB::table('orders')
            ->select('offer_id', 'target_user', 'time_modified', 'geo')
            ->where('id', $orderId)
            ->first();
        $userRole = DB::table('users')->where('id', $order->target_user)->value('role');
        if ($userRole == 1) {
            $usCross = collect(DB::table('order_products')
                ->select(DB::raw('COUNT(type) AS count'), 'type')
                ->where('order_id', $orderId)
                ->whereIn('type', [1, 2, 4])
                ->groupBy('type')
                ->get())->keyBy('type');
            $data['order_id'] = $orderId;
            $data['initiator'] = 'System';
            $data['receiver_id'] = $order->target_user;
            $data['offer_id'] = $order->offer_id;
            $data['geo'] = $order->geo;
            $data['type'] = 'approve';
            $data['sum_approve'] = 5;
            $data['sum_up'] = isset($usCross[1]) ? $usCross[1]->count * 5 : 0;
            $data['count_up'] = isset($usCross[1]) ? $usCross[1]->count : 0;
            $data['sum_up2'] = isset($usCross[2]) ? $usCross[2]->count * 5 : 0;
            $data['count_up2'] = isset($usCross[2]) ? $usCross[2]->count : 0;
            $data['sum_cross'] = isset($usCross[4]) ? $usCross[4]->count * 5 : 0;
            $data['count_cross'] = isset($usCross[4]) ? $usCross[4]->count : 0;
            $data['sum_total'] = $data['sum_approve'] + $data['sum_up'] + $data['sum_up2'] + $data['sum_cross'];
            $data['time_created'] = $order->time_modified;
            $data['time_modified'] = $order->time_modified;
            $transaction = DB::table($this->table)->where('order_id', $orderId)->first();
            if (!$transaction) {
                DB::table($this->table)->where('order_id', $orderId)->insert($data);
            }
        }
    }

    public function getBalanceInCompanies()
    {
        return DB::table($this->table)
            ->select(
                DB::raw('ROUND(SUM(balance),2) as sum'),
                DB::raw('COUNT(balance) as count'),
                DB::raw('MIN(time_created) as min'),
                DB::raw('MAX(time_created) as max'),
                DB::raw('COUNT(approve) as approve'),
                DB::raw('SUM(count_up1) as up_sell'),
                DB::raw('SUM(count_up2) as up_sell_2'),
                DB::raw('SUM(count_cross) as cross_sell'),
                DB::raw('SUM(time_system_crm) as time_crm'),
                DB::raw('SUM(time_system_pbx) as time_pbx'),
                DB::raw('SUM(time_talk) as time_talk'),
                'company_id')
            ->where('payout_id', 0)
            ->where('entity', 'company')
            ->where('time_created', '<', Carbon::today())
            ->groupBy('company_id')
            ->where('active', 1)
            ->get();
    }

    public function getNoPayouts($id, $filter = [])
    {
        $time = Carbon::today();
        $transactions = DB::table($this->table)
            ->select(
                DB::raw('MIN(time_created) as min'),
                DB::raw('MAX(time_created) as max'),
                DB::raw('COUNT(id) as count'),
                DB::raw('COUNT(DISTINCT(user_id)) as users'),
                DB::raw('SUM(balance) as balance'),
                DB::raw('COUNT(approve) as approve'),
                DB::raw('SUM(count_up1) as count_up'),
                DB::raw('SUM(count_up2) as count_up2'),
                DB::raw('SUM(count_cross) as count_cross'),
                DB::raw('SUM(time_system_crm) as time_crm'),
                DB::raw('SUM(time_system_pbx) as time_pbx'),
                DB::raw('SUM(time_talk) as time_talk')
            )
            ->where('company_id', $id)
            ->where('entity', 'company')
            ->where('active', 1)
            ->where('payout_id', 0)
            ->where('time_created', "<=", $time);
        if (isset($filter['date_start'])) {
            $transactions = $transactions->where('time_created', '>=', $filter['date_start']);
        }
        if (isset($filter['date_end'])) {
            $transactions = $transactions->where('time_created', '<=', $filter['date_end']);
        }
        return $transactions->first();
    }

    public function getNoPayoutsUser($id, $filter = [])
    {
        $time = Carbon::today();
        $transactions = DB::table($this->table)
            ->select(
                DB::raw('MIN(time_created) as min'),
                DB::raw('MAX(time_created) as max'),
                DB::raw('COUNT(id) as count'),
                DB::raw('COUNT(DISTINCT(user_id)) as users'),
                DB::raw('SUM(balance) as balance'),
                DB::raw('COUNT(approve) as approve'),
                DB::raw('SUM(count_up1) as count_up'),
                DB::raw('SUM(count_up2) as count_up2'),
                DB::raw('SUM(count_cross) as count_cross'),
                DB::raw('SUM(time_system_crm) as time_crm'),
                DB::raw('SUM(time_system_pbx) as time_pbx'),
                DB::raw('SUM(time_talk) as time_talk')
            )
            ->where('user_id', $id)
            ->where('payout_id', 0)
            ->where('active', 1)
            ->where('entity', 'user')
            ->where('time_created', "<=", $time);
        if (isset($filter['date_start'])) {
            $transactions = $transactions->where('time_created', '>=', $filter['date_start']);
        }
        if (isset($filter['date_end'])) {
            $transactions = $transactions->where('time_created', '<=', $filter['date_end']);
        }
        return $transactions->first();
    }

    public function getAllNoPayoutsTransactions($id)
    {
        return DB::table($this->table)
            ->where('company_id', $id)
            ->where('payout_id', 0)
            ->where('active', 1)
            ->count();
    }

    public function getAllTransactionInCompanies($filter)
    {
        $countOnePage = 100;

        $transactions = DB::table($this->table . ' AS t')
            ->select('t.id', 't.order_id', 'of.name AS offer', 't.initiator', 't.type', 't.plan_id', 't.approve', 't.up1', 't.up2',
                't.cross', 't.count_up1', 't.count_up2', 't.count_cross', 't.balance', 't.time_created', 't.offer_id',
                't.comment', DB::raw('UPPER(t.geo) AS geo'), 'c.currency', 'u.name AS operName', 'u.surname AS operSurname',
                'cc.name AS company', 'cc.type AS cType', 't.time_system_crm AS time_crm', 't.time_system_pbx AS time_pbx',
                't.time_talk AS talk_time', 't.payout_id', 't.result')
            ->leftJoin('offers AS of', 't.offer_id', '=', 'of.id')
            ->leftJoin('countries AS c', 'c.code', '=', 't.geo')
            ->leftJoin('users AS u', 'u.id', '=', 't.user_id')
            ->leftJoin('companies as cc', 'cc.id', '=', 'u.company_id')
            ->where('t.active', 1)
            ->where('t.entity', 'company');

        if (auth()->user()->company_id) {
            $transactions = $transactions->where('t.company_id', auth()->user()->company_id);
        }

        if ($filter['date_start']) {
            $start = Carbon::parse($filter['date_start']);
            $transactions = $transactions->where('t.time_created', '>=', $start);
        }

        if ($filter['date_end']) {
            $end = Carbon::parse($filter['date_end'])->endOfDay();
            $transactions = $transactions->where('t.time_created', '<=', $end);
        }

        if ($filter['id']) {
            $transactions = $transactions->where('t.id', $filter['id']);
        }

        if ($filter['oid']) {
            $transactions = $transactions->where('t.order_id', $filter['oid']);
        }
        if ($filter['company']) {
            $offer = explode(',', $filter['company']);
            $transactions = $transactions->whereIn('t.company_id', $offer);
        }
        if ($filter['country']) {
            $offer = explode(',', $filter['country']);
            $transactions = $transactions->whereIn('t.geo', $offer);
        }

        if ($filter['trans_payed']) {
            $transactions = $transactions->where('t.payout_id', '>', 0);
        } else {
            $transactions = $transactions->where('t.payout_id', 0);
        }

        $transactions = $transactions
            ->orderBy('t.id', 'desc')
            ->paginate($countOnePage);

        $orderIds = [];
        if ($transactions->items()) {
            foreach ($transactions->items() as $transaction) {
                if ($transaction->cType == 'lead') {
                    $orderIds[] = $transaction->order_id;
                }

                if (!empty($transaction->plan_id) && $transaction->plan_id !== 0 && $transaction->plan_id !== NULL) {
                    $transaction->plan = Plan::where('id', $transaction->plan_id)->first();
                }

            }
        }

        $products = DB::table('order_products AS op')
            ->select('p.title', 'op.order_id', 'op.type', 'op.price')
            ->leftJoin('products AS p', 'op.product_id', '=', 'p.id')
            ->whereIn('op.order_id', $orderIds)
            ->get();

        $orderProducts = [];
        if ($products) {
            $i = 0;
            foreach ($products as $product) {
                $currency = '';
                if (isset($transactions->items()[$product->order_id])) {
                    $currency = $transactions->items()[$product->order_id]->currency;
                }
                $orderProducts[$product->order_id][$i]['title'] = $product->title;
                $orderProducts[$product->order_id][$i]['price'] = $product->price;
                $orderProducts[$product->order_id][$i]['currency'] = $currency;
                $orderProducts[$product->order_id][$i]['type'] = $product->type;
                $i++;
            }
        }

        return [
            'orders' => $transactions->appends(Input::except('page')),
            'products' => $orderProducts,
        ];

    }

    public function getAllTransactionNotPayout($companyId)
    {
        return DB::table($this->table . ' AS t')
            ->select('u.name', 'u.surname', 't.sum_total', 't.order_id')
            ->leftJoin('users AS u', 'u.id', '=', 't.receiver_id')
            ->where('u.company_id', '=', $companyId)
            ->where('t.payout_id', '>', 0)
            ->where('t.active', 1)
            ->get();
    }

    public function setPayoutCompanyId($idCompany, $idPay)
    {
        $payout = DB::table('finance_payouts')->where('id', $idPay)->first();
        if ($payout) {
            $dataFilter = [
                'date_start' => $payout->period_start,
                'date_end' => $payout->period_end,
            ];
            $transaction = $this->getNoPayouts($idCompany, $dataFilter);
            DB::table($this->table)
                ->whereBetween('time_created', $dataFilter)
                ->where('company_id', $idCompany)
                ->where('entity', 'company')
                ->update(['payout_id' => $idPay, 'time_modified' => now(),]);
            if ($payout->valuation > $transaction->balance || $payout->valuation < $transaction->balance) {
                if ($payout->valuation > $transaction->balance) {
                    $type = "Долг";
                    $typeTrans = 'debt';
                } else {
                    $type = "Остаток";
                    $typeTrans = 'residue';
                }
                $comment = $type . " за период<br> с <b>" . date('d/m/Y', $dataFilter['date_start']) . "</b> по <b>" . date('d/m/Y', $dataFilter['date_end']);
                $comment .= "</b><br>" . ' Необходимо : ' . $transaction->balance . " грн,<br> Оплатили : " . $payout->valuation . " грн";

                $insert = [
                    'type' => $typeTrans,
                    'comment' => $comment,
                    'initiator' => auth()->user()->id,
                    'time_created' => now(),
                    'time_modified' => now(),
                    'company_id' => $idCompany,
                    'entity' => 'company',
                    'balance' => $transaction->balance - $payout->valuation
                ];
                DB::table($this->table)
                    ->insert($insert);
            }
            return true;
        }
        abort(404);
    }

    public function setPayoutUserId($userId, $idPay)
    {
        $payout = DB::table('finance_payouts')->where('id', $idPay)->first();
        if ($payout) {
            $dataFilter = [
                'date_start' => $payout->period_start,
                'date_end' => $payout->period_end,
            ];
            $transaction = $this->getNoPayoutsUser($userId, $dataFilter);
            DB::table($this->table)
                ->whereBetween('time_created', $dataFilter)
                ->where('user_id', $userId)
                ->where('entity', 'user')
                ->update(['payout_id' => $idPay,
                    'time_modified' => now(),]);
            if ($payout->valuation > $transaction->balance || $payout->valuation < $transaction->balance) {
                if ($payout->valuation > $transaction->balance) {
                    $type = "Долг";
                    $typeTrans = 'debt';
                } else {
                    $type = "Остаток";
                    $typeTrans = 'residue';
                }
                $comment = $type . " за период<br> с <b>" . date('d/m/Y', $dataFilter['date_start']) . "</b> по <b>" . date('d/m/Y', $dataFilter['date_end']);
                $comment .= "</b><br>" . ' Необходимо : ' . $transaction->balance . " грн,<br> Оплатили : " . $payout->valuation . " грн";

                $insert = [
                    'type' => $typeTrans,
                    'comment' => $comment,
                    'initiator' => auth()->user()->id,
                    'time_created' => now(),
                    'time_modified' => now(),
                    'user_id' => $userId,
                    'entity' => 'user',
                    'balance' => $transaction->balance - $payout->valuation
                ];
                DB::table($this->table)
                    ->insert($insert);
            }
            return true;
        }
        abort(404);
    }

    public function getBalanceOperators($filter)
    {
        $countOnePage = 100;
        $operators = DB::table($this->table . ' AS t')
            ->select(
                't.user_id',
                'u.name',
                'u.surname',
                DB::raw('MIN(time_created) as min'),
                DB::raw('MAX(time_created) as max'),
                DB::raw('SUM(t.balance) AS balance'),
                DB::raw('COUNT(approve) as approve'),
                DB::raw('SUM(count_up1) as count_up'),
                DB::raw('SUM(count_up2) as count_up2'),
                DB::raw('SUM(count_cross) as count_cross'),
                DB::raw('SUM(time_system_crm) as time_crm'),
                DB::raw('SUM(time_system_pbx) as time_pbx'),
                DB::raw('SUM(time_talk) as time_talk')
            )
            ->leftJoin('users AS u', 'u.id', '=', 't.user_id')
            ->where('t.time_created', '<', Carbon::today())
            ->where('entity', 'user')
            ->where('payout_id', 0)
            ->where('t.active', 1)
            ->groupBy('t.user_id');

        if (auth()->user()->company_id) {
            $operators = $operators->where('t.company_id', auth()->user()->company_id);
        }

        if ($filter['company']) {
            $offer = explode(',', $filter['company']);
            $operators = $operators->whereIn('t.company_id', $offer);
        }
        if ($filter['operator']) {
            $opers = explode(',', $filter['operator']);
            $operators = $operators->whereIn('t.user_id', $opers);
        }

        $operators = $operators
            ->orderBy('t.id', 'desc')
            ->paginate($countOnePage);
        return [
            'operators' => $operators->appends(Input::except('page')),
        ];
    }

    public function getAllTransactionInUsers($filter)
    {
        $countOnePage = 100;

        $transactions = DB::table($this->table . ' AS t')
            ->select('t.id', 't.order_id', 'of.name AS offer', 't.initiator', 't.type', 't.approve', 't.up1', 't.up2',
                't.cross', 't.count_up1', 't.count_up2', 't.count_cross', 't.balance', 't.time_created', 't.offer_id',
                't.comment', DB::raw('UPPER(t.geo) AS geo'), 'c.currency', 'u.name AS operName', 'u.surname AS operSurname',
                'cc.name AS company', 'cc.type AS cType', 't.time_system_crm AS time_crm', 't.time_system_pbx AS time_pbx',
                't.time_talk AS talk_time', 't.payout_id', 't.result', 't.plan_id')
            ->leftJoin('offers AS of', 't.offer_id', '=', 'of.id')
            ->leftJoin('countries AS c', 'c.code', '=', 't.geo')
            ->leftJoin('users AS u', 'u.id', '=', 't.user_id')
            ->leftJoin('companies as cc', 'cc.id', '=', 'u.company_id')
            ->where('t.entity', 'user')
            ->where('t.active', 1)
            ->where('t.user_id', '>', 0);

        if (auth()->user()->company_id) {
            $transactions = $transactions->where('t.company_id', auth()->user()->company_id);
        }

        if ($filter['date_start']) {
            $start = Carbon::parse($filter['date_start']);
            $transactions = $transactions->where('t.time_created', '>=', $start);
        }

        if ($filter['date_end']) {
            $end = Carbon::parse($filter['date_end'])->endOfDay();
            $transactions = $transactions->where('t.time_created', '<=', $end);
        }

        if ($filter['id']) {
            $transactions = $transactions->where('t.id', $filter['id']);
        }

        if ($filter['oid']) {
            $transactions = $transactions->where('t.order_id', $filter['oid']);
        }
        if ($filter['company']) {
            $offer = explode(',', $filter['company']);
            $transactions = $transactions->whereIn('t.company_id', $offer);
        }
        if ($filter['country']) {
            $offer = explode(',', $filter['country']);
            $transactions = $transactions->whereIn('t.geo', $offer);
        }

        if ($filter['trans_payed']) {
            $transactions = $transactions->where('t.payout_id', '>', 0);
        } else {
            $transactions = $transactions->where('t.payout_id', 0);
        }

        $transactions = $transactions
            ->orderBy('t.id', 'desc')
            ->paginate($countOnePage);
        $orderIds = [];

        if ($transactions->items()) {
            foreach ($transactions->items() as $transaction) {
                if ($transaction->cType == 'lead') {
                    $orderIds[] = $transaction->order_id;
                }
                if (!empty($transaction->plan_id) && $transaction->plan_id !== 0 && $transaction->plan_id !== NULL) {
                    $transaction->plan = Plan::where('id', $transaction->plan_id)->first();
                }
            }
        }

        $products = DB::table('order_products AS op')
            ->select('p.title', 'op.order_id', 'op.type', 'op.price')
            ->leftJoin('products AS p', 'op.product_id', '=', 'p.id')
            ->whereIn('op.order_id', $orderIds)
            ->get();

        $orderProducts = [];
        if ($products) {
            $i = 0;
            foreach ($products as $product) {
                $currency = '';
                if (isset($transactions->items()[$product->order_id])) {
                    $currency = $transactions->items()[$product->order_id]->currency;
                }
                $orderProducts[$product->order_id][$i]['title'] = $product->title;
                $orderProducts[$product->order_id][$i]['price'] = $product->price;
                $orderProducts[$product->order_id][$i]['currency'] = $currency;
                $orderProducts[$product->order_id][$i]['type'] = $product->type;
                $i++;
            }
        }
        return [
            'orders' => $transactions->appends(Input::except('page')),
            'products' => $orderProducts,
        ];

    }

    public function setData($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function setInActiveTransaction($orderId, $userId = false, $type = 'approve')
    {
        $res =  DB::table($this->table)
            ->where('order_id', $orderId);
        if ($userId) {
            $res->where('user_id', $userId);
        }
        return $res->where('type', $type)
            ->update(['active'  => 0]);
    }

    public static function createProjectTransactions(Pass $pass, Request $request)
    {
        $ordersPasses = $pass->ordersPass()->with('order', 'order.targetUser')->get();

        switch ($pass->type) {
            case Pass::TYPE_REDEMPTION : {
                if ($ordersPasses->count()) {
                    foreach ($ordersPasses as $orderPass) {
                        self::createRedemptionTransaction($pass, $orderPass);
                    }
                }
                break;
            }
            case Pass::TYPE_NO_REDEMPTION : {
                if ($ordersPasses->count()) {
                    foreach ($ordersPasses as $orderPass) {
                        self::createNoRedemptionTransaction($pass, $orderPass);//todo перепроверить
                    }
                }
                break;
            }
            case Pass::TYPE_SENDING : {
                if ($ordersPasses->count()) {
                    foreach ($ordersPasses as $orderPass) {
                        self::createSendingTransaction($pass, $orderPass);
                    }
                }
                break;
            }
        }
    }

    public static function createRedemptionTransaction(Pass $pass, OrdersPass $orderPass)
    {
        $order = $orderPass->order;
        if (!$order) {
            return false;
        }
        $transaction = new self();
        $transaction->setDataSubProjectTransaction($order, $pass, self::TYPE_REDEMPTION);

        $transaction->balance_after = $transaction->balance_before + $transaction->order_price;

        return $transaction->save();
    }

    public static function createNoRedemptionTransaction(Pass $pass, OrdersPass $orderPass)
    {
        $order = $orderPass->order;
        if (!$order) {
            return false;
        }
        $transaction = new self();
        $transaction->setDataSubProjectTransaction($order, $pass, self::TYPE_NO_REDEMPTION);

        $transaction->cost_return = $orderPass->cost_return;
        $transaction->balance_after = $transaction->balance_before - $transaction->cost_return;

        return $transaction->save();
    }

    public static function createSendingTransaction(Pass $pass, OrdersPass $orderPass)
    {
        $order = $orderPass->order;
        if (!$order) {
            return false;
        }

        $transaction = new self();
        $transaction->setDataSubProjectTransaction($order, $pass, self::TYPE_SENDING);

        $transaction->cost_actual = $orderPass->cost_actual * -1;
        $transaction->balance_after = $transaction->balance_before + $transaction->cost_actual;

        return $transaction->save();
    }

    public function setDataSubProjectTransaction(Order $order, Pass $pass, $type)
    {
        $this->type = $type;
        $this->order_id = $order->id;
        $this->company_id = $order->targetUser ? +$order->targetUser->company_id : 0;
        $this->sub_project_id = $order->subproject_id;
        $this->entity = self::ENTITY_SUB_PROJECT;
        $this->user_id = $pass->user_id;
        $this->initiator = 'System';
        $this->offer_id = $order->offer_id;
        $this->pass_id = $pass->id;
        $this->geo = $order->geo;
        $this->time_created = now();
        $this->time_modified = now();
        $this->order_price = $order->price_total;
        $this->balance_before = 0;
        $this->cost_return = 0;
        $this->cost_actual = 0;

        $lastTransaction = self::where([
            ['sub_project_id', $order->subproject_id],
            ['entity', self::ENTITY_SUB_PROJECT],
        ])->orderBy('id', 'desc')->first();
        if ($lastTransaction) {
            $this->balance_before = $lastTransaction->balance_after;
        }
    }

    public function createCancelSendTransaction(Order $order)
    {
        $this->type = self::TYPE_CANCEL_SEND;
        $this->order_id = $order->id;
        $this->company_id = $order->targetUser ? +$order->targetUser->company_id : 0;
        $this->sub_project_id = $order->subproject_id;
        $this->entity = self::ENTITY_SUB_PROJECT;
        $this->user_id = Auth::user()->id ?? 0;
        $this->initiator = 'System';
        $this->offer_id = $order->offer_id;
        $this->geo = $order->geo;
        $this->time_created = now();
        $this->time_modified = now();
        $this->order_price = $order->price_total;
        $this->balance_before = 0;
        $this->cost_return = 0;
        $this->cost_actual = 0;

        $lastTransaction = self::where([
            ['sub_project_id', $order->subproject_id],
            ['entity', self::ENTITY_SUB_PROJECT],
        ])->orderBy('id', 'desc')->first();
        if ($lastTransaction) {
            $this->balance_before = $lastTransaction->balance_after;
            $this->cost_actual =  (self::where([
                ['sub_project_id', $order->subproject_id],
                ['type', self::TYPE_SENDING],
                ['entity', self::ENTITY_SUB_PROJECT],
                ['order_id', $order->id]
            ])->orderBy('id', 'desc')->value('cost_actual') ?? 0) * -1;
        }
        $this->balance_after = $this->balance_before + $this->cost_actual;
    }

    public static function getFinanceSubProject($filter)
    {
        $query = self::select(
            DB::raw('SUM(cost_actual) AS cost_actual'),
            DB::raw('SUM(cost_return) AS cost_return'),
            'sub_project_id'
        )
            ->where([
                ['entity', self::ENTITY_SUB_PROJECT],
            ]);

        if ($filter['project']) {
            $subProjectIds = Project::subProject()->where('parent_id', $filter['project'])->pluck('id');
            $query->whereIn('sub_project_id', $subProjectIds);
        }
        if ($filter['sub_project']) {
            $query->where('sub_project_id', $filter['sub_project']);
        }
        if ($filter['date_start']) {
            $query->whereHas('pass', function ($q) use ($filter) {
                $q->where('time_modified', '>=', $filter['date_start'] . ' 00:00:00');
            });
        }
        if ($filter['date_end']) {
            $query->whereHas('pass', function ($q) use ($filter) {
                $q->where('time_modified', '<=', $filter['date_end'] . ' 23:59:59');
            });
        }

        return $query->groupBy('sub_project_id')->get()->keyBy('sub_project_id');
    }
}