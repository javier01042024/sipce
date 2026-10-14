@extends('layouts.app')

@section('title', 'Reporte de Evolución')

@push('styles')
<style>
    .header-buttons { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn-volver, .btn-pdf { background: #ffffff; color: var(--sipce-primary, #667eea); border: none; padding: 10px 18px; border-radius: 25px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s; }
    .btn-volver:hover, .btn-pdf:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.2); }
    .btn-pdf i { color: #dc2626; }
</style>
@endpush

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-chart-line me-2"></i> Reporte de Evolución</h1>
            <p>Tendencias de pacientes, estados y diarios del sistema</p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('reportes.evolucion') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); margin-bottom:24px;">
        <form method="GET" action="{{ route('reportes.evolucion') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
            <div>
                <label style="display:block; font-weight:600; color:#475569; margin-bottom:4px; font-size:12px;">Año</label>
                <select name="anio" style="padding:8px 12px; border:2px solid #e2e8f0; border-radius:8px; font-size:13px;">
                    @for($a = now()->year; $a >= now()->year - 4; $a--)
                    <option value="{{ $a }}" {{ ($estadisticas['anio'] ?? now()->year) == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn-filtrar">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </form>
    </div>

    <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); margin-bottom:24px;">
        <h4 style="margin:0 0 16px 0; color:#1e293b; font-size:16px;"><i class="fas fa-user-plus" style="color:var(--sipce-primary);"></i> Nuevos Pacientes por Mes ({{ $estadisticas['anio'] ?? now()->year }})</h4>
        <div style="display:flex; align-items:end; gap:8px; height:200px; padding-top:10px;">
            @php
                $nuevosPorMes = $estadisticas['nuevos_por_mes'] ?? collect();
                $maxNuevos = $nuevosPorMes->max('total') ?? 1;
                $nombreMeses = [1=>'Ene',2=>'Feb',3=>'Mar',4=>'Abr',5=>'May',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dic'];
            @endphp
            @forelse($nuevosPorMes as $item)
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%;">
                <span style="font-size:11px; color:#475569; font-weight:600; margin-bottom:4px;">{{ $item->total }}</span>
                <div style="width:100%; max-width:50px; height:{{ $maxNuevos > 0 ? round(($item->total / $maxNuevos) * 150) : 0 }}px; background:linear-gradient(180deg,var(--sipce-primary),var(--sipce-primary-dark)); border-radius:6px 6px 0 0;"></div>
                <span style="font-size:10px; color:#94a3b8; margin-top:6px; text-align:center;">{{ $nombreMeses[(int)$item->mes] ?? $item->mes }}</span>
            </div>
            @empty
            <div style="width:100%; text-align:center; color:#94a3b8; padding:40px;">No hay datos disponibles</div>
            @endforelse
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-bottom:24px;">
        <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06);">
            <h4 style="margin:0 0 16px 0; color:#1e293b; font-size:16px;"><i class="fas fa-tags" style="color:#10b981;"></i> Pacientes por Estado</h4>
            @php
                $porEstado = $estadisticas['por_estado'] ?? collect();
                $totalEstados = $porEstado->sum('total') ?: 1;
                $barColors = ['Nuevo' => 'var(--sipce-primary)', 'En seguimiento' => '#f59e0b', 'Estable' => '#10b981', 'Derivado' => '#8b5cf6', 'En espera' => '#fbbf24', 'Inactivo' => '#94a3b8'];
            @endphp
            @forelse($porEstado as $estado)
            @php
                $pct = round(($estado->total / $totalEstados) * 100);
                $barColor = $barColors[$estado->estado] ?? 'var(--sipce-primary)';
            @endphp
            <div style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span style="font-size:13px; color:#475569; font-weight:500;">{{ ucfirst($estado->estado) }}</span>
                    <span style="font-size:13px; color:#64748b;">{{ $estado->total }} ({{ $pct }}%)</span>
                </div>
                <div style="width:100%; height:10px; background:#f1f5f9; border-radius:5px; overflow:hidden;">
                    <div style="width:{{ $pct }}%; height:100%; background:{{ $barColor }}; border-radius:5px;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#94a3b8; padding:30px;">No hay datos disponibles</div>
            @endforelse
        </div>

        <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06);">
            <h4 style="margin:0 0 16px 0; color:#1e293b; font-size:16px;"><i class="fas fa-book" style="color:#f59e0b;"></i> Diarios por Mes ({{ $estadisticas['anio'] ?? now()->year }})</h4>
            @php
                $diariosPorMes = $estadisticas['diarios_por_mes'] ?? collect();
                $maxDiarios = $diariosPorMes->max('total') ?? 1;
            @endphp
            @forelse($diariosPorMes as $dm)
            @php
                $pct = $maxDiarios > 0 ? round(($dm->total / $maxDiarios) * 100) : 0;
            @endphp
            <div style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span style="font-size:13px; color:#475569; font-weight:500;">{{ $nombreMeses[(int)$dm->mes] ?? $dm->mes }}</span>
                    <span style="font-size:13px; color:#64748b;">{{ $dm->total }} entradas</span>
                </div>
                <div style="width:100%; height:10px; background:#f1f5f9; border-radius:5px; overflow:hidden;">
                    <div style="width:{{ $pct }}%; height:100%; background:linear-gradient(90deg,#f59e0b,#fbbf24); border-radius:5px;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#94a3b8; padding:30px;">No hay datos disponibles</div>
            @endforelse
        </div>
    </div>
</div>
@endsection