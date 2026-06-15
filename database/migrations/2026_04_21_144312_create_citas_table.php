<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            
            // Relación con pacientes
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            // Fecha de la cita
            $table->date('fecha');
            
            // Estado de la cita
            $table->enum('estado', ['pendiente', 'atendida', 'cancelada', 'no_asistio'])
                  ->default('pendiente');
            
            // Objetivo terapéutico
            $table->text('objetivo')->nullable();
            
            // Planificación de la sesión
            $table->text('planificacion')->nullable();
            
            // Motivo de cancelación (solo si estado = cancelada)
            $table->text('motivo_cancelacion')->nullable();
            
            // Timestamps (created_at y updated_at)
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('fecha');
            $table->index('estado');
            $table->index(['fecha', 'estado']); // Índice compuesto para búsquedas comunes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};