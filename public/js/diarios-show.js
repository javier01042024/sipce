// ===== DIARIOS: SHOW =====

document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // ===== ELIMINAR DIARIO =====
    var btnEliminar = document.getElementById('btnEliminarDiario');
    
    if (btnEliminar) {
        btnEliminar.addEventListener('click', function() {
            SIPCE_ALERT.confirmDelete({
                html: 'Se eliminará el registro del <strong>' + diarioFecha + '</strong><br><small style="color: #64748b;">Esta acción no se puede deshacer</small>'
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Crear formulario dinámico para eliminar
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;
                    
                    var csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    var methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    
                    SIPCE_ALERT.loading('Eliminando...', 'Por favor espera');
                    
                    // Enviar formulario
                    form.submit();
                }
            });
        });
    }
});
