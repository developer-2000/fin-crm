<?php

namespace App\Console\Commands;

use App\Models\ColdCallList;
use App\Models\TargetValue;
use Illuminate\Console\Command;
use App\Services\PhoneCorrection;
use Illuminate\Support\Facades\DB;

class UpdateTargetValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_target_values';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update target values';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //   try {
        $values = TargetValue::where('target_id', 17)->whereIn('order_id', [1704161,1704651,1705743,1705914,1693070,1694173,1697397,1698730,1705283,1705303,1706064,1706125,1706172,1706230,1700819,1700828,1701348,1703674,1704923,1705025,1704429])->get();
        if ($values) {
            foreach ($values as $row) {

                $val = json_decode($row->values, true);

                //clear track
                if (isset($val['track']) && $val['track']['field_value'] != NULL
                    && $val['track']['field_value'] != '') {
                    $val['track']['field_value'] = 0;
                    $row->values = json_encode($val);
                    $row->track = 0;
                    $row->save();
                    echo $row->order_id . "\n";
                }

//                if (isset($val['cost_actual']) && $val['cost_actual']['field_value'] != NULL
//                    && $val['cost_actual']['field_value'] != '' && $row->cost_actual == 0) {
//
//                    $row->cost_actual = $val['cost_actual']['field_value'];
//                    $row->save();
//                    echo $row->order_id . "\n";
//                }

//                if (isset($val['cost']) && $val['cost']['field_value'] != NULL
//                    && $val['cost']['field_value'] != '') {
//
//                    $row->cost = $val['cost']['field_value'];
//                    $row->save();
//                }

//                if ( isset($val['track']) && $val['track']['field_value'] == '' && $row->track) {
//                    echo $row->order_id."track->json \n";
//                    $val['track']['field_value'] = $row->track;
//                    $row->values = json_encode($val);
//                    $row->save();
//                }
//
//                if ( isset($val['track']) && $val['track']['field_value'] && !$row->track) {
//                    echo $row->order_id."json->track\n";
//                    $row->track = $val['track']['field_value'];
//                    $row->save();
//                }
//
//                if ( isset($val['track']) && $val['track']['field_value'] && $row->track = '') {
//                    echo $row->order_id."json->track\n";
//                    $row->track = $val['track']['field_value'];
//                    $row->save();
//                }
//                if ( !isset($val['track'])) {
//                    echo $row->order_id."\n";
//                    $val['track']['field_title'] = 'Track';
//                    $val['track']['field_name'] = 'track';
//                    $val['track']['field_type'] = 'text';
//                    $val['track']['field_value'] = '';
//                    $val['track']['field_relation_name'] = '';
//                    $val['track']['field_relation_value'] = '';
//                    $val['track']['field_required'] = '';
//                    $val['track']['field_show_result'] = 'on';
//                    $val['track']['options'] = [];
//                    $row->values = json_encode($val);
//                    $row->save();
//                }
//                if (in_array('track',$val) == false) {
//                    $val['track']['field_title'] = 'track';
//                    $val['track']['field_name'] = 'track';
//                    $val['track']['field_type'] = 'text';
//                    $val['track']['field_value'] = $row->track;
//                    $val['track']['field_relation_name'] = '';
//                    $val['track']['field_relation_value'] = '';
//                    $val['track']['field_required'] = '';
//                    $val['track']['field_show_result'] = 'on';
//                    $val['track']['options'] = [];
//                    $row->values = json_encode($val);
//                    $row->save();
//                }
            }
        }
    }
}
