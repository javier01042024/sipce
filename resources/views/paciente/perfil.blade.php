@extends('layouts.paciente')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="page-header" style="background: linear-gradient(135deg, var(--sipce-primary-dark) 0%, var(--sipce-primary) 100%);">
        <div class="header-content">
            <h1><i class="fas fa-user-circle me-2"></i> Mi Perfil</h1>
            <p>Gestiona tu información personal y contraseña</p>
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="margin: 0 0 16px 0; color: #1e293b;">Información Personal</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Nombre</label>
                <p style="color: #1e293b; font-weight: 600;">{{ $user->name }}</p>
            </div>
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">Correo electrónico</label>
                <p style="color: #1e293b; font-weight: 600;">{{ $user->email }}</p>
            </div>
            @if($paciente)
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">N° Expediente</label>
                <p style="color: #1e293b; font-weight: 600;">{{ $paciente->numero_expediente }}</p>
            </div>
            @endif
        </div>
    </div>

    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="margin: 0 0 16px 0; color: #1e293b;">Cambiar Contraseña</h3>
        <form method="POST" action="{{ route('paciente.password') }}">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Contraseña Actual</label>
                <input type="password" name="current_password" required
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Nueva Contraseña</label>
                <input type="password" name="password" required
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 6px;">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" required
                    style="width: 100%; padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px;">
            </div>
            <button type="submit" style="padding: 10px 24px; background: linear-gradient(135deg, var(--sipce-primary), var(--sipce-primary-dark)); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Actualizar Contraseña</button>
        </form>
    </div>
</div>
@endsection
