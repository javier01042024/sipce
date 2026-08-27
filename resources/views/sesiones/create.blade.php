@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('sesiones.index') }}" style="color: #667eea; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin: 0; color: #1e293b;">Nueva Sesión</h2>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <form method="POST" action="{{ route('sesiones.store') }}">
            @csrf

            @if(isset($cita) && $cita)
            <input type="hidden" name="cita_id" value="{{ $cita->id }}">
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Paciente *</label>
                    <select name="paciente_id" id="pacienteSelect" required onchange="cargarAcompanantes()" style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('paciente_id') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">
                        <option value="">Seleccionar paciente</option>
                        @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" data-tipo="{{ $p->tipo_paciente }}" data-acompanantes="{{ $p->acompanantes->toJson() }}" {{ old('paciente_id', $pacienteId ?? '') == $p->id ? 'selected' : '' }}>{{ $p->numero_expediente }} - {{ $p->nombre_completo }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('paciente_id'))
                    <small style="color:#dc2626;font-size:12px;">{{ $errors->first('paciente_id') }}</small>
                    @endif
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Fecha *</label>
                    <input type="date" name="fecha" value="{{ old('fecha', isset($cita) && $cita ? $cita->fecha->format('Y-m-d') : date('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('fecha') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">
                    @if($errors->has('fecha'))
                    <small style="color:#dc2626;font-size:12px;">{{ $errors->first('fecha') }}</small>
                    @endif
                </div>
            </div>

            {{-- ACOMPAÑANTE (solo para adolescente/niño) --}}
            <div id="acompananteSection" style="display: none; margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">¿Quién lo acompañó?</label>
                <select name="acompanante_id" id="acompananteSelect" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
                    <option value="">Sin acompañante</option>
                </select>
                <small style="color: #64748b; font-size: 11px;">Solo aplica para pacientes adolescentes y niños</small>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Duración (minutos)</label>
                <input type="number" name="duracion_minutos" value="{{ old('duracion_minutos', 50) }}" min="1" style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('duracion_minutos') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">
                @if($errors->has('duracion_minutos'))
                <small style="color:#dc2626;font-size:12px;">{{ $errors->first('duracion_minutos') }}</small>
                @endif
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Resumen *</label>
                <textarea name="resumen" rows="4" required placeholder="Resumen general de la sesión (mínimo 5 caracteres)..." style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('resumen') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">{{ old('resumen') }}</textarea>
                @if($errors->has('resumen'))
                <small style="color:#dc2626;font-size:12px;">{{ $errors->first('resumen') }}</small>
                @endif
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Observaciones Clínicas</label>
                <textarea name="observaciones_clinicas" rows="3" placeholder="Observaciones clínicas relevantes..." style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">{{ old('observaciones_clinicas') }}</textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('sesiones.index') }}" style="padding:10px 24px;background:#f1f5f9;color:#64748b;border-radius:10px;text-decoration:none;font-weight:600;">Cancelar</a>
                <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;">Crear Sesión</button>
            </div>
        </form>
    </div>
</div>

<script>
function cargarAcompanantes() {
    const select = document.getElementById('pacienteSelect');
    const acomSection = document.getElementById('acompananteSection');
    const acomSelect = document.getElementById('acompananteSelect');
    const selected = select.options[select.selectedIndex];
    const tipo = selected.dataset.tipo || '';
    
    if (tipo === 'adolescente' || tipo === 'niño') {
        acomSection.style.display = 'block';
        acomSelect.innerHTML = '<option value="">Sin acompañante</option>';
        try {
            const acompanantes = JSON.parse(selected.dataset.acompanantes || '[]');
            acompanantes.forEach(function(a) {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.nombre + ' (' + a.parentesco + ')';
                acomSelect.appendChild(opt);
            });
        } catch(e) {}
    } else {
        acomSection.style.display = 'none';
        acomSelect.innerHTML = '';
    }
}
document.addEventListener('DOMContentLoaded', cargarAcompanantes);
</script>
@endsection
