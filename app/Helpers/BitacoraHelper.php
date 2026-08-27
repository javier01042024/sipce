<?php

namespace App\Helpers;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class BitacoraHelper
{
    public static function registrar(string $accion, string $tabla, ?int $registroId = null, ?array $viejos = null, ?array $nuevos = null): void
    {
        $user = Auth::user();

        Bitacora::create([
            'usuario_id' => $user?->id,
            'usuario_nombre' => $user?->name ?? 'Sistema',
            'accion' => $accion,
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'datos_viejos' => $viejos ? json_encode($viejos, JSON_UNESCAPED_UNICODE) : null,
            'datos_nuevos' => $nuevos ? json_encode($nuevos, JSON_UNESCAPED_UNICODE) : null,
            'ip' => Request::ip(),
            'fecha_hora' => now(),
        ]);
    }

    public static function exito(string $accion, string $tabla, ?string $detalle = null, ?array $extra = null): void
    {
        $datos = $detalle ? ['detalle' => $detalle] : [];
        if ($extra) {
            $datos = array_merge($datos, $extra);
        }
        self::registrar($accion, $tabla, null, null, $datos);
    }

    public static function error(string $accion, string $tabla, ?string $detalle = null, ?array $extra = null): void
    {
        $datos = $detalle ? ['error' => $detalle] : [];
        if ($extra) {
            $datos = array_merge($datos, $extra);
        }
        self::registrar('ERROR_' . $accion, $tabla, null, null, $datos);
    }

    public static function creado(string $tabla, int $id, array $datos): void
    {
        self::registrar('CREATE', $tabla, $id, null, $datos);
    }

    public static function actualizado(string $tabla, int $id, array $viejos, array $nuevos): void
    {
        self::registrar('UPDATE', $tabla, $id, $viejos, $nuevos);
    }

    public static function eliminado(string $tabla, int $id, array $datos): void
    {
        self::registrar('DELETE', $tabla, $id, $datos, null);
    }

    public static function login(): void
    {
        self::registrar('LOGIN', 'users', Auth::id(), null, [
            'email' => Auth::user()?->email,
        ]);
    }
}
