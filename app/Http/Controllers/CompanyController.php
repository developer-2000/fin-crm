<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Rank;
use App\Models\Transaction;

class CompanyController extends BaseController
{
    public function index(Transaction $transactionModel)
    {
        return view('companies.index', [
            'companies' => (new Company)->getCompanies(),
            'balance'   => collect($transactionModel->getBalanceInCompanies())->keyBy('company_id')->toArray(),
        ]);
    }

    public function registration(Request $request)
    {
        if ($request->isMethod('post')) {

            $this->validateDataCompany($request, true);

            return response()->json(
                (new Company)->addNewClientCompany($request->all())
            );
        }
        return view('companies.registration', [
            'ranks' => Rank::where('role_id', 1)->get()//role_id = 1 - оператор
        ]);
    }

    /**
     * поиск всех стран по селекту
     */
    public static function findByWord(Request $request, Company $companyModel)
    {

        $term = trim($request->input('query'));
        $rolesIds = $request->rolesIds;

        $companies = $companyModel->searchCompanyByWord($term, $rolesIds);
        $formatted_companies = [];

        foreach ($companies as $company) {
            $formatted_companies[] = ['id' => $company->id, 'text' => $company->name];
        }

        return \Response::json($formatted_companies);

    }

    public function edit(Request $request, $id, Company $clientCompanyModel)
    {
        if ($request->isMethod('post')) {

            $company = Company::find($id);

            if ($company) {
                $this->validateDataCompany($request);
                $result = $clientCompanyModel->changeCompany($id, $request->all());
                $result['update'] = true;
                return response()->json($result);
            }

            abort(404);
        }
        return view('companies.edit', [
            'company'   => $clientCompanyModel->getOneCompany($id),
            'ranks' => Rank::where('role_id', 1)->get()//role_id = 1 - оператор
        ]);
    }

    public function getDefaultPrices(Request $request, Company $clientCompanyModel)
    {
        if ($request) {
            return response()->json([
                'prices'    => $clientCompanyModel->getDefaultPrices(),
            ]);
        }
        abort(404);
    }

    public function getDefaultPricesForPlan($company_id)
    {
        $company = Company::where('id','=', $company_id)->first();
        return response()->json(['company' => $company]);
    }

    protected function validateDataCompany(Request $request, $create = false)
    {
        $rules = [
            'name'                      => 'max:255|min:2',
            'billing'                   => 'max:5',
            'type'                      => '',
            'global.in-system'          => 'required_if:type,hour|numeric|min:0',
            'global.in-talk'            => 'required_if:type,hour|numeric|min:0',
            'global.approve'            => 'required_if:type,lead|numeric|min:0',
            'global.up-sell'            => 'required_if:type,lead|numeric|min:0',
            'global.up-sell-2'          => 'required_if:type,lead|numeric|min:0',
            'global.cross-sell'         => 'required_if:type,lead|numeric|min:0',
            'global.cross-sell-2'       => 'required_if:type,lead|numeric|min:0',
            'global.rate'               => 'required_if:type,week,month|numeric|min:0',
        ];

        if ($create) {
            $rules['name'] .= '|required';
            $rules['type'] .= 'required';
        }

        if ($request->get('billing')) {
            $rules['global.type-billing'] = 'required|in:billing_hour,billing_lead,billing_week,billing_month';
            $rules['global.billing-in-system'] = 'required_if:global.type-billing,billing_hour|numeric|min:0';
            $rules['global.billing-in-talk'] = 'required_if:global.type-billing,billing_hour|numeric|min:0';
            $rules['global.billing-approve'] = 'required_if:global.type-billing,billing_lead|numeric|min:0';
            $rules['global.billing-up-sell'] = 'required_if:global.type-billing,billing_lead|numeric|min:0';
            $rules['global.billing-up-sell-2'] = 'required_if:global.type-billing,billing_lead|numeric|min:0';
            $rules['global.billing-cross-sell'] = 'required_if:global.type-billing,billing_lead|numeric|min:0';
            $rules['global.billing-cross-sell-2'] = 'required_if:global.type-billing,billing_lead|numeric|min:0';
            $rules['global.billing-rate'] = 'required_if:global.type-billing,billing_month,billing_week|numeric|min:0';
        }

        if ($request->get('ranks')) {
            $rules['ranks.*.rank'] = 'required|numeric|min:1';
            $rules['ranks.*.rank-type'] = 'required|in:hour,lead,week,month';
            $rules['ranks.*.in-system'] = 'required_if:ranks.*.rank-type,hour|numeric|min:0';
            $rules['ranks.*.in-talk'] = 'required_if:ranks.*.rank-type,hour|numeric|min:0';
            $rules['ranks.*.approve'] = 'required_if:ranks.*.rank-type,lead|numeric|min:0';
            $rules['ranks.*.up-sell'] = 'required_if:ranks.*.rank-type,lead|numeric|min:0';
            $rules['ranks.*.up-sell-2'] = 'required_if:ranks.*.rank-type,lead|numeric|min:0';
            $rules['ranks.*.cross-sell'] = 'required_if:ranks.*.rank-type,lead|numeric|min:0';
            $rules['ranks.*.cross-sell-2'] = 'required_if:ranks.*.rank-type,lead|numeric|min:0';
            $rules['ranks.*.rate'] = 'required_if:ranks.*.rank-type,month,week|numeric|min:0';
        }

        if ($request->get('ranks_billing')) {
            $rules['ranks_billing.*.rank'] = 'required|numeric|min:1';
            $rules['ranks_billing.*.rank-type'] = 'required|in:hour,lead,week,month';
            $rules['ranks_billing.*.in-system'] = 'required_if:ranks_billing.*.rank-type,hour|numeric|min:0';
            $rules['ranks_billing.*.in-talk'] = 'required_if:ranks_billing.*.rank-type,hour|numeric|min:0';
            $rules['ranks_billing.*.approve'] = 'required_if:ranks_billing.*.rank-type,lead|numeric|min:0';
            $rules['ranks_billing.*.up-sell'] = 'required_if:ranks_billing.*.rank-type,lead|numeric|min:0';
            $rules['ranks_billing.*.up-sell-2'] = 'required_if:ranks_billing.*.rank-type,lead|numeric|min:0';
            $rules['ranks_billing.*.cross-sell'] = 'required_if:ranks_billing.*.rank-type,lead|numeric|min:0';
            $rules['ranks_billing.*.cross-sell-2'] = 'required_if:ranks_billing.*.rank-type,lead|numeric|min:0';
            $rules['ranks_billing.*.rate'] = 'required_if:ranks_billing.*.rank-type,month,week|numeric|min:0';
        }

        $this->validate($request, $rules);
    }
}
