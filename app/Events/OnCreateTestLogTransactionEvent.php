<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OnCreateTestLogTransactionEvent extends Event
{
    use SerializesModels;
    public $logs;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($logs)
    {
        $this->logs = $logs;
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
