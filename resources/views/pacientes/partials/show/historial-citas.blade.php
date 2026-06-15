<div class="historial-card">
    <div class="historial-header">
        <h3>
            <i class="fas fa-calendar-alt"></i>
            Historial de Citas
        </h3>
    </div>

    <div class="historial-body">
        @php
            $citas = $paciente->citas ?? collect([]);
        @endphp

        @if($citas->count() > 0)
            <div class="citas-timeline">
                @foreach($citas as $cita)
                    @php
                        $estadoClass = 'programada';
                        $iconClass = 'fa-calendar-check';
                        
                        if($cita->fecha < date('Y-m-d') && $cita->estado != 'cancelada') {
                            $estadoClass = 'completada';
                            $iconClass = 'fa-check-circle';
                        } elseif($cita->estado == 'cancelada') {
                            $estadoClass = 'cancelada';
                            $iconClass = 'fa-times-circle';
                        } elseif($cita->estado == 'pendiente') {
                            $estadoClass = 'pendiente';
                            $iconClass = 'fa-clock';
                        }
                    @endphp

                    <div class="cita-item">
                        <div class="cita-icon {{ $estadoClass }}">
                            <i class="fas {{ $iconClass }}"></i>
                        </div>
                        
                        <div class="cita-content">
                            <div class="cita-header">
                                <span class="cita-fecha">
                                    <i class="far fa-calendar me-2"></i>
                                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                </span>
                                <span class="cita-estado {{ $estadoClass }}">
                                    {{ ucfirst($cita->estado ?? 'Programada') }}
                                </span>
                            </div>
                            
                            <div class="cita-objetivo">
                                <i class="fas fa-bullseye"></i>
                                {{ $cita->objetivo ?? 'Sin objetivo definido' }}
                            </div>
                            
                            @if($cita->planificacion)
                            <div class="cita-objetivo" style="margin-top: 8px;">
                                <i class="fas fa-tasks"></i>
                                Planificación: {{ Str::limit($cita->planificacion, 100) }}
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-citas">
                <i class="fas fa-calendar-times"></i>
                <p>No hay citas registradas para este paciente</p>
                <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}" class="btn-nueva-cita">
                    <i class="fas fa-plus-circle"></i>
                    Programar primera cita
                </a>
            </div>
        @endif
    </div>
</div>