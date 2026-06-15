@props(['cita', 'tipo' => 'proxima'])

@php
    $claseEstado = '';
    if($cita->estado === 'atendida') {
        $claseEstado = 'atendida';
    } elseif($cita->estado === 'cancelada') {
        $claseEstado = 'cancelada';
    } elseif($cita->estado === 'no_asistio') {
        $claseEstado = 'no-asistio';
    }
@endphp

<div class="cita-card {{ $tipo }} {{ $claseEstado }}">
    <div class="cita-card-header">
        <div class="cita-paciente-info">
            <div class="cita-nombre">
                <i class="fas fa-user-circle"></i>
                {{ $cita->paciente->nombre_completo ?? 'Paciente' }}
            </div>
            <div class="cita-hora">
                <i class="far fa-calendar-alt"></i>
                {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
            </div>
        </div>
        
        @if($cita->estado === 'pendiente' && $tipo === 'hoy')
            <span class="badge-hoy">HOY</span>
        @endif
    </div>

    <div class="cita-objetivo">
        <i class="fas fa-bullseye"></i>
        {{ $cita->objetivo ?? 'Sin objetivo definido' }}
    </div>

    @if($cita->estado === 'pendiente')
    <div class="cita-actions">
        <button type="button" class="btn-asistio" 
                onclick="cambiarEstado({{ $cita->id }}, 'atendida')"
                title="Marcar como asistido">
            <i class="fas fa-check-circle"></i>
            Asistió
        </button>
        
        <button type="button" class="btn-no-asistio" 
                onclick="cambiarEstado({{ $cita->id }}, 'no_asistio')"
                title="Marcar como no asistido">
            <i class="fas fa-times-circle"></i>
            No Asistió
        </button>
        
        <button type="button" class="btn-cancelar" 
                onclick="abrirModalCancelar({{ $cita->id }})"
                title="Cancelar cita">
            <i class="fas fa-ban"></i>
            Cancelar
        </button>
    </div>
    @endif
</div>