<?php

namespace App\Models;

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
}
