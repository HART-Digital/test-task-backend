<?php

use Illuminate\Support\Facades\Route;

Route::prefix('plans')
    ->name('plans.')
    ->group(__DIR__ . '/api/plans.php');

Route::prefix('steps')
    ->name('plans.steps.')
    ->group(__DIR__ . '/api/steps.php');

Route::prefix('auth')
    ->name('auth.')
    ->group(__DIR__ . '/api/auth.php');

Route::prefix('users')
    ->name('users.')
    ->group(__DIR__ . '/api/users.php');

Route::prefix('service')
    ->name('service.')
    ->group(__DIR__ . '/api/service.php');
