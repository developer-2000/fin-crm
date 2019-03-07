<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\OffersScript;
use App\Models\Script;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use App\Models\ScriptDetail;
use App\Models\Offer;

class ScriptController extends BaseController
{
    public function index()
    {
        if (!auth()->user()->company_id) {
            $scripts = Script::paginate(50);
        } else {
           // $scripts = Script::where('company_id', auth()->user()->company_id)->paginate(50);
            $scripts = Script::paginate(50);
        }
        if(auth()->user()->company_id == 16){
            $scripts = Script::whereHas('offerScript', function ($q){
                $q->whereIn('offer_id', [3303,3345,3408,3497,3703,3713,3726,3727,3729,3730,3732,3735,3737,3739,3740,3742,3744,3745,3746,3752,3756,3758,3760,3761,3767,3768,3771,3772,3773,3774,3786,3790,3791,3792,3802,3807,3811,3812,3814,3828,3835,3839,3841,3842,3844,3845,3846,3849,3852,3853,3854,3856,3861,3867,3868,3872,3873,3874,3875,3876,3877,3881,3882,3883,3885,3886,3889]);
            })->paginate(50);
        }

        foreach ($scripts as $script) {
            $script->scriptOffers = OffersScript::where('script_id', $script->id)->pluck('offer_id');
        }
        return view('scripts.index', ['scripts' => $scripts]);
    }

    public function create()
    {
        return view('scripts.script-add', ['categories' => CategoryRepository::scriptsCategories()]);
    }

    public function edit($scriptId)
    {
        $script = Script::with('ScriptDetails')->where('id', $scriptId)->first();
        $scriptDetailsCollections = collect(ScriptDetail::with('category')->where('script_id', $scriptId)
            ->orderBy('position')->get())->groupBy('category_id');

        $offersIds = OffersScript::where('script_id', $scriptId)->pluck('offer_id')->toArray();
        $offers = Offer::whereIn('id', $offersIds)->with('project')->get()->toArray();

        $offersJson = array_map(function ($element) {
            return $elements = ['id'   => "" . $element['id'] . "",
                                'text' => isset($element['project']['alias']) ? $element['project']['alias'] .'::'
                                    .$element['name'] : $element['name']];

        }, $offers);
        $offersJson = json_encode($offersJson, JSON_UNESCAPED_UNICODE);

        return view('scripts.edit', ['script' => $script, 'scriptDetailsCollections' => $scriptDetailsCollections,
                                     'offers' => Offer::all(), 'offersJson' => $offersJson]);
    }

    public function delete($scriptId, Request $request)
    {
        if ($request->isMethod('POST')) {
            $script = Script::find($scriptId);
            $script->offerScript()->delete();
            $script->scriptDetails()->delete();
            $script->delete();
            $result['success'] = true;
            return response()->json($result);
        }
    }

    public function deleteBlock($blockId, Request $request)
    {
        if ($request->isMethod('POST')) {
            $scriptDetail = ScriptDetail::find($blockId);
            $scriptDetail->delete();
            $result['success'] = true;
            return response()->json(['success' => true, 'pk' => $blockId]);
        }
    }

    public function blockCreate($scriptId)
    {
        $categories = CategoryRepository::scriptsCategories();
        $script = ['scriptId' => $scriptId];
        return view('scripts.block-add', $script, ['categories' => $categories, 'countries' => Country::all()]);
    }

    public function editBlock($scriptId, $scriptDetailId)
    {
        $scriptDetail = ScriptDetail::with('script')->where('id', $scriptDetailId)->first();
        return view('scripts.block-edit', ['scriptDetail' => $scriptDetail,
                                           'categories'   => CategoryRepository::scriptsCategories(), 'countries' => Country::all()]);
    }

    public function addScriptBlockAjax($scriptId, Request $request)
    {
        if ($request->method('post')) {
            $this->validate($request, [
                'text'  => 'required|min:4',
                'block' => 'required|min:4',
            ]);

            $scriptDetail = ScriptDetail::create(['script_id' => $scriptId, 'category_id' => intval($request->input('category')),
                                                  'block'     => $request->input('block'), 'text' => $request->input('text'), 'geo' => $request->input('geo'),
                                                  'position'  => $request->input('position'), 'status' => 'inactive',
                                                  'key'       => !empty($request->input('key')) ? $request->input('key') : NULL
            ]);
            if ($scriptDetail) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
    }

    /*srcipts page*/
    public function scriptsByOffer($id)
    {
        $scriptsIds = OffersScript::where('offer_id', $id)->pluck('script_id')->toArray();
        $scripts = Script::whereIn('id', $scriptsIds)->paginate(20);
        foreach ($scripts as $script) {
            $script->scriptOffers = OffersScript::where('script_id', $script->id)->pluck('offer_id');
        }

        return view('scripts.scripts-by-offer', ['scripts' => $scripts]);
    }

    /*изменение статуса скрипта*/
    public function changeStatus($id, $status)
    {
        $script = Script::where('id', $id)->first();
        $offerId = OffersScript::where('script_id', $id)->pluck('offer_id')->first();
        $offerScript = OffersScript::where('offer_id', $offerId)->pluck('script_id')->toArray();

        $activeScript = Script::where(function ($query) use ($offerScript) {
            $query->whereIn('id', $offerScript);
        })
            ->where('status', 'active')->first();

        if ($activeScript && $status == 'active') {
            return response()->json(['isActiveScript' => true]);
        } elseif ($activeScript && $status == 'inactive') {
            $script->status = $status;
            $script->save();
            return response()->json(['success' => true]);
        } elseif (!$activeScript) {
            $script->status = $status;
            $script->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function show($id)
    {

        $script = Script::
        with(['scriptDetails' => function ($query) {
            $query->orderBy('position', 'ASC');
        }])
            ->where([['id', $id]])
            ->first();



        return view('scripts.show', ['script' => $script]);
    }

    public function updateBlockPosition(Request $request)
    {
        if ($request->isMethod('post')) {
            $positions = [];
            $data = json_decode($request->input('data'), true);

            foreach ($data as $key => $row) {
                $position = $key + 1;
                ScriptDetail::where('id', $row['id'])->update(['position' => $position]);
                $positions[] = ['id' => $row['id']];
            }
            return response()->json(['positions' => $positions]);
        }
    }
}
