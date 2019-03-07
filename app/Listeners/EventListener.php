<?php

namespace App\Listeners;

use App\Events\newTransactionEvent;
use App\Models\Company;
use App\Models\Result;
use App\Models\Transaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Bonus;

class EventListener
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
     * @param  newTransactionEvent  $event
     * @return void
     */
    public function handle(newTransactionEvent $event)
    {
      $transaction = $event->transaction;
      Result::calculateDueToEvent($transaction, []);
    }
}
