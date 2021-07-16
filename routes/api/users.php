<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\RoleHasRouteAccessMiddleware;

Route::middleware(
    [
        'auth:sanctum',
        RoleHasRouteAccessMiddleware::class,
    ]
)->group(
    function () {
        Route::post('/change_role', [UserController::class, 'changeRole'])->name('change_role');
        Route::get('/', [UserController::class, 'list'])->name('list');
    }
);
