<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Database\PostgresConnection;
use App\Database\NeonPgsqlConnector;
use App\Console\Commands\InitLocal;
use App\Console\Commands\SyncCommand;
use App\Observers\BitacoraObserver;
use App\Observers\SyncOutboxObserver;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Diario;
use App\Models\Sesion;
use App\Models\User;
use App\Models\Nota;
use App\Models\PlanTratamiento;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            InitLocal::class,
            SyncCommand::class,
        ]);
    }

    public function boot(): void
    {
        // Conector PostgreSQL compatible con Neon para clientes sin SNI
        // (inyecta options='endpoint=<id>' en el DSN cuando el host es .neon.tech).
        DB::extend('pgsql', function ($config, $name) {
            $pdo = (new NeonPgsqlConnector)->connect($config);
            $config['name'] = $name;

            return new PostgresConnection(
                $pdo,
                $config['database'] ?? '',
                $config['prefix'] ?? '',
                $config
            );
        });

        // Observers para bitácora automática
        Paciente::observe(BitacoraObserver::class);
        Cita::observe(BitacoraObserver::class);
        Diario::observe(BitacoraObserver::class);
        Sesion::observe(BitacoraObserver::class);
        User::observe(BitacoraObserver::class);
        Nota::observe(BitacoraObserver::class);
        PlanTratamiento::observe(BitacoraObserver::class);

        // Outbox de sincronización (solo en el escritorio, que usa BD local).
        // En la versión web los cambios ya están en el servidor: no se encolan.
        if ((bool) env('SYNC_OUTBOX', false)) {
            foreach (config('sync.tables') as [, $modelClass]) {
                $modelClass::observe(SyncOutboxObserver::class);
            }
        }

        // Directiva Blade para verificar permisos
        Blade::if('canPermission', function (string $permission) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            return $user && $user->hasPermission($permission);
        });

        // Directiva Blade para verificar roles
        Blade::if('hasRole', function (string $role) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            return $user && $user->hasRole($role);
        });

        // Compartir permisos del usuario con todas las vistas
        View::composer('*', function ($view) {
            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                $userPermissions = [];
                $userRoles = [];
                
                if ($user->roles) {
                    foreach ($user->roles as $role) {
                        $userRoles[] = $role->slug;
                        if (!empty($role->permissions)) {
                            $userPermissions = array_merge($userPermissions, $role->permissions);
                        }
                    }
                }
                
                $userPermissions = array_values(array_unique($userPermissions));
                
                $view->with('userPermissions', $userPermissions);
                $view->with('userRoles', $userRoles);
            }
        });
    }
}