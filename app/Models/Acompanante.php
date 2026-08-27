<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Acompanante extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'paciente_id',
        'nombre',
        'parentesco',
        'telefono',
        'cedula',
        'es_principal',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class);
    }
}
