<div id="modalNino" class="modal-paciente" style="display: none;">
    <div class="modal-paciente-box" style="max-width: 800px;">
        <div class="modal-paciente-header" style="background: linear-gradient(135deg, #10b981, #059669);">
            <h2><i class="fas fa-child"></i> Registrar Paciente NiÃ±o</h2>
            <button class="modal-paciente-close" onclick="cerrarModal('niÃ±o')">Ã—</button>
        </div>
        
        <div style="padding: 15px 25px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 12px; font-weight: 600; color: #10b981;" id="stepLabelNino">Paso 1 de 7</span>
                <span style="font-size: 12px; color: #64748b;" id="stepTitleNino">Datos BÃ¡sicos</span>
            </div>
            <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px;">
                <div id="progressBarNino" style="width: 14%; height: 100%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 3px; transition: width 0.3s ease;"></div>
            </div>
        </div>
        
        <form action="{{ route('pacientes.store') }}" method="POST" id="formNino" onsubmit="return validarFormNino()">
            @csrf
            <input type="hidden" name="tipo_paciente" value="niÃ±o">
            
            <div class="modal-paciente-body">
                
                {{-- PASO 1: DATOS BÃSICOS --}}
                <div class="stepNino" id="stepNino1">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-info-circle" style="color: #10b981;"></i> Datos BÃ¡sicos</h4>
                    <div class="grid-form">
                        <div class="field"><label>NÂ° Expediente</label><input type="text" class="input-field" placeholder="AutomÃ¡tico" disabled></div>
                        <div class="field">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="input-field" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]/g, '')" placeholder="Solo letras">
                        </div>
                        <div class="field">
                            <label>Apellido</label>
                            <input type="text" name="apellido" class="input-field" required
                                   oninput="this.value = this.value.replace(/[^a-zA-ZÃ¡Ã©Ã­Ã³ÃºÃÃ‰ÃÃ“ÃšÃ±Ã‘\s]/g, '')" placeholder="Solo letras">
                        </div>
                        <div class="field">
                            <label>Fecha de Nacimiento</label>
                            <input type="text" name="fecha_nacimiento" class="input-field" placeholder="DD/MM/AAAA" required
                                   oninput="formatearFechaNino(this)" maxlength="10" autocomplete="off">
                            <span class="text-muted" style="font-size: 11px; color: #64748b;">Edad mÃ¡xima: 11 aÃ±os</span>
                        </div>
                        <div class="field">
                            <label>TelÃ©fono de Contacto</label>
                            <input type="text" name="telefono_contacto" class="input-field"
                                   oninput="this.value = this.value.replace(/[^0-9+]/g, '').slice(0, 15)" placeholder="+584241234567">
                        </div>
                        <div class="field"><label>Grado de InstrucciÃ³n</label><input type="text" name="grado_instruccion" class="input-field" placeholder="Ej: Primaria 2do grado"></div>
                        <div class="field"><label>Maestro</label><input type="text" name="maestro" class="input-field" placeholder="Nombre del maestro"></div>
                        <div class="field full-width"><label>DirecciÃ³n</label><input type="text" name="direccion" class="input-field" placeholder="DirecciÃ³n completa"></div>
                    </div>
                </div>
                
                {{-- PASO 2: DATOS DE LOS PADRES --}}
                <div class="stepNino" id="stepNino2" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-users" style="color: #10b981;"></i> Datos de los Padres</h4>
                    <div class="grid-form">
                        <div class="field"><label>Nombre del Padre</label><input type="text" name="nombre_padre" class="input-field"></div>
                        <div class="field"><label>Edad del Padre</label><input type="number" name="edad_padre" class="input-field" min="18" max="99"></div>
                        <div class="field full-width"><label>OcupaciÃ³n del Padre</label><input type="text" name="ocupacion_padre" class="input-field"></div>
                        <div class="field"><label>Nombre de la Madre</label><input type="text" name="nombre_madre" class="input-field"></div>
                        <div class="field"><label>Edad de la Madre</label><input type="number" name="edad_madre" class="input-field" min="18" max="99"></div>
                        <div class="field full-width"><label>OcupaciÃ³n de la Madre</label><input type="text" name="ocupacion_madre" class="input-field"></div>
                        <div class="field"><label>Hermanos (edades)</label><input type="text" name="hermanos" class="input-field" placeholder="Ej: Juan 8, MarÃ­a 5"></div>
                        <div class="field"><label>Personas con quien vive</label><input type="text" name="personas_vive" class="input-field" placeholder="Ej: Padres, abuela"></div>
                    </div>
                </div>
                
                {{-- PASO 3: MOTIVO DE CONSULTA --}}
                <div class="stepNino" id="stepNino3" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-clipboard-list" style="color: #10b981;"></i> Motivo de Consulta</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Motivo de Consulta</label><textarea name="motivo_consulta" class="input-field" rows="2" required placeholder="Describa el motivo principal de la consulta"></textarea></div>
                        <div class="field full-width"><label>Desarrollo del problema</label><textarea name="desarrollo_problema" class="input-field" rows="2" placeholder="Â¿CÃ³mo se ha desarrollado el problema?"></textarea></div>
                        <div class="field">
                            <label>Frecuencia</label>
                            <select name="frecuencia_sintomas" class="input-field"><option value="">Seleccione...</option><option>Diario</option><option>Semanal</option><option>Mensual</option><option>Ocasional</option></select>
                        </div>
                        <div class="field">
                            <label>Intensidad</label>
                            <select name="intensidad_sintomas" class="input-field"><option value="">Seleccione...</option><option>Leve</option><option>Moderado</option><option>Severo</option></select>
                        </div>
                        <div class="field"><label>DuraciÃ³n</label><input type="text" name="duracion_sintomas" class="input-field" placeholder="Ej: 3 meses, 1 aÃ±o"></div>
                        <div class="field"><label>Contexto</label><input type="text" name="contexto_problema" class="input-field" placeholder="Â¿DÃ³nde y cuÃ¡ndo ocurre?"></div>
                        <div class="field full-width"><label>Estrategias de afrontamiento</label><textarea name="estrategias_afrontamiento" class="input-field" rows="2" placeholder="Â¿QuÃ© han intentado?"></textarea></div>
                        <div class="field full-width"><label>Consecuencias</label><input type="text" name="consecuencias" class="input-field" placeholder="Â¿Por quÃ© creen que ocurre?"></div>
                        <div class="field full-width"><label>Acontecimientos estresantes</label><textarea name="acontecimientos_estresantes" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Expectativas</label><textarea name="expectativas" class="input-field" rows="2"></textarea></div>
                    </div>
                </div>
                
                {{-- PASO 4: HISTORIA PERINATAL --}}
                <div class="stepNino" id="stepNino4" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-baby" style="color: #10b981;"></i> Historia Perinatal</h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Tipo de Parto</label>
                            <select name="tipo_parto" class="input-field"><option value="">Seleccione...</option><option>Natural</option><option>CesÃ¡rea</option><option>Inducido</option></select>
                        </div>
                        <div class="field"><label>Causa parto inducido</label><input type="text" name="causa_parto_inducido" class="input-field"></div>
                        <div class="field full-width"><label>Dificultades en el parto</label><textarea name="dificultades_parto" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Comportamiento del bebÃ©</label><input type="text" name="comportamiento_bebe" class="input-field" placeholder="SueÃ±o, llanto, alimentaciÃ³n"></div>
                        <div class="field full-width"><label>Reacciones ante estÃ­mulos</label><input type="text" name="reacciones_estimulos" class="input-field"></div>
                        <div class="field full-width"><label>SituaciÃ³n familiar postnatal</label><input type="text" name="situacion_familiar_postnatal" class="input-field"></div>
                        <div class="field full-width"><label>AceptaciÃ³n maternidad</label><textarea name="aceptacion_maternidad" class="input-field" rows="2"></textarea></div>
                    </div>
                </div>
                
                {{-- PASO 5: DESARROLLO EVOLUTIVO --}}
                <div class="stepNino" id="stepNino5" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-child-reaching" style="color: #10b981;"></i> Desarrollo Evolutivo</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Desarrollo en diferentes Ã¡reas</label><textarea name="desarrollo_areas" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Habla y lenguaje</label><textarea name="habla_lenguaje" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>AlimentaciÃ³n del bebÃ©</label><input type="text" name="alimentacion_bebe" class="input-field"></div>
                        <div class="field full-width"><label>Dificultades en el destete</label><input type="text" name="dificultades_destete" class="input-field"></div>
                        <div class="field full-width"><label>Trastornos alimentaciÃ³n</label><input type="text" name="trastornos_alimentacion" class="input-field" placeholder="VÃ³mitos, diarreas..."></div>
                        <div class="field full-width"><label>AlimentaciÃ³n actual</label><textarea name="alimentacion_actual" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>DescripciÃ³n dÃ­a anterior</label><textarea name="descripcion_dia_anterior" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Info. sexualidad</label><textarea name="info_sexualidad" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Desarrollo sexual actual</label><input type="text" name="desarrollo_sexual" class="input-field" placeholder="DescripciÃ³n acorde a la edad"></div>
                    </div>
                </div>
                
                {{-- PASO 6: ÃREA ESCOLAR Y CONDUCTUAL --}}
                <div class="stepNino" id="stepNino6" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-school" style="color: #10b981;"></i> Ãrea Escolar y Conductual</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Tareas escolares</label><textarea name="tareas_escolares" class="input-field" rows="2" placeholder="IntervenciÃ³n padres, dificultades"></textarea></div>
                        <div class="field full-width"><label>Rendimiento escolar</label><input type="text" name="rendimiento_escolar" class="input-field"></div>
                        <div class="field full-width"><label>Responsabilidades domÃ©sticas</label><input type="text" name="responsabilidades_domesticas" class="input-field"></div>
                        <div class="field full-width"><label>Recompensas</label><input type="text" name="recompensas" class="input-field"></div>
                        <div class="field">
                            <label>Nivel de actividad</label>
                            <select name="nivel_actividad" class="input-field"><option value="">Seleccione...</option><option>Alto</option><option>Medio</option><option>Bajo</option></select>
                        </div>
                        <div class="field"><label>Entretenimiento</label><input type="text" name="entretenimiento" class="input-field" placeholder="Tipo de actividades"></div>
                        <div class="field">
                            <label>Tipo de actividades</label>
                            <select name="tipo_actividades" class="input-field"><option value="">Seleccione...</option><option>Sedentarias</option><option>Inquietas</option><option>Mixtas</option></select>
                        </div>
                        <div class="field">
                            <label>FinalizaciÃ³n de tareas</label>
                            <select name="finalizacion_tareas" class="input-field"><option value="">Seleccione...</option><option>SÃ­, siempre</option><option>A veces</option><option>No</option></select>
                        </div>
                        <div class="field full-width"><label>Arrebatos</label><textarea name="arrebatos" class="input-field" rows="2" placeholder="CÃ³mo se enoja y reacciona"></textarea></div>
                        <div class="field full-width"><label>Oposicionismo</label><textarea name="oposicionismo" class="input-field" rows="2" placeholder="Hace caso, sigue instrucciones"></textarea></div>
                        <div class="field full-width"><label>Agresiones</label><textarea name="agresiones" class="input-field" rows="2" placeholder="CÃ³mo resuelve conflictos"></textarea></div>
                    </div>
                </div>
                
                {{-- PASO 7: RELACIONES SOCIALES + DATOS DEL SISTEMA --}}
                <div class="stepNino" id="stepNino7" style="display: none;">
                    <h4 style="color: #2d3748; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-heart" style="color: #10b981;"></i> Relaciones Sociales</h4>
                    <div class="grid-form">
                        <div class="field full-width"><label>Autoimagen</label><textarea name="autoimagen" class="input-field" rows="2" placeholder="CÃ³mo se ve a sÃ­ mismo"></textarea></div>
                        <div class="field full-width"><label>PercepciÃ³n de los demÃ¡s</label><textarea name="percepcion_demas" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Relaciones con niÃ±os</label><textarea name="relaciones_ninos" class="input-field" rows="2" placeholder="Con compaÃ±eros"></textarea></div>
                        <div class="field">
                            <label>Â¿Tiene amigos?</label>
                            <select name="tiene_amigos" class="input-field"><option value="">Seleccione...</option><option>SÃ­</option><option>No</option><option>Pocos</option></select>
                        </div>
                        <div class="field"><label>Actividades sociales</label><input type="text" name="actividades_sociales" class="input-field"></div>
                        <div class="field full-width"><label>Tipo de niÃ±os que le atraen</label><input type="text" name="tipo_ninos_atraen" class="input-field"></div>
                        <div class="field full-width"><label>Comportamiento en grupo</label><textarea name="comportamiento_grupo" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Juegos preferidos</label><input type="text" name="juegos_preferidos" class="input-field"></div>
                        <div class="field full-width"><label>RelaciÃ³n con adultos</label><textarea name="relacion_adultos" class="input-field" rows="2"></textarea></div>
                        <div class="field full-width"><label>Entretenimiento en casa</label><input type="text" name="entretenimiento_casa" class="input-field"></div>
                        <div class="field full-width"><label>Actividades exteriores</label><input type="text" name="actividades_exteriores" class="input-field"></div>
                        <div class="field full-width"><label>Actividades deportivas</label><input type="text" name="actividades_deportivas" class="input-field"></div>
                        <div class="field full-width"><label>Intereses</label><textarea name="intereses" class="input-field" rows="2"></textarea></div>
                    </div>
                    
                    <h4 style="color: #2d3748; margin: 20px 0 15px; font-size: 16px;"><i class="fas fa-cog" style="color: #10b981;"></i> Datos del Sistema</h4>
                    <div class="grid-form">
                        <div class="field">
                            <label>Prioridad</label>
                            <select name="prioridad" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgencia">Urgencia</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Estado</label>
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
                            <label>Tipo de AtenciÃ³n</label>
                            <select name="tipo_atencion" class="input-field" required>
                                <option value="">Seleccione...</option>
                                <option value="privado">Privado</option>
                                <option value="publico">PÃºblico</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 20px; background: #ecfdf5; border-radius: 12px; border: 1px solid #a7f3d0;">
                        <h5 style="margin: 0 0 10px; color: #065f46; font-size: 14px;"><i class="fas fa-check-circle" style="color: #10b981;"></i> Resumen del Registro</h5>
                        <p style="margin: 0; color: #047857; font-size: 13px;">Al guardar, se crearÃ¡ el expediente del paciente niÃ±o.</p>
                    </div>
                </div>
                
            </div>
            
            <div class="modal-paciente-footer" style="justify-content: space-between;">
                <button type="button" class="btn-paciente btn-paciente-cancel" id="btnPrevNino" onclick="prevStepNino()" style="display: none;"><i class="fas fa-arrow-left"></i> Anterior</button>
                <div>
                    <button type="button" class="btn-paciente btn-paciente-cancel" onclick="cerrarModal('niÃ±o')" style="margin-right: 8px;">Cancelar</button>
                    <button type="button" class="btn-paciente btn-paciente-save" id="btnNextNino" onclick="nextStepNino()" style="background: #10b981;">Siguiente <i class="fas fa-arrow-right"></i></button>
                    <button type="submit" class="btn-paciente btn-paciente-save" id="btnSubmitNino" style="display: none; background: #10b981;"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

@include('pacientes.partials.modals.estado-inline-modal')