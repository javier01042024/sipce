<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Carbon\Carbon;


class CitaController extends Controller
{
public function index()
{
    $hoy = Carbon::today()->toDateString();

    $citasHoy = Cita::with('paciente')
        ->whereDate('fecha', $hoy)
        ->orderBy('fecha')
        ->get();

    $citas = Cita::with('paciente')
        ->orderBy('fecha', 'asc')
        ->get();

    return view('citas.index', compact('citasHoy', 'citas', 'hoy'));
}

    public function create()
    {
        $pacientes = Paciente::orderBy('nombre_completo')->get();
        return view('citas.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha' => 'required|date',
            'objetivo' => 'nullable|string',
            'planificacion' => 'nullable|string',
        ]);

        Cita::create([
            'paciente_id' => $request->paciente_id,
            'fecha' => $request->fecha,
            'estado' => 'pendiente',
            'objetivo' => $request->objetivo,
            'planificacion' => $request->planificacion,
        ]);

        return redirect()->route('citas.index')
            ->with('success', 'Cita creada correctamente');
    }

    public function edit(Cita $cita)
    {
        $pacientes = Paciente::orderBy('nombre')->get();

        return view('citas.edit', compact('cita', 'pacientes'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha' => 'required|date',
            'estado' => 'required|in:pendiente,atendida,cancelada,no_asistio',
            'objetivo' => 'nullable|string',
            'planificacion' => 'nullable|string',
        ]);

        $cita->update($request->all());

        return redirect()->route('citas.index')
            ->with('success', 'Cita actualizada correctamente');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')
            ->with('success', 'Cita eliminada');
    }
}
