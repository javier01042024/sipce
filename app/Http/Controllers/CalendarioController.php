<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    public function index()
    {
        return view('calendario.index');
    }

    public function eventos(Request $request)
    {
        $user = Auth::user();
        $start = $request->input('start');
        $end = $request->input('end');

        $citas = Cita::with('paciente.detalle')
            ->whereBetween('fecha', [$start, $end])
            ->get()
            ->map(function ($cita) {
                $paciente = $cita->paciente;
                $nombre = $paciente && $paciente->detalle
                    ? $paciente->detalle->nombre . ' ' . $paciente->detalle->apellido
                    : 'Paciente #' . ($paciente->id ?? 'N/A');

                $colores = [
                    'pendiente' => '#f59e0b',
                    'atendida' => '#10b981',
                    'cancelada' => '#ef4444',
                    'no_asistio' => '#6b7280',
                ];

                return [
                    'id' => $cita->id,
                    'title' => $nombre,
                    'start' => $cita->fecha->format('Y-m-d'),
                    'end' => $cita->fecha->format('Y-m-d'),
                    'color' => $colores[$cita->estado] ?? '#6b7280',
                    'extendedProps' => [
                        'paciente_id' => $paciente->id ?? null,
                        'paciente_nombre' => $nombre,
                        'estado' => $cita->estado,
                        'objetivo' => $cita->objetivo,
                        'numero_expediente' => $paciente->numero_expediente ?? '',
                        'tipo_atencion' => $paciente->tipo_atencion ?? '',
                    ],
                ];
            });

        return response()->json($citas);
    }
}
