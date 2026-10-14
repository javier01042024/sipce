<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Reporte de Pacientes</title>
    @include('reportes.pdf.partials.estilos')
</head>
<body>
    @include('reportes.pdf.partials.encabezado')

    <div class="titulo">Reporte de Pacientes</div>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Total de registros: {{ $pacientes->count() }}</p>

    @if(!empty($filtros))
    <p class="filtros">Filtros aplicados: {{ implode('  |  ', array_map(fn($k, $v) => "{$k}: {$v}", array_keys($filtros), array_values($filtros))) }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 22px;">Nº</th>
                <th style="width: 60px;">Expediente</th>
                <th>Paciente</th>
                <th style="width: 70px;">Tipo</th>
                <th style="width: 60px;">Atención</th>
                <th style="width: 60px;">Prioridad</th>
                <th style="width: 90px;">Estado</th>
                <th style="width: 62px;">Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pacientes as $i => $p)
            @php
                $d = $p->detalle;
                $nombre = $d ? trim(($d->nombre ?? '') . ' ' . ($d->apellido ?? '')) : 'N/A';
                $prio = $p->prioridad ?? 'baja';
            @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center">{{ $p->numero_expediente }}</td>
                <td>{{ $nombre }}</td>
                <td class="center">{{ ucfirst($p->tipo_paciente ?? '') }}</td>
                <td class="center">{{ $p->tipo_atencion === 'privado' ? 'Privado' : 'Público' }}</td>
                <td class="center">{{ ucfirst($prio) }}</td>
                <td class="center">{{ $p->estado?->tipo ?? 'N/A' }}</td>
                <td class="center">{{ $p->created_at ? $p->created_at->format('d/m/Y') : '' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="center" style="padding: 20px;">No se encontraron pacientes con los criterios seleccionados.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="resumen">Total de pacientes listados: {{ $pacientes->count() }}</div>

    @include('reportes.pdf.partials.firmas')

    <div class="footer" style="text-align:center; border-top:1px solid #cbd5e1; margin-top:12px; padding-top:4px; font-size:7px; color:#94a3b8;">
        SIPCE &mdash; Sistema de Información de Pacientes de Salud Mental &nbsp;|&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

    @include('reportes.pdf.partials.pie')
</body>
</html>
