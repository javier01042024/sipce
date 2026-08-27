<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class PacienteAdolescente extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'paciente_adolescentes';
    
    protected $fillable = [
        // Datos personales
        'uuid',
        'nombre',
        'apellido',
        'cedula',
        'fecha_nacimiento',
        'genero',
        'telefono_personal',
        'email_personal',
        'institucion_educativa',
        'nivel_educativo',
        'direccion',
        'grado_instruccion',
        'fecha_entrevista',

        // Representante
        'nombre_representante',
        'cedula_representante',
        'telefono_representante',
        'email_representante',
        'parentesco',

        // Familia
        'padre_nombre',
        'padre_edad',
        'padre_ocupacion',
        'madre_nombre',
        'madre_edad',
        'madre_ocupacion',
        'hermanos',
        'personas_vive',

        // Motivo de consulta
        'historia_problema',
        'frecuencia_sintomas',
        'intensidad_sintomas',
        'factores_desencadenantes',
        'pads_observaciones',

        // Historia del problema
        'cuando_empezo',
        'cambios_rendimiento',

        // Hábitos y relaciones
        'habitos',
        'relaciones_sociales',
        'rendimiento_academico',
        'tiene_amigos',
        'actividades_grupales',
        'rechazo_bullying',
        'pareja',
        'redes_sociales',
        'mensajes_dano',

        // Salud
        'salud_fisica',
        'terapia_anterior',

        // Consumo de sustancias
        'sustancias_frecuencia',
        'reducir_consumo',
        'molesta_criticas',
        'usar_para_olvidar',
        'olvidar_lo_que_paso',

        // Sexualidad
        'orientacion_sexual',

        // Evaluación emocional
        'depresion',
        'ansiedad_panico',
        'irritabilidad_impulso',
        'conductas_riesgo',
        'autoimagen',
        'relacion_padres',
        'manejo_emociones',
        'antecedentes_familiares',

        // Recursos
        'que_haces_bien',
        'que_te_da_orgullo',
        'quien_te_respalda',
        'cuanto_quieres_cambio',
        'mini_ejercicio',

        // Observaciones
        'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function paciente()
    {
        return $this->morphOne(Paciente::class, 'paciente_detalle');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}