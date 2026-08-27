<?php
// app/Models/PacienteNino.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class PacienteNino extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'paciente_ninos';
    
    protected $fillable = [
        'uuid',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'telefono_contacto',
        'grado_instruccion',
        'maestro',
        'direccion',
        'nombre_padre',
        'edad_padre',
        'ocupacion_padre',
        'nombre_madre',
        'edad_madre',
        'ocupacion_madre',
        'hermanos',
        'personas_vive',
        'desarrollo_problema',
        'frecuencia_sintomas',
        'intensidad_sintomas',
        'duracion_sintomas',
        'contexto_problema',
        'estrategias_afrontamiento',
        'consecuencias',
        'acontecimientos_estresantes',
        'expectativas',
        'tipo_parto',
        'causa_parto_inducido',
        'dificultades_parto',
        'comportamiento_bebe',
        'reacciones_estimulos',
        'situacion_familiar_postnatal',
        'aceptacion_maternidad',
        'desarrollo_areas',
        'habla_lenguaje',
        'alimentacion_bebe',
        'dificultades_destete',
        'trastornos_alimentacion',
        'alimentacion_actual',
        'descripcion_dia_anterior',
        'info_sexualidad',
        'desarrollo_sexual',
        'tareas_escolares',
        'rendimiento_escolar',
        'responsabilidades_domesticas',
        'recompensas',
        'nivel_actividad',
        'entretenimiento',
        'tipo_actividades',
        'finalizacion_tareas',
        'arrebatos',
        'oposicionismo',
        'agresiones',
        'autoimagen',
        'percepcion_demas',
        'relaciones_ninos',
        'tiene_amigos',
        'actividades_sociales',
        'tipo_ninos_atraen',
        'comportamiento_grupo',
        'juegos_preferidos',
        'relacion_adultos',
        'entretenimiento_casa',
        'actividades_exteriores',
        'actividades_deportivas',
        'intereses',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'edad_padre' => 'integer',
        'edad_madre' => 'integer',
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