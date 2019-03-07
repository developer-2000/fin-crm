<?php

namespace App\Console\Commands;

use App\Http\Controllers\ActionController;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\OrdersOpened;
use App\Models\OrdersPass;
use App\Models\Pass;
use App\Models\Product;
use App\Models\ProductProject;
use App\Models\Project;
use App\Models\StorageContent;
use App\Models\TargetConfig;
use App\Models\TargetValue;
use App\Models\User;
use function foo\func;
use Illuminate\Console\Command;

class UpdateOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $orders = Order::whereIn('target_status', [1, 2, 3])->where('moderation_id', 0)
//            ->where('entity', 'order')->get();
//
//        foreach ($orders as $order) {
//            $ordersOpened = OrdersOpened::where('order_id', $order->id)
//                ->where('user_id', $order->target_user)
//                ->whereNotNull('date_closed')
//                ->orderBy('id', 'desc')->first();
//
//            if ($ordersOpened) {
//                $ordersOpened->target_status = $order->target_status;
//                $ordersOpened->target_status_time = $order->time_modified;
//                $ordersOpened->save();
//                var_dump($order->id,   $ordersOpened->target_status);
//            }
//
//        }

//        $orders = Order::whereBetween('time_modified', ['2018-11-01 00:00:00', '2018-11-30 23:59:59'])
//            ->where('target_status', 1)->where('entity', 'order')->where('service', '!=', 'sending')
//            ->whereHas('targetUser', function ($query){
//                $query->whereIn('company_id', [2,3]);
//            })
//            ->get();
//        foreach ($orders as $order){
//            $ordersLog = OrdersLog::where('order_id', $order->id)
//                ->where('text', 'like', '%Цель - Подтвержден%')
//                ->whereHas('user', function ($q){
//                    $q->where('role_id', 1);
//                })->orderBy('id', 'desc')->first();
//
//            if($ordersLog && $order->target_user != $ordersLog->user_id){
//                $order->target_user = $ordersLog->user_id;
//                $order->save();
//                (new OrdersLog)->addOrderLog($order->id, 'Обновление target_user. Заказ закреплен за оператором: ' . $ordersLog->user_id);
//                var_dump($order->id, $ordersLog->user_id);
//            }
//        }

//        $ordersLog = OrdersLog::where('text', 'like','%Оффер был изменен с "Перехват"%')
//          ->whereBetween('date',['2018-10-01 00:00:00','2018-12-04 23:59:59'])
//            ->get();
//        foreach ($ordersLog as $log ){
//            $order = Order::find($log->order_id);
//            $order->offer_id  = 3739;
//            $order->save();
//            $ordersArray[] = $order->id;
//        }
//
//        dd(count($ordersArray));

//        $ordersLog = \DB::table('orders_log')
//            ->where('text', 'like', '%Коллектор не дозвонился%')
//            ->where('date', '>', '2018-12-04 23:59:59');
//        $ordersIds = $ordersLog->pluck('order_id');
//
//        foreach ($ordersIds as $orderId) {
//            $order = Order::find($orderId);
//            $ordersLog = \DB::table('orders_log')
//                ->where('order_id', $order->id)
//                ->where(function ($query){
//                    $query
//                        ->where('text', 'like', '%Процессинг статус был изменен c "Отправлено" на "Контакт%')
//                        ->orWhere('text', 'like', '%Процессинг статус был изменен c "На отделении" на "Контакт%')
//                        ->orWhere('text', 'like', '%Процессинг статус был изменен c "Возврат" на "Контакт%')
//                        ->orWhere('text', 'like', '%Процессинг статус был изменен c "Забран" на "Контакт%');
//                })
//                //->where('status_id', '!=', 0)
//                ->orderBy('id', 'desc')->first();
//
//            if ($ordersLog) {
//                if ($order->moderation_id > 0 &&  $order->proc_status ==3) {
//                    if($ordersLog->text == 'Процессинг статус был изменен c "Отправлено" на "Контакт'){
//                        var_dump(24);
//                        $newProcStatus = 24;
//                    }
//                    if($ordersLog->text == 'Процессинг статус был изменен c "На отделении" на "Контакт'){
//                        var_dump(25);
//                        $newProcStatus = 25;
//                    }
//                    if($ordersLog->text == 'Процессинг статус был изменен c "Возврат" на "Контакт'){
//                        var_dump(27);
//                        $newProcStatus = 27;
//                    }
//                    if($ordersLog->text == 'Процессинг статус был изменен c "Забран" на "Контакт'){
//                        var_dump(26);
//                        $newProcStatus = 26;
//                    }
//                    var_dump($order->id, 'status - ' . $ordersLog->status_id . '  new - ' . $newProcStatus ). "\n";
//                        $ordersNotProcStatusEqual[] = $order->id;
//                        $order->proc_status = $newProcStatus;
//                        $order->save();
//                }
//            }
//        }
//        var_dump($ordersNotProcStatusEqual, count($ordersNotProcStatusEqual));


//        $orders = Order::
//        whereIn('id', [1968208 ,1967877 ,1967423,1966528 ,1966142,1965985 ,1965833 ,1965828 ,1965446 ,1965244 ,1965210 ,1964910  ,1964328  ,1964274  ,1964161 ,1963510 ,1962275 ,1961454  ,1961367  ,1958768  ,1958541 ,1958344 ,1958335,1954408,1953720 ,1947639 ,1942826  ,1936963])
//            ->where('proc_status', 24)
//            ->get()->pluck('id')->toArray();
//
//        ActionController::runCancelSend($orders);
//        $orders = Order::
//       where('proc_status', 3)
//       ->where('moderation_id', '>', 0)
//       ->where('target_status', 1)
//       ->where('subproject_id', '!=', 5)
//            ->get();
//       // proc_status = 3 and moderation_id > 0  AND  target_status = 1 AND subproject_id = 24
//        foreach ($orders as $order) {
//            $ordersLog = \DB::table('orders_log')
//                ->where('order_id', $order->id)->where('status_id', '!=', 0)->orderBy('id','desc')->first();
//
//            if($ordersLog){
//                    if($order->moderation_id > 0 && $order->proc_status == 3 && $ordersLog->status_id != $order->proc_status){
//                        var_dump($order->id, 'status - '.$ordersLog->status_id, $order->subproject_id). "\n";
////                        $ordersNotProcStatusEqual[] = $order->id;
////                        $order->proc_status = $ordersLog->status_id;
////                        $order->save();
//                    }
//            }
//        }


//        $items = Order::where([['project_id', 11], ['proc_campaign', 7]])
//            ->whereIn('proc_status', [1, 2])->where('geo', 'ru')->where('target_status', 0)->get();
//
//        $conf = TargetConfig::find(13);
//        $confVal = json_decode($conf->options);
//        $confVal->cause->field_value = 10;
//        $value = json_encode($confVal);
//        foreach ($items as $item) {
//            $item->proc_status = 3;
//            $item->target_user = 987593;
//            $item->target_status = 3;
//            $item->save();
//
//            $tV = TargetValue::where('order_id', $item->id)->first();
//            if ($tV) {
//                $tV->delete();
//            }
//            $newTV = new TargetValue();
//            $newTV->order_id = $item->id;
//            $newTV->target_id = 13;
//            $newTV->values = $value;
//            $newTV->save();
//        }

//        $orders = Order::where([['target_status', 1],['moderation_id', '!=', 0]])->where('service', '!=', 'call_center')
////            ->whereHas('logs', function ($query) {
////                $query->where('text', 'like', '%Под проект был изменен с%');
////            })
//           -> with(['logs' => function ($query) {
//                $query->where('text', 'like', '%Под проект был изменен с%');
//            }])
//            ->get();
//       $logs = OrdersLog::where('text', 'like', '%Под проект был изменен с%')
//           ->whereHas('order', function ($query){
//               $query->where([['target_status', 1],['moderation_id', '!=', 0]])->where('service', '!=', 'call_center');
//           })
//           ->with('order')->get()->pluck('order.id');
//
//       $orders = Order::whereIn('id', $logs)->get();
//       foreach ($orders as $order){
//          $log = OrdersLog::where('id', $order->id)->where('text', 'like', '%Под проект был изменен с%')->first();
//          if($log){
//              $userSubproject = User::find($log->user_id)->subproject_id;
//              if($userSubproject != $order->subproject){
//                  dd($order);
//              }
//          }
//       }

//        foreach ($orders as $order) {
//            echo $order->id . "\n";
//            $order->subproject_id = 35;
//            $order->save();
//        }
        //  $a = explode(',', '1804503,1804532,1804556,1804564,1804602,1804609,1804620,1804638,1804646,1804649,1804682,1804721,1804726,1804729,1804758,1804881,1804929,1804936,1804945,1804964,1804999,1805038,1805071,1805132,1805144,1805214,1805259,1805326,1805329,1805357,1805440,1805448,1805458,1805629,1805640,1805649,1805659,1805660,1805676,1805681,1805693,1805700,1805704,1805746,1805751,1805810,1805813,1805822,1805826,1805861,1805881,1805882,1805927,1805932,1805938,1805957,1805978,1805979,1805987,1805995,1806103,1806200,1806250,1806281,1806308,1806318,1806352,1806406,1806408,1806418,1806444,1806458,1806465,1806469,1806488,1806582,1806651,1806725,1806772,1806774,1806799,1806814,1806820,1806847,1806880,1806912,1806913,1806926,1806961,1806982,1807000,1807031,1807035,1807040,1807045,1807067,1807082,1807124,1807178,1807183,1807215,1807241,1807270,1807280,1807281,1807286,1807287,1807298,1807348,1807357,1807358,1807380,1807380,1807387,1807396,1807411,1807439,1807467,1807479,1807499,1807528,1807538,1807543,1807599,1807611,1807618,1807624,1807638,1807674,1807692,1807706,1807707,1807720,1807735,1807744,1807749,1807769,1807773,1807787,1807805,1807814,1807865,1807879,1807884,1807907,1807913,1807914,1807925,1807940,1807941,1808037,1808236,1808264');
        //  $b = explode(',', '"1804503,1804532,1804556,1804564,1804602,1804609,1804620,1804638,1804646,1804649,1804682,1804721,1804726,1804729,1804758,1804881,1804929,1804936,1804945,1804964,1804999,1805038,1805071,1805132,1805144,1805214,1805259,1805326,1805329,1805357,1805440,1805448,1805458,1805629,1805640,1805649,1805659,1805660,1805676,1805681,1805693,1805700,1805704,1805746,1805751,1805801,1805810,1805813,1805822,1805826,1805861,1805881,1805882,1805927,1805932,1805938,1805957,1805978,1805979,1805987,1805995,1806103,1806200,1806250,1806280,1806281,1806308,1806318,1806352,1806406,1806408,1806418,1806444,1806458,1806465,1806469,1806488,1806582,1806651,1806725,1806772,1806774,1806799,1806814,1806820,1806847,1806880,1806912,1806913,1806926,1806961,1806982,1807000,1807031,1807035,1807040,1807045,1807067,1807082,1807124,1807178,1807183,1807215,1807241,1807270,1807280,1807281,1807286,1807287,1807298,1807348,1807357,1807358,1807380,1807387,1807396,1807411,1807439,1807467,1807479,1807499,1807528,1807538,1807543,1807599,1807611,1807618,1807624,1807638,1807674,1807692,1807706,1807707,1807720,1807735,1807744,1807749,1807769,1807773,1807787,1807805,1807814,1807865,1807866,1807879,1807884,1807893,1807907,1807913,1807914,1807925,1807940,1807941,1808037,1808083,1808236,1808264"');
        //   dd(array_diff($b, $a));
//
//        $passes = Pass::with('ordersPass')->whereBetween('created_at', [
//            '2018-10-15 00:00:00',
//            '2018-10-15 23:59:59'
//        ])->get();
//        foreach ($passes as $pass)
//            foreach ($pass->ordersPass as $orderPass) {
//                $order = Order::find($orderPass->order_id);
//
//                if ($pass->type == 'sending') {
//                    if (!$order->pass_send_id) {
//                        $order->pass_send_id = $orderPass->pass_id;
//                        $order->save();
//                        echo $order->id .' set  pass_send_id' . "\n";
//                    }
//                }
//                if ($pass->type == 'redemption') {
//                    if (!$order->pass_id && $order->final_target == 1) {
//                        $order->pass_id = $orderPass->pass_id;
//                        $order->save();
//                        echo $order->id .' set redemption pass_id' . "\n";
//                    }
//                }
//                if ($pass->type == 'no-redemption') {
//                    if (!$order->pass_id && $order->final_target == 2) {
//                        $order->pass_id = $orderPass->pass_id;
//                        $order->save();
//                        echo $order->id .' set no-redemption pass_id' . "\n";
//                    }
//                }
//                if ($pass->type == 'to_print') {
//                    if (!$order->print_id) {
//                        $order->print_id = $orderPass->pass_id;
//                        $order->save();
//                        echo $order->id .' set to_print' . "\n";
//                    }
//                }
//            }
    }
}