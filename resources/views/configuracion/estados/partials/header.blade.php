{{-- configuracion/estados/partials/header.blade.php --}}
<div class="page-header">
    <div class="header-content">
        <h1>
            <i class="fas fa-tag me-2"></i>
            Gestión de Estados
        </h1>
        <p>Administre los estados clínicos de los pacientes</p>
    </div>
    <div class="header-buttons">
        @if(Auth::user()->hasPermission('estados.create'))
        <button class="btn-nuevo" onclick="abrirModalCrear()">
            <i class="fas fa-plus-circle"></i>
            Nuevo Estado
        </button>
        @endif
    </div>
</div>
