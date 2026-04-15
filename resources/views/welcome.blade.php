<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIPCE - Bienvenida</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
        }

        /* Patrón de fondo sutil */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 25% 40%, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Tarjeta de bienvenida - mismo estilo que login/registro */
        .welcome-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo - mismo gradiente que login */
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 12px 24px -8px rgba(79, 70, 229, 0.4);
        }

        .logo-icon i {
            font-size: 2.5rem;
            color: white;
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 100%);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 2rem;
            border-left: 3px solid #7c3aed;
            padding-left: 12px;
            text-align: left;
        }

        /* Botones - mismo estilo que login */
        .btn-login, .btn-register, .btn-dashboard {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 0.8rem;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-login {
            background: linear-gradient(105deg, #4f46e5, #7c3aed);
            color: white;
            margin-bottom: 0.75rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .btn-register {
            background: transparent;
            color: #7c3aed;
            border: 2px solid #7c3aed;
            margin-bottom: 0;
        }

        .btn-register:hover {
            background: #7c3aed;
            color: white;
            transform: translateY(-2px);
        }

        .btn-dashboard {
            background: linear-gradient(105deg, #4f46e5, #7c3aed);
            color: white;
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .footer-text {
            margin-top: 1.5rem;
            color: #9ca3af;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .footer-text i {
            color: #ec489a;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .welcome-card {
                padding: 2rem 1.5rem;
            }
            .logo-icon {
                width: 70px;
                height: 70px;
            }
            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <div class="logo-icon">
            <i class="fas fa-brain"></i>
        </div>
        
        <h1>SIPCE</h1>
        
        <div class="subtitle">
            <i class="fas fa-heartbeat me-1" style="color: #7c3aed;"></i>
            Sistema Integral para Psicología Clínica Especializada
        </div>

        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-dashboard">
                    <i class="fas fa-arrow-right"></i> Ir al Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </a>
                
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-register">
                        <i class="fas fa-user-plus"></i> Crear Cuenta
                    </a>
                @endif
            @endauth
        @endif

        <div class="footer-text">
            <i class="fas fa-heart"></i>
            <span>Cuidando tu bienestar emocional</span>
            <i class="fas fa-shield-alt"></i>
        </div>
    </div>
</body>
</html>