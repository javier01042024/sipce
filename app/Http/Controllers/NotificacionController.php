<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index()
    {
        Notificacion::limpiarVencidas();

        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20);

        return view('notificaciones.index', compact('notificaciones'));
    }

    /**
     * Últimas notificaciones para el panel del campanilla (topbar).
     */
    public function recientes()
    {
        Notificacion::limpiarVencidas();

        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->orderBy('fecha_hora', 'desc')
            ->limit(15)
            ->get()
            ->map(function (Notificacion $notificacion) {
                return [
                    'id'      => $notificacion->id,
                    'tipo'    => $notificacion->tipo,
                    'titulo'  => $notificacion->titulo,
                    'mensaje' => $notificacion->mensaje,
                    'leida'   => (bool) $notificacion->leida,
                    'fecha'   => optional($notificacion->fecha_hora)->diffForHumans(),
                    'url'     => $notificacion->url,
                ];
            })
            ->values();

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas'      => Notificacion::where('user_id', Auth::id())->where('leida', false)->count(),
            'retencion'      => Notificacion::DIAS_RETENCION,
        ]);
    }

    public function noLeidas()
    {
        $count = Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function marcarLeida(Notificacion $notificacion)
    {
        if ($notificacion->user_id !== Auth::id()) {
            abort(403);
        }

        $notificacion->marcarLeida();

        return response()->json(['success' => true]);
    }

    public function marcarTodasLeidas()
    {
        Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return response()->json(['success' => true]);
    }

    public function eliminar(Notificacion $notificacion)
    {
        if ($notificacion->user_id !== Auth::id()) {
            abort(403);
        }

        $notificacion->delete();

        return response()->json(['success' => true]);
    }
}
