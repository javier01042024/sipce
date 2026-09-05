@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('tratamiento.show', $plan) }}" style="color: var(--sipce-primary); text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin: 0; color: #1e293b;">Editar Plan de Tratamiento</h2>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <form method="POST" action="{{ route('tratamiento.update', $plan) }}">
            @csrf
            @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Paciente *</label>
                    <select name="paciente_id" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
                        <option value="">Seleccionar paciente</option>
                        @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" {{ old('paciente_id', $plan->paciente_id) == $p->id ? 'selected' : '' }}>{{ $p->numero_expediente }} - {{ $p->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Estado</label>
                    <select name="estado" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
                        <option value="activo" {{ old('estado', $plan->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="pausado" {{ old('estado', $plan->estado) === 'pausado' ? 'selected' : '' }}>Pausado</option>
                        <option value="completado" {{ old('estado', $plan->estado) === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ old('estado', $plan->estado) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Título *</label>
                <input type="text" name="titulo" value="{{ old('titulo', $plan->titulo) }}" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Objetivo General</label>
                <textarea name="objetivo_general" rows="3" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">{{ old('objetivo_general', $plan->objetivo_general) }}</textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Fecha Inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $plan->fecha_inicio->format('Y-m-d')) }}" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Fecha Fin Estimada</label>
                    <input type="date" name="fecha_fin_estimada" value="{{ old('fecha_fin_estimada', $plan->fecha_fin_estimada ? $plan->fecha_fin_estimada->format('Y-m-d') : '') }}" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
                </div>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Observaciones</label>
                <textarea name="observaciones" rows="2" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">{{ old('observaciones', $plan->observaciones) }}</textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <a href="{{ route('tratamiento.show', $plan) }}" style="padding:10px 24px;background:#f1f5f9;color:#64748b;border-radius:10px;text-decoration:none;font-weight:600;">Cancelar</a>
                <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,var(--sipce-primary),var(--sipce-primary-dark));color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
