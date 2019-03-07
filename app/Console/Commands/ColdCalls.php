<?php

namespace App\Console\Commands;

use App\Models\CallProgressLog;
use App\Models\ColdCallList;
use App\Models\Variables;
use Illuminate\Console\Command;
use App\Models\Order;
use \Log;


class ColdCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cold_calls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'processing cold calls';

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
    public function handle(Variables $variablesModel)
    {
        try {
            $status = $variablesModel->getVariable('cron_cold_calls');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_cold_calls', 1);
            echo "Calls start\n";

            (new Order())->getResultCallsElastixColdCalls(new CallProgressLog(), 'cold_call');
            (new ColdCallList)->addColdCallsInElastix();

           // ColdCallList::sendOrderToBs7();
            echo "Calls end\n";
            $variablesModel->setVariable('cron_cold_calls', 0);
        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_cold_calls', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo  $exception->getMessage(). "\n";
        }

    }
}
