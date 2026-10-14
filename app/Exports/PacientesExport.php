<?php

namespace App\Exports;

use App\Exports\Concerns\PlantillaInstitucional;
use App\Support\CamposClinicos;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PacientesExport
{
    use PlantillaInstitucional;

    protected $pacientes;
    protected $filtros;

    /**
     * Campos del detalle que ya se muestran en la sección de resumen.
     */
    private const CAMPOS_RESUMEN = [
        'nombre', 'apellido', 'cedula', 'fecha_nacimiento', 'genero',
        'sexo', 'telefono', 'telefono_personal', 'telefono_contacto',
        'telefono_representante',
    ];

    /**
     * Hojas por tipo de paciente: tipo (para filtrar) => nombre de hoja.
     */
    private const TIPOS = [
        'niño' => 'Niños',
        'adolescente' => 'Adolescentes',
        'adulto' => 'Adultos',
    ];

    public function __construct($pacientes, $filtros = [])
    {
        $this->pacientes = $pacientes;
        $this->filtros = $filtros;
    }

    private function edad($detalle)
    {
        if (!$detalle || !$detalle->fecha_nacimiento) return '';
        return \Carbon\Carbon::parse($detalle->fecha_nacimiento)->age;
    }

    private function nombre($detalle)
    {
        if (!$detalle) return 'N/A';
        return trim(($detalle->nombre ?? '') . ' ' . ($detalle->apellido ?? ''));
    }

    private function genero($detalle)
    {
        if (!$detalle) return '';
        return $detalle->genero ?? ($detalle->sexo ?? '');
    }

    private function telefono($detalle)
    {
        if (!$detalle) return '';
        return $detalle->telefono
            ?? $detalle->telefono_personal
            ?? $detalle->telefono_representante
            ?? $detalle->telefono_contacto
            ?? '';
    }

    private function cedula($detalle)
    {
        if (!$detalle) return '';
        return $detalle->cedula ?? $detalle->cedula_representante ?? '';
    }

    public function generate(Spreadsheet $spreadsheet)
    {
        // Hoja 1: resumen general (todas las columnas comunes)
        $resumenCols = $this->columnasResumen();
        $resumen = $spreadsheet->getActiveSheet();
        $resumen->setTitle('Resumen');
        $this->construirHoja($resumen, 'REPORTE DE PACIENTES', $resumenCols, $this->pacientes);

        // Una hoja por tipo con TODA la información clínica del expediente
        foreach (self::TIPOS as $tipo => $tituloHoja) {
            $delTipo = $this->pacientes
                ->filter(fn($p) => CamposClinicos::coincide($p->tipo_paciente ?? '', $tipo))
                ->values();

            if ($delTipo->isEmpty()) {
                continue;
            }

            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($tituloHoja);

            $cols = array_merge($resumenCols, $this->columnasDetalle($tipo));
            $this->construirHoja($hoja, 'REPORTE DE PACIENTES - ' . mb_strtoupper($tituloHoja), $cols, $delTipo);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    /**
     * Definición de las columnas del resumen común a todas las hojas.
     */
    private function columnasResumen(): array
    {
        return [
            ['label' => 'Nº', 'grupo' => 'Datos Generales', 'width' => 5, 'get' => fn($p, $i) => $i],
            ['label' => 'Expediente', 'grupo' => 'Datos Generales', 'width' => 13, 'get' => fn($p) => $p->numero_expediente],
            ['label' => 'Apellido y Nombre', 'grupo' => 'Datos Generales', 'width' => 26, 'get' => fn($p) => $this->nombre($p->detalle)],
            ['label' => 'Cédula', 'grupo' => 'Datos Generales', 'width' => 13, 'get' => fn($p) => $this->cedula($p->detalle)],
            ['label' => 'Edad', 'grupo' => 'Datos Generales', 'width' => 7, 'get' => fn($p) => $this->edad($p->detalle)],
            ['label' => 'Sexo', 'grupo' => 'Datos Generales', 'width' => 10, 'get' => fn($p) => $this->genero($p->detalle)],
            ['label' => 'Tipo de Paciente', 'grupo' => 'Datos Generales', 'width' => 14, 'get' => fn($p) => ucfirst($p->tipo_paciente ?? '')],
            ['label' => 'Tipo de Atención', 'grupo' => 'Datos Generales', 'width' => 13, 'get' => fn($p) => $p->tipo_atencion === 'privado' ? 'Privado' : 'Público'],
            ['label' => 'Prioridad', 'grupo' => 'Datos Generales', 'width' => 11, 'get' => fn($p) => ucfirst($p->prioridad ?? '')],
            ['label' => 'Estado', 'grupo' => 'Datos Generales', 'width' => 15, 'get' => fn($p) => $p->estado ? $p->estado->tipo : 'N/A'],
            ['label' => 'Municipio', 'grupo' => 'Datos Generales', 'width' => 12, 'get' => fn($p) => $p->municipio ?? ''],
            ['label' => 'Teléfono', 'grupo' => 'Datos Generales', 'width' => 13, 'get' => fn($p) => $this->telefono($p->detalle)],
            ['label' => 'Motivo de Consulta', 'grupo' => 'Datos Generales', 'width' => 40, 'get' => fn($p) => mb_substr($p->motivo_consulta ?? '', 0, 120)],
            ['label' => 'Fecha Registro', 'grupo' => 'Datos Generales', 'width' => 13, 'get' => fn($p) => $p->created_at ? $p->created_at->format('d/m/Y') : ''],
        ];
    }

    /**
     * Columnas del detalle clínico para un tipo (según CamposClinicos).
     */
    private function columnasDetalle(string $tipo): array
    {
        $cols = [];
        foreach (CamposClinicos::columnasDeTipo($tipo, self::CAMPOS_RESUMEN) as $info) {
            $campo = $info['campo'];
            $cols[] = [
                'label' => $info['etiqueta'],
                'grupo' => $info['grupo'],
                'width' => 22,
                'get' => fn($p) => CamposClinicos::valor($p->detalle, $campo),
            ];
        }
        return $cols;
    }

    /**
     * Construye una hoja con la plantilla institucional completa.
     */
    private function construirHoja($sheet, string $titulo, array $cols, $pacientes): void
    {
        $this->configurarPagina($sheet);

        $totalCols = count($cols);
        $lastCol = $this->col($totalCols);

        $this->encabezadoInstitucional($sheet, $lastCol);
        $this->tituloReporte($sheet, $titulo, $lastCol);
        $this->metaReporte($sheet, 'Generado: ' . now()->format('d/m/Y H:i') . '   |   Total de registros: ' . $pacientes->count(), $lastCol);
        $this->lineaFiltros($sheet, $this->filtros, $lastCol);

        $filaGrupo = 10;
        $filaHeader = 11;
        $filaDatos = 12;

        // Banda de grupos: se fusionan las celdas de cada grupo
        $grupoActual = $cols[0]['grupo'];
        $inicioGrupo = 1;
        for ($i = 2; $i <= $totalCols; $i++) {
            if ($cols[$i - 1]['grupo'] !== $grupoActual) {
                $this->fusionarGrupo($sheet, $grupoActual, $inicioGrupo, $i - 1, $filaGrupo);
                $grupoActual = $cols[$i - 1]['grupo'];
                $inicioGrupo = $i;
            }
        }
        $this->fusionarGrupo($sheet, $grupoActual, $inicioGrupo, $totalCols, $filaGrupo);

        // Fila de etiquetas de campo
        foreach ($cols as $i => $c) {
            $sheet->setCellValue($this->col($i + 1) . $filaHeader, $c['label']);
            $sheet->getColumnDimension($this->col($i + 1))->setWidth($c['width']);
        }

        $this->estilizarEncabezadoTabla($sheet, $filaGrupo, $lastCol);
        $this->estilizarEncabezadoTabla($sheet, $filaHeader, $lastCol);
        $sheet->getRowDimension($filaGrupo)->setRowHeight(20);

        // Datos
        $row = $filaDatos;
        $num = 1;
        foreach ($pacientes as $p) {
            foreach ($cols as $i => $c) {
                $this->ponerValor($sheet, $this->col($i + 1) . $row, ($c['get'])($p, $num));
            }
            $this->estilizarFilaDatos($sheet, $row, $lastCol, $num);
            $row++;
            $num++;
        }

        // Auto-filtro y congelar panel
        $sheet->setAutoFilter("A{$filaHeader}:{$lastCol}" . max($filaHeader, $row - 1));
        $sheet->freezePane('A' . $filaDatos);

        // TOTAL Y FIRMAS
        $totalRow = $row + 1;
        $sheet->setCellValue('C' . $totalRow, 'TOTAL DE PACIENTES:');
        $sheet->getStyle('C' . $totalRow)->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('C' . $totalRow)->getAlignment()->setHorizontal('right');
        $sheet->setCellValue('D' . $totalRow, $pacientes->count());
        $sheet->getStyle('D' . $totalRow)->getFont()->setBold(true)->setSize(10);

        $this->firmasInstitucionales($sheet, $totalRow + 3, $lastCol, $totalCols);
    }

    /**
     * Escribe un valor evitando que los textos largos se interpreten como fórmulas.
     */
    private function ponerValor($sheet, string $coordenada, $valor): void
    {
        if (is_int($valor) || is_float($valor)) {
            $sheet->setCellValue($coordenada, $valor);
            return;
        }
        $sheet->setCellValueExplicit($coordenada, (string) $valor, DataType::TYPE_STRING);
    }

    private function fusionarGrupo($sheet, string $titulo, int $desde, int $hasta, int $fila): void
    {
        $rango = $this->col($desde) . $fila . ':' . $this->col($hasta) . $fila;
        if ($desde !== $hasta) {
            $sheet->mergeCells($rango);
        }
        $sheet->setCellValue($this->col($desde) . $fila, $titulo);
    }
}
