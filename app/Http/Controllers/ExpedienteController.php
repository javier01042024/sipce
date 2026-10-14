<?php

namespace App\Http\Controllers;

use App\Models\Diario;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpedienteController extends Controller
{
    public function exportarResumen(Paciente $paciente)
    {
        $paciente->load([
            'detalle',
            'estado',
            'diagnosticos',
            'citas' => function ($q) {
                $q->orderBy('fecha', 'desc')->limit(10);
            },
            'notas' => function ($q) {
                $q->with('user')->orderBy('created_at', 'desc')->limit(5);
            },
            'planesTratamiento' => function ($q) {
                $q->with('objetivos');
            },
        ]);

        $pdf = Pdf::loadView('expediente.resumen-pdf', compact('paciente'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isPhpEnabled', true);

        $nombre = 'resumen_clinico_' . $paciente->numero_expediente . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($nombre);
    }

    public function exportarFicha(Paciente $paciente)
    {
        $paciente->load([
            'detalle',
            'estado',
            'diagnosticos',
            'citas' => function ($q) {
                $q->orderBy('fecha', 'desc');
            },
            'notas' => function ($q) {
                $q->with('user')->orderBy('created_at', 'desc');
            },
            'sesiones' => function ($q) {
                $q->with('user')->orderBy('fecha', 'desc');
            },
            'planesTratamiento' => function ($q) {
                $q->with('objetivos');
            },
            'acompanantes',
            'user',
        ]);

        $diarios = $paciente->user
            ? Diario::where('user_id', $paciente->user_id)->orderBy('fecha', 'desc')->get()
            : collect([]);

        $pdf = Pdf::loadView('expediente.ficha-pdf', compact('paciente', 'diarios'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isPhpEnabled', true);

        $nombre = 'ficha_' . $paciente->numero_expediente . '_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($nombre);
    }
}
