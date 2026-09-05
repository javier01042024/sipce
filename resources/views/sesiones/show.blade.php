@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('sesiones.index') }}" style="color: var(--sipce-primary); text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin: 0; color: #1e293b;">Detalle de Sesión</h2>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div>
                <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 22px;">Sesión #{{ $sesion->id }}</h3>
                <p style="margin: 0; color: #64748b; font-size: 14px;">
                    Paciente: <strong>
                        <a href="{{ route('pacientes.show', $sesion->paciente) }}" style="color: var(--sipce-primary); text-decoration: none;">
                            {{ $sesion->paciente->detalle->nombre ?? '' }} {{ $sesion->paciente->detalle->apellido ?? 'N/A' }}
                        </a>
                    </strong>
                </p>
            </div>
            <div style="text-align: right;">
                <span class="sipce-cell-date" style="font-size: 14px;"><i class="far fa-calendar-alt"></i> {{ $sesion->fecha->format('d/m/Y') }}</span>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Duración: <strong>{{ $sesion->duracion_minutos ?? 'N/A' }} min</strong></p>
            </div>
        </div>

        @if($sesion->user)
        <div style="margin-bottom: 20px; padding: 12px; background: #f8fafc; border-radius: 10px;">
            <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Atendido por</span>
            <p style="margin: 4px 0 0 0; color: #334155;">{{ $sesion->user->name }}</p>
        </div>
        @endif

        <div style="margin-bottom: 20px;">
            <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Resumen</span>
            <p style="margin: 4px 0 0 0; color: #334155; line-height: 1.6;">{{ $sesion->resumen }}</p>
        </div>

        @if($sesion->observaciones_clinicas)
        <div style="margin-bottom: 20px;">
            <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Observaciones Clínicas</span>
            <p style="margin: 4px 0 0 0; color: #334155; line-height: 1.6;">{{ $sesion->observaciones_clinicas }}</p>
        </div>
        @endif

        <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            <a href="{{ route('pacientes.show', $sesion->paciente) }}" style="padding:10px 20px;background:#f1f5f9;color:#475569;border-radius:10px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
                <i class="fas fa-user"></i> Ver Paciente
            </a>
            <a href="{{ route('sesiones.edit', $sesion) }}" style="padding:10px 20px;background:linear-gradient(135deg,#6366f1,#818cf8);color:white;border-radius:10px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:8px;">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
</div>

{{-- MODAL: AGENDAR PRÓXIMA CITA --}}
@if(session('mostrar_agendar'))
<div id="modalAgendar" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:white;border-radius:20px;padding:0;max-width:450px;width:95%;box-shadow:0 25px 60px rgba(0,0,0,0.3);overflow:hidden;">
        <div style="background:linear-gradient(135deg,#11998e,#38ef7d);padding:20px 24px;text-align:center;">
            <i class="fas fa-calendar-plus" style="font-size:36px;color:white;margin-bottom:8px;display:block;"></i>
            <h3 style="margin:0;color:white;font-size:18px;">¿Agendar próxima cita?</h3>
        </div>
        <div style="padding:24px;text-align:center;">
            <p style="color:#475569;font-size:15px;margin:0 0 6px;">
                ¿Habrá seguimiento en <strong>15 días</strong>?
            </p>
            <p style="color:#94a3b8;font-size:13px;margin:0 0 20px;">
                Se creará automáticamente una cita para el
                <strong id="fechaProximaLabel"></strong>.
            </p>
            <div style="display:flex;gap:10px;justify-content:center;">
                <button type="button" onclick="cerrarModalAgendar()" style="padding:10px 24px;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-size:14px;">
                    No, gracias
                </button>
                <button type="button" id="btnAgendarSi" onclick="agendarProximaCita()" style="padding:10px 24px;background:linear-gradient(135deg,#11998e,#38ef7d);color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-size:14px;">
                    <i class="fas fa-check"></i> Sí, agendar
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
(function() {
    var modal = document.getElementById('modalAgendar');
    if (modal) {
        var fechaSesion = '{{ session("fecha_sesion", "") }}';
        if (fechaSesion) {
            var partes = fechaSesion.split('-');
            var fecha = new Date(partes[0], partes[1] - 1, partes[2]);
            fecha.setDate(fecha.getDate() + 15);
            var dia = String(fecha.getDate()).padStart(2, '0');
            var mes = String(fecha.getMonth() + 1).padStart(2, '0');
            var anio = fecha.getFullYear();
            document.getElementById('fechaProximaLabel').textContent = dia + '/' + mes + '/' + anio;
        }
        modal.style.display = 'flex';
    }
})();

function cerrarModalAgendar() {
    document.getElementById('modalAgendar').style.display = 'none';
}

function agendarProximaCita() {
    var btn = document.getElementById('btnAgendarSi');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agendando...';

    fetch('{{ route("sesiones.agendar-proxima", $sesion->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            document.getElementById('modalAgendar').innerHTML =
                '<div style="background:white;border-radius:20px;padding:0;max-width:450px;width:95%;box-shadow:0 25px 60px rgba(0,0,0,0.3);overflow:hidden;">' +
                '<div style="background:linear-gradient(135deg,#11998e,#38ef7d);padding:20px 24px;text-align:center;">' +
                '<i class="fas fa-check-circle" style="font-size:36px;color:white;margin-bottom:8px;display:block;"></i>' +
                '<h3 style="margin:0;color:white;font-size:18px;">¡Cita agendada!</h3></div>' +
                '<div style="padding:24px;text-align:center;">' +
                '<p style="color:#475569;font-size:15px;">' + data.message + '</p>' +
                '<button onclick="cerrarModalAgendar()" style="margin-top:16px;padding:10px 24px;background:#11998e;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;">Aceptar</button>' +
                '</div></div>';
        } else {
            document.getElementById('modalAgendar').innerHTML =
                '<div style="background:white;border-radius:20px;padding:0;max-width:450px;width:95%;box-shadow:0 25px 60px rgba(0,0,0,0.3);overflow:hidden;">' +
                '<div style="background:linear-gradient(135deg,#f59e0b,#f97316);padding:20px 24px;text-align:center;">' +
                '<i class="fas fa-exclamation-triangle" style="font-size:36px;color:white;margin-bottom:8px;display:block;"></i>' +
                '<h3 style="margin:0;color:white;font-size:18px;">No se pudo agendar</h3></div>' +
                '<div style="padding:24px;text-align:center;">' +
                '<p style="color:#475569;font-size:15px;">' + data.message + '</p>' +
                '<button onclick="cerrarModalAgendar()" style="margin-top:16px;padding:10px 24px;background:#64748b;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;">Aceptar</button>' +
                '</div></div>';
        }
    })
    .catch(function() {
        alert('Error de conexión. Intente de nuevo.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> Sí, agendar';
    });
}
</script>
@endpush
