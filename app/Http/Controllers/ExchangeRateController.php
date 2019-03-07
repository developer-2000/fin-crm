<?php

namespace App\Http\Controllers;

use App\Models\Country;

class ExchangeRateController extends BaseController
{
    public function index()
    {
        return view('exchange-rates.index', ['countries' => Country::paginate(20)]);
    }
}
