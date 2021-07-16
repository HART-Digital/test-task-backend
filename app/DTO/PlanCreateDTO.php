<?php

namespace App\DTO;

use App\VO\PlanOptions\MiddleCutHeight;
use App\VO\PlanOptions\Resolution;
use App\VO\PlanOptions\Styles;
use App\VO\PlanOptions\TopDownViewCount;
use Illuminate\Http\UploadedFile;

final class PlanCreateDTO
{
    private $plan;
    private $mask;
    private $log;
    private $disablePanoramas;
    private $disableTopViews;
    private $disableFurniture;
    private $disableUnreal;
    private $disableMiddleCut;
    private $topDownViewCount;
    private $styles;
    private $resolution;
    private $middleCutHeight;

    public function __construct(
        UploadedFile $plan,
        ?UploadedFile $mask,
        bool $log,
        bool $disablePanoramas,
        bool $disableTopViews,
        bool $disableFurniture,
        bool $disableUnreal,
        bool $disableMiddleCut,
        TopDownViewCount $topDownViewCount,
        Styles $styles,
        Resolution $resolution,
        MiddleCutHeight $middleCutHeight
    ) {
        $this->plan = $plan;
        $this->mask = $mask;
        $this->log = $log;
        $this->disablePanoramas = $disablePanoramas;
        $this->disableTopViews = $disableTopViews;
        $this->disableFurniture = $disableFurniture;
        $this->disableUnreal = $disableUnreal;
        $this->disableMiddleCut = $disableMiddleCut;
        $this->topDownViewCount = $topDownViewCount;
        $this->styles = $styles;
        $this->resolution = $resolution;
        $this->middleCutHeight = $middleCutHeight;
    }

    public function getPlan(): UploadedFile
    {
        return $this->plan;
    }

    public function getMask(): ?UploadedFile
    {
        return $this->mask;
    }

    public function isLog(): bool
    {
        return $this->log;
    }

    public function isDisablePanoramas(): bool
    {
        return $this->disablePanoramas;
    }

    public function isDisableTopViews(): bool
    {
        return $this->disableTopViews;
    }

    public function isDisableFurniture(): bool
    {
        return $this->disableFurniture;
    }

    public function isDisableUnreal(): bool
    {
        return $this->disableUnreal;
    }

    public function isDisableMiddleCut(): bool
    {
        return $this->disableMiddleCut;
    }

    public function getTopDownViewCount(): TopDownViewCount
    {
        return $this->topDownViewCount;
    }

    public function getStyles(): Styles
    {
        return $this->styles;
    }

    public function getResolution(): Resolution
    {
        return $this->resolution;
    }

    public function getMiddleCutHeight(): MiddleCutHeight
    {
        return $this->middleCutHeight;
    }
}
