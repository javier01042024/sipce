// ==========================================
// MANEJO DE MODALES PARA PACIENTES
// ==========================================

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

// ==========================================
// MOSTRAR/OCULTAR CAMPOS DE USUARIO
// ==========================================

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

// ==========================================
// VALIDACIÓN DE CÉDULA
// ==========================================

// Validar cédula (solo números y longitud específica)
function validarCedula(input) {
    input.value = input.value.replace(/[^0-9]/g, '').slice(0, 8);
}

// ==========================================
// ACTUALIZAR CÉDULA COMPLETA (NACIONALIDAD + NÚMERO)
// ==========================================

function actualizarCedulaCompleta() {
    const nacionalidad = document.getElementById('nacionalidad_select');
    const numero = document.getElementById('cedula_numero');
    const cedulaCompleta = document.getElementById('cedula_completa');
    
    if (nacionalidad && numero && cedulaCompleta) {
        cedulaCompleta.value = nacionalidad.value + numero.value;
    }
}

// ==========================================
// NOTIFICACIÓN TOAST
// ==========================================

function showToast(message, type = 'success') {
    if (type === 'success') {
        SIPCE_ALERT.success(message);
    } else {
        SIPCE_ALERT.error(message);
    }
}

// ==========================================
// INICIALIZAR EVENTOS CUANDO EL DOM ESTÉ LISTO
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // CONFIRMACIÓN ANTES DE CANCELAR (FORMULARIO DE EDICIÓN)
    // ==========================================
    
    document.querySelectorAll('.btn-cancel').forEach(function(btnCancel) {
        btnCancel.addEventListener('click', function(e) {
            const formModified = document.querySelector('.form-grid');
            // Si hay cambios en el formulario, preguntar antes de salir
            if (formModified && formModified.querySelector('input:focus, textarea:focus')) {
                e.preventDefault();
                const href = this.getAttribute('href');
                SIPCE_ALERT.confirm({
                    title: '¿Cancelar cambios?',
                    text: 'Los cambios no guardados se perderán.',
                    confirmText: 'Sí, salir',
                    cancelText: 'Continuar editando',
                    confirmColor: SIPCE_ALERT.colors.error
                }).then((result) => {
                    if (result.isConfirmed && href) {
                        window.location.href = href;
                    }
                });
            }
        });
    });

    // ==========================================
    // INICIALIZAR CÉDULA COMPLETA (NACIONALIDAD + NÚMERO)
    // ==========================================
    
    const nacionalidadSelect = document.getElementById('nacionalidad_select');
    const cedulaNumero = document.getElementById('cedula_numero');
    
    if (nacionalidadSelect && cedulaNumero) {
        nacionalidadSelect.addEventListener('change', actualizarCedulaCompleta);
        cedulaNumero.addEventListener('input', actualizarCedulaCompleta);
        
        // Inicializar el valor
        actualizarCedulaCompleta();
    }
    
    // Validación adicional: permitir solo números en el campo de cédula
    if (cedulaNumero) {
        cedulaNumero.addEventListener('keypress', function(e) {
            // Permitir solo números
            if (e.key < '0' || e.key > '9') {
                e.preventDefault();
            }
            
            // Limitar a 8 dígitos
            if (this.value.length >= 8) {
                e.preventDefault();
            }
        });
    }

    // ==========================================
    // INICIALIZAR DATATABLE CON EXPORTACIÓN
    // ==========================================

    // Extraer texto limpio de cada celda para exportaciones
    function textoCeldaExportacion(data, row, column, node) {
        const $node = $(node);
        const nombre = $node.find('.nombre-text');
        if (nombre.length) return nombre.first().text().trim();

        const expediente = $node.find('.expediente-number');
        if (expediente.length) return expediente.first().text().trim();

        return $node.text().replace(/\s+/g, ' ').trim();
    }

    const fechaGeneracion = new Date().toLocaleDateString('es-VE', {
        day: '2-digit', month: 'long', year: 'numeric'
    });

    const exportOptions = {
        columns: [1, 2, 3, 4, 5, 6, 7],
        format: { body: textoCeldaExportacion }
    };

const pacientesTable = document.getElementById('pacientesTable');
if (pacientesTable && typeof $.fn.DataTable !== 'undefined') {
    const table = $(pacientesTable).DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7, 8] }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Excel',
                className: 'd-none',
                filename: function() { return 'listado_pacientes_' + new Date().toISOString().slice(0, 10); },
                title: 'Listado de Pacientes',
                messageTop: 'SIPCE - Sistema Integral de Psicología Clínica\nFecha de generación: ' + fechaGeneracion,
                exportOptions: exportOptions,
                customize: function(xlsx) {
                    const wb = xlsx.getWorkbook ? xlsx.getWorkbook() : null;
                    const ws = wb && wb.Sheets ? wb.Sheets[wb.SheetNames[0]] : null;
                    if (ws) {
                        ws['!cols'] = [
                            { wch: 22 }, { wch: 34 }, { wch: 14 },
                            { wch: 14 }, { wch: 14 }, { wch: 16 }, { wch: 14 }
                        ];
                    }
                }
            },
            {
                extend: 'pdf',
                text: 'PDF',
                className: 'd-none',
                filename: function() { return 'listado_pacientes_' + new Date().toISOString().slice(0, 10); },
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: exportOptions,
                customize: function(doc) {
                    doc.pageMargins = [28, 55, 28, 45];
                    doc.pageOrientation = 'landscape';
                    doc.defaultStyle = { fontSize: 9, color: '#334155' };

                    doc.header = function(currentPage) {
                        return {
                            margin: [28, 14, 28, 0],
                            columns: [
                                { text: 'SIPCE', alignment: 'left', fontSize: 11, bold: true, color: '#667eea' },
                                { text: 'Sistema Integral de Psicología Clínica', alignment: 'right', fontSize: 9, color: '#94a3b8' }
                            ]
                        };
                    };

                    doc.footer = function(currentPage, pageCount) {
                        return {
                            margin: [28, 10, 28, 0],
                            columns: [
                                { text: 'Generado el ' + fechaGeneracion, alignment: 'left', fontSize: 8, color: '#94a3b8' },
                                { text: 'Página ' + currentPage + ' de ' + pageCount, alignment: 'right', fontSize: 8, color: '#94a3b8' }
                            ]
                        };
                    };

                    // Localizar la tabla generada por DataTables
                    let tabla = null;
                    for (let i = 0; i < doc.content.length; i++) {
                        if (doc.content[i].table) { tabla = doc.content[i]; break; }
                    }

                    if (tabla) {
                        const filas = tabla.table.body;
                        const totalPacientes = filas.length > 0 ? filas.length - 1 : 0;

                        // Cabecera de tabla en blanco
                        if (filas.length > 0) {
                            filas[0] = filas[0].map(function(celda) {
                                return { text: celda, style: 'celdaTitulo' };
                            });
                        }

                        doc.content = [
                            { text: 'Listado de Pacientes', style: 'tituloDocumento' },
                            {
                                text: fechaGeneracion,
                                style: 'subtituloDocumento'
                            },
                            {
                                text: 'Total de pacientes registrados: ' + totalPacientes,
                                style: 'totalDocumento'
                            },
                            {
                                table: {
                                    headerRows: 1,
                                    widths: ['18%', '26%', '13%', '12%', '12%', '13%', '12%'],
                                    body: filas,
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return i === 0 ? 2 : (i === node.table.body.length ? 1.5 : 0.4);
                                        },
                                        vLineWidth: function() { return 0.4; },
                                        hLineColor: function() { return '#cbd5e1'; },
                                        vLineColor: function() { return '#e2e8f0'; },
                                        fillColor: function(rowIndex, node, columnIndex) {
                                            if (rowIndex === 0) return '#667eea';
                                            if (rowIndex % 2 === 0) return '#f8fafc';
                                            return null;
                                        },
                                        paddingLeft: function() { return 6; },
                                        paddingRight: function() { return 6; },
                                        paddingTop: function() { return 6; },
                                        paddingBottom: function() { return 6; }
                                    }
                                }
                            }
                        ];

                        doc.styles = {
                            tituloDocumento: {
                                fontSize: 20, bold: true, color: '#1e293b', margin: [0, 0, 0, 2]
                            },
                            subtituloDocumento: {
                                fontSize: 10, color: '#64748b', margin: [0, 0, 0, 4]
                            },
                            totalDocumento: {
                                fontSize: 10, color: '#667eea', bold: true, margin: [0, 0, 0, 14]
                            },
                            celdaTitulo: {
                                color: '#ffffff', bold: true, fontSize: 10, alignment: 'center'
                            },
                            espacioTabla: { margin: [0, 12, 0, 0] }
                        };
                    }
                }
            },
            {
                extend: 'print',
                text: 'Imprimir',
                className: 'd-none',
                title: '',
                autoPrint: true,
                exportOptions: exportOptions,
                customize: function(win) {
                    const $body = $(win.document.body);

                    $body.find('h1').remove();

                    $body.prepend(
                        '<div class="print-encabezado">' +
                            '<div class="print-titulo">SIPCE</div>' +
                            '<div class="print-subtitulo">Sistema Integral de Psicología Clínica</div>' +
                            '<h2 class="print-documento">Listado de Pacientes</h2>' +
                            '<div class="print-fecha">Fecha de generación: ' + fechaGeneracion + '</div>' +
                        '</div>'
                    );

                    $('<style>' +
                        'body { font-family: "Segoe UI", Arial, sans-serif; color: #1e293b; }' +
                        '.print-encabezado { text-align: center; border-bottom: 3px solid #667eea; padding-bottom: 12px; margin-bottom: 20px; }' +
                        '.print-titulo { font-size: 22px; font-weight: 800; color: #667eea; letter-spacing: 2px; }' +
                        '.print-subtitulo { font-size: 12px; color: #64748b; margin-bottom: 6px; }' +
                        '.print-documento { font-size: 16px; font-weight: 700; color: #1e293b; margin: 6px 0 2px; }' +
                        '.print-fecha { font-size: 11px; color: #64748b; }' +
                        'table.dataTable { width: 100% !important; border-collapse: collapse; font-size: 11px; }' +
                        'table.dataTable thead th { background: #667eea !important; color: #ffffff !important; font-weight: 700; padding: 8px 6px; border: 1px solid #4f46e5; text-align: center; }' +
                        'table.dataTable tbody td { padding: 6px; border: 1px solid #e2e8f0; }' +
                        'table.dataTable tbody tr:nth-child(even) { background: #f8fafc; }' +
                        '.dataTables_filter, .dataTables_paginate, .dt-buttons { display: none; }' +
                        '</style>'
                    ).appendTo($body);
                }
            },
            {
                extend: 'copy',
                text: 'Copiar',
                className: 'd-none',
                exportOptions: exportOptions
            }
        ]
    });
    
    // Ocultar los botones de DataTables (usamos los nuestros)
    table.buttons().container().appendTo('body').hide();
    
    // Seleccionar todos
    $('#selectAll').on('click', function() {
        const isChecked = $(this).prop('checked');
        $('.selectRow').prop('checked', isChecked);
    });
    
    // Exportar a Excel
    $('#exportExcelBtn').on('click', function() {
        table.button('.buttons-excel').trigger();
        showToast('Exportando a Excel...', 'success');
    });
    
    // Exportar a PDF
    $('#exportPdfBtn').on('click', function() {
        table.button('.buttons-pdf').trigger();
        showToast('Exportando a PDF...', 'success');
    });
    
    // Imprimir
    $('#printBtn').on('click', function() {
        table.button('.buttons-print').trigger();
    });
    
    // Copiar
    $('#copyBtn').on('click', function() {
        table.button('.buttons-copy').trigger();
        showToast('Datos copiados al portapapeles', 'success');
    });
    
    // Función para exportar datos personalizados
    function exportarDatos(filas, nombreArchivo) {
        if (typeof XLSX === 'undefined') {
            SIPCE_ALERT.error('Librería SheetJS no cargada');
            return;
        }
        
        const data = [];
        data.push(['Expediente', 'Nombre Completo', 'Teléfono', 'Email', 'Prioridad', 'Fecha Registro']);
        
        filas.each(function() {
            const row = $(this);
            data.push([
                row.find('td:eq(1)').text().trim(),
                row.find('td:eq(2)').text().trim().replace(/Acceso/g, '').trim(),
                row.find('td:eq(3)').text().trim(),
                row.find('td:eq(4)').text().trim(),
                row.find('td:eq(5)').text().trim(),
                row.find('td:eq(6)').text().trim()
            ]);
        });
        
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Pacientes');
        XLSX.writeFile(wb, `${nombreArchivo}.xlsx`);
    }
    
    // Exportar todos
    $('#exportTodosBtn').on('click', function() {
        const todasFilas = $('#pacientesTable tbody tr');
        exportarDatos(todasFilas, 'pacientes_todos');
        showToast('Todos los pacientes exportados', 'success');
    });
    
    // Exportar seleccionados
    $('#exportSeleccionadosBtn').on('click', function() {
        const seleccionados = $('.selectRow:checked');
        const filasSeleccionadas = [];
        
        seleccionados.each(function() {
            const id = $(this).val();
            const row = $(`#row-${id}`);
            if (row.length) filasSeleccionadas.push(row);
        });
        
        if (filasSeleccionadas.length === 0) {
            SIPCE_ALERT.warning('Debe seleccionar al menos un paciente para exportar', 'Seleccione pacientes');
            return;
        }
        
        exportarDatos($(filasSeleccionadas), 'pacientes_seleccionados');
        showToast(`${filasSeleccionadas.length} pacientes exportados`, 'success');
    });
    
    // Exportar por rango de fechas
    $('#exportPorFechaBtn').on('click', function() {
        const desde = $('#exportFechaDesde').val();
        const hasta = $('#exportFechaHasta').val();
        
        if (!desde || !hasta) {
            SIPCE_ALERT.warning('Debe seleccionar una fecha de inicio y fin', 'Seleccione rango');
            return;
        }
        
        const filasFiltradas = [];
        $('#pacientesTable tbody tr').each(function() {
            const fechaTexto = $(this).find('td:eq(6)').text().trim();
            const [dia, mes, anio] = fechaTexto.split('/');
            const fechaFila = `${anio}-${mes}-${dia}`;
            
            if (fechaFila >= desde && fechaFila <= hasta) {
                filasFiltradas.push($(this));
            }
        });
        
        if (filasFiltradas.length === 0) {
            SIPCE_ALERT.info('No hay pacientes en el rango de fechas seleccionado', 'Sin resultados');
            return;
        }
        
        exportarDatos($(filasFiltradas), `pacientes_${desde}_a_${hasta}`);
        showToast(`${filasFiltradas.length} pacientes exportados`, 'success');
    });
}
// ==========================================
// AGREGAR ANIMACIONES CSS DINÁMICAMENTE
// ==========================================

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
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .modal-paciente.show {
        animation: fadeIn 0.3s ease;
    }
`;
document.head.appendChild(style);
});