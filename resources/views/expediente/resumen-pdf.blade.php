<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Resumen Clínico</title>
    @include('reportes.pdf.partials.estilos')
    <style>
        body { font-size: 11px; line-height: 1.5; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 13px; font-weight: bold; color: #1F3864; border-bottom: 2px solid #cbd5e1; padding-bottom: 5px; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-grid td { padding: 5px 8px; border: 0.5px solid #cbd5e1; vertical-align: top; }
        .info-grid .label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 600; background: #f1f5f9; width: 28%; }
        .info-grid .valor { font-size: 11px; color: #1e293b; font-weight: 500; }
        .nota-item { padding: 8px; margin-bottom: 8px; background: #f8fafc; border-left: 3px solid #1F3864; }
        .nota-meta { font-size: 9px; color: #94a3b8; margin-top: 4px; }
        .plan-item { padding: 8px; margin-bottom: 8px; background: #f8fafc; border-left: 3px solid #1F3864; }
        .plan-item ul { margin: 6px 0 0 0; padding-left: 18px; font-size: 10px; }
    </style>
</head>
<body>
    @include('reportes.pdf.partials.encabezado')

    <div class="titulo">RESUMEN CLÍNICO</div>
    <p class="meta">Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Expediente Nº {{ $paciente->numero_expediente }}</p>

    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <table class="info-grid">
            <tr>
                <td class="label">Nombre completo</td>
                <td class="valor">{{ $paciente->detalle->nombre ?? '' }} {{ $paciente->detalle->apellido ?? '' }}</td>
                <td class="label">Nº de expediente</td>
                <td class="valor">{{ $paciente->numero_expediente }}</td>
            </tr>
            <tr>
                <td class="label">Cédula</td>
                <td class="valor">{{ $paciente->cedula_paciente ?? 'N/A' }}</td>
                <td class="label">Tipo de paciente</td>
                <td class="valor">{{ ucfirst($paciente->tipo_paciente ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de nacimiento</td>
                <td class="valor">{{ $paciente->detalle->fecha_nacimiento ? $paciente->detalle->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</td>
                <td class="label">Teléfono</td>
                <td class="valor">{{ $paciente->telefono ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Tipo de atención</td>
                <td class="valor">{{ ucfirst($paciente->tipo_atencion ?? 'N/A') }}</td>
                <td class="label">Prioridad</td>
                <td class="valor">{{ ucfirst($paciente->prioridad ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="label">Municipio</td>
                <td class="valor">{{ $paciente->municipio ?? 'N/A' }}</td>
                <td class="label">Estado</td>
                <td class="valor">{{ $paciente->estado?->tipo ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    @if($paciente->motivo_consulta)
    <div class="section">
        <div class="section-title">Motivo de Consulta</div>
        <p style="margin:0;">{{ $paciente->motivo_consulta }}</p>
    </div>
    @endif

    @if($paciente->diagnostico_preliminar)
    <div class="section">
        <div class="section-title">Diagnóstico Preliminar</div>
        <p style="margin:0;">{{ $paciente->diagnostico_preliminar }}</p>
    </div>
    @endif

    @if(isset($paciente->diagnosticos) && $paciente->diagnosticos->count() > 0)
    <div class="section">
        <div class="section-title">Diagnósticos</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">CIE-10</th>
                    <th>Diagnóstico</th>
                    <th style="width: 70px;">Principal</th>
                    <th style="width: 70px;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->diagnosticos as $diag)
                <tr>
                    <td>{{ $diag->codigo_cie ?? '—' }}</td>
                    <td>{{ $diag->diagnostico }}</td>
                    <td class="center">{{ $diag->es_principal ? 'Sí' : 'No' }}</td>
                    <td class="center">{{ $diag->fecha ? $diag->fecha->format('d/m/Y') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($paciente->citas) && $paciente->citas->count() > 0)
    <div class="section">
        <div class="section-title">Historial de Citas</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">Fecha</th>
                    <th style="width: 90px;">Estado</th>
                    <th>Objetivo</th>
                    <th style="width: 130px;">Planificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->citas as $cita)
                <tr>
                    <td class="center">{{ $cita->fecha ? $cita->fecha->format('d/m/Y') : '—' }}</td>
                    <td class="center">{{ $cita->estado_texto }}</td>
                    <td>{{ mb_substr($cita->objetivo ?? '', 0, 120) }}</td>
                    <td>{{ mb_substr($cita->planificacion ?? '', 0, 120) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($paciente->notas) && $paciente->notas->count() > 0)
    <div class="section">
        <div class="section-title">Notas Clínicas</div>
        @foreach($paciente->notas as $nota)
        <div class="nota-item">
            <p style="margin:0;">{{ $nota->anotacion }}</p>
            <div class="nota-meta">
                <strong>{{ $nota->user->name ?? 'Usuario' }}</strong> &mdash; {{ $nota->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if(isset($paciente->planesTratamiento) && $paciente->planesTratamiento->count() > 0)
    <div class="section">
        <div class="section-title">Planes de Tratamiento</div>
        @foreach($paciente->planesTratamiento as $plan)
        <div class="plan-item">
            <strong>{{ $plan->titulo }}</strong>
            &nbsp;<span style="color:#64748b;">[{{ $plan->estado_texto }}]</span>
            @if($plan->objetivo_general)
            <br><span style="font-size:10px;"><em>Objetivo general:</em> {{ $plan->objetivo_general }}</span>
            @endif
            @if($plan->objetivos->count() > 0)
            <ul>
                @foreach($plan->objetivos as $objetivo)
                <li>{{ $objetivo->descripcion }} <em>({{ $objetivo->estado_texto }})</em></li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @include('reportes.pdf.partials.firmas')

    <div class="footer" style="text-align:center; border-top:1px solid #cbd5e1; margin-top:12px; padding-top:4px; font-size:7px; color:#94a3b8;">
        SIPCE &mdash; Sistema de Información de Pacientes de Salud Mental &nbsp;|&nbsp; Documento generado automáticamente el {{ now()->format('d/m/Y H:i') }}
    </div>

    @include('reportes.pdf.partials.pie')
</body>
</html>