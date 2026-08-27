<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CitaController extends Controller
{
    public function index()
{
    $citas = Cita::where('estado', 'pendiente')->get();
    
    foreach ($citas as $cita) {
        $cita->paciente = Paciente::find($cita->paciente_id);
        if ($cita->paciente) {
            $detalle = \App\Models\PacienteAdulto::find($cita->paciente->paciente_detalle_id);
            $cita->paciente->detalle = $detalle;
        }
    }

    $hoy = date('Y-m-d');
    
    $citasHoyPrivadas = collect();
    $citasHoyPublicas = collect();
    $citasProximasPrivadas = collect();
    $citasProximasPublicas = collect();
    
    foreach ($citas as $cita) {
        $fechaCita = $cita->fecha instanceof \Carbon\Carbon 
            ? $cita->fecha->format('Y-m-d') 
            : date('Y-m-d', strtotime($cita->fecha));
        
        $esHoy = ($fechaCita === $hoy);
        $esFutura = ($fechaCita > $hoy);
        $esPrivado = ($cita->paciente && $cita->paciente->tipo_atencion === 'privado');
        $cita->es_hoy = $esHoy;
        
        if ($esHoy && $esPrivado) {
            $citasHoyPrivadas->push($cita);
        } elseif ($esHoy && !$esPrivado) {
            $citasHoyPublicas->push($cita);
        } elseif ($esFutura && $esPrivado) {
            $citasProximasPrivadas->push($cita);
        } elseif ($esFutura && !$esPrivado) {
            $citasProximasPublicas->push($cita);
        }
    }
    
    $citasCanceladas = collect();

    $mostrarVacio = $citasHoyPrivadas->isEmpty()
        && $citasHoyPublicas->isEmpty()
        && $citasProximasPrivadas->isEmpty()
        && $citasProximasPublicas->isEmpty()
        && $citasCanceladas->isEmpty();

    return view('citas.index', compact(
        'citasHoyPrivadas',
        'citasHoyPublicas',
        'citasProximasPrivadas',
        'citasProximasPublicas',
        'citasCanceladas',
        'mostrarVacio'
    ));
}

    public function create(Request $request)
    {
        $pacientes = Paciente::orderBy('created_at', 'desc')->get()->map(function ($paciente) {
            $detalle = null;
            if ($paciente->paciente_detalle_type && $paciente->paciente_detalle_id) {
                try {
                    $detalle = app($paciente->paciente_detalle_type)->find($paciente->paciente_detalle_id);
                } catch (\Exception $e) {
                }
            }

            $paciente->nombre_completo = $detalle
                ? $detalle->nombre . ' ' . $detalle->apellido
                : 'Sin nombre';

            $paciente->setRelation('detalle', $detalle);

            return $paciente;
        });

        $citaOriginal = null;
        $reprogramando = false;

        if ($request->has('reprogramar')) {
            $citaOriginal = Cita::with('paciente')->find($request->reprogramar);
            if ($citaOriginal && $citaOriginal->paciente) {
                $citaOriginal->paciente->load('detalle');
                $reprogramando = true;
            }
        }

        return view('citas.create', compact('pacientes', 'citaOriginal', 'reprogramando'));
    }

    private function contarCitasPorFecha($fecha)
    {
        return Cita::whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->count();
    }

    public function cuposDisponibles(Request $request)
    {
        $fecha = $request->get('fecha');

        if (!$fecha) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha no proporcionada'
            ]);
        }

        $citasEnFecha = $this->contarCitasPorFecha($fecha);
        $cuposDisponibles = 4 - $citasEnFecha;

        return response()->json([
            'success' => true,
            'fecha' => $fecha,
            'citas_agendadas' => $citasEnFecha,
            'cupos_disponibles' => $cuposDisponibles,
            'max_cupos' => 4,
            'disponible' => $cuposDisponibles > 0
        ]);
    }

    public function verificarCitaPaciente(Request $request)
    {
        $fecha = $request->get('fecha');
        $pacienteId = $request->get('paciente_id');
        $citaOriginalId = $request->get('cita_original_id');

        if (!$fecha || !$pacienteId) {
            return response()->json([
                'success' => false,
                'message' => 'Faltan parámetros'
            ]);
        }

        $mismaFechaOriginal = false;
        if ($citaOriginalId) {
            $citaOriginal = Cita::find($citaOriginalId);
            if ($citaOriginal && $citaOriginal->fecha->format('Y-m-d') === $fecha) {
                $mismaFechaOriginal = true;
            }
        }

        $tieneCita = Cita::where('paciente_id', $pacienteId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->exists();

        $citasEnFecha = $this->contarCitasPorFecha($fecha);
        $cuposDisponibles = 4 - $citasEnFecha;

        return response()->json([
            'success' => true,
            'tiene_cita' => $tieneCita,
            'misma_fecha_original' => $mismaFechaOriginal,
            'citas_agendadas' => $citasEnFecha,
            'cupos_disponibles' => $cuposDisponibles,
            'max_cupos' => 4
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha' => 'required|date|after_or_equal:today',
            'objetivo' => 'nullable|string|max:500',
            'planificacion' => 'nullable|string|max:1000',
            'cita_original_id' => 'nullable|exists:citas,id'
        ]);

        if ($request->filled('cita_original_id')) {
            $citaOriginal = Cita::findOrFail($request->cita_original_id);

            if ($citaOriginal->fecha->format('Y-m-d') === $validated['fecha']) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No puedes reprogramar la cita para el mismo día.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'No puedes reprogramar la cita para el mismo día.')
                    ->withInput();
            }
        }

        $citasEnFecha = $this->contarCitasPorFecha($validated['fecha']);

        if ($citasEnFecha >= 4) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay cupos disponibles. Ya hay 4 citas agendadas.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'No hay cupos disponibles. Ya hay 4 citas agendadas.')
                ->withInput();
        }

        $citaExistente = Cita::where('paciente_id', $validated['paciente_id'])
            ->whereDate('fecha', $validated['fecha'])
            ->where('estado', '!=', 'cancelada')
            ->first();

        if ($citaExistente) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya tiene una cita para esta fecha.'
                ], 422);
            }

            return redirect()->back()
                ->with('error', 'Este paciente ya tiene una cita para esta fecha.')
                ->withInput();
        }

        $cita = Cita::create([
            'paciente_id' => $validated['paciente_id'],
            'fecha' => $validated['fecha'],
            'objetivo' => $validated['objetivo'] ?? null,
            'planificacion' => $validated['planificacion'] ?? null,
            'estado' => 'pendiente'
        ]);

        if ($request->filled('cita_original_id')) {
            Log::info('Cita reprogramada', [
                'original_id' => $request->cita_original_id,
                'nueva_id' => $cita->id,
                'paciente_id' => $validated['paciente_id']
            ]);
        }

        $mensaje = $request->has('cita_original_id')
            ? 'Cita reprogramada para el ' . date('d/m/Y', strtotime($validated['fecha']))
            : 'Cita creada correctamente';

        return redirect()->route('citas.index')
            ->with('success', $mensaje);
    }

    public function update(Request $request, Cita $cita)
    {
        if ($request->has('fecha') && $request->fecha != $cita->fecha) {
            $citasEnFecha = Cita::whereDate('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->where('id', '!=', $cita->id)
                ->count();

            if ($citasEnFecha >= 4) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay cupos disponibles.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'No hay cupos disponibles.')
                    ->withInput();
            }

            $citaExistente = Cita::where('paciente_id', $cita->paciente_id)
                ->whereDate('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->where('id', '!=', $cita->id)
                ->first();

            if ($citaExistente) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El paciente ya tiene cita ese día.'
                    ], 422);
                }

                return redirect()->back()
                    ->with('error', 'El paciente ya tiene cita ese día.')
                    ->withInput();
            }
        }

        if ($request->estado === 'cancelada') {
            $request->validate([
                'estado' => 'required|in:cancelada',
                'motivo_cancelacion' => 'required|string|min:5|max:500'
            ]);

            DB::transaction(function () use ($request, $cita) {
                $cita->motivo_cancelacion = $request->motivo_cancelacion;
                $cita->estado = 'cancelada';
                $cita->save();
            });

            $reprogramar = $request->input('reprogramar', false);

            if ($reprogramar || $request->has('reprogramar')) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'reprogramar' => true,
                        'cita_id' => $cita->id,
                        'message' => 'Cita cancelada. Redirigiendo...'
                    ]);
                }

                return redirect()->route('citas.create', ['reprogramar' => $cita->id])
                    ->with('info', 'Cita cancelada. Agenda una nueva fecha.');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cita cancelada correctamente'
                ]);
            }

            return redirect()->route('citas.index')
                ->with('success', 'Cita cancelada correctamente');
        }

        $request->validate([
            'estado' => 'required|in:atendida,no_asistio'
        ]);

        if ($cita->fecha->format('Y-m-d') !== date('Y-m-d')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'La asistencia solo puede registrarse el día de la cita.'
                ], 422);
            }

            return redirect()->route('citas.index')
                ->with('error', 'La asistencia solo puede registrarse el día de la cita.');
        }

        $cita->estado = $request->estado;
        $cita->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cita actualizada correctamente'
            ]);
        }

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada correctamente');
    }
}
