@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-clipboard-list me-2"></i> Sesiones</h1>
            <p>Registro de sesiones clínicas realizadas</p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('sesiones.create') }}" class="btn-nuevo">
                <i class="fas fa-plus-circle"></i> Nueva Sesión
            </a>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div><h3><i class="fas fa-list"></i> Sesiones Registradas</h3>
                <p>{{ $sesiones->total() }} sesiones en total</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Duración</th>
                        <th>Resumen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sesiones as $sesion)
                    <tr>
                        <td><span class="sipce-cell-date"><i class="far fa-calendar-alt"></i> {{ $sesion->fecha->format('d/m/Y') }}</span></td>
                        <td>
                            <span class="sipce-cell-main">{{ $sesion->paciente->detalle->nombre ?? '' }} {{ $sesion->paciente->detalle->apellido ?? 'N/A' }}</span>
                            <span class="sipce-cell-sub">Exp: {{ $sesion->paciente->numero_expediente ?? 'N/A' }}</span>
                        </td>
                        <td><span class="sipce-cell-bold">{{ $sesion->duracion_minutos ?? 'N/A' }} min</span></td>
                        <td><span class="sipce-cell-muted">{{ Str::limit($sesion->resumen, 60) }}</span></td>
                        <td>
                            <div class="sipce-actions">
                                <a href="{{ route('sesiones.show', $sesion) }}" class="sipce-btn-icon sipce-btn-view" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('sesiones.edit', $sesion) }}" class="sipce-btn-icon sipce-btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="sipce-empty"><i class="fas fa-clipboard sipce-empty-icon"></i><p class="sipce-empty-title">No hay sesiones registradas</p><p class="sipce-empty-text">Comienza registrando una nueva sesión clínica</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sipce-pagination">{{ $sesiones->links() }}</div>
    </div>
</div>
@endsection
