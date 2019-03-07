<?php

namespace App\Console\Commands;

use App\Models\Api\Posts\Cdek;
use App\Models\Api\Posts\Wefast;
use App\Models\Variables;
use Illuminate\Console\Command;
use \Log;

class CdekTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdekTracking';

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
        Cdek::track();
    }
}
