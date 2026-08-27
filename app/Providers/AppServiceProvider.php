<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Diario;
use App\Models\Sesion;
use App\Models\User;
use App\Models\Nota;
use App\Models\PlanTratamiento;
use App\Observers\BitacoraObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Observers para bitácora automática
        Paciente::observe(BitacoraObserver::class);
        Cita::observe(BitacoraObserver::class);
        Diario::observe(BitacoraObserver::class);
        Sesion::observe(BitacoraObserver::class);
        User::observe(BitacoraObserver::class);
        Nota::observe(BitacoraObserver::class);
        PlanTratamiento::observe(BitacoraObserver::class);

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