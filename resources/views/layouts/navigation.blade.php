<button class="mobile-menu-btn" onclick="openMobileSidebar()" aria-label="Abrir menÃº">
    <i class="fas fa-bars"></i>
</button>

<div id="sidebarOverlay" class="sidebar-overlay" onclick="closeMobileSidebar()"></div>

<div id="sidebar" class="sidebar">

    <!-- LOGO -->
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

    <!-- MENU -->
    <nav class="nav">
        <a href="{{ route('dashboard') }}"
            class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>

        {{-- Pacientes --}}
        @if(auth()->user()->hasPermission('pacientes.index'))
        <a href="{{ route('pacientes.index') }}"
            class="nav-item {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Pacientes</span>
        </a>
        @endif

        {{-- Citas --}}
        @if(auth()->user()->hasPermission('citas.index'))
        <a href="{{ route('citas.index') }}"
            class="nav-item {{ request()->routeIs('citas.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Citas</span>
        </a>
        @endif

        {{-- Diarios --}}
        @if(auth()->user()->hasPermission('diarios.index'))
        <a href="{{ route('diarios.index') }}"
            class="nav-item {{ request()->routeIs('diarios.*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            <span>Diario</span>
        </a>
        @endif

        {{-- Planes de Tratamiento --}}
        @if(auth()->user()->hasPermission('pacientes.show'))
        <a href="{{ route('tratamiento.index') }}"
            class="nav-item {{ request()->routeIs('tratamiento.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Planes</span>
        </a>
        @endif

        {{-- Reportes --}}
        @if(auth()->user()->hasPermission('pacientes.index'))
        <a href="{{ route('reportes.index') }}"
            class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i>
            <span>Reportes</span>
        </a>
        @endif

        {{-- CONFIGURACIÃ“N CON SUBMENÃš (visible para todos: incluye Apariencia personal) --}}
        @if(auth()->user())
        <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
            <i class="fas fa-cog"></i>
            <span>ConfiguraciÃ³n</span>
            <i class="fas fa-chevron-down submenu-arrow"></i>
        </div>

        <div class="submenu">
            {{-- Apariencia (personal, disponible para todos) --}}
            <a href="{{ route('configuracion.apariencia.index') }}"
                class="submenu-item {{ request()->routeIs('configuracion.apariencia.*') ? 'active' : '' }}">
                <i class="fas fa-palette"></i>
                <span>Apariencia</span>
            </a>

            {{-- Usuarios y Roles --}}
            @if(auth()->user()->hasPermission('usuarios.index') || auth()->user()->hasPermission('roles.index'))
            <a href="{{ route('usuarios.index') }}"
                class="submenu-item {{ request()->routeIs('usuarios.*') || request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Usuarios y Roles</span>
            </a>
            @endif

            {{-- Respaldos --}}
            @if(auth()->user()->hasPermission('respaldos.index'))
            <a href="{{ route('configuracion.respaldos.index') }}"
                class="submenu-item {{ request()->routeIs('configuracion.respaldos.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i>
                <span>Respaldos</span>
            </a>
            @endif
            {{-- Estados --}}
            @if(auth()->user()->hasPermission('estados.index'))
            <a href="{{ route('configuracion.estados.index') }}"
                class="submenu-item {{ request()->routeIs('configuracion.estados.*') ? 'active' : '' }}">
                <i class="fas fa-tag"></i>
                <span>Estados</span>
            </a>
            @endif

            {{-- BitÃ¡cora --}}
            @if(auth()->user()->hasPermission('bitacora.index'))
            <a href="{{ route('configuracion.bitacora.index') }}"
                class="submenu-item {{ request()->routeIs('configuracion.bitacora.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>BitÃ¡cora</span>
            </a>
            @endif
        </div>
        @endif
    </nav>

    <!-- FOOTER -->
    <div class="sidebar-bottom">
        <a href="{{ route('notificaciones.index') }}"
           class="nav-item notif-bell {{ request()->routeIs('notificaciones.*') ? 'active' : '' }}"
           title="Notificaciones">
            <i class="fas fa-bell"></i>
            <span>Notificaciones</span>
            @php $noLeidas = \App\Models\Notificacion::where('user_id', auth()->id())->where('leida', false)->count(); @endphp
            @if($noLeidas > 0)
                <span class="notif-badge">{{ $noLeidas > 99 ? '99+' : $noLeidas }}</span>
            @endif
        </a>

        <div class="wifi-status" id="wifiStatus" title="Verificando conexiÃ³n...">
            <i class="fas fa-wifi" id="wifiIcon"></i>
            <span id="wifiLabel">Verificando...</span>
        </div>

        <button onclick="openLogoutModal()" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar sesiÃ³n</span>
        </button>
    </div>

</div>

<!-- MODAL DE CONFIRMACIÃ“N DE CIERRE DE SESIÃ“N -->
<div id="logoutModal" class="logout-modal-overlay">
    <div class="logout-modal-box">
        <div class="logout-modal-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h3>Cerrar sesiÃ³n</h3>
        <p>Â¿EstÃ¡s seguro que deseas salir del sistema?</p>
        <div class="logout-modal-actions">
            <button class="logout-btn logout-btn-ghost" onclick="closeLogoutModal()">
                Cancelar
            </button>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn logout-btn-danger">
                    Cerrar sesiÃ³n
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* RESET */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #f8fafc;
        overflow-x: hidden;
    }

    /* SIDEBAR - fija */
    .sidebar {
        width: 260px;
        height: 100vh;
        background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
        backdrop-filter: blur(10px);
        color: #e2e8f0;
        display: flex;
        flex-direction: column;
        padding: 20px 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.3);
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1000;
        overflow-y: auto;
        overflow-x: hidden;
    }

    /* Scrollbar personalizada */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(var(--sipce-primary-rgb), 0.5);
        border-radius: 4px;
    }

    /* TOP - logo estilo moderno */
    .sidebar-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        min-height: 52px;
        transition: all 0.3s ease;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
        background: linear-gradient(145deg, var(--sipce-primary), var(--sipce-primary-dark));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(var(--sipce-primary-rgb), 0.3);
    }

    .brand-icon i {
        font-size: 1.2rem;
        color: white;
    }

    .brand-text {
        font-weight: 700;
        font-size: 1.2rem;
        letter-spacing: 1px;
        background: linear-gradient(135deg, #fff 0%, #a78bfa 100%);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        white-space: nowrap;
        transition: all 0.3s ease;
    }

    .toggle {
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: #94a3b8;
        cursor: pointer;
        width: 32px;
        height: 32px;
        min-width: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        position: absolute;
        right: 16px;
        top: 20px;
        z-index: 2;
    }

    .toggle:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    /* NAV */
    .nav {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        color: #94a3b8;
        text-decoration: none;
        transition: all 0.25s ease;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        position: relative;
    }

    .nav-item i {
        width: 20px;
        font-size: 1.1rem;
        text-align: center;
    }

    .nav-item:hover {
        background: rgba(var(--sipce-primary-rgb), 0.15);
        color: white;
    }

    .nav-item.active {
        background: linear-gradient(90deg, var(--sipce-primary), var(--sipce-primary-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(var(--sipce-primary-rgb), 0.4);
    }

    /* FOOTER */
    .sidebar-bottom {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout {
        color: #f87171;
    }

    .logout:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #fecaca;
    }

    /* NOTIFICATION BELL */
    .notif-bell {
        position: relative;
    }
    .notif-badge {
        position: absolute;
        top: 4px;
        right: 12px;
        min-width: 18px;
        height: 18px;
        line-height: 18px;
        padding: 0 5px;
        background: #ef4444;
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        border-radius: 9px;
        text-align: center;
        pointer-events: none;
    }

    /* SUBMENU */
    .has-submenu {
        justify-content: flex-start;
    }

    .submenu-arrow {
        margin-left: auto;
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }

    .has-submenu.active-submenu .submenu-arrow {
        transform: rotate(180deg);
    }

    .submenu {
        display: none;
        flex-direction: column;
        gap: 2px;
        margin-left: 20px;
        padding-left: 12px;
        border-left: 2px solid rgba(var(--sipce-primary-rgb), 0.3);
    }

    .submenu.show {
        display: flex;
    }

    .submenu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 10px;
        color: #94a3b8;
        text-decoration: none;
        transition: all 0.25s ease;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .submenu-item i {
        width: 18px;
        font-size: 1rem;
    }

    .submenu-item:hover {
        background: rgba(var(--sipce-primary-rgb), 0.15);
        color: white;
    }

    .submenu-item.active {
        background: linear-gradient(90deg, var(--sipce-primary), var(--sipce-primary-dark));
        color: white;
        box-shadow: 0 4px 12px rgba(var(--sipce-primary-rgb), 0.3);
    }

    /* SIDEBAR COLAPSADA - VERSIÃ“N 1: Apilado vertical */
    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar.collapsed .sidebar-top {
        flex-direction: column;
        gap: 16px;
        justify-content: center;
        align-items: center;
        padding-bottom: 20px;
    }

    .sidebar.collapsed .brand {
        justify-content: center;
        width: 100%;
    }

    .sidebar.collapsed .brand-text,
    .sidebar.collapsed .nav-item span,
    .sidebar.collapsed .submenu,
    .sidebar.collapsed .submenu-arrow {
        display: none;
    }

    .sidebar.collapsed .nav-item {
        justify-content: center;
        padding: 12px;
    }

    .sidebar.collapsed .nav-item i {
        margin: 0;
        font-size: 1.2rem;
    }

    .sidebar.collapsed .toggle {
        position: relative;
        right: auto;
        top: auto;
        margin: 0 auto;
    }

    .sidebar.collapsed .toggle i {
        transform: rotate(180deg);
    }

    /* CONTENIDO PRINCIPAL */
    .main-content {
        min-height: 100vh;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 20px;
        background: #f8fafc;
        margin-left: 260px;
    }

    .main-content.sidebar-collapsed {
        margin-left: 80px;
    }

    /* MODAL LOGOUT */
    .logout-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999999;
    }

    .logout-modal-overlay.show {
        display: flex;
        animation: fadeInLogoutModal 0.2s ease;
    }

    @keyframes fadeInLogoutModal {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .logout-modal-box {
        background: white;
        padding: 2rem;
        border-radius: 24px;
        width: 380px;
        max-width: 90%;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .logout-modal-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(145deg, var(--sipce-primary), var(--sipce-primary-dark));
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .logout-modal-icon i {
        font-size: 2rem;
        color: white;
    }

    .logout-modal-box h3 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .logout-modal-box p {
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }

    .logout-modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .logout-btn {
        padding: 10px 20px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .logout-btn-ghost {
        background: #f1f5f9;
        color: #475569;
    }

    .logout-btn-ghost:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .logout-btn-danger {
        background: linear-gradient(105deg, #ef4444, #dc2626);
        color: white;
    }

    .logout-btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* WIFI STATUS */
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

    /* Verde: conectado + internet */
    .wifi-status.online {
        color: #22c55e;
    }

    .wifi-status.online i {
        color: #22c55e;
        text-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
    }

    /* Rojo: conectado pero sin internet */
    .wifi-status.offline {
        color: #ef4444;
    }

    .wifi-status.offline i {
        color: #ef4444;
        text-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
    }

    /* Gris: sin conexiÃ³n de red */
    .wifi-status.disconnected {
        color: #64748b;
    }

    .wifi-status.disconnected i {
        color: #64748b;
    }

    /* Sidebar colapsada */
    .sidebar.collapsed .wifi-status span {
        display: none;
    }

    .sidebar.collapsed .wifi-status {
        justify-content: center;
        padding: 10px;
    }

    /* ============================================
       RESPONSIVE MÃ“VILES / TABLETS PORTRAIT
       ============================================ */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(2, 6, 23, 0.65);
        backdrop-filter: blur(3px);
        z-index: 999;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            width: 280px;
            z-index: 1001;
        }

        .sidebar.mobile-open {
            transform: translateX(0);
            box-shadow: 8px 0 30px rgba(0, 0, 0, 0.5);
        }

        .main-content,
        .main-content.sidebar-collapsed {
            margin-left: 0;
            padding: 14px;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* BotÃ³n flotante para abrir el menÃº en mÃ³vil */
        .mobile-menu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(var(--sipce-primary-rgb), 0.4);
            font-size: 1.1rem;
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 998;
            transition: all 0.25s ease;
        }

        .mobile-menu-btn:hover {
            transform: translateY(-2px);
        }
    }

    /* Ocultar botÃ³n mÃ³vil en pantallas grandes */
    @media (min-width: 769px) {
        .mobile-menu-btn {
            display: none;
        }
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        sidebar.classList.toggle('collapsed');
        if (mainContent) {
            mainContent.classList.toggle('sidebar-collapsed');
        }
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    }

    function openMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('collapsed');
        localStorage.removeItem('sidebarCollapsed');
        sidebar.classList.add('mobile-open');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    // Cerrar sidebar mÃ³vil al navegar (se re-renderiza la pÃ¡gina completa)
    function handleMobileNav() {
        if (window.innerWidth <= 768) closeMobileSidebar();
    }

    function toggleSubmenu(element) {
        element.classList.toggle('active-submenu');
        const submenu = element.nextElementSibling;
        submenu.classList.toggle('show');
    }

    function openLogoutModal() {
        document.getElementById('logoutModal').classList.add('show');
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        if (sidebar && mainContent) {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            }
        }

        // Cerrar modal al hacer clic fuera
        const logoutModal = document.getElementById('logoutModal');
        if (logoutModal) {
            logoutModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeLogoutModal();
                }
            });
        }

        // Abrir submenÃº si hay una ruta activa dentro
        const activeSubmenuItem = document.querySelector('.submenu-item.active');
        if (activeSubmenuItem) {
            const submenu = activeSubmenuItem.closest('.submenu');
            const parentItem = submenu.previousElementSibling;
            if (parentItem && parentItem.classList.contains('has-submenu')) {
                parentItem.classList.add('active-submenu');
                submenu.classList.add('show');
            }
        }
    });

    // Cerrar con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLogoutModal();
        }
    });

    // =============================================
    // INDICADOR DE CONEXIÃ“N WiFi
    // =============================================
    (function() {
        const icon = document.getElementById('wifiIcon');
        const label = document.getElementById('wifiLabel');
        const status = document.getElementById('wifiStatus');
        if (!icon || !label || !status) return;

        const ENDPOINT = '{{ url("up") }}';
        const TIMEOUT_MS = 4000;

        function setOnline() {
            status.className = 'wifi-status online';
            icon.className = 'fas fa-wifi';
            label.textContent = 'Conectado';
            status.title = 'ConexiÃ³n estable';
        }

        function setOffline() {
            status.className = 'wifi-status offline';
            icon.className = 'fas fa-wifi';
            label.textContent = 'Sin internet';
            status.title = 'Conectado pero sin acceso a internet';
        }

        function setDisconnected() {
            status.className = 'wifi-status disconnected';
            icon.className = 'fas fa-wifi';
            label.textContent = 'Sin conexiÃ³n';
            status.title = 'No hay conexiÃ³n de red';
        }

        async function checkConnection() {
            // Primer nivel: verificar navigator.onLine
            if (!navigator.onLine) {
                setDisconnected();
                return;
            }

            // Segundo nivel: intentar hacer fetch al health check del servidor
            try {
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), TIMEOUT_MS);
                const resp = await fetch(ENDPOINT, {
                    method: 'HEAD',
                    signal: controller.signal,
                    cache: 'no-store'
                });
                clearTimeout(timeout);

                if (resp.ok) {
                    setOnline();
                } else {
                    setOffline();
                }
            } catch (e) {
                // Fetch fallÃ³ = hay red local pero no internet/servidor
                setOffline();
            }
        }

        // Verificar al cargar
        checkConnection();

        // Escuchar eventos de red del navegador
        window.addEventListener('online', () => {
            checkConnection();
        });

        window.addEventListener('offline', () => {
            setDisconnected();
        });

        // Polling cada 15 segundos como respaldo
        setInterval(checkConnection, 15000);
    })();
</script>