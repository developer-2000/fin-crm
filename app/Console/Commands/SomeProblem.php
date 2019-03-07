<?php

namespace App\Console\Commands;

use App\Classes\Client;
use App\Models\ColdCallFile;
use App\Models\CollectorLog;
use App\Models\CallProgressLog;
use App\Models\Offer;
use App\Models\OffersScript;
use App\Models\OrderProduct;
use App\Models\OrdersLog;
use App\Models\OrdersOpened;
use App\Models\OrdersPass;
use App\Models\Pass;
use App\Models\Product;
use App\Models\Project;
use App\Models\TargetValue;
use App\Models\Transaction;
use \App\Models\User;
use App\Models\CronTasks;
use App\Repositories\CollectingRepository;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SomeProblem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'someProblem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = Order::where('proc_status', 3)
            ->where('proc_campaign', 47)
            ->where('target_status', 0)
            ->get();

        $file = new ColdCallFile();
        $file->file_name = 'Заказы без цели';
        $file->status = 'active';
        $file->geo = 'ua';
        $file->company_id = 11;
        $file->campaign_id = 47;
        $file->save();

        foreach ($orders as $order) {
            echo $order->id . "\n";
            DB::table('cold_call_lists')
                ->insert([
                    'cold_call_file_id' => $file->id,
                    'phone_number' => '[' . $order->phone . ',0]',
                    'add_info' => json_encode([
                        'фио' => implode(' ', [
                            $order->name_last,
                            $order->name_first
                        ]),
                    ]),
                    'proc_status' => 1,
                    'order_id' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }

        echo $orders->count();
     }
}
