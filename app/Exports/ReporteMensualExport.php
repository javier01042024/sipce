<?php

namespace App\Exports;

use App\Exports\Concerns\PlantillaInstitucional;
use App\Models\Paciente;

class ReporteMensualExport
{
    use PlantillaInstitucional;

    protected $mes;
    protected $anio;
    protected $municipioFiltro;

    public function __construct($mes, $anio, $municipioFiltro = null)
    {
        $this->mes = $mes;
        $this->anio = $anio;
        $this->municipioFiltro = $municipioFiltro;
    }

    public function generate(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Registro Mensual');

        $this->configurarPagina($sheet);

        // =============================================
        // ENCABEZADO INSTITUCIONAL, TÍTULO, META Y FILTROS
        // =============================================
        $cols = 12;
        $lastCol = chr(64 + $cols);

        $this->encabezadoInstitucional($sheet, $lastCol);

        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
        $nombreMes = $meses[$this->mes] ?? '';

        $this->tituloReporte($sheet, 'REGISTRO INDIVIDUALIZADO DE PACIENTES DE SALUD MENTAL', $lastCol);
        $this->metaReporte($sheet, 'Generado: ' . now()->format('d/m/Y H:i') . '   |   Año: ' . $this->anio . '   |   Mes: ' . $nombreMes, $lastCol);

        $filtros = [];
        if ($this->municipioFiltro) {
            $filtros['Municipio'] = $this->municipioFiltro;
        }
        $this->lineaFiltros($sheet, $filtros, $lastCol);

        // =============================================
        // ENCABEZADOS DE LA TABLA
        // =============================================
        $filaHeader = $this->filaEncabezadoTabla;
        $headers = [
            'A' => 'Nº',
            'B' => 'APELLIDO Y NOMBRE',
            'C' => 'Nº. DE CÉDULA',
            'D' => 'EDAD',
            'E' => 'MUNICIPIO',
            'F' => 'DIRECCIÓN DE HABITACIÓN COMPLETA',
            'G' => 'Nº TELÉFONO',
            'H' => 'CONSULTORIO POPULAR',
            'I' => 'ASIC',
            'J' => 'SEXO (F/M)',
            'K' => 'TIPO DE CONSULTA (P/S)',
            'L' => 'DX. MÉDICO',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . $filaHeader, $header);
        }

        $this->estilizarEncabezadoTabla($sheet, $filaHeader, $lastCol);

        // Ancho de columnas
        $this->anchoColumnas($sheet, [5, 28, 15, 6, 18, 35, 15, 18, 10, 8, 18, 30]);

        // =============================================
        // OBTENER PACIENTES
        // =============================================
        $query = Paciente::with(['detalle', 'diagnosticoPrincipal'])
            ->whereHas('sesiones', function ($q) {
                $q->whereMonth('fecha', $this->mes)
                  ->whereYear('fecha', $this->anio);
            });

        if ($this->municipioFiltro) {
            $query->where('municipio', $this->municipioFiltro);
        }

        $pacientes = $query->get();
        $row = $filaHeader + 1;
        $num = 1;

        foreach ($pacientes as $paciente) {
            $detalle = $paciente->detalle;
            if (!$detalle) continue;

            // Calcular edad
            $edad = null;
            if ($detalle->fecha_nacimiento) {
                $edad = \Carbon\Carbon::parse($detalle->fecha_nacimiento)->age;
            }

            // Sexo
            $sexo = strtoupper(substr($detalle->genero ?? '', 0, 1)) ?: '';

            // Tipo consulta: P=público, S=privado
            $tipoCons = $paciente->tipo_atencion === 'publico' ? 'P' : 'S';

            // Diagnóstico principal
            $dx = '';
            if ($paciente->diagnosticoPrincipal) {
                $dx = ($paciente->diagnosticoPrincipal->codigo_cie ? $paciente->diagnosticoPrincipal->codigo_cie . ' - ' : '')
                    . $paciente->diagnosticoPrincipal->diagnostico;
            } elseif ($paciente->diagnostico_preliminar) {
                $dx = $paciente->diagnostico_preliminar;
            }

            $data = [
                $num,
                trim(($detalle->apellido ?? '') . ' ' . ($detalle->nombre ?? '')),
                $detalle->cedula ?? '',
                $edad,
                $paciente->municipio ?? '',
                $detalle->direccion ?? '',
                $paciente->telefono ?? '',
                '', // Consultorio Popular (se llena manualmente o se agrega después)
                '', // ASIC (se llena manualmente o se agrega después)
                $sexo,
                $tipoCons,
                $dx,
            ];

            foreach ($data as $colIndex => $value) {
                $col = chr(65 + $colIndex); // A=65, B=66, ...
                $sheet->setCellValue($col . $row, $value);
            }

            $this->estilizarFilaDatos($sheet, $row, $lastCol, $num);

            $row++;
            $num++;
        }

        // Auto-filtro y congelar panel
        $sheet->setAutoFilter("A{$filaHeader}:{$lastCol}" . max($filaHeader, $row - 1));
        $sheet->freezePane('A' . ($filaHeader + 1));

        // =============================================
        // RESUMEN
        // =============================================
        $resumenRow = $row + 1;        $sheet->mergeCells("A{$resumenRow}:L{$resumenRow}");
        $resumenTexto = 'Resumen: Total: ' . $pacientes->count()
            . '   |   Niños: ' . $pacientes->where('tipo_paciente', 'niño')->count()
            . '   |   Adolescentes: ' . $pacientes->where('tipo_paciente', 'adolescente')->count()
            . '   |   Adultos: ' . $pacientes->where('tipo_paciente', 'adulto')->count();
        $sheet->setCellValue("A{$resumenRow}", $resumenTexto);
        $sheet->getStyle("A{$resumenRow}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("A{$resumenRow}")->getAlignment()->setHorizontal('left');

        // =============================================
        // PIE DE PÁGINA - Firmas
        // =============================================
        $this->firmasInstitucionales($sheet, $resumenRow + 3, $lastCol, $cols);

        return $spreadsheet;
    }
}