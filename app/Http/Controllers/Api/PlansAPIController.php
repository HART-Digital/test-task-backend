<?php

namespace App\Http\Controllers\Api;

use App\Services\PlansService\PlansService;
use App\Services\ReloadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Illuminate\Http\JsonResponse;

class PlansAPIController extends APIController
{
    private PlansService $plansService;
    private ReloadService $reloadService;

    public function __construct(
        PlansService $plansService,
        ReloadService $reloadService
    ) {
        $this->plansService = $plansService;
        $this->reloadService = $reloadService;
    }

    public function downloadAlbum(string $id): BinaryFileResponse
    {

        dd(112);
        if (!Uuid::isValid($id)) {
            abort(404);
        }

        $path = $this->plansService->makeArchiveWithUnrealImages($id);

        if ($path === null) {
            abort(404);
        }

        $response = response()->file($path);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "plan_images_{$id}.zip"
        );

        return $response;
    }

    public function makePublic(string $id): Response
    {
        $this->plansService->makePublic($id);
        return response()->noContent();
    }

    public function makePrivate(string $id): Response
    {
        $this->plansService->makePrivate($id);
        return response()->noContent();
    }

    public function delete(string $id): Response
    {
        $this->plansService->delete($id);
        return response()->noContent();
    }

    public function makePathsHidden(Request $request, string $id): Response
    {
        $request->validate(
            [
                'paths' => 'required|array',
                'paths.*' => 'string',
            ]
        );

        $paths = $this->plansService->makePathsFromUrls($id, $request->paths);
        $this->plansService->makePathsHidden($id, $paths);

        return response()->noContent();
    }

    public function makePathsVisible(Request $request, string $id): Response
    {
        $request->validate(
            [
                'paths' => 'required|array',
                'paths.*' => 'string',
            ]
        );

        $paths = $this->plansService->makePathsFromUrls($id, $request->paths);
        $this->plansService->makePathsVisible($id, $paths);

        return response()->noContent();
    }

    public function index(): JsonResponse
    {
        dd(1);
        $data = $this->plansService->getList();
        return response()->json($data);
    }

    public function show(string $id): JsonResponse
    {
        $data = $this->plansService->getPlan($id);

        return response()->json($data);
    }

    public function reloadPlan(string $id): JsonResponse
    {
        $reloadedPlan = $this->reloadService->reload($id);
        return response()->json($reloadedPlan);
    }

    public function getMeta(string $id): JsonResponse
    {
        $meta = $this->plansService->getMeta($id);

        return response()->json($meta);
    }

    public function showPublic(string $id): JsonResponse
    {
        return new JsonResponse($this->plansService->getPublicPlan($id));
    }
}
