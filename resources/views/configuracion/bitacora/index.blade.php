@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.bitacora-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER COLORIDO */
.page-header {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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

.btn-export {
    background: white;
    color: #4facfe;
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

.btn-export:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #0099cc;
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
    box-shadow: 0 5px 20px rgba(79, 172, 254, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(79, 172, 254, 0.15);
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
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-icon.hoy {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-icon.error {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
}

.stat-icon.login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

/* FILTROS */
.filtros-card {
    background: white;
    border-radius: 18px;
    padding: 20px 25px;
    box-shadow: 0 15px 40px rgba(79, 172, 254, 0.12);
    border: 1px solid rgba(79, 172, 254, 0.08);
    margin-bottom: 30px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filtro-group {
    flex: 1;
    min-width: 180px;
}

.filtro-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filtro-select, .filtro-input {
    width: 100%;
    padding: 10px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #334155;
    background: white;
    transition: all 0.3s;
}

.filtro-select:focus, .filtro-input:focus {
    border-color: #4facfe;
    outline: none;
    box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
}

.btn-filtrar {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-filtrar:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
}

.btn-limpiar {
    background: #f1f5f9;
    color: #64748b;
    border: none;
    padding: 10px 25px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-limpiar:hover {
    background: #e2e8f0;
}

/* TABLA */
.card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(79, 172, 254, 0.12);
    border: 1px solid rgba(79, 172, 254, 0.08);
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

/* ICONOS DE ACCIÓN */
.action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 12px;
}

.action-icon.create {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.action-icon.update {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.action-icon.delete {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
}

.action-icon.login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.action-icon.view {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.badge-accion {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-accion.success {
    background: #d1fae5;
    color: #065f46;
}

.badge-accion.error {
    background: #fee2e2;
    color: #991b1b;
}

.badge-accion.warning {
    background: #fef3c7;
    color: #92400e;
}

/* PAGINACIÓN */
.pagination {
    display: flex;
    justify-content: flex-end;
    padding: 20px 30px;
    gap: 8px;
}

.page-item {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #f1f5f9;
    color: #64748b;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 600;
    font-size: 14px;
}

.page-item:hover {
    background: #e2e8f0;
}

.page-item.active {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
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
    
    .filtros-card {
        flex-direction: column;
    }
    
    .filtro-group {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="bitacora-wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <h1>
                <i class="fas fa-history me-2"></i>
                Bitácora del Sistema
            </h1>
            <p>Registro de actividades y eventos del sistema SIPCE</p>
        </div>

        <button class="btn-export" onclick="exportarBitacora()">
            <i class="fas fa-download"></i>
            Exportar Bitácora
        </button>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-info">
                <h4>Total Registros</h4>
                <div class="number">1,247</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon hoy">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h4>Actividad Hoy</h4>
                <div class="number">43</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon error">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h4>Errores</h4>
                <div class="number">3</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon login">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <div class="stat-info">
                <h4>Inicios de Sesión</h4>
                <div class="number">28</div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filtros-card">
        <div class="filtro-group">
            <label><i class="fas fa-user me-1"></i> Usuario</label>
            <select class="filtro-select">
                <option value="">Todos los usuarios</option>
                <option value="1">Dra. María González</option>
                <option value="2">Dr. Juan Pérez</option>
                <option value="3">Ana López</option>
            </select>
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-tasks me-1"></i> Acción</label>
            <select class="filtro-select">
                <option value="">Todas las acciones</option>
                <option value="login">Inicio de sesión</option>
                <option value="create">Creación</option>
                <option value="update">Actualización</option>
                <option value="delete">Eliminación</option>
            </select>
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-calendar me-1"></i> Desde</label>
            <input type="date" class="filtro-input" value="2026-04-01">
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-calendar me-1"></i> Hasta</label>
            <input type="date" class="filtro-input" value="2026-04-21">
        </div>

        <button class="btn-filtrar">
            <i class="fas fa-filter me-1"></i>
            Filtrar
        </button>

        <button class="btn-limpiar">
            <i class="fas fa-times me-1"></i>
            Limpiar
        </button>
    </div>

    <!-- TABLA DE BITÁCORA -->
    <div class="card">
        <div class="card-header-custom">
            <h3>
                <i class="fas fa-clock"></i>
                Registro de Actividades
            </h3>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Usuario</th>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>Fecha y Hora</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon login">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <span>Login</span>
                            </div>
                        </td>
                        <td>
                            <strong>Dra. María González</strong><br>
                            <small style="color: #64748b;">maria.gonzalez@sipce.com</small>
                        </td>
                        <td>Autenticación</td>
                        <td>Inicio de sesión exitoso</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            21/04/2026 08:30
                        </td>
                        <td>
                            <span class="badge-accion success">
                                <i class="fas fa-check-circle me-1"></i>
                                Éxito
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon create">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span>Crear</span>
                            </div>
                        </td>
                        <td>
                            <strong>Dr. Juan Pérez</strong><br>
                            <small style="color: #64748b;">juan.perez@sipce.com</small>
                        </td>
                        <td>Pacientes</td>
                        <td>Creó nuevo paciente: Carlos Ruiz</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            21/04/2026 09:15
                        </td>
                        <td>
                            <span class="badge-accion success">
                                <i class="fas fa-check-circle me-1"></i>
                                Éxito
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon update">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <span>Actualizar</span>
                            </div>
                        </td>
                        <td>
                            <strong>Ana López</strong><br>
                            <small style="color: #64748b;">ana.lopez@sipce.com</small>
                        </td>
                        <td>Citas</td>
                        <td>Actualizó cita #1245 para María González</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            21/04/2026 10:00
                        </td>
                        <td>
                            <span class="badge-accion success">
                                <i class="fas fa-check-circle me-1"></i>
                                Éxito
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon view">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <span>Consultar</span>
                            </div>
                        </td>
                        <td>
                            <strong>Dra. María González</strong><br>
                            <small style="color: #64748b;">maria.gonzalez@sipce.com</small>
                        </td>
                        <td>Diario</td>
                        <td>Consultó registro diario de Juan Pérez</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            21/04/2026 11:20
                        </td>
                        <td>
                            <span class="badge-accion success">
                                <i class="fas fa-check-circle me-1"></i>
                                Éxito
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon delete">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <span>Eliminar</span>
                            </div>
                        </td>
                        <td>
                            <strong>Dr. Carlos Martínez</strong><br>
                            <small style="color: #64748b;">carlos.martinez@sipce.com</small>
                        </td>
                        <td>Pacientes</td>
                        <td>Intentó eliminar paciente con citas activas</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            20/04/2026 16:45
                        </td>
                        <td>
                            <span class="badge-accion error">
                                <i class="fas fa-times-circle me-1"></i>
                                Error
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon login">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <span>Logout</span>
                            </div>
                        </td>
                        <td>
                            <strong>Dr. Juan Pérez</strong><br>
                            <small style="color: #64748b;">juan.perez@sipce.com</small>
                        </td>
                        <td>Autenticación</td>
                        <td>Cierre de sesión</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            20/04/2026 18:00
                        </td>
                        <td>
                            <span class="badge-accion success">
                                <i class="fas fa-check-circle me-1"></i>
                                Éxito
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                <div class="action-icon create">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <span>Crear</span>
                            </div>
                        </td>
                        <td>
                            <strong>Sistema</strong><br>
                            <small style="color: #64748b;">sistema@sipce.com</small>
                        </td>
                        <td>Respaldos</td>
                        <td>Respaldo automático completado</td>
                        <td>
                            <i class="far fa-calendar-alt me-1"></i>
                            20/04/2026 03:00
                        </td>
                        <td>
                            <span class="badge-accion success">
                                <i class="fas fa-check-circle me-1"></i>
                                Éxito
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div class="pagination">
            <a href="#" class="page-item">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="#" class="page-item active">1</a>
            <a href="#" class="page-item">2</a>
            <a href="#" class="page-item">3</a>
            <a href="#" class="page-item">4</a>
            <a href="#" class="page-item">5</a>
            <a href="#" class="page-item">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
function exportarBitacora() {
    alert('Exportando bitácora a CSV...');
}

// Limpiar filtros
document.querySelector('.btn-limpiar').addEventListener('click', function() {
    document.querySelectorAll('.filtro-select, .filtro-input').forEach(input => {
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    });
});

// Aplicar filtros
document.querySelector('.btn-filtrar').addEventListener('click', function() {
    alert('Aplicando filtros...');
});
</script>

@endsection