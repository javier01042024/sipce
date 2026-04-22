<?php

namespace App\Models;

use App\Http\Controllers\DiarioController;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
  protected $fillable = [
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
public function citas()
{
    return $this->hasMany(Cita::class);
}

public function diarios()
{
    return $this->hasMany(Diario::class);
}
}

