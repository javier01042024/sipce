<div id="modalPaciente" class="modal-paciente">
    <div class="modal-paciente-box">
        <div class="modal-paciente-header">
            <h2>
                <i class="fas fa-user-plus me-2"></i>
                Nuevo paciente
            </h2>
            <button onclick="closeModalPaciente()" class="modal-paciente-close">&times;</button>
        </div>

        <form action="{{ route('pacientes.store') }}" method="POST" style="display: flex; flex-direction: column; height: 100%; overflow: hidden;">
            @csrf

            <div class="modal-paciente-body">
                <div class="grid-form">
                    <div class="input-group">
                        <i class="fas fa-hashtag input-icon"></i>
                        <input type="text" name="numero_expediente" placeholder="Número de expediente" class="input-field" required>
                    </div>
                    
                    <div class="input-group" style="display: flex; gap: 0; position: relative;">
                        <i class="fas fa-id-card input-icon" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b; z-index: 2;"></i>
                        <select name="nacionalidad" class="input-field" required
                            style="width: 80px; border-right: none; border-radius: 10px 0 0 10px; padding-left: 40px; background: #f8fafc; font-weight: 600; color: #334155; cursor: pointer;">
                            <option value="V" selected>V</option>
                            <option value="E">E</option>
                        </select>
                        <input type="text" name="cedula_paciente" placeholder="Cédula (8 dígitos)"
                            class="input-field" required
                            maxlength="8"
                            style="border-radius: 0 10px 10px 0; border-left: 1px solid #e2e8f0; flex: 1;"
                            oninput="validarCedula(this)">
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Ingrese 8 dígitos numéricos
                    </small>

                    <div class="input-group full-width">
                        <i class="fas fa-user input-icon"></i>
                        <input name="nombre_completo" placeholder="Nombre completo" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="number" name="telefono" placeholder="Teléfono" class="input-field">
                    </div>

                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="Correo electrónico" class="input-field">
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-calendar input-icon"></i>
                        <input type="date" name="fecha_nacimiento" class="input-field">
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <textarea name="direccion" placeholder="Dirección completa" class="input-field" rows="2"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-notes-medical input-icon"></i>
                        <textarea name="motivo_consulta" placeholder="Motivo de consulta" class="input-field" rows="2"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-stethoscope input-icon"></i>
                        <textarea name="diagnostico_preliminar" placeholder="Diagnóstico preliminar" class="input-field" rows="2"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-flag input-icon"></i>
                        <select name="prioridad" class="input-field">
                            <option value="">Seleccionar prioridad</option>
                            <option value="Alta">🔴 Alta</option>
                            <option value="Media">🟡 Media</option>
                            <option value="Baja">🟢 Baja</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-paciente-footer">
                <button type="button" onclick="closeModalPaciente()" class="btn-paciente btn-paciente-cancel">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button type="submit" class="btn-paciente btn-paciente-save">
                    <i class="fas fa-save"></i>
                    Guardar paciente
                </button>
            </div>
        </form>
    </div>
</div>