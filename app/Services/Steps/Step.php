<?php

namespace App\Services\Steps;

use App\Exceptions\Services\Steps\StepException;
use App\Jobs\SendJenkinsRequestJob;
use App\Utils\JsonTypeQualifier;
use App\Models\Plan;
use App\Utils\PlanUtils;
use Illuminate\Http\UploadedFile;
use Illuminate\Contracts\Filesystem\Filesystem;

abstract class Step
{
    protected Plan $plan;
    protected Filesystem $storage;

    public function __construct(Plan $plan, Filesystem $storage)
    {
        $this->plan = $plan;
        $this->storage = $storage;
    }

    abstract public function start(): void;

    abstract public function finish(): void;

    abstract protected function getPostData(): array;

    abstract public static function type(): string;

    abstract public static function canContinueAfterError(): bool;

    abstract protected static function configName(): string;

    abstract protected static function webhookName(): string;

    protected function createDirectory(): void
    {
        $type = $this->type();
        $dir = $this->projectDir($type);
        $this->storage->createDir($dir);
    }

    protected function getPaths(): array
    {
        $type = $this->type();
        $dir = $this->projectDir($type);
        $paths = $this->storage->allFiles($dir);

        if (count($paths) === 0) {
            throw new StepException("Continue {$type} step error. No {$type} files found.");
        }

        return $paths;
    }

    public static function getJsonType(?UploadedFile $json): string
    {
        return JsonTypeQualifier::qualifyUploadedFile($json);
    }

    protected function saveFile(UploadedFile $file, string $type): array
    {
        $ext = $file->getClientOriginalExtension();
        $nameWithExt = "${type}.${ext}";

        $path = $this->storage->putFileAs(
            $this->projectDir($type),
            $file,
            $nameWithExt
        );

        return [$path, $nameWithExt];
    }

    protected function projectDir(string $type): ?string
    {
        return PlanUtils::getTypeDir($this->plan, $type);
    }

    protected function callbackHooks(): array
    {
        $routeData = [
            'planId' => $this->plan->id,
        ];

        return [
            'callback_hook' => route('api.plans.steps.continue', $routeData),
            'callback_hook_failure' => route('api.plans.steps.fail', $routeData),
        ];
    }

    public static function http(): \Illuminate\Http\Client\PendingRequest
    {
        $username = config('jenkins.username');
        $token = config('jenkins.api_token');

        return \Http::withHeaders(
            [
                'Authorization' => 'Basic ' . base64_encode("${username}:${token}"),
                'Accept' => 'application/json',
            ]
        )->asForm();
    }

    protected function dispatchJenkinsRequestJob(): void
    {
        dispatch(
            new SendJenkinsRequestJob(
                $this->getPostData(),
                static::configName(),
                $this->plan,
                static::webhookName(),
            )
        );
    }
}
