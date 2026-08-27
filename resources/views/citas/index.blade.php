@extends('layouts.app')

@section('content')
<link href="{{ asset('css/citas.css') }}" rel="stylesheet">

<div class="citas-wrapper">

    <!-- HEADER -->
    @include('citas.partials.header')

    {{-- ==================== VISTA TARJETAS ==================== --}}
    <div id="vistaCards">
    
    {{-- CITAS PRIVADAS DE HOY --}}
    @if(isset($citasHoyPrivadas) && $citasHoyPrivadas->count() > 0)
        <div class="citas-section">
            <div class="section-title hoy" style="background: #f0f9ff; border-left: 4px solid #0ea5e9; color: #0369a1;">
                <i class="fas fa-building"></i>
                Citas Privadas de Hoy (<span class="section-count">{{ $citasHoyPrivadas->count() }}</span>)
            </div>
            <div class="citas-grid">
                @foreach($citasHoyPrivadas as $cita)
                    @include('citas.partials.cita-card', ['cita' => $cita])
                @endforeach
            </div>
        </div>
    @endif

    {{-- CITAS PÚBLICAS DE HOY --}}
    @if(isset($citasHoyPublicas) && $citasHoyPublicas->count() > 0)
        <div class="citas-section">
            <div class="section-title hoy" style="background: #fef3c7; border-left: 4px solid #d97706; color: #92400e;">
                <i class="fas fa-hospital"></i>
                Citas Públicas de Hoy (<span class="section-count">{{ $citasHoyPublicas->count() }}</span>)
            </div>
            <div class="citas-grid">
                @foreach($citasHoyPublicas as $cita)
                    @include('citas.partials.cita-card', ['cita' => $cita])
                @endforeach
            </div>
        </div>
    @endif

    {{-- PRÓXIMAS CITAS PRIVADAS --}}
    @if(isset($citasProximasPrivadas) && $citasProximasPrivadas->count() > 0)
        <div class="citas-section">
            <div class="section-title proximas" style="background: #f0f9ff; border-left: 4px solid #0ea5e9; color: #0369a1;">
                <i class="fas fa-building"></i>
                Próximas Citas Privadas (<span class="section-count">{{ $citasProximasPrivadas->count() }}</span>)
            </div>
            <div class="citas-grid">
                @foreach($citasProximasPrivadas as $cita)
                    @include('citas.partials.cita-card', ['cita' => $cita])
                @endforeach
            </div>
        </div>
    @endif

    {{-- PRÓXIMAS CITAS PÚBLICAS --}}
    @if(isset($citasProximasPublicas) && $citasProximasPublicas->count() > 0)
        <div class="citas-section">
            <div class="section-title proximas" style="background: #fef3c7; border-left: 4px solid #d97706; color: #92400e;">
                <i class="fas fa-hospital"></i>
                Próximas Citas Públicas (<span class="section-count">{{ $citasProximasPublicas->count() }}</span>)
            </div>
            <div class="citas-grid">
                @foreach($citasProximasPublicas as $cita)
                    @include('citas.partials.cita-card', ['cita' => $cita])
                @endforeach
            </div>
        </div>
    @endif

    {{-- CITAS CANCELADAS --}}
    @if(isset($citasCanceladas) && $citasCanceladas->count() > 0)
        <div class="citas-section">
            <div class="section-title canceladas" style="background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b;">
                <i class="fas fa-ban"></i>
                Citas Canceladas ({{ $citasCanceladas->count() }})
            </div>
            <div class="citas-grid">
                @foreach($citasCanceladas as $cita)
                    @php
                        $nombrePaciente = 'Sin nombre';
                        $inicial = 'P';
                        $tipoAtencion = $cita->paciente->tipo_atencion ?? 'publico';
                        $expediente = $cita->paciente->numero_expediente ?? 'N/A';
                        if ($cita->paciente && $cita->paciente->detalle) {
                            $nombrePaciente = $cita->paciente->detalle->nombre . ' ' . $cita->paciente->detalle->apellido;
                            $inicial = strtoupper(substr($cita->paciente->detalle->nombre, 0, 1));
                        }
                    @endphp
                    <div class="cita-card" data-id="{{ $cita->id }}" style="border-left: 4px solid #ef4444; opacity: 0.85;">
                        <div class="cita-card-header">
                            <div class="paciente-info">
                                <div class="paciente-avatar" style="background: #94a3b8;">{{ $inicial }}</div>
                                <div>
                                    <h4>{{ $nombrePaciente }}</h4>
                                    <span class="expediente-badge">Exp: {{ $expediente }}</span>
                                </div>
                            </div>
                            <span class="cita-estado estado-cancelada">Cancelada</span>
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
                            @if($cita->motivo_cancelacion)
                            <div class="cita-info-row text-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Motivo:</strong> {{ Str::limit($cita->motivo_cancelacion, 60) }}
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    </div>{{-- fin vistaCards --}}

    {{-- ==================== VISTA CALENDARIO ==================== --}}
    <div id="vistaCalendario" style="display: none;">
        <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- ESTADO VACÍO --}}
    <div id="estadoVacio" style="{{ $mostrarVacio ? '' : 'display: none;' }}">
        @include('citas.partials.empty-state')
    </div>

</div>

@include('citas.partials.modals.cancel-modal')
<script src="{{ asset('js/citas.js') }}"></script>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<style>
    .fc { font-family: 'Segoe UI', system-ui, sans-serif; }
    .fc .fc-toolbar-title { font-size: 1.2rem; font-weight: 700; color: #1e293b; }
    .fc .fc-button-primary {
        background: #667eea; border-color: #667eea; font-size: 0.8rem; padding: 6px 12px;
    }
    .fc .fc-button-primary:hover { background: #5a6fd6; }
    .fc .fc-button-primary.active { background: #4f46e5; }
    .fc .fc-today-button { background: #10b981 !important; border-color: #10b981 !important; }
    .fc .fc-today-button:disabled { opacity: 0.5; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.9/locales/global.es.js"></script>
<script>
function toggleVistaCitas(vista) {
    var cards = document.getElementById('vistaCards');
    var cal = document.getElementById('vistaCalendario');
    var btnCards = document.getElementById('btnVistaCards');
    var btnCal = document.getElementById('btnVistaCalendario');
    if (vista === 'calendario') {
        cards.style.display = 'none';
        cal.style.display = 'block';
        btnCards.classList.remove('active');
        btnCal.classList.add('active');
        if (!window._calendarRendered) initCalendar();
    } else {
        cards.style.display = '';
        cal.style.display = 'none';
        btnCards.classList.add('active');
        btnCal.classList.remove('active');
    }
}

function initCalendar() {
    window._calendarRendered = true;
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: 'es',
        initialView: 'dayGridMonth',
        buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día', list: 'Lista', prev: 'Anterior', next: 'Siguiente' },
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        allDayText: 'Todo el día',
        noEventsText: 'No hay eventos para mostrar',
        events: '{{ route("calendario.eventos") }}',
        eventColor: '#667eea',
        eventTextColor: '#ffffff',
        eventDisplay: 'block',
        dayMaxEvents: 4,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            var ev = info.event;
            var props = ev.extendedProps;
            var badgeClass = 'sipce-badge-info';
            var estado = props.estado || '';
            if (estado === 'pendiente') badgeClass = 'sipce-badge-success';
            else if (estado === 'atendida') badgeClass = 'sipce-badge-info';
            else if (estado === 'cancelada') badgeClass = 'sipce-badge-danger';
            else if (estado === 'no_asistio') badgeClass = 'sipce-badge-neutral';
            else if (estado === 'confirmada') badgeClass = 'sipce-badge-primary';
            var html = '<p><strong>Paciente:</strong> ' + (props.paciente_nombre || 'N/A') + '</p>';
            html += '<p><span class="sipce-badge ' + badgeClass + '">' + estado.charAt(0).toUpperCase() + estado.slice(1).replace('_', ' ') + '</span></p>';
            if (props.objetivo) html += '<p style="margin-top:8px;"><strong>Objetivo:</strong> ' + props.objetivo + '</p>';
            SIPCE_ALERT.info(html, ev.title);
        }
    });
    calendar.render();
}
</script>
@endpush
@endsection