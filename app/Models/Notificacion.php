<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Notificacion extends Model
{
    use HasUuid;

    protected $table = 'notificaciones';

    protected $fillable = [
        'uuid',
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'datos',
        'leida',
        'fecha_hora',
    ];

    protected $casts = [
        'datos' => 'array',
        'leida' => 'boolean',
        'fecha_hora' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function marcarLeida()
    {
        $this->update(['leida' => true]);
    }
}
