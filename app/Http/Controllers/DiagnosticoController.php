<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Diagnostico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DiagnosticoController extends Controller
{
    public function store(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'codigo_cie' => 'nullable|string|max:20',
            'diagnostico' => 'required|string|max:500',
            'observaciones' => 'nullable|string',
            'es_principal' => 'nullable|boolean',
            'fecha' => 'nullable|date',
        ]);

        $data['paciente_id'] = $paciente->id;
        $data['user_id'] = Auth::id();
        $data['es_principal'] = $request->boolean('es_principal', false);
        $data['fecha'] = $data['fecha'] ?? now()->format('Y-m-d');

        // Si es principal, desmarcar los anteriores
        if ($data['es_principal']) {
            Diagnostico::where('paciente_id', $paciente->id)
                ->where('es_principal', true)
                ->update(['es_principal' => false]);
        }

        Diagnostico::create($data);

        return back()->with('success', 'Diagnóstico registrado exitosamente.');
    }

    public function update(Request $request, Diagnostico $diagnostico)
    {
        $data = $request->validate([
            'codigo_cie' => 'nullable|string|max:20',
            'diagnostico' => 'required|string|max:500',
            'observaciones' => 'nullable|string',
            'es_principal' => 'nullable|boolean',
            'fecha' => 'nullable|date',
        ]);

        $data['es_principal'] = $request->boolean('es_principal', false);

        if ($data['es_principal']) {
            Diagnostico::where('paciente_id', $diagnostico->paciente_id)
                ->where('id', '!=', $diagnostico->id)
                ->where('es_principal', true)
                ->update(['es_principal' => false]);
        }

        $diagnostico->update($data);

        return back()->with('success', 'Diagnóstico actualizado exitosamente.');
    }

    public function destroy(Diagnostico $diagnostico)
    {
        $diagnostico->delete();
        return back()->with('success', 'Diagnóstico eliminado exitosamente.');
    }
}
