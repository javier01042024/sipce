<div class="modal-cancel" id="modalCancelar">
    <div class="modal-cancel-content">
        <div class="modal-cancel-header">
            <h4>
                <i class="fas fa-exclamation-triangle"></i>
                Cancelar Cita
            </h4>
            <button class="btn-close-modal" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="formCancelar" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="estado" value="cancelada">
            
            <div class="form-group">
                <label for="motivo">
                    <i class="fas fa-comment-alt me-2"></i>
                    Motivo de cancelaciÃ³n
                </label>
                <textarea 
                    name="motivo_cancelacion" 
                    id="motivo" 
                    required 
                    placeholder="Explica brevemente el motivo de la cancelaciÃ³n..."
                ></textarea>
            </div>

            <label class="reprogramar-option">
                <input type="checkbox" name="reprogramar" id="reprogramar" value="1">
                <div>
                    <strong style="display: block; color: #1e293b;">
                        <i class="fas fa-calendar-alt me-2" style="color: var(--sipce-primary);"></i>
                        Reprogramar esta cita
                    </strong>
                    <small style="color: #64748b;">Se abrirÃ¡ el formulario para agendar nueva fecha</small>
                </div>
            </label>

            <div class="modal-actions">
                <button type="button" class="btn-secondary-modal" onclick="closeModal()">
                    <i class="fas fa-times me-2"></i>Cerrar
                </button>
                <button type="submit" class="btn-confirm-cancel">
                    <i class="fas fa-check me-2"></i>Confirmar CancelaciÃ³n
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.modal-cancel {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(5px);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-cancel.active {
    display: flex;
}

.modal-cancel-content {
    background: white;
    border-radius: 20px;
    padding: 30px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-cancel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.modal-cancel-header h4 {
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-cancel-header h4 i {
    color: #ef4444;
    font-size: 24px;
}

.btn-close-modal {
    background: #f1f5f9;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    color: #64748b;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close-modal:hover {
    background: #e2e8f0;
    color: #1e293b;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #475569;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    resize: vertical;
    min-height: 100px;
    transition: border-color 0.3s;
    font-family: inherit;
}

.form-group textarea:focus {
    outline: none;
    border-color: var(--sipce-primary);
}

.reprogramar-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
    margin-top: 15px;
    border: 2px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.3s;
}

.reprogramar-option:hover {
    border-color: var(--sipce-primary);
    background: #f0f4ff;
}

.reprogramar-option input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: var(--sipce-primary);
}

.modal-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}

.btn-confirm-cancel {
    flex: 1;
    padding: 12px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-confirm-cancel:hover {
    filter: brightness(1.1);
    transform: translateY(-2px);
}

.btn-secondary-modal {
    padding: 12px 24px;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-secondary-modal:hover {
    background: #e2e8f0;
}
</style>