{{-- resources/views/pacientes/partials/header.blade.php --}}
<div class="pacientes-header">
    <div>
        <h1><i class="fas fa-users"></i> Pacientes</h1>
        <p>Gestión de pacientes del sistema</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button class="btn-nuevo" onclick="openModalAdulto()">
            <i class="fas fa-user-tie"></i> Adulto
        </button>
        <button class="btn-nuevo" onclick="openModalAdolescente()">
            <i class="fas fa-user"></i> Adolescente
        </button>
        <button class="btn-nuevo" onclick="openModalNino()">
            <i class="fas fa-child"></i> Niño
        </button>
    </div>
</div>