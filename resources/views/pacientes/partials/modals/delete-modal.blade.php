<div id="deleteModalPaciente" class="modal-paciente">
    <div class="modal-paciente-box" style="max-width: 450px;">
        <div class="modal-paciente-body delete-box">
            <div class="danger-icon">!</div>
            <h3>Eliminar paciente</h3>
            <p>¿Estás seguro de que deseas eliminar este paciente?<br>Esta acción no se puede deshacer.</p>

            <div class="modal-paciente-footer" style="border: none; padding: 20px 0 0 0;">
                <button onclick="closeDeleteModalPaciente()" class="btn-paciente btn-paciente-cancel">
                    <i class="fas fa-times"></i>
                    Cancelar
                </button>
                <button class="btn-paciente btn-paciente-delete" id="confirmDeleteBtnPaciente">
                    <i class="fas fa-trash"></i>
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>