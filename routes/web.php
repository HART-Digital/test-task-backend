<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadArchiveController;

Route::get('/upload', [UploadArchiveController::class, 'uploadForm'])
    ->name('upload');

Route::post('/upload', [UploadArchiveController::class, 'uploadFile'])
    ->name('upload.uploadfile');
