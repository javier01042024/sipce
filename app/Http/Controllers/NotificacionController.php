<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Notificacion::where('user_id', Auth::id())
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20);

        Notificacion::where('user_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);

        return view('notificaciones.index', compact('notificaciones'));
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
