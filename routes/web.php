<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', [\App\Http\Controllers\TestController::class, 'index'])->name('test.index');
Route::post('/upload', [\App\Http\Controllers\TestController::class, 'upload'])->name('upload');
