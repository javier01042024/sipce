<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso completo al sistema',
                'permissions' => [
                    // Dashboard
                    'dashboard',
                    // Perfil
                    'profile.edit', 'profile.update', 'profile.destroy',
                    // Pacientes
                    'pacientes.index', 'pacientes.create', 'pacientes.show', 'pacientes.edit', 'pacientes.destroy',
                    // Citas
                    'citas.index', 'citas.create', 'citas.show', 'citas.edit', 'citas.destroy',
                    // Diarios
                    'diarios.index', 'diarios.create', 'diarios.show', 'diarios.edit', 'diarios.destroy',
                    // Usuarios
                    'usuarios.index', 'usuarios.create', 'usuarios.show', 'usuarios.edit', 'usuarios.destroy', 'usuarios.toggle-status',
                    // Roles - ¡ESTOS SON LOS IMPORTANTES!
                    'roles.index', 'roles.create', 'roles.show', 'roles.edit', 'roles.destroy',
                    // Configuración
                    'respaldos.index', 'bitacora.index',
                ]
            ]
        );

        $this->command->info(" Rol '{$adminRole->name}' creado con todos los permisos!");
        $this->command->info(" Permisos asignados: " . count($adminRole->permissions));
    }
}