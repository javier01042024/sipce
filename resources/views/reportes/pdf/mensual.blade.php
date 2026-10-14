<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Registro Mensual de Pacientes</title>
    @include('reportes.pdf.partials.estilos')
</head>
<body>
    @include('reportes.pdf.partials.encabezado')

    <div class="titulo">REGISTRO INDIVIDUALIZADO DE PACIENTES DE SALUD MENTAL</div>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Año: {{ $anio }} &nbsp;|&nbsp; Mes: {{ strtoupper($meses[$mes - 1] ?? '') }}</p>

    @if($municipio)
    <p class="filtros">Filtros aplicados: Municipio: {{ $municipio }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 18px;">Nº</th>
                <th style="width: 85px;">APELLIDO Y NOMBRE</th>
                <th style="width: 55px;">Nº. DE CÉDULA</th>
                <th style="width: 18px;">EDAD</th>
                <th style="width: 55px;">MUNICIPIO</th>
                <th>DIRECCIÓN DE HABITACIÓN COMPLETA</th>
                <th style="width: 55px;">Nº TELÉFONO</th>
                <th style="width: 55px;">CONSULTORIO POPULAR</th>
                <th style="width: 30px;">ASIC</th>
                <th style="width: 28px;">SEXO (F/M)</th>
                <th style="width: 35px;">TIPO CONS. (P/S)</th>
                <th style="width: 90px;">DX. MÉDICO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pacientes as $i => $p)
            @php
                $d = $p->detalle;
                if (!$d) continue;
                $edad = $d->fecha_nacimiento ? \Carbon\Carbon::parse($d->fecha_nacimiento)->age : '';
                $sexo = $d->genero ?? $d->sexo ?? '';
                $sexo = strtoupper(mb_substr($sexo, 0, 1));
                $tipoCons = $p->tipo_atencion === 'publico' ? 'P' : 'S';
                $dx = '';
                if ($p->diagnosticoPrincipal) {
                    $dx = ($p->diagnosticoPrincipal->codigo_cie ? $p->diagnosticoPrincipal->codigo_cie . ' - ' : '') . $p->diagnosticoPrincipal->diagnostico;
                } elseif ($p->diagnostico_preliminar) {
                    $dx = $p->diagnostico_preliminar;
                }
            @endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ trim(($d->apellido ?? '') . ' ' . ($d->nombre ?? '')) }}</td>
                <td>{{ $d->cedula ?? '' }}</td>
                <td class="center">{{ $edad }}</td>
                <td>{{ $p->municipio ?? '' }}</td>
                <td>{{ $d->direccion ?? '' }}</td>
                <td>{{ $p->telefono ?? '' }}</td>
                <td></td>
                <td></td>
                <td class="center">{{ $sexo }}</td>
                <td class="center">{{ $tipoCons }}</td>
                <td>{{ mb_substr($dx, 0, 110) }}</td>
            </tr>
            @empty
            <tr><td colspan="12" class="center" style="padding: 20px;">No hay pacientes atendidos en este período.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="resumen">
        Resumen: Total: {{ $pacientes->count() }} &nbsp;|&nbsp; Niños: {{ $pacientes->where('tipo_paciente', 'niño')->count() }} &nbsp;|&nbsp; Adolescentes: {{ $pacientes->where('tipo_paciente', 'adolescente')->count() }} &nbsp;|&nbsp; Adultos: {{ $pacientes->where('tipo_paciente', 'adulto')->count() }}
    </div>

    @include('reportes.pdf.partials.firmas')

    <div class="footer" style="text-align:center; border-top:1px solid #cbd5e1; margin-top:12px; padding-top:4px; font-size:7px; color:#94a3b8;">
        SIPCE &mdash; Sistema de Información de Pacientes de Salud Mental &nbsp;|&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}
    </div>

    @include('reportes.pdf.partials.pie')
</body>
</html>