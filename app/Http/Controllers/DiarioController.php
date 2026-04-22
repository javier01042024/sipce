<?php

namespace App\Http\Controllers;

use App\Models\Diario;
use App\Models\Paciente;
use Illuminate\Http\Request;

class DiarioController extends Controller
{
    public function index()
    {
        $diarios = Diario::with('paciente')
            ->orderBy('fecha', 'desc')
            ->get();

        return view('diarios.index', compact('diarios'));
    }

    public function create()
    {
        $pacientes = Paciente::orderBy('nombre_completo')->get();

        return view('diarios.create', compact('pacientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'fecha' => 'required|date',
            'contenido' => 'required|string',
        ]);

        Diario::create($request->all());

        return redirect()->route('diarios.index')
            ->with('success', 'Registro guardado');
    }
}