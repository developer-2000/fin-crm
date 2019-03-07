<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;
use \Log;

class CallProcessing extends BaseModel
{
    protected $table = 'call_processing';

    public function updateCountCalls()
    {
        $companies = collect(DB::table('company_elastix')->select('id')->get())->keyBy('id');
        $orders = DB::table('orders')->select(DB::raw('COUNT(id) AS count'), 'proc_campaign', 'proc_status')->whereIn('proc_status', [1, 2, 13])->groupBy('proc_campaign', 'proc_status')->get();
        $elastix = $this->apiElastixProcessing2('getCallsNoProcessing');
        $data = [];
        if ($companies) {
            foreach ($companies as $c) {
                $data[$c->id] = [
                    'campaign'      => $c->id,
                    'proc_status_1' => 0,
                    'proc_status_2' => 0,
                    'in_elastix'    => 0,
                    'proc_status_13'=> 0,
                    'all_agent'     => 0,
                    'online'        => 0
                ];
            }
        }
        if ($orders) {
            foreach ($orders as $order) {
                if (isset($data[$order->proc_campaign])) {
                    if ($order->proc_status == 1) {
                        $data[$order->proc_campaign]['proc_status_1'] = $order->count;
                    }
                    if ($order->proc_status == 2) {
                        $data[$order->proc_campaign]['proc_status_2'] = $order->count;
                    }
                    if ($order->proc_status == 13) {
                        $data[$order->proc_campaign]['proc_status_13'] = $order->count;
                    }
                }
            }
        }
        $queues = $this->apiElastixProcessing('getQueuesDetails');
        $agents = $this->apiElastixProcessing('getCountAgents');
        $agents = json_encode($agents);
        $agents = json_decode($agents, true);
        $group = [];
        if ($queues) {
            foreach ($queues as $q) {
                if (!isset($group[$q[0]])) {
                    $group[$q[0]]['count'] = 0;
                    $group[$q[0]]['online'] = 0;
                    $group[$q[0]]['company'] = $q[0];
                    if (isset($agents[$q[0]])) {
                        $group[$q[0]]['online'] = $agents[$q[0]]['count'];
                    }
                }
                $group[$q[0]]['count'] ++;
            }
        }
        foreach ($group as $gr) {
            if (isset($data[$gr['company']])) {
                $data[$gr['company']]['all_agent'] = $gr['count'];
                $data[$gr['company']]['online'] = $gr['online'];
            }
        }
        if ($elastix->status == 200) {
            foreach ($elastix->data as $el) {
                if (isset($data[$el->id_campaign])) {
                    $data[$el->id_campaign]['in_elastix'] = $el->count;
                }
            }
        }
        return $data;
    }
}
