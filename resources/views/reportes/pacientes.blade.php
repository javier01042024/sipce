@extends('layouts.app')

@section('title', 'Reporte de Pacientes')

@push('styles')
<style>
    .header-buttons { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn-volver, .btn-excel, .btn-pdf { background: #ffffff; color: var(--sipce-primary, #667eea); border: none; padding: 10px 18px; border-radius: 25px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; }
    .btn-volver:hover, .btn-excel:hover, .btn-pdf:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
    .btn-excel i { color: #1f8a4c; }
    .btn-pdf i { color: #dc2626; }
</style>
@endpush

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-users me-2"></i> Reporte de Pacientes</h1>
            <p>Estadísticas y listado de pacientes</p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('reportes.exportar-pacientes', array_merge(request()->query(), ['formato' => 'xlsx'])) }}" class="btn-excel">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('reportes.exportar-pacientes', array_merge(request()->query(), ['formato' => 'pdf'])) }}" class="btn-pdf">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); margin-bottom:24px;">
        <form method="GET" action="{{ route('reportes.pacientes') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Tipo de Paciente</label>
                <select name="tipo_paciente" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    <option value="">Todos</option>
                    <option value="niño" {{ request('tipo_paciente') === 'niño' ? 'selected' : '' }}>Niño(a)</option>
                    <option value="adolescente" {{ request('tipo_paciente') === 'adolescente' ? 'selected' : '' }}>Adolescente</option>
                    <option value="adulto" {{ request('tipo_paciente') === 'adulto' ? 'selected' : '' }}>Adulto</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Prioridad</label>
                <select name="prioridad" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    <option value="">Todas</option>
                    <option value="urgencia" {{ request('prioridad') === 'urgencia' ? 'selected' : '' }}>Urgencia</option>
                    <option value="alta" {{ request('prioridad') === 'alta' ? 'selected' : '' }}>Alta</option>
                    <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
                    <option value="baja" {{ request('prioridad') === 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Atención</label>
                <select name="tipo_atencion" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    <option value="">Toda</option>
                    <option value="publico" {{ request('tipo_atencion') === 'publico' ? 'selected' : '' }}>Pública</option>
                    <option value="privado" {{ request('tipo_atencion') === 'privado' ? 'selected' : '' }}>Privada</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Estado</label>
                <select name="estado" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    <option value="">Todos</option>
                    @foreach($estados as $estado)
                    <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>{{ $estado->tipo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Municipio</label>
                <input type="text" name="municipio" value="{{ request('municipio') }}" placeholder="Ej: Chacao" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
            </div>
            <button type="submit" class="btn-filtrar">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="{{ route('reportes.pacientes') }}" class="btn-limpiar">
                <i class="fas fa-eraser"></i> Limpiar
            </a>
        </form>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:16px; margin-bottom:24px;">
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:var(--sipce-primary);">{{ $estadisticas['total'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Total Pacientes</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:#10b981;">{{ $estadisticas['nuevos_mes'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Nuevos Este Mes</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:14px; font-weight:600; color:#475569; margin-bottom:8px;">Por Prioridad</div>
            <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                <span class="sipce-badge sipce-badge-danger">Urgencia: {{ $estadisticas['por_prioridad']['urgencia'] ?? 0 }}</span>
                <span class="sipce-badge sipce-badge-danger">Alta: {{ $estadisticas['por_prioridad']['alta'] ?? 0 }}</span>
                <span class="sipce-badge sipce-badge-warning">Media: {{ $estadisticas['por_prioridad']['media'] ?? 0 }}</span>
                <span class="sipce-badge sipce-badge-success">Baja: {{ $estadisticas['por_prioridad']['baja'] ?? 0 }}</span>
            </div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:14px; font-weight:600; color:#475569; margin-bottom:8px;">Por Atención</div>
            <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                <span class="sipce-badge sipce-badge-info">Público: {{ $estadisticas['por_atencion']['publico'] ?? 0 }}</span>
                <span class="sipce-badge sipce-badge-primary">Privado: {{ $estadisticas['por_atencion']['privado'] ?? 0 }}</span>
            </div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:14px; font-weight:600; color:#475569; margin-bottom:8px;">Por Tipo Paciente</div>
            <div style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                <span class="sipce-badge sipce-badge-primary">Niños: {{ $estadisticas['por_tipo_paciente']['niño'] ?? 0 }}</span>
                <span class="sipce-badge sipce-badge-primary">Adolescentes: {{ $estadisticas['por_tipo_paciente']['adolescente'] ?? 0 }}</span>
                <span class="sipce-badge sipce-badge-primary">Adultos: {{ $estadisticas['por_tipo_paciente']['adulto'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div><h3><i class="fas fa-list"></i> Listado de Pacientes</h3>
                <p>{{ $pacientes->total() }} pacientes encontrados</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Expediente</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Atención</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pacientes as $paciente)
                    <tr>
                        <td><span class="sipce-cell-code">{{ $paciente->numero_expediente }}</span></td>
                        <td>
                            <span class="sipce-cell-main">{{ $paciente->detalle->nombre ?? '' }} {{ $paciente->detalle->apellido ?? 'N/A' }}</span>
                        </td>
                        <td><span class="sipce-badge sipce-badge-info">{{ ucfirst($paciente->tipo_paciente) }}</span></td>
                        <td><span class="sipce-badge sipce-badge-{{ $paciente->tipo_atencion === 'privado' ? 'primary' : 'info' }}">{{ $paciente->tipo_atencion === 'privado' ? 'Privado' : 'Público' }}</span></td>
                        <td>
                            @php $prio = $paciente->prioridad ?? 'baja'; @endphp
                            <span class="sipce-badge sipce-badge-{{ $prio === 'alta' || $prio === 'urgencia' ? 'danger' : ($prio === 'media' ? 'warning' : 'success') }}">{{ ucfirst($prio) }}</span>
                        </td>
                        <td><span class="sipce-badge sipce-badge-neutral">{{ $paciente->estado->tipo ?? 'N/A' }}</span></td>
                        <td><span class="sipce-cell-date"><i class="far fa-calendar-alt"></i> {{ $paciente->created_at->format('d/m/Y') }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7"><div class="sipce-empty"><i class="fas fa-users sipce-empty-icon"></i><p class="sipce-empty-title">No se encontraron pacientes</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sipce-pagination">{{ $pacientes->withQueryString()->links() }}</div>
    </div>
</div>
@endsection