@extends('layouts.paciente')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('paciente.mi-diario') }}" style="color: var(--sipce-primary); text-decoration: none;"><i class="fas fa-arrow-left"></i> Volver</a>
        <h2 style="margin: 0; color: #1e293b;">Entrada del Diario</h2>
    </div>

    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <span class="sipce-badge sipce-badge-primary">{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}</span>
            <div class="sipce-actions">
                <form method="POST" action="{{ route('paciente.mi-diario.destroy', $diario) }}" style="display:inline;" id="formDeleteDiario">
                    @csrf @method('DELETE')
                    <button type="button" class="sipce-btn-icon sipce-btn-delete" title="Eliminar"
                        onclick="SIPCE_ALERT.confirmDelete({title:'¿Eliminar esta entrada?',html:'Esta acción no se puede deshacer.'}).then(r=>{if(r.isConfirmed)document.getElementById('formDeleteDiario').submit()})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
        <div style="color: #334155; line-height: 1.8; white-space: pre-wrap;">{{ $diario->contenido }}</div>
    </div>
</div>
@endsection
