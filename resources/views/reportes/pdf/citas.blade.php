<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Reporte de Citas</title>
    @include('reportes.pdf.partials.estilos')
</head>
<body>
    @include('reportes.pdf.partials.encabezado')

    <div class="titulo">Reporte de Citas</div>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Total de citas: {{ $citas->count() }}</p>

    @if(!empty($filtros))
    <p class="filtros">Filtros aplicados: {{ implode('  |  ', array_map(fn($k, $v) => "{$k}: {$v}", array_keys($filtros), array_values($filtros))) }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 22px;">Nº</th>
                <th style="width: 55px;">Fecha</th>
                <th style="width: 95px;">Paciente</th>
                <th style="width: 52px;">Expediente</th>
                <th style="width: 52px;">Atención</th>
                <th style="width: 55px;">Estado</th>
                <th>Objetivo</th>
                <th>Planificación</th>
                <th style="width: 90px;">Motivo de Cancelación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($citas as $i => $c)
            @php
                $d = $c->paciente?->detalle;
                $nombre = $d ? trim(($d->nombre ?? '') . ' ' . ($d->apellido ?? '')) : 'N/A';
                $estados = ['pendiente' => 'Pendiente', 'atendida' => 'Atendida', 'cancelada' => 'Cancelada', 'no_asistio' => 'No Asistió'];
            @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center">{{ $c->fecha ? $c->fecha->format('d/m/Y') : '' }}</td>
                <td>{{ $nombre }}</td>
                <td class="center">{{ $c->paciente?->numero_expediente ?? '' }}</td>
                <td class="center">{{ $c->paciente?->tipo_atencion === 'privado' ? 'Privado' : 'Público' }}</td>
                <td class="center">{{ $estados[$c->estado] ?? $c->estado }}</td>
                <td>{{ mb_substr($c->objetivo ?? '', 0, 120) }}</td>
                <td>{{ mb_substr($c->planificacion ?? '', 0, 120) }}</td>
                <td>{{ mb_substr($c->motivo_cancelacion ?? '', 0, 120) }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="center" style="padding: 20px;">No se encontraron citas con los criterios seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totales">
        <tr>
            <td style="width: 120px; font-weight: bold; font-size: 9px;">TOTAL DE CITAS:</td>
            <td style="font-weight: bold; font-size: 9px;">{{ $citas->count() }}</td>
            <td></td>
        </tr>
    </table>

    @include('reportes.pdf.partials.firmas')

    <div class="footer" style="text-align:center; border-top:1px solid #cbd5e1; margin-top:12px; padding-top:4px; font-size:7px; color:#94a3b8;">
        SIPCE &mdash; Sistema de Información de Pacientes de Salud Mental &nbsp;|&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

    @include('reportes.pdf.partials.pie')
</body>
</html>
