<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>SIPCE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $themeColor = optional(auth()->user())->theme_color ?: '#667eea';
        $themeDark = (bool) (optional(auth()->user())->dark_mode ?? false);
        // Oscurecer el color primario ~12% para el degradado
        $darken = function ($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) !== 6) { return $hex; }
            $rgb = array_map(function ($c) {
                return max(0, hexdec($c) - (int)(hexdec($c) * 0.12));
            }, str_split($hex, 2));
            return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
        };
        $toRgb = function ($hex) {
            $hex = ltrim($hex, '#');
            if (strlen($hex) !== 6) { return '102, 126, 234'; }
            $rgb = str_split($hex, 2);
            return hexdec($rgb[0]) . ', ' . hexdec($rgb[1]) . ', ' . hexdec($rgb[2]);
        };
        // Segundo color del degradado: manual si el usuario lo definió, si no se calcula automático
        $themeColorDark = optional(auth()->user())->theme_color_dark
            ?: $darken($themeColor);
        $themeColorRgb = $toRgb($themeColor);
    @endphp

    <style>
        :root {
            --sipce-primary: {{ $themeColor }};
            --sipce-primary-dark: {{ $themeColorDark }};
            --sipce-primary-rgb: {{ $themeColorRgb }};
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/theme-overrides.css') }}">

    <!-- Font Awesome -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/tables.css') }}">
    @stack('styles')


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $themeDark ? 'theme-dark' : '' }}">

    <!-- Sidebar -->
    @include('layouts.navigation')

    <!-- Contenido principal -->
    <div class="main-content" id="mainContent">
        @include('layouts.partials.topbar')
        @yield('content')
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <!-- JSZip para exportar a Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- pdfmake para exportar a PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

    <!-- Sistema unificado de alertas -->
    <script>
        window.SIPCE_SESSION = {
            success: @json(session('success')),
            error: @json(session('error')),
            validationErrors: @json($errors->any() ? $errors->all() : [])
        };
    </script>
    <script src="{{ asset('js/alerts.js') }}"></script>
    @stack('scripts')
</body>

</html>