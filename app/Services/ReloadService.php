<?php

namespace App\Services;

use App\Actions\CreatePlanAction;
use App\DTO\StepsDTO;
use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use App\VO\PlanOptions\MiddleCutHeight;
use App\VO\PlanOptions\Resolution;
use App\VO\PlanOptions\Styles;
use App\VO\PlanOptions\TopDownViewCount;
use Illuminate\Http\UploadedFile;

class ReloadService
{
    private Plan $plan;
    private StepsDTO $dto;
    private CreatePlanAction $createPlanAction;

    public function __construct(Plan $plan, StepsDTO $stepsDTO, CreatePlanAction $createPlanAction)
    {
        $this->plan = $plan;
        $this->dto = $stepsDTO;
        $this->createPlanAction = $createPlanAction;
    }

    public function reload(string $planId): Plan
    {
        $this->plan = Plan::findOrFail($planId);
        $this->setDtoForReload();
        return $this->createPlanAction->execute($this->dto);
    }

    private function setDtoForReload(): void
    {
        $this->setDtoOptions();
        $this->setDtoFiles();
    }

    private function setDtoOptions(): void
    {
        $styles = Styles::create([$this->plan->options['unrealStyles']]);
        $resolution = Resolution::create($this->plan->options['unrealResolution']);
        $middleCutHeight = MiddleCutHeight::create($this->plan->options['unrealMiddleCutHeight']);
        $topDownViewCount = TopDownViewCount::create($this->plan->options['unrealTopDownViewCount']);

        $this->dto->setSteps($this->plan->options['steps']);
        $this->dto->setIsNeuralLogEnabled($this->plan->options['isNeuralLogEnabled']);
        $this->dto->setIsUnrealPanoramasCaptureEnabled($this->plan->options['isUnrealPanoramasCaptureEnabled']);
        $this->dto->setIsUnrealTopViewsCaptureEnabled($this->plan->options['isUnrealTopViewsCaptureEnabled']);
        $this->dto->setUnrealTopDownViewCount($topDownViewCount);
        $this->dto->setIsUnrealMiddleCutCaptureEnabled($this->plan->options['isUnrealMiddleCutCaptureEnabled']);
        $this->dto->setUnrealMiddleCutHeight($middleCutHeight);
        $this->dto->setUnrealResolution($resolution);
        $this->dto->setUnrealStyles($styles);
    }

    private function setDtoFiles(): void
    {
        $firstStep = $this->plan->options['steps'][0];

        $this->uploadPlan();

        switch ($firstStep) {
            case StepTypes::NEURAL:
                $this->uploadMask();
                break;
            case StepTypes::FURNITURE:
                $this->uploadJsonForFurniture();
                break;
            case StepTypes::UNREAL:
                $this->uploadJsonForUnreal();
                break;
        }
    }

    private function uploadPlan(): void
    {
        if ($this->plan->paths[StepTypes::PLAN]) {
            $file = $this->createFile(StepTypes::PLAN);
            $this->dto->setPlan($file);
        }
    }

    private function uploadMask(): void
    {
        if ($this->plan->paths[StepTypes::MASK]) {
            $file = $this->createFile(StepTypes::MASK);
            $this->dto->setMask($file);
        }
    }

    private function createFile(string $type): UploadedFile
    {
        $path = \Storage::disk('public')->path($this->plan->paths[$type]);
        return new UploadedFile($path, basename($this->plan->paths[$type]));
    }

    private function uploadJsonForUnreal(): void
    {
        if ($this->plan->paths[StepTypes::FURNITURE]) {
            $this->uploadJson(StepTypes::FURNITURE);
        } elseif ($this->plan->paths[StepTypes::NEURAL]) {
            $this->uploadJson(StepTypes::NEURAL);
        }
    }

    private function uploadJson(string $type): void
    {
        $file = $this->createFile($type);
        $this->dto->setJson($file);
    }

    private function uploadJsonForFurniture(): void
    {
        if ($this->plan->paths[StepTypes::NEURAL]) {
            $this->uploadJson(StepTypes::NEURAL);
        }
    }
}
