@extends('layouts.app')

@section('title', 'Reporte de Citas')

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
            <h1><i class="fas fa-calendar-check me-2"></i> Reporte de Citas</h1>
            <p>Estadísticas y listado de citas</p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('reportes.exportar-citas', array_merge(request()->query(), ['formato' => 'xlsx'])) }}" class="btn-excel">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('reportes.exportar-citas', array_merge(request()->query(), ['formato' => 'pdf'])) }}" class="btn-pdf">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('reportes.index') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); margin-bottom:24px;">
        <form method="GET" action="{{ route('reportes.citas') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
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
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="atendida" {{ request('estado') === 'atendida' ? 'selected' : '' }}>Atendida</option>
                    <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    <option value="no_asistio" {{ request('estado') === 'no_asistio' ? 'selected' : '' }}>No Asistió</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Municipio</label>
                <input type="text" name="municipio" value="{{ request('municipio') }}" placeholder="Ej: Chacao" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
            </div>
            <button type="submit" class="btn-filtrar">
                <i class="fas fa-filter"></i> Filtrar
            </button>
            <a href="{{ route('reportes.citas') }}" class="btn-limpiar">
                <i class="fas fa-eraser"></i> Limpiar
            </a>
        </form>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:16px; margin-bottom:24px;">
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:var(--sipce-primary);">{{ $estadisticas['total'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Total Citas</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:#f59e0b;">{{ $estadisticas['pendientes'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Pendientes</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:#10b981;">{{ $estadisticas['atendidas'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Atendidas</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:#ef4444;">{{ $estadisticas['canceladas'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Canceladas</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            <div style="font-size:32px; font-weight:700; color:#94a3b8;">{{ $estadisticas['no_asistio'] ?? 0 }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">No Asistió</div>
        </div>
        <div style="background:white; border-radius:14px; padding:20px; box-shadow:0 4px 16px rgba(0,0,0,0.05); text-align:center;">
            @php
                $totalCitas = $estadisticas['total'] ?? 1;
                $atendidas = $estadisticas['atendidas'] ?? 0;
                $tasa = $totalCitas > 0 ? round(($atendidas / $totalCitas) * 100) : 0;
            @endphp
            <div style="font-size:32px; font-weight:700; color:#11998e;">{{ $tasa }}%</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Tasa de Asistencia</div>
        </div>
    </div>

    <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); margin-bottom:24px;">
        <h4 style="margin:0 0 16px 0; color:#1e293b; font-size:16px;"><i class="fas fa-chart-bar" style="color:var(--sipce-primary);"></i> Citas por Mes ({{ now()->year }})</h4>
        <div style="display:flex; align-items:end; gap:8px; height:180px; padding-top:10px;">
            @php
                $meses = $estadisticas['por_mes'] ?? collect();
                $maxCitas = $meses->max('total') ?? 1;
                $nombreMeses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
            @endphp
            @forelse($meses as $mes)
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%;">
                <span style="font-size:11px; color:#475569; font-weight:600; margin-bottom:4px;">{{ $mes->total }}</span>
                <div style="width:100%; max-width:50px; height:{{ $maxCitas > 0 ? round(($mes->total / $maxCitas) * 130) : 0 }}px; background:linear-gradient(180deg,var(--sipce-primary),var(--sipce-primary-dark)); border-radius:6px 6px 0 0; transition:height 0.3s;"></div>
                <span style="font-size:10px; color:#94a3b8; margin-top:6px; text-align:center;">{{ $nombreMeses[(int)$mes->mes] ?? $mes->mes }}</span>
            </div>
            @empty
            <div style="width:100%; text-align:center; color:#94a3b8; padding:40px;">No hay datos mensuales disponibles</div>
            @endforelse
        </div>
    </div>

    <div class="sipce-table-card">
        <div class="sipce-table-header">
            <div><h3><i class="fas fa-list"></i> Listado de Citas</h3>
                <p>{{ $citas->total() }} citas encontradas</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="sipce-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Paciente</th>
                        <th>Estado</th>
                        <th>Atención</th>
                        <th>Objetivo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($citas as $cita)
                    <tr>
                        <td><span class="sipce-cell-date"><i class="far fa-calendar-alt"></i> {{ $cita->fecha->format('d/m/Y') }}</span></td>
                        <td>
                            <span class="sipce-cell-main">{{ $cita->paciente->detalle->nombre ?? '' }} {{ $cita->paciente->detalle->apellido ?? 'N/A' }}</span>
                            <span class="sipce-cell-sub">Exp: {{ $cita->paciente->numero_expediente ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @php
                                $estadoColors = ['pendiente' => 'warning', 'atendida' => 'success', 'cancelada' => 'danger', 'no_asistio' => 'neutral'];
                                $color = $estadoColors[$cita->estado] ?? 'info';
                            @endphp
                            <span class="sipce-badge sipce-badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $cita->estado)) }}</span>
                        </td>
                        <td><span class="sipce-cell-muted">{{ $cita->paciente->tipo_atencion === 'privado' ? 'Privado' : 'Público' }}</span></td>
                        <td><span class="sipce-cell-muted">{{ Str::limit($cita->objetivo ?? 'N/A', 40) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="sipce-empty"><i class="fas fa-calendar sipce-empty-icon"></i><p class="sipce-empty-title">No se encontraron citas</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="sipce-pagination">{{ $citas->withQueryString()->links() }}</div>
    </div>
</div>
@endsection