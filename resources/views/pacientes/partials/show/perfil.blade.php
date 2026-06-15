<div class="paciente-profile">
    <div class="paciente-avatar-large">
        {{ strtoupper(substr($paciente->nombre_completo, 0, 1)) }}
    </div>
    <div class="paciente-name">
        <h3>{{ $paciente->nombre_completo }}</h3>
        <p>
            <i class="fas fa-hashtag"></i>
            Expediente #{{ $paciente->numero_expediente }}
            <span class="mx-2">•</span>
            <i class="fas fa-id-card"></i>
            Cédula: {{ $paciente->cedula_paciente }}
            
            @if($paciente->user)
            <span class="mx-2">•</span>
            <i class="fas fa-check-circle" style="color: #10b981;"></i>
            <span style="color: #059669;">Con acceso al sistema</span>
            @endif
        </p>
    </div>
</div>