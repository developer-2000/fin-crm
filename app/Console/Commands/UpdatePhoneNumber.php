<?php

namespace App\Console\Commands;

use App\Models\Api\Posts\Novaposhta;
use App\Models\Api\Posts\Viettel;
use App\Models\ColdCallList;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\ProcStatus;
use Illuminate\Console\Command;
use App\Models\Variables;
use App\Services\PhoneCorrection;

class UpdatePhoneNumber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update phones';

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
        try {
            $orders = Order::where('geo', 'id')->whereIn('proc_status', [1,6])->get();
            foreach ($orders as $order) {
                //  $phone = json_decode($order->input_data, true);
                if ($order->phone_input) {
                    $result = (new PhoneCorrection\PhoneCorrectionService)->customCorrectionForCountry($order->geo, $order->phone_input);
                    $oldStatusName = $order->procStatus->name;
                    if($result[1]){
                        $order->proc_status = 6;

                    }else{
                        $order->proc_status = 1;
                    }

                    $order->phone = $result[0];
                    if( $order->save()){
                        if($order->proc_status == 1){
                            $newStatus = ProcStatus::find(1);
                            $statusInfo = ['id' => 1, 'name' =>$newStatus->name];
                        }
                        if($order->proc_status == 6){
                            $newStatus = ProcStatus::find(6);
                            $statusInfo = ['id' => 6, 'name' =>$newStatus->name];
                        }

                        (new OrdersLog())->addOrderLog($order->id, 'Процессинг статус был изменен c "' . $oldStatusName . '" на "' . $newStatus->name, $statusInfo);
                    }

                }

                echo $order->id."\n";
            }

        } catch (\Exception $exception) {
            echo $exception;
        }
    }
}
