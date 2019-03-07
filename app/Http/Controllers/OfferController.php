<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ScriptDetail;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Country;
use App\Models\Script;
use App\Models\OffersScript;

class OfferController extends BaseController
{
    /**
     * Страница всех офферов
     */
    public function index(Request $request, Product $offersModel, Project $projectModel)
    {
        $page = $request->input('page');
        $requestObject = 'query';
        if ($request->isMethod('post')) {
            $requestObject = 'request';
        }
        $filter = [
            'partner' => $request->$requestObject->get('partner'),
            'name'    => $request->$requestObject->get('name'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('offers') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $result = $offersModel->getAllOffersByFilters($filter, $page);
        foreach ($result['data'] as $item) {
            $offerScript = OffersScript::where('offer_id', $item->id)->pluck('script_id')->toArray();;

            $item->script = Script::where(function ($query) use ($offerScript) {
                return $query
                    ->whereIn('id', $offerScript);
            })->where('status', 'active')->first();
        }

        $result['partners'] = Partner::all();

        return view('offers.index', $result);
    }

    /**
     * Страница одного оффера
     */
    public function edit($id, Product $offersModel, Project $projectModel, Country $countriesModel)
    {
        $result['data'] = $offersModel->getOneOffer($id);
        $result['partners'] = Partner::all();

        $result['products'] = $offersModel->getAllProductsOfferGroupByType($id);
        $result['countries'] = collect($countriesModel->getAllCounties())->keyBy('code');

        return view('offers.edit', $result);
    }

    public function changeOfferInformationAjax(Request $request, $id, Product $offerModel)
    {
        if ($request->isMethod('POST')) {
            return response()->json(
                $offerModel->changeOffers($id, $request->all())
            );
        }
        abort(404);
    }

    /**
     * поиск товара для оферра
     */
    public function searchProductsForOfferAjax(Request $request, Product $offersModel)
    {
        if ($request->isMethod('post')) {
            $data = $offersModel->searchProduct($request->input('offerId'), $request->input('search'));
            $html = view('offers.searchProductsForOfferAjax', [
                'products' => $data,
            ])->render();
            return response()->json(['html' => $html]);
        }
        abort(404);
    }

    /**
     * поиск всех офферов по селекту
     */
    public function findByWord(Request $request, Product $offersModel)
    {
        $term = trim($request->q);

        if (isset($request->partner_id) &&  !$request->partner_id && !$request->allowWithoutPtoject) {
            return \Response::json([]);
        }

        $offers = $offersModel->searchOfferByWord($term, $request->partner_id);
        $formatted_offers = [];

        foreach ($offers as $offer) {
            $formatted_offers[] = ['id' => $offer->id, 'text' => $offer->name];
        }

        return \Response::json($formatted_offers);

    }

    /**
     * добавление товара для офера
     */
    public function addNewProductForOffersAjax(Request $request, Product $offersModel, Country $countriesModel, $id)
    {
        if ($request->isMethod('post')) {
            $result = $offersModel->addProductOffers($request->all(), $id);
            if ($result['success']) {

                $data['products'] = $offersModel->getAllProductsOfferGroupByType($id);
                $data['countries'] = collect($countriesModel->getAllCounties())->keyBy('code');
                $result['html'] = view('offers.products-offer', $data)->render();
            }
            return response()->json($result);
        }
        abort(404);
    }

    /**
     * добавление товара для офера
     */
    public function addNewProductsForOfferAjaxColdCalls(
        Request $request,
        Product $offersModel,
        $id
    ) {
        if ($request->isMethod('post')) {

            $result = $offersModel->addProductsOffersColdCall($request->all(), $id);
            if ($result['success']) {

                $data['products'] = $offersModel->getAllProductsOfferGroupByType($id);

                $result['html'] = view('cold-calls.products-offer', $data)->render();
            }
            return response()->json($result);
        }
        abort(404);
    }

    /**
     * удаление товара из офера
     */
    public function deleteProductFromOfferAjax(Request $request, Product $offersModel)
    {
        if ($request->isMethod('post')) {
            return response()->json([
                'status' => $offersModel->deleteProductFromOffer($request->get('id')),
            ]);
        }
        abort(404);
    }


    /*add script*/
    public function addScriptWithOffer($offerId)
    {
        $offer = Offer::find($offerId);
        $categories = Category::where('entity', 'script');
        $offer = ['id'   => $offer->id,
                  'text' => $offer->name];
        $offer = json_encode($offer, JSON_UNESCAPED_UNICODE);
        return view('scripts.script-add-with-offer', ['offer' => $offer, 'categories' => $categories]);
    }

    public function scriptChangeOffer(Request $request)
    {
        if ($request->method('post')) {

            $offers = explode(',', $request->input('offers'));
            foreach ($offers as $offer) {

                $existScriptOffer = OffersScript::where([['script_id', $request->input('script_id')], ['offer_id', $offer]])->first();
                if (!$existScriptOffer) {
                    $offerScript = new OffersScript();
                    $offerScript->script_id = $request->input('script_id');
                    $offerScript->offer_id = $offer;
                    $offerScript->timestamps = false;
                    if ($offerScript->save()) {
                        return response()->json(['success' => true]);
                    } else {
                        return response()->json(['success' => false]);
                    }
                }
            }
            $existScriptOffers = OffersScript::where('script_id', $request->input('script_id'))->pluck('offer_id')->toArray();
            $diffOffers = array_diff($existScriptOffers, $offers);
            OffersScript::whereIn('offer_id', $diffOffers)->delete();

            if (Script::where('id', $request->input('script_id'))->update(['name' => $request->input('name')])) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
    }

    /*add script*/
    public function addScriptAjax(Request $request)
    {
        if ($request->method('post')) {

            $script = Script::create(['name' => $request->input('name'), 'status' => 'inactive', 'comment' => $request->input('comment'), 'company_id' => !empty(auth()->user()->company_id) ? auth()->user()->company_id : NULL]);
            if (!empty($request->input('offers'))) {
                $offers = explode(',', $request->input('offers'));
                foreach ($offers as $offer) {
                    $offerScript = new OffersScript;
                    $offerScript->offer_id = $offer;
                    $offerScript->script_id = $script->id;
                    $offerScript->timestamps = false;
                    $offerScript->save();
                }
            }
            if ($script) {
                return response()->json(['scriptId' => $script->id]);
            } else {
                return response()->json(['success' => false]);
            }

        }
//        $scripts = Script::where('offer_id', $id)->paginate(20);
        return view('scripts.script-add');
    }

    /*edit script*/
    public function editScriptAjax($scriptId, Request $request)
    {
        if ($request->method('post')) {

            $scriptDetailUpdated = ScriptDetail::where('id', $scriptId)->update(['category_id' => $request->input('category'),
                                                                                 'block'              => $request->input('block'), 'text' => $request->input('text'),
                                                                                 'position'           => $request->input('position'), 'geo' => $request->input('geo'),
                                                                                 'key'                => !empty($request->input('key')) ? $request->input('key') : NULL
            ]);
            if ($scriptDetailUpdated) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
        return view('scripts.script-edit');
    }

    /*изменение статуса позиции в скрипте*/
    public function changeStatus($id, $status)
    {
        $script = ScriptDetail::where('id', $id)->first();
        $script->status = $status;
        $script->save();
        return response()->json($script);
    }
}
