<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diario extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'fecha',
        'contenido'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}