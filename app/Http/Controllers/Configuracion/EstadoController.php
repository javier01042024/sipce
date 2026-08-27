<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EstadoController extends Controller
{
    public function index(): View
    {
        $estados = Estado::withCount('pacientes')
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return view('configuracion.estados.index', compact('estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50|unique:estados,tipo',
            'descripcion' => 'required|string|max:255',
            'permite_citas' => 'boolean'
        ], [
            'tipo.required' => 'El tipo de estado es obligatorio',
            'tipo.unique' => 'Este tipo de estado ya existe',
            'descripcion.required' => 'La descripción es obligatoria'
        ]);

        $validated['permite_citas'] = $request->has('permite_citas');

        $estado = Estado::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'estado' => $estado,
                'message' => 'Estado "' . $estado->tipo . '" creado.',
            ]);
        }

        return redirect()->route('configuracion.estados.index')
            ->with('success', 'Estado creado exitosamente.');
    }

    public function show(Estado $estado): JsonResponse
    {
        $estado->loadCount('pacientes');

        return response()->json($estado);
    }

    public function update(Request $request, Estado $estado): RedirectResponse
    {
        $validated = $request->validate([
            'tipo' => 'required|string|max:50|unique:estados,tipo,' . $estado->id,
            'descripcion' => 'required|string|max:255',
            'permite_citas' => 'boolean'
        ], [
            'tipo.required' => 'El tipo de estado es obligatorio',
            'tipo.unique' => 'Este tipo de estado ya existe',
            'descripcion.required' => 'La descripción es obligatoria'
        ]);

        $validated['permite_citas'] = $request->has('permite_citas');

        $estado->update($validated);

        return redirect()->route('configuracion.estados.index')
            ->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy(Estado $estado): RedirectResponse
    {
        if ($estado->pacientes()->exists()) {
            return redirect()->route('configuracion.estados.index')
                ->with('error', 'No se puede eliminar el estado porque tiene pacientes asignados.');
        }

        $estado->delete();

        return redirect()->route('configuracion.estados.index')
            ->with('success', 'Estado eliminado exitosamente.');
    }
}