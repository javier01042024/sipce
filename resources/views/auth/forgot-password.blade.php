    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SIPCE - Recuperar Contraseña</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .auth-card img,
            .auth-card svg,
            [class*="application-logo"],
            .min-h-screen img,
            .min-h-screen svg:not(.fa-spinner):not(.fa-brain):not(.fa-lock):not(.fa-envelope):not(.fa-shield-alt):not(.fa-arrow-left):not(.fa-paper-plane):not(.fa-check-circle):not(.fa-exclamation-triangle):not(.fa-circle-info) {
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
                padding: 1.5rem;
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
                border-radius: 24px;
                box-shadow: 0 25px 50px rgba(0,0,0,0.25);
                width: 100%;
                max-width: 850px;
                display: flex;
                overflow: hidden;
                animation: fadeInUp 0.5s ease;
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* Lado izquierdo - Branding */
            .brand-side {
                flex: 1;
                background: linear-gradient(145deg, #4f46e5, #7c3aed);
                padding: 2.5rem;
                display: flex;
                flex-direction: column;
                justify-content: center;
                color: white;
            }

            .brand-icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }

            .brand-side h2 {
                font-size: 1.8rem;
                font-weight: 700;
                margin-bottom: 0.75rem;
                letter-spacing: -0.5px;
            }

            .brand-side p {
                font-size: 0.9rem;
                line-height: 1.5;
                opacity: 0.9;
                margin-bottom: 1.5rem;
            }

            .feature-list {
                list-style: none;
                margin-top: 0.5rem;
            }

            .feature-list li {
                margin-bottom: 0.7rem;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.85rem;
            }

            .feature-list li i {
                font-size: 1rem;
                width: 20px;
                text-align: center;
            }

            /* Lado derecho - Formulario */
            .form-side {
                flex: 1;
                padding: 2.5rem;
                background: white;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .back-link {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                color: #7c3aed;
                text-decoration: none;
                font-size: 0.8rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                transition: all 0.2s;
                width: fit-content;
            }

            .back-link:hover {
                gap: 8px;
                color: #4f46e5;
            }

            .form-side h1 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 0.5rem;
            }

            .description {
                color: #6b7280;
                margin-bottom: 1.5rem;
                font-size: 0.85rem;
                line-height: 1.5;
                border-left: 3px solid #7c3aed;
                padding-left: 12px;
            }

            .input-group {
                margin-bottom: 1.2rem;
            }

            label {
                display: block;
                font-size: 0.8rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.3rem;
            }

            label i {
                margin-right: 6px;
                color: #7c3aed;
            }

            input {
                width: 100%;
                padding: 0.7rem 1rem;
                font-size: 0.9rem;
                border: 1.5px solid #e5e7eb;
                border-radius: 12px;
                transition: all 0.2s;
                background: #fafbfc;
                color: #1f2937;
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
                margin-top: 0.3rem;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            button {
                width: 100%;
                background: linear-gradient(105deg, #4f46e5, #7c3aed);
                border: none;
                padding: 0.75rem;
                border-radius: 12px;
                font-weight: 600;
                font-size: 0.9rem;
                color: white;
                cursor: pointer;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                margin-top: 0.5rem;
            }

            button:hover:not(:disabled) {
                transform: translateY(-1px);
                box-shadow: 0 6px 20px rgba(79,70,229,0.3);
            }

            button:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
            }

            .success-alert {
                background: #f0fdf4;
                border-left: 4px solid #10b981;
                padding: 0.7rem;
                border-radius: 10px;
                margin-bottom: 1.2rem;
                font-size: 0.8rem;
                color: #065f46;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .error-alert {
                background: #fef2f2;
                border-left: 4px solid #ef4444;
                padding: 0.7rem;
                border-radius: 10px;
                margin-bottom: 1.2rem;
                font-size: 0.8rem;
                color: #b91c1c;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .hidden {
                display: none !important;
            }

            .loading-spinner {
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Tablets */
            @media (max-width: 768px) {
                .forgot-card {
                    flex-direction: column;
                    max-width: 450px;
                }
                
                .brand-side { 
                    padding: 2rem; 
                    text-align: center;
                }
                
                .feature-list { 
                    text-align: left; 
                    display: inline-block;
                }
                
                .form-side { 
                    padding: 2rem; 
                }
            }

            /* Móviles */
            @media (max-width: 480px) {
                body {
                    padding: 0.5rem;
                }
                
                .brand-side { 
                    padding: 1.5rem; 
                }
                
                .form-side { 
                    padding: 1.5rem; 
                }
                
                .form-side h1 {
                    font-size: 1.3rem;
                }
                
                .brand-side h2 {
                    font-size: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="forgot-card">
            <!-- Lado izquierdo -->
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

            <!-- Lado derecho -->
            <div class="form-side">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
                </a>

                <h1>¿Olvidaste tu contraseña?</h1>
                <div class="description">
                    {{ __('No hay problema. Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.') }}
                </div>

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

                <div id="successAlert" class="success-alert hidden">
                    <i class="fas fa-check-circle"></i>
                    <span id="successMessage"></span>
                </div>

                <div id="errorAlert" class="error-alert hidden">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="errorMessage"></span>
                </div>

                <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
                    @csrf

                    <div class="input-group">
                        <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" 
                               class="@error('email') is-invalid @enderror"
                               placeholder="ejemplo@empresa.com" required autofocus autocomplete="email">
                        <div id="emailFeedback" class="invalid-feedback hidden">
                            <i class="fas fa-circle-info"></i> <span></span>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Enviar enlace de restablecimiento
                    </button>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('forgotForm');
                const emailInput = document.getElementById('email');
                const emailFeedback = document.getElementById('emailFeedback');
                const submitBtn = document.getElementById('submitBtn');
                const successAlert = document.getElementById('successAlert');
                const successMessage = document.getElementById('successMessage');
                const errorAlert = document.getElementById('errorAlert');
                const errorMessage = document.getElementById('errorMessage');
                
                emailInput.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        if (!isValidEmail(this.value)) {
                            showError(emailInput, emailFeedback, 'Ingresa un correo electrónico válido');
                        } else {
                            clearError(emailInput, emailFeedback);
                        }
                    } else {
                        clearError(emailInput, emailFeedback);
                    }
                });

                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    successAlert.classList.add('hidden');
                    errorAlert.classList.add('hidden');
                    clearError(emailInput, emailFeedback);
                    
                    let isValid = true;
                    
                    if (!emailInput.value.trim()) {
                        showError(emailInput, emailFeedback, 'El correo electrónico es obligatorio');
                        isValid = false;
                    } else if (!isValidEmail(emailInput.value)) {
                        showError(emailInput, emailFeedback, 'Ingresa un correo electrónico válido');
                        isValid = false;
                    }
                    
                    if (isValid) {
                        setLoadingState(true);
                        
                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: new FormData(form),
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });
                            
                            if (response.headers.get('content-type')?.includes('application/json')) {
                                const data = await response.json();
                                
                                if (response.ok) {
                                    showSuccessMessage(data.message || 'Se ha enviado un enlace a tu correo.');
                                    emailInput.value = '';
                                } else {
                                    const error = data.errors?.email?.[0] || data.message || 'Error al procesar la solicitud';
                                    showError(emailInput, emailFeedback, error);
                                    showErrorMessage(error);
                                }
                            } else {
                                if (response.ok) {
                                    showSuccessMessage('Se ha enviado un enlace a tu correo. Revisa tu bandeja.');
                                    emailInput.value = '';
                                } else {
                                    showErrorMessage('Error al procesar la solicitud.');
                                }
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            showErrorMessage('Error de conexión. Intenta nuevamente.');
                        } finally {
                            setLoadingState(false);
                        }
                    }
                });
                
                function isValidEmail(email) {
                    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
                }
                
                function showError(input, feedbackElement, message) {
                    input.classList.add('is-invalid');
                    feedbackElement.classList.remove('hidden');
                    feedbackElement.querySelector('span').textContent = message;
                }
                
                function clearError(input, feedbackElement) {
                    input.classList.remove('is-invalid');
                    feedbackElement.classList.add('hidden');
                }
                
                function showSuccessMessage(message) {
                    successMessage.textContent = message;
                    successAlert.classList.remove('hidden');
                }
                
                function showErrorMessage(message) {
                    errorMessage.textContent = message;
                    errorAlert.classList.remove('hidden');
                }
                
                function setLoadingState(isLoading) {
                    submitBtn.disabled = isLoading;
                    submitBtn.innerHTML = isLoading ? 
                        '<i class="fas fa-spinner loading-spinner"></i> Enviando...' : 
                        '<i class="fas fa-paper-plane"></i> Enviar enlace de restablecimiento';
                }
            });
        </script>
    </body>
    </html>
