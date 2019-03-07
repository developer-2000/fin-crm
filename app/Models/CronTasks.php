<?php

namespace App\Models;


use App\Classes\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CronTasks extends BaseModel
{
    /**
     * Name table orders
     * @orders string
     */
    protected $orders = 'orders';
    /**
     * Name table company_elastix
     * @company_elastix string
     */
    protected $company_elastix = 'company_elastix';

    /**
     * @callProgressLogModel  CallProgressLog
     */
    protected $callProgressLogModel;

    /**
     * @ordersModel Order
     */
    protected $ordersModel;

    /**
     * @companyElastixModel Campaign
     */
    protected $companyElastixModel;

    /**
     * @companyElastixModel Transaction
     */
    protected $transactionModel;

    /**
     * @userTimeModel UsersTime
     */
    protected $userTimeModel;

    /**
     * @sessionAudit SessionAudit
     */
    protected $sessionAudit;

    public function __construct(
        CallProgressLog $callProgressLog,
        Order $order,
        Campaign $companyElastix,
        Transaction $transaction,
        UsersTime $usersTime,
        SessionAudit $sessionAudit
    ) {
        $this->callProgressLogModel = $callProgressLog;
        $this->ordersModel = $order;
        $this->companyElastixModel = $companyElastix;
        $this->transactionModel = $transaction;
        $this->userTimeModel = $usersTime;
        $this->sessionAudit = $sessionAudit;
    }

    /**
     * send orders in Elastix at night
     */
    public function nightShift()
    {
        $nightTimeStart = Carbon::parse('now 21:00:00');
        $nightTimeEnd = Carbon::parse('now 07:00:00');
        $time = now();
        if ($time >= $nightTimeStart || $time <= $nightTimeEnd) {
            echo "--NightShift--\n";
            if ($time >= $nightTimeStart) {
                $nightTimeEnd = Carbon::tomorrow('Europe/Kiev')->addHours(7);
            } else {
                $nightTimeStart = Carbon::yesterday('Europe/Kiev')->addHour(21);
            }
//            $nightTimeStart = $nightTimeStart->addMinute(15);
            DB::table($this->company_elastix)
                ->where('id', '!=', 22)
                ->where('id', '!=', 10)
                ->update(['cron_status' => 0]);

            DB::table($this->orders . ' AS o')
                ->leftJoin('company_elastix AS ce', 'o.proc_campaign', '=', 'ce.id')
                ->whereBetween('o.time_created', [$nightTimeStart->toDateTimeString(), $nightTimeEnd->toDateTimeString()])
                ->where('o.proc_status', 1)
                ->where('ce.auto_call', 1)
                ->where('o.geo', '!=', 'vn')
                ->where('o.geo', '!=', 'in')
                ->update(['o.proc_campaign' => 22]);                             //22 - nightShift
            $agents = $this->apiElastixProcessing('getCountAgents');
            $ordersFromElastix = $this->apiElastixProcessing2('getCallsNoProcessing');
            if ($ordersFromElastix->status != 200) {
                die('Not got orders from elastix');
            }
            $countOrdersInCampaigns = collect($ordersFromElastix->data)->keyBy('id_campaign');
            if ($agents) {
                $arrayLog = [];
                foreach ($agents as $agent) {
                    if ($agent->queue == 22) {
                        $countOrders = 3;          //3 - quantity orders to one operator
                        $ordersInCampaign = isset($countOrdersInCampaigns[$agent->queue]) ? $countOrdersInCampaigns[$agent->queue]->count : 0;
                        $ordersOperator = $ordersInCampaign / $agent->count;
                        if ($ordersOperator >= $countOrders) {
                            echo("Operators are too busy in group " . $agent->queue . "!\n");
                            continue;
                        }
                        $limit = $countOrders * $agent->count - $ordersInCampaign;
                        $orders = DB::table($this->orders)
                            ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time')
                            ->whereBetween('time_created', [$nightTimeStart->subMinutes(15)->toDateTimeString(), $nightTimeEnd->toDateTimeString()])
                            ->whereBetween('proc_time', [$time->subMinutes(15)->toDateTimeString(), $time->addMinutes(15)->toDateTimeString()])
                            ->where('proc_status', 1)
                            ->where('entity', 'order')
                            ->where('proc_stage', '<=', 1)
                            ->where('proc_campaign', $agent->queue)
                            ->limit((int)$limit)
//                            ->orderBy('proc_time', 'desc')
//                            ->orderBy('proc_callback_time', 'desc')
//                            ->orderBy('proc_stage')
                            ->orderBy('id', 'desc')
                            ->orderBy('proc_stage')
                            ->get();

                        $arrayLog[$agent->queue] = [
                            'group'       => $agent->queue,
                            'all_agents'  => $agent->count,
                            'count_order' => count($orders),
                            'success'     => 0,
                            'error'       => 0
                        ];

                        if ($orders) {
                            $data = [];
                            $orderIds = [];
                            $ordersLog = [];
                            foreach ($orders as $order) {
                                $key = $order->id;
                                $orderIds[] = $key;
                                $data[$key]['id'] = $order->id;
                                $data[$key]['phone'] = $order->phone;
                                $data[$key]['before'] = 0;
                                $data[$key]['company'] = $order->proc_campaign;
                                $data[$key]['weight'] = 3;
                                $data[$key]['entity'] = 'order';
                                if (!$order->proc_stage) {
                                    $data[$key]['weight'] = -1;
                                } elseif ($order->proc_stage <= 2) {
                                    $data[$key]['weight'] = 0;
                                } elseif ($order->proc_stage >= 3 && $order->proc_stage <= 4) {
                                    $data[$key]['weight'] = 1;
                                } elseif ($order->proc_stage = 5) {
                                    $data[$key]['weight'] = 2;
                                }
                                if ($order->proc_callback_time && $order->proc_stage) {
                                    $data[$key]['weight'] = -2;
                                }
                                $ordersLog[$key] = [
                                    'order_id'    => $order->id,
                                    'user_id'     => 0,
                                    'text'        => 'Установлен процессинг статус "В наборе"',
                                    'date'        => now(),
                                    'status_id'   => 2,
                                    'status_name' => 'В наборе'
                                ];
                            }
                            DB::table($this->orders)->whereIn('id', $orderIds)->update(['proc_status' => 13]);
                            $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                            if ($resultApi) {
                                if ($resultApi->status == 200) {
                                    $arrayLog[$agent->queue]['success'] = count($resultApi->data);
                                    $changeStatus = [
                                        'proc_status'         => 2,
//                                        'proc_callback_time'  => null,
                                        'time_status_updated' => Carbon::now(),
                                    ];
                                    DB::table($this->orders)->whereIn('id', $resultApi->data)
                                        ->update($changeStatus);
                                    if (!empty($ordersLog)) {
                                        DB::table(OrdersLog::tableName())->insert($ordersLog);
                                    }
                                }
                            }
                        }
                    }
                }
                echo json_encode($arrayLog) . "\n";
            }
        } else {
            echo "--DayShift--\n";
            DB::table($this->company_elastix)
                ->where('id', '!=', 22)
                ->update(['cron_status' => 1]);
            $this->changeCampaign(22);
        }
    }

    public function setTimePBX()
    {
        $onlineUsers = $this->apiElastixProcessing2('checkOnlineUser');
        $logs = [];
        if ($onlineUsers->status == 200) {
            $userTimeModel = $this->userTimeModel;
            $timeUsers = collect($userTimeModel->getUserWithoutTimeEnd(null, 'pbx', true))->keyBy('user_id');
            $userAdd = [];
            $userEnd = [];
            foreach ($onlineUsers->data as $loginSip => $user) {
                $userId = DB::table('users')->where('login_sip', $loginSip)->value('id');
                if (!isset($timeUsers[$userId]) && $userId) {
                    $data = [
                        'user_id'        => $userId,
                        'type'           => 'pbx',
                        'datetime_start' => now()
                    ];
                    $userTimeModel->addTime($data);
                    $userAdd[] = $userId;
                }
            }

            if ($timeUsers) {
                foreach ($timeUsers as $userId => $user) {
                    $loginSip = DB::table('users')->where('id', $userId)->value('login_sip');
                    if (!isset($onlineUsers->data->$loginSip)) {
                        $data = [
                            'datetime_end' => now(),
                        ];
                        $userTimeModel->setTime($data, $userId, 'pbx');
                        $userEnd[] = $userId;
                    }
                }
            }
            $logs[date('H:i:s d.m.y', time()) . ' pbx'] = [
                'add' => $userAdd,
                'end' => $userEnd
            ];
        }
        echo json_encode($logs) . "\n";
    }

    public function checkOnlineUser()
    {
//        $client = new Client(env('SOCKET_SERVER_URL'));

        $userTimeModel = $this->userTimeModel;
        $users = $userTimeModel->getUserWithoutTimeEnd(null, 'crm', true);
        $logs = [];
        if ($users) {
            $dataLog = [];
            foreach ($users AS $user) {
                $events = $this->sessionAudit->getEventsUserLastTime($user->user_id, 60);
                if ($events->isEmpty()) {
                    $data = [
                        'datetime_end' => date('Y-m-d H:i:s', time() - 60 * 60)
                    ];
                    $userTimeModel->setTime($data, $user->user_id, 'crm');
                    DB::table('sessions')->where('user_id', $user->user_id)->delete();

                    try {
                        $result['key'] = 'logout';
                        $result['data'] = $user->user_id;
                        $send = json_encode($result);
//                        $client->send($send);
                    } catch (\Exception $exception) {

                    }

                    $dataLog[] = $user->user_id;
                }
            }
            $logs[date('H:i:s d.m.y', time()) . ' crm'] = $dataLog;
        }
        echo json_encode($logs) . "\n";
    }

    public function setEndTimeForUsers()
    {
        $userTimeModel = $this->userTimeModel;
        $users = $userTimeModel->getUserWithoutTimeEnd();
        $dataLog = [];
        if ($users) {
            $insert = [];
            foreach ($users as $user) {
                $data = [
                    'datetime_end' => now(),
                    'duration'     => now()->timestamp - Carbon::parse($user->datetime_start)->timestamp,
                ];
                $userTimeModel->setDataTime($user->user_id, $data, $user->type);
                $events = $this->sessionAudit->getEventsUserLastTime($user->user_id, 60);
                if (!$events->isEmpty() && $user->type == 'crm') {
                    $insert[] = [
                        'user_id'        => $user->user_id,
                        'type'           => 'crm',
                        'datetime_start' => now()
                    ];
                    $dataLog['start ' . $user->type][] = $user->user_id;
                }
                $dataLog['end ' . $user->type][] = $user->user_id;
            }
            $userTimeModel->addTime($insert);
        }
        $logs[date('Y-m-d H:i:s', time())][] = $dataLog;
        echo json_encode($logs) . "\n";
    }

    public function setTimeTransaction()
    {
        $yesterday = true;
        $users = $this->userTimeModel->getUsersByCompany($yesterday);

        $talkTimes = collect($this->callProgressLogModel->getTalkTimeByOperator(false, $yesterday))->keyBy('user_id');

        $timeUser = [];
        $timeCompany = [];

        if ($users) {
            foreach ($users as $user) {

                if ($user->company_type == 'hour') {
                    $prices = json_decode($user->prices, true);
                    if (isset($prices['global'])) {
                        $prices = $prices['global'];
                        $priceSecondCrm = $prices['in_system'] / 60 / 60;
                        $priceSecondTalk = $prices['in_talk'] / 60 / 60;
                        $companyTimeCrm = $user->type == 'crm' ? $user->duration : 0;
                        $companyTimePbx = $user->type == 'pbx' ? $user->duration : 0;
                        if (isset($timeCompany[$user->user_id])) {
                            $timeCompany[$user->user_id]['time_system_crm'] += $companyTimeCrm;
                            $timeCompany[$user->user_id]['time_system_pbx'] += $companyTimePbx;
                            $balance = $priceSecondCrm * $timeCompany[$user->user_id]['time_system_crm'] + $priceSecondTalk * $timeCompany[$user->user_id]['time_talk'];
                            $timeCompany[$user->user_id]['balance'] = round($balance);
                        } else {
                            $companyTimeCrm = $user->type == 'crm' ? $user->duration : 0;
                            $companyTimePbx = $user->type == 'pbx' ? $user->duration : 0;
                            $talkTime = isset($talkTimes[$user->user_id]) ? $talkTimes[$user->user_id]->talk_time : 0;
                            $balance = $priceSecondCrm * $companyTimeCrm + $priceSecondTalk * $talkTime;
                            $timeCompany[$user->user_id] = [
                                'type'            => 'approve',
                                'company_id'      => $user->company_id,
                                'entity'          => 'company',
                                'user_id'         => $user->user_id,
                                'initiator'       => 'System',
                                'time_created'    => now(),
                                'time_modified'   => now(),
                                'balance'         => round($balance),
                                'time_system_crm' => $companyTimeCrm,
                                'time_system_pbx' => $companyTimePbx,
                                'time_talk'       => $talkTime
                            ];
                        }
                    }
                }
                if ($user->billing_type == 'hour') {
                    $prices = json_decode($user->billing, true);
                    if (isset($prices['global'])) {
                        $prices = $prices['global'];
                        $priceSecondCrm = $prices['in_system'] / 60 / 60;
                        $priceSecondTalk = $prices['in_talk'] / 60 / 60;
                        $userTimeCrm = $user->type == 'crm' ? $user->duration : 0;
                        $userTimePbx = $user->type == 'pbx' ? $user->duration : 0;
                        if (isset($timeUser[$user->user_id])) {
                            $timeUser[$user->user_id]['time_system_crm'] += $userTimeCrm;
                            $timeUser[$user->user_id]['time_system_pbx'] += $userTimePbx;
                            $balance = $priceSecondCrm * $timeUser[$user->user_id]['time_system_crm'] + $priceSecondTalk * $timeUser[$user->user_id]['time_talk'];
                            $timeUser[$user->user_id]['balance'] = round($balance);
                        } else {
                            $userTalkTime = isset($talkTimes[$user->user_id]) ? $talkTimes[$user->user_id]->talk_time : 0;
                            $balance = $priceSecondCrm * $userTimeCrm + $priceSecondTalk * $userTalkTime;
                            $timeUser[$user->user_id] = [
                                'type'            => 'approve',
                                'company_id'      => $user->company_id,
                                'entity'          => 'user',
                                'user_id'         => $user->user_id,
                                'initiator'       => 'System',
                                'time_created'    => now(),
                                'time_modified'   => now(),
                                'balance'         => round($balance),
                                'time_system_crm' => $userTimeCrm,
                                'time_system_pbx' => $userTimePbx,
                                'time_talk'       => $userTalkTime
                            ];
                        }
                    }
                }
            }

            $this->transactionModel->setData($timeUser);
            $this->transactionModel->setData($timeCompany);
        }
        $logs[date('Y-m-d H:i:s', time())] = [
            'count company transaction' => count($timeCompany),
            'count user transaction'    => count($timeUser),
        ];

        echo json_encode($logs) . "\n";
    }


    public function testCampaign()
    {
        $nightTimeStart = Carbon::parse('now 07:00:00');
        $nightTimeEnd = Carbon::parse('now 21:00:00');
        $time = now();

        if ($time >= $nightTimeStart && $time <= $nightTimeEnd) {
            $agents = $this->apiElastixProcessing('getCountAgents');
            echo date('H:i:s d/m/y') . " getCountAgents\n";
            $ordersFromElastix = $this->apiElastixProcessing2('getCallsNoProcessing', ['entity' => 'order']);
            echo date('H:i:s d/m/y') . " getCallsNoProcessing\n";
            if ($ordersFromElastix->status != 200) {
                die('Not got orders from elastix');
            }
            if ($agents) {
                //$countOrders = 15;
                $countOrders = 500;
                $countOrdersInCampaigns = collect($ordersFromElastix->data)->keyBy('id_campaign');
                $queue = collect(DB::table('company_elastix')->where('learning', 1)->where('cron_status', 1)
                    ->get())->keyBy('id');
                echo date('H:i:s d/m/y') . " get all learning queues\n";
                foreach ($agents as $ag) {
                    if (isset($queue[$ag->queue])) {
                        $ordersInCampaign = isset($countOrdersInCampaigns[$ag->queue]) ? $countOrdersInCampaigns[$ag->queue]->count : 0;
                        $ordersOperator = $ordersInCampaign / $ag->count;
                        $countInProcessing = DB::table($this->orders)->where('proc_time', '<=', $time)
                            ->where('proc_status', 1)
                            ->where('entity', 'order')
                            ->where('proc_campaign', $ag->queue)
                            ->count();
//                        if ($ordersOperator >= $countOrders || $countInProcessing >= $countOrders * $ag->count) {
//                            echo("Operators are too busy in group " . $ag->queue . "!\n");
//                            continue;
//                        }
                     //   $limit = $countOrders * $ag->count - $ordersInCampaign;
                        $limit = 50;

                        $offer = [];
                        if ($queue[$ag->queue]->offer) {
                            $offer = json_decode($queue[$ag->queue]->offer, true);
                        }

                        $country = [];
                        if ($queue[$ag->queue]->country) {
                            $country = json_decode($queue[$ag->queue]->country, true);
                        }
                        $query = DB::table($this->orders . ' as o')
                            ->select('o.id', 'o.proc_status')
                           ->where('o.proc_campaign', '!=', 50)
                            ->where('o.subproject_id', 23)
                            //->where('o.project_id', 12)
                            ->whereIn('o.proc_status', [1, 2])
                            ->where('o.entity', 'order')
                            ->where('o.target_status', 0)
                            ->where('o.proc_time', '<', now())
                        ;
//                        if ($offer) {
//                            $query = $this->createQueryForTestQueues($offer, 'o.offer_id', $query);
//                        }
//                        if ($country) {
//                            $inCountry = [];
//                            $notInCountry = [];
//                            foreach ($country as $of) {
//                                if ($of[1] == 1) {
//                                    $inCountry[] = $of[0];
//                                } else {
//                                    $notInCountry[] = $of[0];
//                                }
//                            }
//                            if ($inCountry || $notInCountry) {
//                                $query = $query->leftJoin('countries as c', 'c.code', '=', 'o.geo');
//                            }
//                            if ($inCountry) {
//                                $query = $query->whereIn('c.id', $inCountry);
//                            }
//                            if ($notInCountry) {
//                                $query = $query->whereNotIn('c.id', $notInCountry);
//                            }
//                        }

//                        $minCallCount = $queue[$ag->queue]->min_call_count ? $queue[$ag->queue]->min_call_count : 0;

                        $query = $query->where('entity', 'order')
                            ->limit((int)$limit)
                            ->orderBy('proc_time', 'desc');

                        $orders = $query->get();

                        echo date('H:i:s d/m/y') . " get orders for learning\n";

                        $ids = [];
                        if ($orders) {
                            foreach ($orders as $order) {
                                $ids[] = $order->id;
                                if ($order->proc_status == 2) {
                                    $this->ordersModel->apiElastixProcessing2('deleteCall', [
                                        'id'     => $order->id,
                                        'entity' => 'order'
                                    ]);
                                    echo date('H:i:s d/m/y') . " deleted call " . $order->id . "\n";
                                }
                            }
                        }

                        DB::table($this->orders)->whereIn('id', $ids)
                            ->update(['proc_campaign' => $ag->queue, 'proc_status' => 1]);

                        echo 'count update orders ' . count($orders) . "<br>";

                    } else {
                        $learning = Campaign::find($ag->queue);
                        if (isset($learning->learning) && $learning->learning == 1) {
                            echo "deleted orders from " . $learning->id . "\n";
                            $this->changeCampaign($learning->id);
                        }
                    }
                }
            }
        }
    }

    public function addLearningCalls()
    {
        $nightTimeStart = Carbon::parse('now 07:00:00');
        $nightTimeEnd = Carbon::parse('now 21:00:00');
        $time = now();
        $arrayLog = [];
        if ($time >= $nightTimeStart && $time <= $nightTimeEnd) {
            $agents = $this->apiElastixProcessing('getCountAgents');
            echo date('H:i:s d/m/y') . " getCountAgents\n";
            $ordersFromElastix = $this->apiElastixProcessing2('getCallsNoProcessing', ['entity' => 'order']);
            echo date('H:i:s d/m/y') . " getCallsNoProcessing\n";
            if ($ordersFromElastix->status != 200) {
                die('Not got orders from elastix');
            }
            if ($agents) {
                $countOrders = 15;
                $countOrdersInCampaigns = collect($ordersFromElastix->data)->keyBy('id_campaign');
                $queue = collect(DB::table('company_elastix')->where('learning', 1)->where('cron_status', 1)
                    ->get())->keyBy('id');
                echo date('H:i:s d/m/y') . " get all learning queues\n";
                foreach ($agents as $ag) {
                    if (isset($queue[$ag->queue])) {
                        $ordersInCampaign = isset($countOrdersInCampaigns[$queue[$ag->queue]->id]) ? $countOrdersInCampaigns[$queue[$ag->queue]->id]->count : 0;
                        $ordersOperator = $ordersInCampaign / $ag->count;
//                        if ($ordersOperator >= $countOrders) {
//                            echo("Operators are too busy in group " . $ag->queue . "!\n");
//                            continue;
//                        }
                        //$limit = $countOrders * $ag->count - $ordersInCampaign;
                        $limit = 50;
                        $result = DB::table($this->orders)
                            ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time', 'proc_priority', 'proc_stage')
                            ->limit($limit)
                            ->where('proc_time', '<=', $time)
                            ->where('proc_status', 1)
                            ->where('entity', 'order')
                            ->where('moderation_id', 0)
                            ->where('proc_campaign', $ag->queue)
                            ->orderBy('proc_time', 'desc')
                            ->orderBy('id', 'desc')
                            ->orderBy('proc_stage')
                            ->get();
//                        echo date('H:i:s d/m/y') . " limit = " . $limit . "\n";
                        echo date('H:i:s d/m/y') . " get orders in queue" . $ag->queue . "\n";
                        $arrayLog[$ag->queue] = [
                            'group'       => $ag->queue,
                            'time'        => date('Y.m.d H:i.s'),
                            'all_agents'  => $ag->count,
                            'count_order' => count($result),
                            'success'     => '',
                            'error'       => ''
                        ];
                        if ($result) {
                            $data = [];
                            $arrayIds = [];
                            foreach ($result as $kr => $vr) {
                                echo $vr->id . "\n";
                                $arrayIds[] = $vr->id;
                                $data[$kr]['id'] = $vr->id;
                                $data[$kr]['phone'] = $vr->phone;
                                $data[$kr]['before'] = 0;
                                $data[$kr]['company'] = $vr->proc_campaign;
                                $data[$kr]['weight'] = $vr->proc_stage;
                                $data[$kr]['entity'] = 'order';

                                if ($vr->proc_callback_time && $vr->proc_priority) {
                                    $data[$kr]['weight'] = $vr->proc_priority;
                                }
                            }

                            echo date('H:i:s d/m/y') . " created array data \n";

                            DB::table($this->orders)->whereIn('id', $arrayIds)->update(['proc_status' => 13]);
                            echo date('H:i:s d/m/y') . " updated status 13 \n";
                            $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                            echo date('H:i:s d/m/y') . " added calls to pbx \n";
                            if ($resultApi) {
                                if ($resultApi->status == 200) {

                                    $arrayLog[$ag->queue]['success'] = count($resultApi->data);
                                    $changeStatus = [
                                        'proc_status'        => 2,
//                                        'proc_callback_time' => null,
                                        'proc_priority'      => 0,
                                    ];
                                    DB::table($this->orders)->whereIn('id', $resultApi->data)
                                        ->update($changeStatus);
                                    echo date('H:i:s d/m/y') . " updated status 2 \n";
                                }
                            }
                            $arrayLog[$ag->queue]['error'] = $arrayLog[$ag->queue]['count_order'] - $arrayLog[$ag->queue]['success'];
                        }
                    }
                }
            }
        }
        echo json_encode($arrayLog) . "\n";
    }

    protected function createQueryForTestQueues($data, $fieldName, $query)
    {
        $inOffer = [];
        $notInOffer = [];
        if ($data) {
            foreach ($data as $of) {
                if ($of[1] == 1) {
                    $inOffer[] = $of[0];
                } else {
                    $notInOffer[] = $of[0];
                }
            }
        }
        if ($inOffer) {
            $query = $query->whereIn($fieldName, $inOffer);
        }
        if ($notInOffer) {
            $query = $query->whereNotIn($fieldName, $notInOffer);
        }
        return $query;
    }

    public function changeCampaign($campaign_id)
    {
        $orders = DB::table($this->orders)
            ->select('id', 'project_id', 'offer_id', 'geo')
            ->where('proc_campaign', $campaign_id)
            ->whereIn('proc_status', [1, 2])
            ->get();
        if ($orders) {
            foreach ($orders as $order) {
                $this->ordersModel->apiElastixProcessing2('deleteCall', [
                    'id'     => $order->id,
                    'entity' => 'order'
                ]);
                $countryId = DB::table('countries')->where('code', strtoupper($order->geo))->value('id');
                $procCampaign = $this->companyElastixModel->getElastixCompanyByFilter($order->project_id, $countryId, 0, $order->offer_id);
                DB::table($this->orders)->where('id', $order->id)
                    ->update(['proc_campaign' => $procCampaign, 'proc_status' => 1]);
            }
        }
    }


    public function getOrdersInProcessing()
    {
        $client = new Client(env('SOCKET_SERVER_URL'));
        while (true) {
            $result['key'] = 'getOrdersInProcessing';
            $result['data'] = $this->ordersModel->getOrdersInProcessing();
            $send = json_encode($result);
            $client->send($send);
            sleep(2);
        }
        echo "disconnect\n";
    }

    public function getOrderToday()
    {
        $client = new Client(env('SOCKET_SERVER_URL'));
        while (true) {
            $result['key'] = 'getOrderToday';
            $result['data'] = $this->ordersModel->getOrderToday();
            $send = json_encode($result);
            $client->send($send);
            sleep(2);
        }
        echo "disconnect\n";
    }

    public function addToCampaign()
    {
//        DB::table($this->orders)
//            ->where([['price_input', 1], ['offer_id', '!=', 3583]])
//            ->where('proc_status', 1)
//            ->update(['proc_campaign' => 48]);
//        DB::table($this->orders)
//            ->where([['price_input', 1], ['offer_id', '=', 3583]])
//            ->where('proc_status', 1)
//            ->update(['proc_campaign' => 7]);
    }

    /**
     * @param $type string (e.g.: week, month)
     */
    public function rateTransaction($type)
    {
        $companies = Company::all();
        $transactions = [];
        $logs = [];
        $logs['time'] = date('d-m-Y H:i:s');
        if ($companies) {
            foreach ($companies as $company) {
                $companyPrice = json_decode($company->prices, true);
                $companyTransactions = $this->checkTypeTransaction($company->id, $companyPrice, 'company', $type);
                if ($companyTransactions) {
                    $transactions = array_merge($transactions, $companyTransactions);
                    $logs['company_' . $company->id]['companyTransaction'] = count($companyTransactions);
                }
                if ($company->billing_type && $company->billing) {
                    $billingPrice = json_decode($company->billing, true);
                    $billingTransaction = $this->checkTypeTransaction($company->id, $billingPrice, 'user', $type);
                    if ($billingTransaction) {
                        $transactions = array_merge($transactions, $billingTransaction);
                        $logs['company_' . $company->id]['billingTransaction'] = count($billingTransaction);
                    }
                }
            }
        }
        $logs['all_transaction'] = count($transactions);
        if ($transactions) {
            DB::table('finance_transaction')->insert($transactions);
        }
        echo json_encode($logs) . "\n";
    }

    /**
     * @param $companyId integer
     * @param $prices array
     * @param $entity string
     * @param $type string
     */
    protected function checkTypeTransaction($companyId, $prices, $entity, $type)
    {
        $userIds = [];
        $result = [];
        if (isset($prices['users'])) {
            foreach ($prices['users'] as $userId => $price) {
                if ($price['type'] == $type) {
                    $userIds[$userId] = $userId;
                    $result[$userId] = $this->createRateTransaction($companyId, $entity, $userId, $type, $price['rate']);
                }
            }
        }
        if (isset($prices['ranks'])) {
            foreach ($prices['ranks'] as $rankId => $price) {
                if ($price['type'] == $type) {
                    $rankUsers = User::where('company_id', $companyId)
                        ->where('rank_id', $rankId)
                        ->whereNotIn('id', $userIds)
                        ->get();
                    if ($rankUsers) {
                        foreach ($rankUsers as $user) {
                            if (!isset($result[$user->id])) {
                                $userIds[$user->id] = $user->id;
                                $result[$user->id] = $this->createRateTransaction($companyId, $entity, $user->id, $type, $price['rate']);
                            }
                        }
                    }
                }
            }
        }
        if (isset($prices['global']) && $prices['global']['type'] == $type) {
            $users = User::where('company_id', $companyId)
                ->whereNotIn('id', $userIds)
                ->get();
            if ($users) {
                foreach ($users as $user) {
                    if (!isset($result[$user->id])) {
                        $userIds[$user->id] = $user->id;
                        $result[$user->id] = $this->createRateTransaction($companyId, $entity, $user->id, $type, $prices['global']['rate']);
                    }
                }
            }
        }
        return $result;
    }

    protected function createRateTransaction($companyId, $entity, $userId, $type, $rate)
    {
        $comment = '';
        if ($type == 'week') {
            $comment = Carbon::now()->subDays(7)->format('d-m-Y H:i:s') . ' - ' . Carbon::now()->format('d-m-Y H:i:s');
        }
        if ($type == 'month') {
            $comment = Carbon::now()->subDays(30)->format('m-Y') . ' - ' . Carbon::now()->format('m-Y');
        }
        return [
            'type'          => $type,
            'company_id'    => $companyId,
            'entity'        => $entity,
            'user_id'       => $userId,
            'initiator'     => 'System',
            'time_created'  => now(),
            'time_modified' => now(),
            'balance'       => $rate,
            'active'        => 1,
            'comment'       => $comment,
        ];
    }

    public function vietnam()
    {
        $agents = (array)$this->apiElastixProcessing('getCountAgents');
        $idDayShift = 10;
        $idNightShift = 52;
        $idDayShiftSamsung = 70;
        $timeNow = now();
        $arrayLog = [];
//18 - 03 nightShift vn
        $timeStart = Carbon::parse('now 02:00:00');
        $timeEnd = Carbon::parse('now 17:00:00');

        if ($timeStart <= $timeNow && $timeNow < $timeEnd) {//dayShift
            echo "dayShift\n";

            if ($timeStart < $timeNow->addMinute(10)) {
                $this->changeCampaign($idNightShift);
            }

            if (isset($agents[$idDayShift])) {
                $operatorCount = 30;
                $result = DB::table($this->orders)
                    ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time', 'proc_priority', 'proc_stage')
                    ->limit($agents[$idDayShift]->count * $operatorCount)
                    ->where('proc_time', '<=', $timeNow)
                    ->where('proc_status', 1)
                    ->where('entity', 'order')
                    ->where('proc_campaign', $idDayShift)
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
                $arrayLog[$idDayShift] = [
                    'group'       => $idDayShift,
                    'time'        => date('Y.m.d H:i.s'),
                    'all_agents'  => $agents[$idDayShift]->count,
                    'count_order' => count($result),
                    'success'     => 0,
                    'error'       => 0
                ];
                if ($result) {
                    $data = [];
                    $arrayIds = [];
                    $ordersLog = [];

                    foreach ($result as $kr => $vr) {
                        echo $vr->id . "\n";
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

                    DB::table($this->orders)->whereIn('id', $arrayIds)->update(['proc_status' => 13]);
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                    if ($resultApi) {
                        if ($resultApi->status == 200) {
                            $arrayLog[$idDayShift]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status'        => 2,
//                                'proc_callback_time' => null,
                                'proc_priority'      => 0,
                                'time_status_updated' => Carbon::now(),
                            ];
                            DB::table($this->orders)->whereIn('id', $resultApi->data)
                                ->update($changeStatus);
                            if (!empty($ordersLog)) {
                                DB::table(OrdersLog::tableName())->insert($ordersLog);
                            }
                        }
                    }
                    $arrayLog[$idDayShift]['error'] = $arrayLog[$idDayShift]['count_order'] - $arrayLog[$idDayShift]['success'];
                }
            }
        } else {//nightShift
            echo "nightShift\n";

            DB::table($this->orders)
                ->where(function ($q) use ($idDayShift, $idDayShiftSamsung) {
                    $q->where('proc_campaign', $idDayShift)
                        ->orWhere('proc_campaign', $idDayShift);
                })
                ->where(function ($query) use ($timeEnd, $timeStart) {
                    $query->where('time_created', '>', $timeEnd)
                        ->orWhere('time_created', '<', $timeStart);
                })
                ->where('proc_status', 1)
                ->where('entity', 'order')
                ->where('proc_stage', '<', 1)
                ->update(['proc_campaign' => $idNightShift]);

            if (isset($agents[$idNightShift])) {
                $ordersFromElastix = $this->apiElastixProcessing2('getCallsNoProcessing');
                if ($ordersFromElastix->status != 200) {
                    echo "Not got orders from elastix\n";
                    return false;
                }

                $countOrdersInCampaigns = collect($ordersFromElastix->data)->where('id_campaign', $idNightShift)
                    ->first();

                $countOrders = 3;          //3 - quantity orders to one operator
                $ordersInCampaign = $countOrdersInCampaigns ? $countOrdersInCampaigns->count : 0;
                $ordersOperator = $ordersInCampaign / $agents[$idNightShift]->count;
                if ($ordersOperator >= $countOrders) {
                    echo("Operators are too busy in group " . $idNightShift . "!\n");
                    echo("Quantity order " . $ordersInCampaign . "\n");
                    return false;
                }
                $limit = $countOrders * $agents[$idNightShift]->count - $ordersInCampaign;
                $orders = DB::table($this->orders)
                    ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time')
                    ->whereBetween('proc_time', [$timeNow->subMinutes(15)->toDateTimeString(), $timeNow->addMinutes(15)->toDateTimeString()])
                    ->where('proc_status', 1)
                    ->where('entity', 'order')
                    ->where('proc_stage', '<', 1)
                    ->where('proc_campaign', $idNightShift)
                    ->where(function ($query) use ($timeEnd, $timeStart) {
                        $query->where('time_created', '>', $timeEnd->toDateTimeString())
                            ->orWhere('time_created', '<', $timeStart->toDateTimeString());
                    })
                    ->where(function ($query) {
                        $query->where('target_status', 0)
                            ->orWhereNotNull('proc_callback_time');
                    })
                    ->limit((int)$limit)
                    ->orderBy('id', 'desc')
                    ->orderBy('proc_stage')
                    ->get();

                $arrayLog[$idNightShift] = [
                    'group'       => $idNightShift,
                    'all_agents'  => $agents[$idNightShift]->count,
                    'count_order' => count($orders),
                    'success'     => 0,
                    'error'       => 0
                ];

                if ($orders) {
                    $data = [];
                    $orderIds = [];
                    foreach ($orders as $order) {
                        $key = $order->id;
                        $orderIds[] = $key;
                        $data[$key]['id'] = $order->id;
                        $data[$key]['phone'] = $order->phone;
                        $data[$key]['before'] = 0;
                        $data[$key]['company'] = $order->proc_campaign;
                        $data[$key]['weight'] = 3;
                        $data[$key]['entity'] = 'order';
                        if (!$order->proc_stage) {
                            $data[$key]['weight'] = -1;
                        } elseif ($order->proc_stage <= 2) {
                            $data[$key]['weight'] = 0;
                        } elseif ($order->proc_stage >= 3 && $order->proc_stage <= 4) {
                            $data[$key]['weight'] = 1;
                        } elseif ($order->proc_stage = 5) {
                            $data[$key]['weight'] = 2;
                        }
                        if ($order->proc_callback_time && $order->proc_stage) {
                            $data[$key]['weight'] = -2;
                        }
                    }
                    DB::table($this->orders)->whereIn('id', $orderIds)->update(['proc_status' => 13]);
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                    if ($resultApi) {
                        if ($resultApi->status == 200) {
                            $arrayLog[$idNightShift]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status'        => 2,
//                                'proc_callback_time' => 0,
                            ];
                            DB::table($this->orders)->whereIn('id', $resultApi->data)
                                ->update($changeStatus);
                        }
                    }
                    $arrayLog[$idNightShift]['error'] = $arrayLog[$idNightShift]['count_order'] - $arrayLog[$idNightShift]['success'];
                }
            }
        }
        echo json_encode($arrayLog) . "\n";
    }

    public function indonesia()
    {
        $agents = (array)$this->apiElastixProcessing('getCountAgents');
        $idDayShift = 66;
        $idNightShift = 69;
        $timeNow = now();
        $arrayLog = [];
        $timeStart = Carbon::parse('now 02:00:00');
        $timeEnd = Carbon::parse('now 17:00:00');
        if ($timeStart <= $timeNow && $timeNow < $timeEnd) {//dayShift
            echo "dayShift\n";

            if ($timeStart < $timeNow->addMinutes(10)) {
                echo "change campaign\n";
                $this->changeCampaign($idNightShift);
            }

            if (isset($agents[$idDayShift])) {
                $operatorCount = 30;
                $result = DB::table($this->orders)
                    ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time', 'proc_priority', 'proc_stage')
                    ->limit($agents[$idDayShift]->count * $operatorCount)
                    ->where('proc_time', '<=', $timeNow)
                    ->where('proc_status', 1)
                    ->where('entity', 'order')
                    ->where('proc_campaign', $idDayShift)
                    ->where(function ($query) {
                        $query->where('target_status', 0)
                            ->orWhere('proc_callback_time', '>', 0);
                    })
                    ->orderBy('id', 'proc_callback_time')
                    ->orderBy('id', 'desc')
                    ->orderBy('proc_stage')
                    ->get();
                $arrayLog[$idDayShift] = [
                    'group'       => $idDayShift,
                    'time'        => date('Y.m.d H:i.s'),
                    'all_agents'  => $agents[$idDayShift]->count,
                    'count_order' => count($result),
                    'success'     => 0,
                    'error'       => 0
                ];
                if ($result) {
                    $data = [];
                    $arrayIds = [];
                    $ordersLog = [];

                    foreach ($result as $kr => $vr) {
                        echo $vr->id . "\n";
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
                            'date'        => time(),
                            'status_id'   => 2,
                            'status_name' => 'В наборе'
                        ];

                        if ($vr->proc_callback_time && $vr->proc_priority) {
                            $data[$kr]['weight'] = $vr->proc_priority;
                        }
                    }

                    DB::table($this->orders)->whereIn('id', $arrayIds)->update(['proc_status' => 13]);
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                    if ($resultApi) {
                        if ($resultApi->status == 200) {
                            $arrayLog[$idDayShift]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status'        => 2,
//                                'proc_callback_time' => 0,
                                'proc_priority'      => 0,
                                'time_status_updated' => Carbon::now(),
                            ];
                            DB::table($this->orders)->whereIn('id', $resultApi->data)
                                ->update($changeStatus);
                            if (!empty($ordersLog)) {
                                DB::table(OrdersLog::tableName())->insert($ordersLog);
                            }
                        }
                    }
                    $arrayLog[$idDayShift]['error'] = $arrayLog[$idDayShift]['count_order'] - $arrayLog[$idDayShift]['success'];
                }
            }
        } else {//nightShift
            echo "nightShift\n";

            DB::table($this->orders)->where('proc_campaign', $idDayShift)
                ->where(function ($query) use ($timeEnd, $timeStart) {
                    $query->where('time_created', '>', $timeEnd)
                        ->orWhere('time_created', '<', $timeStart);
                })
                ->where('proc_status', 1)
                ->where('entity', 'order')
                ->where('proc_stage', '<', 1)
                ->update(['proc_campaign' => $idNightShift]);

            if (isset($agents[$idNightShift])) {
                $ordersFromElastix = $this->apiElastixProcessing2('getCallsNoProcessing');
                if ($ordersFromElastix->status != 200) {
                    echo "Not got orders from elastix\n";
                    return false;
                }

                $countOrdersInCampaigns = collect($ordersFromElastix->data)->where('id_campaign', $idNightShift)
                    ->first();

                $countOrders = 3;          //3 - quantity orders to one operator
                $ordersInCampaign = $countOrdersInCampaigns ? $countOrdersInCampaigns->count : 0;
                $ordersOperator = $ordersInCampaign / $agents[$idNightShift]->count;
                if ($ordersOperator >= $countOrders) {
                    echo("Operators are too busy in group " . $idNightShift . "!\n");
                    echo("Quantity order " . $ordersInCampaign . "\n");
                    return false;
                }
                $limit = $countOrders * $agents[$idNightShift]->count - $ordersInCampaign;

                $orders = DB::table($this->orders)
                    ->select('id', 'phone', 'proc_campaign', 'proc_stage', 'proc_callback_time', 'proc_time')
                    ->whereBetween('proc_time', [$timeNow->subMinutes(15)->toDateTimeString(), $timeNow->addMinutes(15)->toDateTimeString()])
                    ->where('proc_status', 1)
                    ->where('entity', 'order')
                    ->where('proc_stage', '<', 1)
                    ->where('proc_campaign', $idNightShift)
                    ->where(function ($query) use ($timeEnd, $timeStart) {
                        $query->where('time_created', '>', $timeEnd->toDateTimeString())
                            ->orWhere('time_created', '<', $timeStart->toDateTimeString());
                    })
                    ->where(function ($query) {
                        $query->where('target_status', 0)
                            ->orWhere('proc_callback_time', '>', 0);
                    })
                    ->limit((int)$limit)
                    ->orderBy('id', 'desc')
                    ->orderBy('proc_stage')
                    ->get();

                $arrayLog[$idNightShift] = [
                    'group'       => $idNightShift,
                    'all_agents'  => $agents[$idNightShift]->count,
                    'count_order' => count($orders),
                    'success'     => 0,
                    'error'       => 0
                ];

                if ($orders) {
                    $data = [];
                    $orderIds = [];
                    foreach ($orders as $order) {
                        $key = $order->id;
                        $orderIds[] = $key;
                        $data[$key]['id'] = $order->id;
                        $data[$key]['phone'] = $order->phone;
                        $data[$key]['before'] = 0;
                        $data[$key]['company'] = $order->proc_campaign;
                        $data[$key]['weight'] = 3;
                        $data[$key]['entity'] = 'order';
                        if (!$order->proc_stage) {
                            $data[$key]['weight'] = -1;
                        } elseif ($order->proc_stage <= 2) {
                            $data[$key]['weight'] = 0;
                        } elseif ($order->proc_stage >= 3 && $order->proc_stage <= 4) {
                            $data[$key]['weight'] = 1;
                        } elseif ($order->proc_stage = 5) {
                            $data[$key]['weight'] = 2;
                        }
                        if ($order->proc_callback_time && $order->proc_stage) {
                            $data[$key]['weight'] = -2;
                        }
                    }
                    DB::table($this->orders)->whereIn('id', $orderIds)->update(['proc_status' => 13]);
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                    if ($resultApi) {
                        if ($resultApi->status == 200) {
                            $arrayLog[$idNightShift]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status'        => 2,
//                                'proc_callback_time' => null,
                            ];
                            DB::table($this->orders)->whereIn('id', $resultApi->data)
                                ->update($changeStatus);
                        }
                    }
                    $arrayLog[$idNightShift]['error'] = $arrayLog[$idNightShift]['count_order'] - $arrayLog[$idNightShift]['success'];
                }
            }
        }
        echo json_encode($arrayLog) . "\n";
    }
}