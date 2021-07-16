<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ResettingPasswordController;

Route::post('/forgot_password', [ResettingPasswordController::class, 'forgot'])->name('forgot_password');
Route::get('/reset_password', [ResettingPasswordController::class, 'reset'])->name('reset_password');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('auth:sanctum')
    ->name('register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::get('/', [AuthController::class, 'me'])
    ->middleware('auth:sanctum')
    ->name('me');
