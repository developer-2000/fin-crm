<?php

namespace App\Http\Controllers;

use App\Models\CallProgressLog;
use App\Models\Country;
use App\Models\Order;
use App\Models\Partner;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallProgressController extends BaseController
{
    public function getCallByName(Request $request, CallProgressLog $callProgressLog)
    {
        if ($request->isMethod('get')) {
            return $callProgressLog->getCallByName($request->get('fileName'));
        }
        abort(404);
    }

    public function callsDetailing()
    {
        $userCalls = DB::table('call_progress_log')->orderBy('id', 'desc')->paginate(100);
        return view('reports.calls-detailing', ['userCalls' => $userCalls]);
    }

    public function getRecord(Request $request, CallProgressLog $callProgressLog)
    {
        $partner = Partner::where('key', $request->key)->first();

        if ($request->isMethod('get') && $request->fileName && $partner) {
            return $callProgressLog->getCallByName($request->fileName);
        }
        abort(404);
    }

}
