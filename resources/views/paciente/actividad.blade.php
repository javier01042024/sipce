@extends('layouts.paciente')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="page-header" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
        <div class="header-content">
            <h1><i class="fas fa-history me-2"></i> Mi Actividad</h1>
            <p>Registro de tu actividad en el sistema</p>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Acción</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actividad as $log)
                    <tr>
                        <td><span class="sipce-cell-date"><i class="far fa-clock"></i> {{ $log->fecha_hora->format('d/m/Y H:i') }}</span></td>
                        <td><span class="sipce-badge sipce-badge-info">{{ ucfirst($log->accion) }}</span></td>
                        <td>{{ $log->descripcion }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3">
                            <div class="sipce-empty">
                                <i class="fas fa-history sipce-empty-icon"></i>
                                <p class="sipce-empty-title">Sin actividad registrada</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sipce-pagination">{{ $actividad->links() }}</div>
    </div>
</div>
@endsection
