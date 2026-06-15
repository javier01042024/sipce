<div class="action-buttons">
    <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-edit">
        <i class="fas fa-edit"></i>
        Editar información
    </a>

    <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-edit" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <i class="fas fa-calendar-plus"></i>
        Programar cita
    </a>

    <a href="{{ route('pacientes.index') }}" class="btn btn-back">
        <i class="fas fa-arrow-left"></i>
        Volver al listado
    </a>
</div>