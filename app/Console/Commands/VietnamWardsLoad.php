<?php

namespace App\Console\Commands;

use App\Models\VietnamWard;
use Illuminate\Console\Command;

class VietnamWardsLoad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vietnam_wards:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Vietnam wards with districts and provinces weekly';

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
        VietnamWard::loadWards();
    }
}
