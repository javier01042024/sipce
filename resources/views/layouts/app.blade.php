<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SIPCE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <!-- Sidebar -->
    @include('layouts.navigation')

    <!-- Contenido principal -->
    <div class="main-content" id="mainContent">
        @yield('content')
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
        }

        /* Sidebar colapsada */
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

        /* CONTENIDO PRINCIPAL - se desplaza según sidebar */
        .main-content {
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 20px;
            background: #f8fafc;
        }

        /* Cuando sidebar NO está colapsada (por defecto) */
        .main-content {
            margin-left: 260px;
        }

        /* Cuando sidebar ESTÁ colapsada */
        .main-content.sidebar-collapsed {
            margin-left: 80px;
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

        /* NAV */
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

        /* MODAL LOGOUT */
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

        /* Estilos para la tabla (de tu vista) */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        h1 { 
            font-size: 22px; 
            margin: 0; 
            color: #0f172a;
        }
        
        .header p { 
            font-size: 13px; 
            color: #64748b; 
            margin: 0; 
        }

        .btn-primary {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            overflow-x: auto;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            min-width: 600px;
        }

        th {
            background: #0f172a;
            color: white;
            padding: 12px;
            font-size: 13px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .actions {
            display: flex;
            gap: 6px;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            transform: translateY(-1px);
        }

        .btn-icon svg {
            width: 16px;
            height: 16px;
            stroke: white;
            fill: none;
            stroke-width: 2;
        }

        .blue { background: #0ea5e9; }
        .purple { background: #6366f1; }
        .red { background: #ef4444; }

        .badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.Alta { background: #ef4444; color: white; }
        .badge.Media { background: #f59e0b; color: white; }
        .badge.Baja { background: #22c55e; color: white; }

        .alert {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .empty {
            text-align: center;
            color: #64748b;
            padding: 40px;
        }

        .strong {
            font-weight: 600;
            color: #0f172a;
        }
    </style>

    <script>
        // Control de sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Guardar estado
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }

        function openLogoutModal() {
            document.getElementById('logoutModal').classList.add('show');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.remove('show');
        }

        // Recuperar estado al cargar
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
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('logoutModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });
    </script>

</body>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
</html>