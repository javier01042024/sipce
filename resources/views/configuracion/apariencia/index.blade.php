@extends('layouts.app')

@section('content')
<link href="{{ asset('css/apariencia.css') }}" rel="stylesheet">

<div class="apariencia-wrapper">

    <div class="page-header">
        <div class="header-content">
            <h1>
                <i class="fas fa-palette me-2"></i>
                Apariencia
            </h1>
            <p>Personaliza los colores del sistema y activa el modo nocturno a tu gusto</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('configuracion.apariencia.update') }}" id="aparienciaForm">
        @csrf
        @method('PUT')

        <input type="hidden" name="theme_color" id="themeColorInput" value="{{ $usuario->theme_color ?? '' }}">
        <input type="hidden" name="dark_mode" id="darkModeInput" value="{{ $usuario->dark_mode ? '1' : '0' }}">

        <div class="apariencia-grid">

            {{-- COLOR PRINCIPAL --}}
            <div class="apariencia-card">
                <h3 class="card-title">
                    <i class="fas fa-paint-brush" style="color: #667eea;"></i>
                    Color principal
                </h3>
                <p class="card-subtitle">Elige el color que identificará al sistema</p>

                <div class="swatches" id="colorSwatches">
                    <div class="swatch sw-empty {{ empty($usuario->theme_color) ? 'active' : '' }}"
                         data-color="" data-name="Predeterminado"
                         title="Color predeterminado"></div>
                    @foreach($colores as $nombre => $hex)
                        <div class="swatch {{ ($usuario->theme_color ?? null) === $hex ? 'active' : '' }}"
                             data-color="{{ $hex }}" data-name="{{ $nombre }}"
                             style="background: {{ $hex }};"
                             title="{{ $nombre }}"></div>
                    @endforeach
                </div>
            </div>

            {{-- MODO NOCTURNO --}}
            <div class="apariencia-card">
                <h3 class="card-title">
                    <i class="fas fa-moon" style="color: #6366f1;"></i>
                    Modo nocturno
                </h3>
                <p class="card-subtitle">Reduce el brillo para trabajar con menos fatiga visual</p>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-icon"><i class="fas fa-moon"></i></div>
                        <div>
                            <h4>Modo nocturno</h4>
                            <p>Activa los colores oscuros en todo el sistema</p>
                        </div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="darkModeToggle" {{ $usuario->dark_mode ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="dark-preview">
                    <div class="dark-preview-bar"><i class="fas fa-eye"></i> Vista previa</div>
                    <div class="dark-preview-body">
                        Así se verá el contenido del sistema
                        <div role="coment">Ajusta el interruptor para previsualizar el modo nocturno</div>
                    </div>
                </div>
            </div>

        </div>

        <button type="submit" class="btn-save-apariencia">
            <i class="fas fa-save"></i> Guardar apariencia
        </button>
    </form>

</div>

@push('scripts')
<script>
(function () {
    const swatches = document.querySelectorAll('.swatch');
    const themeInput = document.getElementById('themeColorInput');
    const darkToggle = document.getElementById('darkModeToggle');
    const darkInput = document.getElementById('darkModeInput');

    // Selección de color
    swatches.forEach(function (sw) {
        sw.addEventListener('click', function () {
            swatches.forEach(function (s) { s.classList.remove('active'); });
            sw.classList.add('active');
            themeInput.value = sw.dataset.color || '';
        });
    });

    // Toggle modo nocturno (vista previa + campo)
    function applyDarkPreview() {
        document.body.classList.toggle('theme-dark', darkToggle.checked);
        darkInput.value = darkToggle.checked ? '1' : '0';
    }
    darkToggle.addEventListener('change', applyDarkPreview);
    applyDarkPreview();
})();
</script>
@endpush

@endsection
