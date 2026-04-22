@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.citas-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px 15px;
}

/* HEADER COLORIDO */
.citas-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 25px;
}

.citas-header h3 {
    font-weight: 700;
    color: white;
    margin: 0;
}

.btn-nueva-cita {
    background: white;
    color: #667eea;
    border: none;
    padding: 8px 18px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-nueva-cita:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    color: #764ba2;
}

/* GRID 3 COLUMNAS COMPACTAS */
.citas-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

/* TARJETA MÁS PEQUEÑA Y COLORIDA */
.cita-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 8px;
    padding: 10px;
    border: none;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 140px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.cita-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
}

.cita-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
}

/* COLORES VIBRANTES POR ESTADO */
.cita-hoy {
    background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
}

.cita-hoy::before {
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.cita-futura {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.cita-futura::before {
    background: linear-gradient(90deg, #00b4db, #0083b0);
}

.cita-pasada {
    background: linear-gradient(135deg, #d3cce3 0%, #e9e4f0 100%);
    opacity: 0.75;
}

.cita-pasada::before {
    background: linear-gradient(90deg, #bdc3c7, #95a5a6);
}

/* NOMBRE DEL PACIENTE */
.cita-nombre {
    font-size: 12px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* FECHA */
.cita-fecha {
    font-size: 10px;
    color: #4a5568;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
}

/* BADGE HOY */
.badge-hoy {
    background: #f59e0b;
    color: white;
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

/* OBJETIVO */
.cita-objetivo {
    font-size: 11px;
    color: #1e293b;
    line-height: 1.3;
    height: 28px;
    overflow: hidden;
    margin-bottom: 8px;
}

/* BOTONES DE ACCIÓN MÁS COMPACTOS */
.cita-actions {
    display: flex;
    gap: 3px;
    flex-wrap: wrap;
}

.cita-actions button {
    font-size: 9px;
    padding: 3px 0;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
    flex: 1;
    min-width: 40px;
    color: white;
}

/* COLORES BOTONES */
.btn-ok { 
    background: linear-gradient(135deg, #11998e, #38ef7d);
}

.btn-no { 
    background: linear-gradient(135deg, #ee5a24, #f0932b);
}

.btn-cancel { 
    background: linear-gradient(135deg, #eb3349, #f45c43);
}

.btn-re { 
    background: linear-gradient(135deg, #4facfe, #00f2fe);
}

.cita-actions button:hover {
    transform: scale(1.05);
    filter: brightness(1.1);
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .citas-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .cita-card {
        height: 135px;
    }
}

@media (max-width: 480px) {
    .citas-grid {
        grid-template-columns: 1fr;
    }
    
    .citas-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}

/* ICONOS DECORATIVOS */
.cita-icon {
    position: absolute;
    bottom: 5px;
    right: 5px;
    opacity: 0.1;
    font-size: 30px;
}
</style>

<div class="citas-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="citas-header d-flex justify-content-between align-items-center">
        <h3>
            <i class="fas fa-calendar-alt me-2"></i>
            Agenda de Citas
        </h3>

        <a href="{{ route('citas.create') }}" class="btn-nueva-cita">
            <i class="fas fa-plus-circle me-1"></i>
            Nueva cita
        </a>
    </div>

    <!-- GRID DE CITAS -->
    <div class="citas-grid">

        @foreach($citas as $cita)

        @php
            if($cita->fecha == $hoy) {
                $clase = 'cita-hoy';
                $icon = '🌟';
            } elseif($cita->fecha > $hoy) {
                $clase = 'cita-futura';
                $icon = '📅';
            } else {
                $clase = 'cita-pasada';
                $icon = '✓';
            }
        @endphp

        <div class="cita-card {{ $clase }}">

            <div>
                <div class="cita-nombre">
                    <i class="fas fa-user-circle me-1" style="color: #4a5568;"></i>
                    {{ $cita->paciente->nombre_completo }}
                </div>

                <div class="cita-fecha">
                    <i class="far fa-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}

                    @if($cita->fecha == $hoy)
                        <span class="badge-hoy">HOY</span>
                    @endif
                </div>

                <div class="cita-objetivo">
                    <i class="fas fa-flag me-1" style="font-size: 9px; color: #667eea;"></i>
                    {{ $cita->objetivo ?? 'Sin objetivo' }}
                </div>
            </div>

            <div class="cita-actions">
                <button class="btn-ok" title="Asistió">
                    <i class="fas fa-check me-1"></i>OK
                </button>
                <button class="btn-no" title="No asistió">
                    <i class="fas fa-times me-1"></i>No
                </button>
                <button class="btn-cancel" title="Cancelar">
                    <i class="fas fa-ban me-1"></i>Cancel
                </button>
                <button class="btn-re" title="Reprogramar">
                    <i class="fas fa-calendar-alt me-1"></i>Mover
                </button>
            </div>

            <!-- Icono decorativo -->
            <div class="cita-icon">{{ $icon }}</div>

        </div>

        @endforeach

    </div>

    @if(count($citas) == 0)
    <div class="text-center py-5" style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
        <i class="fas fa-calendar-times" style="font-size: 50px; color: #667eea; opacity: 0.5;"></i>
        <p class="mt-3" style="color: #4a5568;">No hay citas programadas</p>
        <a href="{{ route('citas.create') }}" class="btn-nueva-cita mt-2">Crear primera cita</a>
    </div>
    @endif

</div>

@endsection