<?php

namespace App\Exports;

use App\Exports\Concerns\PlantillaInstitucional;

class CitasExport
{
    use PlantillaInstitucional;

    protected $citas;
    protected $filtros;

    public function __construct($citas, $filtros = [])
    {
        $this->citas = $citas;
        $this->filtros = $filtros;
    }

    private function nombrePaciente($cita)
    {
        $detalle = $cita->paciente?->detalle;
        if (!$detalle) return 'N/A';
        return trim(($detalle->nombre ?? '') . ' ' . ($detalle->apellido ?? ''));
    }

    private function estadoTexto($estado)
    {
        return match ($estado) {
            'pendiente' => 'Pendiente',
            'atendida' => 'Atendida',
            'cancelada' => 'Cancelada',
            'no_asistio' => 'No Asistió',
            default => $estado,
        };
    }

    public function generate(\PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Citas');

        $this->configurarPagina($sheet);

        // =============================================
        // ENCABEZADO INSTITUCIONAL, TÍTULO, META Y FILTROS
        // =============================================
        $cols = 10;
        $lastCol = chr(64 + $cols);

        $this->encabezadoInstitucional($sheet, $lastCol);
        $this->tituloReporte($sheet, 'REPORTE DE CITAS', $lastCol);
        $this->metaReporte($sheet, 'Generado: ' . now()->format('d/m/Y H:i') . '   |   Total de registros: ' . $this->citas->count(), $lastCol);
        $this->lineaFiltros($sheet, $this->filtros, $lastCol);

        // =============================================
        // ENCABEZADOS DE LA TABLA
        // =============================================
        $filaHeader = $this->filaEncabezadoTabla;
        $headers = [
            'Nº', 'Fecha', 'Paciente', 'Expediente', 'Tipo de Atención',
            'Estado', 'Objetivo', 'Planificación', 'Motivo de Cancelación',
        ];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i) . $filaHeader, $header);
        }

        $this->estilizarEncabezadoTabla($sheet, $filaHeader, $lastCol);

        // =============================================
        // DATOS
        // =============================================
        $row = $filaHeader + 1;
        $num = 1;

        foreach ($this->citas as $c) {
            $data = [
                $num,
                $c->fecha ? $c->fecha->format('d/m/Y') : '',
                $this->nombrePaciente($c),
                $c->paciente?->numero_expediente ?? '',
                $c->paciente?->tipo_atencion === 'privado' ? 'Privado' : 'Público',
                $this->estadoTexto($c->estado),
                mb_substr($c->objetivo ?? '', 0, 120),
                mb_substr($c->planificacion ?? '', 0, 120),
                mb_substr($c->motivo_cancelacion ?? '', 0, 120),
            ];

            foreach ($data as $i => $value) {
                $sheet->setCellValue(chr(65 + $i) . $row, $value);
            }

            $this->estilizarFilaDatos($sheet, $row, $lastCol, $num);

            $row++;
            $num++;
        }

        // Anchos de columna
        $this->anchoColumnas($sheet, [5, 12, 26, 13, 13, 13, 35, 35, 30]);

        // Auto-filtro y congelar panel
        $sheet->setAutoFilter("A{$filaHeader}:{$lastCol}" . ($row - 1));
        $sheet->freezePane('A' . ($filaHeader + 1));

        // =============================================
        // TOTALES
        // =============================================
        $totalRow = $row + 1;
        $sheet->setCellValue("C{$totalRow}", 'TOTAL DE CITAS:');
        $sheet->getStyle("C{$totalRow}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("C{$totalRow}")->getAlignment()->setHorizontal('right');
        $sheet->setCellValue("D{$totalRow}", $this->citas->count());
        $sheet->getStyle("D{$totalRow}")->getFont()->setBold(true)->setSize(10);

        // =============================================
        // FIRMAS
        // =============================================
        $this->firmasInstitucionales($sheet, $totalRow + 3, $lastCol, $cols);

        return $spreadsheet;
    }
}