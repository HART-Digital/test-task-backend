<?php

namespace App\Services\PlansService;

use App\Actions\CreatePlanAction;
use App\DTO\StepsDTO;
use App\Enums\Steps\StepStatus;
use App\Exceptions\Services\PlansServiceException;
use App\Models\Plan;
use App\Enums\Steps\StepTypes;
use Storage;
use Exception;
use ZipArchive;
use Illuminate\Contracts\Filesystem\Filesystem;

final class PlansService
{
    private ?Plan $plan = null;
    private FileSystem $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('public');
    }

    private function plan(string $id): Plan
    {
        if (isset($this->plan) && $this->plan->id === $id) {
            return $this->plan;
        }

        $this->plan = Plan::findOrFail($id);
        return $this->plan;
    }

    /**
     * Создание архива с изображениями планировки. Возвращает полный путь до архива
     *
     * @param string $planId
     * @return string|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function makeArchiveWithUnrealImages(string $planId): ?string
    {
        $archivesDir = 'archives/projects';

        $storage = Storage::disk('public');

        $archiveName = "{$archivesDir}/{$planId}.zip";

        if ($storage->exists($archiveName)) {
            $storage->delete($archiveName);
        }

        $paths = Plan::findOrFail($planId)->paths[StepTypes::UNREAL];
        if (!$paths) {
            return null;
        }

        if (!$storage->exists($archivesDir)) {
            $storage->makeDirectory($archivesDir);
        }

        $zip = new ZipArchive();

        $zip->open($storage->path($archiveName), ZipArchive::CREATE);

        foreach ($paths as $path) {
            $explodedPath = explode(DIRECTORY_SEPARATOR, $path);

            $basename = array_pop($explodedPath);
            $style = array_pop($explodedPath);

            $filename = implode('_', [$style, $basename]);

            $zip->addFromString($filename, $storage->get($path));
        }

        $zip->close();

        return $storage->path($archiveName);
    }

    public function delete(string $id): ?bool
    {
        $plan = Plan::findOrFail($id);

        $this->deletePlanRelatedFiles($plan);

        return $plan->delete();
    }

    private function deletePlanRelatedFiles(Plan $plan): void
    {
        $this->deletePlanProjectsDir($plan);
        $this->deletePlanLogsDir($plan);
        $this->deletePlanPreviewsDir($plan);
        $this->deletePlanArchive($plan);
    }

    private function deletePlanProjectsDir(Plan $plan): void
    {
        $dir = $this->getPlanProjectsDir($plan);
        Storage::disk('public')->deleteDirectory($dir);
    }

    public function getPlanProjectsDir(Plan $plan): string
    {
        $dir = 'projects';
        return "{$dir}/{$plan->id}";
    }

    private function deletePlanLogsDir(Plan $plan): void
    {
        $dir = $this->getPlanLogsDir($plan);
        Storage::disk('public')->deleteDirectory($dir);
    }

    public function getPlanLogsDir(Plan $plan): string
    {
        $dir = 'logs';
        return "{$dir}/{$plan->id}";
    }

    private function deletePlanPreviewsDir(Plan $plan): void
    {
        $previewsDir = $this->getPlanPreviewsDir($plan);
        Storage::disk('public')->deleteDirectory($previewsDir);
    }

    public function getPlanPreviewsDir(Plan $plan): string
    {
        $dir = 'previews/projects';
        return "{$dir}/{$plan->id}";
    }

    private function deletePlanArchive(Plan $plan): void
    {
        $archiveName = $this->getPlanUnrealImagesArchiveName($plan);
        Storage::disk('public')->delete($archiveName);
    }

    public function getPlanUnrealImagesArchiveName(Plan $plan): string
    {
        $dir = 'archives/projects';
        return "{$dir}/{$plan->id}.zip";
    }

    public function getList(): array
    {
        return Plan::latest()->normalizedPaginate(
            function (Plan $plan) {
                $paths = $plan->paths;
                $pathsLinks = array_map(fn($item) => $this->makeUrls($item), $paths);

                return [
                    'id' => $plan->id,
                    'paths' => $pathsLinks,
                    'steps' => $plan->options['steps'],
                    'status' => $plan->status,
                ];
            }
        );
    }

    /**
     * @param array|string $item
     * @return array|string
     */
    private function makeUrls($item)
    {
        if (is_array($item)) {
            return array_map(fn($i) => $this->makeUrls($i), $item);
        }

        return $item ? Storage::url($item) : null;
    }

    public function getPublicPlan(string $id): array
    {
        $plan = $this->getPlan($id);

        if (!$plan['public']) {
            throw new PlansServiceException('Public plan not found', 404);
        }

        $unrealPaths = array_values(
            array_filter(
                $plan['paths']['unreal'],
                fn($path) => !in_array($path, $plan['hiddenPaths'])
            )
        );
        $links = collect($plan['links'])->only('model', 'panorama');

        return [
            'id' => $plan['id'],
            'paths' => [
                'plan' => $plan['paths']['plan'],
                'unreal' => $unrealPaths,
            ],
            'links' => $links->toArray(),
            'hasModel' => $plan['hasModel'],
            'hasPanorama' => $plan['hasPanorama'],
        ];
    }

    public function getPlan(string $id): array
    {
        $plan = Plan::findOrFail($id);

        $paths = $this->addNecessaryFields($plan->paths);

        return [
            'id' => $plan->id,
            'links' => $this->getPlanLinks($id),
            'paths' => $this->makeUrls($paths),
            'steps' => $plan->options['steps'],
            'public' => $plan->public,
            'status' => $plan->status,
            'hiddenPaths' => $this->makeUrls($plan->hidden_paths),
            'hasModel' => $this->planHasModel($plan),
            'hasPanorama' => $this->planHasPanorama($plan),
        ];
    }

    public function getMeta(string $id): array
    {
        $plan = Plan::findOrFail($id);

        $options = array_filter($plan->options, fn($k) => $k !== 'steps', ARRAY_FILTER_USE_KEY);
        $additional = array_filter($plan->additional, fn($k) => $k !== 'currentStep', ARRAY_FILTER_USE_KEY);

        return [
            'options' => $options,
            'additional' => $additional,
            'createdAt' => $plan->created_at,
            'updatedAt' => $plan->updated_at,
        ];
    }

    private function addNecessaryFields(array $paths): array
    {
        foreach (Plan::initialPaths() as $key => $value) {
            if (!isset($paths[$key])) {
                $paths[$key] = $value;
            }
        }

        return $paths;
    }

    public function getPlanLinks(string $id): array
    {
        $frontURL = config('app.frontend_url');
        $editorURL = config('app.editor_url');
        $svgURL = config('app.svg_url');
        $query = "?id={$id}&env=" . app()->environment();

        return [
            'public' => null,
            'model' => "{$frontURL}/model{$query}",
            'panorama' => "{$frontURL}/panorama{$query}",
            'neural' => [
                'editor' => $editorURL . $query,
                'svg' => $svgURL . $query,
            ],
        ];
    }

    private function planHasModel(Plan $plan): bool
    {
        return in_array(
            StepStatus::FINISH,
            [
                $plan->status[StepTypes::NEURAL] ?? null,
                $plan->status[StepTypes::FURNITURE] ?? null,
            ]
        );
    }

    private function planHasPanorama(Plan $plan): bool
    {
        return strpos(implode('', $plan->paths[StepTypes::UNREAL] ?? []), 'Panorama') !== false;
    }

    public function makePublic(string $id): void
    {
        Plan::whereId($id)->update(
            [
                'public' => true,
            ]
        );
    }

    public function makePrivate(string $id): void
    {
        Plan::whereId($id)->update(
            [
                'public' => false,
            ]
        );
    }

    public function makePathsFromUrls(string $id, array $urls): array
    {
        $result = [];

        foreach ($urls as $url) {
            $path = $this->makePathFromUrl($id, $url);
            if ($path) {
                $result[] = $path;
            }
        }

        return $result;
    }

    public function makePathFromUrl(string $id, string $url): ?string
    {
        $plan = $this->plan($id);

        $paths = \Arr::flatten($plan->paths);

        foreach ($paths as $path) {
            if (\Storage::url($path) === $url) {
                return $path;
            }
        }

        return null;
    }

    public function makePathsHidden(string $id, array $paths): void
    {
        $plan = $this->plan($id);

        $this->checkPathsExists($paths);

        $hiddenPaths = $plan->hidden_paths;
        $hiddenPaths = [
            ...$hiddenPaths,
            ...$paths,
        ];
        $plan->hidden_paths = $hiddenPaths;

        $plan->save();
    }

    private function checkPathsExists(array $paths): bool
    {
        $planPaths = \Arr::flatten($this->plan->paths);

        foreach ($paths as $path) {
            if (!in_array($path, $planPaths)) {
                throw new PlansServiceException('Path not found');
            }
        }

        return true;
    }

    public function makePathsVisible(string $id, array $paths): void
    {
        $plan = $this->plan($id);

        $this->checkPathsExists($paths);

        $plan->hidden_paths = array_filter($plan->hidden_paths, fn($p) => !in_array($p, $paths));

        $plan->save();
    }

    public function getDataForEditor(string $id): array
    {
        $plan = $this->plan($id);

        return collect($plan->paths)
            ->only(StepTypes::NEURAL, StepTypes::FURNITURE, StepTypes::PLAN)
            ->filter()
            ->toArray();
    }

    public function resaveJson(string $json, string $planId): void
    {
        $plan = Plan::findOrFail($planId);
        $pathOrigin = $plan['paths']['neural'];
        $originPathInfo = pathinfo($pathOrigin);

        $dirOrigin = empty($originPathInfo['dirname']) ? '' : "{$originPathInfo['dirname']}/";
        $filenameOrigin = $originPathInfo['filename'];
        $extOrigin = $originPathInfo['extension'];
        $timestamp = (string)time();

        $pathNew = "${dirOrigin}{$filenameOrigin}${timestamp}.{$extOrigin}";

        if ($this->storage->copy($pathOrigin, $pathNew)) {
            $this->storage->delete($pathOrigin);
            $this->storage->put($pathOrigin, $json);
        } else {
            throw new Exception('File rename failed');
        }
    }

    public function saveJenkinsBuildNumber(string $planId, int $buildNumber, string $jobName): Plan
    {
        $plan = Plan::findOrFail($planId);
        $plan->setAdditionalKey("{$jobName}_build_number", $buildNumber);
        $plan->save();

        return $plan;
    }

    public function create(StepsDTO $dto): Plan
    {
        $action = new CreatePlanAction();
        return $action->execute($dto);
    }

    public function getPanoramasPaths(Plan $plan): array
    {
        $json = $plan['paths'][StepTypes::FURNITURE] ?? $plan['paths'][StepTypes::NEURAL] ?? null;

        $panoramas = array_values(
            array_filter(
                $plan['paths'][StepTypes::UNREAL],
                fn($unreal) => stristr($unreal, 'Panorama')
            )
        );

        $plan = $plan['paths'][StepTypes::PLAN];

        return $this->makeUrls(
            [
                'plan' => $plan,
                'json' => $json,
                'panoramas' => $panoramas,
            ]
        );
    }
}
