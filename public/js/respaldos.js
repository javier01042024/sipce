document.addEventListener('DOMContentLoaded', function() {
    
    // Cargar configuración
    loadConfig();
    
    // Crear respaldo manual
    const btnBackup = document.getElementById('btnBackup');
    if (btnBackup) {
        btnBackup.addEventListener('click', createBackup);
    }
    
    // Guardar configuración
    const configToggles = document.querySelectorAll('.config-toggle');
    configToggles.forEach(toggle => {
        toggle.addEventListener('change', saveConfig);
    });
    
    // Restaurar respaldo
    document.querySelectorAll('.btn-restore').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            restoreBackup(filename);
        });
    });
    
    // Descargar respaldo
    document.querySelectorAll('.btn-download').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            downloadBackup(filename);
        });
    });
    
    // Eliminar respaldo
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            deleteBackup(filename);
        });
    });
});

function loadConfig() {
    fetch('/configuracion/respaldos/config')
        .then(response => response.json())
        .then(config => {
            document.getElementById('auto_diario').checked = config.auto_diario || false;
            document.getElementById('auto_semanal').checked = config.auto_semanal || false;
            document.getElementById('notificar_email').checked = config.notificar_email || false;
            document.getElementById('comprimir').checked = config.comprimir || false;
        })
        .catch(error => console.error('Error:', error));
}

function saveConfig() {
    const config = {
        auto_diario: document.getElementById('auto_diario').checked,
        auto_semanal: document.getElementById('auto_semanal').checked,
        notificar_email: document.getElementById('notificar_email').checked,
        comprimir: document.getElementById('comprimir').checked
    };
    
    fetch('/configuracion/respaldos/config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Configuración guardada',
                text: 'Los cambios se han aplicado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => console.error('Error:', error));
}

function createBackup() {
    Swal.fire({
        title: '¿Crear respaldo?',
        text: 'Se generará una copia de seguridad completa de la base de datos',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#11998e',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, crear respaldo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Creando respaldo...',
                text: 'Por favor espera, esto puede tomar unos segundos',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const btn = document.getElementById('btnBackup');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
            btn.disabled = true;
            
            fetch('/configuracion/respaldos/create', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Respaldo creado',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo crear el respaldo',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al conectar con el servidor: ' + error.message,
                    confirmButtonColor: '#ef4444'
                });
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    });
}

function restoreBackup(filename) {
    Swal.fire({
        title: '¿Restaurar respaldo?',
        html: `Se restaurará el respaldo: <strong>${filename}</strong><br><br>⚠️ Los datos actuales serán reemplazados. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Restaurando...',
                text: 'Por favor espera, restaurando la base de datos',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/configuracion/respaldos/restore/${filename}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Respaldo restaurado',
                        text: 'Los datos han sido restaurados correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo restaurar el respaldo',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al conectar con el servidor',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

function downloadBackup(filename) {
    window.location.href = `/configuracion/respaldos/download/${filename}`;
}

function deleteBackup(filename) {
    Swal.fire({
        title: '¿Eliminar respaldo?',
        html: `Se eliminará permanentemente: <strong>${filename}</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/configuracion/respaldos/delete/${filename}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Eliminado',
                        text: 'El respaldo ha sido eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el respaldo',
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al conectar con el servidor',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}