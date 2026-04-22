@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.usuarios-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER COLORIDO */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.btn-nuevo {
    background: white;
    color: #667eea;
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

.btn-nuevo:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #764ba2;
}

/* ESTADÍSTICAS */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.admin {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.stat-icon.activo {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-info h4 {
    margin: 0 0 5px 0;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.stat-info .number {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
}

/* TABLA */
.card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
    border: 1px solid rgba(102, 126, 234, 0.08);
}

.card-header-custom {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: white;
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-input {
    padding: 10px 15px;
    border: none;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 14px;
    width: 250px;
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.search-input:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.15);
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

/* AVATAR */
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
    margin-right: 12px;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.user-name {
    font-weight: 600;
    color: #1e293b;
}

.user-email {
    font-size: 12px;
    color: #64748b;
}

/* BADGES */
.badge-rol {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.badge-rol.admin {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
    color: white;
}

.badge-rol.medico {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge-rol.secretaria {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.badge-estado {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-estado.activo {
    background: #d1fae5;
    color: #065f46;
}

.badge-estado.inactivo {
    background: #fee2e2;
    color: #991b1b;
}

/* ACCIONES */
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

.btn-icon.edit {
    background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
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
        grid-template-columns: 1fr;
    }
    
    .card-header-custom {
        flex-direction: column;
        gap: 15px;
    }
    
    .search-input {
        width: 100%;
    }
    
    .user-info {
        flex-direction: column;
        text-align: center;
    }
    
    .user-avatar {
        margin: 0 0 10px 0;
    }
}
</style>

<div class="usuarios-wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-content">
            <h1>
                <i class="fas fa-user-shield me-2"></i>
                Usuarios y Roles
            </h1>
            <p>Gestión de usuarios, permisos y roles del sistema</p>
        </div>

        <button class="btn-nuevo" onclick="openModal()">
            <i class="fas fa-plus-circle"></i>
            Nuevo Usuario
        </button>
    </div>

    <!-- ESTADÍSTICAS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h4>Total Usuarios</h4>
                <div class="number">12</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon admin">
                <i class="fas fa-crown"></i>
            </div>
            <div class="stat-info">
                <h4>Administradores</h4>
                <div class="number">3</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon activo">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h4>Usuarios Activos</h4>
                <div class="number">10</div>
            </div>
        </div>
    </div>

    <!-- TABLA DE USUARIOS -->
    <div class="card">
        <div class="card-header-custom">
            <h3>
                <i class="fas fa-list"></i>
                Listado de Usuarios
            </h3>
            <div class="search-box">
                <i class="fas fa-search" style="color: rgba(255,255,255,0.6);"></i>
                <input type="text" class="search-input" placeholder="Buscar usuario..." id="searchInput">
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    DR
                                </div>
                                <div class="user-details">
                                    <span class="user-name">Dra. María González</span>
                                    <span class="user-email">maria.gonzalez@sipce.com</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-rol admin">
                                <i class="fas fa-crown me-1"></i>
                                Administrador
                            </span>
                        </td>
                        <td>
                            <span class="badge-estado activo">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                Activo
                            </span>
                        </td>
                        <td>
                            <i class="far fa-clock me-2" style="color: #64748b;"></i>
                            Hace 2 horas
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                                    JR
                                </div>
                                <div class="user-details">
                                    <span class="user-name">Dr. Juan Pérez</span>
                                    <span class="user-email">juan.perez@sipce.com</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-rol medico">
                                <i class="fas fa-user-md me-1"></i>
                                Médico
                            </span>
                        </td>
                        <td>
                            <span class="badge-estado activo">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                Activo
                            </span>
                        </td>
                        <td>
                            <i class="far fa-clock me-2" style="color: #64748b;"></i>
                            Ayer
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar" style="background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);">
                                    AL
                                </div>
                                <div class="user-details">
                                    <span class="user-name">Ana López</span>
                                    <span class="user-email">ana.lopez@sipce.com</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-rol secretaria">
                                <i class="fas fa-user-tie me-1"></i>
                                Secretaria
                            </span>
                        </td>
                        <td>
                            <span class="badge-estado activo">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                Activo
                            </span>
                        </td>
                        <td>
                            <i class="far fa-clock me-2" style="color: #64748b;"></i>
                            Hace 3 días
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar" style="background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);">
                                    CM
                                </div>
                                <div class="user-details">
                                    <span class="user-name">Dr. Carlos Martínez</span>
                                    <span class="user-email">carlos.martinez@sipce.com</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-rol medico">
                                <i class="fas fa-user-md me-1"></i>
                                Médico
                            </span>
                        </td>
                        <td>
                            <span class="badge-estado inactivo">
                                <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                Inactivo
                            </span>
                        </td>
                        <td>
                            <i class="far fa-clock me-2" style="color: #64748b;"></i>
                            Hace 2 meses
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon edit" title="Editar">
                                    <i class="fas fa-edit"></i>
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
// Búsqueda en tiempo real
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

function openModal() {
    alert('Modal para crear nuevo usuario');
}
</script>

@endsection