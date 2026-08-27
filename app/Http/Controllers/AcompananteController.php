<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Acompanante;
use Illuminate\Http\Request;

class AcompananteController extends Controller
{
    public function store(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'parentesco' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:15',
            'cedula' => 'nullable|string|max:20',
            'es_principal' => 'nullable|boolean',
        ]);

        $data['paciente_id'] = $paciente->id;
        $data['es_principal'] = $request->boolean('es_principal', false);

        if ($data['es_principal']) {
            Acompanante::where('paciente_id', $paciente->id)
                ->where('es_principal', true)
                ->update(['es_principal' => false]);
        }

        $acompanante = Acompanante::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Acompañante registrado exitosamente.',
            'acompanante' => $acompanante,
        ]);
    }

    public function update(Request $request, Acompanante $acompanante)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'parentesco' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:15',
            'cedula' => 'nullable|string|max:20',
            'es_principal' => 'nullable|boolean',
        ]);

        $data['es_principal'] = $request->boolean('es_principal', false);

        if ($data['es_principal']) {
            Acompanante::where('paciente_id', $acompanante->paciente_id)
                ->where('id', '!=', $acompanante->id)
                ->update(['es_principal' => false]);
        }

        $acompanante->update($data);

        return back()->with('success', 'Acompañante actualizado exitosamente.');
    }

    public function destroy(Acompanante $acompanante)
    {
        $acompanante->delete();
        return back()->with('success', 'Acompañante eliminado exitosamente.');
    }

    public function listar(Paciente $paciente)
    {
        $acompanantes = $paciente->acompanantes;
        return response()->json(['success' => true, 'acompanantes' => $acompanantes]);
    }
}
