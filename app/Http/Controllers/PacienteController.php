<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pacientes = Paciente::all();
        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pacientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero_expediente' => 'required|unique:pacientes,numero_expediente,' . ($paciente->id ?? 'NULL'),
            'cedula_paciente' => 'required|numeric',
            'nombre_completo' => ['required', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', 'max:100'],
            'fecha_nacimiento' => 'required|date',
            'telefono' => 'required',
            'email' => 'required|email',
            'direccion' => 'required',
            'motivo_consulta' => 'required',
            'diagnostico_preliminar' => 'nullable',
            'prioridad' => 'required|in:Alta,Media,Baja',
        ]);

        Paciente::create($request->all());

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Paciente $paciente)
    {
        return view('pacientes.show', compact('paciente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'cedula_paciente' => 'required|numeric',
            'nombre_completo' => 'required|max:100',
            'fecha_nacimiento' => 'required|date',
            'motivo_consulta' => 'required',
            'diagnostico_preliminar' => 'required',
            'prioridad' => 'required|in:Alta,Media,Baja'
        ]);

        $paciente->update($request->all());

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paciente $paciente)
    {
        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente eliminado correctamente.');
    }
}
