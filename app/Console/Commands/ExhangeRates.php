<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use App\Models\Result;

class ExhangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange_rates';

    /**e
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get exchange rates';

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
        (new Country)->updateExchangeRates();
    }
}
