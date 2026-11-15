<?php

use App\Http\Controllers\OsintController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\PreventSharedTesterAccountChanges;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [OsintController::class, 'index'])->name('osint.index');
    Route::get('/dashboard', [OsintController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-metrics', [OsintController::class, 'liveMetrics'])
        ->middleware('throttle:60,1')
        ->name('osint.live-metrics');
    Route::get('/ekspor-intelijen', [OsintController::class, 'exportPdf'])
        ->middleware('throttle:10,1')
        ->name('osint.export.pdf');

    Route::post('/osint/fetch', [OsintController::class, 'fetch'])
        ->middleware('throttle:1,1')
        ->name('osint.fetch');
    Route::post('/osint/test-telegram', [OsintController::class, 'testTelegram'])
        ->middleware('throttle:1,5')
        ->name('osint.test.telegram');

    Route::middleware(PreventSharedTesterAccountChanges::class)->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
