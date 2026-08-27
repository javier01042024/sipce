<div class="paciente-info-badge">
    <div class="paciente-avatar">
        {{ strtoupper(substr($paciente->nombre_completo, 0, 1)) }}
    </div>
    <div class="paciente-info-text">
        <strong>{{ $paciente->nombre_completo }}</strong><br>
        <small>
            <i class="fas fa-hashtag me-1"></i>
            Expediente: #{{ $paciente->numero_expediente }}
            <span class="mx-2">•</span>
            <i class="fas fa-id-card me-1"></i>
            Cédula: {{ $paciente->cedula_paciente }}
        </small>
    </div>
</div>