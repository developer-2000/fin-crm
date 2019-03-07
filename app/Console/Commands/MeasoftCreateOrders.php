<?php

namespace App\Console\Commands;

use App\Classes\Client;
use App\Models\Api\Posts\Measoft;
use App\Models\OrderProduct;
use App\Models\OrdersPass;
use App\Models\Pass;
use App\Models\Product;
use App\Models\Project;
use App\Models\TargetValue;
use App\Models\Transaction;
use \App\Models\User;
use App\Models\CronTasks;
use App\Models\Variables;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

use Illuminate\Support\Facades\Log;

class MeasoftCreateOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'measoftCreateOrders';

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
    public function handle(Variables $variablesModel)
    {
        try {
            $status = $variablesModel->getVariable('cron_measoft_orders');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_measoft_orders', 1);
            echo date('Y-m-d H:i:s', time()) . " - start \n";

            Measoft::cronCreateOrders();

            $variablesModel->setVariable('cron_measoft_orders', 0);
            echo date('Y-m-d H:i:s', time()) . " - end \n";
        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_measoft_orders', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo  $exception->getMessage(). "\n";
        }
    }
}
