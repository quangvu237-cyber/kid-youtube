<?php

use App\Http\Controllers\Web\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('videos.web.index');
});

Route::get('videos', [VideoController::class, 'index'])->name('videos.web.index');
Route::get('videos/{video}', [VideoController::class, 'show'])->name('videos.web.show');
