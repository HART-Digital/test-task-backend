<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanEvent implements ShouldBroadcast, ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $type;
    public $message;
    public $id;
    public $data;

    public function __construct($type, $id, $message, $data = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->id = $id;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return ["plan.{$this->id}"];
    }

    public function broadcastAs()
    {
        return 'plan.event';
    }
}
