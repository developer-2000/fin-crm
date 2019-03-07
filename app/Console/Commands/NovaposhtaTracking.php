<?php

namespace App\Console\Commands;

use App\Models\Api\Posts\Novaposhta;
use Illuminate\Console\Command;
use App\Models\Variables;

class NovaposhtaTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'novaposhta_tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'nova poshta tracking';

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
            $status = (new Variables)->getVariable('cron_novaposhta_tracking');
            if (!$status || $status->value == 1) {
                echo date('H:i:s d/m/y', time()) . " already started\n";
                exit;
            }
            (new Variables)->setVariable('cron_novaposhta_tracking', 1);
            echo date('H:i:s d/m/y', time()) . " - start\n";
            Novaposhta::track();

            (new Variables)->setVariable('cron_novaposhta_tracking', 0);
            echo date('H:i:s d/m/y', time()) . " - end\n";
        } catch (\Exception $exception) {
            (new Variables)->setVariable('cron_novaposhta_tracking', 0);
            echo date('H:i:s d/m/y', time()) . "\n";
            echo $exception->getMessage() . "\n";
        }
    }
}
