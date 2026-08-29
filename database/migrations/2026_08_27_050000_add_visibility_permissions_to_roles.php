<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Permisos de visibilidad de pacientes (público/privado) que se agregan
     * a los roles existentes sin necesidad de reseedear.
     *
     * - admin y secretaria: mantienen visibilidad total por defecto.
     * - psicologa: además recibe acceso de consulta al editor de roles para
     *   poder configurar qué puede ver la secretaria.
     */
    private array $visibilidad = [
        'pacientes.ver_publico',
        'pacientes.ver_privado',
    ];

    private array $gestionRolesPsicologa = [
        'usuarios.index',
        'roles.index',
        'roles.show',
        'roles.edit',
    ];

    public function up(): void
    {
        foreach (['admin', 'psicologa', 'secretaria'] as $slug) {
            $role = DB::table('roles')->where('slug', $slug)->first();

            if (! $role) {
                continue;
            }

            $permissions = json_decode($role->permissions, true) ?? [];

            $nuevosPermisos = $this->visibilidad;

            if ($slug === 'psicologa') {
                $nuevosPermisos = array_merge($nuevosPermisos, $this->gestionRolesPsicologa);
            }

            $permissions = array_values(array_unique(array_merge($permissions, $nuevosPermisos)));

            DB::table('roles')
                ->where('id', $role->id)
                ->update(['permissions' => json_encode($permissions)]);
        }
    }

    public function down(): void
    {
        foreach (['admin', 'psicologa', 'secretaria'] as $slug) {
            $role = DB::table('roles')->where('slug', $slug)->first();

            if (! $role) {
                continue;
            }

            $permissions = json_decode($role->permissions, true) ?? [];

            $permisosEliminar = $this->visibilidad;

            if ($slug === 'psicologa') {
                $permisosEliminar = array_merge($permisosEliminar, $this->gestionRolesPsicologa);
            }

            $permissions = array_values(array_diff($permissions, $permisosEliminar));

            DB::table('roles')
                ->where('id', $role->id)
                ->update(['permissions' => json_encode($permissions)]);
        }
    }
};