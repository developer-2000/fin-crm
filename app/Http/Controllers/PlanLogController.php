<?php

namespace App\Http\Controllers;

use App\Models\PlanLog;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;

class PlanLogController extends BaseController
{
    public function show(){
        $planLogs = PlanLog::with('plan')->orderBy('created_at', 'desc')->paginate(15);
        foreach ($planLogs as $planLog){
            $planLog['company'] = DB::table('companies')->where('id', $planLog->company_id)->first();
            $planLog['operator'] = DB::table('users')->where('id', $planLog->user_id)->first();
        }

        return view('finance.plan-logs', ['planLogs' => $planLogs] );
    }
}
