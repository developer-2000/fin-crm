<?php

namespace App\Http\Controllers;

use \App\Models\User;
use App\Models\Campaign;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Product;
use App\Models\Source;

class CampaignController extends BaseController
{
    public function index(Campaign $companyElastix, Source $source, Product $offers, Country $countries)
    {
        return view('campaigns.index', [
            'companies' => Campaign::orderBy('position', 'asc')->paginate(50),
            'source'    => $source->getAllSource(),
            'offers'    => $offers->getOffersAll(),
            'countries' => $countries->getAllCounties()
        ]);
    }

    /**
     * страница добавления операторов к группе
     */
    public function campaignsOperators()
    {
        $result['campaigns'] = Campaign::whereNull('company_id')->get();
        $result['operators'] = (new User)->getAllOperatorsInCampaigns();
        return view("campaigns.campaigns-operators", $result);
    }

    public function searchOperatorsForPbxCampaignAjax(Request $request, User $authModel, Campaign $companyElastixModel)
    {
        if ($request->isMethod('post')) {
            $result['operators'] = $authModel->searchOperators($request->get('search'));
            $result['campaigns'] = $companyElastixModel->getAllCompanyElastix();
            $result['all'] = false;
            if ($request->get('search') == '') {
                $result['all'] = true;
            }
            return response()->json([
                'html' => view('campaigns.ajax.searchOperatorAjax', $result)->render()
            ]);
        }
        abort(404);
    }

    public function addUserInClientCompanyAjax(Request $request, User $authModel)
    {
        if ($request->isMethod('post')) {
            return response()->json([
                'status' => $authModel->addUserInCompany($request->all()),
            ]);
        }
        abort(404);
    }

    public function companyElatixAddView(Country $countries, Source $source, Product $offers)
    {
        return view('company_elastix_add.company_elastix_add', [
            'countries' => $countries->getAllCounties(),
            'source'    => $source->getAllSource(),
            'offers'    => $offers->getOffersAll()
        ]);
    }

    public function createAjax(Request $request, Campaign $companyElastix)
    {

        if ($request->isMethod('post')) {
            $data = $companyElastix->addToBase($request->input('data'));
            return response()->json([
                'error'   => $data['error'],
                'message' => $data['message'],
            ]);
        }
        abort(404);
    }

    public function companyElastixPositionUpdate(
        Request $request,
        Campaign $companyElastix,
        Source $source,
        Product $offers,
        Country $countries
    ) {
        if ($request->isMethod('post')) {
            return response()->json([
                'status' => $companyElastix->updatePositionCompany($request->input('data')),
                'view'   => view('x_company_elastix_add.company_elastix_Ajax', [
                    'companies' => Campaign::orderBy('position', 'asc')->paginate(50),
                    'source'    => $source->getAllSource(),
                    'offers'    => $offers->getOffersAll(),
                    'countries' => $countries->getAllCounties(),
                ])->render(),
            ]);
        }
        abort(404);
    }

    public function companyElastixUpdate(
        $id,
        Campaign $companyElastix,
        Country $countries,
        Source $source,
        Product $offers
    ) {
        return view('x_company_elastix_add.company_elastix_update', [
            'company'   => $companyElastix->companyElatixUpdate($id),
            'countries' => $countries->getAllCounties(),
            'source'    => $source->getAllSource(),
            'offers'    => $offers->getOffersAll()
        ]);
    }

    public function companyElastixUpdateAjax(Request $request, $id, Campaign $companyElastix)
    {
        if ($request->isMethod('post')) {
            $data = $companyElastix->companyElastixUpdateAjax($request->input('data'), $id);
            return response()->json([
                'error'   => $data['error'],
                'message' => $data['message'],
            ]);
        }
        abort(404);
    }

    function changeOperatorQueuesElastixAjax(Request $request, User $userModel)
    {
        if ($request->isMethod('post')) {
            return response()->json([
                'status' => $userModel->changeOperatorQueues($request->input('id'), $request->input('queues'))
            ]);
        }
        abort(404);
    }
}
