<?php

use App\Http\Controllers\OsintController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [OsintController::class, 'index'])->name('osint.index');
Route::get('/dashboard/live-metrics', [OsintController::class, 'liveMetrics'])
    ->middleware('throttle:60,1')
    ->name('osint.live-metrics');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [OsintController::class, 'index'])->name('dashboard');
    Route::get('/ekspor-intelijen', [OsintController::class, 'exportPdf'])
        ->middleware('throttle:10,1')
        ->name('osint.export.pdf');

    Route::post('/osint/fetch', [OsintController::class, 'fetch'])->name('osint.fetch');
    Route::post('/osint/test-telegram', [OsintController::class, 'testTelegram'])->name('osint.test.telegram');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
