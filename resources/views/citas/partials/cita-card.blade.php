{{-- citas/partials/cita-card.blade.php --}}
@php
    $nombrePaciente = 'Sin nombre';
    $inicial = 'P';
    $tipoPaciente = $cita->paciente->tipo_paciente ?? 'adulto';
    $tipoAtencion = $cita->paciente->tipo_atencion ?? 'publico';
    $expediente = $cita->paciente->numero_expediente ?? 'N/A';
    if ($cita->paciente && $cita->paciente->detalle) {
        $nombrePaciente = $cita->paciente->detalle->nombre . ' ' . $cita->paciente->detalle->apellido;
        $inicial = strtoupper(substr($cita->paciente->detalle->nombre, 0, 1));
    }
    $colorAvatar = $tipoAtencion === 'privado' ? '#0ea5e9' : '#d97706';
    $colorBorde = $tipoAtencion === 'privado' ? '#0ea5e9' : '#d97706';
@endphp
<div class="cita-card" data-id="{{ $cita->id }}" data-es-hoy="{{ $cita->es_hoy ? '1' : '0' }}" style="border-left: 4px solid {{ $colorBorde }};">
    <div class="cita-card-header">
        <div class="paciente-info">
            <div class="paciente-avatar" style="background: {{ $colorAvatar }};">{{ $inicial }}</div>
            <div>
                <h4>{{ $nombrePaciente }}</h4>
                <span class="expediente-badge">Exp: {{ $expediente }}</span>
            </div>
        </div>
        <span class="cita-estado estado-pendiente">Pendiente</span>
    </div>
    <div class="cita-card-body">
        <div class="cita-info-row">
            <i class="fas fa-calendar-day"></i>
            <strong>Fecha:</strong> {{ $cita->fecha_formateada }}
        </div>
        <div class="cita-info-row">
            <i class="fas fa-clock"></i>
            <strong>Tipo:</strong> {{ $tipoAtencion === 'privado' ? 'Privado' : 'Público' }}
        </div>
        <div class="cita-info-row">
            <i class="fas fa-user-tag"></i>
            <strong>Paciente:</strong> {{ ucfirst($tipoPaciente) }}
        </div>
        @if($cita->objetivo)
        <div class="cita-info-row">
            <i class="fas fa-bullseye"></i>
            <strong>Objetivo:</strong> {{ Str::limit($cita->objetivo, 60) }}
        </div>
        @endif
    </div>
    <div class="cita-card-footer">
        @if($cita->es_hoy)
        <button type="button" class="btn-icon btn-asistio" onclick="CitasManager.cambiarEstado(this, {{ $cita->id }}, 'atendida')" title="Asistió">
            <i class="fas fa-check-circle"></i>
        </button>
        <button type="button" class="btn-icon btn-no-asistio" onclick="CitasManager.cambiarEstado(this, {{ $cita->id }}, 'no_asistio')" title="No asistió">
            <i class="fas fa-user-slash"></i>
        </button>
        @endif
        <button type="button" class="btn-icon btn-cancelar" onclick="abrirModalCancelar({{ $cita->id }})" title="Cancelar">
            <i class="fas fa-times-circle"></i>
        </button>
    </div>
</div>
