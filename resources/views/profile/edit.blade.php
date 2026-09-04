@extends('layouts.app')

@section('content')
<div class="page-header">
    <div class="header-content">
        <h1><i class="fas fa-user-circle"></i> Mi Perfil</h1>
        <p>Administra tu informaciÃ³n de cuenta</p>
    </div>
</div>

<style>
    .profile-section {
        background: white; border-radius: 12px; padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 1.2rem;
    }
    .profile-section h2 { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-bottom: 0.3rem; }
    .profile-section .sub { color: #6b7280; font-size: 0.82rem; margin-bottom: 1rem; }
    .profile-section .field { margin-bottom: 1rem; }
    .profile-section .field label { display: block; font-size: 0.78rem; font-weight: 600; color: #374151; margin-bottom: 0.3rem; }
    .profile-section .field input {
        width: 100%; padding: 0.6rem 0.8rem; font-size: 0.85rem;
        border: 1.5px solid #e5e7eb; border-radius: 10px; background: #fafbfc; transition: all 0.2s;
    }
    .profile-section .field input:focus { outline: none; border-color: var(--sipce-primary-dark); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background: white; }
    .profile-section .field .err { color: #ef4444; font-size: 0.75rem; margin-top: 0.3rem; }
    .profile-section .verified { color: #059669; font-size: 0.82rem; margin-top: 0.5rem; }
    .profile-section .unverified { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 0.6rem 0.8rem; margin-top: 0.5rem; font-size: 0.82rem; color: #92400e; }
    .profile-section .unverified a { color: var(--sipce-primary-dark); text-decoration: underline; }
    .btn-save {
        padding: 0.6rem 1.5rem; background: linear-gradient(135deg, var(--sipce-primary), var(--sipce-primary-dark));
        color: white; border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 600;
        cursor: pointer; transition: all 0.3s;
    }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.4); }
    .btn-send-verify { background: none; border: none; color: var(--sipce-primary-dark); text-decoration: underline; cursor: pointer; font-size: 0.82rem; }
    .btn-send-verify:hover { color: var(--sipce-primary); }
    .danger-zone { border-top: 2px solid #fecaca; padding-top: 1rem; margin-top: 1rem; }
    .btn-danger {
        padding: 0.6rem 1.2rem; background: #ef4444; color: white; border: none;
        border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.3s;
    }
    .btn-danger:hover { background: #dc2626; }
</style>

<!-- Info del perfil -->
<div class="profile-section">
    <h2><i class="fas fa-user" style="color:var(--sipce-primary-dark);margin-right:5px;"></i> InformaciÃ³n del perfil</h2>
    <p class="sub">Actualiza la informaciÃ³n de tu cuenta y direcciÃ³n de correo.</p>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="field">
            <label><i class="fas fa-user" style="color:var(--sipce-primary-dark);margin-right:4px;"></i> Nombre</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name">
            @error('name') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label><i class="fas fa-envelope" style="color:var(--sipce-primary-dark);margin-right:4px;"></i> Correo electrÃ³nico</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username">
            @error('email') <p class="err">{{ $message }}</p> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                <div class="unverified">
                    Tu correo no estÃ¡ verificado.
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-send-verify">Haz clic aquÃ­ para reenviar el correo</button>
                    </form>
                    @if (session('status') === 'verification-link-sent')
                        <p style="color:#059669;margin-top:0.3rem;">Se enviÃ³ un nuevo enlace a tu correo.</p>
                    @endif
                </div>
            @endif
        </div>

        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Guardar
        </button>

        @if (session('status') === 'profile-updated')
            <span style="color:#059669;font-size:0.85rem;margin-left:10px;">Guardado.</span>
        @endif
    </form>
</div>

<!-- Cambiar contraseÃ±a -->
<div class="profile-section">
    <h2><i class="fas fa-lock" style="color:var(--sipce-primary-dark);margin-right:5px;"></i> Cambiar contraseÃ±a</h2>
    <p class="sub">AsegÃºrate de usar una contraseÃ±a larga y segura.</p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="field">
            <label><i class="fas fa-key" style="color:var(--sipce-primary-dark);margin-right:4px;"></i> ContraseÃ±a actual</label>
            <input type="password" name="current_password" autocomplete="current-password">
            @error('current_password') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label><i class="fas fa-key" style="color:var(--sipce-primary-dark);margin-right:4px;"></i> Nueva contraseÃ±a</label>
            <input type="password" name="password" autocomplete="new-password">
            @error('password') <p class="err">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label><i class="fas fa-key" style="color:var(--sipce-primary-dark);margin-right:4px;"></i> Confirmar contraseÃ±a</label>
            <input type="password" name="password_confirmation" autocomplete="new-password">
        </div>

        <button type="submit" class="btn-save">
            <i class="fas fa-save"></i> Guardar contraseÃ±a
        </button>
    </form>
</div>

<!-- Eliminar cuenta -->
<div class="profile-section danger-zone">
    <h2 style="color:#ef4444;"><i class="fas fa-trash-alt" style="margin-right:5px;"></i> Eliminar cuenta</h2>
    <p class="sub">Una vez eliminada tu cuenta, no hay vuelta atrÃ¡s. Por favor asegÃºrate de querer hacer esto.</p>

    <form method="post" action="{{ route('profile.destroy') }}" id="formDeleteAccount">
        @csrf
        @method('delete')
        <div class="field">
            <label><i class="fas fa-lock" style="color:#ef4444;margin-right:4px;"></i> ContraseÃ±a</label>
            <input type="password" name="password" placeholder="Confirma tu contraseÃ±a" autocomplete="current-password">
            @error('password') <p class="err">{{ $message }}</p> @enderror
        </div>
        <button type="button" class="btn-danger"
            onclick="SIPCE_ALERT.confirmDelete({title:'Â¿Eliminar tu cuenta?',html:'Esta acciÃ³n es <strong>irreversible</strong>. Se eliminarÃ¡n todos tus datos.'}).then(r=>{if(r.isConfirmed)document.getElementById('formDeleteAccount').submit()})">
            <i class="fas fa-trash-alt"></i> Eliminar cuenta
        </button>
    </form>
</div>
@endsection
