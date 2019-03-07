<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class ColdCallList extends BaseModel
{
    public $fillable = ['cold_call_file_id', 'phone_number', 'proc_status', 'add_info', 'order_id'];

    /**
     * Получить файл к листу холодных продаж.
     */
    public function coldCallFile()
    {
        return $this->belongsTo('App\Models\ColdCallFile');
    }

    /**
     * Получить результаты по листам холодных продаж .
     */
    public function coldCallResult()
    {
        return $this->hasOne('App\Models\ColdCallResult');
    }

    /**
     * Получить название страны к листу холодных продаж.
     */
    public function country()
    {
        return $this->hasOne('App\Models\Country');
    }


    /**
     * Получить заказ к листу холодных продаж.
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    public function addColdCallsInElastix()
    {
        $coldCallsIds = DB::table('company_elastix')->select('id')->where('company_id', '!=', NULL)->get();

        foreach ($coldCallsIds as $id) {
            $ids[$id->id] = intval($id->id);
        }

        $phoneCount = 10;
        if (!empty($ids)) {

            $campaignArray['ids'] = $ids;
            $campaignsArray['ids'] = json_encode($ids);
            $agents = $this->apiElastixProcessing('getCountAgents', $campaignsArray);

        }
        $result = [];
        if ($agents) {
            $arrayLog = [];
            $arrayLogOrders = [];
            foreach ($agents as $ag) {

                $campaign = DB::table('company_elastix')->where([['id', $ag->queue], ['company_id', '!=', NULL]])->first();
                $campaign->lists = ColdCallFile::with('ColdCallList')->where('campaign_id', $campaign->id)->get();

                if ($campaign->cron_status == 0) {
                    continue;
                }
                $data = [];
                $arrayIds = [];
                $countFreeOperator = $ag->count;

                if (!empty($campaign->lists)) {

                    foreach ($campaign->lists as $key => $list) {

                        if ($list->status == 'active') {
                            $callsInPBX = 0;
                            $ordersFromElastix = $this->apiElastixProcessing2('getCallsNoProcessing', ['entity' => 'cold_call']);
                            if (!empty($ordersFromElastix->data[0])) {
                                $callsInPBX = $ordersFromElastix->data[0]->count;
                            }
                            if ($callsInPBX < $phoneCount) {
                                $phoneCount = $phoneCount - $callsInPBX;
                            }
                            $quantityListRowToProcess = $countFreeOperator * $phoneCount;
                            // $result = $list->ColdCallList->where('proc_status', 1)->take($quantityListRowToProcess);
                            $result = ColdCallList::where([['cold_call_file_id', $list->id],['proc_status', 1], ['order_id', 0]])->take($quantityListRowToProcess)->get();
                            $arrayLog[$ag->queue] = [
                                'group' => $ag->queue,
                                'time' => date('Y.m.d H:i.s'),
                                'all_agents' => $ag->count,
                                'count_order' => count($result),
                                'success' => '',
                                'error' => ''
                            ];

                            if (count($result)) {
                                foreach ($result as $key => $call) {
                                    echo $call->id . "\n";
                                    $data[$key]['id'] = $call->id;
                                    $data[$key]['phone'] = "11".json_decode($call->phone_number)[0];
                                    $data[$key]['before'] = 0;
                                    $data[$key]['company'] = $campaign->id;
                                    $data[$key]['weight'] = 1;
                                    $data[$key]['entity'] = 'cold_call';
                                }
                            }
                            $listRowNotProcessed = DB::table('cold_call_lists')
                                ->where(function ($query) use ($list) {
                                    $query->where('cold_call_file_id', $list->id)
                                        ->whereIn('proc_status', [1,2]);
                                })->get();

                            if (!count($listRowNotProcessed)) {
                                ColdCallFile::where('id', $list->id)->update(['status' => 'finished']);
                            }

                        }
                    }
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $data]);
                    if ($resultApi) {

                        if ($resultApi->status == 200) {
                            $arrayLog[$ag->queue]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status' => 2,
                            ];
                            DB::table('cold_call_lists')->whereIn('id', $resultApi->data)
                                ->update($changeStatus);
                        }
                    }

                    if(!empty( $arrayLog[$ag->queue])){
                        $arrayLog[$ag->queue]['error'] = intval($arrayLog[$ag->queue]['count_order']) - intval($arrayLog[$ag->queue]['success']);

                        echo json_encode($arrayLog) . "\n";
                    }

                }

                $resultOrders = Order::with('ColdCallList')->select('orders.id', 'orders.phone', 'orders.proc_campaign',
                    'orders.proc_callback_time', 'orders.proc_time', 'orders.proc_priority', 'orders.proc_stage')
                    ->where('orders.proc_time', '<=', now())
                    ->where('orders.proc_status',1)
                    ->where('orders.entity', 'cold_call')
                    ->where('orders.proc_campaign', $ag->queue)
                    ->where(function ($query) {
                        $query->where('orders.target_status', 0)
                            ->orWhere('orders.proc_callback_time', '>', 0);
                    })
                    ->orderBy('orders.id', 'proc_time')
                    ->orderBy('orders.id', 'desc')
                    ->orderBy('orders.proc_stage')
                    ->get();

                $arrayLogOrders[$ag->queue] = [
                    'group' => $ag->queue,
                    'type' => 'call_back',
                    'time' => date('Y.m.d H:i.s'),
                    'all_agents' => $ag->count,
                    'count_order' => count($result),
                    'success' => '',
                    'error' => ''
                ];

                if (!empty($resultOrders)) {
                    $dataOrders = [];
                    foreach ($resultOrders as $key => $call) {
                        echo $call->id . "\n";
                        $arrayIds[] = $call->id;
                        $dataOrders[$key]['id'] = $call->ColdCallList->id;
                        $dataOrders[$key]['phone'] = "11".$call->phone;
                        $dataOrders[$key]['before'] = 0;
                        $dataOrders[$key]['company'] = $call->proc_campaign;
                        $dataOrders[$key]['weight'] = -1;
                        if ($call->proc_callback_time && $call->proc_priority) {
                            $data[$key]['weight'] = 1;
                        }
                        $dataOrders[$key]['entity'] = 'cold_call';

                    }
                    DB::table('orders')->whereIn('id', $arrayIds)->update(['proc_status' => 13]);
                    $resultApi = $this->apiElastixProcessing2('addCalls', false, ['calls' => $dataOrders]);

                    if ($resultApi) {
                        if ($resultApi->status == 200) {
                            $arrayLogOrders[$ag->queue]['success'] = count($resultApi->data);
                            $changeStatus = [
                                'proc_status' => 2,
                                'proc_callback_time' => null,
                                //  'proc_priority' => 0,
                            ];
                            DB::table('orders as o')
                                ->leftJoin('cold_call_lists as c','c.order_id', '=','o.id')
                                ->whereIn('c.id', $resultApi->data)
                                ->update($changeStatus);
                        }
                    }
                    if(!empty( $arrayLogOrders[$ag->queue])) {
                        $arrayLogOrders[$ag->queue]['error'] = intval($arrayLogOrders[$ag->queue]['count_order']) - intval($arrayLogOrders[$ag->queue]['success']);
                        echo json_encode($arrayLogOrders) . "\n";
                    }
                }

            }
        }
    }

    public static function sendOrderToBs7()
    {
        $orders = Order::where([['entity', 'cold_call'],
            ['target_status', '=', 1],
            ['partner_oid', 0],
            ['time_changed', '<', now()->subMinutes(5)]])
            ->get();


        foreach ($orders as $key => $order) {
            $productsArray = [];
            if (!empty($order->offer_id)) {
                $offer_name = Offer::find($order->offer_id)->name;
                $offer = $order->offer_id . ':' . $offer_name;
            }
            $campaign = Campaign::with('company')->where('id', $order->proc_campaign)->first();
            $company = $campaign->company->name;
            $comment = (new Comment)->getLastComment($order->id);
            $orderProducts = (new OrderProduct)->getProductsByOrderId($order->id, $order->subproject_id);

            foreach ($orderProducts as $item) {
                $productsArray[] = array(
                    'price' => $item->price,
                    'name' => $item->title
                );
            }
            /*get address from targetfinals*/
            $targets = (new TargetsFinal())->getTargetFinal($order->id, $order->target_id, new Targets(), new NP());

            if ($targets) {
                if ($order->target_status == 1) {
                    foreach ($targets as $t) {
                        $name = explode('__', $t->name);
                        $target[$name[0]] = $t->text;
                        $source[$name[0]] = $t->value;
                    }
                } elseif ($order->target_status == 2) {
                    $targets['comment'] = $targets;
                } elseif ($order->target_status == 3) {
                    $status_ext['status_ext'] = $targets[0]->value;
                }
            }

            if ($campaign->company->id == 9) {
                $_hash = 'NUBSjFdWMHwjzjZT';
                $user_sm = 86;
                $user_cc = 43328;
            }
            if ($campaign->company->id == 11) {
                $_hash = '6dQ5MUizmsrlOcv1';
                $user_sm = 86;
                $user_cc = 350130;
            }

            $data[$key] = array(
                '_hash' => $_hash,
                'status' => 25, // status [moderation]
                'user_sm' => $user_sm,
                'user_vm' => '',
                'user_cc' => $user_cc,
                'order_id' => $order->id,
                'country' => $order->geo,
                'host' => '0.0.0.0',
                'name_first' => $order->name_first,
                'name_last' => $order->name_last,
                'name_middle' => $order->name_middle,
                'phone' => $order->phone,
                'comment' => !empty($comment->text) ? $comment->text : '',
                'offer_id' => !empty($order->offer_id) ? $order->offer_id : '',
                'offer_name' => !empty($offer_name) ? $offer_name : '',
                'advertiser_id' => '',
                'valuation' => $order->price_total,
                'products' => $productsArray,
                'input_data' => !empty($target) ? json_encode($target) : '',
                'tag_source' => 'crm_hp',
                'tag_medium' => '',
                'tag_campaign' => $company,
                'tag_term' => '',
                'tag_content' => $offer,
                'shipping' => !empty($source) ? $source : '',
                'status_ext' => !empty($status_ext) ? $status_ext : '',
            );
        }


        if (!empty($data)) {
            $response = '';
            foreach ($data as $orderConfirmed) {
                $url = "http://bs7.badvps.com/api/set_order1";
                if ($curl = curl_init()) {
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($curl, CURLOPT_POST, TRUE);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($orderConfirmed));
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                    $response = curl_exec($curl);

                    curl_close($curl);
                    if ($response !== false) {

                        $responseArray = json_decode($response, TRUE);
                        if (!$responseArray) {
                            $lastError = jsonErrors(json_last_error());
                            //  pa($lastError, 1);

                            //   return FALSE;
                        }

                        $sent = ($responseArray['code'] == '200' && $responseArray['status'] == 'OK');

                        if ($sent === FALSE) {


                            //     return FALSE;
                        }

                        #################
                        // ok
                        // $responseArray['order_id']
                        #################

                        if (!empty($responseArray['order_id'])) {

                            Order::where([['entity', 'cold_call'], ['id', $orderConfirmed['order_id']]])->update(['partner_oid' => $responseArray['order_id']]);
                        }

                        // return TRUE;
                    } else {
                        $curlError = curl_error($curl);
                        //  pa($curlError, 1);

                        //  return FALSE;
                    }
                }
                echo "---------Send orders to bs7--------\n";
                echo date('H:i:s d/m/y', time()) . "\n";
                echo json_encode($response) . "\n";
            }
        }
    }

    /**
     * Получаем все позиции листа хп
     * @param array $filter Фильтр позиции в листе
     * @return array
     */
    function getListRows($id, $filter)
    {

        $result = DB::table('cold_call_lists as ccl')->select(
            'ccl.id',
            'ccl.add_info', 'ccl.phone_number')
            ->where('cold_call_file_id', $id);

        if ($filter['id']) {
            $result = $result->where('id', $filter['id'])
                ->where('id', '>', 0);
        }

        if ($filter['status']) {
            $status = explode(',', $filter['status']);

            $result = $result
                ->select('ccl.id',
                    'ccl.add_info', 'ccl.phone_number', 'cpl.status', 'cpl.entity')
                ->leftjoin('call_progress_log as cpl', 'cpl.order_id', '=', 'ccl.id')
                ->leftjoin('call_progress_log as cpl1',
                    function ($q) {
                        $q->on('cpl.order_id', '=', 'cpl1.order_id')
                            ->on('cpl.id', '<', 'cpl1.id')
                            ->whereNULL('cpl1.id');
                    })
                ->whereIn('cpl.status', $status)
                ->where('cpl.entity', 'cold_call');
        }

        if ($filter['order_id']) {
            $projectOID = $filter['order_id'];
            $result = $result->whereHas('order', function ($query) use ($projectOID) {
                $query->where('partner_oid', $projectOID);
            });
        }

        if ($filter['phone_number']) {
            $phone_numbers = explode(',', $filter['phone_number']);
            foreach ($phone_numbers as $key => $element) {
                if ($key == 0) {
                    $result->where('phone_number', 'like', '%' . $element . '%');
                }
                $result->orWhere('phone_number', 'like', '%' . $element . '%');
            }
        }
        $result = $result
            ->orderBy('ccl.id', 'desc')
            ->paginate(100);
        return $result;
    }
    public  function deleteCallsFromPBX($coldCallListsIds){

        $listsToDelete = implode(',', $coldCallListsIds);
        $res = $this->apiElastixProcessing2('deleteCallsByIds', [
            'listsToDelete' => $listsToDelete,
            'entity'    => 'cold_call'
        ]);
		
		// pa($res,1);
		
        if (!empty($res) && $res->status == 200) {
			
            DB::table('cold_call_lists')->whereIn('id', $coldCallListsIds)
                ->update(['proc_status' => 1]);
            return true;
        }
		
		return false;
    }
}
