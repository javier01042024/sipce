<div id="modalAdulto" class="modal-paciente" style="display: none;">
    <div class="modal-paciente-box" style="max-width: 800px;">
        <div class="modal-paciente-header">
            <h2><i class="fas fa-user-tie"></i> Registrar Paciente Adulto</h2>
            <button class="modal-paciente-close" onclick="cerrarModal('adulto')">Ã—</button>
        </div>

        <div style="padding: 15px 25px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: var(--sipce-primary);" id="stepLabelAdulto">Paso 1 de 7</span>
                <span style="font-size: 12px; color: #64748b;" id="stepTitleAdulto">Datos Personales</span>
            </div>
            <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px;">
                <div id="progressBarAdulto" style="width: 14%; height: 100%; background: linear-gradient(135deg, var(--sipce-primary) 0%, var(--sipce-primary-dark) 100%); border-radius: 3px; transition: width 0.3s ease;"></div>
            </div>
        </div>

        <form action="{{ route('pacientes.store') }}" method="POST" id="formAdulto" onsubmit="return validarFormAdulto()">
            @csrf
            <input type="hidden" name="tipo_paciente" value="adulto">

            <div class="modal-paciente-body">

                {{-- PASO 1: DATOS PERSONALES --}}
                <div class="step-adulto" id="stepAdulto1">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-id-card" style="color: var(--sipce-primary);"></i> Datos Personales</h4>
                    <div class="grid-form">
                        <div class="field"><label>NÂ° Expediente</label><input type="text" class="input-field" placeholder="AutomÃ¡tico" disabled></div>
                        <div class="field">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" class="input-field" placeholder="Nombre" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]/g, '')">
                        </div>
                        <div class="field">
                            <label>Apellido *</label>
                            <input type="text" name="apellido" class="input-field" placeholder="Apellido" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]/g, '')">
                        </div>
                        <div class="field">
                            <label>CÃ©dula *</label>
                            <div style="display: flex; gap: 5px;">
                                <select name="nacionalidad" class="input-field" style="width: 70px; flex-shrink: 0; padding: 10px;" required>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                </select>
                                <input type="text" name="cedula" class="input-field" style="flex: 1;" placeholder="12345678" required
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8)" maxlength="8">
                            </div>
                        </div>
                        <div class="field">
                            <label>Fecha de Nacimiento *</label>
                            <input type="text" name="fecha_nacimiento" class="input-field" placeholder="DD/MM/AAAA"
                                   oninput="formatearFecha(this)" maxlength="10" autocomplete="off">
                            <span class="text-muted">Edad mÃ­nima: 18 aÃ±os</span>
                        </div>
                        <div class="field">
                            <label>GÃ©nero</label>
                            <select name="genero" class="input-field"><option value="">Seleccione...</option><option>Masculino</option><option>Femenino</option><option>Otro</option></select>
                        </div>
                        <div class="field">
                            <label>Estado Civil</label>
                            <select name="estado_civil" class="input-field"><option value="">Seleccione...</option><option>Soltero/a</option><option>Casado/a</option><option>Divorciado/a</option><option>Viudo/a</option><option>UniÃ³n Libre</option></select>
                        </div>
                        <div class="field">
                            <label>TelÃ©fono *</label>
                            <input type="text" name="telefono" class="input-field" placeholder="+584241234567" required
                                   oninput="this.value = this.value.replace(/[^0-9+]/g, '').slice(0, 15)" maxlength="15">
                        </div>
                        <div class="field">
                            <label>Correo electrÃ³nico</label>
                            <input type="email" name="email" class="input-field" placeholder="correo@ejemplo.com">
                        </div>
                        <div class="field full-width">
                            <label>DirecciÃ³n</label>
                            <input type="text" name="direccion" class="input-field" placeholder="DirecciÃ³n completa">
                        </div>
                        <div class="field">
                            <label>OcupaciÃ³n</label>
                            <input type="text" name="ocupacion" class="input-field" placeholder="ProfesiÃ³n u oficio">
                        </div>
                        <div class="field">
                            <label>Lugar de Trabajo</label>
                            <input type="text" name="lugar_trabajo" class="input-field" placeholder="Nombre de la empresa/instituciÃ³n">
                        </div>
                        <div class="field">
                            <label>Nivel de InstrucciÃ³n</label>
                            <select name="nivel_instruccion" class="input-field">
                                <option value="">Seleccione...</option>
                                <option>Primaria</option>
                                <option>Secundaria</option>
                                <option>Universitario</option>
                                <option>Postgrado</option>
                                <option>Otro</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Fecha de Entrevista</label>
                            <input type="date" name="fecha_entrevista" class="input-field">
                                   oninput="formatearFecha(this)" maxlength="10" autocomplete="off">
                        </div>
                    </div>
                </div>

                {{-- PASO 2: MOTIVO DE CONSULTA --}}
                <div class="step-adulto" id="stepAdulto2" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-clipboard-list" style="color: var(--sipce-primary);"></i> Motivo de Consulta</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Motivo de consulta *</label>
                            <textarea name="motivo_consulta" class="input-field" rows="3" required placeholder="Â¿QuÃ© te trajo a consulta? Â¿QuÃ© te gustarÃ­a que cambiara?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿Hace cuÃ¡nto tiempo te sucede?</label>
                            <textarea name="frecuencia_sintomas" class="input-field" rows="2" placeholder="Describe la frecuencia e intensidad del problema"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© pensamientos te generan mÃ¡s preocupaciÃ³n o intranquilidad?</label>
                            <textarea name="historia_problema" class="input-field" rows="2" placeholder="Describe los pensamientos que te causan malestar"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© sientes en tu cuerpo cuando esto te sucede?</label>
                            <textarea name="sensaciones_corporales" class="input-field" rows="2" placeholder="Ej: tensiÃ³n, palpitaciones, opresiÃ³n en el pecho..."></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© haces normalmente cuando te sucede?</label>
                            <textarea name="estrategias_afrontamiento" class="input-field" rows="2" placeholder="Describe tus respuestas y conductas habituales ante la situaciÃ³n"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿Alguna vez has pensado en hacerte daÃ±o a ti mismo/a?</label>
                            <textarea name="autolesiones" class="input-field" rows="2" placeholder="Si es asÃ­, Â¿con quÃ© frecuencia?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿Has pensado en la muerte o en quitarte la vida?</label>
                            <textarea name="ideacion_suicida" class="input-field" rows="2" placeholder="Si es asÃ­, Â¿con quÃ© frecuencia?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿Has consumido alcohol o drogas?</label>
                            <textarea name="consumo_sustancias" class="input-field" rows="2" placeholder="Â¿Con quÃ© frecuencia? Â¿QuÃ© sustancias?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© estrategias de afrontamiento has usado?</label>
                            <textarea name="estrategias_previas" class="input-field" rows="2" placeholder="Â¿QuÃ© has intentado hacer para sentirte mejor? Â¿QuÃ© ha funcionado y quÃ© no?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© te gustarÃ­a lograr con la terapia?</label>
                            <textarea name="objetivos_terapia" class="input-field" rows="2" placeholder="Describe tus expectativas y metas para el tratamiento"></textarea>
                        </div>
                    </div>
                </div>

                {{-- PASO 3: HISTORIA FAMILIAR Y SOCIAL --}}
                <div class="step-adulto" id="stepAdulto3" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-home" style="color: var(--sipce-primary);"></i> Historia Familiar y Social</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Â¿Con quiÃ©n vives actualmente?</label>
                            <textarea name="convivencia" class="input-field" rows="2" placeholder="Describe tu situaciÃ³n de convivencia familiar"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿CÃ³mo es tu relaciÃ³n familiar?</label>
                            <textarea name="relacion_familiar" class="input-field" rows="3" placeholder="Describe tu relaciÃ³n con tu familia de origen y/o familia actual"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿CÃ³mo manejas los conflictos familiares?</label>
                            <textarea name="conflictos_familiares" class="input-field" rows="2" placeholder="Describe cÃ³mo se resuelven (o no) las diferencias en tu familia"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿CÃ³mo es tu vida social?</label>
                            <textarea name="vida_social" class="input-field" rows="2" placeholder="Describe tus relaciones sociales, amigos, actividades en grupo"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿Tienes pareja actualmente?</label>
                            <textarea name="pareja" class="input-field" rows="2" placeholder="Describe tu relaciÃ³n de pareja (si aplica)"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Redes sociales</label>
                            <textarea name="redes_sociales" class="input-field" rows="2" placeholder="Â¿CuÃ¡nto tiempo pasas en redes sociales? Â¿QuÃ© problemas te generan?"></textarea>
                        </div>
                    </div>
                </div>

                {{-- PASO 4: VIDA SEXUAL --}}
                <div class="step-adulto" id="stepAdulto4" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-venus-mars" style="color: var(--sipce-primary);"></i> Vida Sexual</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Estado sexual actual</label>
                            <textarea name="estado_sexual" class="input-field" rows="2" placeholder="Describe tu estado sexual actual"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Problemas en el Ã¡rea sexual</label>
                            <textarea name="problemas_sexuales" class="input-field" rows="2" placeholder="Describe algÃºn problema que tengas en esta Ã¡rea (si aplica)"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© tipo de actividad sexual has tenido en el Ãºltimo aÃ±o?</label>
                            <textarea name="actividad_sexual" class="input-field" rows="2" placeholder="Describe tu actividad sexual reciente"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿Ha tenido alguna vez algÃºn problema sexual?</label>
                            <textarea name="historial_problemas_sexuales" class="input-field" rows="2" placeholder="Describe problemas sexuales anteriores (si aplica)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- PASO 5: EVALUACIÃ“N EMOCIONAL Y CONDUCTUAL --}}
                <div class="step-adulto" id="stepAdulto5" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-brain" style="color: var(--sipce-primary);"></i> EvaluaciÃ³n Emocional y Conductual</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Estado de Ã¡nimo</label>
                            <textarea name="estado_animo" class="input-field" rows="2" placeholder="Describe tu estado de Ã¡nimo habitual"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>SÃ­ntomas depresivos</label>
                            <textarea name="sintomas_depresivos" class="input-field" rows="3" placeholder="Tristeza, pÃ©rdida de interÃ©s, cambios en sueÃ±o/apetito, fatiga, dificultad para concentrarse..."></textarea>
                        </div>
                        <div class="field full-width">
                            <label>SÃ­ntomas de ansiedad</label>
                            <textarea name="sintomas_ansiedad" class="input-field" rows="3" placeholder="PreocupaciÃ³n excesiva, nerviosismo, tensiÃ³n muscular, dificultad para relajarse..."></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Irritabilidad o episodios de ira</label>
                            <textarea name="irritabilidad" class="input-field" rows="2" placeholder="Describe episodios de irritabilidad o ira (si aplica)"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Conductas de riesgo</label>
                            <textarea name="conductas_riesgo" class="input-field" rows="2" placeholder="Describe conductas que pongan en riesgo tu integridad fÃ­sica o la de otros (si aplica)"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>SÃ­ntomas fÃ­sicos</label>
                            <textarea name="sintomas_fisicos" class="input-field" rows="2" placeholder="Describe sÃ­ntomas fÃ­sicos que puedan estar relacionados con tu estado emocional"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Calidad de vida actual</label>
                            <textarea name="calidad_vida" class="input-field" rows="2" placeholder="Â¿CÃ³mo calificarÃ­as tu calidad de vida actual? Â¿QuÃ© aspectos te gustarÃ­a mejorar?"></textarea>
                        </div>
                    </div>
                </div>

                {{-- PASO 6: RECURSOS Y FORTALEZAS --}}
                <div class="step-adulto" id="stepAdulto6" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-star" style="color: var(--sipce-primary);"></i> Recursos y Fortalezas</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Â¿QuÃ© es lo que mejor haces?</label>
                            <textarea name="fortalezas" class="input-field" rows="2" placeholder="Describe tus habilidades y fortalezas personales"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuÃ© te da orgullo?</label>
                            <textarea name="logros" class="input-field" rows="2" placeholder="Describe logros personales que te hagan sentir bien contigo mismo/a"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Â¿QuiÃ©n te respalda?</label>
                            <textarea name="red_apoyo" class="input-field" rows="2" placeholder="Describe tu red de apoyo (familia, amigos, comunidad)"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Escala 0-10: Â¿CuÃ¡nto quieres que cambien las cosas?</label>
                            <input type="number" name="motivacion_cambio" class="input-field" min="0" max="10" placeholder="0 = nada, 10 = mucho">
                        </div>
                        <div class="field full-width">
                            <label>Mini-ejercicio: 3 valores + 1 acciÃ³n pequeÃ±a</label>
                            <textarea name="valores_accion" class="input-field" rows="3" placeholder="Describe 3 valores importantes para ti y 1 acciÃ³n pequeÃ±a que puedas hacer esta semana para acercarte a ellos"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Observaciones adicionales</label>
                            <textarea name="observaciones" class="input-field" rows="3" placeholder="Cualquier otra informaciÃ³n relevante que quieras compartir"></textarea>
                        </div>
                    </div>
                </div>

                {{-- PASO 7: DATOS DEL SISTEMA --}}
                <div class="step-adulto" id="stepAdulto7" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-cog" style="color: var(--sipce-primary);"></i> Datos del Sistema</h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Prioridad *</label>
                            <select name="prioridad" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgencia">Urgencia</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Estado *</label>
                            <div style="display:flex;gap:6px;align-items:center;">
                                <select name="estado_id" class="input-field" required style="flex:1;">
                                    <option value="">Seleccione...</option>
                                    @foreach(\App\Models\Estado::orderBy('tipo')->get() as $estado)
                                    <option value="{{ $estado->id }}">{{ $estado->tipo }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="abrirModalEstado()" title="Nuevo estado" style="width:38px;height:38px;border-radius:10px;border:2px solid #e2e8f0;background:#f8fafc;color:var(--sipce-primary);cursor:pointer;font-size:16px;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="field full-width">
                            <label>Municipio</label>
                            <select name="municipio" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach(['ArÃ­stides Bastidas','BolÃ­var','Bruzual','Cocorote','Independencia','JosÃ© Antonio PÃ¡ez','La Trinidad','Manuel Monge','Nirgua','PeÃ±a','San Felipe','Sucre','Urachiche','Veroes'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full-width">
                            <label>Tipo de AtenciÃ³n *</label>
                            <select name="tipo_atencion" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="privado">Privado</option>
                                <option value="publico">PÃºblico</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 25px; padding: 20px; background: #f0f9ff; border-radius: 12px; border: 1px solid #bae6fd;">
                        <h5 style="margin: 0 0 10px; color: #2d3748; font-size: 14px;"><i class="fas fa-check-circle" style="color: #11998e;"></i> Resumen del Registro</h5>
                        <p style="margin: 0; color: #64748b; font-size: 13px;">Al guardar, se crearÃ¡ el expediente del paciente adulto. PodrÃ¡ editar la informaciÃ³n posteriormente.</p>
                    </div>
                </div>

            </div>

            <div class="modal-paciente-footer" style="justify-content: space-between;">
                <button type="button" class="btn-paciente btn-paciente-cancel" id="btnPrevAdulto" onclick="prevStepAdulto()" style="display: none;"><i class="fas fa-arrow-left"></i> Anterior</button>
                <div>
                    <button type="button" class="btn-paciente btn-paciente-cancel" onclick="cerrarModal('adulto')" style="margin-right: 8px;">Cancelar</button>
                    <button type="button" class="btn-paciente btn-paciente-save" id="btnNextAdulto" onclick="nextStepAdulto()">Siguiente <i class="fas fa-arrow-right"></i></button>
                    <button type="submit" class="btn-paciente btn-paciente-save" id="btnSubmitAdulto" style="display: none;"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('pacientes.partials.modals.estado-inline-modal')
