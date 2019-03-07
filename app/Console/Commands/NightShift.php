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

class NightShift extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nightShift';

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
        CronTasks $cronTasksModel,
        Variables $variablesModel
) {
        try {
            $status = $variablesModel->getVariable('cron_nightShift');
            if (!$status || $status->value == 1) {
                echo "already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_nightShift', 1);
            $cronTasksModel->nightShift();
            $variablesModel->setVariable('cron_nightShift', 0);
        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_nightShift', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo  $exception->getMessage(). "\n";
        }


    }
}
