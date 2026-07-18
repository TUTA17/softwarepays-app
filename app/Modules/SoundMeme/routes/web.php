<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SoundMeme\Controllers\Theme\SoundController;

Route::middleware(['web'])->group(function () {
    Route::get('/sounds', [SoundController::class, 'index'])->name('sounds.index');
    Route::get('/sounds/{slug}', [SoundController::class, 'show'])->name('sounds.show');
    Route::post('/sounds/{slug}/play', [SoundController::class, 'play'])->name('sounds.play')->middleware('throttle:sound-engagement');
    Route::post('/sounds/{slug}/like', [SoundController::class, 'like'])->name('sounds.like')->middleware('throttle:sound-engagement');
    Route::post('/sounds/{slug}/share', [SoundController::class, 'share'])->name('sounds.share')->middleware('throttle:sound-engagement');
    Route::get('/sounds/{slug}/download', [SoundController::class, 'download'])->name('sounds.download')->middleware('throttle:sound-engagement');
});
