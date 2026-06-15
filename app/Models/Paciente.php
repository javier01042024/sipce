<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $fillable = [
        'user_id', // ← AGREGAR
        'numero_expediente',
        'cedula_paciente',
        'nombre_completo',
        'fecha_nacimiento',
        'telefono',
        'email',
        'direccion',
        'motivo_consulta',
        'diagnostico_preliminar',
        'prioridad'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * NUEVA RELACIÓN: Un paciente puede tener un usuario (login)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS ÚTILES
    |--------------------------------------------------------------------------
    */

    /**
     * Verificar si el paciente tiene acceso al sistema
     *
     * @return bool
     */
    public function tieneAccesoAlSistema()
    {
        return $this->user_id !== null && $this->user !== null;
    }

    /**
     * Verificar si el paciente está activo (tiene usuario habilitado)
     *
     * @return bool
     */
    public function usuarioActivo()
    {
        return $this->tieneAccesoAlSistema() && $this->user->email_verified_at !== null;
    }
}