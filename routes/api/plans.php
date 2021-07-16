<?php

use App\Http\Controllers\Api\PlansAPIController;

Route::get('/', [PlansAPIController::class, 'index']);

Route::get('/{id}/public', [PlansAPIController::class, 'showPublic']);

Route::get('/{id}/download_album', [PlansAPIController::class, 'downloadAlbum'])->name('download_album');

Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('/{id}', [PlansAPIController::class, 'show'])->name('show');

        Route::delete('/{id}', [PlansAPIController::class, 'delete'])->name('delete');

        Route::post('/{id}/make_paths_hidden', [PlansAPIController::class, 'makePathsHidden'])
            ->name('make_paths_hidden');
        Route::post('/{id}/make_paths_visible', [PlansAPIController::class, 'makePathsVisible'])
            ->name('make_paths_visible');

        Route::post('/{id}/make_public', [PlansAPIController::class, 'makePublic'])->name('make_public');
        Route::post('/{id}/make_private', [PlansAPIController::class, 'makePrivate'])->name('make_private');

        Route::get('/{id}/meta', [PlansAPIController::class, 'getMeta'])->name('meta');
    }
);

Route::post('/{id}/reload', [PlansAPIController::class, 'reloadPlan'])->name('reload_plan');
