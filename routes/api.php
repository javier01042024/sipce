<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SyncController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ========================================
// RUTAS DE SINCRONIZACIÓN OFFLINE-ONLINE
// ========================================
Route::prefix('sync')->middleware('auth:sanctum')->group(function () {
    Route::post('/upload', [SyncController::class, 'upload'])
        ->name('api.sync.upload');

    Route::get('/download', [SyncController::class, 'download'])
        ->name('api.sync.download');

    Route::get('/status', [SyncController::class, 'status'])
        ->name('api.sync.status');

    Route::post('/resolve-conflict', [SyncController::class, 'resolveConflict'])
        ->name('api.sync.resolve-conflict');
});
