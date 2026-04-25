<?php

use Illuminate\Support\Facades\Route;
use UltraClarity\Analytics\Http\Controllers\CollectController;

Route::prefix('api/ultraclarity')
    ->middleware(config('ultraclarity.api_middleware'))
    ->group(function (): void {
        Route::post('/collect', [CollectController::class, 'store'])->name('ultraclarity.collect');
        Route::post('/event', [CollectController::class, 'custom'])->name('ultraclarity.event');
    });

