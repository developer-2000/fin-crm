<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends BaseController
{
    public function index()
    {
        return view('partners.index', [
            'partners'  => Partner::all()
        ]);
    }

    public function partnerCreateAjax(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required|max:255|min:2',
            'key'   => 'required|max:255|min:30|unique:partners'
        ]);

        $partner = new Partner();
        $partner->name = $request->name;
        $partner->key = $request->key;
        $result = $partner->save();

        return response()->json([
            'success'   => $result,
            'html'      => view('partners.table', [
                'partners'  => Partner::all(),
            ])->render()
        ]);

    }

    public function partnerEditAjax(Request $request)
    {
        $this->validate($request, [
            'pk'    => 'required|int|exists:partners,id',
            'value' => 'required|max:255|min:2'
        ]);

        $partner = Partner::find($request->pk);
        $partner->name = $request->value;
        $result = $partner->save();

        return response()->json([
            'success' => $result
        ]);
    }

    public function generateKey()
    {
        $key = md5(microtime().rand());

        if (Partner::where('key', $key)->first()) {
            $this->generateKey();
        }

        return response()->json([
            'key'   => $key
        ]);
    }
}
