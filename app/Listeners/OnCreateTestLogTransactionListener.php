<?php

namespace App\Listeners;

use App\Events\OnCreateTestLogTransactionEvent;
use App\Http\Controllers\PlanController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OnCreateTestLogTransactionListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OnCreateTestLogTransactionEvent  $event
     * @return void
     */
    public function handle(OnCreateTestLogTransactionEvent $event)
    {
        $logs = $event->logs;
      //  var_dump($logs).die();
        new PlanController($logs);
    }
}
