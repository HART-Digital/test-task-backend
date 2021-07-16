<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreatePlanAction;
use App\Http\Requests\StartStepsRequest;
use App\Services\ReloadService;
use App\Services\StepsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PlanStepsAPIController extends APIController
{
    private StepsService $stepsService;
    private ReloadService $reloadService;

    public function __construct(StepsService $sp, ReloadService $reloadService)
    {
        $this->stepsService = $sp;
        $this->reloadService = $reloadService;
    }

    public function start(StartStepsRequest $request, CreatePlanAction $action): JsonResponse
    {
        $dto = $request->getDTO();
        $data = $action->execute($dto);
        return response()->json($data);
    }

    public function continue(string $planId): Response
    {
        $this->stepsService->continue($planId);
        return response()->noContent();
    }

    public function fail(string $planId): Response
    {
        $this->stepsService->fail($planId);
        return response()->noContent();
    }

    public function reload(string $planId): JsonResponse
    {
        $plan = $this->reloadService->reload($planId);
        return response()->json($plan);
    }
}
