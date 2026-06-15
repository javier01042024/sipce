// Módulo de gestión de citas para el LISTADO y MODAL de cancelación
const CitasManager = (function() {
    let citaIdActual = null;
    
    // ====================================================
    // FUNCIONES PARA EL LISTADO DE CITAS
    // ====================================================
    
    // Cambiar estado (asistió / no asistió) con AJAX
    async function cambiarEstado(citaId, estado) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const url = `/citas/${citaId}`;
        
        const boton = event.currentTarget;
        const textoOriginal = boton.innerHTML;
        boton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        boton.disabled = true;
        
        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    estado: estado,
                    _method: 'PUT'
                })
            });
            
            if (response.ok) {
                actualizarTarjeta(citaId, estado);
                mostrarNotificacion(estado);
            } else {
                throw new Error('Error al actualizar');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('error', 'Hubo un error al procesar');
        } finally {
            boton.innerHTML = textoOriginal;
            boton.disabled = false;
        }
    }
    
    // Actualizar la tarjeta visualmente sin recargar
    function actualizarTarjeta(citaId, estado) {
        const tarjeta = document.querySelector(`.cita-card[data-id="${citaId}"]`);
        if (!tarjeta) return;
        
        tarjeta.classList.remove('atendida', 'cancelada', 'no-asistio');
        
        if (estado === 'atendida') {
            tarjeta.classList.add('atendida');
            const acciones = tarjeta.querySelector('.cita-actions');
            if (acciones) acciones.style.display = 'none';
            agregarBadge(tarjeta, '✓ ATENDIDA', '#10b981');
        } 
        else if (estado === 'no_asistio') {
            tarjeta.classList.add('no-asistio');
            const acciones = tarjeta.querySelector('.cita-actions');
            if (acciones) acciones.style.display = 'none';
            agregarBadge(tarjeta, '✕ NO ASISTIÓ', '#d97706');
        }
        
        tarjeta.style.animation = 'none';
        tarjeta.offsetHeight;
        tarjeta.style.animation = 'slideUp 0.3s ease';
    }
    
    // Agregar badge a la tarjeta
    function agregarBadge(tarjeta, texto, color) {
        const badgeExistente = tarjeta.querySelector('.badge-estado-final');
        if (badgeExistente) badgeExistente.remove();
        
        const badge = document.createElement('div');
        badge.className = 'badge-estado-final';
        badge.innerHTML = texto;
        badge.style.cssText = `
            position: absolute;
            top: 15px;
            right: -30px;
            background: ${color};
            color: white;
            padding: 5px 40px;
            font-size: 10px;
            font-weight: 700;
            transform: rotate(45deg);
            letter-spacing: 1px;
            z-index: 10;
        `;
        tarjeta.style.position = 'relative';
        tarjeta.appendChild(badge);
    }
    
    // Mostrar notificación suave (toast)
    function mostrarNotificacion(estado, mensaje = null) {
        let texto = '';
        let color = '';
        
        switch(estado) {
            case 'atendida':
                texto = mensaje || '✓ Cita marcada como atendida';
                color = '#10b981';
                break;
            case 'no_asistio':
                texto = mensaje || '✕ Cita marcada como no asistida';
                color = '#d97706';
                break;
            case 'cancelada':
                texto = mensaje || '✕ Cita cancelada correctamente';
                color = '#ef4444';
                break;
            case 'error':
                texto = mensaje || '❌ Error al procesar';
                color = '#ef4444';
                break;
        }
        
        const toast = document.createElement('div');
        toast.innerHTML = texto;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${color};
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // ====================================================
    // FUNCIONES PARA EL MODAL DE CANCELACIÓN
    // ====================================================
    
    function abrirModalCancelar(citaId) {
        citaIdActual = citaId;
        const modal = document.getElementById('modalCancelar');
        const form = document.getElementById('formCancelar');
        
        form.action = `/citas/${citaId}`;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        const motivoInput = document.getElementById('motivo');
        if (motivoInput) motivoInput.focus();
    }
    
    function closeModal() {
        const modal = document.getElementById('modalCancelar');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        
        const motivoInput = document.getElementById('motivo');
        const reprogramarCheck = document.getElementById('reprogramar');
        
        if (motivoInput) motivoInput.value = '';
        if (reprogramarCheck) reprogramarCheck.checked = false;
        citaIdActual = null;
    }
    
    // ====================================================
    // INICIALIZACIÓN
    // ====================================================
    
    function init() {
        // Inicializar listado de citas
        document.querySelectorAll('.cita-card').forEach(card => {
            const id = card.getAttribute('data-id');
            if (!id) {
                const btn = card.querySelector('[onclick*="cambiarEstado"]');
                if (btn) {
                    const match = btn.getAttribute('onclick').match(/\d+/);
                    if (match) {
                        card.setAttribute('data-id', match[0]);
                    }
                }
            }
        });
        
        agregarAnimacionesCSS();
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
        
        const modal = document.getElementById('modalCancelar');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }
        
        const cancelForm = document.getElementById('formCancelar');
        if (cancelForm) {
            cancelForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const reprogramar = document.getElementById('reprogramar').checked;
                const form = this;
                const formData = new FormData(form);
                const token = document.querySelector('meta[name="csrf-token"]').content;
                
                const btnSubmitModal = form.querySelector('.btn-confirm-cancel');
                const textoOriginal = btnSubmitModal.innerHTML;
                btnSubmitModal.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
                btnSubmitModal.disabled = true;
                
                try {
                    if (reprogramar) {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        if (response.ok) {
                            window.location.href = `/citas/create?reprogramar=${citaIdActual}`;
                        }
                    } else {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            actualizarTarjeta(citaIdActual, 'cancelada');
                            closeModal();
                            mostrarNotificacion('cancelada', '✕ Cita cancelada correctamente');
                            
                            const tarjeta = document.querySelector(`.cita-card[data-id="${citaIdActual}"]`);
                            if (tarjeta) {
                                const acciones = tarjeta.querySelector('.cita-actions');
                                if (acciones) acciones.remove();
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarNotificacion('error', 'Error al cancelar la cita');
                } finally {
                    btnSubmitModal.innerHTML = textoOriginal;
                    btnSubmitModal.disabled = false;
                }
            });
        }
    }
    
    function agregarAnimacionesCSS() {
        if (!document.querySelector('#citas-animations')) {
            const style = document.createElement('style');
            style.id = 'citas-animations';
            style.textContent = `
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOutRight {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(30px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .cita-card { transition: all 0.3s ease; }
                .cita-card.atendida,
                .cita-card.cancelada,
                .cita-card.no-asistio { animation: slideUp 0.3s ease; }
                .btn-asistio:disabled,
                .btn-no-asistio:disabled,
                .btn-cancelar:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    return {
        init: init,
        cambiarEstado: cambiarEstado,
        abrirModalCancelar: abrirModalCancelar,
        closeModal: closeModal
    };
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    CitasManager.init();
    window.cambiarEstado = CitasManager.cambiarEstado;
    window.abrirModalCancelar = CitasManager.abrirModalCancelar;
    window.closeModal = CitasManager.closeModal;
});