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
                Editar Registro Diario
            </h1>
            <p>Actualiza el seguimiento emocional y terapéutico</p>
        </div>

        <a href="{{ route('diarios.show', $diario) }}" class="btn-volver">
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
                Editar registro #{{ $diario->id }}
            </h2>
        </div>

        <!-- BODY -->
        <div class="card-body-custom">

            <form method="POST" action="{{ route('diarios.update', $diario) }}" id="diarioForm">
                @csrf
                @method('PUT')

                <!-- USUARIO DE LA ENTRADA (OCULTO) -->
                <input type="hidden" name="user_id" value="{{ $diario->user_id }}">

                <!-- INFO DEL USUARIO (SOLO LECTURA) -->
                <div class="usuario-session-info">
                    <div class="usuario-avatar">
                        {{ strtoupper(substr($diario->user->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="usuario-details">
                        <h4>{{ $diario->user->name ?? 'Usuario' }}</h4>
                        <p>
                            <span>
                                <i class="fas fa-envelope"></i>
                                {{ $diario->user->email ?? '' }}
                            </span>
                            <span>
                                <i class="fas fa-id-badge"></i>
                                ID: #{{ $diario->user_id }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- FECHA DEL REGISTRO -->
                @php
                    $fechaFormateada = \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y');
                    $diaSemana = \Carbon\Carbon::parse($diario->fecha)->locale('es')->dayName;
                @endphp

                <input type="hidden" name="fecha" value="{{ \Carbon\Carbon::parse($diario->fecha)->format('Y-m-d') }}" id="fechaInput">

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
                        <i class="fas fa-calendar me-1"></i>
                        <small>Fecha del registro</small>
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
                                  placeholder="Describe cómo ha sido el día, tus emociones, pensamientos, situaciones relevantes..."
                                  required>{{ old('contenido', $diario->contenido) }}</textarea>
                    </div>

                    <!-- CONTADOR DE CARACTERES -->
                    <div class="char-counter" id="charCounter">
                        <span id="charCount">{{ mb_strlen(old('contenido', $diario->contenido)) }}</span> / 1000 caracteres
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
                    <a href="{{ route('diarios.show', $diario) }}" class="btn btn-cancel">
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

@push('scripts')
<script src="{{ asset('js/diarios-form.js') }}"></script>
@endpush

@endsection
