<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use \App\Models\User;


class Company extends BaseModel
{
    protected $table = 'companies';

    public $timestamps = false;

    /**
     * Получить родительскую запись плана.
     */
    public function plan()
    {
        return $this->hasOne('App\Models\Plan');
    }
    /**
     * Получить логи по компании
     */
    public function resultLogs()
    {
        return $this->hasMany('App\Models\PlanLog');
    }

    /**
     * Получить листы холодных продаж
     */
    public function colsCallList()
    {
        return $this->hasMany('App\Models\ColdCallList');
    }

    /**
     * Получить оффер
     */
    public function offer()
    {
        return $this->hasOne('App\Models\Offer');
    }
    /**
     * get user by company
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * get ordersopened by company
     */
    public function ordersOpened()
    {
        return $this->hasManyThrough('App\Models\OrdersOpened', '\App\Models\User');
    }

    public function feedbacks(){
        return $this->hasManyThrough( Feedback::class,User::class);
    }

    public function campaign(){
        return $this->hasOne('App\Models\Campaign');
    }

    public function getCompanies()
    {
        $result = DB::table($this->table)
            ->where('id', '>', 0);
        if (auth()->user()->company_id) {
            $result = $result->where('id', auth()->user()->company_id);
        }
        return $result->get();
    }

    public function getDefaultPrices()
    {
        $prices = DB::table($this->table)
            ->where('id', 0)
            ->value('prices');
        return json_decode($prices);
    }

    public function addNewClientCompany($data)
    {
        $insert = [
            'name'      => $data['name'],
            'billing'   => '',
            'type'      => $data['type'],
            'prices'     => '',
            'billing_type'  => '',
        ];
        $prices['global'] = [];
        $billing['global'] = [];
        if ($data['type'] == 'lead') {
            $prices['global'] = [
                'type'          => $data['type'],
                'approve'       => $data['global']['approve'],
                'up_sell'       => $data['global']['up-sell'],
                'up_sell_2'     => $data['global']['up-sell-2'],
                'cross_sell'    => $data['global']['cross-sell'],
                'cross_sell_2'  => $data['global']['cross-sell-2'],
            ];
        } elseif ($data['type'] == 'hour') {
            $prices['global'] = [
                'type'      => $data['type'],
                'in_system' => $data['global']['in-system'],
                'in_talk'   => $data['global']['in-talk']
            ];
        } elseif ($data['type'] == 'month' || $data['type'] == 'week') {
            $prices['global'] = [
                'type' => $data['type'],
                'rate' => $data['global']['rate'],
            ];
        }

        if (isset($data['ranks'])) {
            $prices['ranks'] = [];
            foreach ($data['ranks'] as $rank) {
                if ($rank['rank-type'] == 'lead') {
                    $prices['ranks'][$rank['rank']] = [
                        'type'          => $rank['rank-type'],
                        'approve'       => $rank['approve'],
                        'up_sell'       => $rank['up-sell'],
                        'up_sell_2'     => $rank['up-sell-2'],
                        'cross_sell'    => $rank['cross-sell'],
                        'cross_sell_2'  => $rank['cross-sell-2'],
                    ];
                } elseif ($rank['rank-type'] == 'hour') {
                    $prices['ranks'][$rank['rank']] = [
                        'type'      => $rank['rank-type'],
                        'in_system' => $rank['in-system'],
                        'in_talk'   => $rank['in-talk']
                    ];
                } elseif ($rank['rank-type'] == 'month' || $rank['rank-type'] == 'week') {
                    $prices['ranks'][$rank['rank']] = [
                        'type' => $rank['rank-type'],
                        'rate' => $rank['rate'],
                    ];
                }
            }
        }

        if (isset($data['billing'])) {
            if ($data['global']['type-billing'] == 'billing_lead') {
                $billing['global'] = [
                    'type'                  => 'lead',
                    'approve'       => $data['global']['billing-approve'],
                    'up_sell'       => $data['global']['billing-up-sell'],
                    'up_sell_2'     => $data['global']['billing-up-sell-2'],
                    'cross_sell'    => $data['global']['billing-cross-sell'],
                    'cross_sell_2'  => $data['global']['billing-cross-sell-2'],
                ];
                $insert['billing_type'] = 'lead';
            } elseif ($data['global']['type-billing'] == 'billing_hour') {
                $billing['global'] = [
                    'type'  => 'hour',
                    'in_system' => $data['global']['billing-in-system'],
                    'in_talk' => $data['global']['billing-in-talk'],
                ];
                $insert['billing_type'] = 'hour';
            } elseif ($data['global']['type-billing'] == 'billing_month' || $data['global']['type-billing'] == 'billing_week') {
                $billing['global'] = [
                    'type' => $data['global']['type-billing'] == 'billing_month' ? 'month' : 'week',
                    'rate' => $data['global']['billing-rate'],
                ];
                $insert['billing_type'] = $data['global']['type-billing'] == 'billing_month' ? 'month' : 'week';
            }
        }

        if (isset($data['ranks_billing'])) {
            $billing['ranks'] = [];
            foreach ($data['ranks_billing'] as $rank) {
                if ($rank['rank-type'] == 'lead') {
                    $billing['ranks'][$rank['rank']] = [
                        'type'          => $rank['rank-type'],
                        'approve'       => $rank['approve'],
                        'up_sell'       => $rank['up-sell'],
                        'up_sell_2'     => $rank['up-sell-2'],
                        'cross_sell'    => $rank['cross-sell'],
                        'cross_sell_2'  => $rank['cross-sell-2'],
                    ];
                } elseif ($rank['rank-type'] == 'hour') {
                    $billing['ranks'][$rank['rank']] = [
                        'type'      => $rank['rank-type'],
                        'in_system' => $rank['in-system'],
                        'in_talk'   => $rank['in-talk']
                    ];
                } elseif ($rank['rank-type'] == 'month' || $rank['rank-type'] == 'week') {
                    $billing['ranks'][$rank['rank']] = [
                        'type' => $rank['rank-type'],
                        'rate' => $rank['rate'],
                    ];
                }
            }
        }

        if ($billing['global']) {
            $insert['billing'] = json_encode($billing);
        }

        $insert['prices'] = json_encode($prices);

        $result['success'] = DB::table($this->table)->insert($insert);
        return $result;
    }

    public function getOneCompany($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }

    public function changeCompany($id, $data)
    {
        $billing['global'] = [];
        if (auth()->user()->company_id) {
            $insert = [
                'billing'   => '',
                'billing_type'  => '',
            ];
        } else {
            $insert = [
                'name'          => $data['name'],
                'type'          => $data['type'],
                'prices'        => '',
                'billing'       => '',
                'billing_type'  => '',
            ];
            $prices['global'] = [];
            if ($data['type'] == 'lead') {
                $prices['global'] = [
                    'type'          => $data['type'],
                    'approve'       => $data['global']['approve'],
                    'up_sell'       => $data['global']['up-sell'],
                    'up_sell_2'     => $data['global']['up-sell-2'],
                    'cross_sell'    => $data['global']['cross-sell'],
                    'cross_sell_2'  => $data['global']['cross-sell-2'],
                ];
            } elseif ($data['type'] == 'hour') {
                $prices['global'] = [
                    'type'      => $data['type'],
                    'in_system' => $data['global']['in-system'],
                    'in_talk'   => $data['global']['in-talk']
                ];
            } elseif ($data['type'] == 'month' || $data['type'] == 'week') {
                $prices['global'] = [
                    'type' => $data['type'],
                    'rate' => $data['global']['rate'],
                ];
            }

            if (isset($data['ranks'])) {
                $prices['ranks'] = [];
                foreach ($data['ranks'] as $rank) {
                    if ($rank['rank-type'] == 'lead') {
                        $prices['ranks'][$rank['rank']] = [
                            'type'          => $rank['rank-type'],
                            'approve'       => $rank['approve'],
                            'up_sell'       => $rank['up-sell'],
                            'up_sell_2'     => $rank['up-sell-2'],
                            'cross_sell'    => $rank['cross-sell'],
                            'cross_sell_2'  => $rank['cross-sell-2'],
                        ];
                    } elseif ($rank['rank-type'] == 'hour') {
                        $prices['ranks'][$rank['rank']] = [
                            'type'      => $rank['rank-type'],
                            'in_system' => $rank['in-system'],
                            'in_talk'   => $rank['in-talk']
                        ];
                    } elseif ($rank['rank-type'] == 'month' || $rank['rank-type'] == 'week') {
                        $prices['ranks'][$rank['rank']] = [
                            'type' => $rank['rank-type'],
                            'rate' => $rank['rate'],
                        ];
                    }
                }
            }

            $insert['prices'] = json_encode($prices);
        }

        if (isset($data['billing'])) {
            if ($data['global']['type-billing'] == 'billing_lead') {
                $billing['global'] = [
                    'type'                  => 'lead',
                    'approve'       => $data['global']['billing-approve'],
                    'up_sell'       => $data['global']['billing-up-sell'],
                    'up_sell_2'     => $data['global']['billing-up-sell-2'],
                    'cross_sell'    => $data['global']['billing-cross-sell'],
                    'cross_sell_2'  => $data['global']['billing-cross-sell-2'],
                ];
                $insert['billing_type'] = 'lead';
            } elseif ($data['global']['type-billing'] == 'billing_hour') {
                $billing['global'] = [
                    'type'  => 'hour',
                    'in_system' => $data['global']['billing-in-system'],
                    'in_talk' => $data['global']['billing-in-talk'],
                ];
                $insert['billing_type'] = 'hour';
            } elseif ($data['global']['type-billing'] == 'billing_month' || $data['global']['type-billing'] == 'billing_week') {
                $billing['global'] = [
                    'type' => $data['global']['type-billing'] == 'billing_month' ? 'month' : 'week',
                    'rate' => $data['global']['billing-rate'],
                ];
                $insert['billing_type'] = $data['global']['type-billing'] == 'billing_month' ? 'month' : 'week';
            }
        }

        if (isset($data['ranks_billing'])) {
            $billing['ranks'] = [];
            foreach ($data['ranks_billing'] as $rank) {
                if ($rank['rank-type'] == 'lead') {
                    $billing['ranks'][$rank['rank']] = [
                        'type'          => $rank['rank-type'],
                        'approve'       => $rank['approve'],
                        'up_sell'       => $rank['up-sell'],
                        'up_sell_2'     => $rank['up-sell-2'],
                        'cross_sell'    => $rank['cross-sell'],
                        'cross_sell_2'  => $rank['cross-sell-2'],
                    ];
                } elseif ($rank['rank-type'] == 'hour') {
                    $billing['ranks'][$rank['rank']] = [
                        'type'      => $rank['rank-type'],
                        'in_system' => $rank['in-system'],
                        'in_talk'   => $rank['in-talk']
                    ];
                } elseif ($rank['rank-type'] == 'month' || $rank['rank-type'] == 'week') {
                    $billing['ranks'][$rank['rank']] = [
                        'type' => $rank['rank-type'],
                        'rate' => $rank['rate'],
                    ];
                }
            }
        }

        if ($billing['global']) {
            $insert['billing'] = json_encode($billing);
        }

        $result['success'] = DB::table($this->table)
            ->where('id', $id)
            ->update($insert);
        return $result;
    }

    public function searchCompanyByWord($term, $rolesIds)
    {
        if ($rolesIds) {
            $companies = DB::table('companies AS c')
                ->select(DB::raw('DISTINCT(c.id) AS id'), 'c.name')
                ->leftJoin('users AS u', 'u.company_id', '=', 'c.id')
                ->leftJoin('role AS r', 'r.id', '=', 'u.role_id')
                ->where('c.name', 'LIKE', '%' . $term . '%')
                ->whereIn('r.id', $rolesIds)
                ->get();
        } else {
            $companies = DB::table('companies')
                ->select('id', 'name')
                ->where('name', 'LIKE', '%' . $term . '%')->get();
        }
        return $companies;
    }

}