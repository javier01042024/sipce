document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Inicializando respaldos');
    
    // Cargar configuración
    loadConfig();
    
    // Crear respaldo manual
    const btnBackup = document.getElementById('btnBackup');
    if (btnBackup) {
        console.log('Botón "Crear Respaldo" encontrado');
        btnBackup.addEventListener('click', createBackup);
    } else {
        console.log('Botón "Crear Respaldo" NO encontrado');
    }
    
    // Importar SQL
    const btnImportSql = document.getElementById('btnImportSql');
    if (btnImportSql) {
        console.log('Botón "Importar SQL" encontrado');
        btnImportSql.addEventListener('click', importSql);
    } else {
        console.log('Botón "Importar SQL" NO encontrado');
    }
    
    // Guardar configuración
    const configToggles = document.querySelectorAll('.config-toggle');
    console.log('Toggles de configuración encontrados:', configToggles.length);
    configToggles.forEach(toggle => {
        toggle.addEventListener('change', saveConfig);
    });
    
    // Restaurar respaldo
    document.querySelectorAll('.btn-restore').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            console.log('Restaurar respaldo:', filename);
            restoreBackup(filename);
        });
    });
    
    // Descargar respaldo
    document.querySelectorAll('.btn-download').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            console.log('Descargar respaldo:', filename);
            downloadBackup(filename);
        });
    });
    
    // Eliminar respaldo
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const filename = this.getAttribute('data-filename');
            console.log('Eliminar respaldo:', filename);
            deleteBackup(filename);
        });
    });
});

function loadConfig() {
    console.log('Cargando configuración...');
    fetch('/configuracion/respaldos/config')
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(config => {
            console.log('Configuración cargada:', config);
            document.getElementById('auto_diario').checked = config.auto_diario || false;
            document.getElementById('auto_semanal').checked = config.auto_semanal || false;
            document.getElementById('notificar_email').checked = config.notificar_email || false;
            document.getElementById('comprimir').checked = config.comprimir || false;
        })
        .catch(error => {
            console.error('Error al cargar configuración:', error);
            SIPCE_ALERT.warning('Los valores por defecto se aplicarán automáticamente', 'No se pudo cargar la configuración');
        });
}

function saveConfig() {
    console.log('Guardando configuración...');
    const config = {
        auto_diario: document.getElementById('auto_diario').checked,
        auto_semanal: document.getElementById('auto_semanal').checked,
        notificar_email: document.getElementById('notificar_email').checked,
        comprimir: document.getElementById('comprimir').checked
    };
    
    console.log('Configuración a guardar:', config);
    
    fetch('/configuracion/respaldos/config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(config)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Configuración guardada correctamente');
            SIPCE_ALERT.success('Los cambios se han aplicado correctamente', 'Configuración guardada');
        } else {
            throw new Error(data.message || 'Error al guardar la configuración');
        }
    })
    .catch(error => {
        console.error('Error al guardar configuración:', error);
        SIPCE_ALERT.error(error.message || 'No se pudo guardar la configuración. Verifica tu conexión.', 'Error al guardar');
    });
}

function createBackup() {
    console.log('Iniciando creación de respaldo...');
    
    SIPCE_ALERT.confirm({
        title: '¿Crear respaldo?',
        text: 'Se generará una copia de seguridad completa de la base de datos',
        confirmText: 'Sí, crear respaldo',
        cancelText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Usuario confirmó creación de respaldo');
            SIPCE_ALERT.loading('Creando respaldo...', 'Por favor espera, esto puede tomar unos segundos');
            
            const btn = document.getElementById('btnBackup');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
            btn.disabled = true;
            
            fetch('/configuracion/respaldos/create', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ tipo: 'manual' })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'HTTP ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Respaldo creado exitosamente');
                    SIPCE_ALERT.success(data.message, 'Respaldo creado');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    throw new Error(data.message || 'No se pudo crear el respaldo');
                }
            })
            .catch(error => {
                console.error('Error al crear respaldo:', error);
                SIPCE_ALERT.error(error.message || 'No se pudo conectar con el servidor. Verifica tu conexión.', 'Error al crear respaldo');
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    });
}

function importSql() {
    console.log('Iniciando importación de SQL...');
    
    Swal.fire({
        title: 'Importar archivo SQL',
        html: `
            <div style="text-align: left;">
                <p><strong>Selecciona un archivo SQL o ZIP</strong></p>
                <p style="font-size: 13px; color: #64748b;">Archivos permitidos: .sql, .zip (máx. 50MB)</p>
                <div style="margin: 15px 0;">
                    <input type="file" id="sqlFileInput" accept=".sql,.zip" style="width: 100%; padding: 10px; border: 2px dashed #e2e8f0; border-radius: 8px;">
                </div>
                <p style="font-size: 12px; color: #94a3b8;">⚠️ Los datos existentes serán reemplazados si existen tablas con el mismo nombre.</p>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: SIPCE_ALERT.colors.info,
        cancelButtonColor: SIPCE_ALERT.colors.cancel,
        confirmButtonText: 'Importar',
        cancelButtonText: 'Cancelar',
        width: 550,
        preConfirm: () => {
            const fileInput = document.getElementById('sqlFileInput');
            const file = fileInput.files[0];
            
            console.log('preConfirm - Archivo seleccionado:', file ? file.name : 'ninguno');
            
            if (!file) {
                Swal.showValidationMessage('Debes seleccionar un archivo');
                return false;
            }
            
            const validExtensions = ['sql', 'zip'];
            const extension = file.name.split('.').pop().toLowerCase();
            if (!validExtensions.includes(extension)) {
                Swal.showValidationMessage('El archivo debe ser .sql o .zip');
                return false;
            }
            
            if (file.size > 50 * 1024 * 1024) {
                Swal.showValidationMessage('El archivo no debe superar los 50MB');
                return false;
            }
            
            console.log('Archivo válido:', file.name, 'Tamaño:', file.size);
            return file;
        }
    }).then((result) => {
        console.log('Resultado del modal:', result);
        
        if (result.isConfirmed && result.value) {
            const file = result.value;
            const formData = new FormData();
            formData.append('sql_file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            console.log('Enviando archivo:', file.name);
            console.log('Token CSRF:', document.querySelector('meta[name="csrf-token"]').content);
            
            SIPCE_ALERT.loading('Importando archivo...', 'Por favor espera, esto puede tomar varios minutos');
            
            fetch('/configuracion/respaldos/import', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Respuesta del servidor - Status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'HTTP ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    let mensaje = data.message || 'Archivo importado correctamente';
                    
                    if (data.tables_creadas && data.tables_creadas.length > 0) {
                        mensaje += `<br><br><span style="font-size: 13px;">📊 Tablas creadas: ${data.tables_creadas.join(', ')}</span>`;
                    }
                    
                    SIPCE_ALERT.success(mensaje, '¡Importación completada!', { timer: 4000 });
                    
                    setTimeout(() => location.reload(), 3000);
                } else {
                    throw new Error(data.message || 'Error al importar el archivo');
                }
            })
            .catch(error => {
                console.error('Error detallado:', error);
                SIPCE_ALERT.error(error.message || 'No se pudo conectar con el servidor. Verifica tu conexión.', 'Error al importar');
            });
        } else {
            console.log('Importación cancelada por el usuario');
        }
    });
}

function restoreBackup(filename) {
    console.log('Iniciando restauración de respaldo:', filename);
    
    SIPCE_ALERT.confirm({
        title: '¿Restaurar respaldo?',
        html: `
            <div style="text-align: left;">
                <p><strong>Archivo:</strong> ${filename}</p>
                <p><strong>⚠️ Advertencia:</strong> Los datos actuales serán reemplazados.</p>
                <p><strong>⏱️ Tiempo estimado:</strong> Puede tomar varios minutos dependiendo del tamaño.</p>
                <p style="color: #ef4444; font-weight: bold;">Esta acción NO se puede deshacer.</p>
            </div>
        `,
        confirmText: 'Sí, restaurar',
        cancelText: 'Cancelar',
        confirmColor: SIPCE_ALERT.colors.warning,
        icon: 'warning',
        width: 500
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Usuario confirmó restauración');
            SIPCE_ALERT.loading('Restaurando respaldo...', 'Por favor espera, esto puede tomar varios minutos. No cierres esta ventana mientras se procesa.');
            
            fetch(`/configuracion/respaldos/restore/${filename}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                console.log('Respuesta restauración - Status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'HTTP ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    let mensaje = data.message || 'Los datos han sido restaurados correctamente';
                    
                    if (data.executed !== undefined) {
                        mensaje += `<br><br><span style="font-size: 13px;">✅ ${data.executed} consultas ejecutadas</span>`;
                    }
                    
                    if (data.warnings && data.warnings.length > 0) {
                        mensaje += `<br><br><span style="font-size: 13px; color: #f59e0b;">⚠️ ${data.warnings.length} advertencias</span>`;
                    }
                    
                    SIPCE_ALERT.success(mensaje, '¡Restauración completada!', { timer: 4000 });
                    
                    setTimeout(() => location.reload(), 3000);
                } else {
                    throw new Error(data.message || 'Error al restaurar el respaldo');
                }
            })
            .catch(error => {
                console.error('Error al restaurar:', error);
                SIPCE_ALERT.error(error.message || 'No se pudo conectar con el servidor. Verifica tu conexión.', 'Error al restaurar');
            });
        }
    });
}

function downloadBackup(filename) {
    console.log('Descargando respaldo:', filename);
    window.location.href = `/configuracion/respaldos/download/${filename}`;
}

function deleteBackup(filename) {
    console.log('Eliminando respaldo:', filename);
    
    SIPCE_ALERT.confirmDelete({
        html: 'Se eliminará permanentemente: <strong>' + filename + '</strong>'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Usuario confirmó eliminación');
            
            fetch(`/configuracion/respaldos/delete/${filename}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Respuesta eliminación - Status:', response.status);
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'HTTP ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    SIPCE_ALERT.success(data.message || 'El respaldo ha sido eliminado correctamente', 'Eliminado');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Error al eliminar el respaldo');
                }
            })
            .catch(error => {
                console.error('Error al eliminar:', error);
                SIPCE_ALERT.error(error.message || 'No se pudo conectar con el servidor. Verifica tu conexión.', 'Error al eliminar');
            });
        }
    });
}