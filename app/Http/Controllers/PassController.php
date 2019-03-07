<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Pass;
use App\Models\ProcStatus;
use Illuminate\Http\Request;

class PassController extends BaseController
{
    public function passReversal(Request $request)
    {
        $pass = Pass::with('orders')->findOrFail($request->pk);
        if (!empty($request->order_id)) {
            $orders = Order::where('id', $request->order_id)->get();
        } else {
            $orders = $pass->orders;
        }

        $procStatus = ProcStatus::where('action', 'reversal')->first();
        if ($procStatus) {
            $request['action'] = $procStatus->action;
            $request['status'] = $procStatus->id;
            $request['pass_type'] = $pass->type;


            if ($orders->count()) {
                $request['orders'] = $orders;
            }

            (new ActionController)->runActionAjax($request);
        }

        return response()->json(['success' => true]);
    }
}
