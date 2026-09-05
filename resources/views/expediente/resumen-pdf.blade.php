<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Resumen Clínico</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 12px; line-height: 1.5; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid var(--sipce-primary); padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; color: var(--sipce-primary); }
        .header p { margin: 5px 0 0 0; color: #666; font-size: 11px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: var(--sipce-primary); border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 15px; }
        .info-item { padding: 8px; background: #f8fafc; border-radius: 4px; }
        .info-label { font-size: 10px; color: #94a3b8; text-transform: uppercase; font-weight: 600; }
        .info-value { font-size: 12px; color: #1e293b; font-weight: 500; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #f1f5f9; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
        td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .notas-item { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .notas-item:last-child { border-bottom: none; }
        .notas-meta { font-size: 10px; color: #94a3b8; }
        .footer { text-align: center; border-top: 2px solid #e2e8f0; padding-top: 15px; margin-top: 30px; color: #94a3b8; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SIPCE - Resumen Clinico</h1>
        <p>Fecha de generacion: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Informacion del Paciente</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nombre Completo</div>
                <div class="info-value">{{ $paciente->detalle->nombre ?? '' }} {{ $paciente->detalle->apellido ?? '' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">No. Expediente</div>
                <div class="info-value">{{ $paciente->numero_expediente }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Fecha de Nacimiento</div>
                <div class="info-value">{{ $paciente->detalle->fecha_nacimiento ? $paciente->detalle->fecha_nacimiento->format('d/m/Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Telefono</div>
                <div class="info-value">{{ $paciente->detalle->telefono ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    @if($paciente->motivo_consulta)
    <div class="section">
        <div class="section-title">Motivo de Consulta</div>
        <p style="margin:0; font-size:12px;">{{ $paciente->motivo_consulta }}</p>
    </div>
    @endif

    @if($paciente->diagnostico_preliminar)
    <div class="section">
        <div class="section-title">Diagnostico Preliminar</div>
        <p style="margin:0; font-size:12px;">{{ $paciente->diagnostico_preliminar }}</p>
    </div>
    @endif

    @if(isset($paciente->notas) && $paciente->notas->count() > 0)
    <div class="section">
        <div class="section-title">Ultimas Notas ({{ min($paciente->notas->count(), 5) }})</div>
        @foreach($paciente->notas->take(5) as $nota)
        <div class="notas-item">
            <p style="margin:0 0 4px 0; font-size:12px;">{{ $nota->contenido }}</p>
            <div class="notas-meta">
                <strong>{{ $nota->user->name ?? 'Usuario' }}</strong> - {{ $nota->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if(isset($paciente->citas) && $paciente->citas->count() > 0)
    <div class="section">
        <div class="section-title">Ultimas Citas ({{ min($paciente->citas->count(), 10) }})</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->citas->take(10) as $cita)
                <tr>
                    <td>{{ $cita->fecha->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $badgeClass = 'badge-info';
                            if ($cita->estado === 'atendida') $badgeClass = 'badge-success';
                            elseif ($cita->estado === 'cancelada') $badgeClass = 'badge-danger';
                            elseif ($cita->estado === 'pendiente') $badgeClass = 'badge-warning';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($paciente->planesTratamiento) && $paciente->planesTratamiento->count() > 0)
    <div class="section">
        <div class="section-title">Planes de Tratamiento Activos</div>
        @foreach($paciente->planesTratamiento->where('estado', 'activo') as $plan)
        <div style="margin-bottom:10px; padding:8px; background:#f8fafc; border-radius:4px;">
            <strong style="font-size:12px;">{{ $plan->titulo }}</strong>
            <span class="badge {{ $plan->estado === 'activo' ? 'badge-success' : 'badge-warning' }}" style="margin-left:8px;">{{ ucfirst($plan->estado) }}</span>
            @if($plan->objetivos->count() > 0)
            <ul style="margin:6px 0 0 0; padding-left:18px; font-size:11px;">
                @foreach($plan->objetivos as $objetivo)
                <li>{{ $objetivo->descripcion }} - <em>{{ str_replace('_', ' ', $objetivo->estado) }}</em></li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Documento generado automaticamente por SIPCE el {{ now()->format('d/m/Y a las H:i') }}</p>
    </div>
</body>
</html>
