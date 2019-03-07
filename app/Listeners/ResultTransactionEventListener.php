<?php

namespace App\Listeners;

use App\Events\ResultTransactionEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Result;

class ResultTransactionEventListener
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
     * @param  ResultTransactionEvent  $event
     * @return void
     */
    public function handle(ResultTransactionEvent $event)
    {
        $transaction = $event->transaction;
        $planLog = $event->planLog;
        $planLogType = $event->planLogType;
        $data = $event->data;
        Result::processingResultLog($transaction, $planLog, $data);
    }
}
