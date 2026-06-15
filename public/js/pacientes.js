// Manejo de modales para pacientes
let formToDeletePaciente = null;

function openModalPaciente() {
    const modal = document.getElementById('modalPaciente');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModalPaciente() {
    const modal = document.getElementById('modalPaciente');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    // Limpiar checkbox y campos de usuario al cerrar
    const crearUsuario = document.getElementById('crear_usuario');
    if (crearUsuario) crearUsuario.checked = false;
    const userFields = document.getElementById('userFields');
    if (userFields) userFields.classList.remove('show');
}

function openDeleteModalPaciente(button) {
    formToDeletePaciente = button.closest('form');
    const modal = document.getElementById('deleteModalPaciente');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModalPaciente() {
    formToDeletePaciente = null;
    const modal = document.getElementById('deleteModalPaciente');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Mostrar/ocultar campos de usuario
function toggleUserFields() {
    const checkbox = document.getElementById('crear_usuario');
    const userFields = document.getElementById('userFields');
    
    if (checkbox.checked) {
        userFields.classList.add('show');
        // Hacer campos requeridos
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
    } else {
        userFields.classList.remove('show');
        // Quitar required
        document.getElementById('password').required = false;
        document.getElementById('password_confirmation').required = false;
        // Limpiar valores
        document.getElementById('password').value = '';
        document.getElementById('password_confirmation').value = '';
    }
}

// Validar cédula (solo números y longitud específica)
function validarCedula(input) {
    input.value = input.value.replace(/[^0-9]/g, '').slice(0, 8);
}

// Mostrar notificación toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: ${type === 'success' ? '#11998e' : '#ef4444'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 500;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Inicializar eventos
document.addEventListener('DOMContentLoaded', function() {
    // Confirmar eliminación con SweetAlert2
    const confirmBtn = document.getElementById('confirmDeleteBtnPaciente');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (!formToDeletePaciente) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo identificar el paciente a eliminar',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }
            
            // Obtener el nombre del paciente del formulario
            const pacienteRow = formToDeletePaciente.closest('tr');
            let pacienteNombre = 'este paciente';
            if (pacienteRow) {
                const nombreCell = pacienteRow.querySelector('td:nth-child(2)');
                if (nombreCell) {
                    const nombreText = nombreCell.textContent.trim();
                    if (nombreText) pacienteNombre = nombreText;
                }
            }
            
            Swal.fire({
                title: '¿Eliminar paciente?',
                html: `Estás a punto de eliminar a <strong>${pacienteNombre}</strong><br><br>Esta acción no se puede deshacer. Se eliminarán todos los datos asociados (citas, historial, etc.).`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Enviar el formulario
                    formToDeletePaciente.submit();
                }
            });
        });
    }

    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModalPaciente();
            closeDeleteModalPaciente();
        }
    });

    // Cerrar modales al hacer clic fuera
    document.querySelectorAll('.modal-paciente').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModalPaciente();
                closeDeleteModalPaciente();
            }
        });
    });
    
    // Mostrar mensajes flash con SweetAlert2 si existen
    const successMessage = document.querySelector('.alert-success');
    if (successMessage) {
        const message = successMessage.textContent.trim();
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: message,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
        successMessage.style.display = 'none';
    }
    
    const errorMessage = document.querySelector('.alert-error');
    if (errorMessage) {
        const message = errorMessage.textContent.trim();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#ef4444'
        });
        errorMessage.style.display = 'none';
    }
});

// Agregar animaciones CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);