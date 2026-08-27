@extends('layouts.app')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/diarios-show.css') }}">
@endpush

<div class="diario-show-wrapper">

    <!-- BOTÓN VOLVER -->
    <a href="{{ route('diarios.index') }}" class="btn-volver">
        <i class="fas fa-arrow-left"></i>
        Volver al listado
    </a>

    <!-- HEADER DEL DIARIO -->
    <div class="diario-show-header">
        <div class="header-top">
            <div class="user-info-show">
                <div class="user-avatar-large">
                    {{ strtoupper(substr($diario->user->name, 0, 1)) }}
                </div>
                <div class="user-detail-show">
                    <h2>{{ $diario->user->name }}</h2>
                    <div class="fecha-display">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            @php
                // Determinar emoción basada en el contenido
                $emocion = 'neutral';
                $emocionIcon = '😐';
                $emocionTexto = 'Neutral';
                
                if(str_contains(strtolower($diario->contenido), 'bien') || 
                   str_contains(strtolower($diario->contenido), 'feliz') ||
                   str_contains(strtolower($diario->contenido), 'contento') ||
                   str_contains(strtolower($diario->contenido), 'alegre') ||
                   str_contains(strtolower($diario->contenido), 'agradecido')) {
                    $emocion = 'positivo';
                    $emocionIcon = '😊';
                    $emocionTexto = 'Positivo';
                } elseif(str_contains(strtolower($diario->contenido), 'mal') || 
                         str_contains(strtolower($diario->contenido), 'triste') ||
                         str_contains(strtolower($diario->contenido), 'ansioso') ||
                         str_contains(strtolower($diario->contenido), 'deprimido') ||
                         str_contains(strtolower($diario->contenido), 'enojado')) {
                    $emocion = 'negativo';
                    $emocionIcon = '😔';
                    $emocionTexto = 'Negativo';
                }
            @endphp

            <div class="emocion-badge-large {{ $emocion }}">
                <span class="emocion-icon">{{ $emocionIcon }}</span>
                Estado: {{ $emocionTexto }}
            </div>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="diario-content-card">
        <!-- CONTENIDO DEL DIARIO -->
        <div class="content-section">
            <div class="content-label">
                <i class="fas fa-pen-fancy"></i>
                Contenido del registro
            </div>
            <div class="contenido-texto">
                {{ $diario->contenido }}
            </div>
        </div>

        <!-- METADATOS -->
        <div class="metadata-section">
            <div class="metadata-item">
                <div class="metadata-icon created">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="metadata-text">
                    <h5>Creado</h5>
                    <span>{{ \Carbon\Carbon::parse($diario->created_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if($diario->updated_at != $diario->created_at)
            <div class="metadata-item">
                <div class="metadata-icon updated">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="metadata-text">
                    <h5>Última edición</h5>
                    <span>{{ \Carbon\Carbon::parse($diario->updated_at)->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            @endif

            <div class="metadata-item">
                <div class="metadata-icon user">
                    <i class="fas fa-user"></i>
                </div>
                <div class="metadata-text">
                    <h5>Usuario</h5>
                    <span>{{ $diario->user->name }}</span>
                </div>
            </div>

            <div class="metadata-item">
                <div class="metadata-icon created">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="metadata-text">
                    <h5>Fecha del registro</h5>
                    <span>{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- ACCIONES -->
        <div class="actions-section">
            <div class="actions-left">
                <a href="{{ route('diarios.index') }}" class="btn-accion volver-listado">
                    <i class="fas fa-list"></i>
                    Volver al listado
                </a>
            </div>

            <div class="actions-right">
                <a href="{{ route('diarios.edit', $diario) }}" class="btn-accion editar">
                    <i class="fas fa-pen"></i>
                    Editar registro
                </a>

                @if(Auth::id() == 1 || Auth::id() == $diario->user_id)
                <button type="button" class="btn-accion eliminar" id="btnEliminarDiario" 
                        data-id="{{ $diario->id }}" 
                        data-fecha="{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}">
                    <i class="fas fa-trash"></i>
                    Eliminar
                </button>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
// Pasar variables de PHP a JavaScript
var diarioId = @json($diario->id);
var diarioFecha = @json(\Carbon\Carbon::parse($diario->fecha)->format('d/m/Y'));
var deleteUrl = @json(route('diarios.destroy', $diario));
var indexUrl = @json(route('diarios.index'));
</script>
<script src="{{ asset('js/diarios-show.js') }}"></script>
@endpush

@endsection
