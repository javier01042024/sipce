@extends('layouts.app')

@section('content')

<style>
/* RESET Y VARIABLES */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.dashboard-container {
    min-height: 100vh;
    background: #f1f5f9;
    padding: 20px;
}

/* HEADER DEL DASHBOARD */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 14px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
}

.header-left h1 {
    font-weight: 700;
    color: white;
    margin: 0;
    font-size: 26px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-left p {
    color: rgba(255, 255, 255, 0.9);
    margin: 5px 0 0 0;
    font-size: 14px;
}

.header-date {
    background: rgba(255, 255, 255, 0.2);
    padding: 10px 20px;
    border-radius: 25px;
    color: white;
    font-weight: 500;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* CONTENEDOR PRINCIPAL */
.dashboard-wrapper {
    max-width: 1400px;
    margin: 0 auto;
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
    border-radius: 16px;
    padding: 25px 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.3s;
    border: 1px solid #e2e8f0;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
}

.stat-content h3 {
    font-size: 13px;
    color: #64748b;
    margin: 0 0 8px 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-number {
    font-size: 32px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 5px;
}

.stat-trend {
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.trend-up {
    color: #10b981;
}

.trend-down {
    color: #ef4444;
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: white;
}

.icon-pacientes { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.icon-citas { background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%); }
.icon-diarios { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.icon-prioridad { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }

/* GRÁFICAS */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.chart-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-header h3 i {
    color: #667eea;
}

.chart-select {
    padding: 8px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    background: white;
    cursor: pointer;
}

.chart-container {
    position: relative;
    height: 250px;
}

/* ACTIVIDAD Y CITAS */
.activity-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.activity-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.activity-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.activity-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-view-all {
    color: #667eea;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    transition: all 0.3s;
}

.activity-item:hover {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    transform: translateX(5px);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 3px;
    font-size: 14px;
}

.activity-time {
    font-size: 11px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 5px;
}

/* CITAS */
.citas-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cita-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    border-left: 4px solid;
    transition: all 0.3s;
}

.cita-item.hoy {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.cita-item.futura {
    border-left-color: #667eea;
}

.cita-info h4 {
    margin: 0 0 3px 0;
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
}

.cita-info p {
    margin: 0;
    font-size: 12px;
    color: #64748b;
}

.cita-hora {
    margin-left: auto;
    font-weight: 600;
    color: #667eea;
    font-size: 13px;
}

/* ACCESOS RÁPIDOS */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.quick-btn {
    background: white;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    transition: all 0.3s;
    border: 1px solid #e2e8f0;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    display: block;
}

.quick-btn:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.quick-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    color: white;
    font-size: 22px;
}

.quick-btn span {
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
    display: block;
}

.quick-btn small {
    color: #94a3b8;
    font-size: 11px;
    margin-top: 5px;
    display: block;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .charts-grid { grid-template-columns: 1fr; }
    .activity-section { grid-template-columns: 1fr; }
    .quick-actions { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .stats-grid { grid-template-columns: 1fr; }
    .quick-actions { grid-template-columns: 1fr; }
    
    .dashboard-container { padding: 10px; }
    
    .cita-item { flex-wrap: wrap; }
    
    .cita-hora {
        width: 100%;
        margin-left: 52px;
        margin-top: 5px;
    }
}

/* ANIMACIONES */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card, .chart-card, .activity-card {
    animation: fadeInUp 0.6s ease-out forwards;
}

.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.2s; }
.stat-card:nth-child(4) { animation-delay: 0.3s; }
</style>

<div class="dashboard-container">
    <div class="dashboard-wrapper">
        
        <!-- HEADER -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1>
                    <i class="fas fa-chart-pie"></i>
                    Panel de Control
                </h1>
                <p>Resumen general del sistema SIPCE</p>
            </div>
            <div class="header-date">
                <i class="far fa-calendar-alt"></i>
                <span id="currentDate"></span>
            </div>
        </div>
        
        <!-- ESTADÍSTICAS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Total Pacientes</h3>
                    <div class="stat-number">{{ $totalPacientes ?? 156 }}</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+12 este mes</span>
                    </div>
                </div>
                <div class="stat-icon icon-pacientes">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Citas Programadas</h3>
                    <div class="stat-number">{{ $citasProgramadas ?? 24 }}</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+8 esta semana</span>
                    </div>
                </div>
                <div class="stat-icon icon-citas">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Registros Diarios</h3>
                    <div class="stat-number">{{ $registrosDiarios ?? 342 }}</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+23 hoy</span>
                    </div>
                </div>
                <div class="stat-icon icon-diarios">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Alta Prioridad</h3>
                    <div class="stat-number">{{ $altaPrioridad ?? 8 }}</div>
                    <div class="stat-trend trend-down">
                        <i class="fas fa-arrow-down"></i>
                        <span>-2 vs ayer</span>
                    </div>
                </div>
                <div class="stat-icon icon-prioridad">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        
        <!-- GRÁFICAS -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h3>
                        <i class="fas fa-chart-line"></i>
                        Evolución de Pacientes
                    </h3>
                    <select class="chart-select" id="periodoSelect">
                        <option value="semana">Esta semana</option>
                        <option value="mes" selected>Este mes</option>
                        <option value="año">Este año</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="pacientesChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3>
                        <i class="fas fa-chart-bar"></i>
                        Distribución por Prioridad
                    </h3>
                    <select class="chart-select" id="tipoChartSelect">
                        <option value="prioridad" selected>Por Prioridad</option>
                        <option value="edad">Por Edad</option>
                        <option value="genero">Por Género</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="prioridadChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- ACTIVIDAD Y CITAS -->
        <div class="activity-section">
            <div class="activity-card">
                <div class="activity-header">
                    <h3>
                        <i class="fas fa-history"></i>
                        Actividad Reciente
                    </h3>
                    <a href="#" class="btn-view-all">
                        Ver todo <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Nuevo paciente: María González</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> Hace 15 min
                            </div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Cita programada: Juan Pérez</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> Hace 45 min
                            </div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);">
                            <i class="fas fa-pen"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Registro actualizado: Ana Martínez</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> Hace 2 horas
                            </div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
                            <i class="fas fa-flag"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Prioridad Alta: Carlos Ruiz</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> Hace 3 horas
                            </div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">Cita completada: Laura Sánchez</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> Hace 5 horas
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="activity-card">
                <div class="activity-header">
                    <h3>
                        <i class="fas fa-calendar-alt"></i>
                        Próximas Citas
                    </h3>
                    <a href="{{ route('citas.index') }}" class="btn-view-all">
                        Ver todas <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="citas-list">
                    <div class="cita-item hoy">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="cita-info">
                            <h4>María González</h4>
                            <p>Primera consulta • Evaluación</p>
                        </div>
                        <div class="cita-hora">10:30 AM</div>
                    </div>
                    
                    <div class="cita-item hoy">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="cita-info">
                            <h4>Juan Pérez</h4>
                            <p>Seguimiento • Terapia</p>
                        </div>
                        <div class="cita-hora">2:00 PM</div>
                    </div>
                    
                    <div class="cita-item futura">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="cita-info">
                            <h4>Ana Martínez</h4>
                            <p>Control mensual • Revisión</p>
                        </div>
                        <div class="cita-hora">Mañana 9:00 AM</div>
                    </div>
                    
                    <div class="cita-item futura">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="cita-info">
                            <h4>Carlos Ruiz</h4>
                            <p>Emergencia • Alta prioridad</p>
                        </div>
                        <div class="cita-hora">Mañana 11:30 AM</div>
                    </div>
                    
                    <div class="cita-item futura">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="cita-info">
                            <h4>Laura Sánchez</h4>
                            <p>Seguimiento • Evaluación</p>
                        </div>
                        <div class="cita-hora">25/04/2026</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ACCESOS RÁPIDOS -->
        <div class="quick-actions">
            <a href="{{ route('pacientes.create') }}" class="quick-btn">
                <div class="quick-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-user-plus"></i>
                </div>
                <span>Nuevo Paciente</span>
                <small>Registrar paciente</small>
            </a>
            
            <a href="{{ route('citas.create') }}" class="quick-btn">
                <div class="quick-icon" style="background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <span>Nueva Cita</span>
                <small>Agendar consulta</small>
            </a>
            
            <a href="{{ route('diarios.create') }}" class="quick-btn">
                <div class="quick-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                    <i class="fas fa-book-medical"></i>
                </div>
                <span>Registro Diario</span>
                <small>Seguimiento diario</small>
            </a>
            
            <a href="{{ route('pacientes.index') }}" class="quick-btn">
                <div class="quick-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-search"></i>
                </div>
                <span>Buscar Paciente</span>
                <small>Ver expedientes</small>
            </a>
        </div>
        
    </div>
</div>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Fecha actual
const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
document.getElementById('currentDate').textContent = new Date().toLocaleDateString('es-ES', options);

// Gráficas
let pacientesChart, prioridadChart;

document.addEventListener('DOMContentLoaded', function() {
    // Gráfica de pacientes
    const ctx1 = document.getElementById('pacientesChart').getContext('2d');
    pacientesChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'],
            datasets: [{
                label: 'Nuevos pacientes',
                data: [12, 18, 15, 22],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#e2e8f0' } },
                x: { grid: { display: false } }
            }
        }
    });
    
    // Gráfica de prioridad
    const ctx2 = document.getElementById('prioridadChart').getContext('2d');
    prioridadChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Alta', 'Media', 'Baja'],
            datasets: [{
                data: [8, 45, 103],
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 13 } }
                }
            },
            cutout: '65%'
        }
    });
});

// Selector de período
document.getElementById('periodoSelect').addEventListener('change', function() {
    const periodo = this.value;
    let labels, data;
    
    if (periodo === 'semana') {
        labels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        data = [3, 5, 4, 7, 6, 2, 4];
    } else if (periodo === 'mes') {
        labels = ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4'];
        data = [12, 18, 15, 22];
    } else {
        labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
        data = [45, 52, 48, 60, 55, 68];
    }
    
    pacientesChart.data.labels = labels;
    pacientesChart.data.datasets[0].data = data;
    pacientesChart.update();
});

// Selector de tipo de gráfica
document.getElementById('tipoChartSelect').addEventListener('change', function() {
    const tipo = this.value;
    let labels, data, colors;
    
    if (tipo === 'prioridad') {
        labels = ['Alta', 'Media', 'Baja'];
        data = [8, 45, 103];
        colors = ['#ef4444', '#f59e0b', '#10b981'];
    } else if (tipo === 'edad') {
        labels = ['18-30', '31-50', '51-70', '70+'];
        data = [42, 68, 35, 11];
        colors = ['#667eea', '#764ba2', '#f39c12', '#ef4444'];
    } else {
        labels = ['Femenino', 'Masculino', 'Otro'];
        data = [98, 54, 4];
        colors = ['#ec4899', '#3b82f6', '#8b5cf6'];
    }
    
    prioridadChart.data.labels = labels;
    prioridadChart.data.datasets[0].data = data;
    prioridadChart.data.datasets[0].backgroundColor = colors;
    prioridadChart.update();
});
</script>

@endsection