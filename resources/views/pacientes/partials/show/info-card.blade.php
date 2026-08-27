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
        {{-- Tipo de Paciente --}}
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-user-tag"></i>
                Tipo de Paciente
            </div>
            <div class="info-value">
                <span class="badge bg-primary">
                    {{ ucfirst($paciente->tipo_paciente) }}
                </span>
            </div>
        </div>

        {{-- Fecha de Nacimiento --}}
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-calendar"></i>
                Fecha de nacimiento
            </div>
            <div class="info-value">
                {{ $paciente->detalle->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->detalle->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}
            </div>
        </div>

        {{-- Teléfono (varía según tipo) --}}
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-phone"></i>
                Teléfono
            </div>
            <div class="info-value">
                @if($paciente->tipo_paciente === 'adulto')
                    {{ $paciente->detalle->telefono ?? 'No registrado' }}
                @elseif($paciente->tipo_paciente === 'adolescente')
                    {{ $paciente->detalle->telefono_personal ?? $paciente->detalle->telefono_representante ?? 'No registrado' }}
                @elseif($paciente->tipo_paciente === 'niño')
                    {{ $paciente->detalle->telefono_contacto ?? 'No registrado' }}
                @else
                    No registrado
                @endif
            </div>
        </div>

        {{-- Email (varía según tipo) --}}
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-envelope"></i>
                Correo electrónico
            </div>
            <div class="info-value">
                @if($paciente->tipo_paciente === 'adulto')
                    {{ $paciente->detalle->email ?? 'No registrado' }}
                @elseif($paciente->tipo_paciente === 'adolescente')
                    {{ $paciente->detalle->email_personal ?? $paciente->detalle->email_representante ?? 'No registrado' }}
                @else
                    No registrado
                @endif
            </div>
        </div>

        {{-- Registro creado --}}
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-clock"></i>
                Registro creado
            </div>
            <div class="info-value">
                {{ $paciente->created_at->format('d/m/Y') }}
            </div>
        </div>

        {{-- Tipo de Atención --}}
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-hospital-user"></i>
                Tipo de Atención
            </div>
            <div class="info-value">
                <span class="badge bg-{{ $paciente->tipo_atencion === 'privado' ? 'success' : 'info' }}">
                    {{ ucfirst($paciente->tipo_atencion) }}
                </span>
            </div>
        </div>
    </div>

    @include('pacientes.partials.show.datos-clinicos')
    @include('pacientes.partials.show.acciones')
</div>