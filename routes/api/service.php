<?php

use App\Http\Controllers\Api\PlansAPIController;

Route::get('/processed_plans', [PlansAPIController::class, 'getProcessedPlans'])
    ->name('processed_plans');
