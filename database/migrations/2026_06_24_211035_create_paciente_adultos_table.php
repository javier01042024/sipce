<?php
// database/migrations/xxxx_xx_xx_create_paciente_adultos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_adultos', function (Blueprint $table) {
            $table->id();
            
            // Datos personales
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('cedula', 8)->unique();
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable();
            $table->enum('estado_civil', ['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a', 'Unión Libre'])->nullable();
            $table->string('telefono', 15);
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            
            // Datos laborales
            $table->string('ocupacion', 100)->nullable();
            $table->string('lugar_trabajo', 150)->nullable();
            $table->string('telefono_trabajo', 15)->nullable();
            
            // Contacto de emergencia
            $table->string('nombre_emergencia', 150)->nullable();
            $table->string('telefono_emergencia', 15)->nullable();
            $table->string('parentesco_emergencia', 50)->nullable();
            
            // Historia clínica
            $table->text('historia_problema')->nullable();
            $table->enum('frecuencia_sintomas', ['Diario', 'Semanal', 'Mensual', 'Ocasional'])->nullable();
            $table->enum('intensidad_sintomas', ['Leve', 'Moderado', 'Severo'])->nullable();
            $table->text('factores_desencadenantes')->nullable();
            $table->text('estrategias_afrontamiento')->nullable();
            
            // Antecedentes psicológicos
            $table->text('antecedentes_psiquiatricos')->nullable();
            $table->text('medicamentos_actuales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            
            // Hábitos
            $table->enum('consume_alcohol', ['No', 'Ocasional', 'Frecuente'])->nullable();
            $table->enum('consume_tabaco', ['No', 'Ocasional', 'Frecuente'])->nullable();
            $table->string('otras_sustancias', 100)->nullable();
            $table->text('habitos_sueno')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_adultos');
    }
};