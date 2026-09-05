@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-chart-line me-2"></i> Reporte de Evolución</h1>
            <p>Tendencias de pacientes, estados y diarios del sistema</p>
        </div>
        <div class="header-buttons">
            <a href="{{ route('reportes.index') }}" class="btn-nuevo">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div style="background:white; border-radius:16px; padding:24px; box-shadow:0 4px 20px rgba(0,0,0,0.06); margin-bottom:24px;">
        <h4 style="margin:0 0 16px 0; color:#1e293b; font-size:16px;"><i class="fas fa-user-plus" style="color:var(--sipce-primary);"></i> Nuevos Pacientes por Mes</h4>
        <div style="display:flex; align-items:end; gap:8px; height:200px; padding-top:10px;">
            @php
                $nuevosPorMes = $estadisticas['nuevos_por_mes'] ?? collect();
                $maxNuevos = $nuevosPorMes->max('total') ?? 1;
            @endphp
            @forelse($nuevosPorMes as $item)
            <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:flex-end; height:100%;">
                <span style="font-size:11px; color:#475569; font-weight:600; margin-bottom:4px;">{{ $item->total }}</span>
                <div style="width:100%; max-width:50px; height:{{ $maxNuevos > 0 ? round(($item->total / $maxNuevos) * 150) : 0 }}px; background:linear-gradient(180deg,var(--sipce-primary),var(--sipce-primary-dark)); border-radius:6px 6px 0 0;"></div>
                <span style="font-size:10px; color:#94a3b8; margin-top:6px; text-align:center;">{{ $item->mes }}</span>
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
            @endphp
            @forelse($porEstado as $estado)
            @php
                $pct = round(($estado->total / $totalEstados) * 100);
                $barColors = ['activo' => '#10b981', 'inactivo' => '#94a3b8', 'egresado' => 'var(--sipce-primary)'];
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
            <h4 style="margin:0 0 16px 0; color:#1e293b; font-size:16px;"><i class="fas fa-book" style="color:#f59e0b;"></i> Diarios por Mes</h4>
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
                    <span style="font-size:13px; color:#475569; font-weight:500;">{{ $dm->mes }}</span>
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
