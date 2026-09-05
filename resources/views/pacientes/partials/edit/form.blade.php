<div class="container-fluid">
    <div class="form-card">
        <div class="card-header-custom">
            <div>
                <h2>
                    <i class="fas fa-edit"></i>
                    Editar Paciente
                </h2>
                <p>
                    Expediente: <strong>#{{ $paciente->numero_expediente }}</strong> | 
                    Tipo: <strong>{{ ucfirst($paciente->tipo_paciente) }}</strong>
                </p>
            </div>
            <a href="{{ route('pacientes.index') }}" class="btn-export btn-export-excel">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>

        <div class="card-body-custom">
            <form action="{{ route('pacientes.update', $paciente) }}" method="POST" id="formEditPaciente" onsubmit="return validarFormEdit()">
                @csrf
                @method('PUT')
                <input type="hidden" name="tipo_paciente" value="{{ $paciente->tipo_paciente }}">

                {{-- ============================================= --}}
                {{-- CAMPOS COMUNES PARA TODOS LOS TIPOS --}}
                {{-- ============================================= --}}
                <div id="commonFields">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;">
                        <i class="fas fa-clipboard-list" style="color: var(--sipce-primary);"></i> Datos de la Consulta
                    </h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Prioridad</label>
                            <select name="prioridad" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="baja" {{ old('prioridad', $paciente->prioridad) == 'baja' ? 'selected' : '' }}>Baja</option>
                                <option value="media" {{ old('prioridad', $paciente->prioridad) == 'media' ? 'selected' : '' }}>Media</option>
                                <option value="alta" {{ old('prioridad', $paciente->prioridad) == 'alta' ? 'selected' : '' }}>Alta</option>
                                <option value="urgencia" {{ old('prioridad', $paciente->prioridad) == 'urgencia' ? 'selected' : '' }}>Urgencia</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Tipo de Atención</label>
                            <select name="tipo_atencion" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="privado" {{ old('tipo_atencion', $paciente->tipo_atencion) == 'privado' ? 'selected' : '' }}>Privado</option>
                                <option value="publico" {{ old('tipo_atencion', $paciente->tipo_atencion) == 'publico' ? 'selected' : '' }}>Público</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Estado</label>
                            <select name="estado_id" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach($estados as $estado)
                                <option value="{{ $estado->id }}" {{ old('estado_id', $paciente->estado_id) == $estado->id ? 'selected' : '' }}>
                                    {{ $estado->tipo }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full-width">
                            <label>Motivo de Consulta</label>
                            <textarea name="motivo_consulta" class="input-field" rows="3" placeholder="Describa el motivo de la consulta...">{{ old('motivo_consulta', $paciente->motivo_consulta) }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Diagnóstico Preliminar</label>
                            <textarea name="diagnostico_preliminar" class="input-field" rows="3" placeholder="Diagnóstico inicial o impresión clínica...">{{ old('diagnostico_preliminar', $paciente->diagnostico_preliminar) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- ============================================= --}}
                {{-- CAMPOS ESPECÍFICOS SEGÚN TIPO DE PACIENTE --}}
                {{-- ============================================= --}}

                @php
                    // PROTECCIÓN: Si no hay detalle, usar objeto vacío para evitar errores
                    $detalle = $paciente->detalle ?? null;
                @endphp

                {{-- ADULTO --}}
                @if($paciente->tipo_paciente === 'adulto')
                <div id="adultoFields">
                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-id-card" style="color: var(--sipce-primary);"></i> Datos Personales
                    </h4>
                    <div class="grid-form">
                        <div class="field"><label>Nombre *</label><input type="text" name="nombre" class="input-field" value="{{ old('nombre', $detalle->nombre ?? '') }}" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"></div>
                        <div class="field"><label>Apellido *</label><input type="text" name="apellido" class="input-field" value="{{ old('apellido', $detalle->apellido ?? '') }}" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"></div>
                        <div class="field"><label>Cédula *</label>
                            <div style="display: flex; gap: 5px;">
                                <select name="nacionalidad" class="input-field" style="width: 70px; flex-shrink: 0;"><option value="V" {{ old('nacionalidad', substr($detalle->cedula ?? 'V', 0, 1)) == 'V' ? 'selected' : '' }}>V</option><option value="E" {{ old('nacionalidad', substr($detalle->cedula ?? 'V', 0, 1)) == 'E' ? 'selected' : '' }}>E</option></select>
                                <input type="text" name="cedula" class="input-field" style="flex: 1;" value="{{ old('cedula', substr($detalle->cedula ?? '', 1)) }}" required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)" maxlength="8">
                            </div>
                        </div>
                        <div class="field"><label>Fecha de Nacimiento *</label><input type="text" name="fecha_nacimiento" class="input-field" value="{{ old('fecha_nacimiento', isset($detalle->fecha_nacimiento) ? \Carbon\Carbon::parse($detalle->fecha_nacimiento)->format('d/m/Y') : '') }}" placeholder="DD/MM/AAAA" required oninput="formatearFecha(this)" maxlength="10" autocomplete="off"></div>
                        <div class="field"><label>Género</label><select name="genero" class="input-field"><option value="">Seleccione...</option><option value="Masculino" {{ old('genero', $detalle->genero ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option><option value="Femenino" {{ old('genero', $detalle->genero ?? '') == 'Femenino' ? 'selected' : '' }}>Femenino</option><option value="Otro" {{ old('genero', $detalle->genero ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option></select></div>
                        <div class="field"><label>Estado Civil</label><select name="estado_civil" class="input-field"><option value="">Seleccione...</option>@foreach(['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a', 'Unión Libre'] as $ec)<option value="{{ $ec }}" {{ old('estado_civil', $detalle->estado_civil ?? '') == $ec ? 'selected' : '' }}>{{ $ec }}</option>@endforeach</select></div>
                        <div class="field"><label>Teléfono *</label><input type="text" name="telefono" class="input-field" value="{{ old('telefono', $detalle->telefono ?? '') }}" required oninput="this.value = this.value.replace(/[^0-9+]/g, '').slice(0, 15)"></div>
                        <div class="field"><label>Correo electrónico</label><input type="email" name="email" class="input-field" value="{{ old('email', $detalle->email ?? '') }}"></div>
                        <div class="field full-width"><label>Dirección</label><input type="text" name="direccion" class="input-field" value="{{ old('direccion', $detalle->direccion ?? '') }}"></div>
                        <div class="field"><label>Nivel de Instrucción</label><select name="nivel_instruccion" class="input-field"><option value="">Seleccione...</option>@foreach(['Primaria', 'Secundaria', 'Universitario', 'Postgrado', 'Otro'] as $ni)<option value="{{ $ni }}" {{ old('nivel_instruccion', $detalle->nivel_instruccion ?? '') == $ni ? 'selected' : '' }}>{{ $ni }}</option>@endforeach</select></div>
                        <div class="field"><label>Fecha de Entrevista</label><input type="text" name="fecha_entrevista" class="input-field" value="{{ old('fecha_entrevista', isset($detalle->fecha_entrevista) ? \Carbon\Carbon::parse($detalle->fecha_entrevista)->format('d/m/Y') : '') }}" placeholder="DD/MM/AAAA" oninput="formatearFecha(this)" maxlength="10" autocomplete="off"></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-briefcase" style="color: var(--sipce-primary);"></i> Datos Laborales</h4>
                    <div class="grid-form">
                        <div class="field"><label>Ocupación</label><input type="text" name="ocupacion" class="input-field" value="{{ old('ocupacion', $detalle->ocupacion ?? '') }}"></div>
                        <div class="field"><label>Lugar de Trabajo</label><input type="text" name="lugar_trabajo" class="input-field" value="{{ old('lugar_trabajo', $detalle->lugar_trabajo ?? '') }}"></div>
                        <div class="field"><label>Teléfono Trabajo</label><input type="text" name="telefono_trabajo" class="input-field" value="{{ old('telefono_trabajo', $detalle->telefono_trabajo ?? '') }}"></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-phone-alt" style="color: var(--sipce-primary);"></i> Contacto de Emergencia</h4>
                    <div class="grid-form">
                        <div class="field"><label>Nombre</label><input type="text" name="nombre_emergencia" class="input-field" value="{{ old('nombre_emergencia', $detalle->nombre_emergencia ?? '') }}"></div>
                        <div class="field"><label>Teléfono</label><input type="text" name="telefono_emergencia" class="input-field" value="{{ old('telefono_emergencia', $detalle->telefono_emergencia ?? '') }}"></div>
                        <div class="field"><label>Parentesco</label><input type="text" name="parentesco_emergencia" class="input-field" value="{{ old('parentesco_emergencia', $detalle->parentesco_emergencia ?? '') }}"></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-clipboard-list" style="color: var(--sipce-primary);"></i> Motivo de Consulta</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Motivo de consulta</label><textarea name="motivo_consulta" class="input-field" rows="3" placeholder="¿Qué te trajo a consulta? ¿Qué te gustaría que cambiara?">{{ old('motivo_consulta', $detalle->motivo_consulta ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Historia del Problema</label><textarea name="historia_problema" class="input-field" rows="2">{{ old('historia_problema', $detalle->historia_problema ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Frecuencia e intensidad</label><textarea name="frecuencia_sintomas" class="input-field" rows="2" placeholder="Describe la frecuencia e intensidad del problema">{{ old('frecuencia_sintomas', $detalle->frecuencia_sintomas ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Factores Desencadenantes</label><textarea name="factores_desencadenantes" class="input-field" rows="2">{{ old('factores_desencadenantes', $detalle->factores_desencadenantes ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Estrategias de Afrontamiento</label><textarea name="estrategias_afrontamiento" class="input-field" rows="2" placeholder="¿Qué haces normalmente cuando te sucede?">{{ old('estrategias_afrontamiento', $detalle->estrategias_afrontamiento ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Qué sientes en tu cuerpo?</label><textarea name="sensaciones_corporales" class="input-field" rows="2" placeholder="Ej: tensión, palpitaciones, opresión en el pecho...">{{ old('sensaciones_corporales', $detalle->sensaciones_corporales ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Alguna vez has pensado en hacerte daño?</label><textarea name="autolesiones" class="input-field" rows="2">{{ old('autolesiones', $detalle->autolesiones ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Has pensado en la muerte o en quitarte la vida?</label><textarea name="ideacion_suicida" class="input-field" rows="2">{{ old('ideacion_suicida', $detalle->ideacion_suicida ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Has consumido alcohol o drogas?</label><textarea name="consumo_sustancias" class="input-field" rows="2">{{ old('consumo_sustancias', $detalle->consumo_sustancias ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Estrategias previas usadas</label><textarea name="estrategias_previas" class="input-field" rows="2" placeholder="¿Qué has intentado hacer para sentirte mejor?">{{ old('estrategias_previas', $detalle->estrategias_previas ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Qué te gustaría lograr con la terapia?</label><textarea name="objetivos_terapia" class="input-field" rows="2">{{ old('objetivos_terapia', $detalle->objetivos_terapia ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-home" style="color: var(--sipce-primary);"></i> Historia Familiar y Social</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>¿Con quién vives?</label><textarea name="convivencia" class="input-field" rows="2">{{ old('convivencia', $detalle->convivencia ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Relación Familiar</label><textarea name="relacion_familiar" class="input-field" rows="3">{{ old('relacion_familiar', $detalle->relacion_familiar ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Cómo manejas los conflictos familiares?</label><textarea name="conflictos_familiares" class="input-field" rows="2">{{ old('conflictos_familiares', $detalle->conflictos_familiares ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Vida Social</label><textarea name="vida_social" class="input-field" rows="2">{{ old('vida_social', $detalle->vida_social ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Pareja</label><textarea name="pareja" class="input-field" rows="2">{{ old('pareja', $detalle->pareja ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Redes Sociales</label><textarea name="redes_sociales" class="input-field" rows="2">{{ old('redes_sociales', $detalle->redes_sociales ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-venus-mars" style="color: var(--sipce-primary);"></i> Vida Sexual</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Estado sexual actual</label><textarea name="estado_sexual" class="input-field" rows="2">{{ old('estado_sexual', $detalle->estado_sexual ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Problemas en el área sexual</label><textarea name="problemas_sexuales" class="input-field" rows="2">{{ old('problemas_sexuales', $detalle->problemas_sexuales ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Actividad sexual reciente</label><textarea name="actividad_sexual" class="input-field" rows="2">{{ old('actividad_sexual', $detalle->actividad_sexual ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Historial de problemas sexuales</label><textarea name="historial_problemas_sexuales" class="input-field" rows="2">{{ old('historial_problemas_sexuales', $detalle->historial_problemas_sexuales ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-brain" style="color: var(--sipce-primary);"></i> Evaluación Emocional y Conductual</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Estado de ánimo</label><textarea name="estado_animo" class="input-field" rows="2">{{ old('estado_animo', $detalle->estado_animo ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Síntomas depresivos</label><textarea name="sintomas_depresivos" class="input-field" rows="3">{{ old('sintomas_depresivos', $detalle->sintomas_depresivos ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Síntomas de ansiedad</label><textarea name="sintomas_ansiedad" class="input-field" rows="3">{{ old('sintomas_ansiedad', $detalle->sintomas_ansiedad ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Irritabilidad o episodios de ira</label><textarea name="irritabilidad" class="input-field" rows="2">{{ old('irritabilidad', $detalle->irritabilidad ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Conductas de riesgo</label><textarea name="conductas_riesgo" class="input-field" rows="2">{{ old('conductas_riesgo', $detalle->conductas_riesgo ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Síntomas físicos</label><textarea name="sintomas_fisicos" class="input-field" rows="2">{{ old('sintomas_fisicos', $detalle->sintomas_fisicos ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Calidad de vida actual</label><textarea name="calidad_vida" class="input-field" rows="2">{{ old('calidad_vida', $detalle->calidad_vida ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-history" style="color: var(--sipce-primary);"></i> Antecedentes</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Antecedentes Psiquiátricos</label><textarea name="antecedentes_psiquiatricos" class="input-field" rows="3">{{ old('antecedentes_psiquiatricos', $detalle->antecedentes_psiquiatricos ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Medicamentos Actuales</label><textarea name="medicamentos_actuales" class="input-field" rows="2">{{ old('medicamentos_actuales', $detalle->medicamentos_actuales ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Antecedentes Familiares</label><textarea name="antecedentes_familiares" class="input-field" rows="2">{{ old('antecedentes_familiares', $detalle->antecedentes_familiares ?? '') }}</textarea></div>
                        <div class="field"><label>Consume Alcohol</label><select name="consume_alcohol" class="input-field"><option value="">Seleccione...</option>@foreach(['No', 'Ocasional', 'Frecuente'] as $c)<option value="{{ $c }}" {{ old('consume_alcohol', $detalle->consume_alcohol ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select></div>
                        <div class="field"><label>Consume Tabaco</label><select name="consume_tabaco" class="input-field"><option value="">Seleccione...</option>@foreach(['No', 'Ocasional', 'Frecuente'] as $c)<option value="{{ $c }}" {{ old('consume_tabaco', $detalle->consume_tabaco ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select></div>
                        <div class="field"><label>Otras Sustancias</label><input type="text" name="otras_sustancias" class="input-field" value="{{ old('otras_sustancias', $detalle->otras_sustancias ?? '') }}"></div>
                        <div class="field full-width"><label>Hábitos de Sueño</label><textarea name="habitos_sueno" class="input-field" rows="2">{{ old('habitos_sueno', $detalle->habitos_sueno ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;"><i class="fas fa-star" style="color: var(--sipce-primary);"></i> Recursos y Fortalezas</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>¿Qué haces bien?</label><textarea name="fortalezas" class="input-field" rows="2">{{ old('fortalezas', $detalle->fortalezas ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Qué te da orgullo?</label><textarea name="logros" class="input-field" rows="2">{{ old('logros', $detalle->logros ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Quién te respalda?</label><textarea name="red_apoyo" class="input-field" rows="2">{{ old('red_apoyo', $detalle->red_apoyo ?? '') }}</textarea></div>
                        <div class="field"><label>Escala 0-10: ¿Cuánto quieres que cambien las cosas?</label><input type="number" name="motivacion_cambio" class="input-field" min="0" max="10" value="{{ old('motivacion_cambio', $detalle->motivacion_cambio ?? '') }}"></div>
                        <div class="field full-width"><label>3 valores + 1 acción pequeña</label><textarea name="valores_accion" class="input-field" rows="3">{{ old('valores_accion', $detalle->valores_accion ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Observaciones</label><textarea name="observaciones" class="input-field" rows="3">{{ old('observaciones', $detalle->observaciones ?? '') }}</textarea></div>
                    </div>
                </div>
                @endif

                {{-- ADOLESCENTE --}}
                @if($paciente->tipo_paciente === 'adolescente')
                <div id="adolescenteFields">
                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-id-card" style="color: #f59e0b;"></i> Datos Personales
                    </h4>
                    <div class="grid-form">
                        <div class="field"><label>Nombre *</label><input type="text" name="nombre" class="input-field" value="{{ old('nombre', $detalle->nombre ?? '') }}" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"></div>
                        <div class="field"><label>Apellido *</label><input type="text" name="apellido" class="input-field" value="{{ old('apellido', $detalle->apellido ?? '') }}" required oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"></div>
                        <div class="field"><label>Cédula</label><input type="text" name="cedula" class="input-field" value="{{ old('cedula', $detalle->cedula ?? '') }}"></div>
                        <div class="field"><label>Fecha de Nacimiento *</label><input type="text" name="fecha_nacimiento" class="input-field" value="{{ old('fecha_nacimiento', isset($detalle->fecha_nacimiento) ? \Carbon\Carbon::parse($detalle->fecha_nacimiento)->format('d/m/Y') : '') }}" placeholder="DD/MM/AAAA" required oninput="formatearFecha(this)" maxlength="10" autocomplete="off"></div>
                        <div class="field"><label>Género</label><select name="genero" class="input-field"><option value="">Seleccione...</option>@foreach(['Masculino', 'Femenino', 'Otro'] as $g)<option value="{{ $g }}" {{ old('genero', $detalle->genero ?? '') == $g ? 'selected' : '' }}>{{ $g }}</option>@endforeach</select></div>
                        <div class="field"><label>Teléfono Personal</label><input type="text" name="telefono_personal" class="input-field" value="{{ old('telefono_personal', $detalle->telefono_personal ?? '') }}"></div>
                        <div class="field"><label>Correo personal</label><input type="email" name="email_personal" class="input-field" value="{{ old('email_personal', $detalle->email_personal ?? '') }}"></div>
                        <div class="field"><label>Institución Educativa</label><input type="text" name="institucion_educativa" class="input-field" value="{{ old('institucion_educativa', $detalle->institucion_educativa ?? '') }}"></div>
                        <div class="field"><label>Nivel Educativo</label><input type="text" name="nivel_educativo" class="input-field" value="{{ old('nivel_educativo', $detalle->nivel_educativo ?? '') }}"></div>
                        <div class="field full-width"><label>Dirección</label><input type="text" name="direccion" class="input-field" value="{{ old('direccion', $detalle->direccion ?? '') }}"></div>
                        <div class="field"><label>Grado de Instrucción</label><input type="text" name="grado_instruccion" class="input-field" value="{{ old('grado_instruccion', $detalle->grado_instruccion ?? '') }}"></div>
                        <div class="field"><label>Fecha de Entrevista</label><input type="text" name="fecha_entrevista" class="input-field" value="{{ old('fecha_entrevista', isset($detalle->fecha_entrevista) ? \Carbon\Carbon::parse($detalle->fecha_entrevista)->format('d/m/Y') : '') }}" placeholder="DD/MM/AAAA" oninput="formatearFecha(this)" maxlength="10" autocomplete="off"></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-user-tie" style="color: #f59e0b;"></i> Datos del Representante
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Nombre del Representante</label><input type="text" name="nombre_representante" class="input-field" value="{{ old('nombre_representante', $detalle->nombre_representante ?? '') }}"></div>
                        <div class="field"><label>Cédula</label><input type="text" name="cedula_representante" class="input-field" value="{{ old('cedula_representante', $detalle->cedula_representante ?? '') }}"></div>
                        <div class="field"><label>Teléfono</label><input type="text" name="telefono_representante" class="input-field" value="{{ old('telefono_representante', $detalle->telefono_representante ?? '') }}"></div>
                        <div class="field"><label>Correo electrónico</label><input type="email" name="email_representante" class="input-field" value="{{ old('email_representante', $detalle->email_representante ?? '') }}"></div>
                        <div class="field"><label>Parentesco</label><select name="parentesco" class="input-field"><option value="">Seleccione...</option>@foreach(['Padre', 'Madre', 'Abuelo/a', 'Tío/a', 'Hermano/a', 'Otro'] as $p)<option value="{{ $p }}" {{ old('parentesco', $detalle->parentesco ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>@endforeach</select></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-users" style="color: #f59e0b;"></i> Familia
                    </h4>
                    <div class="grid-form">
                        <div class="field"><label>Nombre del Padre</label><input type="text" name="padre_nombre" class="input-field" value="{{ old('padre_nombre', $detalle->padre_nombre ?? '') }}"></div>
                        <div class="field"><label>Edad del Padre</label><input type="number" name="padre_edad" class="input-field" value="{{ old('padre_edad', $detalle->padre_edad ?? '') }}" min="20" max="99"></div>
                        <div class="field"><label>Ocupación del Padre</label><input type="text" name="padre_ocupacion" class="input-field" value="{{ old('padre_ocupacion', $detalle->padre_ocupacion ?? '') }}"></div>
                        <div class="field"><label>Nombre de la Madre</label><input type="text" name="madre_nombre" class="input-field" value="{{ old('madre_nombre', $detalle->madre_nombre ?? '') }}"></div>
                        <div class="field"><label>Edad de la Madre</label><input type="number" name="madre_edad" class="input-field" value="{{ old('madre_edad', $detalle->madre_edad ?? '') }}" min="20" max="99"></div>
                        <div class="field"><label>Ocupación de la Madre</label><input type="text" name="madre_ocupacion" class="input-field" value="{{ old('madre_ocupacion', $detalle->madre_ocupacion ?? '') }}"></div>
                        <div class="field full-width"><label>Hermanos (edades)</label><textarea name="hermanos" class="input-field" rows="2">{{ old('hermanos', $detalle->hermanos ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Personas con quien vive</label><textarea name="personas_vive" class="input-field" rows="2">{{ old('personas_vive', $detalle->personas_vive ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-clipboard-list" style="color: #f59e0b;"></i> Motivo de Consulta
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Motivo de consulta</label><textarea name="motivo_consulta" class="input-field" rows="4" placeholder="¿Qué te trajo a consulta?">{{ old('motivo_consulta', $detalle->motivo_consulta ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Observaciones de los padres</label><textarea name="pads_observaciones" class="input-field" rows="4" placeholder="¿Qué observan en casa?">{{ old('pads_observaciones', $detalle->pads_observaciones ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Historia del Problema</label><textarea name="historia_problema" class="input-field" rows="3">{{ old('historia_problema', $detalle->historia_problema ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Frecuencia e intensidad</label><textarea name="frecuencia_sintomas" class="input-field" rows="2">{{ old('frecuencia_sintomas', $detalle->frecuencia_sintomas ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Factores Desencadenantes</label><textarea name="factores_desencadenantes" class="input-field" rows="2">{{ old('factores_desencadenantes', $detalle->factores_desencadenantes ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-history" style="color: #f59e0b;"></i> Historia del Problema
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>¿Cuándo empezó? ¿Fue gradual o tras evento?</label><textarea name="cuando_empezo" class="input-field" rows="3">{{ old('cuando_empezo', $detalle->cuando_empezo ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Cambios en rendimiento, faltas, repeticiones</label><textarea name="cambios_rendimiento" class="input-field" rows="2">{{ old('cambios_rendimiento', $detalle->cambios_rendimiento ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-users" style="color: #f59e0b;"></i> Relaciones, Vida Social y Académica
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Relaciones Sociales</label><textarea name="relaciones_sociales" class="input-field" rows="3">{{ old('relaciones_sociales', $detalle->relaciones_sociales ?? '') }}</textarea></div>
                        <div class="field"><label>Rendimiento Académico</label><input type="text" name="rendimiento_academico" class="input-field" value="{{ old('rendimiento_academico', $detalle->rendimiento_academico ?? '') }}"></div>
                        <div class="field"><label>¿Tiene amigos?</label><input type="text" name="tiene_amigos" class="input-field" value="{{ old('tiene_amigos', $detalle->tiene_amigos ?? '') }}"></div>
                        <div class="field"><label>Actividades Grupales</label><input type="text" name="actividades_grupales" class="input-field" value="{{ old('actividades_grupales', $detalle->actividades_grupales ?? '') }}"></div>
                        <div class="field full-width"><label>¿Rechazo, bullying o ciberacoso?</label><textarea name="rechazo_bullying" class="input-field" rows="2">{{ old('rechazo_bullying', $detalle->rechazo_bullying ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Pareja</label><textarea name="pareja" class="input-field" rows="2">{{ old('pareja', $detalle->pareja ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Redes Sociales</label><textarea name="redes_sociales" class="input-field" rows="3">{{ old('redes_sociales', $detalle->redes_sociales ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Mensajes que te hayan hecho daño?</label><textarea name="mensajes_dano" class="input-field" rows="2">{{ old('mensajes_dano', $detalle->mensajes_dano ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-heartbeat" style="color: #f59e0b;"></i> Salud, Consumo y Evaluación Emocional
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Salud física</label><textarea name="salud_fisica" class="input-field" rows="3">{{ old('salud_fisica', $detalle->salud_fisica ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Terapia anterior o medicación?</label><textarea name="terapia_anterior" class="input-field" rows="2">{{ old('terapia_anterior', $detalle->terapia_anterior ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Antecedentes Familiares</label><textarea name="antecedentes_familiares" class="input-field" rows="2">{{ old('antecedentes_familiares', $detalle->antecedentes_familiares ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Consumo de sustancias</label><textarea name="sustancias_frecuencia" class="input-field" rows="3">{{ old('sustancias_frecuencia', $detalle->sustancias_frecuencia ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Reducir consumo? ¿Molestia por críticas?</label><textarea name="reducir_consumo" class="input-field" rows="2">{{ old('reducir_consumo', $detalle->reducir_consumo ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Usar para olvidar? ¿Olvidar lo que pasó?</label><textarea name="usar_para_olvidar" class="input-field" rows="2">{{ old('usar_para_olvidar', $detalle->usar_para_olvidar ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Orientación sexual / Identidad de género</label><textarea name="orientacion_sexual" class="input-field" rows="2">{{ old('orientacion_sexual', $detalle->orientacion_sexual ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Depresión</label><textarea name="depresion" class="input-field" rows="3">{{ old('depresion', $detalle->depresion ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Ansiedad / Pánico</label><textarea name="ansiedad_panico" class="input-field" rows="3">{{ old('ansiedad_panico', $detalle->ansiedad_panico ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Irritabilidad / Impulso</label><textarea name="irritabilidad_impulso" class="input-field" rows="2">{{ old('irritabilidad_impulso', $detalle->irritabilidad_impulso ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Conductas de riesgo</label><textarea name="conductas_riesgo" class="input-field" rows="2">{{ old('conductas_riesgo', $detalle->conductas_riesgo ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Autoimagen</label><textarea name="autoimagen" class="input-field" rows="2">{{ old('autoimagen', $detalle->autoimagen ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Relación con los Padres</label><textarea name="relacion_padres" class="input-field" rows="2">{{ old('relacion_padres', $detalle->relacion_padres ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Manejo de Emociones</label><textarea name="manejo_emociones" class="input-field" rows="2">{{ old('manejo_emociones', $detalle->manejo_emociones ?? '') }}</textarea></div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-star" style="color: #f59e0b;"></i> Recursos y Fortalezas
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>¿Qué haces bien? ¿Qué te da orgullo?</label><textarea name="que_haces_bien" class="input-field" rows="2">{{ old('que_haces_bien', $detalle->que_haces_bien ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Qué te da orgullo?</label><textarea name="que_te_da_orgullo" class="input-field" rows="2">{{ old('que_te_da_orgullo', $detalle->que_te_da_orgullo ?? '') }}</textarea></div>
                        <div class="field full-width"><label>¿Quién te respalda?</label><textarea name="quien_te_respalda" class="input-field" rows="2">{{ old('quien_te_respalda', $detalle->quien_te_respalda ?? '') }}</textarea></div>
                        <div class="field"><label>Escala 0-10: ¿Cuánto quieres que cambien las cosas?</label><input type="number" name="cuanto_quieres_cambio" class="input-field" min="0" max="10" value="{{ old('cuanto_quieres_cambio', $detalle->cuanto_quieres_cambio ?? '') }}"></div>
                        <div class="field full-width"><label>3 valores + 1 acción pequeña</label><textarea name="mini_ejercicio" class="input-field" rows="3">{{ old('mini_ejercicio', $detalle->mini_ejercicio ?? '') }}</textarea></div>
                        <div class="field full-width"><label>Observaciones</label><textarea name="observaciones" class="input-field" rows="3">{{ old('observaciones', $detalle->observaciones ?? '') }}</textarea></div>
                    </div>
                </div>
                @endif

                {{-- NIÑO --}}
                @if($paciente->tipo_paciente === 'niño')
                <div id="ninoFields">
                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-info-circle" style="color: #10b981;"></i> Datos Básicos
                    </h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Nombre </label>
                            <input type="text" name="nombre" class="input-field" 
                                   value="{{ old('nombre', $detalle->nombre ?? '') }}" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                        </div>
                        <div class="field">
                            <label>Apellido</label>
                            <input type="text" name="apellido" class="input-field" 
                                   value="{{ old('apellido', $detalle->apellido ?? '') }}" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')">
                        </div>
                        <div class="field">
                            <label>Fecha de Nacimiento</label>
                            <input type="text" name="fecha_nacimiento" class="input-field" 
                                   value="{{ old('fecha_nacimiento', isset($detalle->fecha_nacimiento) ? \Carbon\Carbon::parse($detalle->fecha_nacimiento)->format('d/m/Y') : '') }}" 
                                   placeholder="DD/MM/AAAA" required
                                   oninput="formatearFecha(this)" maxlength="10" autocomplete="off">
                        </div>
                        <div class="field">
                            <label>Teléfono de Contacto</label>
                            <input type="text" name="telefono_contacto" class="input-field" value="{{ old('telefono_contacto', $detalle->telefono_contacto ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Grado de Instrucción</label>
                            <input type="text" name="grado_instruccion" class="input-field" value="{{ old('grado_instruccion', $detalle->grado_instruccion ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Maestro</label>
                            <input type="text" name="maestro" class="input-field" value="{{ old('maestro', $detalle->maestro ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="input-field" value="{{ old('direccion', $detalle->direccion ?? '') }}">
                        </div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-users" style="color: #10b981;"></i> Datos de los Padres
                    </h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Nombre del Padre</label>
                            <input type="text" name="nombre_padre" class="input-field" value="{{ old('nombre_padre', $detalle->nombre_padre ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Edad del Padre</label>
                            <input type="number" name="edad_padre" class="input-field" value="{{ old('edad_padre', $detalle->edad_padre ?? '') }}" min="18" max="99">
                        </div>
                        <div class="field full-width">
                            <label>Ocupación del Padre</label>
                            <input type="text" name="ocupacion_padre" class="input-field" value="{{ old('ocupacion_padre', $detalle->ocupacion_padre ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Nombre de la Madre</label>
                            <input type="text" name="nombre_madre" class="input-field" value="{{ old('nombre_madre', $detalle->nombre_madre ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Edad de la Madre</label>
                            <input type="number" name="edad_madre" class="input-field" value="{{ old('edad_madre', $detalle->edad_madre ?? '') }}" min="18" max="99">
                        </div>
                        <div class="field full-width">
                            <label>Ocupación de la Madre</label>
                            <input type="text" name="ocupacion_madre" class="input-field" value="{{ old('ocupacion_madre', $detalle->ocupacion_madre ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Hermanos (edades)</label>
                            <input type="text" name="hermanos" class="input-field" value="{{ old('hermanos', $detalle->hermanos ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Personas con quien vive</label>
                            <input type="text" name="personas_vive" class="input-field" value="{{ old('personas_vive', $detalle->personas_vive ?? '') }}">
                        </div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-clipboard-list" style="color: #10b981;"></i> Motivo de Consulta
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Desarrollo del Problema</label>
                            <textarea name="desarrollo_problema" class="input-field" rows="2">{{ old('desarrollo_problema', $detalle->desarrollo_problema ?? '') }}</textarea>
                        </div>
                        <div class="field">
                            <label>Frecuencia</label>
                            <select name="frecuencia_sintomas" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Diario', 'Semanal', 'Mensual', 'Ocasional'] as $f)
                                <option value="{{ $f }}" {{ old('frecuencia_sintomas', $detalle->frecuencia_sintomas ?? '') == $f ? 'selected' : '' }}>{{ $f }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Intensidad</label>
                            <select name="intensidad_sintomas" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Leve', 'Moderado', 'Severo'] as $i)
                                <option value="{{ $i }}" {{ old('intensidad_sintomas', $detalle->intensidad_sintomas ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Duración</label>
                            <input type="text" name="duracion_sintomas" class="input-field" value="{{ old('duracion_sintomas', $detalle->duracion_sintomas ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Contexto</label>
                            <input type="text" name="contexto_problema" class="input-field" value="{{ old('contexto_problema', $detalle->contexto_problema ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Estrategias de Afrontamiento</label>
                            <textarea name="estrategias_afrontamiento" class="input-field" rows="2">{{ old('estrategias_afrontamiento', $detalle->estrategias_afrontamiento ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Consecuencias</label>
                            <input type="text" name="consecuencias" class="input-field" value="{{ old('consecuencias', $detalle->consecuencias ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Acontecimientos Estresantes</label>
                            <textarea name="acontecimientos_estresantes" class="input-field" rows="2">{{ old('acontecimientos_estresantes', $detalle->acontecimientos_estresantes ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Expectativas</label>
                            <textarea name="expectativas" class="input-field" rows="2">{{ old('expectativas', $detalle->expectativas ?? '') }}</textarea>
                        </div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-baby" style="color: #10b981;"></i> Historia Perinatal
                    </h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Tipo de Parto</label>
                            <select name="tipo_parto" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Natural', 'Cesárea', 'Inducido'] as $tp)
                                <option value="{{ $tp }}" {{ old('tipo_parto', $detalle->tipo_parto ?? '') == $tp ? 'selected' : '' }}>{{ $tp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Causa Parto Inducido</label>
                            <input type="text" name="causa_parto_inducido" class="input-field" value="{{ old('causa_parto_inducido', $detalle->causa_parto_inducido ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Dificultades en el Parto</label>
                            <textarea name="dificultades_parto" class="input-field" rows="2">{{ old('dificultades_parto', $detalle->dificultades_parto ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Comportamiento del Bebé</label>
                            <input type="text" name="comportamiento_bebe" class="input-field" value="{{ old('comportamiento_bebe', $detalle->comportamiento_bebe ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Reacciones ante Estímulos</label>
                            <input type="text" name="reacciones_estimulos" class="input-field" value="{{ old('reacciones_estimulos', $detalle->reacciones_estimulos ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Situación Familiar Postnatal</label>
                            <input type="text" name="situacion_familiar_postnatal" class="input-field" value="{{ old('situacion_familiar_postnatal', $detalle->situacion_familiar_postnatal ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Aceptación Maternidad</label>
                            <textarea name="aceptacion_maternidad" class="input-field" rows="2">{{ old('aceptacion_maternidad', $detalle->aceptacion_maternidad ?? '') }}</textarea>
                        </div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-child-reaching" style="color: #10b981;"></i> Desarrollo Evolutivo
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Desarrollo en Diferentes Áreas</label>
                            <textarea name="desarrollo_areas" class="input-field" rows="2">{{ old('desarrollo_areas', $detalle->desarrollo_areas ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Habla y Lenguaje</label>
                            <textarea name="habla_lenguaje" class="input-field" rows="2">{{ old('habla_lenguaje', $detalle->habla_lenguaje ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Alimentación del Bebé</label>
                            <input type="text" name="alimentacion_bebe" class="input-field" value="{{ old('alimentacion_bebe', $detalle->alimentacion_bebe ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Dificultades en el Destete</label>
                            <input type="text" name="dificultades_destete" class="input-field" value="{{ old('dificultades_destete', $detalle->dificultades_destete ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Trastornos Alimentación</label>
                            <input type="text" name="trastornos_alimentacion" class="input-field" value="{{ old('trastornos_alimentacion', $detalle->trastornos_alimentacion ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Alimentación Actual</label>
                            <textarea name="alimentacion_actual" class="input-field" rows="2">{{ old('alimentacion_actual', $detalle->alimentacion_actual ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Descripción Día Anterior</label>
                            <textarea name="descripcion_dia_anterior" class="input-field" rows="2">{{ old('descripcion_dia_anterior', $detalle->descripcion_dia_anterior ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Info. Sexualidad</label>
                            <textarea name="info_sexualidad" class="input-field" rows="2">{{ old('info_sexualidad', $detalle->info_sexualidad ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Desarrollo Sexual Actual</label>
                            <input type="text" name="desarrollo_sexual" class="input-field" value="{{ old('desarrollo_sexual', $detalle->desarrollo_sexual ?? '') }}">
                        </div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-school" style="color: #10b981;"></i> Área Escolar y Conductual
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Tareas Escolares</label>
                            <textarea name="tareas_escolares" class="input-field" rows="2">{{ old('tareas_escolares', $detalle->tareas_escolares ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Rendimiento Escolar</label>
                            <input type="text" name="rendimiento_escolar" class="input-field" value="{{ old('rendimiento_escolar', $detalle->rendimiento_escolar ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Responsabilidades Domésticas</label>
                            <input type="text" name="responsabilidades_domesticas" class="input-field" value="{{ old('responsabilidades_domesticas', $detalle->responsabilidades_domesticas ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Recompensas</label>
                            <input type="text" name="recompensas" class="input-field" value="{{ old('recompensas', $detalle->recompensas ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Nivel de Actividad</label>
                            <select name="nivel_actividad" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Alto', 'Medio', 'Bajo'] as $na)
                                <option value="{{ $na }}" {{ old('nivel_actividad', $detalle->nivel_actividad ?? '') == $na ? 'selected' : '' }}>{{ $na }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Entretenimiento</label>
                            <input type="text" name="entretenimiento" class="input-field" value="{{ old('entretenimiento', $detalle->entretenimiento ?? '') }}">
                        </div>
                        <div class="field">
                            <label>Tipo de Actividades</label>
                            <select name="tipo_actividades" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Sedentarias', 'Inquietas', 'Mixtas'] as $ta)
                                <option value="{{ $ta }}" {{ old('tipo_actividades', $detalle->tipo_actividades ?? '') == $ta ? 'selected' : '' }}>{{ $ta }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Finalización de Tareas</label>
                            <select name="finalizacion_tareas" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Sí, siempre', 'A veces', 'No'] as $ft)
                                <option value="{{ $ft }}" {{ old('finalizacion_tareas', $detalle->finalizacion_tareas ?? '') == $ft ? 'selected' : '' }}>{{ $ft }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full-width">
                            <label>Arrebatos</label>
                            <textarea name="arrebatos" class="input-field" rows="2">{{ old('arrebatos', $detalle->arrebatos ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Oposicionismo</label>
                            <textarea name="oposicionismo" class="input-field" rows="2">{{ old('oposicionismo', $detalle->oposicionismo ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Agresiones</label>
                            <textarea name="agresiones" class="input-field" rows="2">{{ old('agresiones', $detalle->agresiones ?? '') }}</textarea>
                        </div>
                    </div>

                    <h4 style="color: #2d3748; margin: 25px 0 15px; font-size: 16px;">
                        <i class="fas fa-heart" style="color: #10b981;"></i> Relaciones Sociales
                    </h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Autoimagen</label>
                            <textarea name="autoimagen" class="input-field" rows="2">{{ old('autoimagen', $detalle->autoimagen ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Percepción de los Demás</label>
                            <textarea name="percepcion_demas" class="input-field" rows="2">{{ old('percepcion_demas', $detalle->percepcion_demas ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Relaciones con Niños</label>
                            <textarea name="relaciones_ninos" class="input-field" rows="2">{{ old('relaciones_ninos', $detalle->relaciones_ninos ?? '') }}</textarea>
                        </div>
                        <div class="field">
                            <label>¿Tiene amigos?</label>
                            <select name="tiene_amigos" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['Sí', 'No', 'Pocos'] as $am)
                                <option value="{{ $am }}" {{ old('tiene_amigos', $detalle->tiene_amigos ?? '') == $am ? 'selected' : '' }}>{{ $am }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Actividades Sociales</label>
                            <input type="text" name="actividades_sociales" class="input-field" value="{{ old('actividades_sociales', $detalle->actividades_sociales ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Tipo de Niños que le Atraen</label>
                            <input type="text" name="tipo_ninos_atraen" class="input-field" value="{{ old('tipo_ninos_atraen', $detalle->tipo_ninos_atraen ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Comportamiento en Grupo</label>
                            <textarea name="comportamiento_grupo" class="input-field" rows="2">{{ old('comportamiento_grupo', $detalle->comportamiento_grupo ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Juegos Preferidos</label>
                            <input type="text" name="juegos_preferidos" class="input-field" value="{{ old('juegos_preferidos', $detalle->juegos_preferidos ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Relación con Adultos</label>
                            <textarea name="relacion_adultos" class="input-field" rows="2">{{ old('relacion_adultos', $detalle->relacion_adultos ?? '') }}</textarea>
                        </div>
                        <div class="field full-width">
                            <label>Entretenimiento en Casa</label>
                            <input type="text" name="entretenimiento_casa" class="input-field" value="{{ old('entretenimiento_casa', $detalle->entretenimiento_casa ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Actividades Exteriores</label>
                            <input type="text" name="actividades_exteriores" class="input-field" value="{{ old('actividades_exteriores', $detalle->actividades_exteriores ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Actividades Deportivas</label>
                            <input type="text" name="actividades_deportivas" class="input-field" value="{{ old('actividades_deportivas', $detalle->actividades_deportivas ?? '') }}">
                        </div>
                        <div class="field full-width">
                            <label>Intereses</label>
                            <textarea name="intereses" class="input-field" rows="2">{{ old('intereses', $detalle->intereses ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Botones de Acción --}}
                <div class="form-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                    <a href="{{ route('pacientes.index') }}" class="btn-paciente btn-paciente-cancel">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn-paciente btn-paciente-save">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function formatearFecha(input) {
    var valor = input.value.replace(/\D/g, '');
    if (valor.length > 8) valor = valor.slice(0, 8);
    if (valor.length >= 5) {
        valor = valor.slice(0, 2) + '/' + valor.slice(2, 4) + '/' + valor.slice(4);
    } else if (valor.length >= 3) {
        valor = valor.slice(0, 2) + '/' + valor.slice(2);
    }
    input.value = valor;
}

function validarFormEdit() {
    var prioridad = document.querySelector('[name="prioridad"]');
    var tipoAtencion = document.querySelector('[name="tipo_atencion"]');
    var estadoId = document.querySelector('[name="estado_id"]');
    var nombre = document.querySelector('[name="nombre"]');
    var apellido = document.querySelector('[name="apellido"]');
    var fechaInput = document.querySelector('[name="fecha_nacimiento"]');
    var tipoPaciente = document.querySelector('[name="tipo_paciente"]').value;

    if (!prioridad || !prioridad.value) {
        SIPCE_ALERT.warning('Seleccione la prioridad.');
        if (prioridad) prioridad.focus();
        return false;
    }

    if (!tipoAtencion || !tipoAtencion.value) {
        SIPCE_ALERT.warning('Seleccione el tipo de atención.');
        if (tipoAtencion) tipoAtencion.focus();
        return false;
    }

    if (!estadoId || !estadoId.value) {
        SIPCE_ALERT.warning('Seleccione el estado.');
        if (estadoId) estadoId.focus();
        return false;
    }

    if (nombre && !nombre.value.trim()) {
        SIPCE_ALERT.warning('Ingrese el nombre.');
        nombre.focus();
        return false;
    }

    if (apellido && !apellido.value.trim()) {
        SIPCE_ALERT.warning('Ingrese el apellido.');
        apellido.focus();
        return false;
    }

    if (fechaInput && fechaInput.value.trim()) {
        if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fechaInput.value)) {
            SIPCE_ALERT.warning('Ingrese una fecha válida en formato DD/MM/AAAA.');
            fechaInput.focus();
            return false;
        }

        var partes = fechaInput.value.split('/');
        var dia = parseInt(partes[0], 10);
        var mes = parseInt(partes[1], 10);
        var anio = parseInt(partes[2], 10);

        if (mes < 1 || mes > 12) {
            SIPCE_ALERT.warning('El mes debe estar entre 01 y 12.');
            fechaInput.focus();
            return false;
        }

        var diasPorMes = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if (mes === 2 && ((anio % 4 === 0 && anio % 100 !== 0) || anio % 400 === 0)) {
            diasPorMes[1] = 29;
        }

        if (dia < 1 || dia > diasPorMes[mes - 1]) {
            SIPCE_ALERT.warning('El día ingresado no es válido.');
            fechaInput.focus();
            return false;
        }

        var anioActual = new Date().getFullYear();
        if (anio < 1900 || anio > anioActual) {
            SIPCE_ALERT.warning('El año debe estar entre 1900 y ' + anioActual + '.');
            fechaInput.focus();
            return false;
        }

        var fechaNac = new Date(anio, mes - 1, dia);
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaNac.getFullYear();
        var diferenciaMeses = hoy.getMonth() - fechaNac.getMonth();

        if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }

        if (fechaNac > hoy) {
            SIPCE_ALERT.warning('La fecha de nacimiento no puede ser futura.');
            fechaInput.focus();
            return false;
        }

        if (tipoPaciente === 'niño' && edad > 11) {
            SIPCE_ALERT.warning('El paciente niño debe tener máximo 11 años.');
            fechaInput.focus();
            return false;
        }

        if (tipoPaciente === 'adolescente' && (edad < 12 || edad > 17)) {
            SIPCE_ALERT.warning('El paciente adolescente debe tener entre 12 y 17 años.');
            fechaInput.focus();
            return false;
        }
    }

    return true;
}
</script>

<style>
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .card-header-custom {
        padding: 20px 25px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-custom h2 {
        margin: 0;
        color: #1e293b;
        font-size: 20px;
    }

    .card-header-custom p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .card-body-custom {
        padding: 25px;
    }

    .grid-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .field {
        display: flex;
        flex-direction: column;
    }

    .field.full-width {
        grid-column: 1 / -1;
    }

    .field label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }

    .field label i {
        color: var(--sipce-primary);
        margin-right: 5px;
    }

    .input-field {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.2s;
        background: #fff;
    }

    .input-field:focus {
        outline: none;
        border-color: var(--sipce-primary);
        box-shadow: 0 0 0 3px rgba(var(--sipce-primary-rgb), 0.1);
    }

    select.input-field {
        cursor: pointer;
    }

    textarea.input-field {
        resize: vertical;
        min-height: 80px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-paciente {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-paciente-save {
        background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%);
        color: white;
    }

    .btn-paciente-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(var(--sipce-primary-rgb), 0.4);
    }

    .btn-paciente-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 2px solid #e2e8f0;
    }

    .btn-paciente-cancel:hover {
        background: #e2e8f0;
    }

    h4 {
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
    }

    .alert {
        padding: 14px 20px;
        margin: 15px 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        font-size: 14px;
    }

    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
    }

    .alert-close {
        margin-left: auto;
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: 0.6;
        font-size: 18px;
    }

    .alert-close:hover {
        opacity: 1;
    }
</style>