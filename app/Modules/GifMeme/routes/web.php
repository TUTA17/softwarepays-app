<?php

use Illuminate\Support\Facades\Route;
use App\Modules\GifMeme\Controllers\Theme\GifController;

Route::middleware(['web'])->group(function () {
    Route::get('/Gifs', [GifController::class, 'index'])->name('Gifs.index');
    Route::get('/Gifs/{slug}', [GifController::class, 'show'])->name('Gifs.show');
    Route::post('/Gifs/{slug}/play', [GifController::class, 'play'])->name('Gifs.play')->middleware('throttle:Gif-engagement');
    Route::post('/Gifs/{slug}/like', [GifController::class, 'like'])->name('Gifs.like')->middleware('throttle:Gif-engagement');
    Route::post('/Gifs/{slug}/share', [GifController::class, 'share'])->name('Gifs.share')->middleware('throttle:Gif-engagement');
    Route::get('/Gifs/{slug}/download', [GifController::class, 'download'])->name('Gifs.download')->middleware('throttle:Gif-engagement');
});


