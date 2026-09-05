@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-calendar-alt me-2"></i> Calendario</h1>
            <p>Vista general de citas y sesiones</p>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px;">
        <div id="calendar"></div>
    </div>

    <div style="display: flex; gap: 16px; flex-wrap: wrap; padding: 0 4px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 14px; height: 14px; border-radius: 3px; background: #10b981; display: inline-block;"></span>
            <span style="font-size: 13px; color: #475569;">Pendiente</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 14px; height: 14px; border-radius: 3px; background: var(--sipce-primary); display: inline-block;"></span>
            <span style="font-size: 13px; color: #475569;">Confirmada</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 14px; height: 14px; border-radius: 3px; background: #f59e0b; display: inline-block;"></span>
            <span style="font-size: 13px; color: #475569;">En Progreso</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 14px; height: 14px; border-radius: 3px; background: #0ea5e9; display: inline-block;"></span>
            <span style="font-size: 13px; color: #475569;">Atendida</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 14px; height: 14px; border-radius: 3px; background: #ef4444; display: inline-block;"></span>
            <span style="font-size: 13px; color: #475569;">Cancelada</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="width: 14px; height: 14px; border-radius: 3px; background: #94a3b8; display: inline-block;"></span>
            <span style="font-size: 13px; color: #475569;">No Asistió</span>
        </div>
    </div>

    <div id="eventoModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center;">
        <div style="background:white; border-radius:16px; padding:30px; max-width:420px; width:90%; box-shadow:0 25px 50px rgba(0,0,0,0.3); position:relative;">
            <button onclick="cerrarModal()" style="position:absolute; top:12px; right:12px; background:none; border:none; font-size:18px; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
            <h3 id="modalTitulo" style="margin:0 0 12px 0; color:#1e293b; font-size:18px;"></h3>
            <div id="modalPaciente" style="margin-bottom:8px; color:#475569;"></div>
            <div id="modalEstado" style="margin-bottom:8px;"></div>
            <div id="modalObjetivo" style="color:#64748b; font-size:13px;"></div>
            <div style="margin-top:20px; text-align:right;">
                <button onclick="cerrarModal()" style="padding:8px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.9/locales/global.es.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día',
            list: 'Lista',
            prev: 'Anterior',
            next: 'Siguiente',
            prevYear: 'Año anterior',
            nextYear: 'Año siguiente'
        },
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        allDayText: 'Todo el día',
        noEventsText: 'No hay eventos para mostrar',
        events: '{{ route("calendario.eventos") }}',
        eventColor: 'var(--sipce-primary)',
        eventTextColor: '#ffffff',
        eventDisplay: 'block',
        dayMaxEvents: 4,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            var ev = info.event;
            var props = ev.extendedProps;
            document.getElementById('modalTitulo').textContent = ev.title;
            document.getElementById('modalPaciente').innerHTML = '<strong>Paciente:</strong> ' + (props.paciente_nombre || 'N/A');
            var estado = props.estado || 'N/A';
            var badgeClass = 'sipce-badge-info';
            if (estado === 'pendiente') badgeClass = 'sipce-badge-success';
            else if (estado === 'atendida') badgeClass = 'sipce-badge-info';
            else if (estado === 'cancelada') badgeClass = 'sipce-badge-danger';
            else if (estado === 'no_asistio') badgeClass = 'sipce-badge-neutral';
            else if (estado === 'confirmada') badgeClass = 'sipce-badge-primary';
            document.getElementById('modalEstado').innerHTML = '<span class="sipce-badge ' + badgeClass + '">' + estado.charAt(0).toUpperCase() + estado.slice(1).replace('_', ' ') + '</span>';
            document.getElementById('modalObjetivo').innerHTML = props.objetivo ? '<strong>Objetivo:</strong> ' + props.objetivo : '';
            document.getElementById('eventoModal').style.display = 'flex';
        }
    });
    calendar.render();
});

function cerrarModal() {
    document.getElementById('eventoModal').style.display = 'none';
}

document.getElementById('eventoModal').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
</script>
@endpush
@endsection
