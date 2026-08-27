<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class PlanObjetivo extends Model
{
    use HasUuid;
    protected $table = 'plan_objetivos';

    protected $fillable = [
        'uuid',
        'plan_id',
        'descripcion',
        'meta',
        'estado',
        'fecha_limite',
        'orden',
    ];

    protected $casts = [
        'fecha_limite' => 'date',
        'orden' => 'integer',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanTratamiento::class, 'plan_id');
    }

    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'cumplido' => 'Cumplido',
            default => 'Desconocido',
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'neutral',
            'en_progreso' => 'warning',
            'cumplido' => 'success',
            default => 'neutral',
        };
    }
}
