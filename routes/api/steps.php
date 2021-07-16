<?php

use App\Http\Controllers\Api\PlanStepsAPIController;

Route::post('/start', [PlanStepsAPIController::class, 'start'])->name('start');
Route::post('/{planId}/continue', [PlanStepsAPIController::class, 'continue'])->name('continue');
Route::post('/{planId}/fail', [PlanStepsAPIController::class, 'fail'])->name('fail');
