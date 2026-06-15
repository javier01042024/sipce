<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'usuario_id',
        'usuario_nombre',
        'accion',
        'tabla_afectada',
        'registro_id',
        'datos_viejos',
        'datos_nuevos',
        'ip',
        'fecha_hora'
    ];
    
    protected $casts = [
        'fecha_hora' => 'datetime',
        'registro_id' => 'integer'
    ];
    
    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    /**
     * Scopes para filtrar
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        if ($usuarioId) {
            return $query->where('usuario_id', $usuarioId);
        }
        return $query;
    }
    
    public function scopePorAccion($query, $accion)
    {
        if ($accion) {
            return $query->where('accion', $accion);
        }
        return $query;
    }
    
    public function scopePorTabla($query, $tabla)
    {
        if ($tabla) {
            return $query->where('tabla_afectada', $tabla);
        }
        return $query;
    }
    
    public function scopePorFecha($query, $desde, $hasta)
    {
        if ($desde) {
            $query->whereDate('fecha_hora', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha_hora', '<=', $hasta);
        }
        return $query;
    }
    
    /**
     * Obtener estadísticas
     */
    public static function getEstadisticas()
    {
        return [
            'total' => self::count(),
            'hoy' => self::whereDate('fecha_hora', today())->count(),
            'errores' => self::where('accion', 'DELETE')->orWhere('accion', 'ERROR')->count(),
            'login' => self::where('accion', 'LOGIN')->count()
        ];
    }
}