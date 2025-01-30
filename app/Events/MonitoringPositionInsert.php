<?php

namespace App\Events;

use App\MonitoringPosition;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MonitoringPositionInsert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $position;

    /**
     * Create a new event instance.
     *
     * @param MonitoringPosition $position
     */
    public function __construct(MonitoringPosition $position)
    {
        $position->save();

        $this->position = $position;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('monitoring');
    }
}
