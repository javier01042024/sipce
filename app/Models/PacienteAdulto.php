<?php
// app/Models/PacienteAdulto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class PacienteAdulto extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'paciente_adultos';
    
    protected $fillable = [
        // Datos personales
        'uuid',
        'nombre',
        'apellido',
        'cedula',
        'fecha_nacimiento',
        'genero',
        'estado_civil',
        'telefono',
        'email',
        'direccion',
        'nivel_instruccion',
        'fecha_entrevista',

        // Datos laborales
        'ocupacion',
        'lugar_trabajo',
        'telefono_trabajo',

        // Contacto de emergencia
        'nombre_emergencia',
        'telefono_emergencia',
        'parentesco_emergencia',

        // Motivo de consulta
        'motivo_consulta',
        'historia_problema',
        'frecuencia_sintomas',
        'intensidad_sintomas',
        'factores_desencadenantes',
        'estrategias_afrontamiento',

        // Sensaciones y pensamientos
        'sensaciones_corporales',

        // Riesgo
        'autolesiones',
        'ideacion_suicida',
        'consumo_sustancias',
        'estrategias_previas',
        'objetivos_terapia',

        // Historia familiar y social
        'convivencia',
        'relacion_familiar',
        'conflictos_familiares',
        'vida_social',
        'pareja',
        'redes_sociales',

        // Vida sexual
        'estado_sexual',
        'problemas_sexuales',
        'actividad_sexual',
        'historial_problemas_sexuales',

        // Evaluación emocional
        'estado_animo',
        'sintomas_depresivos',
        'sintomas_ansiedad',
        'irritabilidad',
        'conductas_riesgo',
        'sintomas_fisicos',
        'calidad_vida',

        // Antecedentes
        'antecedentes_psiquiatricos',
        'medicamentos_actuales',
        'antecedentes_familiares',
        'consume_alcohol',
        'consume_tabaco',
        'otras_sustancias',
        'habitos_sueno',

        // Recursos y fortalezas
        'fortalezas',
        'logros',
        'red_apoyo',
        'motivacion_cambio',
        'valores_accion',

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
        return trim($this->nombre . ' ' . $this->apellido);
    }

    // Agrega este mutator para guardar la fecha correctamente
    public function setFechaNacimientoAttribute($value)
    {
        if (str_contains($value, '/')) {
            $this->attributes['fecha_nacimiento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } else {
            $this->attributes['fecha_nacimiento'] = $value;
        }
    }
}

