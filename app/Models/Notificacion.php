<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Notificacion extends Model
{
    use HasUuid;

    /**
     * Días que una notificación permanece en el sistema antes de borrarse.
     */
    public const DIAS_RETENCION = 30;

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

    /**
     * Destino interno de la notificación (misma pestaña). Null si no tiene.
     */
    public function getUrlAttribute(): ?string
    {
        $datos = $this->datos;

        return is_array($datos) && !empty($datos['url']) ? $datos['url'] : null;
    }

    /**
     * Borra las notificaciones con más de DIAS_RETENCION días.
     */
    public static function limpiarVencidas(): int
    {
        return static::where('fecha_hora', '<', now()->subDays(static::DIAS_RETENCION))->delete();
    }
}
