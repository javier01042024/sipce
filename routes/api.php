<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SyncController;
use App\Http\Controllers\BitacoraController;

// ========================================
// AUTENTICACIÓN MÓVIL (SANCTUM TOKENS)
// ========================================
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/bitacora', [BitacoraController::class, 'apiIndex'])->name('api.bitacora.index');
});

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
