<?php
// app/Models/Paciente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Paciente extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'tipo_paciente',
        'paciente_detalle_id',
        'paciente_detalle_type',
        'user_id',
        'estado_id',
        'numero_expediente',
        'motivo_consulta',
        'diagnostico_preliminar',
        'prioridad',
        'tipo_atencion',
        'municipio',
    ];

    protected $casts = [
        'estado_id' => 'integer',
        'user_id' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function detalle()
    {
        return $this->morphTo(__FUNCTION__, 'paciente_detalle_type', 'paciente_detalle_id');
    }

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class)->orderBy('created_at', 'desc');
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class)->orderBy('fecha', 'desc');
    }

    public function planesTratamiento()
    {
        return $this->hasMany(PlanTratamiento::class);
    }

    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class)->orderBy('fecha', 'desc');
    }

    public function diagnosticoPrincipal()
    {
        return $this->hasOne(Diagnostico::class)->where('es_principal', true)->latest();
    }

    public function acompanantes()
    {
        return $this->hasMany(Acompanante::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNombreCompletoAttribute()
    {
        return $this->detalle ? $this->detalle->nombre_completo : 'Sin nombre';
    }

    public function getTelefonoAttribute()
    {
        if (!$this->detalle) return null;
        
        return $this->detalle->telefono 
            ?? $this->detalle->telefono_personal 
            ?? $this->detalle->telefono_contacto
            ?? $this->detalle->telefono_representante 
            ?? null;
    }

    public function getEmailAttribute()
    {
        if (!$this->detalle) return null;
        
        return $this->detalle->email 
            ?? $this->detalle->email_personal 
            ?? $this->detalle->email_representante 
            ?? null;
    }

    public function getCedulaPacienteAttribute()
    {
        if (!$this->detalle) return null;
        
        return $this->detalle->cedula 
            ?? $this->detalle->cedula_representante 
            ?? null;
    }

    public function getFechaNacimientoAttribute()
    {
        return $this->detalle ? $this->detalle->fecha_nacimiento : null;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_paciente', $tipo);
    }

    public function scopeConEstado($query, $estadoTipo)
    {
        return $query->whereHas('estado', function($q) use ($estadoTipo) {
            $q->where('tipo', $estadoTipo);
        });
    }
}