{{-- DATOS CLÍNICOS SEGÚN TIPO DE PACIENTE --}}

@if($paciente->tipo_paciente === 'adulto')
    {{-- DATOS ESPECÍFICOS DE ADULTO --}}
    
    {{-- DIRECCIÓN --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-map-marker-alt"></i>
            Dirección
        </h4>
        <div class="text-box">
            {{ $paciente->detalle->direccion ?? 'No registrada' }}
        </div>
    </div>

    {{-- OCUPACIÓN --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-briefcase"></i>
            Ocupación
        </h4>
        <div class="text-box">
            {{ $paciente->detalle->ocupacion ?? 'No registrada' }}
            @if($paciente->detalle->lugar_trabajo)
                <br><small class="text-muted">Lugar de trabajo: {{ $paciente->detalle->lugar_trabajo }}</small>
            @endif
        </div>
    </div>

    {{-- CONTACTO DE EMERGENCIA --}}
    @if($paciente->detalle->nombre_emergencia)
    <div class="text-section">
        <h4>
            <i class="fas fa-phone-alt"></i>
            Contacto de Emergencia
        </h4>
        <div class="text-box">
            <strong>{{ $paciente->detalle->nombre_emergencia }}</strong>
            @if($paciente->detalle->parentesco_emergencia)
                ({{ $paciente->detalle->parentesco_emergencia }})
            @endif
            @if($paciente->detalle->telefono_emergencia)
                <br>Tel: {{ $paciente->detalle->telefono_emergencia }}
            @endif
        </div>
    </div>
    @endif

    {{-- ESTADO CIVIL Y GÉNERO --}}
    <div class="info-grid" style="margin-top: 20px;">
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-venus-mars"></i>
                Género
            </div>
            <div class="info-value">
                {{ $paciente->detalle->genero ?? 'No especificado' }}
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">
                <i class="fas fa-ring"></i>
                Estado Civil
            </div>
            <div class="info-value">
                {{ $paciente->detalle->estado_civil ?? 'No especificado' }}
            </div>
        </div>
    </div>

@elseif($paciente->tipo_paciente === 'adolescente')
    {{-- DATOS ESPECÍFICOS DE ADOLESCENTE --}}
    
    {{-- DATOS ESCOLARES --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-school"></i>
            Información Educativa
        </h4>
        <div class="text-box">
            @if($paciente->detalle->institucion_educativa)
                <strong>Institución:</strong> {{ $paciente->detalle->institucion_educativa }}<br>
            @endif
            @if($paciente->detalle->nivel_educativo)
                <strong>Nivel:</strong> {{ $paciente->detalle->nivel_educativo }}<br>
            @endif
            @if($paciente->detalle->rendimiento_academico)
                <strong>Rendimiento:</strong> {{ $paciente->detalle->rendimiento_academico }}
            @endif
            @if(!$paciente->detalle->institucion_educativa && !$paciente->detalle->nivel_educativo && !$paciente->detalle->rendimiento_academico)
                No registrada
            @endif
        </div>
    </div>

    {{-- REPRESENTANTE --}}
    @if($paciente->detalle->nombre_representante)
    <div class="text-section">
        <h4>
            <i class="fas fa-user-tie"></i>
            Representante Legal
        </h4>
        <div class="text-box">
            <strong>{{ $paciente->detalle->nombre_representante }}</strong>
            @if($paciente->detalle->parentesco)
                ({{ $paciente->detalle->parentesco }})
            @endif
            @if($paciente->detalle->cedula_representante)
                <br>Cédula: {{ $paciente->detalle->cedula_representante }}
            @endif
            @if($paciente->detalle->telefono_representante)
                <br>Tel: {{ $paciente->detalle->telefono_representante }}
            @endif
        </div>
    </div>
    @endif

    {{-- RELACIONES SOCIALES --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-users"></i>
            Relaciones Sociales
        </h4>
        <div class="text-box">
            @if($paciente->detalle->tiene_amigos)
                <strong>Amigos:</strong> {{ $paciente->detalle->tiene_amigos }}<br>
            @endif
            @if($paciente->detalle->actividades_grupales)
                <strong>Act. Grupales:</strong> {{ $paciente->detalle->actividades_grupales }}<br>
            @endif
            @if($paciente->detalle->relaciones_sociales)
                <strong>Observaciones:</strong> {{ $paciente->detalle->relaciones_sociales }}
            @endif
            @if(!$paciente->detalle->tiene_amigos && !$paciente->detalle->actividades_grupales && !$paciente->detalle->relaciones_sociales)
                No registradas
            @endif
        </div>
    </div>

@elseif($paciente->tipo_paciente === 'niño')
    {{-- DATOS ESPECÍFICOS DE NIÑO --}}
    
    {{-- DATOS DE LOS PADRES --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-people-arrows"></i>
            Información de los Padres
        </h4>
        <div class="text-box">
            @if($paciente->detalle->nombre_padre)
                <strong>Padre:</strong> {{ $paciente->detalle->nombre_padre }}
                @if($paciente->detalle->edad_padre)
                    ({{ $paciente->detalle->edad_padre }} años)
                @endif
                @if($paciente->detalle->ocupacion_padre)
                    - {{ $paciente->detalle->ocupacion_padre }}
                @endif
                <br>
            @endif
            @if($paciente->detalle->nombre_madre)
                <strong>Madre:</strong> {{ $paciente->detalle->nombre_madre }}
                @if($paciente->detalle->edad_madre)
                    ({{ $paciente->detalle->edad_madre }} años)
                @endif
                @if($paciente->detalle->ocupacion_madre)
                    - {{ $paciente->detalle->ocupacion_madre }}
                @endif
            @endif
            @if(!$paciente->detalle->nombre_padre && !$paciente->detalle->nombre_madre)
                No registrada
            @endif
        </div>
    </div>

    {{-- ESCOLARIDAD --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-school"></i>
            Escolaridad
        </h4>
        <div class="text-box">
            @if($paciente->detalle->grado_instruccion)
                <strong>Grado:</strong> {{ $paciente->detalle->grado_instruccion }}<br>
            @endif
            @if($paciente->detalle->maestro)
                <strong>Maestro/a:</strong> {{ $paciente->detalle->maestro }}<br>
            @endif
            @if($paciente->detalle->rendimiento_escolar)
                <strong>Rendimiento:</strong> {{ $paciente->detalle->rendimiento_escolar }}
            @endif
            @if(!$paciente->detalle->grado_instruccion && !$paciente->detalle->maestro && !$paciente->detalle->rendimiento_escolar)
                No registrada
            @endif
        </div>
    </div>

    {{-- DATOS PERINATALES --}}
    <div class="text-section">
        <h4>
            <i class="fas fa-baby"></i>
            Datos Perinatales
        </h4>
        <div class="text-box">
            @if($paciente->detalle->tipo_parto)
                <strong>Tipo de parto:</strong> {{ $paciente->detalle->tipo_parto }}<br>
            @endif
            @if($paciente->detalle->dificultades_parto)
                <strong>Dificultades:</strong> {{ $paciente->detalle->dificultades_parto }}<br>
            @endif
            @if($paciente->detalle->comportamiento_bebe)
                <strong>Comportamiento bebé:</strong> {{ $paciente->detalle->comportamiento_bebe }}
            @endif
            @if(!$paciente->detalle->tipo_parto && !$paciente->detalle->dificultades_parto && !$paciente->detalle->comportamiento_bebe)
                No registrados
            @endif
        </div>
    </div>
@endif

{{-- SECCIONES COMUNES PARA TODOS LOS TIPOS --}}

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

{{-- DIAGNÓSTICOS --}}
<div class="text-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
        <h4 style="margin: 0;">
            <i class="fas fa-stethoscope"></i>
            Diagnósticos
        </h4>
        <button type="button" onclick="abrirModalDiagnostico({{ $paciente->id }})" style="padding: 6px 14px; background: #11998e; color: #fff; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer;">
            <i class="fas fa-plus"></i> Nuevo
        </button>
    </div>

    @if($paciente->diagnosticos->count())
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px;">CIE-10</th>
                    <th style="padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px;">DIAGNÓSTICO</th>
                    <th style="padding: 8px 10px; text-align: center; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px;">PRINCIPAL</th>
                    <th style="padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px;">FECHA</th>
                    <th style="padding: 8px 10px; text-align: center; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 11px;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($paciente->diagnosticos as $diag)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 10px; font-weight: 600; color: var(--sipce-primary);">{{ $diag->codigo_cie ?? 'â€”' }}</td>
                    <td style="padding: 8px 10px;">
                        {{ $diag->diagnostico }}
                        @if($diag->observaciones)
                        <br><small style="color: #94a3b8; font-size: 11px;">{{ Str::limit($diag->observaciones, 80) }}</small>
                        @endif
                    </td>
                    <td style="padding: 8px 10px; text-align: center;">
                        @if($diag->es_principal)
                        <span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Principal</span>
                        @else
                        <span style="color: #cbd5e1;">â€”</span>
                        @endif
                    </td>
                    <td style="padding: 8px 10px; color: #64748b; font-size: 12px;">{{ $diag->fecha ? $diag->fecha->format('d/m/Y') : 'â€”' }}</td>
                    <td style="padding: 8px 10px; text-align: center;">
                        <button type="button" onclick='editarDiagnostico({{ $paciente->id }}, @json($diag))' title="Editar" style="background:none;border:none;color:var(--sipce-primary);cursor:pointer;font-size:13px;margin:0 3px;">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" onclick="eliminarDiagnostico({{ $diag->id }})" title="Eliminar" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:13px;margin:0 3px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">
        <i class="fas fa-stethoscope" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
        Sin diagnósticos registrados
    </div>
    @endif
</div>