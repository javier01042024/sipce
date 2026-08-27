{{-- configuracion/estados/partials/modals/create.blade.php --}}
<div id="modalCrear" class="estado-modal-overlay">
    <div class="estado-modal">
        <div class="estado-modal-header">
            <h3>➕ Nuevo Estado</h3>
            <button type="button" class="estado-modal-close" onclick="cerrarModalCrear()">×</button>
        </div>
        <form action="{{ route('configuracion.estados.store') }}" method="POST">
            @csrf
            <div class="estado-modal-body">
                <div class="estado-form-group">
                    <label class="estado-form-label">Tipo de Estado *</label>
                    <input type="text" name="tipo" class="estado-form-input" placeholder="Ej: Activo, Alta, En Pausa..." required>
                    <span class="estado-form-hint">Nombre único para identificar el estado</span>
                </div>
                <div class="estado-form-group">
                    <label class="estado-form-label">Descripción *</label>
                    <textarea name="descripcion" class="estado-form-input estado-form-textarea" rows="2" placeholder="Describa el propósito de este estado..." required></textarea>
                </div>
                <div class="estado-form-group">
                    <div class="estado-checkbox-wrapper">
                        <input type="checkbox" name="permite_citas" id="crear_permite_citas" value="1">
                        <label for="crear_permite_citas">
                            ¿Permite agendar citas?
                            <span class="checkbox-hint">Los pacientes con este estado podrán programar citas</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="estado-modal-footer">
                <button type="button" class="estado-btn estado-btn-cancelar" onclick="cerrarModalCrear()">Cancelar</button>
                <button type="submit" class="estado-btn estado-btn-guardar">💾 Guardar</button>
            </div>
        </form>
    </div>
</div>
