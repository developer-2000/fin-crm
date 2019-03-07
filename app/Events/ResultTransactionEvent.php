<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Transaction;
use App\Models\Plan;

class ResultTransactionEvent extends Event
{
    use SerializesModels;
    public $transaction;
    public $planLog;
    public $planLogType;
    public $data;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($transaction, $planLog, $data)
    {
        $this->transaction = $transaction;
        $this->planLog = $planLog;
        $this->data = $data;
    }


    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
