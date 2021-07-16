<?php

namespace App\Listeners;

use App\Events\Plan\PlanCreatedEvent;
use App\Services\StepsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StartPlanProcess implements ShouldQueue
{
    private StepsService $stepsService;

    public function __construct(StepsService $stepsService)
    {
        $this->stepsService = $stepsService;
    }

    public function handle(PlanCreatedEvent $event)
    {
        $this->stepsService->start($event->plan->id);
    }
}
