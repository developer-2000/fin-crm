<?php

namespace App\Console\Commands;

use App\Models\CronTasks;
use Illuminate\Console\Command;

class GetOrdersInProcessing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getOrdersInProcessing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(CronTasks $cronTasksModel)
    {
        $cronTasksModel->getOrdersInProcessing();
    }
}
