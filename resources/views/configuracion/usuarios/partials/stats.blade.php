<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h4>Total Usuarios</h4>
            <div class="number">{{ $totalUsers }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon admin">
            <i class="fas fa-crown"></i>
        </div>
        <div class="stat-info">
            <h4>Administradores</h4>
            <div class="number">{{ $adminUsers }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon activo">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h4>Usuarios Activos</h4>
            <div class="number">{{ $activeUsers }}</div>
        </div>
    </div>
</div>