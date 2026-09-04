@extends('layouts.paciente')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="page-header" style="background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);">
        <div class="header-content">
            <h1><i class="fas fa-calendar-alt me-2"></i> Mis Citas</h1>
            <p>Consulta todas tus citas programadas</p>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div>
                <h3><i class="fas fa-calendar-check"></i> Historial de Citas</h3>
                <p>Total: {{ $citas->count() }} citas</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Objetivo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $cita)
                    <tr>
                        <td><span class="sipce-cell-bold">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</span></td>
                        <td>
                            @php
                                $badgeClass = match($cita->estado) { 'atendida' => 'sipce-badge-success', 'cancelada' => 'sipce-badge-danger', 'no_asistio' => 'sipce-badge-warning', default => 'sipce-badge-primary' };
                            @endphp
                            <span class="sipce-badge {{ $badgeClass }}">{{ $cita->estado_texto }}</span>
                        </td>
                        <td>{{ $cita->objetivo ? Str::limit($cita->objetivo, 60) : 'â€”' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="sipce-empty">
                                <i class="fas fa-calendar-times sipce-empty-icon"></i>
                                <p class="sipce-empty-title">No tienes citas registradas</p>
                                <p class="sipce-empty-text">Tus prÃ³ximas citas aparecerÃ¡n aquÃ­</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
