<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            $nuevosPermisos = [
                'respaldos.create',
                'respaldos.download',
                'respaldos.restore',
                'respaldos.delete',
                'respaldos.import',
            ];

            $permissions = array_unique(array_merge($permissions, $nuevosPermisos));

            DB::table('roles')
                ->where('id', $adminRole->id)
                ->update(['permissions' => json_encode($permissions)]);
        }
    }

    public function down(): void
    {
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();

        if ($adminRole) {
            $permissions = json_decode($adminRole->permissions, true) ?? [];

            $permisosEliminar = [
                'respaldos.create',
                'respaldos.download',
                'respaldos.restore',
                'respaldos.delete',
                'respaldos.import',
            ];

            $permissions = array_values(array_diff($permissions, $permisosEliminar));

            DB::table('roles')
                ->where('id', $adminRole->id)
                ->update(['permissions' => json_encode($permissions)]);
        }
    }
};
