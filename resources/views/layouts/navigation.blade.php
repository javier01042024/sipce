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

        <a href="{{ route('pacientes.index') }}"
            class="nav-item {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Pacientes</span>
        </a>

        <a href="{{ route('citas.index') }}" 
            class="nav-item {{ request()->routeIs('citas.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Citas</span>
        </a>

        <a href="{{ route('diarios.index') }}" 
            class="nav-item {{ request()->routeIs('diarios.*') ? 'active' : '' }}">
            <i class="fas fa-book-open"></i>
            <span>Diario</span>
        </a>

        <!-- CONFIGURACIÓN CON SUBMENÚ -->
        <div class="nav-item has-submenu" onclick="toggleSubmenu(this)">
            <i class="fas fa-cog"></i>
            <span>Configuración</span>
            <i class="fas fa-chevron-down submenu-arrow"></i>
        </div>
        
        <div class="submenu">
            <a href="{{ route('usuarios.index') }}" 
                class="submenu-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i>
                <span>Usuarios y Roles</span>
            </a>
            
            <a href="{{ route('respaldos.index') }}" 
                class="submenu-item {{ request()->routeIs('respaldos.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i>
                <span>Respaldos</span>
            </a>
            
            <a href="{{ route('bitacora.index') }}" 
                class="submenu-item {{ request()->routeIs('bitacora.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Bitácora</span>
            </a>
        </div>
    </nav>

    <!-- FOOTER -->
    <div class="sidebar-bottom">
        <button onclick="openLogoutModal()" class="nav-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Cerrar sesión</span>
        </button>
    </div>

</div>

<!-- MODAL -->
<div id="logoutModal" class="modal">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-question-circle"></i>
        </div>
        <h3>Cerrar sesión</h3>
        <p>¿Seguro que deseas salir del sistema?</p>

        <div class="modal-actions">
            <button onclick="closeLogoutModal()" class="btn ghost">Cancelar</button>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn danger">Cerrar sesión</button>
            </form>
        </div>
    </div>
</div>

<style>
    /* BASE - mismo gradiente que login */
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        margin: 0;
        padding: 0;
    }

    /* SIDEBAR - mismo estilo corporativo */
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
    }

    /* Scrollbar personalizada */
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(79, 70, 229, 0.5);
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
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(145deg, #4f46e5, #7c3aed);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
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
    }

    .toggle {
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: #94a3b8;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .toggle:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .toggle i {
        font-size: 1rem;
    }

    /* NAV - items mejorados */
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
    }

    .nav-item:hover {
        background: rgba(79, 70, 229, 0.15);
        color: white;
    }

    .nav-item.active {
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
    }

    /* Submenu arrow */
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

    /* SUBMENU */
    .submenu {
        display: none;
        flex-direction: column;
        gap: 2px;
        margin-left: 20px;
        padding-left: 12px;
        border-left: 2px solid rgba(79, 70, 229, 0.3);
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
        background: rgba(79, 70, 229, 0.15);
        color: white;
    }

    .submenu-item.active {
        background: linear-gradient(90deg, #4f46e5, #7c3aed);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    /* FOOTER - logout */
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

    /* COLLAPSE - sidebar reducido */
    .sidebar.collapsed {
        width: 80px;
    }

    .sidebar.collapsed .brand-text,
    .sidebar.collapsed .nav-item span,
    .sidebar.collapsed .submenu-arrow,
    .sidebar.collapsed .submenu {
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

    .sidebar.collapsed .brand {
        justify-content: center;
    }

    .sidebar.collapsed .toggle i {
        transform: rotate(180deg);
    }

    /* MODAL - mismo estilo que login */
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .modal.show {
        display: flex;
        animation: fadeInModal 0.2s ease;
    }

    @keyframes fadeInModal {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-box {
        background: white;
        padding: 2rem;
        border-radius: 24px;
        width: 380px;
        max-width: 90%;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .modal-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(145deg, #4f46e5, #7c3aed);
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .modal-icon i {
        font-size: 2rem;
        color: white;
    }

    .modal-box h3 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .modal-box p {
        color: #6b7280;
        font-size: 0.85rem;
        margin-bottom: 1.5rem;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    /* BUTTONS - mismo estilo */
    .btn {
        padding: 10px 20px;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .btn.ghost {
        background: #f1f5f9;
        color: #475569;
    }

    .btn.ghost:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .btn.danger {
        background: linear-gradient(105deg, #ef4444, #dc2626);
        color: white;
    }

    .btn.danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Ajuste para el contenido principal */
    .main-content {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
        padding: 20px;
    }

    .sidebar.collapsed~.main-content {
        margin-left: 80px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.mobile-open {
            transform: translateX(0);
        }
        
        .main-content {
            margin-left: 0 !important;
        }
    }
</style>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
        
        // Guardar estado en localStorage
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }
    
    // Toggle submenu
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

    // Cerrar modal al hacer clic fuera
    document.getElementById('logoutModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeLogoutModal();
        }
    });
    
    // Restaurar estado del sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            document.getElementById('sidebar').classList.add('collapsed');
        }
        
        // Abrir submenu si hay una ruta activa dentro
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
</script>

<!-- Font Awesome - necesario para los íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">