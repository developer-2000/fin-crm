<?php

namespace App\Console\Commands;

use App\Models\Moving;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\OrdersPass;
use App\Models\Pass;
use App\Models\ProcStatus;
use App\Models\StorageContent;
use App\Models\StorageTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update store';

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
        //очистить storage_content
        StorageContent::query()->delete();
        //очистить storage_transactions
        StorageTransaction::query()->delete();

        //Возобновление складский танзакций при движениях товаров
        echo "start moving \n";
        $movings = Moving::with('movingProducts.parts')->get();
        foreach ($movings as $moving) {
            foreach ($moving->movingProducts as $product) {
                if ($moving->sender_id) {
                    $fromStorage = StorageContent::where([
                        ['project_id', $moving->sender_id],
                        ['product_id', $product->product_id]
                    ])->first();
                    $st = StorageTransaction::firstOrCreate([
                        'project_id' => $moving->sender_id,
                        'product_id' => $product->product_id,
                        'moving_id'  => $moving->id,
                        'type'       => StorageTransaction::TYPE_SYSTEM_SENT,
                    ], [
                        'amount1'    => $fromStorage->amount ?? 0,
                        'amount2'    => $fromStorage->amount ?? 0,
                        'hold1'      => $fromStorage->hold ?? 0,
                        'hold2'      => ($fromStorage->hold ?? 0) + $product->amount,
                        'user_id'    => $moving->user_id,
                        'created_at' => $moving->created_at,
                    ]);
                    if (!$fromStorage) {
                        $fromStorage = new StorageContent();
                        $fromStorage->project_id = $moving->sender_id;
                        $fromStorage->product_id = $product->product_id;
                        $fromStorage->created_at = $moving->created_at;
                    }
                    $fromStorage->amount = $st->amount2;
                    $fromStorage->hold = $st->hold2;
                    $fromStorage->save();
                }
                foreach ($product->parts as $part) {
                    if ($moving->sender_id) {
                        $storagefrom = StorageContent::where('project_id', $moving->sender_id)
                            ->where('product_id', $product->product_id)
                            ->first();

                        $releasedTransaction = new StorageTransaction();
                        $releasedTransaction->product_id = $product->product_id;
                        $releasedTransaction->project_id = $moving->sender_id;
                        $releasedTransaction->moving_id = $moving->id;
                        $releasedTransaction->user_id = $moving->user_id;
                        $releasedTransaction->amount1 = $storagefrom->amount ?? 0;
                        $releasedTransaction->amount2 = $releasedTransaction->amount1;
                        $releasedTransaction->hold1 = $storagefrom->hold ?? 0;
                        $releasedTransaction->hold2 = $releasedTransaction->hold1 - $part->amount;
                        $releasedTransaction->type = StorageTransaction::TYPE_SYSTEM_RELEASED;
                        $releasedTransaction->created_at = $part->created_at;
                        $releasedTransaction->save();


                        if (!$storagefrom) {
                            $storagefrom = new StorageContent();
                            $storagefrom->project_id = $moving->sender_id;
                            $storagefrom->product_id = $releasedTransaction->product_id;
                            $storagefrom->created_at = $part->created_at;
                        }
                        $storagefrom->amount = $releasedTransaction->amount2;
                        $storagefrom->hold = $releasedTransaction->hold2;
                        $storagefrom->save();
                    }

                    $storageTo = StorageContent::where('project_id', $moving->receiver_id)
                        ->where('product_id', $product->product_id)
                        ->first();

                    $receivedTransaction = new StorageTransaction();
                    $receivedTransaction->product_id = $product->product_id;
                    $receivedTransaction->project_id = $moving->receiver_id;
                    $receivedTransaction->moving_id = $moving->id;
                    $receivedTransaction->user_id = $moving->user_id;
                    $receivedTransaction->amount1 = $storageTo->amount ?? 0;
                    $receivedTransaction->amount2 = $receivedTransaction->amount1 + $part->amount;
                    $receivedTransaction->hold1 = $storageTo->hold ?? 0;
                    $receivedTransaction->hold2 = $storageTo->hold ?? 0;
                    $receivedTransaction->type = StorageTransaction::TYPE_SYSTEM_RECEIVED;
                    $receivedTransaction->created_at = $part->created_at;
                    $receivedTransaction->save();

                    if (!$storageTo) {
                        $storageTo = new StorageContent();
                        $storageTo->project_id = $moving->receiver_id;
                        $storageTo->product_id = $receivedTransaction->product_id;
                        $storageTo->created_at = $part->created_at;
                    }
                    $storageTo->amount = $receivedTransaction->amount2;
                    $storageTo->hold = $receivedTransaction->hold2;
                    $storageTo->save();

                }
            }
        }


        //Создание транзакций и движений по складу на заказы у которых статус Отправлен или больше но нет еще выкупа
        echo "start sent transactions \n";
        $procStatusesId = ProcStatus::whereIn('action', ['sent', 'at_department', 'received', 'returned'])
            ->pluck('id')->toArray();
        $ordersSent = Order::whereIn('proc_status', $procStatusesId)
            ->where('final_target', 0)
            ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32])
            ->get();
        foreach ($ordersSent as $order) {
            $sentStatusUserId = 0;
            if ($order->pass_send_id) {
                $pass = Pass::where('id', $order->pass_send_id)->where('type', Pass::TYPE_SENDING)->first();
                $sentStatusUserId = $pass->user_id;
            } else {
                $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                foreach ($orderLogs as $log) {
                    if (stripos($log->text, ' на "Отправлено')) {
                        $sentStatusUserId = $log->user_id;
                    }
                }
            }

            $timeSent = $order->time_sent;

            //если у заказа нет даты отпраки ->> то дата StorageTransaction ставим дату модерации
            if (!$timeSent) {
                $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                foreach ($orderLogs as $log) {
                    if (stripos($log->text, ' на "Отправлено')) {
                        $timeSent = date('Y-m-d H:i:s', (int)$log->date);
                    }
                }
            }
            if (!$timeSent) {
                $timeSent = $order->moderation_time;
            }

            $products = (new OrderProduct())->getProductsByOrderId($order->id, $order->subproject_id);
            foreach ($products as $product) {
                $sc = StorageContent::where('project_id', $order->subproject_id)
                    ->where('product_id', $product->id)->first();

                if (!$sc) {
                    $sc = new StorageContent();
                    $sc->project_id = $order->subproject_id;
                    $sc->product_id = $product->id;
                    $sc->hold = 0;
                    $sc->amount = 0;
                }

                if ($sc) {
                    $sc->amount += -1; //т.к. в заказе каждого товара по 1 шт
                    $sc->hold += 1;
                    if ($sc->save()) {
                        StorageTransaction::create([
                            'product_id' => $product->id,
                            'project_id' => $order->subproject_id,
                            'user_id'    => $sentStatusUserId,
                            'amount1'    => $sc->amount + 1,
                            'amount2'    => $sc->amount,
                            'hold1'      => $sc->hold - 1,
                            'hold2'      => $sc->hold,
                            'type'       => StorageTransaction::TYPE_SENT,
                            'moving_id'  => 0,
                            'created_at' => $timeSent,
                            'order_id'   => $order->id
                        ]);
                    }
                }
            }
        }

        echo "start sent transactions for redemption \n"; //заказ выкуплен, но когда-то выл отправлен
        $ordersSent = Order::where('final_target', 1)
            ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32])
            ->get();
        foreach ($ordersSent as $order) {
            $sentStatusUserId = 0;
            if ($order->pass_send_id) {
                $pass = Pass::where('id', $order->pass_send_id)->where('type', Pass::TYPE_SENDING)->first();
                $sentStatusUserId = $pass->user_id;
            } else {

                $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                foreach ($orderLogs as $log) {
                    if (stripos($log->text, ' на "Отправлено')) {
                        $sentStatusUserId = $log->user_id;
                    }

                }
            }

            $timeSent = $order->time_sent;

            //если у заказа нет даты отпраки ->> то дата StorageTransaction ставим дату модерации
            if (!$timeSent) {
                $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                foreach ($orderLogs as $log) {
                    if (stripos($log->text, ' на "Отправлено')) {
                        $timeSent = date('Y-m-d H:i:s', (int)$log->date);
                    }
                }
            }
            if (!$timeSent) {
                $timeSent = $order->moderation_time;
            }

            if (!$order->pass_send_id) {
                $sendPass = new Pass();
                $sendPass->active = 0;
                $sendPass->type = Pass::TYPE_SENDING;
                $sendPass->user_id = $sentStatusUserId;
                $sendPass->sub_project_id = $order->subproject_id;
                $sendPass->created_at = $timeSent;
                $sendPass->updated_at = $timeSent;
                $sendPass->comment = 'manual updated';
                $sendPass->save();

                if (!OrdersPass::where('order_id', $order->id)->where('pass_id', $sendPass->id)->first()) {
                    $orderPassPaidUp = new OrdersPass();
                    $orderPassPaidUp->pass_id = $sendPass->id;
                    $orderPassPaidUp->order_id = $order->id;
                    $orderPassPaidUp->created_at = $timeSent;
                    $orderPassPaidUp->updated_at = $timeSent;
                    $orderPassPaidUp->save();
                }

                $order->pass_send_id = $sendPass->id;
                $order->save();
            }

            $products = (new OrderProduct())->getProductsByOrderId($order->id, $order->subproject_id);
            foreach ($products as $product) {
                $sc = StorageContent::where('project_id', $order->subproject_id)
                    ->where('product_id', $product->id)->first();

                if (!$sc) {
                    $sc = new StorageContent();
                    $sc->project_id = $order->subproject_id;
                    $sc->product_id = $product->id;
                    $sc->hold = 0;
                    $sc->amount = 0;
                }

                if ($sc) {
                    $sc->amount += -1; //т.к. в заказе каждого товара по 1 шт
                    $sc->hold += 1;
                    if ($sc->save()) {
                        StorageTransaction::create([
                            'product_id' => $product->id,
                            'project_id' => $order->subproject_id,
                            'user_id'    => $sentStatusUserId,
                            'amount1'    => $sc->amount + 1,
                            'amount2'    => $sc->amount,
                            'hold1'      => $sc->hold - 1,
                            'hold2'      => $sc->hold,
                            'type'       => StorageTransaction::TYPE_SENT,
                            'moving_id'  => 0,
                            'created_at' => $timeSent,
                            'order_id'   => $order->id
                        ]);
                    }
                }
            }
        }

        echo "start sent transactions for no-redemption \n"; //заказ не выкуплен, но когда-то выл отправлен
        $ordersSent = Order::where('final_target', 2)
            ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32])
            ->get();
        foreach ($ordersSent as $order) {
            $sentStatusUserId = 0;
            if ($order->pass_send_id) {
                $pass = Pass::where('id', $order->pass_send_id)->where('type', Pass::TYPE_SENDING)->first();
                $sentStatusUserId = $pass->user_id;
            } else {
                $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                foreach ($orderLogs as $log) {
                    if (stripos($log->text, ' на "Отправлено')) {
                        $sentStatusUserId = $log->user_id;
                    }

                }
            }

            $timeSent = $order->time_sent;

            //если у заказа нет даты отпраки ->> то дата StorageTransaction ставим дату модерации
            if (!$timeSent) {
                $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                foreach ($orderLogs as $log) {
                    if (stripos($log->text, ' на "Отправлено')) {
                        $timeSent = date('Y-m-d H:i:s', (int)$log->date);
                    }
                }
            }
            if (!$timeSent) {
                $timeSent = $order->moderation_time;
            }

            if (!$order->pass_send_id) {
                $sendPass = new Pass();
                $sendPass->active = 0;
                $sendPass->type = Pass::TYPE_SENDING;
                $sendPass->user_id = $sentStatusUserId;
                $sendPass->sub_project_id = $order->subproject_id;
                $sendPass->created_at = $timeSent;
                $sendPass->updated_at = $timeSent;
                $sendPass->comment = 'manual updated';
                $sendPass->save();

                if (!OrdersPass::where('order_id', $order->id)->where('pass_id', $sendPass->id)->first()) {
                    $orderPassPaidUp = new OrdersPass();
                    $orderPassPaidUp->pass_id = $sendPass->id;
                    $orderPassPaidUp->order_id = $order->id;
                    $orderPassPaidUp->created_at = $timeSent;
                    $orderPassPaidUp->updated_at = $timeSent;
                    $orderPassPaidUp->save();
                }

                $order->pass_send_id = $sendPass->id;
                $order->save();
            }

            $products = (new OrderProduct())->getProductsByOrderId($order->id, $order->subproject_id);
            foreach ($products as $product) {
                $sc = StorageContent::where('project_id', $order->subproject_id)
                    ->where('product_id', $product->id)->first();

                if (!$sc) {
                    $sc = new StorageContent();
                    $sc->project_id = $order->subproject_id;
                    $sc->product_id = $product->id;
                    $sc->hold = 0;
                    $sc->amount = 0;
                }

                if ($sc) {
                    $sc->amount += -1; //т.к. в заказе каждого товара по 1 шт
                    $sc->hold += 1;
                    if ($sc->save()) {
                        StorageTransaction::create([
                            'product_id' => $product->id,
                            'project_id' => $order->subproject_id,
                            'user_id'    => $sentStatusUserId,
                            'amount1'    => $sc->amount + 1,
                            'amount2'    => $sc->amount,
                            'hold1'      => $sc->hold - 1,
                            'hold2'      => $sc->hold,
                            'type'       => StorageTransaction::TYPE_SENT,
                            'moving_id'  => 0,
                            'created_at' => $timeSent,
                            'order_id'   => $order->id
                        ]);
                    }
                }
            }
        }

        //Выкуп
        echo "start redemption transactions \n";
        $procStatusPaidUp = ProcStatus::whereIn('action', ['paid_up'])
            ->pluck('id')->toArray();
        $ordersPaidUp = Order::whereIn('proc_status', $procStatusPaidUp)->where('final_target', 1)
            ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32])
            ->get();
        foreach ($ordersPaidUp as $order) {
            if ($order instanceof Order) {
                $paiUpStatusUserId = 0;
                if ($order->pass_id) {
                    $pass = Pass::where('id', $order->pass_id)->where('type', Pass::TYPE_REDEMPTION)->first();
                    if ($pass) {
                        $paiUpStatusUserId = $pass->user_id;
                    }
                } else {
                    $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                    foreach ($orderLogs as $log) {
                        if (stripos($log->text, '"Выкуп"')) {
                            $paiUpStatusUserId = $log->user_id;
                        }
                    }
                }
                if (!$order->pass_id) {
                    $ordersPaidUpWithoutPass[] = [
                        'order_id' => $order->id,
                        'user_id'  => $paiUpStatusUserId];
                }

                $timePaidUp = $order->time_paid_up;

                //если у заказа нет даты отпраки ->> то дата StorageTransaction ставим дату модерации
                if (!$timePaidUp) {
                    $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                    foreach ($orderLogs as $log) {
                        if (stripos($log->text, '"Выкуп"')) {
                            $timePaidUp = date('Y-m-d H:i:s', (int)$log->date);
                        }
                    }
                }
                if (!$timePaidUp) {
                    $timePaidUp = $order->moderation_time;
                }

                $products = $order->products;
                if ($products->isNotEmpty()) {
                    foreach ($products as $product) {
                        $storageContent = StorageContent::where([
                            ['project_id', $order->subproject_id],
                            ['product_id', $product->id]
                        ])->first();
                        if ($storageContent) {
                            $storageContent->hold -= 1;
                            if ($storageContent->save()) {
                                StorageTransaction::create([
                                    'product_id' => $product->id,
                                    'project_id' => $order->subproject_id,
                                    'user_id'    => $paiUpStatusUserId,
                                    'amount1'    => $storageContent->amount,
                                    'amount2'    => $storageContent->amount,
                                    'hold1'      => $storageContent->hold + 1,
                                    'hold2'      => $storageContent->hold,
                                    'type'       => StorageTransaction::TYPE_RECEIVED,
                                    'moving_id'  => 0,
                                    'created_at' => $timePaidUp,
                                    'order_id'   => $order->id
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // Не Выкуп
        echo "start No-redemption transactions \n";
        $procStatuseRefused = ProcStatus::whereIn('action', ['refused'])
            ->pluck('id')->toArray();
        $ordersRefused = Order::whereIn('proc_status', $procStatuseRefused)->where('final_target', 2)
            ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32])
            ->get();
        foreach ($ordersRefused as $order) {
            if ($order instanceof Order) {
                echo $order->id . "\n";
                $refusedStatusUserId = 0;
                if ($order->pass_id) {
                    $pass = Pass::where('id', $order->pass_id)->where('type', Pass::TYPE_NO_REDEMPTION)->first();
                    if ($pass) {
                        $refusedStatusUserId = $pass->user_id;
                    }
                } else {
                    $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                    foreach ($orderLogs as $log) {
                        if (stripos($log->text, '"Не выкуп"')) {
                            $refusedStatusUserId = $log->user_id;
                        }
                    }
                    $ordersRefusedWithoutPass[] = $order->id;
                }

                if (!$order->pass_id) {
                    $ordersRefusedWithoutPass[] = [
                        'order_id' => $order->id,
                        'user_id'  => $refusedStatusUserId];
                }

                $timeRefused = $order->time_refused;

                //если у заказа нет даты отпраки ->> то дата StorageTransaction ставим дату модерации
                if (!$timeRefused) {
                    $orderLogs = OrdersLog::where('order_id', $order->id)->get();
                    foreach ($orderLogs as $log) {
                        if (stripos($log->text, '"Не выкуп"')) {
                            $timeRefused = date('Y-m-d H:i:s', (int)$log->date);
                        }
                    }
                }
                if (!$timeRefused) {
                    $timeRefused = $order->moderation_time;
                }

                $products = $order->products;
                if ($products->isNotEmpty()) {
                    foreach ($products as $product) {
                        $storageContent = StorageContent::where([
                            ['project_id', $order->subproject_id],
                            ['product_id', $product->id]
                        ])->first();
                        if ($storageContent) {
                            $storageContent->hold -= 1;
                            $storageContent->amount += 1;
                            if ($storageContent->save()) {
                                StorageTransaction::create([
                                    'product_id' => $product->id,
                                    'project_id' => $order->subproject_id,
                                    'user_id'    => $refusedStatusUserId,
                                    'amount1'    => $storageContent->amount - 1,
                                    'amount2'    => $storageContent->amount,
                                    'hold1'      => $storageContent->hold + 1,
                                    'hold2'      => $storageContent->hold,
                                    'type'       => StorageTransaction::TYPE_RETURN,
                                    'moving_id'  => 0,
                                    'created_at' => $timeRefused,
                                    'order_id'   => $order->id
                                ]);
                            }
                        }
                    }
                }
            }
        }

        //создать проводку если нет на каждый из статусов

        //Создание проводки при отправке
        echo "start create pass sent transactions \n";
        $storageTransactionsSent = StorageTransaction::where('type', StorageTransaction::TYPE_SENT)
            ->whereHas('order', function ($query) {
                $procStatusesId = ProcStatus::whereIn('action', ['sent', 'at_department', 'received', 'returned'])
                    ->pluck('id')->toArray();
                $query->where('pass_send_id', 0)->whereIn('proc_status', $procStatusesId)
                    ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32]);
            })->get();
        foreach ($storageTransactionsSent as $itemSent) {
            $orderItemSent = Order::find($itemSent->order_id);
            $passSent = Pass::where('created_at', $itemSent->created_at)->where('type', 'sending')
                ->where('user_id', $itemSent->user_id)->where('sub_project_id', $orderItemSent->subproject_id)->first();
            if (!$passSent) {
                $passSent = new Pass();
                $passSent->active = 0;
                $passSent->type = Pass::TYPE_SENDING;
                $passSent->user_id = $itemSent->user_id;
                $passSent->sub_project_id = $orderItemSent->subproject_id;
                $passSent->created_at = $itemSent->created_at;
                $passSent->updated_at = $itemSent->created_at;
                $passSent->comment = 'manual updated';
                $passSent->save();
            }

            if (!OrdersPass::where('order_id', $orderItemSent->id)->where('pass_id', $passSent->id)->first()) {
                $orderPassSent = new OrdersPass();
                $orderPassSent->pass_id = $passSent->id;
                $orderPassSent->order_id = $orderItemSent->id;
                $orderPassSent->created_at = $itemSent->created_at;
                $orderPassSent->updated_at = $itemSent->created_at;
                $orderPassSent->save();
            }
            $orderItemSent->pass_send_id = $passSent->id;
            $orderItemSent->save();

        }

        //Создание проводки при выкупе
        echo "start create pass redemption transactions \n";
        $storageTransactionsPaidUp = StorageTransaction::where('type', StorageTransaction::TYPE_RECEIVED)
            ->whereHas('order', function ($query) {
                $query->where('pass_id', 0)->where('final_target', 1)
                    ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32]);
            })->get();
        foreach ($storageTransactionsPaidUp as $itemPaidIp) {
            $orderPaidUp = Order::find($itemPaidIp->order_id);
            $passPaidUp = Pass::where('created_at', $itemPaidIp->created_at)->where('type', 'redemption')
                ->where('user_id', $itemPaidIp->user_id)->where('sub_project_id', $orderPaidUp->subproject_id)
                ->first();
            if (!$passPaidUp) {
                $passPaidUp = new Pass();
                $passPaidUp->active = 0;
                $passPaidUp->type = Pass::TYPE_REDEMPTION;
                $passPaidUp->user_id = $itemPaidIp->user_id;
                $passPaidUp->sub_project_id = $orderPaidUp->subproject_id;
                $passPaidUp->created_at = $itemPaidIp->created_at;
                $passPaidUp->updated_at = $itemPaidIp->created_at;
                $passPaidUp->comment = 'manual updated';
                $passPaidUp->save();

            }
            $existPaidUpPass = OrdersPass::where('order_id', $orderPaidUp->id)->where('pass_id', $passPaidUp->id)->first();
            if (!$existPaidUpPass) {
                $orderPassPaidUp = new OrdersPass();
                $orderPassPaidUp->pass_id = $passPaidUp->id;
                $orderPassPaidUp->order_id = $orderPaidUp->id;
                $orderPassPaidUp->created_at = $itemPaidIp->created_at;
                $orderPassPaidUp->updated_at = $itemPaidIp->created_at;
                $orderPassPaidUp->save();
            }

            $orderPaidUp->pass_id = $passPaidUp->id;
            $orderPaidUp->save();
        }

        //Создание проводки при Не выкупе
        echo "start create pass No-redemption transactions \n";
        $storageTransactionsRefused = StorageTransaction::where('type', StorageTransaction::TYPE_RETURN)
            ->whereHas('order', function ($query) {
                $procStatusRefused = ProcStatus::whereIn('action', ['refused'])
                    ->pluck('id')->toArray();
                $query->where('pass_id', 0)->whereIn('proc_status', $procStatusRefused)->where('final_target', 2)
                    ->whereIn('subproject_id', [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 32]);
            })->get();
        foreach ($storageTransactionsRefused as $itemRefused) {
            $orderRefused = Order::find($itemRefused->order_id);
            $passRefused = Pass::where('created_at', $itemRefused->created_at)->where('type', 'no-redemption')
                ->where('user_id', $itemRefused->user_id)->where('sub_project_id', $orderRefused->subproject_id)
                ->first();
            if (!$passRefused) {
                $passRefused = new Pass();
                $passRefused->active = 0;
                $passRefused->type = Pass::TYPE_NO_REDEMPTION;
                $passRefused->user_id = $itemRefused->user_id;
                $passRefused->sub_project_id = $orderRefused->subproject_id;
                $passRefused->created_at = $itemRefused->created_at;
                $passRefused->updated_at = $itemRefused->created_at;
                $passRefused->comment = 'manual updated';
                $passRefused->save();

            }
            if (!OrdersPass::where('order_id', $orderRefused->id)->where('pass_id', $passRefused->id)->first()) {
                $orderPassRefused = new OrdersPass();
                $orderPassRefused->pass_id = $passRefused->id;
                $orderPassRefused->order_id = $orderRefused->id;
                $orderPassRefused->created_at = $itemRefused->created_at;
                $orderPassRefused->updated_at = $itemRefused->created_at;
                $orderPassRefused->save();
            }

            $orderRefused->pass_id = $passRefused->id;
            $orderRefused->save();
        }
    }
}
