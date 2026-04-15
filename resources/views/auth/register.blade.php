<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPCE - Registro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        /* Tarjeta más compacta verticalmente */
        .register-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 880px;
            display: flex;
            overflow: hidden;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Lado izquierdo - más compacto */
        .brand-side {
            flex: 1;
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
            padding: 1.8rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }
        .brand-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
        .brand-side h2 { font-size: 1.6rem; font-weight: 700; margin-bottom: 0.3rem; }
        .brand-side p { font-size: 0.8rem; opacity: 0.9; margin-bottom: 1rem; line-height: 1.3; }
        .feature-list { list-style: none; }
        .feature-list li {
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
        }
        .feature-list li i { font-size: 0.8rem; }
        /* Lado derecho - muy compacto verticalmente */
        .form-side {
            flex: 1;
            padding: 1.5rem;
            background: white;
        }
        .form-side h1 { font-size: 1.4rem; font-weight: 700; color: #1f2937; margin-bottom: 0.2rem; }
        .form-side .sub {
            color: #6b7280;
            margin-bottom: 1rem;
            font-size: 0.75rem;
            border-left: 3px solid #7c3aed;
            padding-left: 8px;
        }
        .input-group { margin-bottom: 0.75rem; }
        label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.2rem;
        }
        label i { margin-right: 4px; color: #7c3aed; font-size: 0.7rem; }
        input {
            width: 100%;
            padding: 0.55rem 0.8rem;
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
        input.is-invalid { border-color: #ef4444; background: #fef2f2; }
        .invalid-feedback {
            color: #ef4444;
            font-size: 0.65rem;
            margin-top: 0.2rem;
            display: flex;
            align-items: center;
            gap: 4px;
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
            gap: 6px;
        }
        button {
            width: 100%;
            background: linear-gradient(105deg, #4f46e5, #7c3aed);
            border: none;
            padding: 0.6rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin: 0.5rem 0 0.7rem;
        }
        button:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79,70,229,0.3); }
        button:disabled { opacity: 0.7; transform: none; }
        .login-link { text-align: center; font-size: 0.75rem; color: #4b5563; }
        .login-link a { color: #7c3aed; font-weight: 600; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        .terms {
            font-size: 0.6rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 0.7rem;
            padding-top: 0.5rem;
            border-top: 1px solid #f0f0f0;
        }
        @media (max-width: 750px) {
            .register-card { flex-direction: column; max-width: 400px; }
            .brand-side { padding: 1.2rem; text-align: center; }
            .feature-list { text-align: left; }
            .form-side { padding: 1.2rem; }
        }
    </style>
</head>

<body>
    <div class="register-card">
        <!-- Lado izquierdo -->
        <div class="brand-side">
            <div class="brand-icon"><i class="fas fa-brain"></i></div>
            <h2>SIPCE</h2>
            <p>Sistema Integral para el Bienestar Emocional</p>
            <ul class="feature-list">
                <li><i class="fas fa-chart-line"></i> Seguimiento personalizado</li>
                <li><i class="fas fa-shield-alt"></i> Datos seguros</li>
                <li><i class="fas fa-hand-holding-heart"></i> Apoyo profesional</li>
            </ul>
        </div>

        <!-- Lado derecho - Formulario -->
        <div class="form-side">
            <h1>Crear cuenta</h1>
            <div class="sub">Completa tus datos</div>

            @if ($errors->any())
                <div class="error-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>
                        @if ($errors->has('name')) {{ $errors->first('name') }}
                        @elseif ($errors->has('email')) {{ $errors->first('email') }}
                        @elseif ($errors->has('password')) {{ $errors->first('password') }}
                        @else Revisa los datos ingresados
                        @endif
                    </span>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <div class="input-group">
                    <label><i class="fas fa-user"></i> Nombre</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="@error('name') is-invalid @enderror" placeholder="Tu nombre">
                    @error('name')<div class="invalid-feedback"><i class="fas fa-circle-info"></i> {{ $message }}</div>@enderror
                </div>

                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Correo</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="@error('email') is-invalid @enderror" placeholder="ejemplo@correo.com">
                    @error('email')<div class="invalid-feedback"><i class="fas fa-circle-info"></i> {{ $message }}</div>@enderror
                </div>

                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" name="password" class="@error('password') is-invalid @enderror" placeholder="Mínimo 6 caracteres">
                    @error('password')<div class="invalid-feedback"><i class="fas fa-circle-info"></i> {{ $message }}</div>@enderror
                </div>

                <div class="input-group">
                    <label><i class="fas fa-check-circle"></i> Confirmar</label>
                    <input type="password" name="password_confirmation" placeholder="Repite tu contraseña">
                </div>

                <button type="submit"><i class="fas fa-user-plus"></i> Registrarse</button>

                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión <i class="fas fa-arrow-right fa-xs"></i></a>
                </div>
                <div class="terms">
                    <i class="fas fa-shield-alt"></i> Al registrarte aceptas términos y condiciones
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('registerForm');
            if(form) {
                form.addEventListener('submit', function(e) {
                    const name = form.querySelector('input[name="name"]');
                    const email = form.querySelector('input[name="email"]');
                    const pass = form.querySelector('input[name="password"]');
                    const confirm = form.querySelector('input[name="password_confirmation"]');
                    let error = false;

                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => { if(!el.classList.contains('server-feedback')) el.remove(); });

                    if(!name.value.trim()) { markError(name, 'Nombre requerido'); error = true; }
                    if(!email.value.trim()) { markError(email, 'Correo requerido'); error = true; }
                    else if(!email.value.includes('@')) { markError(email, 'Correo inválido'); error = true; }
                    if(!pass.value) { markError(pass, 'Contraseña requerida'); error = true; }
                    else if(pass.value.length < 6) { markError(pass, 'Mínimo 6 caracteres'); error = true; }
                    if(!confirm.value) { markError(confirm, 'Confirma tu contraseña'); error = true; }
                    else if(pass.value !== confirm.value) { markError(confirm, 'Las contraseñas no coinciden'); error = true; }

                    if(error) {
                        e.preventDefault();
                        const firstError = document.querySelector('.is-invalid');
                        if(firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        const btn = form.querySelector('button');
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';
                        btn.disabled = true;
                    }
                });
            }
            function markError(input, msg) {
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'invalid-feedback';
                div.innerHTML = '<i class="fas fa-circle-info"></i> ' + msg;
                input.parentNode.appendChild(div);
            }
            document.querySelectorAll('input').forEach(inp => {
                inp.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const fb = this.parentNode.querySelector('.invalid-feedback');
                    if(fb && !fb.classList.contains('server-feedback')) fb.remove();
                });
            });
        })();
    </script>
</body>

</html>