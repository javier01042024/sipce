<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')
                  ->constrained('citas')
                  ->onDelete('cascade');

            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->date('fecha');
            $table->unsignedInteger('duracion_minutos')->nullable();

            $table->text('resumen');
            $table->text('observaciones_clinicas')->nullable();
            $table->text('evolucion')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};
