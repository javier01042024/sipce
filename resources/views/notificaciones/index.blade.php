@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-bell me-2"></i> Notificaciones</h1>
            <p>Centro de notificaciones del sistema</p>
        </div>
        <div class="header-buttons">
            <form method="POST" action="{{ route('notificaciones.leer-todas') }}" style="display:inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-nuevo">
                    <i class="fas fa-check-double"></i> Marcar todas como leídas
                </button>
            </form>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div><h3><i class="fas fa-list"></i> Notificaciones</h3>
                <p>{{ $notificaciones->total() }} notificaciones</p>
            </div>
        </div>

        @forelse($notificaciones as $notificacion)
        <div style="padding:16px 24px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:16px; {{ !$notificacion->leida ? 'background:#f0f9ff;' : '' }}">
            <div style="width:40px; height:40px; border-radius:10px; background:{{ !$notificacion->leida ? 'linear-gradient(135deg,#667eea,#764ba2)' : '#f1f5f9' }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="fas fa-bell" style="color:{{ !$notificacion->leida ? 'white' : '#94a3b8' }}; font-size:14px;"></i>
            </div>
            <div style="flex:1; min-width:0;">
                <p style="margin:0; color:#1e293b; font-weight:{{ !$notificacion->leida ? '600' : '400' }}; font-size:14px;">{{ $notificacion->mensaje }}</p>
                <span style="font-size:12px; color:#94a3b8;">{{ $notificacion->created_at->diffForHumans() }}</span>
            </div>
            <div class="sipce-actions">
                @if(!$notificacion->leida)
                <form method="POST" action="{{ route('notificaciones.marcar-leida', $notificacion) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="sipce-btn-icon sipce-btn-view" title="Marcar como leída">
                        <i class="fas fa-check"></i>
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('notificaciones.eliminar', $notificacion) }}" style="display:inline;" id="formDeleteNotif{{ $notificacion->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="sipce-btn-icon sipce-btn-delete" title="Eliminar"
                        onclick="SIPCE_ALERT.confirmDelete({title:'¿Eliminar esta notificación?'}).then(r=>{if(r.isConfirmed)document.getElementById('formDeleteNotif{{ $notificacion->id }}').submit()})">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="sipce-empty">
            <i class="fas fa-bell-slash sipce-empty-icon"></i>
            <p class="sipce-empty-title">No tienes notificaciones</p>
            <p class="sipce-empty-text">Estás al día con todas tus notificaciones</p>
        </div>
        @endforelse

        <div class="sipce-pagination">{{ $notificaciones->links() }}</div>
    </div>
</div>
@endsection
