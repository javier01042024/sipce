<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Sesion extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'sesiones';

    protected $fillable = [
        'uuid',
        'cita_id',
        'paciente_id',
        'acompanante_id',
        'user_id',
        'fecha',
        'duracion_minutos',
        'resumen',
        'observaciones_clinicas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'duracion_minutos' => 'integer',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function acompanante()
    {
        return $this->belongsTo(Acompanante::class);
    }

    public function getDuracionFormatoAttribute()
    {
        if (!$this->duracion_minutos) return 'N/D';
        $horas = floor($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;
        if ($horas > 0) {
            return "{$horas}h {$minutos}min";
        }
        return "{$minutos} min";
    }
}
