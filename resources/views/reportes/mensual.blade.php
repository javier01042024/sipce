@extends('layouts.app')

@section('title', 'Reporte Mensual de Pacientes')

@section('styles')
<style>
    .reporte-container { max-width: 1400px; margin: 0 auto; }
    .reporte-header { background: #fff; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .reporte-filters { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin-top: 15px; }
    .reporte-filters .field { flex: 1; min-width: 150px; }
    .reporte-filters label { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px; }
    .reporte-filters select, .reporte-filters input { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
    .btn-filtrar { background: #11998e; color: #fff; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; white-space: nowrap; }
    .btn-filtrar:hover { background: #0d8a7f; }
    .btn-exportar { background: #10b981; color: #fff; border: none; padding: 10px 25px; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-exportar:hover { background: #059669; }
    .institucional-header { margin-bottom: 20px; }
    .institucional-header h3 { margin: 2px 0; font-size: 14px; color: #2d3748; }
    .institucional-header h2 { margin: 2px 0; font-size: 13px; color: #2d3748; }
    .institucional-header h4 { margin: 2px 0; font-size: 13px; color: #2d3748; }
    .reporte-title { color: #dc2626; font-size: 18px; font-weight: 700; text-align: center; margin: 15px 0 5px; }
    .reporte-year { text-align: center; font-size: 14px; font-weight: 600; color: #2d3748; }
    .reporte-table { width: 100%; border-collapse: collapse; font-size: 12px; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .reporte-table th { background: #d9e2f3; color: #1e293b; padding: 8px 6px; font-weight: 700; text-align: center; border: 1px solid #94a3b8; font-size: 11px; }
    .reporte-table td { padding: 6px; border: 1px solid #e2e8f0; vertical-align: middle; }
    .reporte-table tr:hover td { background: #f0f9ff; }
    .reporte-table td.center { text-align: center; }
    .stats-row { display: flex; gap: 15px; margin-top: 15px; }
    .stat-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; text-align: center; }
    .stat-box .number { font-size: 24px; font-weight: 700; color: #11998e; }
    .stat-box .label { font-size: 12px; color: #64748b; margin-top: 4px; }
    .firmas-section { margin-top: 40px; display: flex; justify-content: space-around; }
    .firma-box { text-align: center; }
    .firma-line { width: 200px; border-top: 1px solid #333; margin-top: 50px; padding-top: 5px; font-size: 12px; color: #64748b; }
</style>
@endsection

@section('content')
<div class="reporte-container">
    <div class="reporte-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="margin: 0; color: #2d3748;"><i class="fas fa-file-excel"></i> Reporte Mensual de Pacientes</h2>
                <p style="margin: 5px 0 0; color: #64748b; font-size: 14px;">Registro Individualizado de Pacientes de Salud Mental</p>
            </div>
            <a href="{{ route('reportes.exportar-mensual', ['mes' => $mes, 'anio' => $anio, 'municipio' => $municipio]) }}" class="btn-exportar">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>

        <div class="reporte-filters">
            <form action="{{ route('reportes.mensual') }}" method="GET" style="display: contents;">
                <div class="field">
                    <label>Mes</label>
                    <select name="mes">
                        @php $meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre']; @endphp
                        @foreach($meses as $num => $nombre)
                        <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Año</label>
                    <input type="number" name="anio" value="{{ $anio }}" min="2020" max="2030">
                </div>
                <div class="field">
                    <label>Municipio</label>
                    <select name="municipio">
                        <option value="">Todos</option>
                        @foreach($municipios as $m)
                        <option value="{{ $m }}" {{ $municipio == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-filtrar"><i class="fas fa-filter"></i> Filtrar</button>
            </form>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <div class="number">{{ $pacientes->count() }}</div>
                <div class="label">Pacientes Atendidos</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $pacientes->where('tipo_paciente', 'adulto')->count() }}</div>
                <div class="label">Adultos</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $pacientes->where('tipo_paciente', 'adolescente')->count() }}</div>
                <div class="label">Adolescentes</div>
            </div>
            <div class="stat-box">
                <div class="number">{{ $pacientes->where('tipo_paciente', 'niño')->count() }}</div>
                <div class="label">Niños</div>
            </div>
        </div>
    </div>

    {{-- ENCABEZADO INSTITUCIONAL + TABLA --}}
    <div style="background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <div class="institucional-header">
            <h3>VICEMINISTERIO DE REDES DE SALUD COLECTIVA</h3>
            <h2>DIRECCIÓN GENERAL DE PROGRAMAS DE SALUD</h2>
            <h4>PROGRAMA NACIONAL SALUD MENTAL</h4>
        </div>

        <div class="reporte-title">REGISTRO INDIVIDUALIZADO DE PACIENTES DE SALUD MENTAL</div>
        <div class="reporte-year">AÑO {{ $anio }} — MES: {{ strtoupper($meses[$mes] ?? '') }}</div>

        <table class="reporte-table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th style="width: 40px;">Nº</th>
                    <th>APELLIDO Y NOMBRE</th>
                    <th style="width: 110px;">Nº. DE CÉDULA</th>
                    <th style="width: 45px;">EDAD</th>
                    <th style="width: 130px;">MUNICIPIO</th>
                    <th>DIRECCIÓN DE HABITACIÓN</th>
                    <th style="width: 110px;">Nº TELÉFONO</th>
                    <th style="width: 130px;">CONSULTORIO POPULAR</th>
                    <th style="width: 70px;">ASIC</th>
                    <th style="width: 60px;">SEXO (F/M)</th>
                    <th style="width: 90px;">TIPO CONSULTA (P/S)</th>
                    <th>DX. MÉDICO</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $i => $paciente)
                @php
                    $detalle = $paciente->detalle;
                    $edad = $detalle && $detalle->fecha_nacimiento ? \Carbon\Carbon::parse($detalle->fecha_nacimiento)->age : '';
                    $sexo = $detalle ? strtoupper(substr($detalle->genero ?? '', 0, 1)) : '';
                    $tipoCons = $paciente->tipo_atencion === 'publico' ? 'P' : 'S';
                    $dx = '';
                    if ($paciente->diagnosticoPrincipal) {
                        $dx = ($paciente->diagnosticoPrincipal->codigo_cie ? $paciente->diagnosticoPrincipal->codigo_cie . ' - ' : '') . $paciente->diagnosticoPrincipal->diagnostico;
                    } elseif ($paciente->diagnostico_preliminar) {
                        $dx = $paciente->diagnostico_preliminar;
                    }
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $detalle ? $detalle->apellido . ' ' . $detalle->nombre : 'N/A' }}</td>
                    <td class="center">{{ $paciente->cedula_paciente ?? '' }}</td>
                    <td class="center">{{ $edad }}</td>
                    <td>{{ $paciente->municipio ?? '' }}</td>
                    <td>{{ $detalle->direccion ?? '' }}</td>
                    <td class="center">{{ $paciente->telefono ?? '' }}</td>
                    <td></td>
                    <td class="center"></td>
                    <td class="center">{{ $sexo }}</td>
                    <td class="center">{{ $tipoCons }}</td>
                    <td>{{ $dx }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align: center; padding: 30px; color: #94a3b8;">
                        No hay pacientes atendidos en este período.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="firmas-section">
            <div class="firma-box">
                <div class="firma-line">Firma del Profesional</div>
            </div>
            <div class="firma-box">
                <div class="firma-line">Sello de la Institución</div>
            </div>
            <div class="firma-box">
                <div class="firma-line">Vo.Bo. Coordinación</div>
            </div>
        </div>
    </div>
</div>
@endsection
