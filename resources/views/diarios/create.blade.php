@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.diario-create-wrapper {
    max-width: 900px;
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

.card-header-custom h2 i {
    margin-right: 10px;
    color: #a8edea;
}

/* BODY DEL FORMULARIO */
.card-body-custom {
    padding: 35px;
}

/* INFO DEL PACIENTE (SESIÓN) */
.paciente-session-info {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    padding: 20px;
    border-radius: 14px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    border-left: 4px solid #667eea;
}

.paciente-avatar {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.paciente-details h4 {
    margin: 0 0 5px 0;
    color: #1e293b;
    font-size: 18px;
    font-weight: 700;
}

.paciente-details p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.paciente-details p i {
    margin-right: 5px;
    color: #667eea;
}

/* FECHA DEL DISPOSITIVO */
.fecha-dispositivo {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    color: white;
}

.fecha-icon {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.fecha-text {
    flex: 1;
}

.fecha-text strong {
    font-size: 16px;
    display: block;
    margin-bottom: 3px;
}

.fecha-text small {
    opacity: 0.9;
    font-size: 13px;
}

/* FORMULARIO */
.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 10px;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-label i {
    color: #667eea;
}

.textarea-wrapper {
    position: relative;
}

.textarea-icon {
    position: absolute;
    left: 15px;
    top: 15px;
    color: #667eea;
    font-size: 18px;
}

.textarea-custom {
    width: 100%;
    padding: 15px 15px 15px 45px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    line-height: 1.6;
    resize: vertical;
    min-height: 200px;
    transition: all 0.3s;
    font-family: inherit;
    background: white;
}

.textarea-custom:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

.textarea-custom::placeholder {
    color: #94a3b8;
    font-style: italic;
}

/* SUGERENCIAS RÁPIDAS */
.quick-suggestions {
    margin-top: 15px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.suggestion-badge {
    background: #f1f5f9;
    color: #64748b;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #e2e8f0;
}

.suggestion-badge:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

/* CONTADOR DE CARACTERES */
.char-counter {
    text-align: right;
    margin-top: 8px;
    font-size: 12px;
    color: #94a3b8;
}

.char-counter.warning {
    color: #f59e0b;
}

.char-counter.danger {
    color: #ef4444;
}

/* ESTADO EMOCIONAL */
.emocion-selector {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.emocion-option {
    flex: 1;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: white;
}

.emocion-option:hover {
    border-color: #667eea;
    background: #f8fafc;
}

.emocion-option.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
}

.emocion-option .emoji {
    font-size: 24px;
    display: block;
    margin-bottom: 5px;
}

.emocion-option .label {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
}

/* BOTONES DE ACCIÓN */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.4);
    color: white;
}

/* CAMPO OCULTO */
.input-hidden {
    display: none;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px 20px;
    }
    
    .card-body-custom {
        padding: 25px 20px;
    }
    
    .paciente-session-info {
        flex-direction: column;
        text-align: center;
    }
    
    .paciente-details p {
        justify-content: center;
    }
    
    .fecha-dispositivo {
        flex-direction: column;
        text-align: center;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 15px;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .emocion-selector {
        flex-direction: column;
    }
}
</style>

<div class="diario-create-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="page-header">
        <div>
            <h1>
                <i class="fas fa-book-medical me-2"></i>
                Nuevo Registro Diario
            </h1>
            <p>Documenta el seguimiento emocional y terapéutico</p>
        </div>

        <a href="{{ route('diarios.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left"></i>
            Volver al diario
        </a>
    </div>

    <!-- CARD PRINCIPAL -->
    <div class="form-card">

        <!-- HEADER CARD -->
        <div class="card-header-custom">
            <h2>
                <i class="fas fa-pen-fancy"></i>
                Formulario de registro
            </h2>
        </div>

        <!-- BODY -->
        <div class="card-body-custom">

            <form method="POST" action="{{ route('diarios.store') }}" id="diarioForm">
                @csrf

                <!-- PACIENTE DE LA SESIÓN (OCULTO) -->
                @php
                    $pacienteActual = auth()->user()->paciente ?? null;
                    // Si no hay paciente en sesión, usar el primero de la lista
                    if(!$pacienteActual && isset($pacientes) && $pacientes->count() > 0) {
                        $pacienteActual = $pacientes->first();
                    }
                @endphp

                @if($pacienteActual)
                    <input type="hidden" name="paciente_id" value="{{ $pacienteActual->id }}">
                    
                    <!-- INFO DEL PACIENTE (SOLO LECTURA) -->
                    <div class="paciente-session-info">
                        <div class="paciente-avatar">
                            {{ strtoupper(substr($pacienteActual->nombre_completo, 0, 1)) }}
                        </div>
                        <div class="paciente-details">
                            <h4>{{ $pacienteActual->nombre_completo }}</h4>
                            <p>
                                <span>
                                    <i class="fas fa-hashtag"></i>
                                    Exp: #{{ $pacienteActual->numero_expediente }}
                                </span>
                                <span>
                                    <i class="fas fa-id-card"></i>
                                    Cédula: {{ $pacienteActual->cedula_paciente }}
                                </span>
                            </p>
                        </div>
                    </div>
                @else
                    <!-- FALLBACK: SELECTOR DE PACIENTE -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i>
                            Seleccionar paciente
                        </label>
                        <select name="paciente_id" class="form-select" required style="width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 10px;">
                            <option value="">Seleccione un paciente...</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- FECHA DEL DISPOSITIVO -->
                @php
                    $fechaActual = date('Y-m-d');
                    $fechaFormateada = \Carbon\Carbon::now()->format('d/m/Y');
                    $diaSemana = \Carbon\Carbon::now()->locale('es')->dayName;
                @endphp

                <input type="hidden" name="fecha" value="{{ $fechaActual }}" id="fechaInput">

                <div class="fecha-dispositivo">
                    <div class="fecha-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="fecha-text">
                        <strong>
                            <i class="fas fa-clock me-1"></i>
                            {{ $fechaFormateada }}
                        </strong>
                        <small>Registro del {{ ucfirst($diaSemana) }}</small>
                    </div>
                    <div>
                        <i class="fas fa-mobile-alt me-1"></i>
                        <small>Fecha del dispositivo</small>
                    </div>
                </div>

                <!-- ESTADO EMOCIONAL -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-smile"></i>
                        ¿Cómo te sientes hoy?
                    </label>
                    
                    <div class="emocion-selector">
                        <div class="emocion-option" onclick="selectEmocion(this, 'positivo', '😊 Me siento bien, con energía positiva')">
                            <span class="emoji">😊</span>
                            <span class="label">Positivo</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'neutral', '😐 Me siento estable, sin cambios significativos')">
                            <span class="emoji">😐</span>
                            <span class="label">Neutral</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'negativo', '😔 Me siento mal, con malestar emocional')">
                            <span class="emoji">😔</span>
                            <span class="label">Negativo</span>
                        </div>
                    </div>
                </div>

                <!-- CONTENIDO DEL DÍA -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-pencil-alt"></i>
                        Contenido del registro
                    </label>

                    <div class="textarea-wrapper">
                        <i class="fas fa-quote-right textarea-icon"></i>
                        <textarea name="contenido" 
                                  id="contenido" 
                                  rows="8" 
                                  class="textarea-custom"
                                  placeholder="Describe cómo ha sido el día de hoy, emociones, pensamientos, situaciones relevantes..."
                                  required></textarea>
                    </div>

                    <!-- CONTADOR DE CARACTERES -->
                    <div class="char-counter" id="charCounter">
                        <span id="charCount">0</span> / 1000 caracteres
                    </div>

                    <!-- SUGERENCIAS RÁPIDAS -->
                    <div class="quick-suggestions">
                        <span class="suggestion-badge" onclick="agregarSugerencia('Hoy me he sentido ')">
                            <i class="far fa-smile"></i> Emociones
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('Durante la sesión trabajamos ')">
                            <i class="fas fa-comments"></i> Sesión
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('He notado mejoría en ')">
                            <i class="fas fa-chart-line"></i> Progreso
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('Me preocupa ')">
                            <i class="fas fa-heart"></i> Preocupaciones
                        </span>
                    </div>
                </div>

                <!-- BOTONES DE ACCIÓN -->
                <div class="form-actions">
                    <a href="{{ route('diarios.index') }}" class="btn btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>

                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i>
                        Guardar registro
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

<!-- JAVASCRIPT -->
<script>
// Seleccionar emoción
function selectEmocion(element, tipo, textoBase) {
    // Remover selected de todas las opciones
    document.querySelectorAll('.emocion-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Agregar selected a la opción clickeada
    element.classList.add('selected');
    
    // Agregar texto sugerido al textarea
    const textarea = document.getElementById('contenido');
    if (textarea.value.trim() === '') {
        textarea.value = textoBase + ' ';
    } else {
        textarea.value = textarea.value + '\n\n' + textoBase + ' ';
    }
    
    // Actualizar contador
    updateCharCount();
    
    // Enfocar textarea
    textarea.focus();
}

// Agregar sugerencia
function agregarSugerencia(texto) {
    const textarea = document.getElementById('contenido');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    
    textarea.value = textBefore + texto + textAfter;
    textarea.focus();
    textarea.setSelectionRange(cursorPos + texto.length, cursorPos + texto.length);
    
    // Actualizar contador
    updateCharCount();
}

// Contador de caracteres
function updateCharCount() {
    const textarea = document.getElementById('contenido');
    const counter = document.getElementById('charCount');
    const counterDiv = document.getElementById('charCounter');
    const count = textarea.value.length;
    
    counter.textContent = count;
    
    // Cambiar color según cantidad
    counterDiv.classList.remove('warning', 'danger');
    if (count > 800) {
        counterDiv.classList.add('warning');
    }
    if (count > 950) {
        counterDiv.classList.add('danger');
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('contenido');
    textarea.addEventListener('input', updateCharCount);
    updateCharCount();
    
    // Establecer fecha máxima como hoy
    const fechaInput = document.getElementById('fechaInput');
    if (fechaInput) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.max = today;
    }
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        document.getElementById('diarioForm').submit();
    }
});
</script>

@endsection