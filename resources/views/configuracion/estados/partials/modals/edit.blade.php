{{-- configuracion/estados/partials/modals/edit.blade.php --}}
<div id="modalEditar" class="estado-modal-overlay">
    <div class="estado-modal">
        <div class="estado-modal-header">
            <h3>✏️ Editar Estado</h3>
            <button type="button" class="estado-modal-close" onclick="cerrarModalEditar()">×</button>
        </div>
        <form id="formEditar" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="estado_id" id="edit_id">
            <div class="estado-modal-body">
                <div class="estado-form-group">
                    <label class="estado-form-label">Tipo de Estado *</label>
                    <input type="text" name="tipo" id="edit_tipo" class="estado-form-input" required>
                </div>
                <div class="estado-form-group">
                    <label class="estado-form-label">Descripción *</label>
                    <textarea name="descripcion" id="edit_descripcion" class="estado-form-input estado-form-textarea" rows="2" required></textarea>
                </div>
                <div class="estado-form-group">
                    <div class="estado-checkbox-wrapper">
                        <input type="checkbox" name="permite_citas" id="edit_permite_citas" value="1">
                        <label for="edit_permite_citas">
                            ¿Permite agendar citas?
                            <span class="checkbox-hint">Los pacientes con este estado podrán programar citas</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="estado-modal-footer">
                <button type="button" class="estado-btn estado-btn-cancelar" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="estado-btn estado-btn-guardar">💾 Actualizar</button>
            </div>
        </form>
    </div>
</div>
