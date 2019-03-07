<?php

namespace App\Models;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\CreatePlanRequest;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class Plan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Получить компанию к плану.
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }

    /**
     * Получить логи к плану.
     */
    public function log()
    {
        return $this->hasMany('App\Models\PlanLog');
    }

    /**
     * Получить транзакции к плану.
     */
    public function transaction()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    /*создание нового плана*/

    public static function createPlan($data)
    {
        $plan = new Plan;
        $plan->name = $data['name'];
        $plan->status = 'inactive';
        $plan->company_id = $data['company_id'];
        $plan->type_object = $data['type-object'];
        $plan->type_method = !empty($data["type-method"]) ? $data["type-method"] : 'schedule';

        if (!empty($data['basis-for-action'])) {
            $plan->basis_for_calculation = $data['basis-for-action'];
        }
        if (!empty($data['basis-for-schedule'])) {
            $plan->basis_for_calculation = $data['basis-for-schedule'];
        }

        if (!empty($data['product-type-action'])) {
            $plan->product_type_action = serialize($data['product-type-action']);
        }

        if (!empty($data['sum-percent-product-type-action'])) {
            $plan->product_type_action = serialize($data['sum-percent-product-type-action']);
        }

        $plan->new_prices = json_encode([
            'approve-bonus' => !empty($data['approve-bonus']) ? $data['approve-bonus'] : NULL,
            'up_sell-bonus' => !empty($data['up_sell-bonus']) ? $data['up_sell-bonus'] : NULL,
            'up_sell_2-bonus' => !empty($data['up_sell_2-bonus']) ? $data['up_sell_2-bonus'] : NULL,
            'cross_sell-bonus' => !empty($data['cross_sell-bonus']) ? $data['cross_sell-bonus'] : NULL,

            'fixed-bonus' => !empty($data['fixed-bonus']) ? $data['fixed-bonus'] : NULL,

            'approve-retention' => !empty($data['approve-retention']) ? $data['approve-bonus'] : NULL,
            'up_sell-retention' => !empty($data['up_sell-retention']) ? $data['up_sell-retention'] : NULL,
            'up_sell_2-retention' => !empty($data['up_sell_2-retention']) ? $data['up_sell_2-retention'] : NULL,
            'cross_sell-retention' => !empty($data['cross_sell-retention']) ? $data['cross_sell-retention'] : NULL,

            'fixed-retention' => !empty($data['fixed-retention']) ? $data['fixed-retention'] : NULL,

            /*if company type = hour*/
            'in_system_bonus' => !empty($data['in_system-bonus']) ? $data['in_system-bonus'] : NULL,
            'in_talk_bonus' => !empty($data['in_talk-bonus']) ? $data['in_talk-bonus'] : NULL,
            'in_system_retention' => !empty($data['in_system-retention']) ? $data['in_system-retention'] : NULL,
            'in_talk_retention' => !empty($data['in_talk-retention']) ? $data['in_talk-retention'] : NULL,
        ]);

        $plan->criteria = json_encode([
            /*доступные критерии отбора*/
            "operator_group" => !empty($data['user-group-select2']) ? $data['user-group-select2'] : NULL,
            "operator" => !empty($data['user-select2']) ? $data['user-select2'] : NULL,
            "country" => !empty($data['country-select2']) ? $data['country-select2'] : NULL,
            "offers" => !empty($data['offers-select2']) ? $data['offers-select2'] : NULL,
            "products" => !empty($data['products-select2']) ? $data['products-select2'] : NULL,
            "product_type" => !empty($data['product-type']) ? serialize($data['product-type']) : NULL,
            /*доступные исключения*/
            "operator_group_except" => !empty($data['user-group-select2-except']) ? $data['user-group-select2-except'] : NULL,
            "operator_except" => !empty($data['user-select2-except']) ? $data['user-select2-except'] : NULL,
            "country_except" => !empty($data['country-select2-except']) ? $data['country-select2-except'] : NULL,
            "offers_except" => !empty($data['offers-select2-except']) ? $data['offers-select2-except'] : NULL,
            "products_except" => !empty($data['products-select2-except']) ? $data['products-select2-except'] : NULL,
            "product_type_except" => !empty($data['product-type-except']) ? serialize($data['product-type-except']) : NULL,
        ]);

        if (!empty($data['interval'])) {
            $plan->interval = $data['interval'];
        }
        /*оператор сравнения суммы, количества или процента*/
        if (!empty($data['action-sum-operator'])) {
            $plan->compare_operator = json_encode($data['action-sum-operator']);
        }
        if (!empty($data['schedule-quantity-operator'])) {
            $plan->compare_operator = json_encode($data['schedule-quantity-operator']);
        }
        if (!empty($data['schedule-percent-operator'])) {
            $plan->compare_operator = json_encode($data['schedule-percent-operator']);
        }
        if (!empty($data['action-quantity-operator'])) {
            $plan->compare_operator = json_encode($data['action-quantity-operator']);
        }

        if (!empty($data['schedule-quantity-value']) || !empty($data['schedule-percent-value'])) {
            $plan->success_plan = !empty($data['schedule-quantity-value']) ? $data['schedule-quantity-value'] : $data['schedule-percent-value'];
        }
        if (!empty($data['action-quantity-value']) || !empty($data['action-sum-value'])) {
            $plan->success_plan = !empty($data['action-quantity-value']) ? $data['action-quantity-value'] : $data['action-sum-value'];
        }
        if (!empty($data['schedule-quantity-operator']) || !empty($data['schedule-quantity-operator'])) {
            $plan->compare_operator = !empty($data['schedule-quantity-operator']) ? $data['schedule-quantity-operator'] : $data['schedule-percent-operator'];
        }
        if (!empty($data['action-quantity-operator']) || !empty($data['action-sum-operator'])) {
            $plan->compare_operator = !empty($data['action-quantity-operator']) ? $data['action-quantity-operator'] : $data['action-sum-operator'];
        }

        if (!empty($data['comment'])) {
            $plan->comment = $data['comment'];
        }
        $result['success'] = $plan->save();
        return $result;
    }

    public static function edit($id, $data)
    {
        $plan = Plan::where('id', $id)->first();
        $plan->name = $data['name'];
        $plan->status = 'inactive';
        $plan->company_id = $data['company_id'];
        $plan->type_object = $data['type-object'];
        $plan->type_method = !empty($data["type-method"]) ? $data["type-method"] : 'schedule';

        if (!empty($data['basis-for-action'])) {
            $plan->basis_for_calculation = $data['basis-for-action'];
        }
        if (!empty($data['basis-for-schedule'])) {
            $plan->basis_for_calculation = $data['basis-for-schedule'];
        }

        if (!empty($data['product-type-action'])) {
            $plan->product_type_action = serialize($data['product-type-action']);
        }

        if (!empty($data['sum-percent-product-type-action'])) {
            $plan->product_type_action = serialize($data['sum-percent-product-type-action']);
        }

        $plan->new_prices = json_encode([
            'approve-bonus' => !empty($data['approve-bonus']) ? $data['approve-bonus'] : NULL,
            'up_sell-bonus' => !empty($data['up_sell-bonus']) ? $data['up_sell-bonus'] : NULL,
            'up_sell_2-bonus' => !empty($data['up_sell_2-bonus']) ? $data['up_sell_2-bonus'] : NULL,
            'cross_sell-bonus' => !empty($data['cross_sell-bonus']) ? $data['cross_sell-bonus'] : NULL,

            'fixed-bonus' => !empty($data['fixed-bonus']) ? $data['fixed-bonus'] : NULL,

            'approve-retention' => !empty($data['approve-retention']) ? $data['approve-bonus'] : NULL,
            'up_sell-retention' => !empty($data['up_sell-retention']) ? $data['up_sell-retention'] : NULL,
            'up_sell_2-retention' => !empty($data['up_sell_2-retention']) ? $data['up_sell_2-retention'] : NULL,
            'cross_sell-retention' => !empty($data['cross_sell-retention']) ? $data['cross_sell-retention'] : NULL,

            'fixed-retention' => !empty($data['fixed-retention']) ? $data['fixed-retention'] : NULL,

            /*if company type = hour*/
            'in_system_bonus' => !empty($data['in_system-bonus']) ? $data['in_system-bonus'] : NULL,
            'in_talk_bonus' => !empty($data['in_talk-bonus']) ? $data['in_talk-bonus'] : NULL,
            'in_system_retention' => !empty($data['in_system-retention']) ? $data['in_system-retention'] : NULL,
            'in_talk_retention' => !empty($data['in_talk-retention']) ? $data['in_talk-retention'] : NULL,
        ]);

        $plan->criteria = json_encode([
            /*доступные критерии отбора*/
            "operator_group" => !empty($data['user-group-select2']) ? $data['user-group-select2'] : NULL,
            "operator" => !empty($data['user-select2']) ? $data['user-select2'] : NULL,
            "country" => !empty($data['country-select2']) ? $data['country-select2'] : NULL,
            "offers" => !empty($data['offers-select2']) ? $data['offers-select2'] : NULL,
            "products" => !empty($data['products-select2']) ? $data['products-select2'] : NULL,
            "product_type" => !empty($data['product-type']) ? $data['product-type'] : NULL,
            /*доступные исключения*/
            "operator_group_except" => !empty($data['user-group-select2-except']) ? $data['user-group-select2-except'] : NULL,
            "operator_except" => !empty($data['user-select2-except']) ? $data['user-select2-except'] : NULL,
            "country_except" => !empty($data['country-select2-except']) ? $data['country-select2-except'] : NULL,
            "offers_except" => !empty($data['offers-select2-except']) ? $data['offers-select2-except'] : NULL,
            "products_except" => !empty($data['products-select2-except']) ? $data['products-select2-except'] : NULL,
            "product_type_except" => !empty($data['product-type-except']) ? $data['product-type-except'] : NULL,
        ]);

        if (!empty($data['interval'])) {
            $plan->interval = $data['interval'];
        }
        /*оператор сравнения суммы, количества или процента*/
        if (!empty($data['action-sum-operator'])) {
            $plan->compare_operator = json_encode($data['action-sum-operator']);
        }
        if (!empty($data['schedule-quantity-operator'])) {
            $plan->compare_operator = json_encode($data['schedule-quantity-operator']);
        }
        if (!empty($data['schedule-percent-operator'])) {
            $plan->compare_operator = json_encode($data['schedule-percent-operator']);
        }
        if (!empty($data['action-quantity-operator'])) {
            $plan->compare_operator = json_encode($data['action-quantity-operator']);
        }

        if (!empty($data['schedule-quantity-value']) || !empty($data['schedule-percent-value'])) {
            $plan->success_plan = !empty($data['schedule-quantity-value']) ? $data['schedule-quantity-value'] : $data['schedule-percent-value'];
        }
        if (!empty($data['action-quantity-value']) || !empty($data['action-sum-value'])) {
            $plan->success_plan = !empty($data['action-quantity-value']) ? $data['action-quantity-value'] : $data['action-sum-value'];
        }
        if (!empty($data['schedule-quantity-operator']) || !empty($data['schedule-quantity-operator'])) {
            $plan->compare_operator = !empty($data['schedule-quantity-operator']) ? $data['schedule-quantity-operator'] : $data['schedule-percent-operator'];
        }
        if (!empty($data['action-quantity-operator']) || !empty($data['action-sum-operator'])) {
            $plan->compare_operator = !empty($data['action-quantity-operator']) ? $data['action-quantity-operator'] : $data['action-sum-operator'];
        }


        if (!empty($data['comment'])) {
            $plan->comment = $data['comment'];
        }
        $result['success'] = $plan->save();
        return $result;
    }

    public static function sendForTest($id, $data)
    {

        (new Transaction())->createOrUpdateTransaction($data['order_id'], $type = 'approve', $results = '', $qa = 'test');
        return;
    }

    public static function sendForTestIfSchedule($period, $qa)
    {

        $result = (new Result())->calculateDueToSchedule($period, $qa);

        return $result;
    }
}
