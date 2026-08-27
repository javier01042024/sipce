<div id="tab-diario" class="tab-panel">
    
    {{-- VERIFICAR SI EL PACIENTE TIENE USUARIO ASOCIADO --}}
    @if($paciente->user)
        
        {{-- OBTENER LAS ENTRADAS DEL DIARIO DEL USUARIO --}}
        @php
            $diarios = \App\Models\Diario::where('user_id', $paciente->user_id)
                ->orderBy('fecha', 'desc')
                ->get();
        @endphp

        {{-- CABECERA CON BOTÓN DE NUEVO REGISTRO --}}
        <div class="diario-card">
            <div class="diario-header">
                <h3>
                    <i class="fas fa-book-medical"></i>
                    Diario de {{ $paciente->user->name }}
                </h3>
                <a href="{{ route('diarios.create') }}?user_id={{ $paciente->user_id }}" class="btn-nuevo-diario">
                    <i class="fas fa-plus-circle"></i>
                    Nuevo Registro
                </a>
            </div>

            <div class="diario-body">
                @if($diarios->count() > 0)
                    {{-- ESTADÍSTICAS RÁPIDAS --}}
                    <div class="diario-stats-mini">
                        <div class="stat-mini">
                            <i class="fas fa-book"></i>
                            <span>{{ $diarios->count() }} registros</span>
                        </div>
                        <div class="stat-mini">
                            <i class="fas fa-calendar-day"></i>
                            <span>{{ $diarios->where('fecha', date('Y-m-d'))->count() }} hoy</span>
                        </div>
                        <div class="stat-mini">
                            <i class="fas fa-calendar-week"></i>
                            <span>{{ $diarios->where('fecha', '>=', date('Y-m-d', strtotime('-7 days')))->count() }} esta semana</span>
                        </div>
                    </div>

                    {{-- LISTADO DE ENTRADAS --}}
                    <div class="diario-timeline">
                        @foreach($diarios->take(10) as $diario)
                            @php
                                $emocion = 'neutral';
                                $emocionIcon = '😐';
                                $emocionClass = 'neutral';
                                
                                if(str_contains(strtolower($diario->contenido), 'bien') || 
                                   str_contains(strtolower($diario->contenido), 'feliz') ||
                                   str_contains(strtolower($diario->contenido), 'contento') ||
                                   str_contains(strtolower($diario->contenido), 'alegre') ||
                                   str_contains(strtolower($diario->contenido), 'agradecido') ||
                                   str_contains(strtolower($diario->contenido), 'positivo')) {
                                    $emocion = 'positivo';
                                    $emocionIcon = '😊';
                                    $emocionClass = 'positivo';
                                } elseif(str_contains(strtolower($diario->contenido), 'mal') || 
                                         str_contains(strtolower($diario->contenido), 'triste') ||
                                         str_contains(strtolower($diario->contenido), 'ansioso') ||
                                         str_contains(strtolower($diario->contenido), 'deprimido') ||
                                         str_contains(strtolower($diario->contenido), 'enojado') ||
                                         str_contains(strtolower($diario->contenido), 'negativo')) {
                                    $emocion = 'negativo';
                                    $emocionIcon = '😔';
                                    $emocionClass = 'negativo';
                                }
                            @endphp

                            <div class="diario-item">
                                <div class="diario-icon {{ $emocionClass }}">
                                    <span class="emocion-emoji">{{ $emocionIcon }}</span>
                                </div>
                                <div class="diario-content">
                                    <div class="diario-item-header">
                                        <span class="diario-fecha">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}
                                        </span>
                                        <span class="diario-emocion-badge {{ $emocionClass }}">
                                            {{ ucfirst($emocion) }}
                                        </span>
                                    </div>
                                    
                                    <div class="diario-texto">
                                        <i class="fas fa-quote-left"></i>
                                        {{ Str::limit($diario->contenido, 200) }}
                                    </div>
                                    
                                    <div class="diario-item-footer">
                                        <span class="diario-hora">
                                            <i class="far fa-clock"></i>
                                            {{ $diario->created_at->format('H:i') }}
                                        </span>
                                        <div class="diario-item-actions">
                                            <a href="{{ route('diarios.show', $diario) }}" class="btn-icon-mini view" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('diarios.edit', $diario) }}" class="btn-icon-mini edit" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($diarios->count() > 10)
                        <div class="ver-mas-container">
                            <a href="{{ route('diarios.index') }}?user_id={{ $paciente->user_id }}" class="btn-ver-mas">
                                <i class="fas fa-list"></i>
                                Ver todos los registros ({{ $diarios->count() }})
                            </a>
                        </div>
                    @endif

                @else
                    <div class="empty-state-tab">
                        <div class="empty-icon-tab">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h4>Sin registros en el diario</h4>
                        <p>{{ $paciente->user->name }} aún no tiene entradas en su diario</p>
                        <a href="{{ route('diarios.create') }}?user_id={{ $paciente->user_id }}" class="btn-empty-tab">
                            <i class="fas fa-plus-circle"></i>
                            Crear primer registro
                        </a>
                    </div>
                @endif
            </div>
        </div>

    @else
        {{-- PACIENTE SIN USUARIO --}}
        <div class="diario-card">
            <div class="diario-header">
                <h3>
                    <i class="fas fa-book-medical"></i>
                    Diario del Paciente
                </h3>
            </div>

            <div class="diario-body">
                <div class="empty-state-tab sin-acceso">
                    <div class="empty-icon-tab">
                        <i class="fas fa-user-lock"></i>
                    </div>
                    <h4>Paciente sin acceso al sistema</h4>
                    <p>
                        <i class="fas fa-info-circle"></i>
                        Este paciente no tiene un usuario asociado en el sistema, por lo tanto no puede tener un diario personal.
                    </p>
                    <p class="text-muted">
                        Para habilitar el diario, primero debe darle acceso al sistema desde la sección de 
                        <strong>Datos del Paciente</strong>.
                    </p>
                </div>
            </div>
        </div>

    @endif

</div>