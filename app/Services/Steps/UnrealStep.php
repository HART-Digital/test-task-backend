<?php

namespace App\Services\Steps;

use App\Exceptions\Services\StepsServiceException;
use App\Enums\Steps\StepTypes;

class UnrealStep extends Step
{
    public static function type(): string
    {
        return StepTypes::UNREAL;
    }

    public static function canContinueAfterError(): bool
    {
        return true;
    }

    public function start(): void
    {
        $this->createDirectory();
        $this->dispatchJenkinsRequestJob();
    }

    protected static function configName(): string
    {
        return 'jenkins.webhooks.unreal';
    }

    protected static function webhookName(): string
    {
        return 'webhook_status_code_unreal';
    }

    protected function getPostData(): array
    {
        $data = [
            'unreal_path' => config('jenkins.paths.unreal'),
            'master_destination' => $this->storage->path($this->projectDir(StepTypes::UNREAL)),
            'disable_panoramas' => (int)!$this->plan->options['isUnrealPanoramasCaptureEnabled'],
            'disable_top_views' => (int)!$this->plan->options['isUnrealTopViewsCaptureEnabled'],
            'disable_middle_cut' => (int)!$this->plan->options['isUnrealMiddleCutCaptureEnabled'],
            'top_down_view_count' => $this->plan->options['unrealTopDownViewCount'],
            'styles' => $this->plan->options['unrealStyles'],
            'resolution' => $this->plan->options['unrealResolution'],
            'middle_cut_height' => $this->plan->options['unrealMiddleCutHeight'],
            'json_link' => $this->getJsonLink(),
        ];

        return array_merge($this->callbackHooks(), $data);
    }

    private function getJsonLink(): string
    {
        $paths = $this->plan->paths;

        $jsonPath = $paths[StepTypes::FURNITURE] ?? $paths[StepTypes::NEURAL] ?? null;

        if ($jsonPath === null) {
            throw new StepsServiceException("Json file for unreal is not exists.");
        }

        return $this->storage->url($jsonPath);
    }

    public function finish(): void
    {
        $fsPaths = $this->getPaths();

        $this->plan->setPathsKey(static::type(), $fsPaths);

        $this->addPanoramasInHiddenPaths($fsPaths);
    }

    public function addPanoramasInHiddenPaths(array $fsPaths)
    {
        $panoramas = array_filter($fsPaths, fn($path) => str_contains($path, 'Panorama'));

        $this->plan->hidden_paths = array_merge($this->plan->hidden_paths, $panoramas);
    }
}
