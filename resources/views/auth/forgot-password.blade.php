<x-guest-layout>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SIPCE - Recuperar Contraseña</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .auth-card img,
            .auth-card svg,
            [class*="application-logo"],
            .min-h-screen img,
            .min-h-screen svg:not(.fa-spinner) {
                display: none !important;
            }
            
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

            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: radial-gradient(circle at 25% 40%, rgba(255,255,255,0.03) 1px, transparent 1px);
                background-size: 40px 40px;
                pointer-events: none;
            }

            .forgot-card {
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

            .back-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                color: #7c3aed;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 2rem;
                transition: all 0.2s;
            }

            .back-link:hover {
                gap: 10px;
                color: #4f46e5;
            }

            .form-side h1 {
                font-size: 1.8rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 0.5rem;
            }

            .description {
                color: #6b7280;
                margin-bottom: 2rem;
                font-size: 0.9rem;
                line-height: 1.6;
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
            }

            button:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(79,70,229,0.3);
            }

            .success-alert {
                background: #f0fdf4;
                border-left: 4px solid #10b981;
                padding: 0.8rem;
                border-radius: 12px;
                margin-bottom: 1.5rem;
                font-size: 0.85rem;
                color: #065f46;
                display: flex;
                align-items: center;
                gap: 10px;
            }

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

            @media (max-width: 800px) {
                .forgot-card {
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
        <div class="forgot-card">
            <!-- Lado izquierdo: presentación corporativa -->
            <div class="brand-side">
                <div class="brand-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h2>SIPCE</h2>
                <p>Sistema Integral para el Cuidado y Bienestar Emocional</p>
                <ul class="feature-list">
                    <li><i class="fas fa-lock"></i> Recuperación segura</li>
                    <li><i class="fas fa-envelope"></i> Enlace enviado por correo</li>
                    <li><i class="fas fa-shield-alt"></i> Protección de datos</li>
                </ul>
            </div>

            <!-- Lado derecho: formulario de recuperación -->
            <div class="form-side">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
                </a>

                <h1>¿Olvidaste tu contraseña?</h1>
                <div class="description">
                    {{ __('No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.') }}
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="success-alert">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>
                            @if ($errors->has('email')) 
                                {{ $errors->first('email') }}
                            @else 
                                Ha ocurrido un error. Intenta nuevamente.
                            @endif
                        </span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                    @csrf

                    <div class="input-group">
                        <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               class="@error('email') is-invalid @enderror"
                               placeholder="ejemplo@empresa.com" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="fas fa-circle-info"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit">
                        <i class="fas fa-paper-plane"></i> Enviar enlace de restablecimiento
                    </button>
                </form>
            </div>
        </div>

        <script>
            const form = document.getElementById('forgotForm');
            if(form) {
                form.addEventListener('submit', function(e) {
                    const email = form.querySelector('input[name="email"]');
                    let hasErr = false;
                    
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => { if(!el.classList.contains('server-feedback')) el.remove(); });
                    
                    if(!email.value.trim()) {
                        markError(email, 'El correo es obligatorio');
                        hasErr = true;
                    } else if(!email.value.includes('@')) {
                        markError(email, 'Ingresa un correo válido');
                        hasErr = true;
                    }
                    
                    if(hasErr) {
                        e.preventDefault();
                    } else {
                        const btn = form.querySelector('button');
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
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
</x-guest-layout>