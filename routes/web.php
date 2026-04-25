<?php

use Illuminate\Support\Facades\Route;
use PrismPath\Analytics\Http\Controllers\DashboardController;
use PrismPath\Analytics\Http\Controllers\SnippetController;

Route::get('/ultraclarity.js', SnippetController::class)->name('ultraclarity.snippet');

$dashboardPrefix = trim((string) config('ultraclarity.route_prefix'), '/');

Route::prefix(config('ultraclarity.route_prefix'))
    ->middleware(config('ultraclarity.middleware'))
    ->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('ultraclarity.dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('ultraclarity.dashboard.alias');
        Route::get('/data', [DashboardController::class, 'data'])->name('ultraclarity.data');
        Route::get('/docs', [DashboardController::class, 'docs'])->name('ultraclarity.docs');
        Route::get('/section/{section}', [DashboardController::class, 'section'])->name('ultraclarity.section');
        Route::get('/live', [DashboardController::class, 'live'])->name('ultraclarity.live');
        Route::get('/replay/{session}', [DashboardController::class, 'replay'])->name('ultraclarity.replay');
        Route::get('/export/{type}', [DashboardController::class, 'export'])->name('ultraclarity.export');
        Route::get('/report/{format}', [DashboardController::class, 'report'])->name('ultraclarity.report');
    });

if ($dashboardPrefix !== 'prismpath') {
    Route::redirect('/prismpath', '/' . $dashboardPrefix);
    Route::redirect('/prismpath/dashboard', '/' . $dashboardPrefix . '/dashboard');
}

