<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotaController extends Controller
{
    /**
     * Almacena una nueva nota para un paciente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'anotacion' => 'required|string|max:5000',
            'diario_id' => 'nullable|exists:diarios,id',
        ]);

        try {
            Nota::create([
                'paciente_id' => $request->paciente_id,
                'user_id' => Auth::id(),
                'anotacion' => $request->anotacion,
                'diario_id' => $request->diario_id,
            ]);

            return redirect()->back()->with('success', 'Nota guardada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar nota: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar la nota.')->withInput();
        }
    }

    /**
     * Elimina una nota.
     * Solo el autor de la nota o un administrador pueden eliminarla.
     */
    public function destroy(Nota $nota)
    {
        try {
            $user = Auth::user();
            if ($nota->user_id !== $user->id && !$user->isAdmin()) {
                return redirect()->back()->with('error', 'No tienes permiso para eliminar esta nota.');
            }

            $nota->delete();
            return redirect()->back()->with('success', 'Nota eliminada.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar nota: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar la nota.');
        }
    }
}