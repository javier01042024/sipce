<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Diagnostico extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'paciente_id',
        'user_id',
        'codigo_cie',
        'diagnostico',
        'observaciones',
        'es_principal',
        'fecha',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
        'fecha' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
