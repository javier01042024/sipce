<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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
        // Usamos un enfoque diferido para evitar problemas de inicialización
        View::composer('*', function ($view) {
            if (Auth::check()) {
                /** @var \App\Models\User $user */
                $user = Auth::user();
                
                $userPermissions = [];
                $userRoles = [];
                
                // Obtener todos los permisos del usuario a través de sus roles
                if ($user->roles) {
                    foreach ($user->roles as $role) {
                        $userRoles[] = $role->slug;
                        if (!empty($role->permissions)) {
                            $userPermissions = array_merge($userPermissions, $role->permissions);
                        }
                    }
                }
                
                // Eliminar duplicados y reindexar
                $userPermissions = array_values(array_unique($userPermissions));
                
                // Compartir con la vista
                $view->with('userPermissions', $userPermissions);
                $view->with('userRoles', $userRoles);
            }
        });
    }
}