<?php

namespace App\Services;

use App\Events\PlanEvent;
use App\Enums\Steps\StepTypes;

class StepsNotificationsService
{
    private const STEPS_SUCCESS_CODES = [
        StepTypes::NEURAL => 101,
        StepTypes::FURNITURE => 102,
        StepTypes::UNREAL => 103,
    ];

    private const STEPS_FAILURE_CODES = [
        StepTypes::NEURAL => 201,
        StepTypes::FURNITURE => 202,
        StepTypes::UNREAL => 203,
    ];

    private string $type;
    private string $planId;
    private int $code;
    private string $message;

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setPlanId(string $planId): void
    {
        $this->planId = $planId;
    }

    public function sendSuccessNotification(): void
    {
        if (isset($this->type, $this->planId)) {
            $this->code = self::STEPS_SUCCESS_CODES[$this->type];
            $this->message = "success:{$this->type}";
            $this->sendNotification();
        }
    }

    private function sendNotification(): void
    {
        $event = new PlanEvent($this->code, $this->planId, $this->message);
        event($event);
    }

    public function sendFailureNotification(): void
    {
        if (isset($this->type, $this->planId)) {
            $this->code = self::STEPS_FAILURE_CODES[$this->type];
            $this->message = "failure:{$this->type}";
            $this->sendNotification();
        }
    }
}
