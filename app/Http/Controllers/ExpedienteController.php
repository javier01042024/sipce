<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpedienteController extends Controller
{
    public function exportarResumen(Paciente $paciente)
    {
        $paciente->load(['detalle', 'estado', 'citas' => function ($q) {
            $q->orderBy('fecha', 'desc')->limit(10);
        }, 'notas' => function ($q) {
            $q->with('user')->orderBy('created_at', 'desc')->limit(5);
        }, 'planesTratamiento' => function ($q) {
            $q->where('estado', 'activo')->with('objetivos');
        }]);

        $pdf = Pdf::loadView('expediente.resumen-pdf', compact('paciente'))
            ->setPaper('letter')
            ->setOption('isRemoteEnabled', true);

        $nombre = 'resumen_clinico_' . $paciente->numero_expediente . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($nombre);
    }
}
