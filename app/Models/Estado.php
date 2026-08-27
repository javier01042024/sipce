<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Estado extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'estados';
    
    protected $fillable = [
        'uuid',
        'tipo',
        'descripcion',
        'permite_citas' // ← NUEVO
    ];

    protected $casts = [
        'permite_citas' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePermiteCitas($query)
    {
        return $query->where('permite_citas', true);
    }
}