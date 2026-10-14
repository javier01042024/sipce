<?php

namespace App\Exports\Concerns;

use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

trait PlantillaInstitucional
{
    protected $colorPrincipal = '1F3864';
    protected $colorZebra = 'EFF3FA';
    protected $colorTextoFiltros = '1F4E78';
    protected $filaInstitucional = 1;
    protected $filaTitulo = 5;
    protected $filaMeta = 6;
    protected $filaFiltros = 7;
    protected $filaEncabezadoTabla = 10;

    protected function configurarPagina($sheet)
    {
        $sheet->setShowGridlines(false);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
        $sheet->getHeaderFooter()->setOddFooter('&L&"Arial,Bold"&9Página &P de &N&R&"Arial"&9' . now()->format('d/m/Y H:i'));
    }

    protected function encabezadoInstitucional($sheet, $ultimaColumna, bool $centrado = true)
    {
        $lineas = [
            ['VICEMINISTERIO DE REDES DE SALUD COLECTIVA', true, 10],
            ['DIRECCIÓN GENERAL DE PROGRAMAS DE SALUD', true, 10],
            ['PROGRAMA NACIONAL SALUD MENTAL', false, 10],
        ];

        foreach ($lineas as $i => [$texto, $negrita, $tamano]) {
            $fila = $this->filaInstitucional + $i;
            $sheet->mergeCells("A{$fila}:{$ultimaColumna}{$fila}");
            $sheet->setCellValue("A{$fila}", $texto);
            $sheet->getStyle("A{$fila}")->getFont()->setBold($negrita)->setSize($tamano);
            if ($centrado) {
                $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal('center');
            }
        }
    }

    protected function tituloReporte($sheet, string $titulo, $ultimaColumna)
    {
        $sheet->mergeCells("A{$this->filaTitulo}:{$ultimaColumna}{$this->filaTitulo}");
        $sheet->setCellValue("A{$this->filaTitulo}", $titulo);
        $sheet->getStyle("A{$this->filaTitulo}")->getFont()->setBold(true)->setSize(14)->setColor(new Color($this->colorPrincipal));
        $sheet->getStyle("A{$this->filaTitulo}")->getAlignment()->setHorizontal('center');
    }

    protected function metaReporte($sheet, string $texto, $ultimaColumna)
    {
        $sheet->mergeCells("A{$this->filaMeta}:{$ultimaColumna}{$this->filaMeta}");
        $sheet->setCellValue("A{$this->filaMeta}", $texto);
        $sheet->getStyle("A{$this->filaMeta}")->getFont()->setSize(9)->setItalic(true);
        $sheet->getStyle("A{$this->filaMeta}")->getAlignment()->setHorizontal('center');
    }

    protected function lineaFiltros($sheet, array $filtros, $ultimaColumna)
    {
        if (empty($filtros)) {
            return $this->filaFiltros;
        }

        $fila = $this->filaFiltros;
        $sheet->mergeCells("A{$fila}:{$ultimaColumna}{$fila}");
        $texto = 'Filtros aplicados: ' . implode('  |  ', array_map(
            fn($k, $v) => "{$k}: {$v}",
            array_keys($filtros),
            array_values($filtros)
        ));
        $sheet->setCellValue("A{$fila}", $texto);
        $sheet->getStyle("A{$fila}")->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB($this->colorTextoFiltros);
        $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal('left');

        return $fila;
    }

    protected function estilizarEncabezadoTabla($sheet, $fila, $ultimaColumna)
    {
        $estilo = $sheet->getStyle("A{$fila}:{$ultimaColumna}{$fila}");
        $estilo->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('FFFFFF');
        $estilo->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
        $estilo->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($this->colorPrincipal);
        $estilo->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getRowDimension($fila)->setRowHeight(28);
    }

    protected function estilizarFilaDatos($sheet, $fila, $ultimaColumna, int $numero)
    {
        $estilo = $sheet->getStyle("A{$fila}:{$ultimaColumna}{$fila}");
        $estilo->getAlignment()->setVertical('center')->setWrapText(true);
        $estilo->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $estilo->getFont()->setSize(9);

        if ($numero % 2 === 0) {
            $estilo->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($this->colorZebra);
        }
    }

    protected function anchoColumnas($sheet, array $anchos)
    {
        foreach ($anchos as $i => $ancho) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($ancho);
        }
    }

    /**
     * Letra de columna de Excel a partir de un índice 1-based (soporta > 26).
     */
    protected function col(int $indice): string
    {
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($indice);
    }

    protected function firmasInstitucionales($sheet, int $fila, $ultimaColumna, int $cantidadColumnas)
    {
        $tercio = intdiv($cantidadColumnas, 3);
        $colPrimero = $this->col($tercio);
        $colSegundoIni = $this->col($tercio + 1);
        $colSegundoFin = $this->col($tercio * 2);
        $colTerceroIni = $this->col($tercio * 2 + 1);
        $linea = '_________________________';

        $sheet->mergeCells("A{$fila}:{$colPrimero}{$fila}");
        $sheet->setCellValue("A{$fila}", $linea);
        $sheet->getStyle("A{$fila}")->getAlignment()->setHorizontal('left');

        $sheet->mergeCells("{$colSegundoIni}{$fila}:{$colSegundoFin}{$fila}");
        $sheet->setCellValue("{$colSegundoIni}{$fila}", $linea);
        $sheet->getStyle("{$colSegundoIni}{$fila}")->getAlignment()->setHorizontal('center');

        $sheet->mergeCells("{$colTerceroIni}{$fila}:{$ultimaColumna}{$fila}");
        $sheet->setCellValue("{$colTerceroIni}{$fila}", $linea);
        $sheet->getStyle("{$colTerceroIni}{$fila}")->getAlignment()->setHorizontal('right');

        $filaEtiquetas = $fila + 1;

        $sheet->mergeCells("A{$filaEtiquetas}:{$colPrimero}{$filaEtiquetas}");
        $sheet->setCellValue("A{$filaEtiquetas}", 'Firma del Profesional');
        $sheet->getStyle("A{$filaEtiquetas}")->getFont()->setSize(9);

        $sheet->mergeCells("{$colSegundoIni}{$filaEtiquetas}:{$colSegundoFin}{$filaEtiquetas}");
        $sheet->setCellValue("{$colSegundoIni}{$filaEtiquetas}", 'Sello de la Institución');
        $sheet->getStyle("{$colSegundoIni}{$filaEtiquetas}")->getFont()->setSize(9);
        $sheet->getStyle("{$colSegundoIni}{$filaEtiquetas}")->getAlignment()->setHorizontal('center');

        $sheet->mergeCells("{$colTerceroIni}{$filaEtiquetas}:{$ultimaColumna}{$filaEtiquetas}");
        $sheet->setCellValue("{$colTerceroIni}{$filaEtiquetas}", 'Vo.Bo. Coordinación');
        $sheet->getStyle("{$colTerceroIni}{$filaEtiquetas}")->getFont()->setSize(9);
        $sheet->getStyle("{$colTerceroIni}{$filaEtiquetas}")->getAlignment()->setHorizontal('right');
    }
}