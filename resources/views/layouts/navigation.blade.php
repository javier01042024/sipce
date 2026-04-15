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

        <a href="#" class="nav-item">
            <i class="fas fa-calendar-alt"></i>
            <span>Citas</span>
        </a>

        <a href="#" class="nav-item">
            <i class="fas fa-chart-line"></i>
            <span>Reportes</span>
        </a>

        <a href="#" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Configuración</span>
        </a>
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
}

/* TOP - logo estilo moderno */
.sidebar-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
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
    gap: 6px;
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
.sidebar.collapsed .nav-item span {
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

.sidebar.collapsed ~ .main-content {
    margin-left: 80px;
}
</style>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
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
</script>

<!-- Font Awesome - necesario para los íconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">