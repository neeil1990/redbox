<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MonitoringProjectBeforeDelete
{
    use SerializesModels;

    public $user;
    public $project;

    public function __construct($user, $project)
    {
        $this->user = $user;
        $this->project = $project;
    }

}
