<?php

namespace App\Console\Commands;

use App\Models\CronTasks;
use App\Models\Variables;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use App\Models\Order;
use App\Models\CallProcessing;
use App\Models\Campaign;
use App\Models\CallProgressLog;
use \Log;

class AddLearningCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addLearningCalls';

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
    public function handle(
        Order $orderModel,
        CallProgressLog $callProgressLogModel,
        CronTasks $cronTasksModel,
        Variables $variablesModel
) {
        try {
            $status = $variablesModel->getVariable('cron_add_learning_calls');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_add_learning_calls', 1);

            echo "----addLearningCalls----\n";
            $cronTasksModel->addLearningCalls();
            $variablesModel->setVariable('cron_add_learning_calls', 0);
            Log::info('Calls end');
        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_add_learning_calls', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo  $exception->getMessage(). "\n";
        }

    }
}
