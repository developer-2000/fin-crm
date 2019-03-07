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

class WeekTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weekTransaction';

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
    public function handle(CronTasks $cronTasks)
    {
        $cronTasks->rateTransaction('week');
    }
}
