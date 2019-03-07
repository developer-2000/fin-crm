<?php

use Illuminate\Database\Seeder;

class UpdateOrdersApi extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skip = 0;
        $take = 10000;
        $orderModel = new \App\Models\Order();
        $count = 0;
        do {
            $orders = DB::table('orders_api')
                ->skip($skip)
                ->take($take)
                ->orderBy('order_id', 'desc')
                ->get();

            if ($orders->isNotEmpty()) {

                $ordersIds = [];
                $data = [];
                foreach ($orders as $order) {
                    $ordersIds[] = $order->order_id;
                    $data[] = [
                        'order_id' => $order->order_id,
                        'data'      => $orderModel->getOrderForApi($order->order_id),
                        'date'      => now(),
                    ];
                }

                DB::table('orders_api')
                    ->whereIn('order_id', $ordersIds)
                    ->delete();

                DB::table('orders_api')
                    ->insert($data);
            }

            $count += $orders->count();
            $skip = $count;
            echo $count . "\n";

        } while ($orders->isNotEmpty());
    }
}
