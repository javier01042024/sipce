<?php

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
                    'dashboard',
                    'profile.edit', 'profile.update', 'profile.destroy',
                    'pacientes.index', 'pacientes.create', 'pacientes.show', 'pacientes.edit', 'pacientes.destroy',
                    'citas.index', 'citas.create', 'citas.show', 'citas.edit', 'citas.destroy',
                    'diarios.index', 'diarios.create', 'diarios.show', 'diarios.edit', 'diarios.destroy',
                    'usuarios.index', 'usuarios.create', 'usuarios.show', 'usuarios.edit', 'usuarios.destroy', 'usuarios.toggle-status',
                    'roles.index', 'roles.create', 'roles.show', 'roles.edit', 'roles.destroy',
                    'estados.index', 'estados.create', 'estados.show', 'estados.edit', 'estados.destroy',
                    'respaldos.index', 'respaldos.create', 'respaldos.download', 'respaldos.restore', 'respaldos.delete', 'respaldos.import',
                    'bitacora.index',
                    'planes.index', 'planes.create', 'planes.show', 'planes.edit', 'planes.destroy',
                    'sesiones.index', 'sesiones.create', 'sesiones.show', 'sesiones.edit', 'sesiones.destroy',
                    'calendario.index',
                    'reportes.index', 'reportes.pacientes', 'reportes.citas', 'reportes.evolucion',
                    'notificaciones.index',
                ]
            ]
        );

        $psicologaRole = Role::firstOrCreate(
            ['slug' => 'psicologa'],
            [
                'name' => 'Psicóloga',
                'slug' => 'psicologa',
                'description' => 'Acceso clínico completo: pacientes, citas, tratamientos, sesiones, diarios, reportes',
                'permissions' => [
                    'dashboard',
                    'profile.edit', 'profile.update', 'profile.destroy',
                    'pacientes.index', 'pacientes.create', 'pacientes.show', 'pacientes.edit', 'pacientes.destroy',
                    'citas.index', 'citas.create', 'citas.show', 'citas.edit', 'citas.destroy',
                    'diarios.index', 'diarios.create', 'diarios.show', 'diarios.edit', 'diarios.destroy',
                    'planes.index', 'planes.create', 'planes.show', 'planes.edit', 'planes.destroy',
                    'sesiones.index', 'sesiones.create', 'sesiones.show', 'sesiones.edit', 'sesiones.destroy',
                    'calendario.index',
                    'reportes.index', 'reportes.pacientes', 'reportes.citas', 'reportes.evolucion',
                    'notificaciones.index',
                ]
            ]
        );

        $secretariaRole = Role::firstOrCreate(
            ['slug' => 'secretaria'],
            [
                'name' => 'Secretaria',
                'slug' => 'secretaria',
                'description' => 'Registro de pacientes y asignación de citas',
                'permissions' => [
                    'dashboard',
                    'profile.edit', 'profile.update', 'profile.destroy',
                    'pacientes.index', 'pacientes.create', 'pacientes.show', 'pacientes.edit',
                    'citas.index', 'citas.create', 'citas.show', 'citas.edit',
                    'notificaciones.index',
                ]
            ]
        );

        $this->command->info("✅ Roles creados: {$adminRole->name} (" . count($adminRole->permissions) . " permisos), {$psicologaRole->name} (" . count($psicologaRole->permissions) . " permisos), {$secretariaRole->name} (" . count($secretariaRole->permissions) . " permisos)");
    }
}
