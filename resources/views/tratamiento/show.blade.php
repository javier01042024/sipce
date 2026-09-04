@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('tratamiento.index') }}" style="color: var(--sipce-primary); text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin: 0; color: #1e293b;">Detalle del Plan</h2>
    </div>

    <div style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); margin-bottom: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <div>
                <h3 style="margin: 0 0 8px 0; color: #1e293b; font-size: 22px;">{{ $plan->titulo }}</h3>
                <p style="margin: 0; color: #64748b; font-size: 14px;">
                    Paciente: <strong>{{ $plan->paciente->nombre_completo ?? 'N/A' }}</strong>
                </p>
            </div>
            <span class="sipce-badge sipce-badge-{{ $plan->estado_color }}">{{ $plan->estado_texto }}</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
            <div>
                <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Fecha Inicio</span>
                <p style="margin: 4px 0 0 0; color: #334155;">{{ $plan->fecha_inicio->format('d/m/Y') }}</p>
            </div>
            <div>
                <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Fecha Fin Estimada</span>
                <p style="margin: 4px 0 0 0; color: #334155;">{{ $plan->fecha_fin_estimada ? $plan->fecha_fin_estimada->format('d/m/Y') : 'No definida' }}</p>
            </div>
        </div>

        @if($plan->objetivo_general)
        <div style="margin-bottom: 20px;">
            <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Objetivo General</span>
            <p style="margin: 4px 0 0 0; color: #334155;">{{ $plan->objetivo_general }}</p>
        </div>
        @endif

        @if($plan->observaciones)
        <div style="margin-bottom: 20px;">
            <span style="font-size: 12px; color: #94a3b8; text-transform: uppercase; font-weight: 600;">Observaciones</span>
            <p style="margin: 4px 0 0 0; color: #334155;">{{ $plan->observaciones }}</p>
        </div>
        @endif

        <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            <a href="{{ route('tratamiento.edit', $plan) }}" style="padding:10px 20px;background:linear-gradient(135deg,#6366f1,#818cf8);color:white;border:none;border-radius:10px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form method="POST" action="{{ route('tratamiento.destroy', $plan) }}" style="display:inline;" id="formDeletePlan">
                @csrf
                @method('DELETE')
                <button type="button" style="padding:10px 20px;background:linear-gradient(135deg,#ef4444,#f87171);color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;"
                    onclick="SIPCE_ALERT.confirmDelete({title:'Â¿Eliminar este plan?',html:'Se eliminarÃ¡n todos los objetivos asociados.'}).then(r=>{if(r.isConfirmed)document.getElementById('formDeletePlan').submit()})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </form>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div><h3><i class="fas fa-bullseye"></i> Objetivos del Plan</h3></div>
            <button onclick="document.getElementById('formNuevoObjetivo').style.display = document.getElementById('formNuevoObjetivo').style.display === 'none' ? 'block' : 'none'" style="padding:8px 16px;background:rgba(255,255,255,0.15);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:8px;cursor:pointer;font-weight:600;font-size:13px;">
                <i class="fas fa-plus"></i> Nuevo Objetivo
            </button>
        </div>

        <div id="formNuevoObjetivo" style="display:none; padding:20px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
            <form method="POST" action="{{ route('tratamiento.objetivo.store', $plan) }}">
                @csrf
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; align-items: end;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px; font-size: 13px;">DescripciÃ³n del Objetivo *</label>
                        <input type="text" name="descripcion" required style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
                    </div>
                    <div>
                        <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,var(--sipce-primary),var(--sipce-primary-dark));color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;width:100%;">
                            Agregar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DescripciÃ³n</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plan->objetivos as $idx => $objetivo)
                    <tr>
                        <td><span class="sipce-cell-bold">{{ $idx + 1 }}</span></td>
                        <td><span class="sipce-cell-main">{{ $objetivo->descripcion }}</span></td>
                        <td>
                            @if($objetivo->estado === 'cumplido')
                                <span class="sipce-badge sipce-badge-success"><i class="fas fa-check-circle"></i> Cumplido</span>
                            @elseif($objetivo->estado === 'en_progreso')
                                <span class="sipce-badge sipce-badge-info"><i class="fas fa-spinner"></i> En Progreso</span>
                            @else
                                <span class="sipce-badge sipce-badge-warning"><i class="fas fa-clock"></i> Pendiente</span>
                            @endif
                        </td>
                        <td>
                            <div class="sipce-actions">
                                @if($objetivo->estado !== 'cumplido')
                                <form method="POST" action="{{ route('tratamiento.objetivo.estado', $objetivo) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="{{ $objetivo->estado === 'pendiente' ? 'en_progreso' : 'cumplido' }}">
                                    <button type="submit" class="sipce-btn-icon sipce-btn-restore" title="{{ $objetivo->estado === 'pendiente' ? 'Iniciar' : 'Marcar cumplido' }}">
                                        <i class="fas fa-{{ $objetivo->estado === 'pendiente' ? 'play' : 'check' }}"></i>
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('tratamiento.objetivo.destroy', $objetivo) }}" style="display:inline;" id="formDeleteObj{{ $objetivo->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="sipce-btn-icon sipce-btn-delete" title="Eliminar"
                                        onclick="SIPCE_ALERT.confirmDelete({title:'Â¿Eliminar este objetivo?',html:'Esta acciÃ³n no se puede deshacer.'}).then(r=>{if(r.isConfirmed)document.getElementById('formDeleteObj{{ $objetivo->id }}').submit()})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4"><div class="sipce-empty"><i class="fas fa-bullseye sipce-empty-icon"></i><p class="sipce-empty-title">No hay objetivos registrados</p><p class="sipce-empty-text">Agrega objetivos al plan de tratamiento</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
