<?php

namespace App\Services\Steps;

use App\Enums\Steps\StepTypes;

class FurnitureStep extends Step
{
    public static function type(): string
    {
        return StepTypes::FURNITURE;
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
        return 'jenkins.webhooks.furniture';
    }

    protected static function webhookName(): string
    {
        return 'webhook_status_code_furniture';
    }

    protected function getPostData(): array
    {
        $data = [
            'furniture_path' => config('jenkins.paths.furniture'),
            'master_destination' => $this->storage->path($this->projectDir(StepTypes::FURNITURE)),
            'json_link' => $this->storage->url($this->plan->paths[StepTypes::NEURAL]),
        ];

        return array_merge($this->callbackHooks(), $data);
    }

    public function finish(): void
    {
        $fsPaths = $this->getPaths();
        $this->plan->setPathsKey(static::type(), $fsPaths[0]);
    }
}
