@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="page-header">
        <div class="header-content">
            <h1><i class="fas fa-chart-bar me-2"></i> Reportes</h1>
            <p>EstadÃ­sticas y anÃ¡lisis del sistema</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
        <a href="{{ route('reportes.pacientes') }}" style="text-decoration:none;">
            <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border-left:4px solid var(--sipce-primary); transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)'">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,var(--sipce-primary),var(--sipce-primary-dark)); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-users" style="font-size:24px; color:white;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; color:#1e293b; font-size:18px;">Pacientes</h3>
                        <p style="margin:4px 0 0 0; color:#64748b; font-size:13px;">EstadÃ­sticas de pacientes</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reportes.citas') }}" style="text-decoration:none;">
            <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border-left:4px solid #10b981; transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)'">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#10b981,#38ef7d); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-calendar-check" style="font-size:24px; color:white;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; color:#1e293b; font-size:18px;">Citas</h3>
                        <p style="margin:4px 0 0 0; color:#64748b; font-size:13px;">Reporte de citas</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reportes.evolucion') }}" style="text-decoration:none;">
            <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border-left:4px solid #f59e0b; transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)'">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#f59e0b,#fbbf24); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-chart-line" style="font-size:24px; color:white;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; color:#1e293b; font-size:18px;">EvoluciÃ³n</h3>
                        <p style="margin:4px 0 0 0; color:#64748b; font-size:13px;">EvoluciÃ³n de pacientes</p>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ route('reportes.exportar') }}" style="text-decoration:none;">
            <div style="background:white; border-radius:16px; padding:30px; box-shadow:0 4px 20px rgba(0,0,0,0.06); border-left:4px solid #0ea5e9; transition:transform 0.2s, box-shadow 0.2s; cursor:pointer;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.06)'">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#0ea5e9,#38bdf8); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-file-csv" style="font-size:24px; color:white;"></i>
                    </div>
                    <div>
                        <h3 style="margin:0; color:#1e293b; font-size:18px;">Exportar CSV</h3>
                        <p style="margin:4px 0 0 0; color:#64748b; font-size:13px;">Exportar datos del sistema</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
