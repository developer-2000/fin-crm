<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BarcodegeneratorController extends Controller
{
    public function barcode(Request $request)
    {
        return response()->view('barcode.barcodegenerator', [
            'order_id' => $request->order_id
        ])->header('Content-type', 'image/png');
    }
}
