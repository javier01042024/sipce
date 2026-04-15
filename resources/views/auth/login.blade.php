<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPCE - Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Fondo con patrón sutil */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 25% 40%, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Tarjeta ANCHA para PC - máximo ancho 1000px */
        .login-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 1000px;
            display: flex;
            overflow: hidden;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Lado izquierdo - Branding visual */
        .brand-side {
            flex: 1;
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }

        .brand-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }

        .brand-side h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .brand-side p {
            font-size: 1rem;
            line-height: 1.5;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .feature-list {
            list-style: none;
            margin-top: 1rem;
        }

        .feature-list li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .feature-list li i {
            font-size: 1.1rem;
        }

        /* Lado derecho - Formulario */
        .form-side {
            flex: 1;
            padding: 3rem;
            background: white;
        }

        .form-side h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .form-side .sub {
            color: #6b7280;
            margin-bottom: 2rem;
            font-size: 0.9rem;
            border-left: 3px solid #7c3aed;
            padding-left: 12px;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.4rem;
        }

        label i {
            margin-right: 6px;
            color: #7c3aed;
        }

        input {
            width: 100%;
            padding: 0.85rem 1rem;
            font-size: 0.95rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 14px;
            transition: all 0.2s;
            background: #fafbfc;
        }

        input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        }

        input.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.3rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0 1.5rem;
            font-size: 0.85rem;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: #7c3aed;
        }

        .forgot {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot:hover { text-decoration: underline; }

        button {
            width: 100%;
            background: linear-gradient(105deg, #4f46e5, #7c3aed);
            border: none;
            padding: 0.9rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.2rem;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(79,70,229,0.3);
        }

        .register {
            text-align: center;
            font-size: 0.9rem;
            color: #4b5563;
        }

        .register a {
            color: #7c3aed;
            font-weight: 700;
            text-decoration: none;
        }

        .register a:hover { text-decoration: underline; }

        .error-alert {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 0.8rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Responsive para cuando la pantalla es más pequeña que 800px */
        @media (max-width: 800px) {
            .login-card {
                flex-direction: column;
                max-width: 550px;
            }
            .brand-side { padding: 2rem; text-align: center; }
            .feature-list { text-align: left; }
            .form-side { padding: 2rem; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Lado izquierdo: presentación corporativa -->
        <div class="brand-side">
            <div class="brand-icon">
                <i class="fas fa-brain"></i>
            </div>
            <h2>SIPCE</h2>
            <p>Sistema Integral para el Cuidado y Bienestar Emocional</p>
            <ul class="feature-list">
                <li><i class="fas fa-chart-line"></i> Seguimiento personalizado</li>
                <li><i class="fas fa-shield-alt"></i> Datos seguros y confidenciales</li>
                <li><i class="fas fa-hand-holding-heart"></i> Apoyo profesional continuo</li>
            </ul>
        </div>

        <!-- Lado derecho: formulario de acceso -->
        <div class="form-side">
            <h1>Bienvenido de vuelta</h1>
            <div class="sub">Ingresa con tus credenciales</div>

            @if ($errors->any())
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        @if ($errors->has('email')) {{ $errors->first('email') }}
                        @elseif ($errors->has('password')) {{ $errors->first('password') }}
                        @else Credenciales incorrectas. Intenta nuevamente.
                        @endif
                    </span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="@error('email') is-invalid @enderror"
                           placeholder="ejemplo@empresa.com">
                    @error('email')
                        <div class="invalid-feedback"><i class="fas fa-circle-info"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" name="password" 
                           class="@error('password') is-invalid @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <div class="invalid-feedback"><i class="fas fa-circle-info"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                </button>

                <div class="register">
                    ¿Sin cuenta? <a href="{{ route('register') }}">Regístrate ahora</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Pequeño script para evitar doble submit y validación mínima -->
    <script>
        const form = document.getElementById('loginForm');
        if(form) {
            form.addEventListener('submit', function(e) {
                const email = form.querySelector('input[name="email"]');
                const pass = form.querySelector('input[name="password"]');
                let hasErr = false;
                
                // Limpiar errores visuales previos
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => { if(!el.classList.contains('server-feedback')) el.remove(); });
                
                if(!email.value.trim()) {
                    markError(email, 'El correo es obligatorio');
                    hasErr = true;
                } else if(!email.value.includes('@')) {
                    markError(email, 'Ingresa un correo válido');
                    hasErr = true;
                }
                
                if(!pass.value) {
                    markError(pass, 'La contraseña es requerida');
                    hasErr = true;
                }
                
                if(hasErr) {
                    e.preventDefault();
                } else {
                    const btn = form.querySelector('button');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';
                    btn.disabled = true;
                }
            });
            
            function markError(input, msg) {
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'invalid-feedback';
                div.innerHTML = '<i class="fas fa-circle-info"></i> ' + msg;
                input.parentNode.appendChild(div);
            }
        }
    </script>
</body>
</html>