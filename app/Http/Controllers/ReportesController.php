<?php

namespace App\Http\Controllers;

use App\Exports\CitasExport;
use App\Exports\PacientesExport;
use App\Exports\ReporteMensualExport;
use App\Models\Cita;
use App\Models\Diario;
use App\Models\Estado;
use App\Models\Paciente;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    private $estadosCita = ['pendiente', 'atendida', 'cancelada', 'no_asistio'];

    private $prioridades = ['urgencia', 'alta', 'media', 'baja'];

    private $tiposPaciente = ['niño', 'adolescente', 'adulto'];

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

    /*
    |--------------------------------------------------------------------------
    | FILTROS
    |--------------------------------------------------------------------------
    */

    private function fechaDesde(Request $request)
    {
        return $request->input('fecha_inicio', $request->input('desde'));
    }

    private function fechaHasta(Request $request)
    {
        return $request->input('fecha_fin', $request->input('hasta'));
    }

    private function aplicarFiltrosPacientes(Request $request, $query)
    {
        $desde = $this->fechaDesde($request);
        $hasta = $this->fechaHasta($request);

        if ($desde) {
            $query->whereDate('created_at', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('created_at', '<=', $hasta);
        }
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->filled('tipo_paciente')) {
            $query->where('tipo_paciente', $request->tipo_paciente);
        }
        if ($request->filled('tipo_atencion')) {
            $query->where('tipo_atencion', $request->tipo_atencion);
        }
        if ($request->filled('estado')) {
            $query->where('estado_id', $request->estado);
        }
        if ($request->filled('municipio')) {
            $query->where('municipio', $request->municipio);
        }
    }

    private function aplicarFiltrosCitas(Request $request, $query)
    {
        $desde = $this->fechaDesde($request);
        $hasta = $this->fechaHasta($request);

        if ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        }
        if ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('tipo_atencion')) {
            $query->whereHas('paciente', function ($q) use ($request) {
                $q->where('tipo_atencion', $request->tipo_atencion);
            });
        }
        if ($request->filled('tipo_paciente')) {
            $query->whereHas('paciente', function ($q) use ($request) {
                $q->where('tipo_paciente', $request->tipo_paciente);
            });
        }
        if ($request->filled('municipio')) {
            $query->whereHas('paciente', function ($q) use ($request) {
                $q->where('municipio', $request->municipio);
            });
        }
    }

    private function filtrosPacientes(Request $request): array
    {
        $f = [];
        $desde = $this->fechaDesde($request);
        $hasta = $this->fechaHasta($request);
        if ($desde) $f['Período desde'] = $desde;
        if ($hasta) $f['Período hasta'] = $hasta;
        if ($request->filled('prioridad')) $f['Prioridad'] = ucfirst($request->prioridad);
        if ($request->filled('tipo_paciente')) {
            $f['Tipo de paciente'] = $request->tipo_paciente === 'niño' ? 'Niño(a)' : ucfirst($request->tipo_paciente);
        }
        if ($request->filled('tipo_atencion')) {
            $f['Atención'] = $request->tipo_atencion === 'publico' ? 'Público' : 'Privado';
        }
        if ($request->filled('estado')) {
            $estado = Estado::find($request->estado);
            if ($estado) $f['Estado'] = $estado->tipo;
        }
        if ($request->filled('municipio')) $f['Municipio'] = $request->municipio;
        return $f;
    }

    private function filtrosCitas(Request $request): array
    {
        $f = [];
        $desde = $this->fechaDesde($request);
        $hasta = $this->fechaHasta($request);
        if ($desde) $f['Período desde'] = $desde;
        if ($hasta) $f['Período hasta'] = $hasta;
        if ($request->filled('estado')) $f['Estado'] = str_replace('_', ' ', ucfirst($request->estado));
        if ($request->filled('tipo_atencion')) {
            $f['Atención'] = $request->tipo_atencion === 'publico' ? 'Público' : 'Privado';
        }
        if ($request->filled('tipo_paciente')) {
            $f['Tipo de paciente'] = $request->tipo_paciente === 'niño' ? 'Niño(a)' : ucfirst($request->tipo_paciente);
        }
        if ($request->filled('municipio')) $f['Municipio'] = $request->municipio;
        return $f;
    }

    /*
    |--------------------------------------------------------------------------
    | VISTAS
    |--------------------------------------------------------------------------
    */

    public function pacientes(Request $request)
    {
        $base = Paciente::query();
        $this->aplicarFiltrosPacientes($request, $base);

        $pacientes = (clone $base)->with(['detalle', 'estado'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $estadisticas = [
            'total' => (clone $base)->count(),
            'nuevos_mes' => (clone $base)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'por_prioridad' => (clone $base)->selectRaw('prioridad, count(*) as total')
                ->groupBy('prioridad')
                ->pluck('total', 'prioridad'),
            'por_atencion' => (clone $base)->selectRaw('tipo_atencion, count(*) as total')
                ->groupBy('tipo_atencion')
                ->pluck('total', 'tipo_atencion'),
            'por_tipo_paciente' => (clone $base)->selectRaw('tipo_paciente, count(*) as total')
                ->groupBy('tipo_paciente')
                ->pluck('total', 'tipo_paciente'),
        ];

        $estados = Estado::orderBy('tipo')->get();

        return view('reportes.pacientes', compact('pacientes', 'estadisticas', 'estados'));
    }

    public function citas(Request $request)
    {
        $base = Cita::query();
        $this->aplicarFiltrosCitas($request, $base);

        $citas = (clone $base)->with('paciente.detalle')
            ->orderBy('fecha', 'desc')
            ->paginate(20)
            ->withQueryString();

        $estadisticas = [
            'total' => (clone $base)->count(),
            'pendientes' => (clone $base)->where('estado', 'pendiente')->count(),
            'atendidas' => (clone $base)->where('estado', 'atendida')->count(),
            'canceladas' => (clone $base)->where('estado', 'cancelada')->count(),
            'no_asistio' => (clone $base)->where('estado', 'no_asistio')->count(),
            'tasa_asistencia' => (clone $base)->count() > 0
                ? round(((clone $base)->where('estado', 'atendida')->count() / (clone $base)->count()) * 100, 1)
                : 0,
            'por_mes' => (clone $base)->selectRaw(DB::raw($this->monthExpr('fecha') . ' as mes, count(*) as total'))
                ->whereYear('fecha', now()->year)
                ->groupBy(DB::raw($this->monthExpr('fecha')))
                ->orderBy(DB::raw($this->monthExpr('fecha')))
                ->get(),
        ];

        return view('reportes.citas', compact('citas', 'estadisticas'));
    }

    public function evolucion(Request $request)
    {
        $anio = $request->input('anio', now()->year);

        $estadisticas = [
            'nuevos_por_mes' => Paciente::selectRaw(DB::raw($this->monthExpr('created_at') . ' as mes, count(*) as total'))
                ->whereYear('created_at', $anio)
                ->groupBy(DB::raw($this->monthExpr('created_at')))
                ->orderBy(DB::raw($this->monthExpr('created_at')))
                ->get(),
            'por_estado' => Paciente::join('estados', 'pacientes.estado_id', '=', 'estados.id')
                ->selectRaw('estados.tipo as estado, count(*) as total')
                ->groupBy('estados.tipo')
                ->orderBy('estados.tipo')
                ->get(),
            'diarios_por_mes' => Diario::selectRaw(DB::raw($this->monthExpr('fecha') . ' as mes, count(*) as total'))
                ->whereYear('fecha', $anio)
                ->groupBy(DB::raw($this->monthExpr('fecha')))
                ->orderBy(DB::raw($this->monthExpr('fecha')))
                ->get(),
            'anio' => $anio,
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

    /*
    |--------------------------------------------------------------------------
    | EXPORTAR: VER, EXCEL, PDF
    |--------------------------------------------------------------------------
    */

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

    public function exportarMensualPdf(Request $request)
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

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $pdf = Pdf::loadView('reportes.pdf.mensual', compact('pacientes', 'mes', 'anio', 'municipio', 'meses'))
            ->setPaper('a4', 'landscape')
            ->setOption('isPhpEnabled', true);

        return $pdf->download("reporte_mensual_{$anio}_{$mes}.pdf");
    }

    public function exportarPacientes(Request $request)
    {
        $formato = $request->input('formato', 'xlsx');

        $base = Paciente::query();
        $this->aplicarFiltrosPacientes($request, $base);
        $pacientes = $base->with(['detalle', 'estado', 'diagnosticoPrincipal'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filtros = $this->filtrosPacientes($request);

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.pacientes', compact('pacientes', 'filtros'))
                ->setPaper('a4', 'landscape')
                ->setOption('isPhpEnabled', true);
            return $pdf->download('reporte_pacientes_' . now()->format('Y-m-d_His') . '.pdf');
        }

        $export = new PacientesExport($pacientes, $filtros);
        $spreadsheet = new Spreadsheet();
        $export->generate($spreadsheet);

        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_pacientes_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportarCitas(Request $request)
    {
        $formato = $request->input('formato', 'xlsx');

        $base = Cita::query();
        $this->aplicarFiltrosCitas($request, $base);
        $citas = $base->with('paciente.detalle')
            ->orderBy('fecha', 'desc')
            ->get();

        $filtros = $this->filtrosCitas($request);

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView('reportes.pdf.citas', compact('citas', 'filtros'))
                ->setPaper('a4', 'landscape')
                ->setOption('isPhpEnabled', true);
            return $pdf->download('reporte_citas_' . now()->format('Y-m-d_His') . '.pdf');
        }

        $export = new CitasExport($citas, $filtros);
        $spreadsheet = new Spreadsheet();
        $export->generate($spreadsheet);

        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_citas_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Exportación CSV genérica (compatibilidad).
     */
    public function exportar(Request $request)
    {
        $tipo = $request->input('tipo', 'pacientes');

        $filename = "reporte_{$tipo}_" . now()->format('Y-m-d_His') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tipo, $request) {
            $file = fopen('php://output', 'w');

            if ($tipo === 'citas') {
                fputcsv($file, ['ID', 'Paciente', 'Fecha', 'Estado', 'Objetivo']);
                $base = Cita::query();
                $this->aplicarFiltrosCitas($request, $base);
                $base->with('paciente.detalle')->orderBy('fecha', 'desc')->chunk(100, function ($citas) use ($file) {
                    foreach ($citas as $cita) {
                        $nombre = $cita->paciente && $cita->paciente->detalle
                            ? trim($cita->paciente->detalle->nombre . ' ' . $cita->paciente->detalle->apellido)
                            : 'N/A';
                        fputcsv($file, [
                            $cita->id,
                            $nombre,
                            $cita->fecha ? $cita->fecha->format('d/m/Y') : '',
                            $cita->estado,
                            $cita->objetivo,
                        ]);
                    }
                });
            } else {
                fputcsv($file, ['ID', 'Expediente', 'Nombre', 'Tipo', 'Prioridad', 'Estado', 'Fecha Registro']);
                $base = Paciente::query();
                $this->aplicarFiltrosPacientes($request, $base);
                $base->with(['detalle', 'estado'])->orderBy('created_at', 'desc')->chunk(100, function ($pacientes) use ($file) {
                    foreach ($pacientes as $paciente) {
                        fputcsv($file, [
                            $paciente->id,
                            $paciente->numero_expediente,
                            $paciente->nombre_completo,
                            $paciente->tipo_paciente,
                            $paciente->prioridad,
                            $paciente->estado ? $paciente->estado->tipo : 'N/A',
                            $paciente->created_at ? $paciente->created_at->format('d/m/Y') : '',
                        ]);
                    }
                });
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}