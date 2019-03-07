<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrdOrder;
use App\Models\Product;
use App\Models\ProductProject;
use App\Models\Project;
use App\Models\Projects_new;
use App\Models\StorageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends BaseController
{
    public function test(Request $request) {

        $products = Product::where('sub_project_id', 15)
            ->select('id', 'title')
            ->where('title', 'like', '%' . 'por' . '%')
            ->orderBy('id', 'asc')
            ->get()
            ->keyBy('id');


//        ->where('title', 'like', '%' . $word . '%')
//            ->orderBy('id', 'asc')
//            ->select('id, title');
//        ->orderBy('id', 'asc')->get()->keyBy('id');

        $aaa = OrdOrder::all()->count();

        dd(json_encode($aaa));

//    return json_encode($query);





    }


}
