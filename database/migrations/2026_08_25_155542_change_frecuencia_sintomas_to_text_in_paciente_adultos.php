<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paciente_adultos', function (Blueprint $table) {
            $table->text('frecuencia_sintomas')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('paciente_adultos', function (Blueprint $table) {
            $table->enum('frecuencia_sintomas', ['Diario', 'Semanal', 'Mensual', 'Ocasional'])->nullable()->change();
        });
    }
};
