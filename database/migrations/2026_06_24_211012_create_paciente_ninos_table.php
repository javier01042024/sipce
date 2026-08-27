<?php
// database/migrations/xxxx_xx_xx_create_paciente_ninos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_ninos', function (Blueprint $table) {
            $table->id();
            
            // Datos básicos
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->date('fecha_nacimiento');
            $table->string('telefono_contacto', 15)->nullable();
            $table->string('grado_instruccion', 50)->nullable();
            $table->string('maestro', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            
            // Datos de los padres
            $table->string('nombre_padre', 150)->nullable();
            $table->integer('edad_padre')->nullable();
            $table->string('ocupacion_padre', 100)->nullable();
            $table->string('nombre_madre', 150)->nullable();
            $table->integer('edad_madre')->nullable();
            $table->string('ocupacion_madre', 100)->nullable();
            $table->string('hermanos', 255)->nullable();
            $table->string('personas_vive', 255)->nullable();
            
            // Motivo de consulta e historia
            $table->text('desarrollo_problema')->nullable();
            $table->enum('frecuencia_sintomas', ['Diario', 'Semanal', 'Mensual', 'Ocasional'])->nullable();
            $table->enum('intensidad_sintomas', ['Leve', 'Moderado', 'Severo'])->nullable();
            $table->string('duracion_sintomas', 100)->nullable();
            $table->string('contexto_problema', 255)->nullable();
            $table->text('estrategias_afrontamiento')->nullable();
            $table->string('consecuencias', 255)->nullable();
            $table->text('acontecimientos_estresantes')->nullable();
            $table->text('expectativas')->nullable();
            
            // Historia perinatal
            $table->enum('tipo_parto', ['Natural', 'Cesárea', 'Inducido'])->nullable();
            $table->string('causa_parto_inducido', 255)->nullable();
            $table->text('dificultades_parto')->nullable();
            $table->string('comportamiento_bebe', 255)->nullable();
            $table->string('reacciones_estimulos', 255)->nullable();
            $table->string('situacion_familiar_postnatal', 255)->nullable();
            $table->text('aceptacion_maternidad')->nullable();
            
            // Desarrollo evolutivo
            $table->text('desarrollo_areas')->nullable();
            $table->text('habla_lenguaje')->nullable();
            $table->string('alimentacion_bebe', 255)->nullable();
            $table->string('dificultades_destete', 255)->nullable();
            $table->string('trastornos_alimentacion', 255)->nullable();
            $table->text('alimentacion_actual')->nullable();
            $table->text('descripcion_dia_anterior')->nullable();
            $table->text('info_sexualidad')->nullable();
            $table->string('desarrollo_sexual', 255)->nullable();
            
            // Área escolar y conductual
            $table->text('tareas_escolares')->nullable();
            $table->string('rendimiento_escolar', 100)->nullable();
            $table->string('responsabilidades_domesticas', 255)->nullable();
            $table->string('recompensas', 255)->nullable();
            $table->enum('nivel_actividad', ['Alto', 'Medio', 'Bajo'])->nullable();
            $table->string('entretenimiento', 255)->nullable();
            $table->enum('tipo_actividades', ['Sedentarias', 'Inquietas', 'Mixtas'])->nullable();
            $table->enum('finalizacion_tareas', ['Sí, siempre', 'A veces', 'No'])->nullable();
            $table->text('arrebatos')->nullable();
            $table->text('oposicionismo')->nullable();
            $table->text('agresiones')->nullable();
            
            // Relaciones sociales
            $table->text('autoimagen')->nullable();
            $table->text('percepcion_demas')->nullable();
            $table->text('relaciones_ninos')->nullable();
            $table->enum('tiene_amigos', ['Sí', 'No', 'Pocos'])->nullable();
            $table->string('actividades_sociales', 255)->nullable();
            $table->string('tipo_ninos_atraen', 255)->nullable();
            $table->text('comportamiento_grupo')->nullable();
            $table->string('juegos_preferidos', 255)->nullable();
            $table->text('relacion_adultos')->nullable();
            $table->string('entretenimiento_casa', 255)->nullable();
            $table->string('actividades_exteriores', 255)->nullable();
            $table->string('actividades_deportivas', 255)->nullable();
            $table->text('intereses')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_ninos');
    }
};