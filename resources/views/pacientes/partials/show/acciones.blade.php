<div class="action-buttons">
    @if(Auth::user()->hasPermission('pacientes.edit'))
    <a href="{{ route('pacientes.edit', $paciente) }}" class="btn btn-edit">
        <i class="fas fa-edit"></i>
        Editar información
    </a>
    @endif

    @if(Auth::user()->hasPermission('citas.create'))
    <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}" class="btn btn-primary">
        <i class="fas fa-calendar-plus"></i>
        Programar cita
    </a>
    @endif

    @if(Auth::user()->hasPermission('pacientes.show'))
    <a href="{{ route('pacientes.exportar-ficha', $paciente) }}" class="btn btn-export btn-export-pdf">
        <i class="fas fa-file-pdf"></i>
        Exportar ficha completa
    </a>
    @endif

    <a href="{{ route('pacientes.index') }}" class="btn btn-back">
        <i class="fas fa-arrow-left"></i>
        Volver al listado
    </a>
</div>
