<?php

namespace App\Console\Commands;

use App\Models\CronTasks;
use App\Models\Variables;
use Illuminate\Console\Command;
use DB;

use Log;

class CronVietnam extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronVietnam';

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
    public function handle(Variables $variablesModel, CronTasks $cronTasksModel)
    {
        try {
            $status = $variablesModel->getVariable('cron_cron_vietnam');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_cron_vietnam', 1);

            $cronTasksModel->vietnam();
            echo "indonesia\n";
            $cronTasksModel->indonesia();

            $variablesModel->setVariable('cron_cron_vietnam', 0);

        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_cron_vietnam', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo 'line : ' . $exception->getLine() . ' ' .  $exception->getMessage(). "\n";
        }
    }
}
