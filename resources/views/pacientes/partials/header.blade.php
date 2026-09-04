{{-- resources/views/pacientes/partials/header.blade.php --}}
<div class="pacientes-header">
    <div>
        <h1><i class="fas fa-users"></i> Pacientes</h1>
        <p>GestiÃ³n de pacientes del sistema</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="btn-nuevo" onclick="openModalAdulto()" style="background: white; color: var(--sipce-primary);">
            <i class="fas fa-user-tie"></i> Adulto
        </button>
        <button class="btn-nuevo" onclick="openModalAdolescente()" style="background: white; color: #f59e0b;">
            <i class="fas fa-user"></i> Adolescente
        </button>
        <button class="btn-nuevo" onclick="openModalNino()" style="background: white; color: #10b981;">
            <i class="fas fa-child"></i> NiÃ±o
        </button>
    </div>
</div>