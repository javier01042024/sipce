<div class="citas-header d-flex justify-content-between align-items-center flex-wrap">
    <h3>
        <i class="fas fa-calendar-alt me-2"></i>
        Gestión de Citas
    </h3>
    <div style="display: flex; gap: 10px; align-items: center;">
        <div class="view-toggle" style="display: flex; background: #f1f5f9; border-radius: 10px; padding: 3px;">
            <button type="button" id="btnVistaCards" class="toggle-btn active" onclick="toggleVistaCitas('cards')">
                <i class="fas fa-th-large"></i> Tarjetas
            </button>
            <button type="button" id="btnVistaCalendario" class="toggle-btn" onclick="toggleVistaCitas('calendario')">
                <i class="fas fa-calendar"></i> Calendario
            </button>
        </div>
        <a href="{{ route('citas.create') }}" class="btn-nueva-cita">
            <i class="fas fa-plus-circle"></i>
            Nueva Cita
        </a>
    </div>
</div>

<style>
    .view-toggle .toggle-btn {
        padding: 8px 16px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 8px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .view-toggle .toggle-btn:hover { color: #334155; }
    .view-toggle .toggle-btn.active {
        background: white;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
</style>
