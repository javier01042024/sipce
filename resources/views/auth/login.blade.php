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
            padding: 1rem;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 25% 40%, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 800px;
            display: flex;
            overflow: hidden;
            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Lado izquierdo - Branding */
        .brand-side {
            flex: 1;
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }

        .brand-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .brand-side h2 {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .brand-side p {
            font-size: 0.8rem;
            line-height: 1.4;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
            margin-top: 0.5rem;
        }

        .feature-list li {
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
        }

        .feature-list li i {
            font-size: 0.9rem;
            width: 18px;
            text-align: center;
        }

        /* Lado derecho - Formulario */
        .form-side {
            flex: 1;
            padding: 2rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-side h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .form-side .sub {
            color: #6b7280;
            margin-bottom: 1.2rem;
            font-size: 0.8rem;
            border-left: 3px solid #7c3aed;
            padding-left: 10px;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        label i {
            margin-right: 5px;
            color: #7c3aed;
        }

        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-wrapper input {
            width: 100%;
            padding: 0.6rem 2.5rem 0.6rem 0.8rem;
            font-size: 0.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            transition: all 0.2s;
            background: #fafbfc;
        }

        .password-wrapper input:focus {
            outline: none;
            border-color: #7c3aed;
            background: white;
            box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        }

        .password-wrapper input.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 5px;
            font-size: 0.9rem;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: auto;
        }

        .toggle-password:hover {
            color: #7c3aed;
        }

        input {
            width: 100%;
            padding: 0.6rem 0.8rem;
            font-size: 0.85rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
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
            font-size: 0.7rem;
            margin-top: 0.2rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.75rem 0 1rem;
            font-size: 0.75rem;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .checkbox input {
            width: 14px;
            height: 14px;
            margin: 0;
            accent-color: #7c3aed;
        }

        .forgot {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot:hover { text-decoration: underline; }

        button[type="submit"] {
            width: 100%;
            background: linear-gradient(105deg, #4f46e5, #7c3aed);
            border: none;
            padding: 0.65rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button[type="submit"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(79,70,229,0.3);
        }

        button[type="submit"]:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .error-alert {
            background: #fef2f2;
            border-left: 3px solid #ef4444;
            padding: 0.5rem 0.7rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.75rem;
            color: #b91c1c;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .success-alert {
            background: #f0fdf4;
            border-left: 3px solid #10b981;
            padding: 0.5rem 0.7rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.75rem;
            color: #065f46;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 700px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
            }
            .brand-side { 
                padding: 1.5rem; 
                text-align: center; 
            }
            .feature-list { 
                text-align: left; 
                display: inline-block;
            }
            .form-side { 
                padding: 1.5rem; 
            }
        }

        @media (max-width: 400px) {
            body { padding: 0.5rem; }
            .brand-side { padding: 1.2rem; }
            .form-side { padding: 1.2rem; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Lado izquierdo -->
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

        <!-- Lado derecho -->
        <div class="form-side">
            <h1>Bienvenido de vuelta</h1>
            <div class="sub">Ingresa con tus credenciales</div>

            @if ($errors->any())
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        @if ($errors->has('email')) 
                            {{ $errors->first('email') }}
                        @elseif ($errors->has('password')) 
                            {{ $errors->first('password') }}
                        @else 
                            Credenciales incorrectas. Intenta nuevamente.
                        @endif
                    </span>
                </div>
            @endif

            @if (session('status'))
                <div class="success-alert">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="@error('email') is-invalid @enderror"
                           placeholder="ejemplo@empresa.com" autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback"><i class="fas fa-circle-info"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password"
                               class="@error('password') is-invalid @enderror"
                               placeholder="••••••••" autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="far fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
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

                <button type="submit" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                </button>
            </form>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form validation
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if(form) {
            form.addEventListener('submit', function(e) {
                const email = form.querySelector('input[name="email"]');
                const pass = form.querySelector('input[name="password"]');
                let hasErr = false;
                
                // Limpiar errores previos
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.querySelectorAll('.invalid-feedback').forEach(el => { 
                    if(!el.classList.contains('server-error')) el.remove(); 
                });
                
                // Validar email
                if(!email.value.trim()) {
                    markError(email, 'El correo electrónico es obligatorio');
                    hasErr = true;
                } else if(!email.value.includes('@')) {
                    markError(email, 'Ingresa un correo electrónico válido');
                    hasErr = true;
                }
                
                // Validar contraseña
                if(!pass.value.trim()) {
                    markError(pass, 'La contraseña es obligatoria');
                    hasErr = true;
                }
                
                if(hasErr) {
                    e.preventDefault();
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
                    submitBtn.disabled = true;
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