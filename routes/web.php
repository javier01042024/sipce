<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;



// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (todos los usuarios autenticados pueden verlo)
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas autenticadas
Route::middleware('auth')->group(function () {

    // Perfil (todos pueden gestionar su perfil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ====================================================
    // PACIENTES
    // ====================================================
    Route::get('/pacientes/sin-usuario', [PacienteController::class, 'sinUsuario'])
        ->name('pacientes.sin-usuario');
    Route::get('/pacientes', [PacienteController::class, 'index'])
        ->middleware('permission:pacientes.index')
        ->name('pacientes.index');

    Route::get('/pacientes/create', [PacienteController::class, 'create'])
        ->middleware('permission:pacientes.create')
        ->name('pacientes.create');

    Route::post('/pacientes', [PacienteController::class, 'store'])
        ->middleware('permission:pacientes.create')
        ->name('pacientes.store');

    Route::get('/pacientes/{paciente}', [PacienteController::class, 'show'])
        ->middleware('permission:pacientes.show')
        ->name('pacientes.show');

    Route::get('/pacientes/{paciente}/edit', [PacienteController::class, 'edit'])
        ->middleware('permission:pacientes.edit')
        ->name('pacientes.edit');

    Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])
        ->middleware('permission:pacientes.edit')
        ->name('pacientes.update');

    Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])
        ->middleware('permission:pacientes.destroy')
        ->name('pacientes.destroy');

    // ====================================================
    // CITAS
    // ====================================================
    Route::get('/citas/verificar', [CitaController::class, 'verificarCitaPaciente'])
        ->middleware(['auth'])
        ->name('citas.verificar');
        
    Route::get('/citas/cupos', [CitaController::class, 'cuposDisponibles'])
        ->middleware(['auth'])
        ->name('citas.cupos');

    Route::get('/citas', [CitaController::class, 'index'])
        ->middleware('permission:citas.index')
        ->name('citas.index');

    Route::get('/citas/create', [CitaController::class, 'create'])
        ->middleware('permission:citas.create')
        ->name('citas.create');

    Route::post('/citas', [CitaController::class, 'store'])
        ->middleware('permission:citas.create')
        ->name('citas.store');

    Route::get('/citas/{cita}', [CitaController::class, 'show'])
        ->middleware('permission:citas.show')
        ->name('citas.show');

    Route::get('/citas/{cita}/edit', [CitaController::class, 'edit'])
        ->middleware('permission:citas.edit')
        ->name('citas.edit');

    Route::put('/citas/{cita}', [CitaController::class, 'update'])
        ->middleware('permission:citas.edit')
        ->name('citas.update');

    Route::delete('/citas/{cita}', [CitaController::class, 'destroy'])
        ->middleware('permission:citas.destroy')
        ->name('citas.destroy');


    // ====================================================
    // DIARIOS
    // ====================================================
    Route::get('/diarios', [DiarioController::class, 'index'])
        ->middleware('permission:diarios.index')
        ->name('diarios.index');

    Route::get('/diarios/create', [DiarioController::class, 'create'])
        ->middleware('permission:diarios.create')
        ->name('diarios.create');

    Route::post('/diarios', [DiarioController::class, 'store'])
        ->middleware('permission:diarios.create')
        ->name('diarios.store');

    Route::get('/diarios/{diario}', [DiarioController::class, 'show'])
        ->middleware('permission:diarios.show')
        ->name('diarios.show');

    Route::get('/diarios/{diario}/edit', [DiarioController::class, 'edit'])
        ->middleware('permission:diarios.edit')
        ->name('diarios.edit');

    Route::put('/diarios/{diario}', [DiarioController::class, 'update'])
        ->middleware('permission:diarios.edit')
        ->name('diarios.update');

    Route::delete('/diarios/{diario}', [DiarioController::class, 'destroy'])
        ->middleware('permission:diarios.destroy')
        ->name('diarios.destroy');

    // ====================================================
    // USUARIOS
    // ====================================================

    Route::get('/usuarios', [UserController::class, 'index'])
        ->middleware('permission:usuarios.index')
        ->name('usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])
        ->middleware('permission:usuarios.create')
        ->name('usuarios.store');
    Route::get('/usuarios/{user}', [UserController::class, 'show'])
        ->middleware('permission:usuarios.show')
        ->name('usuarios.show');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->middleware('permission:usuarios.edit')
        ->name('usuarios.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:usuarios.destroy')
        ->name('usuarios.destroy');
    Route::post('/usuarios/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->middleware('permission:usuarios.toggle-status')
        ->name('usuarios.toggle-status');


    // ====================================================
    // ROLES
    // ====================================================
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.index')
        ->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.create')
        ->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->middleware('permission:roles.show')
        ->name('roles.show');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.edit')
        ->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.destroy')
        ->name('roles.destroy');

    // Respaldos
    Route::prefix('configuracion/respaldos')->name('configuracion.respaldos.')->middleware('permission:respaldos.index')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/download/{filename}', [BackupController::class, 'download'])->name('download');
        Route::post('/restore/{filename}', [BackupController::class, 'restore'])->name('restore');
        Route::delete('/delete/{filename}', [BackupController::class, 'delete'])->name('delete');
        Route::get('/config', [BackupController::class, 'getConfig'])->name('config.get');
        Route::post('/config', [BackupController::class, 'saveConfig'])->name('config.save');
    });
    // Bitácora
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/configuracion/bitacora', [BitacoraController::class, 'index'])
            ->name('configuracion.bitacora.index');

        Route::get('/configuracion/bitacora/exportar', [BitacoraController::class, 'exportar'])
            ->name('configuracion.bitacora.exportar');
    });
});

require __DIR__ . '/auth.php';
