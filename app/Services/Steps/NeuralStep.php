<?php

namespace App\Services\Steps;

use App\Enums\Steps\StepTypes;

class NeuralStep extends Step
{
    public static function type(): string
    {
        return StepTypes::NEURAL;
    }

    public static function canContinueAfterError(): bool
    {
        return false;
    }

    public function start(): void
    {
        $this->createDirectory();
        $this->createLogsDirectory();
        $this->dispatchJenkinsRequestJob();
    }

    protected static function configName(): string
    {
        return 'jenkins.webhooks.neural';
    }

    protected static function webhookName(): string
    {
        return 'webhook_status_code_neural';
    }

    private function createLogsDirectory()
    {
        $dir = $this->projectDir(StepTypes::LOGS);
        $this->storage->createDir($dir);
    }

    protected function getPostData(): array
    {
        $data = [
            'neural_path' => config('jenkins.paths.neural'),
            'master_destination' => $this->storage->path($this->projectDir(StepTypes::NEURAL)),
            'master_destination_logs' => $this->storage->path($this->projectDir(StepTypes::LOGS)),
            'log' => $this->plan->options['isNeuralLogEnabled'],
            'plan_link' => null,
            'plan_name' => null,
            'mask_link' => null,
            'mask_name' => null,
        ];

        $path = $this->plan->paths[StepTypes::PLAN];
        $data['plan_link'] = $this->storage->url($path);
        $data['plan_name'] = basename($path);

        if ($this->plan->paths[StepTypes::MASK]) {
            $path = $this->plan->paths[StepTypes::MASK];
            $data['mask_link'] = $this->storage->url($path);
            $data['mask_name'] = basename($path);
        }

        return array_merge($this->callbackHooks(), $data);
    }

    public function finish(): void
    {
        $fsPaths = $this->getPaths();
        $this->plan->setPathsKey(static::type(), $fsPaths[0]);

        $fsLogsPaths = $this->getLogsPaths();
        $this->plan->setPathsKey(StepTypes::LOGS, $fsLogsPaths);
    }

    private function getLogsPaths(): array
    {
        $type = StepTypes::LOGS;
        $dir = $this->projectDir($type);
        return $this->storage->allFiles($dir);
    }
}
