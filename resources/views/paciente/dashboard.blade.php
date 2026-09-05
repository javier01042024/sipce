@extends('layouts.paciente')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="page-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <div class="header-content">
            <h1><i class="fas fa-home me-2"></i> Mi Panel</h1>
            <p>Bienvenido/a, {{ $user->name }}</p>
        </div>
    </div>

    @if($proximaCita)
    <div style="background: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-left: 4px solid #f59e0b;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <i class="fas fa-bell" style="color: #f59e0b; font-size: 20px;"></i>
            <strong style="color: #1e293b;">Próxima Cita</strong>
        </div>
        <p style="color: #475569; margin: 0;">
            {{ \Carbon\Carbon::parse($proximaCita->fecha)->format('d/m/Y') }}
            @if($proximaCita->objetivo) â€” {{ Str::limit($proximaCita->objetivo, 60) }} @endif
        </p>
    </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <a href="{{ route('paciente.mis-citas') }}" style="background: white; border-radius: 16px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-align: center; transition: transform 0.2s;">
            <i class="fas fa-calendar-check" style="font-size: 28px; color: var(--sipce-primary); margin-bottom: 10px; display: block;"></i>
            <strong style="color: #1e293b; display: block;">Mis Citas</strong>
            <span style="color: #94a3b8; font-size: 13px;">Ver agenda</span>
        </a>
        <a href="{{ route('paciente.mi-diario') }}" style="background: white; border-radius: 16px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-align: center; transition: transform 0.2s;">
            <i class="fas fa-book-open" style="font-size: 28px; color: #11998e; margin-bottom: 10px; display: block;"></i>
            <strong style="color: #1e293b; display: block;">Mi Diario</strong>
            <span style="color: #94a3b8; font-size: 13px;">Escribir entrada</span>
        </a>
        <a href="{{ route('paciente.perfil') }}" style="background: white; border-radius: 16px; padding: 24px; text-decoration: none; color: inherit; box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-align: center; transition: transform 0.2s;">
            <i class="fas fa-user-circle" style="font-size: 28px; color: var(--sipce-primary-dark); margin-bottom: 10px; display: block;"></i>
            <strong style="color: #1e293b; display: block;">Mi Perfil</strong>
            <span style="color: #94a3b8; font-size: 13px;">Configuración</span>
        </a>
    </div>

    @if($ultimasCitas->count())
    <div style="background: white; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="margin: 0 0 16px 0; color: #1e293b; font-size: 16px;"><i class="fas fa-clock" style="color: var(--sipce-primary);"></i> Últimas Citas</h3>
        @foreach($ultimasCitas as $cita)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <div>
                <span style="color: #475569; font-weight: 600;">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span>
                @if($cita->objetivo)
                    <span style="color: #94a3b8; font-size: 13px; margin-left: 8px;">{{ Str::limit($cita->objetivo, 40) }}</span>
                @endif
            </div>
            @php
                $badgeClass = match($cita->estado) { 'atendida' => 'sipce-badge-success', 'cancelada' => 'sipce-badge-danger', 'no_asistio' => 'sipce-badge-warning', default => 'sipce-badge-primary' };
            @endphp
            <span class="sipce-badge {{ $badgeClass }}">{{ ucfirst($cita->estado) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    @if($ultimosDiarios->count())
    <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <h3 style="margin: 0 0 16px 0; color: #1e293b; font-size: 16px;"><i class="fas fa-book-open" style="color: #11998e;"></i> Últimas Entradas del Diario</h3>
        @foreach($ultimosDiarios as $diario)
        <div style="padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
            <span style="color: #475569; font-weight: 600;">{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}</span>
            <p style="color: #64748b; margin: 4px 0 0 0; font-size: 14px;">{{ Str::limit($diario->contenido, 80) }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
