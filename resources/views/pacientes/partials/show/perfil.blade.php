<div class="paciente-profile">
    <div class="paciente-avatar-large">
        {{ strtoupper(substr($paciente->detalle->nombre ?? $paciente->detalle->nombre_completo ?? 'P', 0, 1)) }}
    </div>
    <div class="paciente-name">
        <h3>{{ $paciente->detalle->nombre ?? '' }} {{ $paciente->detalle->apellido ?? '' }}</h3>
        <p>
            <i class="fas fa-hashtag"></i>
            Expediente #{{ $paciente->numero_expediente }}
            <span class="mx-2">•</span>
            
            @if($paciente->tipo_paciente === 'adulto' && $paciente->detalle->cedula)
                <i class="fas fa-id-card"></i>
                Cédula: {{ $paciente->detalle->cedula }}
            @elseif($paciente->detalle->cedula ?? false)
                <i class="fas fa-id-card"></i>
                Cédula: {{ $paciente->detalle->cedula }}
            @endif
            
            @if($paciente->user)
            <span class="mx-2">•</span>
            <i class="fas fa-check-circle" style="color: #10b981;"></i>
            <span style="color: #059669;">Con acceso al sistema</span>
            @endif
        </p>
    </div>
</div>