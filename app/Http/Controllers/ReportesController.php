<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Cita;
use App\Models\Diario;
use App\Exports\ReporteMensualExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportesController extends Controller
{
    private $municipios = [
        'Arístides Bastidas',
        'Bolívar',
        'Bruzual',
        'Cocorote',
        'Independencia',
        'José Antonio Páez',
        'La Trinidad',
        'Manuel Monge',
        'Nirgua',
        'Peña',
        'San Felipe',
        'Sucre',
        'Urachiche',
        'Veroes',
    ];

    /**
     * Expresión SQL portable para extraer el mes (MySQL vs PostgreSQL).
     */
    private function monthExpr(string $column): string
    {
        return DB::connection()->getDriverName() === 'pgsql'
            ? "EXTRACT(MONTH FROM {$column})"
            : "MONTH({$column})";
    }

    public function index()
    {
        return view('reportes.index');
    }

    public function pacientes(Request $request)
    {
        $query = Paciente::with(['detalle', 'estado']);

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(20);

        $estadisticas = [
            'total' => Paciente::count(),
            'nuevos_mes' => Paciente::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'por_prioridad' => Paciente::select('prioridad', DB::raw('count(*) as total'))
                ->groupBy('prioridad')
                ->pluck('total', 'prioridad'),
            'por_tipo' => Paciente::select('tipo_paciente', DB::raw('count(*) as total'))
                ->groupBy('tipo_paciente')
                ->pluck('total', 'tipo_paciente'),
        ];

        return view('reportes.pacientes', compact('pacientes', 'estadisticas'));
    }

    public function citas(Request $request)
    {
        $query = Cita::with('paciente.detalle');

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $citas = $query->orderBy('fecha', 'desc')->paginate(20);

        $estadisticas = [
            'total' => Cita::count(),
            'pendientes' => Cita::where('estado', 'pendiente')->count(),
            'atendidas' => Cita::where('estado', 'atendida')->count(),
            'canceladas' => Cita::where('estado', 'cancelada')->count(),
            'no_asistio' => Cita::where('estado', 'no_asistio')->count(),
            'tasa_asistencia' => Cita::count() > 0
                ? round((Cita::where('estado', 'atendida')->count() / Cita::count()) * 100, 1)
                : 0,
            'por_mes' => Cita::select(DB::raw($this->monthExpr('fecha') . ' as mes'), DB::raw('count(*) as total'))
                ->whereYear('fecha', now()->year)
                ->groupBy(DB::raw($this->monthExpr('fecha')))
                ->pluck('total', 'mes'),
        ];

        return view('reportes.citas', compact('citas', 'estadisticas'));
    }

    public function evolucion(Request $request)
    {
        $estadisticas = [
            'nuevos_por_mes' => Paciente::select(DB::raw($this->monthExpr('created_at') . ' as mes'), DB::raw('count(*) as total'))
                ->whereYear('created_at', now()->year)
                ->groupBy(DB::raw($this->monthExpr('created_at')))
                ->pluck('total', 'mes'),
            'por_estado' => Paciente::join('estados', 'pacientes.estado_id', '=', 'estados.id')
                ->select('estados.tipo', DB::raw('count(*) as total'))
                ->groupBy('estados.tipo')
                ->pluck('total', 'tipo'),
            'diarios_por_mes' => Diario::select(DB::raw($this->monthExpr('fecha') . ' as mes'), DB::raw('count(*) as total'))
                ->whereYear('fecha', now()->year)
                ->groupBy(DB::raw($this->monthExpr('fecha')))
                ->pluck('total', 'mes'),
        ];

        return view('reportes.evolucion', compact('estadisticas'));
    }

    public function mensual(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $municipio = $request->input('municipio');

        $query = Paciente::with(['detalle', 'diagnosticoPrincipal'])
            ->whereHas('sesiones', function ($q) use ($mes, $anio) {
                $q->whereMonth('fecha', $mes)
                  ->whereYear('fecha', $anio);
            });

        if ($municipio) {
            $query->where('municipio', $municipio);
        }

        $pacientes = $query->get();

        return view('reportes.mensual', compact('pacientes', 'mes', 'anio', 'municipio'))
            ->with('municipios', $this->municipios);
    }

    public function exportarMensual(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $municipio = $request->input('municipio');

        $export = new ReporteMensualExport($mes, $anio, $municipio);
        $spreadsheet = new Spreadsheet();
        $export->generate($spreadsheet);

        $writer = new Xlsx($spreadsheet);
        $filename = "reporte_mensual_{$anio}_{$mes}.xlsx";

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportar(Request $request)
    {
        $tipo = $request->input('tipo', 'pacientes');

        $filename = "reporte_{$tipo}_" . now()->format('Y-m-d_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tipo) {
            $file = fopen('php://output', 'w');

            if ($tipo === 'citas') {
                fputcsv($file, ['ID', 'Paciente', 'Fecha', 'Estado', 'Objetivo']);
                Cita::with('paciente.detalle')->orderBy('fecha', 'desc')->chunk(100, function ($citas) use ($file) {
                    foreach ($citas as $cita) {
                        $nombre = $cita->paciente && $cita->paciente->detalle
                            ? $cita->paciente->detalle->nombre . ' ' . $cita->paciente->detalle->apellido
                            : 'N/A';
                        fputcsv($file, [
                            $cita->id,
                            $nombre,
                            $cita->fecha->format('d/m/Y'),
                            $cita->estado,
                            $cita->objetivo,
                        ]);
                    }
                });
            } else {
                fputcsv($file, ['ID', 'Expediente', 'Nombre', 'Tipo', 'Prioridad', 'Estado', 'Fecha Registro']);
                Paciente::with(['detalle', 'estado'])->orderBy('created_at', 'desc')->chunk(100, function ($pacientes) use ($file) {
                    foreach ($pacientes as $paciente) {
                        fputcsv($file, [
                            $paciente->id,
                            $paciente->numero_expediente,
                            $paciente->nombre_completo,
                            $paciente->tipo_paciente,
                            $paciente->prioridad,
                            $paciente->estado ? $paciente->estado->tipo : 'N/A',
                            $paciente->created_at->format('d/m/Y'),
                        ]);
                    }
                });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
