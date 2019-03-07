<?php

namespace App\Console\Commands;

use App\Models\CronTasks;
use Illuminate\Console\Command;

class ResetTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetTime';

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
        CronTasks $cronTasksModel
    ) {
        $cronTasksModel->setEndTimeForUsers();
        $cronTasksModel->setTimeTransaction();
    }
}
