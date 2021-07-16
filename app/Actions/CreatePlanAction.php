<?php

namespace App\Actions;

use App\DTO\StepsDTO;
use App\Enums\Steps\StepStatus;
use App\Exceptions\Actions\ActionException;
use App\Utils\JsonTypeQualifier;
use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use App\Utils\PlanUtils;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Storage;

class CreatePlanAction implements Action
{
    private Plan $plan;
    private StepsDTO $dto;
    private FilesystemAdapter $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('public');
        $this->plan = new Plan();
    }

    public function execute(StepsDTO $dto): Plan
    {
        $this->dto = $dto;

        $this->setPlanOptions();
        $this->initStepsStatusesAsWait();
        $this->initCurrentStep();

        $this->createFromCurrentStep();

        $this->plan->save();

        return $this->plan;
    }

    private function setPlanOptions(): void
    {
        $this->plan->options = [
            'steps' => $this->dto->getSteps(),
            'isNeuralLogEnabled' => $this->dto->isNeuralLogEnabled(),
            'isUnrealPanoramasCaptureEnabled' => $this->dto->isUnrealPanoramasCaptureEnabled(),
            'isUnrealTopViewsCaptureEnabled' => $this->dto->isUnrealTopViewsCaptureEnabled(),
            'unrealTopDownViewCount' => $this->dto->getUnrealTopDownViewCount()->value(),
            'isUnrealMiddleCutCaptureEnabled' => $this->dto->isUnrealMiddleCutCaptureEnabled(),
            'unrealMiddleCutHeight' => $this->dto->getUnrealMiddleCutHeight()->value(),
            'unrealResolution' => $this->dto->getUnrealResolution()->value(),
            'unrealStyles' => $this->dto->getUnrealStyles()->value(),
        ];
    }

    private function initStepsStatusesAsWait(): void
    {
        foreach ($this->dto->getSteps() as $step) {
            $this->plan->setStatusKey($step, StepStatus::WAIT);
        }
    }

    private function initCurrentStep(): void
    {
        $steps = $this->plan->options['steps'];
        $this->plan->setCurrentStep($steps[0]);
    }

    private function createFromCurrentStep(): void
    {
        switch ($this->plan->getCurrentStep()) {
            case StepTypes::NEURAL:
                $this->createFromNeural();
                break;
            case StepTypes::FURNITURE:
                $this->createFromFurniture();
                break;
            case StepTypes::UNREAL:
                $this->createFromUnreal();
                break;
            default:
                throw new ActionException('Что-то тут не так');
        }
    }

    private function createFromNeural(): void
    {
        $this->savePlan();
        $this->saveMask();
    }

    private function savePlan(): void
    {
        if ($this->dto->getPlan()) {
            $path = $this->saveFileByType($this->dto->getPlan(), StepTypes::PLAN);
            $this->plan->setPathsKey(StepTypes::PLAN, $path);
        }
    }

    private function saveFileByType(UploadedFile $file, string $type): string
    {
        $ext = $file->getClientOriginalExtension();
        $nameWithExt = "${type}.${ext}";

        return $this->storage->putFileAs(
            PlanUtils::getTypeDir($this->plan, $type),
            $file,
            $nameWithExt
        );
    }

    private function saveMask(): void
    {
        if ($this->dto->getMask()) {
            $path = $this->saveFileByType($this->dto->getMask(), StepTypes::MASK);
            $this->plan->setPathsKey(StepTypes::MASK, $path);
        }
    }

    private function createFromFurniture(): void
    {
        $this->saveJsonByType(StepTypes::NEURAL);
        $this->savePlan();
    }

    private function saveJsonByType(string $type): void
    {
        if ($this->dto->getJson()) {
            $path = $this->saveFileByType($this->dto->getJson(), $type);
            $this->plan->setPathsKey($type, $path);
        }
    }

    private function createFromUnreal(): void
    {
        $type = JsonTypeQualifier::qualifyUploadedFile($this->dto->getJson());
        $this->saveJsonByType($type);
        $this->savePlan();
    }
}
