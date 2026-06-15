<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  
        'fecha',
        'contenido'
    ];

    /**
     * Relación con el usuario (dueño del diario)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);  // Cambiado de Paciente::class a User::class
    }
}