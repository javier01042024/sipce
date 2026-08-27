// Módulo de gestión de citas para el LISTADO y MODAL de cancelación
const CitasManager = (function() {
    let citaIdActual = null;
    
    // ====================================================
    // FUNCIONES PARA EL LISTADO DE CITAS
    // ====================================================
    
    // Cambiar estado (asistió / no asistió) con AJAX
    async function cambiarEstado(boton, citaId, estado) {
        const tarjeta = boton.closest('.cita-card');
        if (!tarjeta) return;

        // La asistencia solo puede registrarse el mismo día de la cita
        if (tarjeta.dataset.esHoy !== '1') {
            SIPCE_ALERT.info('La asistencia solo puede registrarse el día de la cita.', 'No disponible');
            return;
        }

        const esAtendida = estado === 'atendida';
        const confirmado = await SIPCE_ALERT.confirm({
            title: esAtendida ? '¿Marcar cita como atendida?' : '¿Marcar cita como no asistida?',
            html: 'La cita se retirará de la lista de pendientes.',
            confirmText: esAtendida ? 'Sí, asistió' : 'Sí, no asistió',
            confirmColor: esAtendida ? '#10b981' : '#d97706',
            icon: esAtendida ? 'success' : 'warning'
        });

        if (!confirmado.isConfirmed) return;

        const token = document.querySelector('meta[name="csrf-token"]').content;
        const url = `/citas/${citaId}`;

        deshabilitarAcciones(tarjeta);

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
                mostrarNotificacion(estado);
                quitarTarjeta(tarjeta);
                if (esAtendida) {
                    setTimeout(function() {
                        SIPCE_ALERT.confirm({
                            title: '¿Crear sesión clínica?',
                            html: '¿Deseas registrar una sesión clínica para esta cita?',
                            confirmText: 'Crear sesión',
                            confirmColor: '#667eea',
                            icon: 'question'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                window.location.href = '/sesiones/create?cita_id=' + citaId;
                            }
                        });
                    }, 500);
                }
            } else {
                const data = await response.json().catch(() => null);
                SIPCE_ALERT.error(data && data.message ? data.message : 'Error al actualizar la cita');
                habilitarAcciones(tarjeta);
            }
        } catch (error) {
            console.error('Error:', error);
            SIPCE_ALERT.error('Hubo un error al procesar la solicitud.');
            habilitarAcciones(tarjeta);
        }
    }

    function deshabilitarAcciones(tarjeta) {
        tarjeta.querySelectorAll('button').forEach(btn => btn.disabled = true);
    }

    function habilitarAcciones(tarjeta) {
        tarjeta.querySelectorAll('button').forEach(btn => btn.disabled = false);
    }

    // Quitar la tarjeta de la vista con animación (sin recargar)
    function quitarTarjeta(tarjeta) {
        tarjeta.style.transition = 'all 0.4s ease';
        tarjeta.style.opacity = '0';
        tarjeta.style.transform = 'translateY(-20px) scale(0.95)';
        tarjeta.style.maxHeight = '0';
        tarjeta.style.padding = '0 22px';
        tarjeta.style.overflow = 'hidden';

        setTimeout(() => {
            tarjeta.remove();
            actualizarSecciones();
        }, 400);
    }

    // Actualizar contadores de las secciones y el estado vacío
    function actualizarSecciones() {
        let algunaVisible = false;

        document.querySelectorAll('.citas-section').forEach(section => {
            const tarjetas = section.querySelectorAll('.cita-card');
            const contador = section.querySelector('.section-count');

            if (contador) contador.textContent = tarjetas.length;

            if (tarjetas.length === 0) {
                section.style.display = 'none';
            } else {
                section.style.display = '';
                algunaVisible = true;
            }
        });

        const estadoVacio = document.getElementById('estadoVacio');
        if (estadoVacio) {
            estadoVacio.style.display = algunaVisible ? 'none' : '';
        }
    }
    
    // Notificación con el estándar unificado de alertas
    function mostrarNotificacion(estado, mensaje = null) {
        switch(estado) {
            case 'atendida':
                SIPCE_ALERT.success(mensaje || 'Cita marcada como atendida');
                break;
            case 'no_asistio':
                SIPCE_ALERT.success(mensaje || 'Cita marcada como no asistida');
                break;
            case 'cancelada':
                SIPCE_ALERT.success(mensaje || 'Cita cancelada correctamente');
                break;
            case 'error':
                SIPCE_ALERT.error(mensaje || 'Error al procesar');
                break;
        }
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
                            const tarjeta = document.querySelector(`.cita-card[data-id="${citaIdActual}"]`);
                            if (tarjeta) quitarTarjeta(tarjeta);
                            closeModal();
                            mostrarNotificacion('cancelada', 'Cita cancelada correctamente');
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
                .cita-card { transition: all 0.3s ease; }
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