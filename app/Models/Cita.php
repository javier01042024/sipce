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
        'planificacion'
    ];

    protected $casts = [
        'fecha' => 'date',
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
    | SCOPES (Útiles luego)
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
}