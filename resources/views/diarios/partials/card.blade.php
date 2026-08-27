{{-- diarios/partials/card.blade.php --}}
@php
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

<div class="diario-card" data-usuario="{{ $diario->user_id }}" data-fecha="{{ $diario->fecha }}" data-emocion="{{ $emocion }}" id="diario-row-{{ $diario->id }}">
    <div class="diario-header-card">
        <div class="user-info">
            <div class="user-avatar-small">
                {{ strtoupper(substr($diario->user->name, 0, 1)) }}
            </div>
            <div class="user-detalle">
                <h4>{{ $diario->user->name }}</h4>
                <span class="fecha-badge">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div class="emocion-badge {{ $emocion }}">
            <span>{{ $emocionIcon }}</span>
            {{ $emocionTexto }}
        </div>
    </div>

    <div class="diario-body">
        <div class="diario-contenido">
            <i class="fas fa-quote-left me-2" style="color: #cbd5e1; font-size: 12px;"></i>
            {{ Str::limit($diario->contenido, 300) }}
        </div>

        <div class="diario-footer">
            <div class="diario-tags">
                <span class="tag">
                    <i class="far fa-clock"></i>
                    {{ \Carbon\Carbon::parse($diario->created_at)->diffForHumans() }}
                </span>
                @if($diario->updated_at != $diario->created_at)
                    <span class="tag">
                        <i class="fas fa-edit"></i>
                        Editado
                    </span>
                @endif
            </div>

            <div class="diario-actions">
                <a href="{{ route('diarios.show', $diario) }}" class="btn-icon-small view" title="Ver detalles">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('diarios.edit', $diario) }}" class="btn-icon-small edit" title="Editar">
                    <i class="fas fa-pen"></i>
                </a>
                <button class="btn-icon-small delete btn-eliminar-diario"
                        data-id="{{ $diario->id }}"
                        data-fecha="{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}"
                        title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
