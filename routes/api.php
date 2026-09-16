<?php

use App\Http\Controllers\Api\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('videos/{video}/comments', [VideoController::class, 'comments'])->name('videos.comments');
Route::post('videos/{video}/refresh', [VideoController::class, 'refresh'])->name('videos.refresh');
Route::apiResource('videos', VideoController::class);
