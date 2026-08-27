<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesionController extends Controller
{
    public function index(Request $request)
    {
        $sesiones = Sesion::with(['paciente.detalle', 'user', 'cita'])
            ->when($request->filled('paciente_id'), function ($q) use ($request) {
                $q->where('paciente_id', $request->paciente_id);
            })
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        return view('sesiones.index', compact('sesiones'));
    }

    public function create(Request $request)
    {
        $cita = null;
        $pacienteId = $request->paciente_id;

        if ($request->filled('cita_id')) {
            $cita = Cita::with('paciente.detalle')->find($request->cita_id);
            if ($cita) {
                $pacienteId = $cita->paciente_id;
            }
        }

        $pacientes = Paciente::with(['detalle', 'acompanantes'])->orderBy('created_at', 'desc')->get();

        return view('sesiones.create', compact('cita', 'pacientes', 'pacienteId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cita_id' => 'nullable|exists:citas,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'acompanante_id' => 'nullable|exists:acompanantes,id',
            'fecha' => 'required|date',
            'duracion_minutos' => 'nullable|integer|min:1|max:300',
            'resumen' => 'required|string|min:5',
            'observaciones_clinicas' => 'nullable|string',
        ]);

        $sesion = Sesion::create([
            ...$request->only(['cita_id', 'paciente_id', 'acompanante_id', 'fecha', 'duracion_minutos', 'resumen', 'observaciones_clinicas']),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('sesiones.show', $sesion)
            ->with('success', 'Sesión registrada correctamente')
            ->with('mostrar_agendar', true)
            ->with('paciente_id', $sesion->paciente_id)
            ->with('fecha_sesion', $sesion->fecha->format('Y-m-d'));
    }

    public function show(Sesion $sesion)
    {
        $sesion->load(['paciente.detalle', 'user', 'cita']);

        return view('sesiones.show', compact('sesion'));
    }

    public function edit(Sesion $sesion)
    {
        $sesion->load(['cita', 'paciente.detalle', 'acompanante']);
        $pacientes = Paciente::with(['detalle', 'acompanantes'])->orderBy('created_at', 'desc')->get();

        return view('sesiones.edit', compact('sesion', 'pacientes'));
    }

    public function update(Request $request, Sesion $sesion)
    {
        $request->validate([
            'acompanante_id' => 'nullable|exists:acompanantes,id',
            'fecha' => 'required|date',
            'duracion_minutos' => 'nullable|integer|min:1|max:300',
            'resumen' => 'required|string|min:5',
            'observaciones_clinicas' => 'nullable|string',
        ]);

        $sesion->update($request->only(['acompanante_id', 'fecha', 'duracion_minutos', 'resumen', 'observaciones_clinicas']));

        return redirect()->route('sesiones.show', $sesion)
            ->with('success', 'Sesión actualizada');
    }

    public function destroy(Sesion $sesion)
    {
        $sesion->delete();

        return redirect()->route('sesiones.index')
            ->with('success', 'Sesión eliminada');
    }

    public function agendarProxima(Sesion $sesion)
    {
        $fechaProxima = \Carbon\Carbon::parse($sesion->fecha)->addDays(15)->format('Y-m-d');

        $citaExistente = \App\Models\Cita::where('paciente_id', $sesion->paciente_id)
            ->whereDate('fecha', $fechaProxima)
            ->where('estado', '!=', 'cancelada')
            ->first();

        if ($citaExistente) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una cita para este paciente el ' . \Carbon\Carbon::parse($fechaProxima)->format('d/m/Y') . '.',
            ]);
        }

        $citasEnFecha = \App\Models\Cita::whereDate('fecha', $fechaProxima)->where('estado', '!=', 'cancelada')->count();
        if ($citasEnFecha >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'No hay cupos disponibles para el ' . \Carbon\Carbon::parse($fechaProxima)->format('d/m/Y') . '.',
            ]);
        }

        $cita = \App\Models\Cita::create([
            'paciente_id' => $sesion->paciente_id,
            'fecha' => $fechaProxima,
            'objetivo' => 'Seguimiento de sesión del ' . $sesion->fecha->format('d/m/Y'),
            'estado' => 'pendiente',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Próxima cita agendada para el ' . \Carbon\Carbon::parse($fechaProxima)->format('d/m/Y') . '.',
            'fecha' => $fechaProxima,
        ]);
    }
}
