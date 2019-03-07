<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Remainder;
use App\Models\StorageTransaction;
use App\Models\StorageContent;
use App\Models\Product;
use App\Models\Moving;
use App\Models\MovingProduct;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StorageController extends BaseController
{
    public function all(Request $request)
    {
        $begin_query = Remainder::searchQuery($request);

        $query = Remainder::searchQuery($request)
            ->searchSelect()
            ->setSearchWhere($request)
            ->setSearchSort($request);
        if (!empty($request->date)) {

            $query = $query->rightJoin(\DB::raw('(select MAX(id) maxid from storage_transactions where storage_transactions.created_at
       <= \'' . date('Y-m-d H:i:s', strtotime($request->date)) . '\'
group by storage_transactions.project_id, storage_transactions.product_id) t'), 'storage_transactions.id', '=', 't.maxid');
        } else {
            $query = $query->rightJoin(\DB::raw('(select MAX(id) maxid from storage_transactions
group by storage_transactions.project_id, storage_transactions.product_id) t'), 'storage_transactions.id', '=', 't.maxid');
        }
        $remainders = $query->paginate(15);

        $sortLinks = (new Remainder())->sortLinks($request);
        $appendPage = (new Remainder)->appendPage($request);

        $projects = $begin_query->select(DB::raw('pj.id as pj_id, pj.name as pj_name'))
            ->groupBy('pj.id')->pluck('pj_name', 'pj_id');
        $storages = $begin_query->select(DB::raw('sp.id as sp_id, sp.name as sp_name'))
            ->groupBy('sp.id')->pluck('sp_name', 'sp_id');

        $users = $begin_query->select(DB::raw('u.id as user_id, concat_ws(\' \', u.name, u.surname) as user_name'))
            ->groupBy('u.id')->pluck('user_name', 'user_id');
        $product_id = $request->get('product_id', 0);
        $product_name = $product_id ? Product::where('id', $product_id)->pluck('title')->first() : '';


        return view('storages.all', compact(
            'remainders', 'sortLinks', 'appendPage', 'projects', 'storages', 'users', 'product_name'
        ));
    }

    public function getProductsList(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }
        $word = $request->post('word', '');
        $products = Remainder::searchQuery($request)
            ->where('p.title', 'like', '%' . $word . '%')
            ->select(DB::raw('p.title as text, p.id as id'))
            ->groupBy('p.id')
            ->orderBy('p.title')
            ->get();
        return $products;
    }
}