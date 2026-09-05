@extends('layouts.app')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

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
                    <div class="stat-number">{{ $totalPacientes }}</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+{{ $pacientesNuevosMes }} este mes</span>
                    </div>
                </div>
                <div class="stat-icon icon-pacientes">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Citas Programadas</h3>
                    <div class="stat-number">{{ $citasProgramadas }}</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+{{ $citasEstaSemana }} esta semana</span>
                    </div>
                </div>
                <div class="stat-icon icon-citas">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Registros Diarios</h3>
                    <div class="stat-number">{{ $registrosDiarios }}</div>
                    <div class="stat-trend trend-up">
                        <i class="fas fa-arrow-up"></i>
                        <span>+{{ $registrosHoy }} hoy</span>
                    </div>
                </div>
                <div class="stat-icon icon-diarios">
                    <i class="fas fa-book-open"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <h3>Alta Prioridad</h3>
                    <div class="stat-number">{{ $altaPrioridad }}</div>
                    <div class="stat-trend {{ $diferenciaPrioridad >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ $diferenciaPrioridad >= 0 ? 'up' : 'down' }}"></i>
                        <span>{{ $diferenciaPrioridad >= 0 ? '+' : '' }}{{ $diferenciaPrioridad }} vs ayer</span>
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
                    @forelse($actividadReciente as $actividad)
                    <div class="activity-item">
                        <div class="activity-icon" style="background: {{ $actividad['color'] }};">
                            <i class="{{ $actividad['icono'] }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">{{ $actividad['titulo'] }}</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> {{ $actividad['tiempo'] }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="activity-item">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title">No hay actividad reciente</div>
                            <div class="activity-time">
                                <i class="far fa-clock"></i> Comienza a usar el sistema
                            </div>
                        </div>
                    </div>
                    @endforelse
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
                    @forelse($citasHoy as $cita)
                    <div class="cita-item hoy">
                        <div class="activity-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="cita-info">
                            <h4>{{ $cita->paciente->nombre_completo ?? 'Sin paciente' }}</h4>
                            <p>{{ $cita->objetivo ?? 'Consulta' }} â€¢ {{ $cita->planificacion ?? 'General' }}</p>
                        </div>
                        <div class="cita-hora">{{ \Carbon\Carbon::parse($cita->fecha)->format('h:i A') }}</div>
                    </div>
                    @empty
                    @endforelse
                    
                    @forelse($citasFuturas as $cita)
                    <div class="cita-item futura">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="cita-info">
                            <h4>{{ $cita->paciente->nombre_completo ?? 'Sin paciente' }}</h4>
                            <p>{{ $cita->objetivo ?? 'Consulta' }} â€¢ {{ $cita->planificacion ?? 'General' }}</p>
                        </div>
                        <div class="cita-hora">{{ $cita->fecha_formateada }}</div>
                    </div>
                    @empty
                    <div class="cita-item futura">
                        <div class="activity-icon" style="background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="cita-info">
                            <h4>No hay citas programadas</h4>
                            <p>Agenda una nueva cita</p>
                        </div>
                        <div class="cita-hora">--</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- ACCESOS RÁPIDOS -->
        <div class="quick-actions">
            <a href="{{ route('pacientes.create') }}" class="quick-btn">
                <div class="quick-icon" style="background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);">
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

@push('scripts')
<script>
// Datos desde PHP
window.evolucionPacientes = @json($evolucionPacientes);
window.distribucionPrioridad = @json($distribucionPrioridad);
window.distribucionEdad = @json($distribucionEdad);
</script>
<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>
@endpush

@endsection
