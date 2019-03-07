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

class CheckOnlineUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'online';

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
            $status = $variablesModel->getVariable('cron_online');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_online', 1);
            if (time() > strtotime('now 00:05:00')) {
                $cronTasksModel->setTimePBX();
                $cronTasksModel->checkOnlineUser();
            }
            $variablesModel->setVariable('cron_online', 0);
        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_online', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo  $exception->getFile() . ' ' . $exception->getLine() . ' ' . $exception->getMessage(). "\n";
        }

    }
}
