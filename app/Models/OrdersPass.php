<?php

namespace App\Models;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersPass extends Model
{
    protected $table = 'orders_passes';
    protected $fillable = ['order_id', 'pass_id', 'track', 'cost_actual', 'cost_return'];

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function getFinanceSubProject($filter)
    {
        $subProject = [];
        $result = [];

        $types = [
            Pass::TYPE_REDEMPTION,
            Pass::TYPE_NO_REDEMPTION,
            Pass::TYPE_SENDING
        ];
        foreach ($types as $type) {
            $data = Order::getStatisticsOrder($filter, $type);
            if ($data->count()) {
                foreach ($data as $subProjectId => $stat) {
                    if (!isset($subProject[$subProjectId])) {
                        $subProject[$subProjectId] = collect();
                    }
                    $subProject[$subProjectId][$type] = $stat;
                }
            }
        }

        if ($subProject) {
            foreach ($subProject as $id => $values) {
                $result[$id] = [
                    'sub_project_id' => $id,
                    'orders'         => 0,
                    'price_total'    => 0,
                    'price_products' => 0,
                    'cost'           => 0,
                    'cost_actual'    => 0,
                    'cost_return'    => 0,
                    'income'         => 0,
                ];

                foreach ($values as $type => $value) {
                    $result[$id]['orders'] += $value->orders;
                    $result[$id]['price_total'] += $value->price_total;
                    $result[$id]['price_products'] += $value->price_products;
                    $result[$id]['cost'] += $value->cost;
                    $result[$id]['cost_actual'] += $value->cost_actual;
                    $result[$id]['cost_return'] += $value->cost_return;
                    $result[$id]['passes'][$type] = [
                        'orders'         => $value->orders,
                        'price_total'    => $value->price_total,
                        'price_products' => $value->price_products,
                        'cost'           => $value->cost,
                        'cost_actual'    => $value->cost_actual,
                        'cost_return'    => $value->cost_return,
                        'income'         => self::getIncome($type, $value),
                    ];
                    $result[$id]['income'] += self::getIncome($type, $value);
                }

            }
        }
        return $result;
    }

    protected static function getIncome($type, $value)
    {
        $result = 0;
        switch ($type) {
            case Pass::TYPE_REDEMPTION : {
                $result += $value->price_total + $value->cost;
                break;
            }
            case Pass::TYPE_NO_REDEMPTION : {
                $result -= $value->cost_return;
                break;
            }
            case Pass::TYPE_SENDING : {
                $result -= $value->cost_actual;
                break;
            }
        }
        return $result;
    }
}