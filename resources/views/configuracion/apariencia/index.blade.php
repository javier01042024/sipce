@extends('layouts.app')

@section('content')
@php
    $usuario = auth()->user();
    $primary = $usuario->theme_color ?: '#667eea';
    $hv = ltrim($primary, '#');
    $darkenDefault = $hv;
    if (strlen($hv) === 6) {
        $parts = str_split($hv, 2);
        $darkenDefault = sprintf('#%02x%02x%02x',
            max(0, hexdec($parts[0]) - (int)(hexdec($parts[0]) * 0.12)),
            max(0, hexdec($parts[1]) - (int)(hexdec($parts[1]) * 0.12)),
            max(0, hexdec($parts[2]) - (int)(hexdec($parts[2]) * 0.12))
        );
    }
@endphp
<link href="{{ asset('css/apariencia.css') }}" rel="stylesheet">

<div class="apariencia-wrapper">

    <div class="page-header">
        <div class="header-content">
            <h1>
                <i class="fas fa-palette me-2"></i>
                Apariencia
            </h1>
            <p>Personaliza completamente los colores del sistema: color principal, degradado y modo nocturno</p>
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

        <input type="hidden" name="theme_color" id="themeColorInput" value="{{ $usuario->theme_color ?? '#667eea' }}">
        <input type="hidden" name="theme_color_dark" id="themeColorDarkInput" value="{{ $usuario->theme_color_dark ?? '' }}">
        <input type="hidden" name="dark_mode" id="darkModeInput" value="{{ $usuario->dark_mode ? '1' : '0' }}">

        <div class="apariencia-grid">

            {{-- COLOR PRINCIPAL (selector libre) --}}
            <div class="apariencia-card">
                <h3 class="card-title">
                    <i class="fas fa-paint-brush" style="color: var(--sipce-primary);"></i>
                    Color principal
                </h3>
                <p class="card-subtitle">Elige cualquier color. Se aplica a todo el sistema (logo, menú, botones, cabeceras, tablas, modales).</p>

                <div class="color-picker-row">
                    <div class="custom-color-control">
                        <label class="color-input-wrap" id="customColorWrap" title="Elegir color personalizado">
                            <input type="color" id="customColorPicker" value="{{ $usuario->theme_color ?? '#667eea' }}">
                        </label>
                        <div class="hex-field">
                            <label for="hexInput">Código hexadecimal</label>
                            <input type="text" id="hexInput" class="hex-input" maxlength="7"
                                   placeholder="#667eea" value="{{ $usuario->theme_color ?? '#667eea' }}">
                        </div>
                    </div>
                </div>

                <div class="swatch-sep">O elige rápido un color sugerido</div>
                <div class="swatches" id="colorSwatches">
                    <div class="swatch sw-empty {{ empty($usuario->theme_color) || ($usuario->theme_color ?? '#667eea') === '#667eea' ? 'active' : '' }}"
                         data-color="#667eea" data-name="Predeterminado"
                         title="Predeterminado SIPCE"></div>
                    @foreach($colores as $nombre => $hex)
                        <div class="swatch {{ ($usuario->theme_color ?? '#667eea') === $hex ? 'active' : '' }}"
                             data-color="{{ $hex }}" data-name="{{ $nombre }}"
                             style="background: {{ $hex }};"
                             title="{{ $nombre }}"></div>
                    @endforeach
                </div>
            </div>

            {{-- DEGRADADO PERSONALIZABLE --}}
            <div class="apariencia-card">
                <h3 class="card-title">
                    <i class="fas fa-fill-drip" style="color: var(--sipce-primary);"></i>
                    Degradado
                </h3>
                <p class="card-subtitle">Define el segundo color del degradado. Si lo dejas automático, el sistema lo calcula solo.</p>

                <div class="gradient-block">
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-icon"><i class="fas fa-fill-drip"></i></div>
                            <div>
                                <h4>Degradado personalizado</h4>
                                <p>Usar un segundo color elegido a mano</p>
                            </div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" id="customGradientToggle"
                                   {{ !empty($usuario->theme_color_dark) ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="gradient-controls {{ !empty($usuario->theme_color_dark) ? 'visible' : '' }}" id="gradientControls">
                        <div class="gradient-preview-label">Color final del degradado</div>
                        <label class="color-input-wrap" id="gradientColorWrap" title="Elegir color final">
                            <input type="color" id="gradientColorPicker" value="{{ $usuario->theme_color_dark ?? '#000000' }}">
                        </label>
                        <div class="hex-field">
                            <label for="gradientHex">Código hexadecimal</label>
                            <input type="text" id="gradientHex" class="hex-input" maxlength="7"
                                   placeholder="#666" value="{{ $usuario->theme_color_dark ?? '' }}">
                        </div>
                    </div>

                    <p class="auto-gradient-note" id="autoGradientNote">
                        <i class="fas fa-magic"></i>
                        Automático: el degradado se calcula oscureciendo el color principal (~12%).
                    </p>

                    <div class="gradient-preview" id="gradientPreview" style="--grad-from: {{ $usuario->theme_color ?? '#667eea' }}; --grad-to: {{ $usuario->theme_color_dark ?: $darkenDefault }};"></div>
                    <div class="gradient-preview-label" style="margin-top:6px;">Así se verá el degradado del sistema</div>
                </div>
            </div>

        </div>

        {{-- MODO NOCTURNO --}}
        <div class="apariencia-card" style="margin-top:25px;">
            <h3 class="card-title">
                <i class="fas fa-moon" style="color: var(--sipce-primary);"></i>
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

        <button type="submit" class="btn-save-apariencia">
            <i class="fas fa-save"></i> Guardar apariencia
        </button>
    </form>

</div>

@push('scripts')
<script>
(function () {
    const form = document.getElementById('aparienciaForm');
    const swatches = document.querySelectorAll('.swatch');
    const themeInput = document.getElementById('themeColorInput');
    const themeDarkInput = document.getElementById('themeColorDarkInput');
    const customColorWrap = document.getElementById('customColorWrap');
    const customPicker = document.getElementById('customColorPicker');
    const hexInput = document.getElementById('hexInput');
    const gradientToggle = document.getElementById('customGradientToggle');
    const gradientControls = document.getElementById('gradientControls');
    const gradientPicker = document.getElementById('gradientColorPicker');
    const gradientHex = document.getElementById('gradientHex');
    const gradientPreview = document.getElementById('gradientPreview');
    const autoGradientNote = document.getElementById('autoGradientNote');
    const darkToggle = document.getElementById('darkModeToggle');
    const darkInput = document.getElementById('darkModeInput');

    function normalizeHex(v) {
        v = String(v || '').trim();
        if (v.charAt(0) !== '#') v = '#' + v;
        if (!/^#[0-9a-fA-F]{6}$/.test(v)) return null;
        return v.toLowerCase();
    }

    function setCustomActive() {
        customColorWrap.classList.add('active');
        swatches.forEach(function (s) { s.classList.remove('active'); });
    }

    // Aplicar el tema en vivo sobre el documento (preview)
    function applyThemeVars(primary, dark) {
        const rgb = hexToRgb(primary || '#667eea');
        document.documentElement.style.setProperty('--sipce-primary', primary);
        document.documentElement.style.setProperty('--sipce-primary-dark', dark);
        document.documentElement.style.setProperty('--sipce-primary-rgb', rgb);
    }
    function hexToRgb(hex) {
        hex = normalizeHex(hex) || '#667eea';
        const n = parseInt(hex.slice(1), 16);
        return (n >> 16 & 255) + ', ' + (n >> 8 & 255) + ', ' + (n & 255);
    }
    function darkenHex(hex) {
        const norm = normalizeHex(hex) || '#667eea';
        const n = parseInt(norm.slice(1), 16);
        const dr = Math.max(0, (n >> 16 & 255) - Math.floor((n >> 16 & 255) * 0.12));
        const dg = Math.max(0, (n >> 8 & 255) - Math.floor((n >> 8 & 255) * 0.12));
        const db = Math.max(0, (n & 255) - Math.floor((n & 255) * 0.12));
        return '#' + ((dr << 16) | (dg << 8) | db).toString(16).padStart(6, '0');
    }
    function updateTheme() {
        const primary = normalizeHex(themeInput.value) || '#667eea';
        const darkInputVal = normalizeHex(themeDarkInput.value);
        const dark = gradientToggle.checked && darkInputVal ? darkInputVal : darkenHex(primary);
        applyThemeVars(primary, dark);
        gradientPreview.style.setProperty('--grad-from', primary);
        gradientPreview.style.setProperty('--grad-to', dark);
    }

    // Swatches predeterminados
    swatches.forEach(function (sw) {
        sw.addEventListener('click', function () {
            swatches.forEach(function (s) { s.classList.remove('active'); });
            sw.classList.add('active');
            customColorWrap.classList.remove('active');
            const color = sw.dataset.color || '#667eea';
            themeInput.value = color;
            customPicker.value = color;
            hexInput.value = '#' + color.replace('#', '');
            updateTheme();
        });
    });

    // Selector de color libre
    customPicker.addEventListener('input', function () {
        const v = customPicker.value;
        themeInput.value = v;
        hexInput.value = v;
        hexInput.value = '#' + v.replace('#', '');
        updateTheme();
        setCustomActive();
    });

    // Campo hex editable
    hexInput.addEventListener('input', function () {
        const v = normalizeHex(hexInput.value);
        if (v) {
            themeInput.value = v;
            customPicker.value = v;
            updateTheme();
            setCustomActive();
        }
    });
    hexInput.addEventListener('blur', function () {
        if (!normalizeHex(hexInput.value)) {
            hexInput.value = '#' + (themeInput.value || '#667eea').replace('#', '');
        }
    });

    // Degradado personalizado
    gradientToggle.addEventListener('change', function () {
        gradientControls.classList.toggle('visible', gradientToggle.checked);
        if (!gradientToggle.checked) {
            themeDarkInput.value = '';
            updateTheme();
        } else {
            const d = normalizeHex(gradientHex.value);
            if (d) { themeDarkInput.value = d; }
            updateTheme();
            if (!gradientPicker.value) gradientPicker.value = darkenHex(themeInput.value || '#667eea');
        }
    });

    gradientPicker.addEventListener('input', function () {
        gradientHex.value = '#' + gradientPicker.value.replace('#', '');
        themeDarkInput.value = gradientPicker.value;
        updateTheme();
    });
    gradientHex.addEventListener('input', function () {
        const v = normalizeHex(gradientHex.value);
        if (v) {
            themeDarkInput.value = v;
            gradientPicker.value = v;
            updateTheme();
        }
    });

    // Toggle modo nocturno (vista previa + campo)
    function applyDarkPreview() {
        document.body.classList.toggle('theme-dark', darkToggle.checked);
        darkInput.value = darkToggle.checked ? '1' : '0';
    }
    darkToggle.addEventListener('change', function () {
        applyDarkPreview();
        updateTheme();
    });

    // Inicialización
    applyDarkPreview();
    updateTheme();
})();
</script>
@endpush

@endsection
