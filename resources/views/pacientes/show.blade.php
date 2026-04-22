@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.ficha-paciente-wrapper {
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

.page-header h1 {
    font-weight: 700;
    color: white;
    margin: 0;
    font-size: 28px;
}

.page-header p {
    color: rgba(255, 255, 255, 0.9);
    margin: 8px 0 0 0;
    font-size: 15px;
}

.btn-volver {
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
}

.btn-volver:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #764ba2;
}

/* CARD PRINCIPAL */
.info-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
    border: 1px solid rgba(102, 126, 234, 0.08);
    margin-bottom: 30px;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-header-custom {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: white;
    padding: 22px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header-custom h2 {
    margin: 0;
    font-weight: 600;
    font-size: 20px;
}

.card-header-custom h2 i {
    margin-right: 10px;
    color: #a8edea;
}

.prioridad-badge {
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    color: white;
}

.prioridad-badge.Alta {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.prioridad-badge.Media {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.prioridad-badge.Baja {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

/* AVATAR Y NOMBRE */
.paciente-profile {
    padding: 30px;
    display: flex;
    align-items: center;
    gap: 25px;
    border-bottom: 2px solid #f1f5f9;
}

.paciente-avatar-large {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 36px;
    font-weight: bold;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.paciente-name h3 {
    margin: 0 0 5px 0;
    font-size: 24px;
    font-weight: 700;
    color: #1e293b;
}

.paciente-name p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.paciente-name p i {
    margin-right: 5px;
    color: #667eea;
}

/* GRID DE INFORMACIÓN */
.info-grid {
    padding: 30px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.info-item {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    padding: 18px;
    border-radius: 12px;
    transition: all 0.3s;
}

.info-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
}

.info-label {
    font-size: 12px;
    color: #64748b;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-label i {
    color: #667eea;
    font-size: 14px;
}

.info-value {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
}

.info-full {
    grid-column: span 2;
}

/* SECCIONES DE TEXTO */
.text-section {
    padding: 0 30px 30px 30px;
}

.text-section h4 {
    font-size: 16px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 12px;
}

.text-section h4 i {
    margin-right: 8px;
    color: #667eea;
}

.text-box {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    padding: 18px;
    border-radius: 12px;
    color: #334155;
    line-height: 1.6;
    font-size: 14px;
}

/* BOTONES DE ACCIÓN */
.action-buttons {
    padding: 25px 30px;
    border-top: 2px solid #f1f5f9;
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 25px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-back {
    background: white;
    color: #64748b;
    border: 2px solid #e2e8f0;
}

.btn-back:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #475569;
    transform: translateY(-2px);
}

/* HISTORIAL DE CITAS */
.historial-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
    border: 1px solid rgba(102, 126, 234, 0.08);
}

.historial-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 20px 30px;
}

.historial-header h3 {
    margin: 0;
    font-weight: 600;
    font-size: 18px;
}

.historial-header h3 i {
    margin-right: 10px;
}

.historial-body {
    padding: 0;
}

.citas-timeline {
    padding: 20px 30px;
}

.cita-item {
    display: flex;
    align-items: center;
    padding: 18px 0;
    border-bottom: 1px solid #f1f5f9;
}

.cita-item:last-child {
    border-bottom: none;
}

.cita-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 18px;
    color: white;
    font-size: 20px;
}

.cita-icon.completada {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.cita-icon.pendiente {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.cita-icon.cancelada {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.cita-icon.programada {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.cita-content {
    flex: 1;
}

.cita-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.cita-fecha {
    font-weight: 700;
    color: #1e293b;
    font-size: 15px;
}

.cita-estado {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    color: white;
}

.cita-estado.completada {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.cita-estado.pendiente {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.cita-estado.cancelada {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.cita-estado.programada {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.cita-objetivo {
    color: #64748b;
    font-size: 13px;
    margin-top: 4px;
}

.cita-objetivo i {
    margin-right: 5px;
    color: #667eea;
}

.empty-citas {
    text-align: center;
    padding: 50px 30px;
    color: #94a3b8;
}

.empty-citas i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.btn-nueva-cita {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s;
}

.btn-nueva-cita:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
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
    
    .info-grid {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .info-full {
        grid-column: span 1;
    }
    
    .paciente-profile {
        flex-direction: column;
        text-align: center;
        padding: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .card-header-custom {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .cita-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .cita-item {
        flex-direction: column;
        text-align: center;
    }
    
    .cita-icon {
        margin: 0 0 15px 0;
    }
}
</style>

<div class="ficha-paciente-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="page-header">
        <div>
            <h1>
                <i class="fas fa-address-card me-2"></i>
                Ficha del Paciente
            </h1>
            <p>Expediente clínico SIPCE - Información detallada</p>
        </div>

        <a href="{{ route('pacientes.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i>
            Volver al listado
        </a>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="info-card">

        <!-- HEADER CON PRIORIDAD -->
        <div class="card-header-custom">
            <h2>
                <i class="fas fa-clipboard-list"></i>
                Información del paciente
            </h2>
            
            <span class="prioridad-badge {{ $paciente->prioridad }}">
                <i class="fas fa-flag me-1"></i>
                Prioridad {{ $paciente->prioridad }}
            </span>
        </div>

        <!-- PERFIL DEL PACIENTE -->
        <div class="paciente-profile">
            <div class="paciente-avatar-large">
                {{ strtoupper(substr($paciente->nombre_completo, 0, 1)) }}
            </div>
            <div class="paciente-name">
                <h3>{{ $paciente->nombre_completo }}</h3>
                <p>
                    <i class="fas fa-hashtag"></i>
                    Expediente #{{ $paciente->numero_expediente }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-id-card"></i>
                    Cédula: {{ $paciente->cedula_paciente }}
                </p>
            </div>
        </div>

        <!-- GRID DE INFORMACIÓN -->
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-calendar"></i>
                    Fecha de nacimiento
                </div>
                <div class="info-value">
                    {{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-phone"></i>
                    Teléfono
                </div>
                <div class="info-value">
                    {{ $paciente->telefono ?? 'No registrado' }}
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-envelope"></i>
                    Correo electrónico
                </div>
                <div class="info-value">
                    {{ $paciente->email ?? 'No registrado' }}
                </div>
            </div>

            <div class="info-item">
                <div class="info-label">
                    <i class="fas fa-clock"></i>
                    Registro creado
                </div>
                <div class="info-value">
                    {{ $paciente->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- DIRECCIÓN -->
        <div class="text-section">
            <h4>
                <i class="fas fa-map-marker-alt"></i>
                Dirección
            </h4>
            <div class="text-box">
                {{ $paciente->direccion ?? 'No registrada' }}
            </div>
        </div>

        <!-- MOTIVO DE CONSULTA -->
        <div class="text-section">
            <h4>
                <i class="fas fa-notes-medical"></i>
                Motivo de consulta
            </h4>
            <div class="text-box">
                {{ $paciente->motivo_consulta ?? 'No registrado' }}
            </div>
        </div>

        <!-- DIAGNÓSTICO -->
        <div class="text-section">
            <h4>
                <i class="fas fa-stethoscope"></i>
                Diagnóstico preliminar
            </h4>
            <div class="text-box">
                {{ $paciente->diagnostico_preliminar ?? 'Sin diagnóstico registrado' }}
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="action-buttons">
            <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-edit">
                <i class="fas fa-edit"></i>
                Editar información
            </a>

            <a href="{{ route('pacientes.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i>
                Volver al listado
            </a>
        </div>

    </div>

    <!-- HISTORIAL DE CITAS -->
    <div class="historial-card">
        <div class="historial-header">
            <h3>
                <i class="fas fa-calendar-alt"></i>
                Historial de Citas
            </h3>
        </div>

        <div class="historial-body">
            @php
                $citas = $paciente->citas ?? collect([]);
            @endphp

            @if($citas->count() > 0)
                <div class="citas-timeline">
                    @foreach($citas as $cita)
                        @php
                            $estadoClass = 'programada';
                            $iconClass = 'fa-calendar-check';
                            
                            if($cita->fecha < date('Y-m-d')) {
                                $estadoClass = 'completada';
                                $iconClass = 'fa-check-circle';
                            } elseif($cita->estado == 'cancelada') {
                                $estadoClass = 'cancelada';
                                $iconClass = 'fa-times-circle';
                            } elseif($cita->estado == 'pendiente') {
                                $estadoClass = 'pendiente';
                                $iconClass = 'fa-clock';
                            }
                        @endphp

                        <div class="cita-item">
                            <div class="cita-icon {{ $estadoClass }}">
                                <i class="fas {{ $iconClass }}"></i>
                            </div>
                            
                            <div class="cita-content">
                                <div class="cita-header">
                                    <span class="cita-fecha">
                                        <i class="far fa-calendar me-2"></i>
                                        {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                    </span>
                                    <span class="cita-estado {{ $estadoClass }}">
                                        {{ ucfirst($cita->estado ?? 'Programada') }}
                                    </span>
                                </div>
                                
                                <div class="cita-objetivo">
                                    <i class="fas fa-bullseye"></i>
                                    {{ $cita->objetivo ?? 'Sin objetivo definido' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-citas">
                    <i class="fas fa-calendar-times"></i>
                    <p>No hay citas registradas para este paciente</p>
                    <a href="{{ route('citas.create') }}" class="btn-nueva-cita">
                        <i class="fas fa-plus-circle"></i>
                        Programar primera cita
                    </a>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- FONT AWESOME -->
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@endsection