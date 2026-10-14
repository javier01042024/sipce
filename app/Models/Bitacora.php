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
     * Descripción legible del cambio, decodificando los datos JSON
     * de la bitácora y enmascarando campos sensibles.
     */
    public function getDescripcionLegibleAttribute(): string
    {
        $viejo = $this->formatearDatos($this->datos_viejos);
        $nuevo = $this->formatearDatos($this->datos_nuevos);

        if ($viejo && $nuevo && $viejo !== $nuevo) {
            return $viejo . '  →  ' . $nuevo;
        }

        return $nuevo ?: $viejo;
    }

    protected function formatearDatos($raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return (string) $raw;
        }

        $partes = [];
        foreach ($data as $clave => $valor) {
            if (is_array($valor)) {
                $valor = json_encode($valor, JSON_UNESCAPED_UNICODE);
            }
            if (in_array($clave, ['password', 'password_confirmation', 'token', 'remember_token'], true)) {
                $valor = '***';
            }
            $partes[] = ucwords(str_replace('_', ' ', $clave)) . ': ' . $valor;
        }

        return implode('; ', $partes);
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