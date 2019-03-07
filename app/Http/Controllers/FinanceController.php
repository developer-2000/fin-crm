<?php

namespace App\Http\Controllers;


use \App\Models\User;
use App\Models\Company;
use App\Models\Country;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Transaction;
use Illuminate\Http\Request;

class FinanceController extends BaseController
{
    public function balanceCompanies(Company $clientCompanyModel, Transaction $transactionModel)
    {
        if (auth()->user()->company_id) {
            $data['companies'][] = $clientCompanyModel->getOneCompany(auth()->user()->company_id);
        } else {
            $data['companies'] = Company::all();
        }
        $data['balance'] = collect($transactionModel->getBalanceInCompanies())->keyBy('company_id');
        return view('finance.balance', $data);
    }

    public function transactionsCompanies(
        Request $request,
        Transaction $transactionModel,
        Country $countriesModel
    ) {
        $filter = [
            'date_start'  => $request->get('date_start'),
            'date_end'    => $request->get('date_end'),
            'id'          => $request->get('id'),
            'oid'         => $request->get('oid'),
            'country'     => $request->get('country'),
            'company'     => $request->get('company'),
            'trans_payed' => $request->get('trans_payed'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('finance-transactions-companies') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $result = $transactionModel->getAllTransactionInCompanies($filter);
        $result['country'] = $countriesModel->getAllCounties();
        $result['companies'] = Company::all();

        return view('finance.transaction', $result);
    }

    public function payoutsCompanies(Payout $payoutModel)
    {
        $result = $payoutModel->getAllPayoutsCompanies();
        return view('finance.payouts', $result);
    }

    public function newPayoutCompany(
        Request $request,
        $id,
        Company $clientCompanyModel,
        Payout $payoutModel,
        Transaction $transactionModel
    ) {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $data['entity_id'] = $id;
            $result = $payoutModel->addNewPayoutForCompany($data);
            if ($result['status']) {
                $transactionModel->setPayoutCompanyId($id, $result['status']);
            }
            return response()->json($result);
        }
        return view('finance.new-payout', [
            'transaction' => $transactionModel->getNoPayouts($id),
            'company'     => $clientCompanyModel->getOneCompany($id),
        ]);
    }

    public function getInfoTransactionAjax(Request $request, Transaction $transactionModel, $id)
    {
        if ($request->isMethod('post')) {
            $filter = [
                'date_start' => $request->get('date_start') . ' 00:00:00',
                'date_end'   => $request->get('date_end') . ' 23:59:59',
            ];
            return response()->json([
                'html' => view('finance.infoTransactionForPayoutAjax', [
                    'transaction' => $transactionModel->getNoPayouts($id, $filter),
                ])->render()
            ]);
        }
    }

    public function getInfoTransactionUserAjax(Request $request, Transaction $transactionModel, $id)
    {
        if ($request->isMethod('post')) {
            $filter = [
                'date_start' => $request->get('date_start') . ' 00:00:00',
                'date_end'   => $request->get('date_end') . ' 23:59:59',
            ];
            return response()->json([
                'html' => view('finance.infoTransactionForPayoutAjax', [
                    'transaction' => $transactionModel->getNoPayoutsUser($id, $filter),
                ])->render()
            ]);
        }
    }

    public function balanceUsers(
        Request $request,
        User $authModel,
        Transaction $transactionModel,
        Company $companiesModel
    ) {
        $filter = [
            'company'  => $request->get('company'),
            'operator' => $request->get('operator'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('balance-users') . $this->getFilterUrl($filter), true, 303);
            exit;
        }

        $result = $transactionModel->getbalanceOperators($filter);
        $result['companies'] = Company::all();
        $result['allOperators'] = $authModel->getOperators(auth()->user()->company_id);
        return view('finance.balance-users', $result);
    }

    public function transactionUsers(
        Request $request,
        Transaction $transactionModel,
        Company $companiesModel,
        Country $countriesModel
    ) {
        $filter = [
            'date_start'  => $request->get('date_start'),
            'date_end'    => $request->get('date_end'),
            'id'          => $request->get('id'),
            'oid'         => $request->get('oid'),
            'country'     => $request->get('country'),
            'company'     => $request->get('company'),
            'trans_payed' => $request->get('trans_payed'),
        ];
        if ($request->isMethod('post')) {
            header('Location: ' . route('transaction-users') . $this->getFilterUrl($filter), true, 303);
            exit;
        }
        $result = $transactionModel->getAllTransactionInUsers($filter);
        $result['country'] = $countriesModel->getAllCounties();
        $result['companies'] = Company::all();
        return view('finance.transaction-users', $result);
    }

    public function payoutsUsers(Request $request, Payout $payoutModel)
    {
        $page = $request->input('page');

        $result = $payoutModel->getAllPayoutsUsers($page);
        return view('finance.payouts-users', $result);
    }

    public function newPayoutUser(
        Request $request,
        $id,
        Payout $payoutModel,
        Transaction $transactionModel,
        User $authModel
    ) {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $data['entity_id'] = $id;
            $result = $payoutModel->addNewPayoutForUser($data);
            if ($result['status']) {
                $transactionModel->setPayoutUserId($id, $result['status']);
            }
            return response()->json($result);
        }
        return view('finance.new-payout-user', [
            'transaction' => $transactionModel->getNoPayoutsUser($id),
            'user'        => $authModel->getOneUser($id),
        ]);
    }
}
