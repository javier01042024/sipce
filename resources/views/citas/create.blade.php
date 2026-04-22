@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.citas-form-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER COLORIDO */
.form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 35px;
    border-radius: 14px;
    margin-bottom: 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-header h2 {
    font-weight: 700;
    color: white;
    margin: 0;
    font-size: 28px;
}

.form-header p {
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
    display: inline-block;
}

.btn-volver:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #764ba2;
}

/* CARD PRINCIPAL */
.form-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
    border: 1px solid rgba(102, 126, 234, 0.08);
}

.card-header-custom {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: white;
    padding: 22px 35px;
}

.card-header-custom h5 {
    margin: 0;
    font-weight: 600;
    font-size: 18px;
}

.card-header-custom h5 i {
    margin-right: 10px;
    color: #a8edea;
}

/* FORMULARIO CON MÁS ESPACIO */
.form-section {
    padding: 40px 35px;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 12px;
    font-size: 15px;
}

.form-label i {
    margin-right: 8px;
    color: #667eea;
}

/* INPUTS CON MÁS ESPACIO */
.form-select-custom, .form-input-custom {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 15px;
    transition: all 0.3s;
    background: white;
    margin-bottom: 5px;
}

.form-select-custom:focus, .form-input-custom:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.08);
    outline: none;
}

/* TEXTAREA CON MÁS ESPACIO */
.textarea-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    border-radius: 14px;
    padding: 20px;
    border: none;
    margin-top: 8px;
}

.textarea-custom {
    border: none !important;
    background: transparent !important;
    resize: none;
    font-size: 15px;
    color: #2d3748;
    padding: 0 !important;
    line-height: 1.6;
}

.textarea-custom:focus {
    outline: none;
    box-shadow: none !important;
}

.textarea-custom::placeholder {
    color: #94a3b8;
    font-style: italic;
}

/* ESPACIADO ENTRE FILAS */
.row.g-4 {
    --bs-gutter-y: 2rem !important;
}

/* BOTONES SEPARADOS */
.buttons-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 2px solid #f1f5f9;
    padding-top: 30px;
    margin-top: 20px;
}

.btn-cancel {
    background: white;
    color: #64748b;
    border: 2px solid #e2e8f0;
    padding: 14px 30px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s;
    text-decoration: none;
}

.btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #475569;
    transform: translateY(-1px);
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.25);
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.35);
    color: white;
}

.btn-submit i {
    margin-right: 8px;
}

/* ANIMACIONES */
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

.form-card {
    animation: slideIn 0.6s ease-out;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .form-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px 20px;
    }
    
    .form-section {
        padding: 25px 20px;
    }
    
    .card-header-custom {
        padding: 18px 25px;
    }
    
    .buttons-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .btn-cancel, .btn-submit {
        width: 100%;
        text-align: center;
    }
}

/* ICONOS */
.input-icon-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    z-index: 10;
    font-size: 16px;
}

.input-with-icon {
    padding-left: 48px !important;
}

/* MEJORA VISUAL PARA SELECT */
select.form-select-custom {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 18px center;
    background-size: 16px;
    padding-right: 45px !important;
}
</style>

<div class="citas-form-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="form-header">
        <div>
            <h2>
                <i class="fas fa-calendar-plus me-2"></i>
                Nueva Cita
            </h2>
            <p>Programación de atención clínica del paciente</p>
        </div>

        <a href="{{ route('citas.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al calendario
        </a>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="form-card">

                <!-- HEADER CARD -->
                <div class="card-header-custom">
                    <h5>
                        <i class="fas fa-calendar-check"></i>
                        Agendado de citas
                    </h5>
                </div>

                <!-- FORMULARIO -->
                <div class="form-section">

                    <form method="POST" action="{{ route('citas.store') }}">
                        @csrf

                        <div class="row g-4">

                            <!-- PACIENTE Y FECHA EN LA MISMA FILA -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Paciente
                                </label>

                                <div class="input-icon-wrapper">
                                    <i class="fas fa-user-circle input-icon"></i>
                                    <select name="paciente_id"
                                            class="form-select-custom w-100 input-with-icon"
                                            required>
                                        <option value="">Seleccionar paciente...</option>
                                        @foreach($pacientes as $paciente)
                                            <option value="{{ $paciente->id }}">
                                                {{ $paciente->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i>
                                    Fecha de cita
                                </label>

                                <div class="input-icon-wrapper">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <input type="date"
                                           name="fecha"
                                           class="form-input-custom w-100 input-with-icon"
                                           required
                                           min="{{ date('Y-m-d') }}">
                                </div>
                            </div>

                            <!-- OBJETIVO -->
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-bullseye"></i>
                                    Objetivo terapéutico
                                </label>

                                <div class="textarea-card">
                                    <textarea name="objetivo"
                                              rows="3"
                                              class="textarea-custom w-100"
                                              placeholder="Definir el objetivo principal de la sesión..."></textarea>
                                </div>
                            </div>

                            <!-- PLANIFICACIÓN -->
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-tasks"></i>
                                    Planificación de la sesión
                                </label>

                                <div class="textarea-card">
                                    <textarea name="planificacion"
                                              rows="5"
                                              class="textarea-custom w-100"
                                              placeholder="Estrategia, técnicas o enfoque terapéutico que se utilizarán durante la consulta..."></textarea>
                                </div>
                            </div>

                        </div>

                        <!-- BOTONES SEPARADOS -->
                        <div class="buttons-container">
                            <a href="{{ route('citas.index') }}" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>

                            <button type="submit" class="btn-submit">
                                <i class="fas fa-check-circle"></i>
                                Crear cita
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- AGREGAR FONT AWESOME -->
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@endsection