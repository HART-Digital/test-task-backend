<?php

namespace App\Events\Plan;

use App\Models\Plan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanCreatedEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public Plan $plan;

    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }
}
