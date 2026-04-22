@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.editar-paciente-wrapper {
    max-width: 1100px;
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
.form-card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
    border: 1px solid rgba(102, 126, 234, 0.08);
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
    padding: 22px 35px;
}

.card-header-custom h2 {
    margin: 0;
    font-weight: 600;
    font-size: 20px;
}

.card-header-custom p {
    margin: 5px 0 0 0;
    font-size: 14px;
    opacity: 0.9;
}

.card-header-custom i {
    margin-right: 10px;
    color: #a8edea;
}

/* BODY DEL FORMULARIO */
.card-body-custom {
    padding: 35px;
}

/* ALERTA DE ERROR */
.alert-error {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    border-left: 4px solid #ef4444;
    animation: shake 0.3s ease;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.alert-error ul {
    margin: 0;
    padding-left: 20px;
}

.alert-error li {
    margin: 5px 0;
}

/* FORM GRID */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.field {
    display: flex;
    flex-direction: column;
}

.field.full-width {
    grid-column: span 2;
}

.field label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #2d3748;
}

.field label i {
    margin-right: 8px;
    color: #667eea;
}

/* INPUTS Y SELECTS */
.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 16px;
    z-index: 10;
}

.input-field {
    width: 100%;
    padding: 12px 12px 12px 45px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
    background: white;
}

.input-field:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

textarea.input-field {
    padding: 12px;
    resize: vertical;
    font-family: inherit;
}

select.input-field {
    padding-left: 45px;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 16px;
}

/* INFO DEL PACIENTE */
.paciente-info-badge {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.paciente-avatar {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.paciente-info-text {
    flex: 1;
}

.paciente-info-text strong {
    color: #2d3748;
    font-size: 16px;
}

.paciente-info-text small {
    color: #64748b;
    font-size: 13px;
}

/* BOTONES DE ACCIÓN */
.form-actions {
    grid-column: span 2;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 2px solid #f1f5f9;
}

.btn {
    padding: 14px 30px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-cancel {
    background: white;
    color: #64748b;
    border: 2px solid #e2e8f0;
}

.btn-cancel:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #475569;
    transform: translateY(-2px);
}

.btn-save {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .field.full-width {
        grid-column: span 1;
    }
    
    .form-actions {
        grid-column: span 1;
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .card-body-custom {
        padding: 25px 20px;
    }
    
    .paciente-info-badge {
        flex-direction: column;
        text-align: center;
    }
}

/* CAMPO SOLO LECTURA */
.input-field[readonly] {
    background: #f8fafc;
    color: #64748b;
    cursor: not-allowed;
}
</style>

<div class="editar-paciente-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="page-header">
        <div>
            <h1>
                <i class="fas fa-user-edit me-2"></i>
                Editar Paciente
            </h1>
            <p>Actualización de información clínica del paciente</p>
        </div>

        <a href="{{ route('pacientes.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i>
            Volver al listado
        </a>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="form-card">

        <!-- HEADER CARD -->
        <div class="card-header-custom">
            <h2>
                <i class="fas fa-edit"></i>
                Formulario de edición
            </h2>
            <p>Modifique los campos que desea actualizar</p>
        </div>

        <!-- BODY -->
        <div class="card-body-custom">

            <!-- INFO DEL PACIENTE -->
            <div class="paciente-info-badge">
                <div class="paciente-avatar">
                    {{ strtoupper(substr($paciente->nombre_completo, 0, 1)) }}
                </div>
                <div class="paciente-info-text">
                    <strong>{{ $paciente->nombre_completo }}</strong><br>
                    <small>
                        <i class="fas fa-hashtag me-1"></i>
                        Expediente: #{{ $paciente->numero_expediente }}
                        <span class="mx-2">•</span>
                        <i class="fas fa-id-card me-1"></i>
                        Cédula: {{ $paciente->cedula_paciente }}
                    </small>
                </div>
            </div>

            <!-- ERRORES -->
            @if ($errors->any())
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORMULARIO -->
            <form action="{{ route('pacientes.update', $paciente) }}" method="POST" class="form-grid">
                @csrf
                @method('PUT')

                <!-- EXPEDIENTE -->
                <div class="field">
                    <label>
                        <i class="fas fa-hashtag"></i>
                        Número de expediente
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-folder input-icon"></i>
                        <input type="number" 
                               name="numero_expediente"
                               value="{{ old('numero_expediente', $paciente->numero_expediente) }}"
                               class="input-field"
                               required>
                    </div>
                </div>

                <!-- CÉDULA -->
                <div class="field">
                    <label>
                        <i class="fas fa-id-card"></i>
                        Cédula
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-credit-card input-icon"></i>
                        <input type="text"
                               name="cedula_paciente"
                               value="{{ old('cedula_paciente', $paciente->cedula_paciente) }}"
                               class="input-field"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                               required>
                    </div>
                </div>

                <!-- NOMBRE COMPLETO -->
                <div class="field">
                    <label>
                        <i class="fas fa-user"></i>
                        Nombre completo
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-circle input-icon"></i>
                        <input type="text"
                               name="nombre_completo"
                               value="{{ old('nombre_completo', $paciente->nombre_completo) }}"
                               class="input-field"
                               oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')"
                               required>
                    </div>
                </div>

                <!-- FECHA NACIMIENTO -->
                <div class="field">
                    <label>
                        <i class="fas fa-calendar"></i>
                        Fecha de nacimiento
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-cake-candles input-icon"></i>
                        <input type="date"
                               name="fecha_nacimiento"
                               value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento) }}"
                               class="input-field">
                    </div>
                </div>

                <!-- TELÉFONO -->
                <div class="field">
                    <label>
                        <i class="fas fa-phone"></i>
                        Teléfono
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone-alt input-icon"></i>
                        <input type="text"
                               name="telefono"
                               value="{{ old('telefono', $paciente->telefono) }}"
                               class="input-field"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    </div>
                </div>

                <!-- EMAIL -->
                <div class="field">
                    <label>
                        <i class="fas fa-envelope"></i>
                        Correo electrónico
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-at input-icon"></i>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $paciente->email) }}"
                               class="input-field">
                    </div>
                </div>

                <!-- PRIORIDAD -->
                <div class="field">
                    <label>
                        <i class="fas fa-flag"></i>
                        Prioridad
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-exclamation-circle input-icon"></i>
                        <select name="prioridad" class="input-field">
                            <option value="">Seleccionar prioridad</option>
                            <option value="Alta" {{ old('prioridad', $paciente->prioridad) == 'Alta' ? 'selected' : '' }}>🔴 Alta</option>
                            <option value="Media" {{ old('prioridad', $paciente->prioridad) == 'Media' ? 'selected' : '' }}>🟡 Media</option>
                            <option value="Baja" {{ old('prioridad', $paciente->prioridad) == 'Baja' ? 'selected' : '' }}>🟢 Baja</option>
                        </select>
                    </div>
                </div>

                <!-- DIRECCIÓN -->
                <div class="field full-width">
                    <label>
                        <i class="fas fa-map-marker-alt"></i>
                        Dirección completa
                    </label>
                    <textarea name="direccion" 
                              rows="2" 
                              class="input-field"
                              placeholder="Calle, número, colonia, ciudad...">{{ old('direccion', $paciente->direccion) }}</textarea>
                </div>

                <!-- MOTIVO DE CONSULTA -->
                <div class="field full-width">
                    <label>
                        <i class="fas fa-notes-medical"></i>
                        Motivo de consulta
                    </label>
                    <textarea name="motivo_consulta" 
                              rows="3" 
                              class="input-field"
                              placeholder="Describa el motivo de la consulta...">{{ old('motivo_consulta', $paciente->motivo_consulta) }}</textarea>
                </div>

                <!-- DIAGNÓSTICO PRELIMINAR -->
                <div class="field full-width">
                    <label>
                        <i class="fas fa-stethoscope"></i>
                        Diagnóstico preliminar
                    </label>
                    <textarea name="diagnostico_preliminar" 
                              rows="3" 
                              class="input-field"
                              placeholder="Diagnóstico inicial o impresión clínica...">{{ old('diagnostico_preliminar', $paciente->diagnostico_preliminar) }}</textarea>
                </div>

                <!-- BOTONES -->
                <div class="form-actions">
                    <a href="{{ route('pacientes.index') }}" class="btn btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i>
                        Guardar cambios
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- FONT AWESOME -->
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

@endsection