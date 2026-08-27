<?php
// database/migrations/2024_01_01_000002_create_pacientes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            
            // Relaciones polimórficas
            $table->string('tipo_paciente'); // 'niño', 'adolescente', 'adulto'
            $table->unsignedBigInteger('paciente_detalle_id'); // ID de la tabla específica
            $table->string('paciente_detalle_type'); // Modelo: Niño, Adolescente, Adulto
            
            // Datos generales del sistema
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            
            $table->foreignId('estado_id')
                  ->nullable()
                  ->constrained('estados')
                  ->nullOnDelete();
            
            // Datos administrativos
            $table->string('numero_expediente', 20)->unique();
            $table->string('motivo_consulta', 500)->nullable();
            $table->text('diagnostico_preliminar')->nullable();
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgencia'])->default('media');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};