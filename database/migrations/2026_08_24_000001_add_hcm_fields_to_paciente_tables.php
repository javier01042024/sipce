<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =============================================
        // CAMPOS NUEVOS PARA ADOLESCENTES
        // =============================================
        Schema::table('paciente_adolescentes', function (Blueprint $table) {
            // Datos personales nuevos
            $table->string('direccion', 255)->nullable()->after('email_personal');
            $table->string('grado_instruccion', 50)->nullable()->after('nivel_educativo');
            $table->date('fecha_entrevista')->nullable()->after('grado_instruccion');

            // Familia - Padre
            $table->string('padre_nombre', 150)->nullable()->after('parentesco');
            $table->integer('padre_edad')->nullable()->after('padre_nombre');
            $table->string('padre_ocupacion', 100)->nullable()->after('padre_edad');

            // Familia - Madre
            $table->string('madre_nombre', 150)->nullable()->after('padre_ocupacion');
            $table->integer('madre_edad')->nullable()->after('madre_nombre');
            $table->string('madre_ocupacion', 100)->nullable()->after('madre_edad');

            // Familia - Convivencia
            $table->text('hermanos')->nullable()->after('madre_ocupacion');
            $table->text('personas_vive')->nullable()->after('hermanos');

            // Motivo de consulta expandido
            $table->text('pads_observaciones')->nullable()->after('factores_desencadenantes');

            // Historia del problema
            $table->text('cuando_empezo')->nullable()->after('pads_observaciones');
            $table->text('cambios_rendimiento')->nullable()->after('cuando_empezo');

            // Relaciones sociales
            $table->text('rechazo_bullying')->nullable()->after('actividades_grupales');
            $table->text('pareja')->nullable()->after('rechazo_bullying');
            $table->text('redes_sociales')->nullable()->after('pareja');
            $table->text('mensajes_dano')->nullable()->after('redes_sociales');

            // Salud
            $table->text('salud_fisica')->nullable()->after('mensajes_dano');
            $table->text('terapia_anterior')->nullable()->after('salud_fisica');

            // Consumo de sustancias
            $table->text('sustancias_frecuencia')->nullable()->after('terapia_anterior');
            $table->text('reducir_consumo')->nullable()->after('sustancias_frecuencia');
            $table->text('molesta_criticas')->nullable()->after('reducir_consumo');
            $table->text('usar_para_olvidar')->nullable()->after('molesta_criticas');
            $table->text('olvidar_lo_que_paso')->nullable()->after('usar_para_olvidar');

            // Sexualidad e identidad
            $table->text('orientacion_sexual')->nullable()->after('olvidar_lo_que_paso');

            // Evaluación emocional
            $table->text('depresion')->nullable()->after('orientacion_sexual');
            $table->text('ansiedad_panico')->nullable()->after('depresion');
            $table->text('irritabilidad_impulso')->nullable()->after('ansiedad_panico');
            $table->text('conductas_riesgo')->nullable()->after('irritabilidad_impulso');

            // Recursos y fortalezas
            $table->text('que_haces_bien')->nullable()->after('conductas_riesgo');
            $table->text('que_te_da_orgullo')->nullable()->after('que_haces_bien');
            $table->text('quien_te_respalda')->nullable()->after('que_te_da_orgullo');
            $table->integer('cuanto_quieres_cambio')->nullable()->after('quien_te_respalda');
            $table->text('mini_ejercicio')->nullable()->after('cuanto_quieres_cambio');

            // Observaciones
            $table->text('observaciones')->nullable()->after('mini_ejercicio');
        });

        // =============================================
        // CAMPOS NUEVOS PARA ADULTOS
        // =============================================
        Schema::table('paciente_adultos', function (Blueprint $table) {
            // Datos personales nuevos
            $table->string('nivel_instruccion', 50)->nullable()->after('direccion');
            $table->date('fecha_entrevista')->nullable()->after('nivel_instruccion');

            // Motivo de consulta expandido
            $table->text('sensaciones_corporales')->nullable()->after('estrategias_afrontamiento');
            $table->text('autolesiones')->nullable()->after('sensaciones_corporales');
            $table->text('ideacion_suicida')->nullable()->after('autolesiones');
            $table->text('consumo_sustancias')->nullable()->after('ideacion_suicida');
            $table->text('estrategias_previas')->nullable()->after('consumo_sustancias');
            $table->text('objetivos_terapia')->nullable()->after('estrategias_previas');

            // Historia familiar y social
            $table->text('convivencia')->nullable()->after('objetivos_terapia');
            $table->text('relacion_familiar')->nullable()->after('convivencia');
            $table->text('conflictos_familiares')->nullable()->after('relacion_familiar');
            $table->text('vida_social')->nullable()->after('conflictos_familiares');
            $table->text('pareja')->nullable()->after('vida_social');
            $table->text('redes_sociales')->nullable()->after('pareja');

            // Vida sexual
            $table->text('estado_sexual')->nullable()->after('redes_sociales');
            $table->text('problemas_sexuales')->nullable()->after('estado_sexual');
            $table->text('actividad_sexual')->nullable()->after('problemas_sexuales');
            $table->text('historial_problemas_sexuales')->nullable()->after('actividad_sexual');

            // Evaluación emocional
            $table->text('estado_animo')->nullable()->after('historial_problemas_sexuales');
            $table->text('sintomas_depresivos')->nullable()->after('estado_animo');
            $table->text('sintomas_ansiedad')->nullable()->after('sintomas_depresivos');
            $table->text('irritabilidad')->nullable()->after('sintomas_ansiedad');
            $table->text('conductas_riesgo')->nullable()->after('irritabilidad');
            $table->text('sintomas_fisicos')->nullable()->after('conductas_riesgo');
            $table->text('calidad_vida')->nullable()->after('sintomas_fisicos');

            // Recursos y fortalezas
            $table->text('fortalezas')->nullable()->after('calidad_vida');
            $table->text('logros')->nullable()->after('fortalezas');
            $table->text('red_apoyo')->nullable()->after('logros');
            $table->integer('motivacion_cambio')->nullable()->after('red_apoyo');
            $table->text('valores_accion')->nullable()->after('motivacion_cambio');

            // Observaciones
            $table->text('observaciones')->nullable()->after('valores_accion');
        });
    }

    public function down(): void
    {
        Schema::table('paciente_adolescentes', function (Blueprint $table) {
            $table->dropColumn([
                'direccion', 'grado_instruccion', 'fecha_entrevista',
                'padre_nombre', 'padre_edad', 'padre_ocupacion',
                'madre_nombre', 'madre_edad', 'madre_ocupacion',
                'hermanos', 'personas_vive',
                'pads_observaciones', 'cuando_empezo', 'cambios_rendimiento',
                'rechazo_bullying', 'pareja', 'redes_sociales', 'mensajes_dano',
                'salud_fisica', 'terapia_anterior',
                'sustancias_frecuencia', 'reducir_consumo', 'molesta_criticas',
                'usar_para_olvidar', 'olvidar_lo_que_paso',
                'orientacion_sexual', 'depresion', 'ansiedad_panico',
                'irritabilidad_impulso', 'conductas_riesgo',
                'que_haces_bien', 'que_te_da_orgullo', 'quien_te_respalda',
                'cuanto_quieres_cambio', 'mini_ejercicio', 'observaciones',
            ]);
        });

        Schema::table('paciente_adultos', function (Blueprint $table) {
            $table->dropColumn([
                'nivel_instruccion', 'fecha_entrevista',
                'sensaciones_corporales', 'autolesiones', 'ideacion_suicida',
                'consumo_sustancias', 'estrategias_previas', 'objetivos_terapia',
                'convivencia', 'relacion_familiar', 'conflictos_familiares',
                'vida_social', 'pareja', 'redes_sociales',
                'estado_sexual', 'problemas_sexuales', 'actividad_sexual',
                'historial_problemas_sexuales',
                'estado_animo', 'sintomas_depresivos', 'sintomas_ansiedad',
                'irritabilidad', 'conductas_riesgo', 'sintomas_fisicos', 'calidad_vida',
                'fortalezas', 'logros', 'red_apoyo', 'motivacion_cambio',
                'valores_accion', 'observaciones',
            ]);
        });
    }
};
