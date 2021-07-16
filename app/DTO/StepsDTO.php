<?php

namespace App\DTO;

use App\VO\PlanOptions\MiddleCutHeight;
use App\VO\PlanOptions\Resolution;
use App\VO\PlanOptions\Styles;
use App\VO\PlanOptions\TopDownViewCount;
use Illuminate\Http\UploadedFile;

final class StepsDTO
{
    private array $steps;
    private ?UploadedFile $plan;
    private ?UploadedFile $mask;
    private bool $isNeuralLogEnabled;
    private ?UploadedFile $json;
    private bool $isUnrealPanoramasCaptureEnabled;
    private bool $isUnrealTopViewsCaptureEnabled;
    private TopDownViewCount $unrealTopDownViewCount;
    private bool $isUnrealMiddleCutCaptureEnabled;
    private MiddleCutHeight $unrealMiddleCutHeight;
    private Resolution $unrealResolution;
    private Styles $unrealStyles;

    /**
     * DEFAULTS
     */
    public function __construct()
    {
        $this->steps = [];
        $this->plan = null;
        $this->mask = null;
        $this->isNeuralLogEnabled = false;
        $this->json = null;
        $this->isUnrealPanoramasCaptureEnabled = false;
        $this->isUnrealTopViewsCaptureEnabled = false;
        $this->unrealTopDownViewCount = TopDownViewCount::create(1);
        $this->isUnrealMiddleCutCaptureEnabled = false;
        $this->unrealMiddleCutHeight = MiddleCutHeight::create(150);
        $this->unrealResolution = Resolution::create(512);
        $this->unrealStyles = Styles::create([0]);
    }

    /**
     * @return array
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param array $steps
     * @return StepsDTO
     */
    public function setSteps(array $steps): StepsDTO
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getPlan(): ?UploadedFile
    {
        return $this->plan;
    }

    /**
     * @param UploadedFile $plan
     * @return StepsDTO
     */
    public function setPlan(UploadedFile $plan): StepsDTO
    {
        $this->plan = $plan;
        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getMask(): ?UploadedFile
    {
        return $this->mask;
    }

    /**
     * @param UploadedFile $mask
     * @return StepsDTO
     */
    public function setMask(UploadedFile $mask): StepsDTO
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNeuralLogEnabled(): bool
    {
        return $this->isNeuralLogEnabled;
    }

    /**
     * @param bool $isNeuralLogEnabled
     * @return StepsDTO
     */
    public function setIsNeuralLogEnabled(bool $isNeuralLogEnabled): StepsDTO
    {
        $this->isNeuralLogEnabled = $isNeuralLogEnabled;
        return $this;
    }

    /**
     * @return UploadedFile|null
     */
    public function getJson(): ?UploadedFile
    {
        return $this->json;
    }

    /**
     * @param UploadedFile $json
     * @return StepsDTO
     */
    public function setJson(UploadedFile $json): StepsDTO
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnrealPanoramasCaptureEnabled(): bool
    {
        return $this->isUnrealPanoramasCaptureEnabled;
    }

    /**
     * @param bool $isUnrealPanoramasCaptureEnabled
     * @return StepsDTO
     */
    public function setIsUnrealPanoramasCaptureEnabled(bool $isUnrealPanoramasCaptureEnabled): StepsDTO
    {
        $this->isUnrealPanoramasCaptureEnabled = $isUnrealPanoramasCaptureEnabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnrealTopViewsCaptureEnabled(): bool
    {
        return $this->isUnrealTopViewsCaptureEnabled;
    }

    /**
     * @param bool $isUnrealTopViewsCaptureEnabled
     * @return StepsDTO
     */
    public function setIsUnrealTopViewsCaptureEnabled(bool $isUnrealTopViewsCaptureEnabled): StepsDTO
    {
        $this->isUnrealTopViewsCaptureEnabled = $isUnrealTopViewsCaptureEnabled;
        return $this;
    }

    /**
     * @return TopDownViewCount
     */
    public function getUnrealTopDownViewCount(): TopDownViewCount
    {
        return $this->unrealTopDownViewCount;
    }

    /**
     * @param TopDownViewCount $unrealTopDownViewCount
     * @return StepsDTO
     */
    public function setUnrealTopDownViewCount(TopDownViewCount $unrealTopDownViewCount): StepsDTO
    {
        $this->unrealTopDownViewCount = $unrealTopDownViewCount;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnrealMiddleCutCaptureEnabled(): bool
    {
        return $this->isUnrealMiddleCutCaptureEnabled;
    }

    /**
     * @param bool $isUnrealMiddleCutCaptureEnabled
     * @return StepsDTO
     */
    public function setIsUnrealMiddleCutCaptureEnabled(bool $isUnrealMiddleCutCaptureEnabled): StepsDTO
    {
        $this->isUnrealMiddleCutCaptureEnabled = $isUnrealMiddleCutCaptureEnabled;
        return $this;
    }

    /**
     * @return MiddleCutHeight
     */
    public function getUnrealMiddleCutHeight(): MiddleCutHeight
    {
        return $this->unrealMiddleCutHeight;
    }

    /**
     * @param MiddleCutHeight $unrealMiddleCutHeight
     * @return StepsDTO
     */
    public function setUnrealMiddleCutHeight(MiddleCutHeight $unrealMiddleCutHeight): StepsDTO
    {
        $this->unrealMiddleCutHeight = $unrealMiddleCutHeight;
        return $this;
    }

    /**
     * @return Resolution
     */
    public function getUnrealResolution(): Resolution
    {
        return $this->unrealResolution;
    }

    /**
     * @param Resolution $unrealResolution
     * @return StepsDTO
     */
    public function setUnrealResolution(Resolution $unrealResolution): StepsDTO
    {
        $this->unrealResolution = $unrealResolution;
        return $this;
    }

    /**
     * @return Styles
     */
    public function getUnrealStyles(): Styles
    {
        return $this->unrealStyles;
    }

    /**
     * @param Styles $unrealStyles
     * @return StepsDTO
     */
    public function setUnrealStyles(Styles $unrealStyles): StepsDTO
    {
        $this->unrealStyles = $unrealStyles;
        return $this;
    }
}
