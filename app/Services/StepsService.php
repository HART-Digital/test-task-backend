<?php

namespace App\Services;

use App\Enums\Steps\StepStatus;
use App\Models\Plan;
use App\Services\Steps\FurnitureStep;
use App\Services\Steps\NeuralStep;
use App\Services\Steps\Step;
use App\Services\Steps\UnrealStep;
use Illuminate\Contracts\Filesystem\Filesystem;
use Storage;
use App\Enums\Steps\StepTypes;
use App\Exceptions\Services\StepsServiceException;

final class StepsService
{
    private Plan $plan;
    private Filesystem $storage;
    private StepsNotificationsService $stepsNotificationsService;

    private const STEP_TYPE_MAP = [
        StepTypes::NEURAL => NeuralStep::class,
        StepTypes::FURNITURE => FurnitureStep::class,
        StepTypes::UNREAL => UnrealStep::class,
    ];

    public function __construct(StepsNotificationsService $stepsNotificationsService)
    {
        $this->storage = Storage::disk('public');
        $this->stepsNotificationsService = $stepsNotificationsService;
    }

    public function start(string $planId): Plan
    {
        $this->plan = Plan::findOrFail($planId);

        $step = $this->getCurrentStep();

        $this->startProcess($step);

        $this->plan->save();

        return $this->plan;
    }

    private function startProcess(Step $step): void
    {
        $step->start();
        $this->plan->setStatusKey($step::type(), StepStatus::PROCESS);
    }

    private function getCurrentStep(): ?Step
    {
        $type = $this->plan->additional['currentStep'];

        if ($type) {
            return $this->getStepByType($type);
        }

        return null;
    }

    private function getStepByType(string $type): Step
    {
        if (!isset(self::STEP_TYPE_MAP[$type])) {
            throw new StepsServiceException('Step is not found.');
        }

        $stepClass = self::STEP_TYPE_MAP[$type];
        return new $stepClass($this->plan, $this->storage);
    }

    public function continue(string $planId): Plan
    {
        $this->plan = Plan::findOrFail($planId);

        if ($this->isLastStep()) {
            return $this->plan;
        }

        $step = $this->getCurrentStep();
        $this->finishProcess($step);
        $this->goToNextStepAndStartProcessIfIsNotLastStep();
        $this->plan->save();

        return $this->plan;
    }

    private function isLastStep(): bool
    {
        return $this->getCurrentStep() === null;
    }

    private function goToNextStepAndStartProcessIfIsNotLastStep(): void
    {
        $this->goToNextStep();

        if (!$this->isLastStep()) {
            $step = $this->getCurrentStep();
            $this->startProcess($step);
        }
    }

    private function finishProcess(Step $step): void
    {
        $step->finish();
        $this->plan->setStatusKey($step::type(), StepStatus::FINISH);
        $this->dispatchSuccessEvent($step::type());
    }

    private function goToNextStep(): ?string
    {
        $prevStep = $this->plan->getCurrentStep();
        $steps = $this->plan->options['steps'];

        $stepsIndices = array_flip($steps);
        $prevStepIndex = $stepsIndices[$prevStep];
        $nextStep = $steps[$prevStepIndex + 1] ?? null;

        $this->plan->setCurrentStep($nextStep);

        return $nextStep;
    }

    public function fail(string $planId): Plan
    {
        $this->plan = Plan::findOrFail($planId);

        $step = $this->getCurrentStep();
        $this->failProcess($step);

        if ($step::canContinueAfterError()) {
            $this->goToNextStepAndStartProcessIfIsNotLastStep();
        }

        $this->plan->save();
        return $this->plan;
    }

    private function failProcess(Step $step): void
    {
        $this->plan->setStatusKey($step::type(), StepStatus::ERROR);
        $this->dispatchFailEvent($step::type());
    }

    private function dispatchSuccessEvent(string $type)
    {
        $this->stepsNotificationsService->setType($type);
        $this->stepsNotificationsService->setPlanId($this->plan->id);
        $this->stepsNotificationsService->sendSuccessNotification();
    }

    private function dispatchFailEvent(string $type)
    {
        $this->stepsNotificationsService->setType($type);
        $this->stepsNotificationsService->setPlanId($this->plan->id);
        $this->stepsNotificationsService->sendFailureNotification();
    }
}
