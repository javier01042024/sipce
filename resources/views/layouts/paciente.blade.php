<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE - Portal del Paciente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/tables.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div id="sidebar" class="sidebar">
        <div class="sidebar-top">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <span class="brand-text">SIPCE</span>
            </div>
            <button class="toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="nav">
            <a href="{{ route('paciente.dashboard') }}" class="nav-item {{ request()->routeIs('paciente.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
            <a href="{{ route('paciente.mis-citas') }}" class="nav-item {{ request()->routeIs('paciente.mis-citas') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Mis Citas</span>
            </a>
            <a href="{{ route('paciente.mi-diario') }}" class="nav-item {{ request()->routeIs('paciente.mi-diario*') ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>
                <span>Mi Diario</span>
            </a>
            <a href="{{ route('paciente.perfil') }}" class="nav-item {{ request()->routeIs('paciente.perfil') ? 'active' : '' }}">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
            <a href="{{ route('paciente.actividad') }}" class="nav-item {{ request()->routeIs('paciente.actividad') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Mi Actividad</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="wifi-status" id="wifiStatus" title="Verificando conexión...">
                <i class="fas fa-wifi" id="wifiIcon"></i>
                <span id="wifiLabel">Verificando...</span>
            </div>
            <button onclick="openLogoutModal()" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar sesión</span>
            </button>
        </div>
    </div>

    <div id="logoutModal" class="logout-modal-overlay">
        <div class="logout-modal-box">
            <div class="logout-modal-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <h3>Cerrar sesión</h3>
            <p>¿Estás seguro que deseas salir del sistema?</p>
            <div class="logout-modal-actions">
                <button class="logout-btn logout-btn-ghost" onclick="closeLogoutModal()">Cancelar</button>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="logout-btn logout-btn-danger">Cerrar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        @yield('content')
    </div>

    <style>
        .wifi-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: default;
            margin-bottom: 4px;
        }
        .wifi-status i {
            font-size: 1.05rem;
            transition: color 0.3s ease;
        }
        .wifi-status.online { color: #22c55e; }
        .wifi-status.online i { color: #22c55e; text-shadow: 0 0 8px rgba(34, 197, 94, 0.5); }
        .wifi-status.offline { color: #ef4444; }
        .wifi-status.offline i { color: #ef4444; text-shadow: 0 0 8px rgba(239, 68, 68, 0.5); }
        .wifi-status.disconnected { color: #64748b; }
        .wifi-status.disconnected i { color: #64748b; }
        .sidebar.collapsed .wifi-status span { display: none; }
        .sidebar.collapsed .wifi-status { justify-content: center; padding: 10px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.SIPCE_SESSION = {
            success: @json(session('success')),
            error: @json(session('error'))
        };
    </script>
    <script src="{{ asset('js/alerts.js') }}"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
        function openLogoutModal() { document.getElementById('logoutModal').classList.add('show'); }
        function closeLogoutModal() { document.getElementById('logoutModal').classList.remove('show'); }
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            if (sidebar && mainContent && localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            }
            document.getElementById('logoutModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeLogoutModal();
            });
        });

        // WiFi Status
        (function() {
            const icon = document.getElementById('wifiIcon');
            const label = document.getElementById('wifiLabel');
            const status = document.getElementById('wifiStatus');
            if (!icon || !label || !status) return;
            const ENDPOINT = '{{ url("up") }}';
            const TIMEOUT_MS = 4000;
            function setOnline() { status.className = 'wifi-status online'; icon.className = 'fas fa-wifi'; label.textContent = 'Conectado'; status.title = 'Conexión estable'; }
            function setOffline() { status.className = 'wifi-status offline'; icon.className = 'fas fa-wifi'; label.textContent = 'Sin internet'; status.title = 'Conectado pero sin acceso a internet'; }
            function setDisconnected() { status.className = 'wifi-status disconnected'; icon.className = 'fas fa-wifi'; label.textContent = 'Sin conexión'; status.title = 'No hay conexión de red'; }
            async function checkConnection() {
                if (!navigator.onLine) { setDisconnected(); return; }
                try {
                    const controller = new AbortController();
                    const timeout = setTimeout(() => controller.abort(), TIMEOUT_MS);
                    const resp = await fetch(ENDPOINT, { method: 'HEAD', signal: controller.signal, cache: 'no-store' });
                    clearTimeout(timeout);
                    resp.ok ? setOnline() : setOffline();
                } catch (e) { setOffline(); }
            }
            checkConnection();
            window.addEventListener('online', () => checkConnection());
            window.addEventListener('offline', () => setDisconnected());
            setInterval(checkConnection, 15000);
        })();
    </script>
    @stack('scripts')
</body>
</html>
