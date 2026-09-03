<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AparienciaController extends Controller
{
    /**
     * Mostrar la vista de personalización de apariencia.
     */
    public function index()
    {
        $usuario = auth()->user();
        $colores = [
            'Morado SIPCE'  => '#667eea',
            'Azul'          => '#3b82f6',
            'Teal'          => '#14b8a6',
            'Verde'         => '#10b981',
            'Naranja'       => '#f59e0b',
            'Rojo'          => '#ef4444',
            'Rosa'          => '#ec4899',
            'Índigo'        => '#6366f1',
            'Cian'          => '#06b6d4',
            'Gris pizarra'  => '#64748b',
        ];

        return view('configuracion.apariencia.index', compact('usuario', 'colores'));
    }

    /**
     * Guardar las preferencias de apariencia del usuario.
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme_color' => ['nullable', 'string', 'max:20'],
            'dark_mode'   => ['nullable', 'boolean'],
        ]);

        $usuario = auth()->user();
        $color = $request->input('theme_color');
        if (in_array($color, ['', 'null', null], true)) {
            $color = null;
        }

        $usuario->theme_color = $color ?: null;
        $usuario->dark_mode = $request->boolean('dark_mode');
        $usuario->save();

        return back()->with('success', 'Apariencia actualizada correctamente.');
    }
}
