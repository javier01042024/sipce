@extends('layouts.app')

@section('content')
<div class="estados-container">
    <div class="page-header" style="background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);">
        <div class="header-content">
            <h1><i class="fas fa-clipboard-list me-2"></i> Planes de Tratamiento</h1>
            <p>GestiÃ³n de planes terapÃ©uticos por paciente</p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('tratamiento.create') }}" class="btn-nuevo">
                <i class="fas fa-plus-circle"></i> Nuevo Plan
            </a>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div><h3><i class="fas fa-list"></i> Planes Activos</h3></div>
        </div>
        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>TÃ­tulo</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Objetivos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($planes as $plan)
                    <tr>
                        <td><span class="sipce-cell-main">{{ $plan->paciente->nombre_completo ?? 'N/A' }}</span></td>
                        <td>{{ $plan->titulo }}</td>
                        <td><span class="sipce-badge sipce-badge-{{ $plan->estado_color }}">{{ $plan->estado_texto }}</span></td>
                        <td><span class="sipce-cell-date"><i class="far fa-calendar-alt"></i> {{ $plan->fecha_inicio->format('d/m/Y') }}</span></td>
                        <td><span class="sipce-cell-bold">{{ $plan->objetivos->count() }}</span></td>
                        <td>
                            <div class="sipce-actions">
                                <a href="{{ route('tratamiento.show', $plan) }}" class="sipce-btn-icon sipce-btn-view" title="Ver"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('tratamiento.edit', $plan) }}" class="sipce-btn-icon sipce-btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="sipce-empty"><i class="fas fa-clipboard sipce-empty-icon"></i><p class="sipce-empty-title">No hay planes de tratamiento</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sipce-pagination">{{ $planes->links() }}</div>
    </div>
</div>
@endsection
