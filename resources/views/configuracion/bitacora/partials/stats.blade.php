<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-list"></i>
        </div>
        <div class="stat-info">
            <h4>Total Registros</h4>
            <div class="number">{{ number_format($totalRegistros) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon hoy">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-info">
            <h4>Actividad Hoy</h4>
            <div class="number">{{ number_format($registrosHoy) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon error">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <h4>Eliminaciones/Errores</h4>
            <div class="number">{{ number_format($errores) }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon login">
            <i class="fas fa-sign-in-alt"></i>
        </div>
        <div class="stat-info">
            <h4>Inicios de Sesión</h4>
            <div class="number">{{ number_format($iniciosSesion) }}</div>
        </div>
    </div>
</div>