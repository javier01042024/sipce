{{-- DIRECCIÓN --}}
<div class="text-section">
    <h4>
        <i class="fas fa-map-marker-alt"></i>
        Dirección
    </h4>
    <div class="text-box">
        {{ $paciente->direccion ?? 'No registrada' }}
    </div>
</div>

{{-- MOTIVO DE CONSULTA --}}
<div class="text-section">
    <h4>
        <i class="fas fa-notes-medical"></i>
        Motivo de consulta
    </h4>
    <div class="text-box">
        {{ $paciente->motivo_consulta ?? 'No registrado' }}
    </div>
</div>

{{-- DIAGNÓSTICO --}}
<div class="text-section">
    <h4>
        <i class="fas fa-stethoscope"></i>
        Diagnóstico preliminar
    </h4>
    <div class="text-box">
        {{ $paciente->diagnostico_preliminar ?? 'Sin diagnóstico registrado' }}
    </div>
</div>