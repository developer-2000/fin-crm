<?php

namespace App\Console\Commands;

use App\Models\CronTasks;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\TargetValue;
use App\Models\User;
use App\Models\Variables;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use App\Models\Order;
use App\Models\CallProcessing;
use App\Models\Campaign;
use App\Models\CallProgressLog;
use \Log;

class RestoreOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore_orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore orders';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lostOrders = '1809241,1809270,1809273,1809288,1809292,1809308,1809321,1809374,1809387,1809408,1809422,1809429,1809433,1809436,1809440,1809448,1809451,1809464,1809471,1809488,1809492,1809527,1809559,1809562,1809603,1809660,1809661,1809677,1809683,1809696,1809720,1809724,1809750,1809752,1809778,1809779,1809809,1809816,1809825,1809842,1809897,1809914,1809999,1810072,1810130,1810182,1810184,1810193,1810194,1810204,1810225,1810312,1810345,1810354,1810358,1810361,1810388,1810403,1810488,1810512,1810516,1810529,1810581,1810589,1810599,1810608,1810609,1810630,1810654,1810657,1810666,1810732,1810785';
        $lostOrdersArray = explode(',', $lostOrders);
        $ordersApiData = \DB::table('orders_api')->whereIn('order_id', $lostOrdersArray)->get();
        foreach ($ordersApiData as $data) {
            $apiData = json_decode($data->data);
            $orderProducts = OrderProduct::where('order_id', $data->order_id)->get();
            $price = 0;
            foreach ($orderProducts as $orderProduct) {
                if (!$orderProduct->disabled) {
                    $price += $orderProduct->price;
                }
            }
            $order = Order::where('id', $data->order_id)->first();
            $ordersLog = OrdersLog::where('order_id', $data->order_id)->get();
            if (!$order) {
                if ($ordersLog[0]) {
                    $orderLogExist = OrdersLog::where('order_id', $data->order_id)->where(function ($query) {
                        $query->where('text', 'like', '%Заказ был склонирован%')
                            ->orWhere('text', 'like', '%Заказ был создан%');
                    })
                        ->first();
                    echo $data->order_id . "\n";
                    if ($orderLogExist) {
                        $userOwnerProj = 0;
                        $userOwnerSubProj = 0;
                        if (User::find($ordersLog[0]->user_id)) {
                            $userOwnerProj = User::find($ordersLog[0]->user_id)->project_id;
                            $userOwnerSubProj = User::find($ordersLog[0]->user_id)->sub_project_id;
                        }

                        $targetValue = TargetValue::where('order_id', $data->order_id)->first();
                        if (!$order) {
                            $order = new Order;
                            $order->id = $data->order_id;
                            $order->geo = $apiData->geo;
                            $order->name_first = $apiData->name;
                            $order->name_last = $apiData->surname;
                            $order->name_middle = $apiData->middle;
                            $order->phone = $apiData->phone;
                            $order->price_total = $price;
                            $order->time_created = $ordersLog[0]->date;
                            $order->entity = 'order';
                            $order->offer_id = $apiData->offer_id;
                            $order->partner_id = 0;
                            $order->project_id = $userOwnerProj;
                            $order->partner_oid = 0;
                            $order->handmade = 1;
                            $order->service = 'sending';
                            $order->moderation_id = User::find($ordersLog[0]->user_id)->id;
                            $order->moderation_time = $ordersLog[0]->date;

                            $order->subproject_id = $userOwnerSubProj;
                            if (isset($targetValue->target_id)) {
                                $order->target_approve = $targetValue->target_id;
                            }
                            $order->proc_status = $apiData->proc_status;
                            $order->target_status = $apiData->target_status;
                            $order->target_status = $apiData->final_target;
                            $order->target_user = $ordersLog[0]->user_id;
                            $order->save();
                        }
                    }
                }
            }
        }
    }
}
