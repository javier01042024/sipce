@extends('layouts.paciente')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="page-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <div class="header-content">
            <h1><i class="fas fa-book-open me-2"></i> Mi Diario</h1>
            <p>Escribe sobre tus experiencias y sentimientos</p>
        </div>
        <div class="header-buttons">
            <button class="btn-nuevo" onclick="document.getElementById('nuevaEntrada').style.display='block'">
                <i class="fas fa-plus-circle"></i> Nueva Entrada
            </button>
        </div>
    </div>

    <div id="nuevaEntrada" style="display:{{ $errors->any() ? 'block' : 'none' }}; background: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="margin: 0 0 16px 0; color: #1e293b;">Nueva Entrada del Diario</h3>
        <form method="POST" action="{{ route('paciente.mi-diario.store') }}">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Fecha</label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">¿Qué sientes hoy?</label>
                <textarea name="contenido" rows="5" required
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px; resize: vertical;">{{ old('contenido') }}</textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="padding: 10px 24px; background: linear-gradient(135deg, #11998e, #38ef7d); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Guardar</button>
                <button type="button" onclick="document.getElementById('nuevaEntrada').style.display='none'" style="padding: 10px 24px; background: #f1f5f9; color: #64748b; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Cancelar</button>
            </div>
        </form>
    </div>

    <div class="sipce-table-card">
        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diarios as $diario)
                    <tr>
                        <td><span class="sipce-cell-bold">{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}</span></td>
                        <td>{{ Str::limit($diario->contenido, 80) }}</td>
                        <td>
                            <div class="sipce-actions">
                                <a href="{{ route('paciente.mi-diario.show', $diario) }}" class="sipce-btn-icon sipce-btn-view" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" action="{{ route('paciente.mi-diario.destroy', $diario) }}" style="display:inline;" id="formDeleteDiario{{ $diario->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" class="sipce-btn-icon sipce-btn-delete" title="Eliminar"
                                        onclick="SIPCE_ALERT.confirmDelete({title:'¿Eliminar esta entrada?',html:'Esta acción no se puede deshacer.'}).then(r=>{if(r.isConfirmed)document.getElementById('formDeleteDiario{{ $diario->id }}').submit()})">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="sipce-empty">
                                <i class="fas fa-book-open sipce-empty-icon"></i>
                                <p class="sipce-empty-title">No tienes entradas en tu diario</p>
                                <p class="sipce-empty-text">Comienza a escribir sobre tus experiencias</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
