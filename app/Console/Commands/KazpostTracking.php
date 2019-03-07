<?php

namespace App\Console\Commands;

use App\Models\Api\Posts\Kazpost;
use App\Models\Api\Posts\Wefast;
use App\Models\Variables;
use Illuminate\Console\Command;
use \Log;

class KazpostTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kazpostTracking';

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
    public function handle(Variables $variablesModel) {
//        try {
//            $status = $variablesModel->getVariable('cron_kazpost_tracking');
//            if (!$status || $status->value == 1) {
//                echo date('H:i:s d/m/y', time()) . " already started\n";
//                exit;
//            }
//            $variablesModel->setVariable('cron_kazpost_tracking', 1);

            Kazpost::track();

//            $variablesModel->setVariable('cron_kazpost_tracking', 0);
//        } catch (\Exception $exception) {
//            $variablesModel->setVariable('cron_kazpost_tracking', 0);
//            echo  date('H:i:s d/m/y', time()) . "\n";
//            echo  $exception->getMessage(). "\n";
//        }

    }
}
