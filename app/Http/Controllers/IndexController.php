<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Projects_new;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class IndexController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Главаня страница
     */
    function index()
    {
//        Artisan::call('cache:clear');
//        Artisan::call('config:clear');
//        Artisan::call('view:clear');

//        if (auth()->user()->role_id != 17) {
//            return view('errors.technical_work');
//        }



        //todo 1 - role operator
        if (auth()->user()->roles->where('id', 1)->isNotEmpty()) {
            return redirect()->route('user', auth()->user()->id);
        } else {
            if (auth()->user()->can('page_orders')) {
                return redirect()->route('orders');
            } elseif (auth()->user()->can('page_requests')) {
                return redirect()->route('requests');
            } else {
                return redirect()->route('user', auth()->user()->id);
            }
        }
    }
}
