<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_tratamiento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('titulo');
            $table->text('objetivo_general')->nullable();
            $table->enum('estado', ['activo', 'pausado', 'completado', 'cancelado'])->default('activo');

            $table->date('fecha_inicio');
            $table->date('fecha_fin_estimada')->nullable();
            $table->date('fecha_fin_real')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_tratamiento');
    }
};
