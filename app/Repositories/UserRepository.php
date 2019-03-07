<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\Permission;
use App\Models\ProcStatus;
use \App\Models\User;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public static function operatorsSortBySurname()
    {
        return DB::table('users')->orderBy('surname')->get();
    }

    /*get users by company*/
    public static function getUsersByCompanyId( $companyId )
    {
        return User::where([['company_id', $companyId], ['role', 1]])->get();
    }

    /**/
    public static function findByRoleId( $term, $rolesIds )
    {
        $users = DB::table('users')
            ->select('id', 'surname', 'name')
            ->where(function ( $query ) use ( $term, $rolesIds ) {
                $query->where('name', 'LIKE', '%' . $term . '%')
                    ->whereIn('role_id', $rolesIds);
            })
            ->orWhere(function ( $query ) use ( $term, $rolesIds ) {
                $query->where('surname', 'LIKE', '%' . $term . '%')
                    ->whereIn('role_id', $rolesIds);
            })
            ->get();
        return $users;
    }

    public static function getWhereInArray( $array )
    {
        $users = User::select(['id', 'name', 'surname']);
        $users = $users->whereIn('id', $array);
        return $users->get();
    }

    public static function getModeratorsStatistics( $filter )
    {
        $filter['date_start'] = $filter['date_start'] ? Carbon::parse($filter['date_start']) : Carbon::today();
        $filter['date_end'] = $filter['date_end'] ? Carbon::parse($filter['date_end'])->endOfDay() : Carbon::today()->endOfDay();

        $moderatorsStatistics = Order::select(
        // DB::raw('count(CASE WHEN orders.moderation_id > 0 THEN 1 END) as ordersCount'),
            DB::raw('count(CASE WHEN orders.target_status = 1 AND orders.moderation_id > 0 THEN 1 END) AS approve'),
            DB::raw('count(CASE WHEN orders.target_status = 2 AND orders.moderation_id > 0   THEN 1 END) AS cancel'),
            DB::raw('count(CASE WHEN orders.target_status = 3 AND orders.moderation_id > 0   THEN 1 END) AS refused'),
            // DB::raw('count(CASE WHEN orders.pre_moderation_uid > 0 THEN 1 END) AS preModeration'),
            DB::raw('count(CASE WHEN orders.pre_moderation_uid > 0 AND orders.pre_moderation_type = 4 THEN 1 END) AS repeated'),
            DB::raw('count(CASE WHEN orders.pre_moderation_uid > 0 AND orders.pre_moderation_type = 5 THEN 1 END) AS notCall'),
            DB::raw('count(CASE WHEN orders.pre_moderation_uid > 0 AND orders.pre_moderation_type = 6 THEN 1 END) AS notData'),
            DB::raw('count(CASE WHEN orders.pre_moderation_uid > 0 AND orders.pre_moderation_type = 7 THEN 1 END) AS otherLanguage'),
            DB::raw('count(CASE WHEN orders.pre_moderation_uid > 0 AND orders.pre_moderation_type = 11 THEN 1 END) AS incorrectProject'),
            'u.name as uName', 'u.surname as uSurname', 'u.id as uId', 'us.name as usName', 'us.surname as usSurname', 'us.id as usId')
            ->leftJoin('users as u', 'u.id', '=', 'orders.moderation_id')
            ->leftJoin('users as us', 'us.id', '=', 'orders.pre_moderation_uid')
            ->where('service', '!=', 'sending')->where(function ( $query ) {
                $query->where('moderation_id', '>', 0)
                    ->orWhere('pre_moderation_uid', '>', 0);
            });

        if ($filter['country']) {
            $country = explode(',', $filter['country']);
            $moderatorsStatistics = $moderatorsStatistics->whereIn('geo', $country);
        }
        if ($filter['project']) {
            $project = explode(',', $filter['project']);
            $moderatorsStatistics = $moderatorsStatistics->whereIn('orders.project_id', $project);
        }
        if ($filter['sub_project']) {
            $subProject = explode(',', $filter['sub_project']);
            $moderatorsStatistics = $moderatorsStatistics->whereIn('orders.subproject_id', $subProject);
        }

        if ($filter['moderator']) {
            $moderators = is_string($filter['moderator']) ? explode(',', $filter['moderator']) : [$filter['moderator']];
            $moderatorsStatistics = $moderatorsStatistics->whereIn('orders.moderation_id', $moderators);
        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $moderatorsStatistics = $moderatorsStatistics->where(
                function ( $query ) use ( $filter ) {
                    $query->whereBetween('orders.moderation_time', [
                        $filter['date_start'],
                        $filter['date_end']
                    ]);
                    $query->orWhereBetween('orders.pre_moderation_time', [
                        $filter['date_start'],
                        $filter['date_end']
                    ]);
                }
            );
        }

        $moderatorsStatistics = $moderatorsStatistics->groupBy('moderation_id', 'pre_moderation_uid')->get();
        $statisticData = [];
        foreach ($moderatorsStatistics as $moderatorsStatistic) {
            if ($moderatorsStatistic->uId != NULL && array_key_exists($moderatorsStatistic->uId, $statisticData)) {
                $statisticData[$moderatorsStatistic->uId]->approve += intval($moderatorsStatistic->approve);
                $statisticData[$moderatorsStatistic->uId]->cancel += intval($moderatorsStatistic->cancel);
                $statisticData[$moderatorsStatistic->uId]->refused += intval($moderatorsStatistic->refused);
            } else {
                $statisticData[$moderatorsStatistic->uId] = $moderatorsStatistic;
            }
            if ($moderatorsStatistic->usId != NULL && array_key_exists($moderatorsStatistic->usId, $statisticData)) {

                $statisticData[$moderatorsStatistic->usId]->repeated += intval($moderatorsStatistic->repeated);
                $statisticData[$moderatorsStatistic->usId]->notCall += intval($moderatorsStatistic->notCall);
                $statisticData[$moderatorsStatistic->usId]->notData += intval($moderatorsStatistic->notData);
                $statisticData[$moderatorsStatistic->usId]->otherLanguage += intval($moderatorsStatistic->otherLanguage);
                $statisticData[$moderatorsStatistic->usId]->incorrectProject += intval($moderatorsStatistic->incorrectProject);
            } else {
                $statisticData[$moderatorsStatistic->usId] = $moderatorsStatistic;
            }
        }
        //  dd($statisticData);
        return $statisticData;
    }

    public static function getOperatorsStatistics( $filter, $operators )
    {

        $filter['date_type'] = 'time_modified';
        if (!$filter['date_start'] || !$filter['date_end']) {
            $filter['date_start'] = Carbon::parse('now 00:00:00');
            $filter['date_end'] = Carbon::parse('now 23:59:59');
        } else {
            $filter['date_start'] = Carbon::parse($filter['date_start'] . ' 00:00:00');
            $filter['date_end'] = Carbon::parse($filter['date_end'])->endOfDay();
        }

        $operatorStatistics = Order::with('orderOpened', 'targetUser')
            ->where('target_status', 1)
            ->where('service', '!=', Order::SERVICE_SENDING);

        if ($filter['company']) {
            $operatorsStatistics = $operatorStatistics
                ->whereHas('targetUser', function ( $q ) use ( $filter ) {
                    $q->where('company_id', $filter['company']);
                });
        }
        if ($filter['operator']) {
            $operator = is_array($filter['operator']) ? $filter['operator'] : explode(',', $filter['operator']);
            $operatorsStatistics = $operatorStatistics
                ->whereHas('targetUser', function ( $q ) use ( $operator ) {
                    $q->whereIn('id', $operator);
                });
        }

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $operatorsStatistics = $operatorStatistics->where(
                function ( $query ) use ( $filter ) {
                    $query->whereBetween('orders.' . $filter['date_type'], [
                        $filter['date_start'],
                        $filter['date_end']
                    ]);
                }
            );
        }
        $data = $operatorsStatistics->get();

        $ordersLog = OrdersLog::whereIn('user_id', array_keys($operators->toArray()))
            ->where('text', 'like', '%Цель - Подтвержден%')
            ->whereHas('order', function ( $query ) {
                $query->where('target_status', 1)
                    ->where('service', '!=', Order::SERVICE_SENDING);
            });

        if ($filter['date_start'] && $filter['date_end'] && ($filter['date_start'] <= $filter['date_end'])) {
            $ordersLog = $ordersLog->where(
                function ( $query ) use ( $filter ) {
                    $query->whereBetween('date', [
                        $filter['date_start'],
                        $filter['date_end']
                    ]);
                }
            );
        }
        $ordersLog = $ordersLog->get()->keyBy('order_id');
        $ordersIds = [];
        $dataByOper = [];
        foreach ($data as $raw) {
            $dataByOper[$raw->target_user][$raw->id] = $raw;
            $ordersIds[] = $raw->id;

        }
        $orders = Order::with('orderOpened', 'targetUser')
            ->where('target_status', 1)
            ->where('service', '!=', Order::SERVICE_SENDING)
            ->where(
                function ( $query ) use ( $filter ) {
                    $query->whereBetween('orders.' . $filter['date_type'], [
                        $filter['date_start'],
                        $filter['date_end']
                    ]);
                }
            );

        $orders = $orders->get()->keyBy('id');

        foreach ($ordersLog as $raw) {
            if (!empty($dataByOper[$raw->user_id]) && !in_array($raw->order_id, $ordersIds) && isset($orders[$raw->order_id])) {
                array_push($dataByOper[$raw->user_id], $orders[$raw->order_id]);
            }
        }
        return $dataByOper;
    }
}