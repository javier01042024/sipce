<?php

namespace App\Exports;

use App\Models\Paciente;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class ReporteMensualExport
{
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

        // Configurar orientación landscape y tamaño de papel
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // =============================================
        // ENCABEZADOS INSTITUCIONALES
        // =============================================
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'VICEMINISTERIO DE REDES DE SALUD COLECTIVA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('left');

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2', 'DIRECCIÓN GENERAL DE PROGRAMAS DE SALUD');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('left');

        $sheet->mergeCells('A3:L3');
        $sheet->setCellValue('A3', 'PROGRAMA NACIONAL SALUD MENTAL');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('left');

        // Título en rojo
        $sheet->mergeCells('A5:L5');
        $sheet->setCellValue('A5', 'REGISTRO INDIVIDUALIZADO DE PACIENTES DE SALUD MENTAL');
        $titleStyle = $sheet->getStyle('A5');
        $titleStyle->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF0000'));
        $titleStyle->getAlignment()->setHorizontal('center');

        // Año
        $sheet->mergeCells('A6:L6');
        $sheet->setCellValue('A6', 'AÑO ' . $this->anio);
        $sheet->getStyle('A6')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A6')->getAlignment()->setHorizontal('center');

        // Mes
        $meses = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
        $sheet->mergeCells('A7:L7');
        $sheet->setCellValue('A7', 'MES: ' . ($meses[$this->mes] ?? ''));
        $sheet->getStyle('A7')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');

        // =============================================
        // ENCABEZADOS DE LA TABLA (Fila 9)
        // =============================================
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
            $sheet->setCellValue($col . '9', $header);
        }

        // Estilo de encabezados
        $headerStyle = $sheet->getStyle('A9:L9');
        $headerStyle->getFont()->setBold(true)->setSize(9);
        $headerStyle->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
        $headerStyle->getFill()->setFillType('solid')->getStartColor()->setRGB('D9E2F3');
        $headerStyle->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(5);   // Nº
        $sheet->getColumnDimension('B')->setWidth(28);  // Apellido y Nombre
        $sheet->getColumnDimension('C')->setWidth(15);  // Cédula
        $sheet->getColumnDimension('D')->setWidth(6);   // Edad
        $sheet->getColumnDimension('E')->setWidth(18);  // Municipio
        $sheet->getColumnDimension('F')->setWidth(35);  // Dirección
        $sheet->getColumnDimension('G')->setWidth(15);  // Teléfono
        $sheet->getColumnDimension('H')->setWidth(18);  // Consultorio Popular
        $sheet->getColumnDimension('I')->setWidth(10);  // ASIC
        $sheet->getColumnDimension('J')->setWidth(8);   // Sexo
        $sheet->getColumnDimension('K')->setWidth(15);  // Tipo Consulta
        $sheet->getColumnDimension('L')->setWidth(25);  // DX

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
        $row = 10;
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

            // Estilo de celdas de datos
            $dataStyle = $sheet->getStyle('A' . $row . ':L' . $row);
            $dataStyle->getAlignment()->setVertical('center')->setWrapText(true);
            $dataStyle->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $dataStyle->getFont()->setSize(9);

            $row++;
            $num++;
        }

        // =============================================
        // PIE DE PÁGINA - Firmas
        // =============================================
        $pieRow = $row + 2;
        $sheet->mergeCells('A' . $pieRow . ':C' . $pieRow);
        $sheet->setCellValue('A' . $pieRow, '_________________________');
        $sheet->getStyle('A' . $pieRow)->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('D' . $pieRow . ':F' . $pieRow);
        $sheet->setCellValue('D' . $pieRow, '_________________________');
        $sheet->getStyle('D' . $pieRow)->getAlignment()->setHorizontal('center');

        $sheet->mergeCells('H' . $pieRow . ':J' . $pieRow);
        $sheet->setCellValue('H' . $pieRow, '_________________________');
        $sheet->getStyle('H' . $pieRow)->getAlignment()->setHorizontal('center');

        $labelRow = $pieRow + 1;
        $sheet->mergeCells('A' . $labelRow . ':C' . $labelRow);
        $sheet->setCellValue('A' . $labelRow, 'Firma del Profesional');
        $sheet->getStyle('A' . $labelRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A' . $labelRow)->getFont()->setSize(9);

        $sheet->mergeCells('D' . $labelRow . ':F' . $labelRow);
        $sheet->setCellValue('D' . $labelRow, 'Sello de la Institución');
        $sheet->getStyle('D' . $labelRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('D' . $labelRow)->getFont()->setSize(9);

        $sheet->mergeCells('H' . $labelRow . ':J' . $labelRow);
        $sheet->setCellValue('H' . $labelRow, 'Vo.Bo. Coordinación');
        $sheet->getStyle('H' . $labelRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('H' . $labelRow)->getFont()->setSize(9);

        return $spreadsheet;
    }
}
