<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Nota extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'paciente_id',
        'user_id',
        'anotacion',
        'diario_id',
    ];

    protected $casts = [
        'paciente_id' => 'integer',
        'user_id' => 'integer',
        'diario_id' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function diario()
    {
        return $this->belongsTo(Diario::class);
    }
}