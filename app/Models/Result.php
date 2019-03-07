<?php

namespace App\Models;

use App\Events\OnCreateTestLogTransactionEvent;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use  App\Models\PlanLog;
use App\Events\ResultTransactionEvent;

class Result extends Model
{
    public $currentMonth;
    public $currentWeekOfYear;
    public $currentDay;

    function __construct()
    {
        $this->currentMonth = Carbon::now()->month;
        $this->previousMonth = $this->currentMonth - 1;
        $this->currentWeekOfYear = Carbon::now()->weekOfYear;
        $this->previousWeekOfYear = $this->currentWeekOfYear - 1;
        $this->previousDay = Carbon::now()->yesterday()->toDateString();
    }

    /**
     * @param $plan
     * @param $data
     * @return mixed
     */
    public static function setTransactionEntity($plan, $data)
    {
        if ($plan->type_object == 'company') {
            $data['type'] = 'company';
        }
        if ($plan->type_object == 'operator') {
            $data['type'] = 'user';
        }
        return $data['type'];
    }

    /*SCHEDULE*/
    public function calculateDueToSchedule($period, $qa)
    {
        $currentDayOfMonth = Carbon::now()->day; //день месяца
        $currentDayOfWeek = Carbon::now()->dayOfWeek;
        /*get proper plans*/
        if (empty($period)) {
            if ($currentDayOfMonth == 1) {
                $plans = Plan::where([['interval', 'month'], ['status', 'active'], ['type_method', 'schedule']])
                    ->orWhere([['interval', 'day'], ['status', 'active'], ['type_method', 'schedule']])
                    ->get();
            } elseif ($currentDayOfWeek == 1 && $currentDayOfMonth == 1) {
                $plans = Plan::where([['interval', 'month'], ['status', 'active'], ['type_method', 'schedule']])
                    ->orWhere([['interval', 'week'], ['status', 'active'], ['type_method', 'schedule']])
                    ->orWhere([['interval', 'day'], ['status', 'active'], ['type_method', 'schedule']])
                    ->get();
            } elseif ($currentDayOfWeek == 1) {
                $plans = Plan::where([['interval', 'week'], ['status', 'active'], ['type_method', 'schedule']])
                    ->orWhere([['interval', 'day'], ['status', 'active'], ['type_method', 'schedule']])
                    ->get();
            } else {
                $plans = Plan::where([['interval', 'day'], ['status', 'active'], ['type_method', 'schedule']])->get();
            }
        } else {
            $plans = Plan::where([['id', $period['plan_id']], ['interval', $period['type']], ['status', 'active'], ['type_method', 'schedule']])->get();
        }
        foreach ($plans as $plan) {
            $companyType = $plan->company()->first()->type;

            if (!empty($period)) {
                $timeNow = $period['value'];
            } else {
                if ($plan->interval = 'month') {
                    $timeNow = $this->previousMonth;
                } elseif ($plan->interval = 'week') {
                    $timeNow = $this->previousWeekOfYear;
                } else {
                    $timeNow = $this->previousDay;
                }
            }
            if (!empty($period) && $period['type'] == 'week' && $plan->interval == 'week') {
                if ($period['type'] == 'week') {
                    $week = explode("W", $period['value']);
                    $timeNow = $week[1];
                }
            }

            /* HOUR --- MONTH */
            if ($companyType == 'hour' && $plan->interval == 'month') {
                $planBasis = self::getProductTypeAction($plan);

                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);
                $planCriteria = self::searchPlanCriteria($plan);
                $financeTransactionsByCriteria = DB::select("SELECT  t.id, t.company_id, t.order_id, 
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                          AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                           AND t.type = 'approve'
                        " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                        AND time_system_crm <> 0
                        OR time_system_pbx <> 0
                        OR time_talk <> 0
                    GROUP BY t.id");
                if ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'quantity') {

                    $planResult = DB::select(" SELECT  count(t.id) as approve,
                    CASE WHEN (COUNT(t.id)) " . $plan->compare_operator . " " . $plan->success_plan . " THEN 1 END AS success
                    FROM finance_transaction AS t
                    WHERE t.company_id = " . $plan->company_id . "
                     AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                      ");

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;

                        $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    }
                } elseif ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select(" SELECT  o.id, o.target_status, o.created_at,
                     count(CASE WHEN (o.target_status = 1) THEN 1 END) AS approve,
                    count(CASE WHEN (o.target_status = 2) THEN 1 END) AS failure,
                    count(CASE WHEN (o.target_status = 4) THEN 1 END) AS deleted
                    FROM orders AS o
                  LEFT JOIN users AS u ON o.target_user = u.id
                  LEFT JOIN companies AS c ON u.company_id = c.id
                  LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE u.company_id = " . $plan->company_id . "
                      AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                     GROUP BY approve ");
                    // dd($planResult);
                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countOrders = $planResult[0]->approve + $planResult[0]->failure + $planResult[0]->deleted;
                        $result = $planResult[0]->approve / $countOrders * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' .
                            $planResult[0]->approve . ' (подтверженные заказы) / ' . $countOrders . '(все заказы) * 100' .
                            $plan->compare_operator . $plan->success_plan;

                        $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    }
                } /*up1/up2/cross*/
                elseif (in_array(1, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(2, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(4, $planBasis) && $plan->basis_for_calculation == 'percent') {

                    if (in_array(1, $planBasis)) {
                        $up1 = "f.count_up1";
                    } else {
                        $up1 = 0;
                    };
                    if (in_array(2, $planBasis)) {
                        $up2 = "f.count_up2";
                    } else {
                        $up2 = 0;
                    };
                    if (in_array(4, $planBasis)) {
                        $cross = "f.count_cross";
                    } else {
                        $cross = 0;
                    };

                    $planResult = DB::select("
                      SELECT Sum(t.count_up1) AS count_up1, Sum(t.count_up2) AS count_up2, Sum(t.count_cross) 
                      AS count_cross,  count(t.id) AS approve
                      FROM finance_transaction AS t
                      WHERE t.company_id = 2
                   AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                      " . $searchByUserEntity . "
                      " . $searchByCompanyEntity . "
                      ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countUp1Up2Cross = $planResult[0]->count_up1 + $planResult[0]->count_up2 + $planResult[0]->count_cross;
                        $result = $countUp1Up2Cross / $planResult[0]->approve * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->count_up1 . ' ( Up_sell_1) ' .
                            ' + ' . $planResult[0]->count_up2 . ' ( Up_sell_2)' .
                            $planResult[0]->count_cross . ' ( Cross_sell)' . ' / ' . $planResult[0]->approve .
                            '( Общее кол-во подтвержденных заказов)' .
                            $plan->compare_operator . $plan->success_plan;

                        $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    }
                } else {

                    foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                        }
                        if ($plan->basis_for_calculation == 'sum-each') {

                            $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);
                        }
                        if ($plan->basis_for_calculation == 'percent-each') {

                            $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                        }

                        if (!empty($planResult) && $planResult[0]->success == 1 && $planResult !== NULL) {

                            $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        } elseif (!empty($planResult) && $planResult[0]->success == 0 && $planResult == NULL) {
                            $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                }
            }

            /* HOUR --- WEEK */
            if ($companyType == 'hour' && $plan->interval == 'week') {

                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);
                $planCriteria = self::searchPlanCriteria($plan);
                $financeTransactionsByCriteria = DB::select("SELECT  t.id, t.company_id, t.order_id, 
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross,  t.result, t.qa
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                  AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                   AND t.type = 'approve'
                        " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                        AND time_system_crm <> 0
                        OR time_system_pbx <> 0
                        OR time_talk <> 0
                    GROUP BY t.id");
                if ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'quantity') {

                    $planResult = DB::select(" SELECT  o.target_status, count(*) as approve, t.id,
                    CASE WHEN (COUNT(o.target_status)) " . $plan->compare_operator . " " . $plan->success_plan . " THEN 1 END AS success
                    FROM orders AS o
                    LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE t.company_id = " . $plan->company_id . "
                     AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                      ");

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;
                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {

                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } elseif ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select(" SELECT  o.id, o.target_status, o.created_at,
                     count(CASE WHEN (o.target_status = 1) THEN 1 END) AS approve,
                    count(CASE WHEN (o.target_status = 2) THEN 1 END) AS failure,
                    count(CASE WHEN (o.target_status = 4) THEN 1 END) AS deleted
                    FROM orders AS o
                  LEFT JOIN users AS u ON o.target_user = u.id
                  LEFT JOIN companies AS c ON u.company_id = c.id
                  LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE u.company_id = " . $plan->company_id . "
                      AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                     GROUP BY approve ");
                    // dd($planResult);
                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countOrders = $planResult[0]->approve + $planResult[0]->failure + $planResult[0]->deleted;
                        $result = $planResult[0]->approve / $countOrders * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' .
                            $planResult[0]->approve . ' (подтверженные заказы) / ' . $countOrders . '(все заказы) * 100' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } /*up1/up2/cross*/
                elseif (in_array(1, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(2, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(4, $planBasis) && $plan->basis_for_calculation == 'percent') {

                    if (in_array(1, $planBasis)) {
                        $up1 = "f.count_up1";
                    } else {
                        $up1 = 0;
                    };
                    if (in_array(2, $planBasis)) {
                        $up2 = "f.count_up2";
                    } else {
                        $up2 = 0;
                    };
                    if (in_array(4, $planBasis)) {
                        $cross = "f.count_cross";
                    } else {
                        $cross = 0;
                    };

                    $planResult = DB::select("
                      SELECT Sum(f.count_up1), Sum(f.count_up2), Sum(f.count_cross), count(f.id),
                        CASE
                      WHEN (Sum($up1) + Sum($up2) +  Sum($cross)) >=
                       SUM(f.id) THEN 1 END AS success
                      FROM finance_transaction AS f
                        WHERE f.company_id = " . $plan->company_id . "
                    AND WEEKOFYEAR(FROM_UNIXTIME (f.time_created)) = '" . $timeNow . "'
                      AND f.entity = 'company'");

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;
                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } else {
                    foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                        }
                        if ($plan->basis_for_calculation == 'sum-each') {

                            $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);
                        }
                        if ($plan->basis_for_calculation == 'percent-each') {

                            $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                        }

                        if (!empty($planResult) && $planResult[0]->success == 1 && $planResult !== NULL) {

                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        } elseif (!empty($planResult) && $planResult[0]->success == 0 && $planResult == NULL) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                }
            }

            /* HOUR --- DAY */
            if ($companyType == 'hour' && $plan->interval == 'day') {

                $planBasis = self::getProductTypeAction($plan);
                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);
                $planCriteria = self::searchPlanCriteria($plan);
                $financeTransactionsByCriteria = DB::select("SELECT  t.id, t.company_id, t.order_id, t.user_id,
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross, t.time_system_crm,
                    t.time_system_pbx, t.time_talk, t.result, t.qa
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                     AND t.type = 'approve'
                     AND FROM_UNIXTIME(t.time_created, '%Y-%m-%d') = '" . $timeNow . "'
                        " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                        AND time_system_crm <> 0
                        OR time_system_pbx <> 0
                        OR time_talk <> 0
                    GROUP BY t.id");

                if ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'quantity') {

                    $planResult = DB::select(" SELECT  count(t.id) as approve,
                    CASE WHEN (COUNT(t.id)) " . $plan->compare_operator . " " . $plan->success_plan . " THEN 1 END AS success
                    FROM finance_transaction AS t
                    WHERE t.company_id = " . $plan->company_id . "
                      AND FROM_UNIXTIME(t.time_created, '%Y-%m-%d') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                      ");

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {

                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;

                        $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);

                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {

                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    }
                } elseif ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select(" SELECT  o.id, o.target_status, o.created_at,
                     count(CASE WHEN (o.target_status = 1) THEN 1 END) AS approve,
                    count(CASE WHEN (o.target_status = 2) THEN 1 END) AS failure,
                    count(CASE WHEN (o.target_status = 4) THEN 1 END) AS deleted
                    FROM orders AS o
                  LEFT JOIN users AS u ON o.target_user = u.id
                  LEFT JOIN companies AS c ON u.company_id = c.id
                  LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE u.company_id = " . $plan->company_id . "
                      AND FROM_UNIXTIME(o.time_modified, '%Y-%m-%d') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                     GROUP BY approve ");
                    // dd($planResult);
                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countOrders = $planResult[0]->approve + $planResult[0]->failure + $planResult[0]->deleted;
                        $result = $planResult[0]->approve / $countOrders * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }
                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' .
                            $planResult[0]->approve . ' (подтверженные заказы) / ' . $countOrders . '(все заказы) * 100' .
                            $plan->compare_operator . $plan->success_plan;

                        $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    }
                } /*up1/up2/cross*/
                elseif (in_array(1, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(2, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(4, $planBasis) && $plan->basis_for_calculation == 'percent') {

                    if (in_array(1, $planBasis)) {
                        $up1 = "f.count_up1";
                    } else {
                        $up1 = 0;
                    };
                    if (in_array(2, $planBasis)) {
                        $up2 = "f.count_up2";
                    } else {
                        $up2 = 0;
                    };
                    if (in_array(4, $planBasis)) {
                        $cross = "f.count_cross";
                    } else {
                        $cross = 0;
                    };

                    $planResult = DB::select("
                      SELECT Sum(t.count_up1) AS count_up1, Sum(t.count_up2) AS count_up2, Sum(t.count_cross) 
                      AS count_cross,  count(t.id) AS approve
                      FROM finance_transaction AS t
                      WHERE t.company_id = 2
                      AND FROM_UNIXTIME(t.time_created, '%Y-%m-%d') = '" . $timeNow . "'
                      " . $searchByUserEntity . "
                      " . $searchByCompanyEntity . "
                      ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countUp1Up2Cross = $planResult[0]->count_up1 + $planResult[0]->count_up2 + $planResult[0]->count_cross;
                        $result = $countUp1Up2Cross / $planResult[0]->approve * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->count_up1 . ' ( Up_sell_1) ' .
                            ' + ' . $planResult[0]->count_up2 . ' ( Up_sell_2)' .
                            $planResult[0]->count_cross . ' ( Cross_sell)' . ' / ' . $planResult[0]->approve .
                            '( Общее кол-во подтвержденных заказов)' .
                            $plan->compare_operator . $plan->success_plan;

                        $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                    }
                } else {

                    foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                        }
                        if ($plan->basis_for_calculation == 'quantity') {

                            $planResult = self::calculateIfBasisQuantity($plan, $financeTransactionByCriteria);

                        }
                        if ($plan->basis_for_calculation == 'sum-each') {

                            $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);
                        }
                        if ($plan->basis_for_calculation == 'percent-each') {

                            $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                        }

                        if (!empty($planResult) && $planResult[0]->success == 1 && $planResult !== NULL) {

                            $this->calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);

                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        } elseif (!empty($planResult) && $planResult[0]->success == 0 && $planResult == NULL) {
                            $this->calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                }
            }

            /* LEAD --- MONTH*/
            if ($companyType == 'lead' && $plan->interval == 'month') {

                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);
                $planCriteria = self::searchPlanCriteria($plan);
                $planBasis = self::getProductTypeAction($plan);

                $financeTransactionsByCriteria = DB::select("SELECT  t.id, t.company_id, t.order_id, 
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross, t.result, t.qa
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                     AND t.type = 'approve'
                        AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                        " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                    GROUP BY t.id");
                if ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'quantity') {

                    $planResult = DB::select(" SELECT  count(t.id) as approve,
                    CASE WHEN (COUNT(t.id)) " . $plan->compare_operator . " " . $plan->success_plan . " THEN 1 END AS success
                    FROM finance_transaction AS t
                    WHERE t.company_id = " . $plan->company_id . "
                    AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                      ");


                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {

                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }

                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);

                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));

                            if ($financeTransactionByCriteria->qa == 'test') {

                                $testLog = new \App\Models\PlanLog();
                                $testLog->created_at = $plan->created_at;
                                $testLog->type = $data['result_type'];
                                $testLog->plan_id = $plan->id;
                                $testLog->transaction_id = $financeTransactionByCriteria->id;
                                $testLog->company_id = $financeTransactionByCriteria->company_id;
                                $testLog->user_id = $financeTransactionByCriteria->user_id;
                                $testLog->result = $data['result'];
                                $testLog->text = $planResult['log'];
                                $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
                                $testLogArray[] = $testLog;
                            }

                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }

                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                            if ($financeTransactionByCriteria->qa == 'test') {

                                $testLog = new \App\Models\PlanLog();
                                $testLog->created_at = $plan->created_at;
                                $testLog->type = $data['result_type'];
                                $testLog->plan_id = $plan->id;
                                $testLog->transaction_id = $financeTransactionByCriteria->id;
                                $testLog->company_id = $financeTransactionByCriteria->company_id;
                                $testLog->user_id = $financeTransactionByCriteria->user_id;
                                $testLog->result = $data['result'];
                                $testLog->text = $planResult['log'];
                                $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
                                $testLogArray[] = $testLog;
                            }

                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }

                    }
                } elseif ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select(" SELECT  o.id, o.target_status, o.created_at,
                     count(CASE WHEN (o.target_status = 1) THEN 1 END) AS approve,
                    count(CASE WHEN (o.target_status = 2) THEN 1 END) AS failure,
                    count(CASE WHEN (o.target_status = 4) THEN 1 END) AS deleted
                    FROM orders AS o
                  LEFT JOIN users AS u ON o.target_user = u.id
                  LEFT JOIN companies AS c ON u.company_id = c.id
                  LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE u.company_id = " . $plan->company_id . "
                         AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                     GROUP BY approve ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countOrders = $planResult[0]->approve + $planResult[0]->failure + $planResult[0]->deleted;
                        $result = $planResult[0]->approve / $countOrders * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' .
                            $planResult[0]->approve . ' (подтверженные заказы) / ' . $countOrders . '(все заказы) * 100' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                            if ($financeTransactionByCriteria->qa == 'test') {
                                list($testLog, $testLogArray) = $this->createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray);
                            }
                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } /*up1/up2/cross*/
                elseif (in_array(1, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(2, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(4, $planBasis) && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select("
                      SELECT Sum(t.count_up1) AS count_up1, Sum(t.count_up2) AS count_up2, Sum(t.count_cross)
                      AS count_cross,  count(t.id) AS approve
                      FROM finance_transaction AS t
                      WHERE t.company_id = 2
                         AND FROM_UNIXTIME(t.time_created, '%Y-%m') = '" . $timeNow . "'
                      " . $searchByUserEntity . "
                      " . $searchByCompanyEntity . "
                      ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countUp1Up2Cross = (!empty($planResult[0]->count_up1) ? $planResult[0]->count_up1 : 0) +
                            (!empty($planResult[0]->count_up2) ? $planResult[0]->count_up2 : 0)
                            + (!empty($planResult[0]->count_cross) ? $planResult[0]->count_cross : 0);
                        $result = $countUp1Up2Cross / $planResult[0]->approve * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->count_up1 . ' ( Up_sell_1) ' .
                            ' + ' . $planResult[0]->count_up2 . ' ( Up_sell_2)' .
                            $planResult[0]->count_cross . ' ( Cross_sell)' . ' / ' . $planResult[0]->approve .
                            '( Общее кол-во подтвержденных заказов)' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } else {
                    $testLogArray = [];
                    $data['result_type'] = '';
                    $planResult['log'] = '';
                    foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                            $planResult['log_type'] = 'test';
                        }
                        if ($plan->basis_for_calculation == 'quantity') {

                            $planResult = self::calculateIfBasisQuantity($plan, $financeTransactionByCriteria);

                        }
                        if ($plan->basis_for_calculation == 'sum-each') {

                            $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);
                        }

                        if ($plan->basis_for_calculation == 'percent-each') {
                            $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                        }

                        if (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == 1 && $planResult !== NULL) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);

                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));

                        } elseif (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == NULL) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        } elseif (!empty($planResult['log']) && empty($planResult[0])) {
                            //   var_dump('999').die();
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data['result_type'] = 'failed';
                            $data['result'] = 0;
                            $data['plan_id'] = $plan->id;
                            // var_dump($planResult).data();
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                        if ($financeTransactionByCriteria->qa == 'test') {

                            $testLogArray[] = self::createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray);
                        }
                    }

                    if (!empty($testLogArray)) {
                        return $testLogArray;
                    }
                }
            }

            /* LEAD -- WEEK */
            if ($companyType == 'lead' && $plan->interval == 'week') {

                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);
                $planBasis = self::getProductTypeAction($plan);
                /*select finance transactions due to some criteria*/
                $planCriteria = self::searchPlanCriteria($plan);
                $financeTransactionsByCriteria = DB::select("SELECT  t.id, t.company_id, t.order_id, t.user_id,
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                     AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                     AND t.type = 'approve'
                         " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                    GROUP BY t.id");

                if ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'quantity') {

                    $planResult = DB::select(" SELECT  count(t.id) as approve,
                    CASE WHEN (COUNT(t.id)) " . $plan->compare_operator . " " . $plan->success_plan . " THEN 1 END AS success
                    FROM finance_transaction AS t
                    WHERE t.company_id = " . $plan->company_id . "
                       AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                      ");


                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {

                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }

                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);

                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));

                            if ($financeTransactionByCriteria->qa == 'test') {

                                $testLog = new \App\Models\PlanLog();
                                $testLog->created_at = $plan->created_at;
                                $testLog->type = $data['result_type'];
                                $testLog->plan_id = $plan->id;
                                $testLog->transaction_id = $financeTransactionByCriteria->id;
                                $testLog->company_id = $financeTransactionByCriteria->company_id;
                                $testLog->user_id = $financeTransactionByCriteria->user_id;
                                $testLog->result = $data['result'];
                                $testLog->text = $planResult['log'];
                                $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
                                $testLogArray[] = $testLog;
                            }

                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }

                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                            if ($financeTransactionByCriteria->qa == 'test') {

                                $testLog = new \App\Models\PlanLog();
                                $testLog->created_at = $plan->created_at;
                                $testLog->type = $data['result_type'];
                                $testLog->plan_id = $plan->id;
                                $testLog->transaction_id = $financeTransactionByCriteria->id;
                                $testLog->company_id = $financeTransactionByCriteria->company_id;
                                $testLog->user_id = $financeTransactionByCriteria->user_id;
                                $testLog->result = $data['result'];
                                $testLog->text = $planResult['log'];
                                $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
                                $testLogArray[] = $testLog;
                            }

                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }

                    }
                } elseif ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select(" SELECT  o.id, o.target_status, o.created_at,
                     count(CASE WHEN (o.target_status = 1) THEN 1 END) AS approve,
                    count(CASE WHEN (o.target_status = 2) THEN 1 END) AS failure,
                    count(CASE WHEN (o.target_status = 4) THEN 1 END) AS deleted
                    FROM orders AS o
                  LEFT JOIN users AS u ON o.target_user = u.id
                  LEFT JOIN companies AS c ON u.company_id = c.id
                  LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE u.company_id = " . $plan->company_id . "
                       AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                     GROUP BY approve ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countOrders = $planResult[0]->approve + $planResult[0]->failure + $planResult[0]->deleted;
                        $result = $planResult[0]->approve / $countOrders * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' .
                            $planResult[0]->approve . ' (подтверженные заказы) / ' . $countOrders . '(все заказы) * 100' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                            if ($financeTransactionByCriteria->qa == 'test') {
                                list($testLog, $testLogArray) = $this->createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray);
                            }
                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } /*up1/up2/cross*/
                elseif (in_array(1, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(2, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(4, $planBasis) && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select("
                      SELECT Sum(t.count_up1) AS count_up1, Sum(t.count_up2) AS count_up2, Sum(t.count_cross)
                      AS count_cross,  count(t.id) AS approve
                      FROM finance_transaction AS t
                         WHERE t.company_id = " . $plan->company_id . "
                       AND WEEKOFYEAR(FROM_UNIXTIME (t.time_created)) = '" . $timeNow . "'
                      " . $searchByUserEntity . "
                      " . $searchByCompanyEntity . "
                      ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countUp1Up2Cross = (!empty($planResult[0]->count_up1) ? $planResult[0]->count_up1 : 0) +
                            (!empty($planResult[0]->count_up2) ? $planResult[0]->count_up2 : 0)
                            + (!empty($planResult[0]->count_cross) ? $planResult[0]->count_cross : 0);
                        $result = $countUp1Up2Cross / $planResult[0]->approve * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->count_up1 . ' ( Up_sell_1) ' .
                            ' + ' . $planResult[0]->count_up2 . ' ( Up_sell_2)' .
                            $planResult[0]->count_cross . ' ( Cross_sell)' . ' / ' . $planResult[0]->approve .
                            '( Общее кол-во подтвержденных заказов)' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }

                } else {
                    $testLogArray = [];
                    $data['result_type'] = '';
                    $planResult['log'] = '';
                    foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                            $planResult['log_type'] = 'test';
                        }
                        if ($plan->basis_for_calculation == 'quantity') {

                            $planResult = self::calculateIfBasisQuantity($plan, $financeTransactionByCriteria);

                        }
                        if ($plan->basis_for_calculation == 'sum-each') {

                            $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);
                        }
                        if ($plan->basis_for_calculation == 'percent-each') {

                            $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                        }
                        if (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == 1 && $planResult !== NULL) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));

                        } elseif (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == NULL) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        } elseif (!empty($planResult['log'])) {

                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data['result_type'] = 'failed';
                            $data['result'] = 0;
                            $data['plan_id'] = $plan->id;

                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                        if ($financeTransactionByCriteria->qa == 'test') {
                            $testLogArray[] = self::createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray);
                        }
                    }

                    if (!empty($testLogArray)) {
                        return $testLogArray;
                    }
                }
            }

            /* LEAD --> DAY */
            if ($companyType == 'lead' && $plan->interval === 'day') {

                $planBasis = self::getProductTypeAction($plan);
                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);
                $planCriteria = self::searchPlanCriteria($plan);

                $financeTransactionsByCriteria = DB::select("SELECT  t.id, t.company_id, t.order_id,
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross, t.type, t.user_id,
                    t.result, t.qa
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                      AND FROM_UNIXTIME(t.time_created, '%Y-%m-%d') = '" . $timeNow . "'
                       AND t.type = 'approve'
                        " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                    GROUP BY t.id");

                if ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'quantity') {

                    $planResult = DB::select(" SELECT  count(t.id) as approve,
                    CASE WHEN (COUNT(t.id)) " . $plan->compare_operator . " " . $plan->success_plan . " THEN 1 END AS success
                    FROM finance_transaction AS t
                    WHERE t.company_id = " . $plan->company_id . "
                      AND FROM_UNIXTIME(t.time_created, '%Y-%m-%d') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                      ");


                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {

                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }

                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);

                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));

                            if ($financeTransactionByCriteria->qa == 'test') {

                                $testLog = new \App\Models\PlanLog();
                                $testLog->created_at = $plan->created_at;
                                $testLog->type = $data['result_type'];
                                $testLog->plan_id = $plan->id;
                                $testLog->transaction_id = $financeTransactionByCriteria->id;
                                $testLog->company_id = $financeTransactionByCriteria->company_id;
                                $testLog->user_id = $financeTransactionByCriteria->user_id;
                                $testLog->result = $data['result'];
                                $testLog->text = $planResult['log'];
                                $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
                                $testLogArray[] = $testLog;
                            }

                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }

                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                            if ($financeTransactionByCriteria->qa == 'test') {

                                $testLog = new \App\Models\PlanLog();
                                $testLog->created_at = $plan->created_at;
                                $testLog->type = $data['result_type'];
                                $testLog->plan_id = $plan->id;
                                $testLog->transaction_id = $financeTransactionByCriteria->id;
                                $testLog->company_id = $financeTransactionByCriteria->company_id;
                                $testLog->user_id = $financeTransactionByCriteria->user_id;
                                $testLog->result = $data['result'];
                                $testLog->text = $planResult['log'];
                                $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
                                $testLogArray[] = $testLog;
                            }

                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }

                    }
                }
                elseif ($planBasis[0] == 'approve' && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select(" SELECT  o.id, o.target_status, o.created_at,
                     count(CASE WHEN (o.target_status = 1) THEN 1 END) AS approve,
                    count(CASE WHEN (o.target_status = 2) THEN 1 END) AS failure,
                    count(CASE WHEN (o.target_status = 4) THEN 1 END) AS deleted
                    FROM orders AS o
                  LEFT JOIN users AS u ON o.target_user = u.id
                  LEFT JOIN companies AS c ON u.company_id = c.id
                  LEFT JOIN finance_transaction AS t ON t.order_id = o.id
                    WHERE u.company_id = " . $plan->company_id . "
                      AND FROM_UNIXTIME(o.time_modified, '%Y-%m-%d') = '" . $timeNow . "'
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                     GROUP BY approve ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countOrders = $planResult[0]->approve + $planResult[0]->failure + $planResult[0]->deleted;
                        $result = $planResult[0]->approve / $countOrders * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' .
                            $planResult[0]->approve . ' (подтверженные заказы) / ' . $countOrders . '(все заказы) * 100' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                            if ($financeTransactionByCriteria->qa == 'test') {
                                list($testLog, $testLogArray) = $this->createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray);
                            }
                        }
                        if (!empty($testLogArray)) {
                            return $testLogArray;
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } /*up1/up2/cross*/
                elseif (in_array(1, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(2, $planBasis) && $plan->basis_for_calculation == 'percent' ||
                    in_array(4, $planBasis) && $plan->basis_for_calculation == 'percent') {

                    $planResult = DB::select("
                      SELECT Sum(t.count_up1) AS count_up1, Sum(t.count_up2) AS count_up2, Sum(t.count_cross)
                      AS count_cross,  count(t.id) AS approve
                      FROM finance_transaction AS t
                      WHERE t.company_id = 2
                      AND FROM_UNIXTIME(t.time_created, '%Y-%m-%d') = '" . $timeNow . "'
                      " . $searchByUserEntity . "
                      " . $searchByCompanyEntity . "
                      ");

                    $countOrders = NULL;
                    if (!empty($planResult)) {
                        $countUp1Up2Cross = (!empty($planResult[0]->count_up1) ? $planResult[0]->count_up1 : 0) +
                            (!empty($planResult[0]->count_up2) ? $planResult[0]->count_up2 : 0)
                            + (!empty($planResult[0]->count_cross) ? $planResult[0]->count_cross : 0);
                        $result = $countUp1Up2Cross / $planResult[0]->approve * 100;

                        if ($plan->compare_operator == '>' && $result > $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } elseif ($plan->compare_operator == '>=' && $result >= $plan->success_plan) {
                            $planResult[0]->success = 1;
                        } else {
                            $planResult[0]->success = NULL;
                        }
                    }

                    if (!empty($planResult) && $planResult[0]->success !== NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->count_up1 . ' ( Up_sell_1) ' .
                            ' + ' . $planResult[0]->count_up2 . ' ( Up_sell_2)' .
                            $planResult[0]->count_cross . ' ( Cross_sell)' . ' / ' . $planResult[0]->approve .
                            '( Общее кол-во подтвержденных заказов)' .
                            $plan->compare_operator . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->approve . ' (approve)' .
                            '< ( <= )' . $plan->success_plan;

                        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                    }
                } else {
                    $testLogArray = [];
                    $data['result_type'] = '';
                    $planResult['log'] = '';
                    foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                            $planResult['log_type'] = 'test';
                        }
                        if ($plan->basis_for_calculation == 'quantity') {

                            $planResult = self::calculateIfBasisQuantity($plan, $financeTransactionByCriteria);

                        }
                        if ($plan->basis_for_calculation == 'sum-each') {

                            $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);
                        }

                        if ($plan->basis_for_calculation == 'percent-each') {
                            $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                        }

                        if (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == 1 && $planResult !== NULL) {
                            //  var_dump('999').die();
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);

                            $data['result_type'] = 'success';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));

                        }
                        elseif (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == NULL) {
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                            $data['result_type'] = 'failed';
                            /*Ивент который отслеживает логи на новую транзакцию*/
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }
                        elseif (!empty($planResult['log'])) {
                            //   var_dump('999').die();
                            if (!empty($qa)) {
                                $financeTransactionByCriteria->qa = 'test';
                                $planResult['log_type'] = 'test';
                            }
                            $data['result_type'] = 'failed';
                            $data['result'] = 0;
                            $data['plan_id'] = $plan->id;
                            // var_dump($planResult).data();
                            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        }

                        if ($financeTransactionByCriteria->qa == 'test') {

                            $testLogArray[] = self::createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray);
                        }
                    }

                    if (!empty($testLogArray)) {
                        return $testLogArray;
                    }
                }
            }
        }
    }

    /*EVENT*/
    public
    static function calculateDueToEvent($transaction, $plan)
    {
        $company = CompanyRepository::getCompany($transaction->company_id);

        $plans = Plan::where('company_id', $company->id)->get();
        if($transaction->qa == 'test'){
            $plans[0] = $plan;
        }
        foreach ($plans as $plan) {

            if ($plan->status == 'active') {

                list($searchByCompanyEntity, $searchByUserEntity) = self::searchByTransactionType($plan);

                /*select finance transactions due to some criteria*/
                $planCriteria = self::searchPlanCriteria($plan);
                $financeTransactionByCriteria = DB::select("SELECT  t.id, t.user_id, t.company_id, t.order_id, 
                    t.approve, t.up1, t.up2, t.`cross`, t.count_up1, t.count_up2, t.count_cross, t.qa
                    FROM finance_transaction AS t
                        LEFT JOIN order_products AS op ON t.order_id = op.order_id
                    WHERE t.company_id = " . $plan->company_id . "
                        AND t.id = " . $transaction->id . "
                        " . $planCriteria . "
                        " . $searchByUserEntity . "
                        " . $searchByCompanyEntity . "
                         GROUP BY t.id
                        ");

                if (!empty($financeTransactionByCriteria)) {

                    $financeTransactionByCriteria = $financeTransactionByCriteria[0];
                }
                else{
                    return;
                }
                /* if plan-parameters => active/company/action/sum */
                if (!empty($financeTransactionByCriteria) && $plan->basis_for_calculation == 'sum-each') {
                    $planResult = self::calculateIfBasisSum($plan, $financeTransactionByCriteria);

                    if (!empty($planResult) && $planResult[0]->success == 1 && $planResult !== NULL) {

                        $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                        $data['result_type'] = 'success';
                        /*Ивент который отслеживает логи на новую транзакцию*/
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
                    } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                        $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                        $data['result_type'] = 'failed';
                        /*Ивент который отслеживает логи на новую транзакцию*/
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
                    } else {
                        return;
                    }
                }

                /* if plan-parameters => active/company/action/percent */
                if (!empty($financeTransactionByCriteria) && $plan->basis_for_calculation == 'percent-each') {
                    $planResult = self::calculateIfBasisPercent($plan, $financeTransactionByCriteria);
                    if (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == 1 && $planResult !== NULL) {

                        $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                        $data['result_type'] = 'success';
                        /*Ивент который отслеживает логи на новую транзакцию*/
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
                    } elseif (!empty($planResult) && !empty($planResult[0]) && $planResult[0]->success == NULL) {
                        $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                        $data['result_type'] = 'failed';
                        /*Ивент который отслеживает логи на новую транзакцию*/
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
                    } elseif (!empty($planResult['log'])) {
                        if (!empty($qa)) {
                            $financeTransactionByCriteria->qa = 'test';
                            $planResult['log_type'] = 'test';
                        }
                        $data['result_type'] = 'failed';
                        $data['result'] = 0;
                        $data['plan_id'] = $plan->id;
                        // var_dump($planResult).data();
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
                    }
                }

                /* if plan-parameters => active/company/action/quantity */
                if (!empty($financeTransactionByCriteria) && $plan->basis_for_calculation == 'quantity') {
                    $planResult = self::calculateIfBasisQuantity($plan, $financeTransactionByCriteria);

                    if (!empty($planResult) && $planResult[0]->success == 1 && $planResult !== NULL) {

                        $data = self::calculateSuccessPlan($financeTransactionByCriteria, $plan);
                        $data['result_type'] = 'success';
                        /*Ивент который отслеживает логи на новую транзакцию*/
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
                    } else {

                        $data = self::calculateFailedPlan($financeTransactionByCriteria, $plan);
                        $data['result_type'] = 'failed';
                        /*Ивент который отслеживает логи на новую транзакцию*/
                        event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
                        return;
//                        }
                    }
                }
            }
        }
    }

    /**
     * @param $plan
     * @param $financeTransactionByCriteria
     * @return array
     */
    public
    static function calculateIfBasisQuantity($plan, $financeTransactionByCriteria)
    {

        $planBasis = self::getProductTypeAction($plan);

        if (in_array(1, $planBasis)) {
            $up1 = "f.count_up1";
        } else {
            $up1 = 0;
        };
        if (in_array(2, $planBasis)) {
            $up2 = "f.count_up2";
        } else {
            $up2 = 0;
        };
        if (in_array(4, $planBasis)) {
            $cross = "f.count_cross";
        } else {
            $cross = 0;
        };
        $planResult = DB::select("SELECT f.count_up1, f.count_up2, f.count_cross,
                    CASE
                        WHEN " . $up1 . " + " . $up2 . " + " . $cross . " " . $plan->compare_operator . " 
                        " . $plan->success_plan . " THEN 1 END AS success
                    FROM finance_transaction AS f
                    WHERE f.type = 'approve'
                    AND f.id =  " . $financeTransactionByCriteria->id . "
                    GROUP BY success
                    ");

        if (!empty($planResult) && $planResult[0]->success !== 0 && $planResult[0]->success !== NULL) {
            $planResult['log'] = 'Согласно алгоритма: ' . ($planResult[0]->count_up1 ? $planResult[0]->count_up1 . '(up_sell_1)' : '0 up_sell') .
                '+' . ($planResult[0]->count_up2 ? $planResult[0]->count_up2 . '(up_sell_2)' : ' 0 (up_sell_2)') .
                '+ ' . ($planResult[0]->count_cross ? $planResult[0]->count_cross . '(cross_sell)' : ' 0 (cross_sell)') . $plan->compare_operator . ' ' .
                $plan->success_plan;

        } elseif (!empty($planResult) && $planResult[0]->success == 0 || !empty($planResult) && $planResult[0]->success == NULL) {
            $planResult['log'] = 'Согласно алгоритма: ' . ($planResult[0]->count_up1 ? $planResult[0]->count_up1 . '(up_sell_1)' : '0 up_sell') .
                '+' . ($planResult[0]->count_up2 ? $planResult[0]->count_up2 . '(up_sell_2)' : ' 0 (up_sell_2)') .
                '+ ' . ($planResult[0]->count_cross ? $planResult[0]->count_cross . '(cross_sell)' : ' 0 (cross_sell)') . '< ( <= )' . ' ' .
                $plan->success_plan;

        }

        return $planResult;
    }

    /**
     * @param $plan
     * @param $financeTransactionByCriteria
     * @return mixed
     */
    public
    static function calculateIfBasisPercent($plan, $financeTransactionByCriteria)
    {

        $productTypeAction = unserialize($plan->product_type_action);
        $planResult = '';
        if ($productTypeAction[0] == 'total') {

            $planResult = DB::select("SELECT o.price_input, o.price_total,
                        CASE
                            WHEN (100 - ROUND( o.price_input / o.price_total  )* 100) " . $plan->compare_operator . "
                            " . $plan->success_plan . "  THEN 1 END AS success
                        FROM orders AS o
                         WHERE o.id = " . $financeTransactionByCriteria->order_id . "
                         GROUP BY success
                         ");


            if (!empty($planResult) && $planResult[0]->success !== 0 && $planResult[0]->success !== NULL) {
                $planResult['log'] = 'Согласно алгоритма: 100 - ('
                    . $planResult[0]->price_input . '( первичная сумма заказа)  / '
                    . $planResult[0]->price_total . '( общая суммма заказа) )  * 100 '
                    . $plan->compare_operator . ' ' .
                    $plan->success_plan . '%';
                return $planResult;

            } elseif (!empty($planResult) && $planResult[0]->success == 0 || !empty($planResult) && $planResult[0]->success == NULL) {
                $planResult['log'] = 'Согласно алгоритма: 100 - ('
                    . $planResult[0]->price_input . '( первичная сумма заказа)  / '
                    . $planResult[0]->price_total . '( общая суммма заказа) ) * 100 < (<=)' . $plan->success_plan . '%';
                return $planResult;
            } else {
                $planResult['log'] = 'Согласно алгоритма: 100 - ('
                    . $planResult[0]->price_input . '( первичная сумма заказа)  / '
                    . $planResult[0]->price_total . '( общая суммма заказа) ) * 100 < (<=)' . $plan->success_plan . '%';
                return $planResult;
            }
        }
        /*search if isset sip1, up2, cross*/
        if ($productTypeAction == '1' || $productTypeAction == '2' || $productTypeAction == '4') {

            $typeCriteria['up1'] = ($productTypeAction == '1') ? "AND op.type = 1" : '';
            $typeCriteria['up2'] = ($productTypeAction == '2') ? "AND op.type = 2" : '';
            $typeCriteria['cross'] = ($productTypeAction == '4') ? "AND op.type = 4" : '';
            $typeCriteria = "
            " . $typeCriteria['up1'] . "
            " . $typeCriteria['up2'] . "
            " . $typeCriteria['cross'] . "
            ";

            $planResult = DB::select("SELECT o.price_input, o.price_total, op.price, op.type,
                        CASE WHEN
                        (100 - ROUND(op.price / o.price_input )* 100) " . $plan->compare_operator . " " . $plan->success_plan . "  THEN 1 END AS success
                        FROM orders AS o
                        LEFT JOIN order_products AS op ON o.id = op.order_id
                         WHERE o.id = " . $financeTransactionByCriteria->order_id . "
                        " . $typeCriteria . "
                        GROUP BY success
                        ");

            if (!empty($planResult) && $planResult[0]->success !== 0 && $planResult[0]->success !== NULL) {

                $planResult['log'] = 'Согласно алгоритма: 100 - (' .
                    ($planResult[0]->type == 1 ? $planResult[0]->price . '( up_sell_1)' : '')
                    . ($planResult[0]->type == 2 ? $planResult[0]->price . '( up_sell_2)' : '')
                    . ($planResult[0]->type == 4 ? $planResult[0]->price . '( cross_sell)' : '') .
                    ' / '
                    . $planResult[0]->price_input . '( первичная суммма заказа) ) * 100 '

                    . $plan->compare_operator . ' ' . $plan->success_plan . ' % ';
                return $planResult;
            } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                $planResult['log'] = 'Согласно алгоритма:  100 - (' .
                    ($planResult[0]->type == 1 ? $planResult[0]->price . '( up_sell_1)' : '')
                    . ($planResult[0]->type == 2 ? $planResult[0]->price . '( up_sell_2)' : '')
                    . ($planResult[0]->type == 4 ? $planResult[0]->price . '( cross_sell)' : '') .
                    ' / '
                    . $planResult[0]->price_input . '( первичная суммма заказа) ) * 100 '

                    . '< ( <= )' . ' ' . $plan->success_plan;
                return $planResult;
            } else {
                $planResult['log'] = 'Согласно алгоритма:' . ($productTypeAction == 1 ? '( up_sell_1)' : '')
                    . ($productTypeAction == 2 ? '( up_sell_2)' : '')
                    . ($productTypeAction == 4 ? '( cross_sell)' : '') . ' транзакция 
                не содержит, план рассчитать не возможно';
                return $planResult;
            }
        }
    }

    /**
     * @param $plan
     * @param $financeTransactionByCriteria
     * @return $planResult
     */
    public
    static function calculateIfBasisSum($plan, $financeTransactionByCriteria)
    {
        $productTypeAction = unserialize($plan->product_type_action)[0];
        if ($productTypeAction == 'total') {
            $planBasis = "o.price_total";
            $planResult = DB::select("SELECT o.price_input, o.price_total, op.price, op.type,
                        CASE
                            WHEN (" . $planBasis . " - o.price_input) " . $plan->compare_operator . " " . $plan->success_plan . "  THEN 1 END AS success
                        FROM orders AS o
                        LEFT JOIN order_products AS op ON o.id = op.order_id
                        WHERE o.id = " . $financeTransactionByCriteria->order_id . "
                        GROUP BY success
                        ");

            if (!empty($planResult) && $planResult[0]->success !== 0 && $planResult[0]->success !== NULL) {
                $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->price_total . '( общая суммма заказа)' .
                    ' - ' . $planResult[0]->price_input . '( первичная суммма заказа)' . $plan->compare_operator . ' ' .
                    $plan->success_plan;
                return $planResult;
            } elseif (!empty($planResult) && $planResult[0]->success == NULL) {
                $planResult['log'] = 'Согласно алгоритма: ' . $planResult[0]->price_total . '( общая суммма заказа)' .
                    ' - ' . $planResult[0]->price_input . '( первичная суммма заказа)' . '< ( <= )' . ' ' .
                    $plan->success_plan;
                return $planResult;
            }
        }
        if ($productTypeAction == '1' || $productTypeAction == '2' || $productTypeAction == '4') {
            // var_dump($productTypeAction).die();
            DB::connection()->enableQueryLog();
            $planResult = DB::select("SELECT o.price_input, o.price_total, op.price, op.type,
                        CASE
                            WHEN (op.price - o.price_input) " . $plan->compare_operator . " " . $plan->success_plan . "  THEN 1 END AS success
                        FROM orders AS o
                        LEFT JOIN order_products AS op ON o.id = op.order_id
                        WHERE o.id = " . $financeTransactionByCriteria->order_id . "
                       AND op.type = " . $productTypeAction . "
                       GROUP BY success
                        ");
            $ss = DB::connection()->getQueryLog();

            if (!empty($planResult) && $planResult[0]->success !== 0 && $planResult[0]->success !== NULL) {

                $planResult['log'] = 'Согласно алгоритма: ' . ($planResult[0]->type == 1 ? $planResult[0]->price . '( up_sell_1)' : '')
                    . ($planResult[0]->type == 2 ? $planResult[0]->price . '( up_sell_2)' : '')
                    . ($planResult[0]->type == 4 ? $planResult[0]->price . '( cross_sell)' : '')
                    . ' - ' . $planResult[0]->price_input . '( первичная суммма заказа)'
                    . $plan->compare_operator . ' ' . $plan->success_plan;

                return $planResult;
            } elseif (!empty($planResult) && $planResult[0]->success == NULL) {

                $planResult['log'] = 'Согласно алгоритма: '
                    . ($planResult[0]->type == 1 ? $planResult[0]->price . '( up_sell_1)' : '')
                    . ($planResult[0]->type == 2 ? $planResult[0]->price . '( up_sell_2)' : '')
                    . ($planResult[0]->type == 4 ? $planResult[0]->price . '( cross_sell)' : '')
                    . ' - ' . $planResult[0]->price_input . '( первичная суммма заказа)'
                    . '< ( <= )' . ' ' . $plan->success_plan;
                return $planResult;
            } else {
                $planResult['log'] = 'Согласно алгоритма:' . ($productTypeAction == 1 ? '( up_sell_1)' : '')
                    . ($productTypeAction == 2 ? '( up_sell_2)' : '')
                    . ($productTypeAction == 4 ? '( cross_sell)' : '') . ' транзакция 
                не содержит, план рассчитать не возможно';

                return $planResult;
            }
        }
    }

    /**
     * @param $transaction
     * @param $plan
     * @return array
     */
    public
    static function calculateSuccessPlan($transaction, $plan)
    {
        $data = [];
        $orderId = $transaction->order_id;
        $new_prices = json_decode($plan->new_prices, true);

        $data['result'] = (isset($new_prices['approve-bonus']) ? $new_prices['approve-bonus'] : 0) +
            (isset($new_prices['up_sell-bonus']) ? $new_prices['up_sell-bonus'] : 0) * $transaction->count_up1 +
            (isset($new_prices['up_sell_2-bonus']) ? $new_prices['up_sell_2-bonus'] : 0) * $transaction->count_up2 +
            (isset($new_prices['cross_sell-bonus']) ? $new_prices['cross_sell-bonus'] : 0) * $transaction->count_cross +
            (isset($new_prices['fixed-bonus']) ? $new_prices['fixed-bonus'] : 0);

        $data['type'] = self::setTransactionEntity($plan, $data);
        if ($data['result'] !== 0 && $data['result'] !== NULL && !empty($data['result'])) {
            $data['plan_id'] = $plan->id;

            if ($transaction->qa == 'test') {

                (new Transaction())->createOrUpdateTransaction($orderId, $type = 'bonus', $data, $test = 'test');

            } else {
                (new Transaction())->createOrUpdateTransaction($orderId, $type = 'bonus', $data, $test = '');
            }
        } else {
            $data['plan_id'] = $plan->id;
        }
        return $data;
    }

    /**
     * @param $transaction
     * @param $plan
     * @return mixed
     */
    public
    static function calculateFailedPlan($transaction, $plan)
    {
        $data = [];
        $new_prices = json_decode($plan->new_prices, true);
        $orderId = $transaction->order_id;
        $data['type'] = self::setTransactionEntity($plan, $data);
        $data['plan_id'] = $plan->id;
        $data['result'] = (isset($new_prices['approve-retention']) ? $new_prices['approve-retention'] : 0) +
            (isset($new_prices['up_sell-retention']) ? $new_prices['up_sell-retention'] : 0) * $transaction->count_up1 +
            (isset($new_prices['up_sell_2-retention']) ? $new_prices['up_sell_2-retention'] : 0) *
            $transaction->count_up2 + (isset($new_prices['cross_sell-retention']) ? $new_prices['cross_sell-retention'] : 0) *
            $transaction->count_cross + (isset($new_prices['fixed-retention']) ? $new_prices['fixed-retention'] : 0);

        if ($data['result'] !== 0 && $data['result'] !== NULL && !empty($data['result'])) {

            $data['plan_id'] = $plan->id;
            if ($transaction->qa == 'test') {
                (new Transaction())->createOrUpdateTransaction($orderId, $type = 'retention', $data, $test = 'test');
            } else {
                (new Transaction())->createOrUpdateTransaction($orderId, $type = 'retention', $data, $test = '');
            }
        } else {
            $data['plan_id'] = $plan->id;
        }
        return $data;
    }

    public
    static function processingResultLog($transaction, $planLog, $data)
    {
        $newLog = new PlanLog();
        $newLog->type = $data['result_type'];

        $newLog->plan_id = $data['plan_id'];
        $newLog->transaction_id = $transaction->id;

        $newLog->company_id = $transaction->company_id;
        $newLog->user_id = !empty($transaction->user_id) ? $transaction->user_id : 0;

        $newLog->result = $data['result'];

        $newLog->text = $planLog;
        $newLog->qa = !empty($transaction->qa) ? $transaction->qa : NULL;
        $newLog->order_id = !empty($transaction->order_id) ? $transaction->order_id : NULL;

        $newLog->save();
    }

    /**
     * @param $plan
     * @return mixed
     */
    public
    static function searchPlanCriteria($plan)
    {
        $planCriteria = json_decode($plan->criteria, true);

        /*select finance transactions due to some criteria*/
        $planCriteria['operator_group'] = (!empty($planCriteria['operator_group'])) ? "AND t.user_id IN('" . $planCriteria['operator_group'] . "')" : '';
        $planCriteria['operator'] = (!empty($planCriteria['operator'])) ? "AND t.user_id IN('" . $planCriteria['operator'] . "')" : '';
        $planCriteria['country'] = (!empty($planCriteria['country'])) ? "AND t.geo IN('" . strtolower($planCriteria['country']) . "')" : '';
        $planCriteria['offers'] = (!empty($planCriteria['offers'])) ? "AND  t.offer_id IN('" . $planCriteria['offers'] . "')" : '';
        $planCriteria['products'] = (!empty($planCriteria['products'])) ? "AND op.product_id  IN('" . $planCriteria['products'] . "')" : '';
        $planCriteria['product_type'] = (!empty($planCriteria['product_type'])) ? "AND op.type IN('" . $planCriteria['product_type'] . "')" : '';

        $planCriteria['operator_group_except'] = (!empty($planCriteria['operator_group_except'])) ? "AND t.user_id NOT IN('" . $planCriteria['operator_except'] . "')" : '';
        $planCriteria['operator_except'] = (!empty($planCriteria['operator_except'])) ? "AND t.user_id NOT IN('" . $planCriteria['operator_except'] . "')" : '';
        $planCriteria['country_except'] = (!empty($planCriteria['country_except'])) ? "AND t.geo NOT IN('" . strtolower($planCriteria['country_except']) . "')" : '';
        $planCriteria['offers_except'] = (!empty($planCriteria['offers_except'])) ? "AND  t.offer_id NOT IN('" . $planCriteria['offers_except'] . "')" : '';
        $planCriteria['products_except'] = (!empty($planCriteria['products_except'])) ? "AND op.product_id NOT IN('" . $planCriteria['products_except'] . "')" : '';
        $planCriteria['product_type_except'] = (!empty($planCriteria['product_type_except'])) ? "AND op.type NOT IN('" . $planCriteria['product_type_except'] . "')" : '';

        $planCriteria = "
                        " . $planCriteria['operator'] . "
                        " . $planCriteria['operator_group'] . "
                        " . $planCriteria['country'] . "
                        " . $planCriteria['offers'] . "
                        " . $planCriteria['products'] . "
                        " . $planCriteria['product_type'] . "

                        " . $planCriteria['operator_except'] . "
                        " . $planCriteria['operator_group_except'] . "
                        " . $planCriteria['operator_except'] . "
                        " . $planCriteria['country_except'] . "
                        " . $planCriteria['offers_except'] . "
                        " . $planCriteria['products_except'] . "
                        " . $planCriteria['product_type_except'] . "
                        ";

        return $planCriteria;
    }

    /**
     * @param $qa
     * @param $financeTransactionsByCriteria
     * @param $plan
     * @param $planResult
     * @return array
     */
    public
    function calculateSuccessResultIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult)
    {
        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {

            $orderId = $financeTransactionByCriteria->order_id;

            $new_prices = json_decode($plan->new_prices, true);

            $data = [];
            $data['plan_id'] = $plan->id;
            $data['user_id'] = (!empty($financeTransactionByCriteria->user_id) ? $financeTransactionByCriteria->user_id : 0);

            $data['time_system_crm'] = (!empty($financeTransactionByCriteria->time_system_crm) ? $financeTransactionByCriteria->time_system_crm : 0);
            $data['time_talk'] = (!empty($financeTransactionByCriteria->time_talk) ? $financeTransactionByCriteria->time_talk : 0);
            $data['time_system_pbx'] = (!empty($financeTransactionByCriteria->time_system_pbx) ? $financeTransactionByCriteria->time_system_pbx : 0);


            $data['type'] = self::setTransactionEntity($plan, $data);
            /* грн:сек(за сек) * грн = надбавка */

            $data['result'] = (isset($new_prices['in_system_bonus']) ?
                    $new_prices['in_system_bonus'] / 3600 * $data['time_system_crm'] : 0) +
                (isset($new_prices['in_talk_bonus']) ? $new_prices['in_talk_bonus'] / 3600 *
                    $financeTransactionByCriteria->time_talk : 0);

            $data['result'] = round($data['result']);

            if ($data['result'] !== 0 && $data['result'] !== NULL && !empty($data['result'])) {

                if (!empty($qa)) {

                    (new Transaction())->createOrUpdateTransaction($orderId, $type = 'bonus', $data, $test = 'test');
                } else {
                    (new Transaction())->createOrUpdateTransaction($orderId, $type = 'bonus', $data, $test = '');
                }
                $data['result_type'] = 'success';
                /*Ивент который отслеживает логи на новую транзакцию*/
                event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
            }
        }
    }

    /**
     * @param $qa
     * @param $financeTransactionsByCriteria
     * @param $plan
     * @param $planResult
     * @return array
     */
    public
    function calculateFailedIfHourType($qa, $financeTransactionsByCriteria, $plan, $planResult)
    {
        foreach ($financeTransactionsByCriteria as $financeTransactionByCriteria) {

            $orderId = $financeTransactionByCriteria->order_id;
            $new_prices = json_decode($plan->new_prices, true);
            $data = [];
            $data['plan_id'] = $plan->id;
            $data['user_id'] = (!empty($financeTransactionByCriteria->user_id) ? $financeTransactionByCriteria->user_id : 0);

            $data['time_system_crm'] = (!empty($financeTransactionByCriteria->time_system_crm) ? $financeTransactionByCriteria->time_system_crm : 0);
            $data['time_talk'] = (!empty($financeTransactionByCriteria->time_talk) ? $financeTransactionByCriteria->time_talk : 0);
            $data['time_system_pbx'] = (!empty($financeTransactionByCriteria->time_system_pbx) ? $financeTransactionByCriteria->time_system_pbx : 0);

            $data['type'] = self::setTransactionEntity($plan, $data);

            /* грн:сек(за сек) * грн = надбавка */
            $data['result'] = (isset($new_prices['in_system_retention']) ?
                    $new_prices['in_system_retention'] / 3600 * $financeTransactionByCriteria->time_system_crm : 0) +
                (isset($new_prices['in_talk_retention']) ? $new_prices['in_talk_retention'] / 3600 *
                    $financeTransactionByCriteria->time_talk : 0);
            $data['result'] = round($data['result'], 2);
            if ($data['result'] !== 0 && $data['result'] !== NULL && !empty($data['result'])) {
                if (!empty($qa)) {
                    (new Transaction())->createOrUpdateTransaction($orderId, $type = 'bonus', $data, $test = 'test');
                } else {
                    (new Transaction())->createOrUpdateTransaction($orderId, $type = 'bonus', $data, $test = '');
                }
            }
            $data['result_type'] = 'failed';
            /*Ивент который отслеживает логи на новую транзакцию*/
            event(new ResultTransactionEvent($financeTransactionByCriteria, $planResult['log'], $data));
        }
        return array($financeTransactionByCriteria, $data);
    }

    /**
     * @param $plan
     * @return array
     */
    public
    static function searchByTransactionType($plan)
    {
        $searchByCompanyEntity = ($plan->type_object == 'company') ? "AND t.entity = 'company'" : '';
        $searchByUserEntity = ($plan->type_object == 'operator') ? "AND t.entity = 'user'" : '';
        return array($searchByCompanyEntity, $searchByUserEntity);
    }

    /**
     * @param $plan
     * @return mixed
     */
    public
    static function getProductTypeAction($plan)
    {
        $planBasis = unserialize($plan->product_type_action);
        return $planBasis;
    }

    /**
     * @param $plan
     * @param $data
     * @param $financeTransactionByCriteria
     * @param $planResult
     * @param $testLogArray
     * @return $testLog
     */
    public function createTestLogObject($plan, $data, $financeTransactionByCriteria, $planResult, $testLogArray)
    {
        $testLog = new \App\Models\PlanLog();
        $testLog->created_at = $plan->created_at;
        $testLog->type = $data['result_type'];
        $testLog->plan_id = $plan->id;
        $testLog->transaction_id = $financeTransactionByCriteria->id;
        $testLog->company_id = $financeTransactionByCriteria->company_id;
        $testLog->user_id = !empty($financeTransactionByCriteria->user_id) ? $financeTransactionByCriteria->user_id : 0;
        $testLog->result = $data['result'];
        $testLog->text = $planResult['log'];
        $testLog->qa = !empty($financeTransactionByCriteria->qa) ? $financeTransactionByCriteria->qa : NULL;
        return $testLog;
    }
}
