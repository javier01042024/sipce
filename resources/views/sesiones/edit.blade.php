@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('sesiones.show', $sesion) }}" style="color: #667eea; text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin: 0; color: #1e293b;">Editar Sesión</h2>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <form method="POST" action="{{ route('sesiones.update', $sesion) }}">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Paciente</label>
                    <input type="text" value="{{ $sesion->paciente->numero_expediente ?? '' }} - {{ $sesion->paciente->detalle->nombre ?? '' }} {{ $sesion->paciente->detalle->apellido ?? '' }}" disabled style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;background:#f8fafc;color:#64748b;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Fecha *</label>
                    <input type="date" name="fecha" value="{{ old('fecha', $sesion->fecha->format('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('fecha') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">
                    @if($errors->has('fecha'))
                    <small style="color:#dc2626;font-size:12px;">{{ $errors->first('fecha') }}</small>
                    @endif
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Duración (minutos)</label>
                <input type="number" name="duracion_minutos" value="{{ old('duracion_minutos', $sesion->duracion_minutos) }}" min="1" style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('duracion_minutos') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Resumen *</label>
                <textarea name="resumen" rows="4" required placeholder="Resumen general de la sesión (mínimo 5 caracteres)..." style="width:100%;padding:10px 14px;border:2px solid {{ $errors->has('resumen') ? '#ef4444' : '#e2e8f0' }};border-radius:10px;font-size:14px;">{{ old('resumen', $sesion->resumen) }}</textarea>
                @if($errors->has('resumen'))
                <small style="color:#dc2626;font-size:12px;">{{ $errors->first('resumen') }}</small>
                @endif
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Observaciones Clínicas</label>
                <textarea name="observaciones_clinicas" rows="3" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">{{ old('observaciones_clinicas', $sesion->observaciones_clinicas) }}</textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('sesiones.show', $sesion) }}" style="padding:10px 24px;background:#f1f5f9;color:#64748b;border-radius:10px;text-decoration:none;font-weight:600;">Cancelar</a>
                <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
