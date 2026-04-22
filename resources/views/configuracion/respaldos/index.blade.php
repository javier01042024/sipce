@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.respaldos-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER COLORIDO */
.page-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    padding: 30px 35px;
    border-radius: 14px;
    margin-bottom: 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h1 {
    font-weight: 700;
    color: white;
    margin: 0;
    font-size: 28px;
}

.header-content p {
    color: rgba(255, 255, 255, 0.9);
    margin: 8px 0 0 0;
    font-size: 15px;
}

.btn-backup {
    background: white;
    color: #11998e;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.btn-backup:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #0d7a6e;
}

/* ESTADÍSTICAS */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(17, 153, 142, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.15);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.total {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-icon.size {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.auto {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.stat-icon.last {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-info h4 {
    margin: 0 0 5px 0;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.stat-info .number {
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

/* CONFIGURACIÓN */
.config-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 15px 40px rgba(17, 153, 142, 0.12);
    border: 1px solid rgba(17, 153, 142, 0.08);
    margin-bottom: 30px;
}

.config-title {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.config-title i {
    color: #11998e;
}

.config-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.config-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
}

.config-label {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #334155;
}

.config-label i {
    color: #11998e;
}

/* TOGGLE SWITCH */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

input:checked + .slider {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

input:checked + .slider:before {
    transform: translateX(24px);
}

/* TABLA */
.card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(17, 153, 142, 0.12);
    border: 1px solid rgba(17, 153, 142, 0.08);
}

.card-header-custom {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: white;
    padding: 20px 30px;
}

.card-header-custom h3 {
    margin: 0;
    font-weight: 600;
    font-size: 18px;
}

.card-header-custom h3 i {
    margin-right: 10px;
    color: #a8edea;
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

th {
    padding: 16px;
    text-align: left;
    font-weight: 600;
    color: #475569;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 14px;
}

tbody tr {
    transition: all 0.2s;
}

tbody tr:hover {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
}

.badge-tipo {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-tipo.automatico {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.badge-tipo.manual {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-icon.restore {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.btn-icon.download {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.btn-icon.delete {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
}

.btn-icon:hover {
    transform: translateY(-2px);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .config-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="respaldos-wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <h1>
                <i class="fas fa-database me-2"></i>
                Respaldos del Sistema
            </h1>
            <p>Gestión de copias de seguridad y restauración de datos</p>
        </div>

        <button class="btn-backup" onclick="createBackup()">
            <i class="fas fa-plus-circle"></i>
            Crear Respaldo
        </button>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-archive"></i>
            </div>
            <div class="stat-info">
                <h4>Total Respaldos</h4>
                <div class="number">24</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon size">
                <i class="fas fa-hdd"></i>
            </div>
            <div class="stat-info">
                <h4>Tamaño Total</h4>
                <div class="number">2.4 GB</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon auto">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h4>Automáticos</h4>
                <div class="number">18</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon last">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h4>Último Respaldo</h4>
                <div class="number">Hoy</div>
            </div>
        </div>
    </div>

    <!-- CONFIGURACIÓN -->
    <div class="config-card">
        <div class="config-title">
            <i class="fas fa-sliders-h"></i>
            Configuración de Respaldos
        </div>
        
        <div class="config-grid">
            <div class="config-item">
                <span class="config-label">
                    <i class="fas fa-clock"></i>
                    Respaldo automático diario
                </span>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="config-item">
                <span class="config-label">
                    <i class="fas fa-calendar-week"></i>
                    Respaldo semanal completo
                </span>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="config-item">
                <span class="config-label">
                    <i class="fas fa-envelope"></i>
                    Notificar por correo
                </span>
                <label class="switch">
                    <input type="checkbox">
                    <span class="slider"></span>
                </label>
            </div>
            
            <div class="config-item">
                <span class="config-label">
                    <i class="fas fa-compress"></i>
                    Comprimir respaldos
                </span>
                <label class="switch">
                    <input type="checkbox" checked>
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- TABLA DE RESPALDOS -->
    <div class="card">
        <div class="card-header-custom">
            <h3>
                <i class="fas fa-history"></i>
                Historial de Respaldos
            </h3>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tipo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <i class="fas fa-file-archive me-2" style="color: #11998e;"></i>
                            backup_20260421_0300.zip
                        </td>
                        <td>
                            <span class="badge-tipo automatico">
                                <i class="fas fa-robot me-1"></i>
                                Automático
                            </span>
                        </td>
                        <td>156 MB</td>
                        <td>
                            <i class="far fa-calendar-alt me-2"></i>
                            21/04/2026 03:00
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon restore" title="Restaurar">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <button class="btn-icon download" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <i class="fas fa-file-archive me-2" style="color: #667eea;"></i>
                            backup_manual_20260420_1430.zip
                        </td>
                        <td>
                            <span class="badge-tipo manual">
                                <i class="fas fa-user me-1"></i>
                                Manual
                            </span>
                        </td>
                        <td>152 MB</td>
                        <td>
                            <i class="far fa-calendar-alt me-2"></i>
                            20/04/2026 14:30
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon restore" title="Restaurar">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <button class="btn-icon download" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <i class="fas fa-file-archive me-2" style="color: #11998e;"></i>
                            backup_20260420_0300.zip
                        </td>
                        <td>
                            <span class="badge-tipo automatico">
                                <i class="fas fa-robot me-1"></i>
                                Automático
                            </span>
                        </td>
                        <td>150 MB</td>
                        <td>
                            <i class="far fa-calendar-alt me-2"></i>
                            20/04/2026 03:00
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon restore" title="Restaurar">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <button class="btn-icon download" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <i class="fas fa-file-archive me-2" style="color: #11998e;"></i>
                            backup_20260419_0300.zip
                        </td>
                        <td>
                            <span class="badge-tipo automatico">
                                <i class="fas fa-robot me-1"></i>
                                Automático
                            </span>
                        </td>
                        <td>148 MB</td>
                        <td>
                            <i class="far fa-calendar-alt me-2"></i>
                            19/04/2026 03:00
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon restore" title="Restaurar">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <button class="btn-icon download" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <i class="fas fa-file-archive me-2" style="color: #667eea;"></i>
                            backup_semanal_20260418.zip
                        </td>
                        <td>
                            <span class="badge-tipo manual">
                                <i class="fas fa-user me-1"></i>
                                Manual
                            </span>
                        </td>
                        <td>155 MB</td>
                        <td>
                            <i class="far fa-calendar-alt me-2"></i>
                            18/04/2026 10:15
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon restore" title="Restaurar">
                                    <i class="fas fa-undo-alt"></i>
                                </button>
                                <button class="btn-icon download" title="Descargar">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
function createBackup() {
    if (confirm('¿Crear un nuevo respaldo del sistema ahora?')) {
        alert('Creando respaldo... Esta operación puede tomar unos minutos.');
    }
}

// Confirmación para restaurar
document.querySelectorAll('.btn-icon.restore').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('¿Estás seguro de restaurar este respaldo? Los datos actuales serán reemplazados.')) {
            alert('Restaurando respaldo...');
        }
    });
});

// Confirmación para eliminar
document.querySelectorAll('.btn-icon.delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm('¿Eliminar este respaldo permanentemente?')) {
            alert('Respaldo eliminado.');
        }
    });
});
</script>

@endsection