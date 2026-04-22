<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DiarioController;

Route::resource('pacientes', PacienteController::class);
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('citas', CitaController::class);

Route::resource('diarios', DiarioController::class);

// ===== RUTAS DE CONFIGURACIÓN (SOLO VISTAS INDEX) =====
Route::middleware('auth')->group(function () {

    // Usuarios y Roles
    Route::get('/usuarios', function () {
        return view('configuracion.usuarios.index');
    })->name('usuarios.index');

    // Respaldos
    Route::get('/respaldos', function () {
        return view('configuracion.respaldos.index');
    })->name('respaldos.index');

    // Bitácora
    Route::get('/bitacora', function () {
        return view('configuracion.bitacora.index');
    })->name('bitacora.index');
});

require __DIR__ . '/auth.php';
