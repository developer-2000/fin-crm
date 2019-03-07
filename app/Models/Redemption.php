<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Redemption extends Model
{
    protected $fillable = ['product_id', 'subproject_id', 'start_month', 'end_month', 'percent', 'approve', 'redemption_quantity'];

    public static function calculatePercent()
    {
        //actual period
        $startMonth = Carbon::now()->subMonth(3)->format('Y-m');
        $endMonth = Carbon::now()->subMonth(1)->format('Y-m');
        //test dates;
        $startMonth = '2018-07';
        $endMonth = '2018-08';
        $products = DB::table('products AS p')
            ->select('p.id', 'p.project_id', 'o.subproject_id',
                DB::raw('  case when o.final_target = 1 then count(distinct o.id ) else 0 end as paidUpOrdersCount'),//correct +
                DB::raw('  case when o.final_target = 1 then count(distinct op.id ) else 0 end as paidUpProductsCount'),//correct +
                'pass.updated_at as redemption_date')
            ->leftJoin('order_products AS op', 'op.product_id', '=', 'p.id')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->leftJoin('passes AS pass', 'pass.id', '=', 'o.pass_id')
            ->where('op.disabled', 0)
            ->where('o.final_target', 1)
            //  ->whereBetween(DB::raw("DATE_FORMAT(pass.updated_at,'%Y-%m')"), array($startMonth, $endMonth))
            ->groupBy('p.id')
           ->groupBy('o.subproject_id');
        $data['products'] = $products->get();

        $productsIds = $data['products']->pluck('id')->toArray();


        $ordersQuery = DB::table('products AS p')
            ->select('p.id', 'o.subproject_id', 'pass.updated_at',
                DB::raw('  case when o.target_status = 1 then count(distinct o.id) else 0 end as approveOrdersCount'))
            ->leftJoin('order_products AS op', 'op.product_id', '=', 'p.id')
            ->leftJoin('orders AS o', 'o.id', '=', 'op.order_id')
            ->leftJoin('passes AS pass', 'pass.id', '=', 'o.pass_id')
            ->where('op.disabled', 0)
            ->where('o.target_status', 1)
            // ->whereBetween(DB::raw("DATE_FORMAT(pass.updated_at,'%Y-%m')"), array($startMonth, $endMonth))
            ->whereIn('p.id', $productsIds)
            ->groupBy('p.id')
            ->groupBy('o.subproject_id');

        $data['productWithOrders'] = $ordersQuery->get();

        foreach ($data['products'] as $key => $productData) {
            foreach ($data['productWithOrders'] as $approveData) {
                if (isset($approveData->id) && $productData->id == $approveData->id && $productData->subproject_id == $approveData->subproject_id) {
                    $percent = $productData->paidUpOrdersCount / $approveData->approveOrdersCount * 100;
                    Redemption::create(['product_id'          => $productData->id,
                                        'subproject_id'       => $approveData->subproject_id,
                                        'start_month'         => $startMonth,
                                        'end_month'           => $endMonth,
                                        'percent'             => $percent,
                                        'approve'             => $approveData->approveOrdersCount,
                                        'redemption_quantity' => $productData->paidUpOrdersCount]);
                }
            }
        }
    }
}
