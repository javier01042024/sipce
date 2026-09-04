<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPCE - Verificar email</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            display: flex; align-items: center; justify-content: center; padding: 1rem;
        }
        .auth-card {
            background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            width: 100%; max-width: 460px; padding: 2.5rem; text-align: center; animation: fadeInUp 0.4s ease;
        }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .auth-card .icon { font-size: 2.5rem; color: var(--sipce-primary-dark); margin-bottom: 1rem; }
        .auth-card h2 { font-size: 1.3rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
        .auth-card p { color: #6b7280; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.2rem; }
        .success-msg { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 0.6rem; color: #166534; font-size: 0.85rem; margin-bottom: 1rem; }
        .btn-primary {
            display: inline-block; padding: 0.65rem 1.5rem; background: linear-gradient(135deg, var(--sipce-primary), var(--sipce-primary-dark));
            color: white; border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 600;
            cursor: pointer; transition: all 0.3s; text-decoration: none;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,0.4); }
        .btn-ghost {
            display: inline-block; margin-top: 1rem; color: #6b7280; font-size: 0.82rem;
            background: none; border: none; cursor: pointer; text-decoration: underline;
        }
        .btn-ghost:hover { color: #374151; }
    </style>
<style>:root{--sipce-primary:#667eea;--sipce-primary-dark:#5a6bd9;--sipce-primary-rgb:102, 126, 234}</style>

  </head>
<body>
    <div class="auth-card">
        <div class="icon"><i class="fas fa-envelope-open-text"></i></div>
        <h2>Verificar correo electrÃ³nico</h2>
        <p>Gracias por registrarte. Antes de comenzar, verifica tu correo electrÃ³nico haciendo clic en el enlace que te enviamos.</p>

        @if (session('status') == 'verification-link-sent')
            <div class="success-msg">
                <i class="fas fa-check-circle"></i> Se enviÃ³ un nuevo enlace de verificaciÃ³n a tu correo.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i> Reenviar correo de verificaciÃ³n
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-ghost">Cerrar sesiÃ³n</button>
        </form>
    </div>
</body>
</html>
