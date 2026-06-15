<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'fecha',
        'estado',
        'objetivo',
        'planificacion',
        'motivo_cancelacion'
    ];

    protected $casts = [
        'fecha' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', now()->toDateString());
    }
    
    public function scopeFuturas($query)
    {
        return $query->whereDate('fecha', '>', now()->toDateString());
    }
    
    public function scopePasadas($query)
    {
        return $query->whereDate('fecha', '<', now()->toDateString());
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS & ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFechaFormateadaAttribute()
    {
        return $this->fecha ? $this->fecha->format('d/m/Y') : 'N/A';
    }
    
    public function getEstadoTextoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'atendida' => 'Atendida',
            'cancelada' => 'Cancelada',
            'no_asistio' => 'No Asistió'
        ];
        
        return $estados[$this->estado] ?? 'Desconocido';
    }
    
    public function getEstadoColorAttribute()
    {
        $colores = [
            'pendiente' => '#f59e0b',
            'atendida' => '#10b981',
            'cancelada' => '#ef4444',
            'no_asistio' => '#6b7280'
        ];
        
        return $colores[$this->estado] ?? '#6b7280';
    }
}