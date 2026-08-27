<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIPCE - Restablecer contraseña</title>
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
            width: 100%; max-width: 460px; padding: 2.5rem; animation: fadeInUp 0.4s ease;
        }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .auth-card .icon { text-align: center; font-size: 2.5rem; color: #7c3aed; margin-bottom: 1rem; }
        .auth-card h2 { text-align: center; font-size: 1.3rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem; }
        .auth-card p { color: #6b7280; font-size: 0.85rem; text-align: center; margin-bottom: 1.5rem; }
        .input-group { margin-bottom: 1rem; }
        .input-group label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 0.3rem; }
        .input-group input {
            width: 100%; padding: 0.65rem 0.8rem; font-size: 0.85rem;
            border: 1.5px solid #e5e7eb; border-radius: 10px; background: #fafbfc; transition: all 0.2s;
        }
        .input-group input:focus { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background: white; }
        .input-error { color: #ef4444; font-size: 0.75rem; margin-top: 0.3rem; }
        .btn-primary {
            width: 100%; padding: 0.7rem; background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 600;
            cursor: pointer; transition: all 0.3s; margin-top: 0.5rem;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,0.4); }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="icon"><i class="fas fa-key"></i></div>
        <h2>Restablecer contraseña</h2>
        <p>Ingresa tu nueva contraseña a continuación.</p>

        @if($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:0.6rem 0.8rem;margin-bottom:1rem;">
                @foreach($errors->all() as $error)
                    <p class="input-error">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="input-group">
                <label><i class="fas fa-envelope"></i> Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            </div>

            <div class="input-group">
                <label><i class="fas fa-lock"></i> Nueva contraseña</label>
                <input type="password" name="password" required autocomplete="new-password">
            </div>

            <div class="input-group">
                <label><i class="fas fa-lock"></i> Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-check"></i> Restablecer contraseña
            </button>
        </form>
    </div>
</body>
</html>
