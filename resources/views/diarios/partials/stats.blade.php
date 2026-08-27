{{-- diarios/partials/stats.blade.php --}}
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon total">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <h4>Total registros</h4>
            <div class="number">{{ $diarios->count() }}</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon hoy">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-info">
            <h4>Registros hoy</h4>
            <div class="number">
                {{ $diarios->where('fecha', date('Y-m-d'))->count() }}
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon semana">
            <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-info">
            <h4>Esta semana</h4>
            <div class="number">
                {{ $diarios->where('fecha', '>=', date('Y-m-d', strtotime('-7 days')))->count() }}
            </div>
        </div>
    </div>
</div>
