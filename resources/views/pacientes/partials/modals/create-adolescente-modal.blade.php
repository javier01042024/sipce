<div id="modalAdolescente" class="modal-paciente" style="display: none;">
    <div class="modal-paciente-box" style="max-width: 800px;">
        <div class="modal-paciente-header" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <h2><i class="fas fa-user"></i> Registrar Paciente Adolescente</h2>
            <button class="modal-paciente-close" onclick="cerrarModal('adolescente')">×</button>
        </div>
        
        <div style="padding: 15px 25px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #f59e0b;" id="stepLabelAdol">Paso 1 de 7</span>
                <span style="font-size: 12px; color: #64748b;" id="stepTitleAdol">Datos Personales</span>
            </div>
            <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px;">
                <div id="progressBarAdol" style="width: 14%; height: 100%; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 3px; transition: width 0.3s ease;"></div>
            </div>
        </div>
        
        <form action="{{ route('pacientes.store') }}" method="POST" id="formAdolescente" onsubmit="return validarFormAdolescente()">
            @csrf
            <input type="hidden" name="tipo_paciente" value="adolescente">
            
            <div class="modal-paciente-body">
                
                {{-- PASO 1: DATOS PERSONALES --}}
                <div class="stepAdol" id="stepAdol1">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-id-card" style="color: #f59e0b;"></i> Datos Personales</h4>
                    <div class="grid-form">
                        <div class="field"><label>N° Expediente</label><input type="text" class="input-field" placeholder="Automático" disabled></div>
                        <div class="field">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" class="input-field" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" placeholder="Solo letras">
                        </div>
                        <div class="field">
                            <label>Apellido *</label>
                            <input type="text" name="apellido" class="input-field" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')" placeholder="Solo letras">
                        </div>
                        <div class="field">
                            <label>Fecha de Nacimiento *</label>
                            <input type="text" name="fecha_nacimiento" class="input-field" placeholder="DD/MM/AAAA" required
                                   oninput="formatearFechaAdol(this)" maxlength="10" autocomplete="off">
                            <span class="text-muted" style="font-size: 11px; color: #64748b;">Edad: 13-17 años</span>
                        </div>
                        <div class="field">
                            <label>Edad</label>
                            <input type="number" name="edad" class="input-field" min="13" max="17" placeholder="Se calcula según la fecha de nacimiento">
                        </div>
                        <div class="field">
                            <label>Grado de Instrucción</label>
                            <select name="grado_instruccion" class="input-field">
                                <option value="">Seleccione...</option>
                                <option>1er año</option>
                                <option>2do año</option>
                                <option>3er año</option>
                                <option>4to año</option>
                                <option>5to año</option>
                                <option>6to año</option>
                            </select>
                        </div>
                        <div class="field full-width">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="input-field" placeholder="Dirección de vivienda">
                        </div>
                        <div class="field">
                            <label>N° de Tlf</label>
                            <input type="text" name="telefono" class="input-field"
                                   oninput="this.value = this.value.replace(/[^0-9+]/g, '').slice(0, 15)" placeholder="+584241234567">
                        </div>
                        <div class="field">
                            <label>Fecha de Entrevista</label>
                            <input type="date" name="fecha_entrevista" class="input-field">
                                   oninput="formatearFechaAdol(this)" maxlength="10" autocomplete="off">
                        </div>
                    </div>
                </div>
                
                {{-- PASO 2: DATOS FAMILIARES --}}
                <div class="stepAdol" id="stepAdol2" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-home" style="color: #f59e0b;"></i> Datos Familiares</h4>
                    <div class="grid-form">
                        <div class="field"><label>Nombre del Padre</label><input type="text" name="padre_nombre" class="input-field" placeholder="Nombre del padre"></div>
                        <div class="field"><label>Edad del Padre</label><input type="number" name="padre_edad" class="input-field" min="0" placeholder="Edad"></div>
                        <div class="field"><label>Ocupación del Padre</label><input type="text" name="padre_ocupacion" class="input-field" placeholder="Ocupación"></div>
                        <div class="field"><label>Nombre de la Madre</label><input type="text" name="madre_nombre" class="input-field" placeholder="Nombre de la madre"></div>
                        <div class="field"><label>Edad de la Madre</label><input type="number" name="madre_edad" class="input-field" min="0" placeholder="Edad"></div>
                        <div class="field"><label>Ocupación de la Madre</label><input type="text" name="madre_ocupacion" class="input-field" placeholder="Ocupación"></div>
                        <div class="field full-width">
                            <label>Hermanos (edades)</label>
                            <textarea name="hermanos" class="input-field" rows="2" placeholder="Nombres y edades de los hermanos"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Personas con quien vive</label>
                            <textarea name="personas_con_quien_vive" class="input-field" rows="2" placeholder="¿Con quiénes vives actualmente?"></textarea>
                        </div>
                    </div>
                </div>
                
                {{-- PASO 3: MOTIVO DE CONSULTA --}}
                <div class="stepAdol" id="stepAdol3" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-clipboard-list" style="color: #f59e0b;"></i> Motivo de Consulta</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Motivo de consulta: ¿Qué te trae por consulta? *</label>
                            <textarea name="motivo_consulta" class="input-field" rows="4" required placeholder="Cuéntanos con tus propias palabras qué te trae por consulta"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Hace cuánto tiempo llevas con esto? ¿Qué te gustaría que cambiara?</label>
                            <textarea name="tiempo_y_cambio" class="input-field" rows="3" placeholder="Desde cuándo ocurre y qué te gustaría cambiar"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Hay algo que te preocupe más?</label>
                            <textarea name="preocupacion_principal" class="input-field" rows="3" placeholder="¿Hay algo de esto que te preocupe más que lo demás?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Qué crees que lo empeora o lo mejora?</label>
                            <textarea name="empeora_o_mejora" class="input-field" rows="3" placeholder="¿En qué momentos está mejor o peor?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Padres: ¿Qué observan en casa?</label>
                            <textarea name="observacion_padres" class="input-field" rows="4" placeholder="Lo que los padres han observado en el hogar"></textarea>
                        </div>
                    </div>
                </div>
                
                {{-- PASO 4: HISTORIA DEL PROBLEMA --}}
                <div class="stepAdol" id="stepAdol4" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-search" style="color: #f59e0b;"></i> Historia del Problema</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>¿Cuándo empezó? ¿Fue gradual o tras un evento?</label>
                            <textarea name="inicio_problema" class="input-field" rows="3" placeholder="¿Cómo empezó todo? ¿De poco en poco o después de algo que pasó?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Frecuencia y ejemplo concreto</label>
                            <textarea name="frecuencia_ejemplo" class="input-field" rows="3" placeholder="¿Cada cuánto pasa? Cuéntanos un ejemplo reciente"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Qué pensamientos pasan por tu cabeza?</label>
                            <textarea name="pensamientos_automaticos" class="input-field" rows="3" placeholder="¿Qué piensas cuando sucede?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Qué sientes en el cuerpo? Estado de ánimo</label>
                            <textarea name="sensaciones_corporales" class="input-field" rows="3" placeholder="¿Cómo lo sientes en tu cuerpo? ¿Cómo está tu ánimo?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Qué haces normalmente? ¿Te ayuda?</label>
                            <textarea name="conductas_habituales" class="input-field" rows="3" placeholder="¿Qué haces cuando te pasa? ¿Te ha servido de algo?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Pensamientos de hacerte daño</label>
                            <textarea name="pensamientos_autolesion" class="input-field" rows="2" placeholder="¿Has tenido pensamientos de hacerte daño?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Has pensado en cómo? ¿Plan? ¿Medios?</label>
                            <textarea name="plan_autolesion" class="input-field" rows="2" placeholder="¿Has pensado en cómo lo harías? ¿Tienes acceso a medios?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Has intentado hacerte daño?</label>
                            <textarea name="intentos_autolesion" class="input-field" rows="2" placeholder="¿Lo has intentado alguna vez? Cuéntanos cuándo"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Has pensado en hacer daño a otra persona?</label>
                            <textarea name="pensamientos_heterolesion" class="input-field" rows="2" placeholder="¿Has pensado en hacerle daño a alguien?"></textarea>
                        </div>
                    </div>
                </div>
                
                {{-- PASO 5: RELACIONES, VIDA SOCIAL Y ACADÉMICA --}}
                <div class="stepAdol" id="stepAdol5" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-users" style="color: #f59e0b;"></i> Relaciones, Vida Social y Académica</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Relaciones familiares: ¿Con quién vives? ¿Cómo es tu relación?</label>
                            <textarea name="relaciones_familiares" class="input-field" rows="3" placeholder="¿Cómo es la relación con las personas con quienes vives?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Qué pasa cuando hay conflicto en casa?</label>
                            <textarea name="conflictos_familiares" class="input-field" rows="3" placeholder="¿Qué suele pasar cuando discuten o hay conflictos en casa?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Vida académica: ¿Cómo te va? ¿Asignaturas?</label>
                            <textarea name="vida_academica" class="input-field" rows="3" placeholder="¿Cómo te va en el colegio o liceo? ¿Qué asignaturas prefieres o te cuestan?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Cambios en rendimiento, faltas, repeticiones</label>
                            <textarea name="cambios_academicos" class="input-field" rows="2" placeholder="¿Ha cambiado tu rendimiento? ¿Faltas o repeticiones de año?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Vida social: ¿Tienes amigos? ¿Te sientes aceptado?</label>
                            <textarea name="vida_social" class="input-field" rows="3" placeholder="Cuéntanos sobre tus amistades y si te sientes aceptado/a"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Rechazo, bullying o ciberacoso?</label>
                            <textarea name="bullying" class="input-field" rows="2" placeholder="¿Has vivido rechazo, bullying o ciberacoso?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Tienes pareja? ¿Cómo es la relación?</label>
                            <textarea name="relacion_pareja" class="input-field" rows="2" placeholder="¿Tienes novio/a o pareja? ¿Cómo es la relación?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Redes sociales: ¿Cuánto tiempo? ¿Problemas?</label>
                            <textarea name="redes_sociales" class="input-field" rows="3" placeholder="¿Cuánto tiempo pasas en redes sociales? ¿Han causado algún problema?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Mensajes que te hayan hecho daño?</label>
                            <textarea name="mensajes_daninos" class="input-field" rows="2" placeholder="¿Has recibido mensajes o contenido que te haya hecho daño?"></textarea>
                        </div>
                    </div>
                </div>
                
                {{-- PASO 6: SALUD, CONSUMO Y EVALUACIÓN EMOCIONAL --}}
                <div class="stepAdol" id="stepAdol6" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-heartbeat" style="color: #f59e0b;"></i> Salud, Consumo y Evaluación Emocional</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>Salud física: enfermedad crónica, medicación, sueño, apetito</label>
                            <textarea name="salud_fisica" class="input-field" rows="3" placeholder="Enfermedades, medicamentos que tomes, cómo duermes y comes"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Terapia anterior o medicación?</label>
                            <textarea name="terapia_previa" class="input-field" rows="2" placeholder="¿Has ido a terapia antes o tomado algún medicamento?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Antecedentes familiares de psicopatología</label>
                            <textarea name="antecedentes_familiares" class="input-field" rows="2" placeholder="¿Alguien en tu familia ha tenido problemas de salud mental?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Consumo de sustancias: alcohol/drogas, frecuencia</label>
                            <textarea name="consumo_sustancias" class="input-field" rows="3" placeholder="¿Consumes alcohol u otras sustancias? ¿Con qué frecuencia?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Reducir consumo? ¿Molestia por críticas?</label>
                            <textarea name="deseo_reduccion_consumo" class="input-field" rows="2" placeholder="¿Has querido reducirlo? ¿Te molesta que te critiquen por ello?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Usar para olvidar? ¿Olvidar lo que pasó?</label>
                            <textarea name="consumo_para_olvidar" class="input-field" rows="2" placeholder="¿Lo has usado para olvidar algo o no pensar en ello?"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Identidad de género / orientación sexual</label>
                            <textarea name="identidad_genero" class="input-field" rows="2" placeholder="Si lo deseas, cuéntanos cómo te identificas"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Depresión: tristeza, pérdida de interés, sueño, apetito</label>
                            <textarea name="sintomas_depresivos" class="input-field" rows="3" placeholder="Tristeza, pérdida de interés, cambios en el sueño o apetito"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Ansiedad / pánico</label>
                            <textarea name="sintomas_ansiosos" class="input-field" rows="3" placeholder="Preocupación excesiva, nervios, crisis de pánico"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Irritabilidad / impulso</label>
                            <textarea name="irritabilidad_impulsividad" class="input-field" rows="2" placeholder="Irritabilidad, dificultad para controlar impulsos"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Conductas de riesgo</label>
                            <textarea name="conductas_riesgo" class="input-field" rows="2" placeholder="Comportamientos peligrosos o arriesgados"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>Estrategias de afrontamiento</label>
                            <textarea name="estrategias_afrontamiento" class="input-field" rows="2" placeholder="¿Qué haces para enfrentar los momentos difíciles?"></textarea>
                        </div>
                    </div>
                </div>
                
                {{-- PASO 7: RECURSOS, FORTALEZAS Y DATOS DEL SISTEMA --}}
                <div class="stepAdol" id="stepAdol7" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-star" style="color: #f59e0b;"></i> Recursos, Fortalezas y Datos del Sistema</h4>
                    <div class="grid-form">
                        <div class="field full-width">
                            <label>¿Qué haces bien? ¿Qué te da orgullo?</label>
                            <textarea name="fortalezas" class="input-field" rows="3" placeholder="Cosas que se te dan bien, logros, algo que te enorgullezca"></textarea>
                        </div>
                        <div class="field full-width">
                            <label>¿Quién te respalda?</label>
                            <textarea name="red_apoyo" class="input-field" rows="2" placeholder="Personas en quien confías o que te acompañan"></textarea>
                        </div>
                        <div class="field">
                            <label>Escala 0-10: ¿Cuánto quieres que cambien las cosas?</label>
                            <input type="number" name="escala_motivacion" class="input-field" min="0" max="10" placeholder="0 a 10">
                        </div>
                        <div class="field full-width">
                            <label>Mini-ejercicio: 3 valores y 1 acción pequeña</label>
                            <textarea name="valores_accion" class="input-field" rows="3" placeholder="Escribe 3 valores importantes para ti y una acción pequeña para esta semana"></textarea>
                        </div>
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
                                @foreach(['Arístides Bastidas','Bolívar','Bruzual','Cocorote','Independencia','José Antonio Páez','La Trinidad','Manuel Monge','Nirgua','Peña','San Felipe','Sucre','Urachiche','Veroes'] as $m)
                                <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field full-width">
                            <label>Tipo de Atención *</label>
                            <select name="tipo_atencion" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="privado">Privado</option>
                                <option value="publico">Público</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 20px; background: #fef3c7; border-radius: 12px; border: 1px solid #fcd34d;">
                        <h5 style="margin: 0 0 10px; color: #92400e; font-size: 14px;"><i class="fas fa-check-circle" style="color: #f59e0b;"></i> Resumen del Registro</h5>
                        <p style="margin: 0; color: #78350f; font-size: 13px;">Al guardar, se creará el expediente clínico del paciente adolescente.</p>
                    </div>
                </div>
                
            </div>
            
            <div class="modal-paciente-footer" style="justify-content: space-between;">
                <button type="button" class="btn-paciente btn-paciente-cancel" id="btnPrevAdol" onclick="prevStepAdol()" style="display: none;"><i class="fas fa-arrow-left"></i> Anterior</button>
                <div>
                    <button type="button" class="btn-paciente btn-paciente-cancel" onclick="cerrarModal('adolescente')" style="margin-right: 8px;">Cancelar</button>
                    <button type="button" class="btn-paciente btn-paciente-save" id="btnNextAdol" onclick="nextStepAdol()" style="background: #f59e0b;">Siguiente <i class="fas fa-arrow-right"></i></button>
                    <button type="submit" class="btn-paciente btn-paciente-save" id="btnSubmitAdol" style="display: none; background: #f59e0b;"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('pacientes.partials.modals.estado-inline-modal')
