<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class PlanTratamiento extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'planes_tratamiento';

    protected $fillable = [
        'uuid',
        'paciente_id',
        'user_id',
        'titulo',
        'objetivo_general',
        'estado',
        'fecha_inicio',
        'fecha_fin_estimada',
        'fecha_fin_real',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin_estimada' => 'date',
        'fecha_fin_real' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function objetivos()
    {
        return $this->hasMany(PlanObjetivo::class, 'plan_id')->orderBy('orden');
    }

    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            'activo' => 'Activo',
            'pausado' => 'Pausado',
            'completado' => 'Completado',
            'cancelado' => 'Cancelado',
            default => 'Desconocido',
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'activo' => 'success',
            'pausado' => 'warning',
            'completado' => 'info',
            'cancelado' => 'danger',
            default => 'neutral',
        };
    }
}
