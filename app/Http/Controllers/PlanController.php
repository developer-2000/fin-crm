<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Offer;
use App\Models\PlanLog;
use App\Models\PlanRate;
use App\Models\PlanRateOffer;
use App\Models\Result;
use App\Models\Transaction;
use App\Repositories\CountryRepository;
use App\Repositories\OfferRepository;
use App\Repositories\UserRepository;
use App\Repositories\UsersGroupRepository;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlanController extends BaseController
{
    public function index()
    {
        $plans = Plan::with('company')->orderBy('created_at', 'desc')->paginate(15);

        foreach ($plans as $plan) {
            $plan = self::getDataForPlan($plan);
        }

        return view('plans-and-rates.index', ['plans' => $plans]);
    }

    public function create(Request $request)
    {
        $companies = Company::all()->sortBy('name');
        $offers = OfferRepository::offersSortByName();
        $countriesCodes = CountryRepository::countriesSortByName();
        $operators = UserRepository::operatorsSortBySurname();
        $operatorsGroups = UsersGroupRepository::operatorsGroupSorted();

        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
                'type-object' => 'required',
                'action-sum-value' => 'integer|min:1',
                'action-quantity-value' => 'integer|min:1',
            ]);

            if ($validator->fails()) {
                //   dd($validator);
                return view('finance.plan-create', ['companies' => $companies, 'offers' => $offers,
                    'countriesCodes' => $countriesCodes, 'operators' => $operators,
                    'operatorsGroups' => $operatorsGroups])->withErrors($validator);
            }

            Plan::createPlan($request->all());

            return redirect('/plans')->with('status', 'План успешно создан!');
        }

        return view('plans.create', ['companies' => $companies, 'offers' => $offers,
            'countriesCodes' => $countriesCodes, 'operators' => $operators,
            'operatorsGroups' => $operatorsGroups]);
    }

    public static function edit($id, Request $request)
    {
        $companies = Company::all()->sortBy('name');
        $offers = OfferRepository::offersSortByName();
        $countriesCodes = CountryRepository::countriesSortByName();
        $operators = UserRepository::operatorsSortBySurname();
        $operatorsGroups = UsersGroupRepository::operatorsGroupSorted();
        $plan = Plan::where('id', $id)->with('company')->first();

        if ($request->isMethod('post')) {
            $result = Plan::edit($id, $request->all());
            $result['update'] = true;
            return response()->json($result);
        }

        $plan = self::getDataForPlan($plan);

        return view('plans.edit', [
            'plan' => $plan,
            'companies' => $companies, 'offers' => $offers,
            'countriesCodes' => $countriesCodes, 'operators' => $operators,
            'operatorsGroups' => $operatorsGroups]);
    }

    /*изменение статуса плана*/
    public function changeStatus($id, $status)
    {
        $plan = Plan::where('id', $id)->first();
        $plan->status = $status;
        $plan->save();
        return response()->json($plan);
    }

    /*изменение статуса плана*/
    public function getNewPrices($planId)
    {
        $plan = Plan::where('id', $planId)->first();
        $newPrices = json_decode($plan->new_prices, true);
        return response()->json($newPrices);
    }

    /**
     * @param $plan
     */
    public static function getDataForPlan($plan)
    {
        $planCriteria = json_decode($plan->criteria, true);

        $operators = [];
        $operatorsGroups = [];
        $countries = [];
        $offers = [];
        $products = [];
        $operatorsExcept = [];
        $operatorsGroupsExcept = [];
        $countriesExcept = [];
        $offersExcept = [];
        $productsExcept = [];

        if (!empty($planCriteria['operator'])) {
            $operatorsId = explode(",", $planCriteria['operator']);
            foreach ($operatorsId as $operatorId) {
                $operators['data'][] = DB::table('users')->select('id', 'surname', 'name')->where('id', $operatorId)->first();
            }

            $operators['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->surname . "  " . $element->name];

            }, $operators['data']);

            $operators['json'] = json_encode($operators['json'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($planCriteria['country'])) {

            $countriesId = explode(",", $planCriteria['country']);
            foreach ($countriesId as $countryId) {
                $countries['data'][] = Country::where('code', strtolower($countryId))->first();
            }
            $countries['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->code . "",
                    'text' => $element->name];

            }, $countries['data']);
            $countries['json'] = json_encode($countries['json'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($planCriteria['offers'])) {
            $offersId = explode(",", $planCriteria['offers']);
            foreach ($offersId as $offerId) {
                $offers['data'][] = Offer::where('id', $offerId)->first();
            }
            $offers['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->name];

            }, $offers['data']);

            $offers['json'] = json_encode($offers['json'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($planCriteria['products'])) {
            $productsId = explode(",", $planCriteria['products']);

            foreach ($productsId as $productId) {
                $products['data'][] = Product::where('id', $productId)->first();
            }
            $products['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->title];

            }, $products['data']);

            $products['json'] = json_encode($products['json'], JSON_UNESCAPED_UNICODE);
        }
        /*основные критерии*/
        if (!empty($planCriteria['operator_group'])) {
            $operatorsGroupsIds = explode(",", $planCriteria['operator_group']);
            foreach ($operatorsGroupsIds as $operatorsGroupId) {
                $operatorsGroups['data'][] = DB::table('users_group')->select('id', 'name')->where('id', $operatorsGroupId)->first();
            }

            $operatorsGroups['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->name];

            }, $operatorsGroups['data']);

            $operatorsGroups['json'] = json_encode($operatorsGroups['json'], JSON_UNESCAPED_UNICODE);
        }

        $plan['operators'] = $operators;
        $plan['operators_groups'] = $operatorsGroups;
        $plan['countries'] = $countries;
        $plan['offers'] = $offers;
        $plan['products'] = $products;

        if (!empty($planCriteria['operator_except'])) {
            $operatorsIdExcept = explode(",", $planCriteria['operator_except']);
            foreach ($operatorsIdExcept as $operatorIdExcept) {
                $operatorsExcept['data'][] = DB::table('users')->select('id', 'surname', 'name')->where('id', $operatorIdExcept)->first();
            }
            $operatorsExcept['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->surname . "  " . $element->name];

            }, $operatorsExcept['data']);

            $operatorsExcept['json'] = json_encode($operatorsExcept['json'], JSON_UNESCAPED_UNICODE);
        }

        if (!empty($planCriteria['country_except'])) {
            $countriesIdExcept = explode(",", $planCriteria['country_except']);
            foreach ($countriesIdExcept as $countryIdExcept) {
                $countriesExcept['data'][] = Country::where('code', strtolower($countryIdExcept))->first();
            }
            $countriesExcept['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->code . "",
                    'text' => $element->name];

            }, $countriesExcept['data']);

            $countriesExcept['json'] = json_encode($countriesExcept['json'], JSON_UNESCAPED_UNICODE);
        }

        if (!empty($planCriteria['offers_except'])) {
            $offersIdExcept = explode(",", $planCriteria['offers_except']);
            foreach ($offersIdExcept as $offerIdExcept) {
                $offersExcept['data'][] = Offer::where('id', $offerIdExcept)->first();
            }
            $offersExcept['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->name];

            }, $offersExcept['data']);

            $offersExcept['json'] = json_encode($offersExcept['json'], JSON_UNESCAPED_UNICODE);
        }
        if (!empty($planCriteria['products_except'])) {
            $productsIdExcept = explode(",", $planCriteria['products_except']);

            foreach ($productsIdExcept as $productIdExcept) {
                $productsExcept['data'][] = Product::where('id', $productIdExcept)->first();
            }
            $productsExcept['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->title];

            }, $productsExcept['data']);

            $productsExcept['json'] = json_encode($productsExcept['json'], JSON_UNESCAPED_UNICODE);
        }

        if (!empty($planCriteria['operator_group_except'])) {
            $operatorsGroupsIdsExcept = explode(",", $planCriteria['operator_group_except']);
            foreach ($operatorsGroupsIdsExcept as $operatorsGroupIdExcept) {
                $operatorsGroupsExcept['data'][] = DB::table('users_group')->select('id', 'name')->where('id', $operatorsGroupIdExcept)->first();
            }

            $operatorsGroupsExcept['json'] = array_map(function ($element) {
                return $elements = ['id' => "" . $element->id . "",
                    'text' => $element->name];

            }, $operatorsGroupsExcept['data']);

            $operatorsGroupsExcept['json'] = json_encode($operatorsGroupsExcept['json'], JSON_UNESCAPED_UNICODE);
        }
        /*исключающие критерии*/
        $plan['operators_groups_except'] = $operatorsGroupsExcept;
        $plan['operators_except'] = $operatorsExcept;
        $plan['countries_except'] = $countriesExcept;
        $plan['offers_except'] = $offersExcept;
        $plan['products_except'] = $productsExcept;

        $plan['product_type'] = json_encode(unserialize($planCriteria['product_type']));
        $plan['product_type_except'] = json_encode(unserialize($planCriteria['product_type_except']));

        return $plan;
    }

    public function test($id, Request $request)
    {
        $plan = Plan::where('id', $id)->with('company')->first();
        $company = Company::where('id', $plan->company_id)->first();
        $randomOrderIdNotApproved = NULL;
        $ordersNotApproved = NULL;
        $ordersNotApproved = DB::select(" SELECT target_status,o.id
        FROM orders AS o
        LEFT JOIN users AS u ON u.id = o.target_user
        LEFT JOIN companies AS c ON c.id = u.company_id
        LEFT JOIN order_products AS op ON o.id = op.order_id
        WHERE u.company_id = " . $company->id . "
        AND o.target_status NOT IN (1,2,3)
        AND o.target_user <> 0
        AND op.type <> 0
        ORDER BY o.time_created DESC
        LIMIT 2500");

        if (!empty($ordersNotApproved)) {

            $ids = [];
            foreach ($ordersNotApproved as $item) {
                $ids[] = $item->id;
            }
            $array = array_flip($ids);
            $randomOrderIdNotApproved = array_rand($array, 1);
        }

        if ($request->isMethod('post')) {

            if ($plan->type_method == 'action') {
                $financeTransactionByCriteria = collect(DB::select("SELECT  t.id, t.user_id, t.company_id, t.order_id, 
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross, t.qa
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                    AND t.order_id = " . $request['order_id'] . "
                         GROUP BY t.id
                        "))->first();


                if ($plan->status == 'inactive') {
                    $plan->status = 'active';
                    $plan->save();

                    if ($financeTransactionByCriteria) {
                        (new Result())->calculateDueToEvent($financeTransactionByCriteria, $plan);
                    } else {
                        Plan::sendForTest($id, $request->all());
                    }
                    $plan->status = 'inactive';
                    $plan->save();
                } else {

                    if ($financeTransactionByCriteria) {
                        (new Result())->calculateDueToEvent($financeTransactionByCriteria, $plan);
                    } else {
                        Plan::sendForTest($id, $request->all());
                    }
                }

                $transaction = Transaction::where([
                    ['order_id', $request['order_id']],
                    ['qa', 'test']
                ])->first();


                $logs = PlanLog::where([['plan_id', $plan->id], ['transaction_id', $transaction->id], ['qa', 'test']])->orderBy('id', 'DESC')->get();
                if (!empty($logs)) {
                    $data = [];
                    foreach ($logs as $log) {
                        $log['operator'] = DB::table('users')->where('id', $transaction->user_id)->first();
                        $log['company'] = DB::table('companies')->where('id', $transaction->company_id)->first();
                        $data[] = $log;
                        break;
                    }
                    if (!empty($data)) {
                        return response()->json([
                            'success' => [
                                'html' => view('finance.plan-test-logs', ['data' => $data])->render(),
                            ],
                        ]);
                    }
                }
            }

            if ($plan->type_method == 'schedule') {
                $period = [];

                if (!empty($request->input('month'))) {
                    $period['value'] = $request->input('month');
                }
                if (!empty($request->input('week'))) {
                    $period['value'] = $request->input('week');
                }
                if (!empty($request->input('day'))) {
                    $period['value'] = $request->input('day');
                }

                $period['type'] = $plan->interval;
                $period['plan_id'] = $id;

                if ($plan->status = 'inactive') {
                    $plan->status = 'active';
                    $plan->save();
                    $result = Plan::sendForTestIfSchedule($period, $qa = 'test');
                    $plan->status = 'inactive';
                    $plan->save();
                } else {
                    $result = Plan::sendForTestIfSchedule($period, $qa = 'test');
                }
                $testLogs = [];

                if (!empty($result)) {

                    foreach ($result as $log) {

                        $log['operator'] = DB::table('users')->where('id', $log->user_id)->first();
                        $log['company'] = DB::table('companies')->where('id', $log->company_id)->first();
                        $testLogs[] = $log;
                    }
                    return response()->json([
                        'success' => [
                            'html' => view('finance.plan-test-logs', ['data' => $testLogs])->render(),
                        ],
                    ]);
                }
            }
        }

        $plan = self::getDataForPlan($plan);
        $plan['company'] = $company;
        return view('finance.plan-test', ['plan' => $plan, 'randomOrderIdNotApproved' => $randomOrderIdNotApproved,
            'ordersNotApproved' => $ordersNotApproved]);
    }

    /*offer_rates list*/
    public function rates()
    {
        $planRates = PlanRate::
        with('planRatesOffers', 'planRatesOffers.offer')->get();
        return view('plans-and-rates.rates',
            ['planRates' => $planRates]);
    }

    /*add offer-rate */
    public function rateAdd()
    {
        return view('plans-and-rates.rate-create', ['countries' => Country::all()]);
    }

    /*add offer-rate */
    public function deletePlanOffer(Request $request)
    {
        if ($request->isMethod('POST')) {

            $rateOffer = $request->input('offerRow');
            if ($rateOffer) {
                $rateOffer = PlanRateOffer::where([
                    ['plan_rate_id', $rateOffer['plan_rate_id']],
                    ['offer_id', $rateOffer['offer_id']]
                ])->first();

                $rateOffer->delete();
                $result['success'] = true;
                return response()->json($result);
            }
        }
    }

    /*add offer-rate */
    public function deletePlanRate(Request $request)
    {
        $newArray = [];
        if ($request->isMethod('POST')) {
            $planRate = PlanRate::find($request->input('planRateId'));
            $data = json_decode($planRate->data);
            foreach ($data as $row) {

                if ($row->geo !== $request->input('geo')) {
                    $newArray[] = $row;
                }
                if (empty($newArray)) {
                    $newArray[] = ['geo' => NULL, 'rate' => NULL, 'upsell_rate' => NULL];
                }
            }

            if ($newArray) {
                $planRate->data = json_encode($newArray);
                $planRate->save();

                $result['success'] = true;
                return response()->json($result);
            }
        }
    }

    /*add offer-rate */
    public function deleteRate($id, Request $request)
    {
        if ($request->isMethod('POST')) {
            $planRate = PlanRate::find($id);
            if ($planRate->planRatesOffers()->delete() && $planRate->delete()) {
                $result['success'] = true;
                return response()->json($result);
            }
        }
    }

    /*edit offer-rate */
    public function editRate($id)
    {
        $planRate = PlanRate::with('planRatesOffers', 'planRatesOffers.offer')->where('id', $id)->first();
        $offers = PlanRateOffer::where('plan_rate_id', $planRate->id)->pluck('offer_id')->toArray();
        foreach ($offers as $offer) {
            $offersArray[] = ['id' => $offer,
                'text' => Offer::find($offer)->name];
        }

        return view('plans-and-rates.rate-edit', ['planRate' => $planRate,
            'planRateOffers' => json_encode($offersArray, JSON_UNESCAPED_UNICODE),
            'countries' => Country::all()]);
    }

    public function addNewPlanRateAjax(Request $request)
    {
        if ($request->isMethod('post')) {
            //var_dump($request->all()) . die();
            $offers = $request->input('offers');
            $offers = explode(',', $offers);
            $data[] = [
                'geo' => $request->input('geo'),
                'rate' => $request->input('rate'),
                'upsell_rate' => $request->input('upsell_rate')
            ];


            $planRate = PlanRate::create(['data' => json_encode($data)]);

            if ($planRate) {
                foreach ($offers as $offer) {
                    $planRateOffer[] = PlanRateOffer::create(['offer_id' => $offer, 'plan_rate_id' => $planRate->id]);
                }
            }
            if ($planRateOffer) {
                return response()->json([
                    'planRateId' => intval($planRate->id)]);
            }
        }
    }

    public function addPlanOffersAjax($id, Request $request)
    {
        $planOffers = NULL;
        if ($request->isMethod('post')) {
            $offers = $request->input('offers');
            $offers = explode(',', $offers);
            $request->input('geo');
            $offersIds = PlanRateOffer::where('plan_rate_id', $id)->pluck('offer_id')->toArray();
            $diffOffers = array_diff($offers, $offersIds);

            foreach ($diffOffers as $offer) {
                $existOffer = in_array($offer, $offersIds);
                if (!$existOffer) {
                    $planOffers = PlanRateOffer::create(['plan_rate_id' => $id, 'offer_id' => $offer]);
                }
            }
            if ($planOffers) {
                $planRate = PlanRate::with('planRatesOffers', 'planRatesOffers.offer')->where('id', $id)->first();
                return response()->json([
                    'html' => view('plans-and-rates.ajax.rate-create', ['planRate' => $planRate])->render(),
                ]);
            }
        }
    }

    public function addNewPlanRateWithLinkAjax($planRateId, Request $request)
    {
        if ($request->isMethod('post')) {
            $request->input('geo');
            $planRate = PlanRate::find($planRateId);
            $data = json_decode($planRate->data, true);
            foreach ($data as $row) {
                $geos[] = $row['geo'];
            }
            $existGeo = in_array($request->input('geo'), $geos);
            if ($existGeo) {
                return response()->json([
                    'error' => 'Ставки для указанной страны уже созданы',
                ]);
            }

            $newData = array_merge($data, [[
                'geo' => $request->input('geo'),
                'rate' => $request->input('rate'),
                'upsell_rate' => $request->input('upsell_rate')
            ]]);

            $planRate->data = json_encode($newData);

            if ($planRate->save()) {
                $planRate = PlanRate::with('planRatesOffers', 'planRatesOffers.offer')->where('id', $planRate->id)->first();
                return response()->json([
                    'html' => view('plans-and-rates.ajax.rate-create', ['planRate' => $planRate])->render(),
                ]);
            }
        }
    }
}
