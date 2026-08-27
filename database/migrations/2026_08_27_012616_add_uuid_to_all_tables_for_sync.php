<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'pacientes',
            'paciente_adultos',
            'paciente_adolescentes',
            'paciente_ninos',
            'citas',
            'sesiones',
            'notas',
            'diarios',
            'diagnosticos',
            'acompanantes',
            'notificaciones',
            'planes_tratamiento',
            'plan_objetivos',
            'estados',
            'users',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->string('uuid', 36)->nullable()->after('id')->unique();
                });

                // Backfill existing rows with UUIDs
                $modelClass = match($table) {
                    'pacientes' => \App\Models\Paciente::class,
                    'paciente_adultos' => \App\Models\PacienteAdulto::class,
                    'paciente_adolescentes' => \App\Models\PacienteAdolescente::class,
                    'paciente_ninos' => \App\Models\PacienteNino::class,
                    'citas' => \App\Models\Cita::class,
                    'sesiones' => \App\Models\Sesion::class,
                    'notas' => \App\Models\Nota::class,
                    'diarios' => \App\Models\Diario::class,
                    'diagnosticos' => \App\Models\Diagnostico::class,
                    'acompanantes' => \App\Models\Acompanante::class,
                    'notificaciones' => \App\Models\Notificacion::class,
                    'planes_tratamiento' => \App\Models\PlanTratamiento::class,
                    'plan_objetivos' => \App\Models\PlanObjetivo::class,
                    'estados' => \App\Models\Estado::class,
                    'users' => \App\Models\User::class,
                    default => null,
                };

                if ($modelClass && class_exists($modelClass)) {
                    $rows = \DB::table($table)->whereNull('uuid')->get();
                    foreach ($rows as $row) {
                        \DB::table($table)
                            ->where('id', $row->id)
                            ->update(['uuid' => (string) Str::uuid()]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'pacientes', 'paciente_adultos', 'paciente_adolescentes', 'paciente_ninos',
            'citas', 'sesiones', 'notas', 'diarios', 'diagnosticos', 'acompanantes',
            'notificaciones', 'planes_tratamiento', 'plan_objetivos', 'estados', 'users',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('uuid');
                });
            }
        }
    }
};
