<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadArchiveController;

Route::get('/upload', [UploadArchiveController::class, 'uploadForm'])
    ->name('upload');

Route::post('/upload', [UploadArchiveController::class, 'uploadFile2'])
    ->name('upload.uploadfile');

//Route::post('/upload', [UploadArchiveController::class, 'uploadFile3'])
//    ->name('upload.uploadfile');
