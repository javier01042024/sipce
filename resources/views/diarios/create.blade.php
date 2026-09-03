@extends('layouts.app')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/diarios-form.css') }}">
@endpush

<div class="diario-create-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="page-header">
        <div>
            <h1>
                <i class="fas fa-book-medical me-2"></i>
                Nuevo Registro Diario
            </h1>
            <p>Documenta tu seguimiento emocional y terapéutico</p>
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

                <!-- USUARIO DE LA SESIÓN (OCULTO) -->
                @php
                    $usuarioActual = auth()->user();
                @endphp

                <input type="hidden" name="user_id" value="{{ $usuarioActual->id }}">
                
                <!-- INFO DEL USUARIO (SOLO LECTURA) -->
                <div class="usuario-session-info">
                    <div class="usuario-avatar">
                        {{ strtoupper(substr($usuarioActual->name, 0, 1)) }}
                    </div>
                    <div class="usuario-details">
                        <h4>{{ $usuarioActual->name }}</h4>
                        <p>
                            <span>
                                <i class="fas fa-envelope"></i>
                                {{ $usuarioActual->email }}
                            </span>
                            <span>
                                <i class="fas fa-id-badge"></i>
                                ID: #{{ $usuarioActual->id }}
                            </span>
                        </p>
                    </div>
                </div>

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
                        <div class="emocion-option" onclick="selectEmocion(this, 'positivo', '😊 Me siento feliz, con energía positiva')">
                            <span class="emoji">😊</span>
                            <span class="label">Feliz</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'positivo', '😄 Me siento muy bien, alegre y contento')">
                            <span class="emoji">😄</span>
                            <span class="label">Alegre</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'positivo', '😌 Me siento tranquilo y en calma')">
                            <span class="emoji">😌</span>
                            <span class="label">Tranquilo</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'neutral', '😐 Me siento estable, sin cambios significativos')">
                            <span class="emoji">😐</span>
                            <span class="label">Neutral</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'neutral', '😕 Me siento confundido, sin saber qué pensar')">
                            <span class="emoji">😕</span>
                            <span class="label">Confundido</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'negativo', '😢 Me siento triste y desanimado')">
                            <span class="emoji">😢</span>
                            <span class="label">Triste</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'negativo', '😠 Me siento enojado o irritado')">
                            <span class="emoji">😠</span>
                            <span class="label">Enojado</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'negativo', '😰 Me siento ansioso o nervioso')">
                            <span class="emoji">😰</span>
                            <span class="label">Ansioso</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'negativo', '😨 Me siento asustado o con miedo')">
                            <span class="emoji">😨</span>
                            <span class="label">Asustado</span>
                        </div>
                        <div class="emocion-option" onclick="selectEmocion(this, 'negativo', '😫 Me siento cansado o agotado')">
                            <span class="emoji">😫</span>
                            <span class="label">Cansado</span>
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
                                  placeholder="Describe cómo ha sido el día de hoy, tus emociones, pensamientos, situaciones relevantes..."
                                  required></textarea>
                    </div>

                    <!-- CONTADOR DE CARACTERES -->
                    <div class="char-counter" id="charCounter">
                        <span id="charCount">0</span> / 1000 caracteres
                    </div>

                    <!-- GUÍA PARA COMENZAR A ESCRIBIR -->
                    <div class="quick-suggestions">
                        <span class="suggestion-badge" onclick="agregarSugerencia('1. Pensamiento: ')">
                            <i class="fas fa-brain"></i> 1. Pensamiento
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('2. Situación: ')">
                            <i class="fas fa-map-pin"></i> 2. Situación
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('3. Emoción: ')">
                            <i class="fas fa-smile"></i> 3. Emoción
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('4. Conducta: ')">
                            <i class="fas fa-running"></i> 4. Conducta
                        </span>
                        <span class="suggestion-badge" onclick="agregarSugerencia('5. Del 0 al 10, ¿cómo me sentí? ')">
                            <i class="fas fa-tachometer-alt"></i> 5. Del 0 al 10 me sentí
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

@push('scripts')
<script src="{{ asset('js/diarios-form.js') }}"></script>
@endpush

@endsection
