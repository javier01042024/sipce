<?php
// routes/web.php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\Configuracion\EstadoController;
use App\Http\Controllers\Configuracion\AparienciaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PacientePortalController;
use App\Http\Controllers\PlanTratamientoController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\AcompananteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ====================================================
// RUTAS PÚBLICAS
// ====================================================
Route::get('/', function () {
    return view('welcome');
});

// ====================================================
// DASHBOARD
// ====================================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ====================================================
// RUTAS AUTENTICADAS
// ====================================================
Route::middleware('auth')->group(function () {

    // --------------------------------------------------
    // PERFIL
    // --------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --------------------------------------------------
    // PACIENTES
    // --------------------------------------------------
    Route::get('/pacientes/sin-usuario', [PacienteController::class, 'sinUsuario'])
        ->middleware('permission:pacientes.index')
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

    // --------------------------------------------------
    // CITAS
    // --------------------------------------------------
    Route::get('/citas/verificar', [CitaController::class, 'verificarCitaPaciente'])
        ->middleware('permission:citas.index')
        ->name('citas.verificar');

    Route::get('/citas/cupos', [CitaController::class, 'cuposDisponibles'])
        ->middleware('permission:citas.index')
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

    Route::put('/citas/{cita}', [CitaController::class, 'update'])
        ->middleware('permission:citas.edit')
        ->name('citas.update');

    Route::delete('/citas/{cita}', [CitaController::class, 'destroy'])
        ->middleware('permission:citas.destroy')
        ->name('citas.destroy');

    // --------------------------------------------------
    // DIARIOS
    // --------------------------------------------------
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

    // --------------------------------------------------
    // NOTAS
    // --------------------------------------------------
    Route::post('/notas', [NotaController::class, 'store'])
        ->middleware('permission:pacientes.show')
        ->name('notas.store');
    Route::delete('/notas/{nota}', [NotaController::class, 'destroy'])
        ->middleware('permission:pacientes.show')
        ->name('notas.destroy');

    // --------------------------------------------------
    // USUARIOS
    // --------------------------------------------------
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

    // --------------------------------------------------
    // ROLES
    // --------------------------------------------------
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

    // --------------------------------------------------
    // CONFIGURACIÓN
    // --------------------------------------------------

    // Respaldos
    Route::prefix('configuracion/respaldos')
        ->name('configuracion.respaldos.')
        ->group(function () {
            Route::get('/', [BackupController::class, 'index'])
                ->middleware('permission:respaldos.index')
                ->name('index');
            Route::post('/create', [BackupController::class, 'create'])
                ->middleware('permission:respaldos.create')
                ->name('create');
            Route::get('/download/{filename}', [BackupController::class, 'download'])
                ->middleware('permission:respaldos.download')
                ->name('download');
            Route::post('/restore/{filename}', [BackupController::class, 'restore'])
                ->middleware('permission:respaldos.restore')
                ->name('restore');
            Route::delete('/delete/{filename}', [BackupController::class, 'delete'])
                ->middleware('permission:respaldos.delete')
                ->name('delete');
            Route::get('/config', [BackupController::class, 'getConfig'])
                ->middleware('permission:respaldos.index')
                ->name('config.get');
            Route::post('/config', [BackupController::class, 'saveConfig'])
                ->middleware('permission:respaldos.index')
                ->name('config.save');
            Route::post('/import', [BackupController::class, 'importSql'])
                ->middleware('permission:respaldos.import')
                ->name('import');
        });

    // Estados
    Route::prefix('configuracion/estados')
        ->name('configuracion.estados.')
        ->middleware('permission:estados.index')
        ->group(function () {
            Route::get('/', [EstadoController::class, 'index'])->name('index');

            Route::post('/', [EstadoController::class, 'store'])
                ->middleware('permission:estados.create')
                ->name('store');

            Route::get('/{estado}', [EstadoController::class, 'show'])
                ->middleware('permission:estados.show')
                ->name('show');

            Route::put('/{estado}', [EstadoController::class, 'update'])
                ->middleware('permission:estados.edit')
                ->name('update');

            Route::delete('/{estado}', [EstadoController::class, 'destroy'])
                ->middleware('permission:estados.destroy')
                ->name('destroy');
        });

    // Bitácora
    Route::get('/configuracion/bitacora', [BitacoraController::class, 'index'])
        ->middleware('permission:bitacora.index')
        ->name('configuracion.bitacora.index');

    Route::get('/configuracion/bitacora/exportar', [BitacoraController::class, 'exportar'])
        ->middleware('permission:bitacora.index')
        ->name('configuracion.bitacora.exportar');

    // Apariencia (colores y modo nocturno) - preferencia personal de cada usuario
    Route::prefix('configuracion/apariencia')
        ->name('configuracion.apariencia.')
        ->group(function () {
            Route::get('/', [AparienciaController::class, 'index'])->name('index');
            Route::put('/', [AparienciaController::class, 'update'])->name('update');
        });

    // --------------------------------------------------
    // PLANES DE TRATAMIENTO
    // --------------------------------------------------
    Route::get('/tratamiento', [PlanTratamientoController::class, 'index'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.index');

    Route::get('/tratamiento/create', [PlanTratamientoController::class, 'create'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.create');

    Route::post('/tratamiento', [PlanTratamientoController::class, 'store'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.store');

    Route::get('/tratamiento/{tratamiento}', [PlanTratamientoController::class, 'show'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.show');

    Route::get('/tratamiento/{tratamiento}/edit', [PlanTratamientoController::class, 'edit'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.edit');

    Route::put('/tratamiento/{tratamiento}', [PlanTratamientoController::class, 'update'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.update');

    Route::delete('/tratamiento/{tratamiento}', [PlanTratamientoController::class, 'destroy'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.destroy');

    Route::post('/tratamiento/{tratamiento}/objetivos', [PlanTratamientoController::class, 'storeObjetivo'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.objetivo.store');

    Route::put('/tratamiento/objetivos/{objetivo}', [PlanTratamientoController::class, 'updateObjetivo'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.objetivo.update');

    Route::delete('/tratamiento/objetivos/{objetivo}', [PlanTratamientoController::class, 'destroyObjetivo'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.objetivo.destroy');

    Route::patch('/tratamiento/objetivos/{objetivo}/estado', [PlanTratamientoController::class, 'cambiarEstadoObjetivo'])
        ->middleware('permission:pacientes.show')
        ->name('tratamiento.objetivo.estado');

    // --------------------------------------------------
    // SESIONES
    // --------------------------------------------------
    Route::get('/sesiones', [SesionController::class, 'index'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.index');

    Route::get('/sesiones/create', [SesionController::class, 'create'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.create');

    Route::post('/sesiones', [SesionController::class, 'store'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.store');

    Route::get('/sesiones/{sesion}', [SesionController::class, 'show'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.show');

    Route::get('/sesiones/{sesion}/edit', [SesionController::class, 'edit'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.edit');

    Route::put('/sesiones/{sesion}', [SesionController::class, 'update'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.update');

    Route::delete('/sesiones/{sesion}', [SesionController::class, 'destroy'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.destroy');

    Route::post('/sesiones/{sesion}/agendar-proxima', [SesionController::class, 'agendarProxima'])
        ->middleware('permission:pacientes.show')
        ->name('sesiones.agendar-proxima');

    // --------------------------------------------------
    // CALENDARIO
    // --------------------------------------------------
    Route::get('/calendario', [CalendarioController::class, 'index'])
        ->middleware('permission:citas.index')
        ->name('calendario.index');

    Route::get('/calendario/eventos', [CalendarioController::class, 'eventos'])
        ->middleware('permission:citas.index')
        ->name('calendario.eventos');

    // --------------------------------------------------
    // REPORTES
    // --------------------------------------------------
    Route::prefix('reportes')->name('reportes.')->middleware('permission:pacientes.index')->group(function () {
        Route::get('/', [ReportesController::class, 'index'])->name('index');
        Route::get('/pacientes', [ReportesController::class, 'pacientes'])->name('pacientes');
        Route::get('/citas', [ReportesController::class, 'citas'])->name('citas');
        Route::get('/evolucion', [ReportesController::class, 'evolucion'])->name('evolucion');
        Route::get('/mensual', [ReportesController::class, 'mensual'])->name('mensual');
        Route::get('/exportar-mensual', [ReportesController::class, 'exportarMensual'])->name('exportar-mensual');
        Route::get('/exportar', [ReportesController::class, 'exportar'])->name('exportar');
    });

    // --------------------------------------------------
    // NOTIFICACIONES
    // --------------------------------------------------
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/notificaciones/no-leidas', [NotificacionController::class, 'noLeidas'])->name('notificaciones.no-leidas');
    Route::patch('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar-leida');
    Route::patch('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.leer-todas');
    Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');

    // --------------------------------------------------
    // EXPEDIENTE PDF
    // --------------------------------------------------
    Route::get('/pacientes/{paciente}/exportar-pdf', [ExpedienteController::class, 'exportarResumen'])
        ->middleware('permission:pacientes.show')
        ->name('pacientes.exportar-pdf');

    // --------------------------------------------------
    // DIAGNÓSTICOS
    // --------------------------------------------------
    Route::post('/pacientes/{paciente}/diagnosticos', [DiagnosticoController::class, 'store'])
        ->middleware('permission:pacientes.edit')
        ->name('diagnosticos.store');
    Route::put('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'update'])
        ->middleware('permission:pacientes.edit')
        ->name('diagnosticos.update');
    Route::delete('/diagnosticos/{diagnostico}', [DiagnosticoController::class, 'destroy'])
        ->middleware('permission:pacientes.edit')
        ->name('diagnosticos.destroy');

    // --------------------------------------------------
    // ACOMPAÑANTES
    // --------------------------------------------------
    Route::post('/pacientes/{paciente}/acompanantes', [AcompananteController::class, 'store'])
        ->middleware('permission:pacientes.edit')
        ->name('acompanantes.store');
    Route::put('/acompanantes/{acompanante}', [AcompananteController::class, 'update'])
        ->middleware('permission:pacientes.edit')
        ->name('acompanantes.update');
    Route::delete('/acompanantes/{acompanante}', [AcompananteController::class, 'destroy'])
        ->middleware('permission:pacientes.edit')
        ->name('acompanantes.destroy');
    Route::get('/pacientes/{paciente}/acompanantes', [AcompananteController::class, 'listar'])
        ->middleware('permission:pacientes.show')
        ->name('acompanantes.listar');
});

// ====================================================
// PORTAL DEL PACIENTE
// ====================================================
Route::middleware(['auth', 'patient'])->prefix('paciente')->name('paciente.')->group(function () {
    Route::get('/', [PacientePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/mis-citas', [PacientePortalController::class, 'misCitas'])->name('mis-citas');
    Route::get('/mi-diario', [PacientePortalController::class, 'miDiario'])->name('mi-diario');
    Route::post('/mi-diario', [PacientePortalController::class, 'storeDiario'])->name('mi-diario.store');
    Route::get('/mi-diario/{diario}', [PacientePortalController::class, 'showDiario'])->name('mi-diario.show');
    Route::put('/mi-diario/{diario}', [PacientePortalController::class, 'updateDiario'])->name('mi-diario.update');
    Route::delete('/mi-diario/{diario}', [PacientePortalController::class, 'destroyDiario'])->name('mi-diario.destroy');
    Route::get('/perfil', [PacientePortalController::class, 'perfil'])->name('perfil');
    Route::put('/password', [PacientePortalController::class, 'updatePassword'])->name('password');
    Route::get('/actividad', [PacientePortalController::class, 'actividad'])->name('actividad');
});

require __DIR__ . '/auth.php';
