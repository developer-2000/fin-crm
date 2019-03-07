<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineController extends BaseController
{
    function getOnlineUser()
    {
        if (auth()->user()->id) {
            $maxTimeOffline = 180;
            $data = DB::table('online')->where('user_id', auth()->user()->id)
                                       ->orderBy('date_end', 'DESC')
                                       ->limit(1)
                                       ->first();
            if (!$data || time() - Carbon::parse($data->date_end)->timestamp >= $maxTimeOffline) {
                DB::table('online')->insert([
                    'user_id'             => auth()->user()->id,
                    'date_start'          => now(),
                    'date_end'            => now(),
                ]);
            } else {
                DB::table('online')->where('id', $data->id)
                                   ->update([
                    'date_end'          => now(),
                ]);
            }
        }
    }
}
