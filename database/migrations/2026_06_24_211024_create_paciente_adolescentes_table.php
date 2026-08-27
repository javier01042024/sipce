<?php
// database/migrations/xxxx_xx_xx_create_paciente_adolescentes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_adolescentes', function (Blueprint $table) {
            $table->id();
            
            // Datos personales
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('cedula', 20)->nullable();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable();
            $table->string('telefono_personal', 15)->nullable();
            $table->string('email_personal', 100)->nullable();
            $table->string('institucion_educativa', 100)->nullable();
            $table->string('nivel_educativo', 50)->nullable();
            
            // Datos del representante
            $table->string('nombre_representante', 150)->nullable();
            $table->string('cedula_representante', 20)->nullable();
            $table->string('telefono_representante', 15)->nullable();
            $table->string('email_representante', 100)->nullable();
            $table->string('parentesco', 50)->nullable();
            
            // Historia clínica
            $table->text('historia_problema')->nullable();
            $table->enum('frecuencia_sintomas', ['Diario', 'Semanal', 'Mensual', 'Ocasional'])->nullable();
            $table->enum('intensidad_sintomas', ['Leve', 'Moderado', 'Severo'])->nullable();
            $table->text('factores_desencadenantes')->nullable();
            
            // Hábitos y relaciones
            $table->text('habitos')->nullable();
            $table->text('relaciones_sociales')->nullable();
            $table->string('rendimiento_academico', 100)->nullable();
            $table->enum('tiene_amigos', ['Sí, varios', 'Sí, pocos', 'No'])->nullable();
            $table->enum('actividades_grupales', ['Sí, frecuentemente', 'A veces', 'No'])->nullable();
            $table->text('autoimagen')->nullable();
            $table->text('relacion_padres')->nullable();
            $table->text('manejo_emociones')->nullable();
            
            // Antecedentes
            $table->text('antecedentes_familiares')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_adolescentes');
    }
};