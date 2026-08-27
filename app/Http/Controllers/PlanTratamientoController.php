<?php

namespace App\Http\Controllers;

use App\Models\PlanTratamiento;
use App\Models\PlanObjetivo;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanTratamientoController extends Controller
{
    public function index(Request $request)
    {
        $planes = PlanTratamiento::with(['paciente.detalle', 'user', 'objetivos'])
            ->when($request->filled('estado'), function ($q) use ($request) {
                $q->where('estado', $request->estado);
            })
            ->when($request->filled('paciente_id'), function ($q) use ($request) {
                $q->where('paciente_id', $request->paciente_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('tratamiento.index', compact('planes'));
    }

    public function create(Request $request)
    {
        $pacientes = Paciente::with('detalle')->orderBy('created_at', 'desc')->get();
        $pacienteSeleccionado = $request->paciente_id ?? null;

        return view('tratamiento.create', compact('pacientes', 'pacienteSeleccionado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'titulo' => 'required|string|max:255',
            'objetivo_general' => 'nullable|string',
            'estado' => 'required|in:activo,pausado,completado,cancelado',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string',
        ]);

        $plan = PlanTratamiento::create([
            ...$request->only(['paciente_id', 'titulo', 'objetivo_general', 'estado', 'fecha_inicio', 'fecha_fin_estimada', 'observaciones']),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('tratamiento.show', $plan)
            ->with('success', 'Plan de tratamiento creado');
    }

    public function show(PlanTratamiento $tratamiento)
    {
        $tratamiento->load(['paciente.detalle', 'user', 'objetivos']);

        return view('tratamiento.show', ['plan' => $tratamiento]);
    }

    public function edit(PlanTratamiento $tratamiento)
    {
        $tratamiento->load('paciente.detalle');
        $pacientes = Paciente::with('detalle')->orderBy('created_at', 'desc')->get();

        return view('tratamiento.edit', ['plan' => $tratamiento, 'pacientes' => $pacientes]);
    }

    public function update(Request $request, PlanTratamiento $tratamiento)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'objetivo_general' => 'nullable|string',
            'estado' => 'required|in:activo,pausado,completado,cancelado',
            'fecha_inicio' => 'required|date',
            'fecha_fin_estimada' => 'nullable|date',
            'fecha_fin_real' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->only(['titulo', 'objetivo_general', 'estado', 'fecha_inicio', 'fecha_fin_estimada', 'fecha_fin_real', 'observaciones']);

        if ($request->estado === 'completado' && !$tratamiento->fecha_fin_real) {
            $data['fecha_fin_real'] = now()->toDateString();
        }

        $tratamiento->update($data);

        return redirect()->route('tratamiento.show', $tratamiento)
            ->with('success', 'Plan actualizado');
    }

    public function destroy(PlanTratamiento $tratamiento)
    {
        $tratamiento->delete();

        return redirect()->route('tratamiento.index')
            ->with('success', 'Plan eliminado');
    }

    public function storeObjetivo(Request $request, PlanTratamiento $tratamiento)
    {
        $request->validate([
            'descripcion' => 'required|string|max:500',
            'meta' => 'nullable|string',
            'fecha_limite' => 'nullable|date',
        ]);

        $orden = $tratamiento->objetivos()->max('orden') + 1;

        $tratamiento->objetivos()->create([
            ...$request->only(['descripcion', 'meta', 'fecha_limite']),
            'orden' => $orden,
        ]);

        return redirect()->route('tratamiento.show', $tratamiento)
            ->with('success', 'Objetivo agregado');
    }

    public function updateObjetivo(Request $request, PlanObjetivo $objetivo)
    {
        $request->validate([
            'descripcion' => 'required|string|max:500',
            'meta' => 'nullable|string',
            'fecha_limite' => 'nullable|date',
        ]);

        $objetivo->update($request->only(['descripcion', 'meta', 'fecha_limite']));

        return redirect()->route('tratamiento.show', $objetivo->plan)
            ->with('success', 'Objetivo actualizado');
    }

    public function destroyObjetivo(PlanObjetivo $objetivo)
    {
        $plan = $objetivo->plan;
        $objetivo->delete();

        return redirect()->route('tratamiento.show', $plan)
            ->with('success', 'Objetivo eliminado');
    }

    public function cambiarEstadoObjetivo(Request $request, PlanObjetivo $objetivo)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,cumplido',
        ]);

        $objetivo->update(['estado' => $request->estado]);

        return redirect()->route('tratamiento.show', $objetivo->plan)
            ->with('success', 'Estado del objetivo actualizado');
    }
}
