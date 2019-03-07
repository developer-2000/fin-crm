<?php

namespace App\Console\Commands;

use App\Models\Variables;
use Illuminate\Console\Command;

use App\Models\AuditOwner;
use Illuminate\Support\Facades\Log;

class AuditOperator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditOperator';

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
        AuditOwner $auditOwnerModel,
        Variables $variablesModel
    ) {
        try {
            $status = $variablesModel->getVariable('cron_audit_operator');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            $variablesModel->setVariable('cron_audit_operator', 1);
            echo date('H:i:s d/m/y', time()) . " - start\n";
            $auditOwnerModel->addTime();
            $variablesModel->setVariable('cron_audit_operator', 0);
            echo date('H:i:s d/m/y', time()) . " - end\n";
        } catch (\Exception $exception) {
            $variablesModel->setVariable('cron_audit_operator', 0);
            echo  date('H:i:s d/m/y', time()) . "\n";
            echo  $exception->getMessage(). "\n";
        }
    }
}
