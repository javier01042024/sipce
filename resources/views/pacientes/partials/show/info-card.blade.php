<div class="info-card">

    <div class="card-header-custom">
        <h2>
            <i class="fas fa-clipboard-list"></i>
            Información del paciente
        </h2>
        
        <span class="prioridad-badge {{ $paciente->prioridad }}">
            <i class="fas fa-flag me-1"></i>
            Prioridad {{ $paciente->prioridad }}
        </span>
    </div>

    @include('pacientes.partials.show.perfil')

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-calendar"></i>
                Fecha de nacimiento
            </div>
            <div class="info-value">
                {{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-phone"></i>
                Teléfono
            </div>
            <div class="info-value">
                {{ $paciente->telefono ?? 'No registrado' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-envelope"></i>
                Correo electrónico
            </div>
            <div class="info-value">
                {{ $paciente->email ?? 'No registrado' }}
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-clock"></i>
                Registro creado
            </div>
            <div class="info-value">
                {{ $paciente->created_at->format('d/m/Y') }}
            </div>
        </div>
    </div>

    @include('pacientes.partials.show.datos-clinicos')
    @include('pacientes.partials.show.acciones')

</div>