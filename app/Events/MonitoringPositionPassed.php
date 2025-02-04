<?php

namespace App\Events;

use App\MonitoringKeyword;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MonitoringPositionPassed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $key;
    public $date;

    /**
     * Create a new event instance.
     *
     * @param MonitoringKeyword $keyword
     * @param $date
     */
    public function __construct(MonitoringKeyword $keyword, $date)
    {
        $this->key = $keyword;
        $this->date= $date;
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
