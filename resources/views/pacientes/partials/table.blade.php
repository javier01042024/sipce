<div class="card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-list"></i>
            Listado de Pacientes
        </h3>
        <div style="display: flex; gap: 10px;">
            <!-- Botones de exportación -->
            <button id="exportExcelBtn" class="btn-export-excel" style="background: #10b981; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button id="exportPdfBtn" class="btn-export-pdf" style="background: #ef4444; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button id="printBtn" class="btn-print" style="background: #4a5568; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-print"></i> Imprimir
            </button>
            <button id="copyBtn" class="btn-copy" style="background: #667eea; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;">
                <i class="fas fa-copy"></i> Copiar
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="pacientesTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th><i class="fas fa-hashtag me-1"></i> Expediente</th>
                    <th><i class="fas fa-user me-1"></i> Nombre completo</th>
                    <th><i class="fas fa-phone me-1"></i> Teléfono</th>
                    <th><i class="fas fa-envelope me-1"></i> Email</th>
                    <th><i class="fas fa-flag me-1"></i> Prioridad</th>
                    <th><i class="fas fa-calendar me-1"></i> Registro</th>
                    <th><i class="fas fa-cog me-1"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pacientes as $paciente)
                <tr id="row-{{ $paciente->id }}">
                    <td><input type="checkbox" class="selectRow" value="{{ $paciente->id }}"></td>
                    <td class="strong">#{{ $paciente->numero_expediente }}</td>
                    <td>
                        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>
                        {{ $paciente->nombre_completo }}
                        @if($paciente->user)
                            <span style="background: #10b98120; color: #059669; font-size: 10px; padding: 2px 8px; border-radius: 20px; margin-left: 8px;">
                                <i class="fas fa-check-circle"></i> Con acceso
                            </span>
                        @endif
                    </td>
                    <td>
                        <i class="fas fa-phone-alt me-2" style="color: #10b981;"></i>
                        {{ $paciente->telefono ?? 'No registrado' }}
                    </td>
                    <td>{{ $paciente->email ?? 'No registrado' }}</td>
                    <td>
                        <span class="badge {{ $paciente->prioridad }}">
                            {{ $paciente->prioridad ?? 'No definida' }}
                        </span>
                    </td>
                    <td>{{ $paciente->created_at ? $paciente->created_at->format('d/m/Y') : 'N/A' }}</td>
                    <td class="actions">
                        <a href="{{ route('pacientes.show', $paciente) }}" class="btn-icon btn-view" title="Ver detalles">
                            <svg viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </a>

                        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn-icon btn-edit" title="Editar">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34" />
                                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2" />
                            </svg>
                        </a>

                        <button type="button" class="btn-icon btn-delete" onclick="openDeleteModalPaciente(this)" title="Eliminar">
                            <svg viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                <line x1="10" y1="11" x2="10" y2="17" />
                                <line x1="14" y1="11" x2="14" y2="17" />
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Selector de exportación por rango de fechas -->
    <div class="export-filters" style="padding: 15px; border-top: 1px solid #e2e8f0; display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <span style="font-weight: 600;">Exportar por fecha:</span>
        <div style="display: flex; gap: 10px; align-items: center;">
            <label>Desde:</label>
            <input type="date" id="exportFechaDesde" class="form-control" style="padding: 5px 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <label>Hasta:</label>
            <input type="date" id="exportFechaHasta" class="form-control" style="padding: 5px 10px; border: 1px solid #e2e8f0; border-radius: 6px;">
        </div>
        <button id="exportPorFechaBtn" class="btn-export-fecha" style="background: #11998e; color: white; border: none; padding: 6px 15px; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-filter"></i> Exportar por rango
        </button>
        <button id="exportSeleccionadosBtn" class="btn-export-seleccionados" style="background: #667eea; color: white; border: none; padding: 6px 15px; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-check-square"></i> Exportar seleccionados
        </button>
        <button id="exportTodosBtn" class="btn-export-todos" style="background: #4a5568; color: white; border: none; padding: 6px 15px; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-database"></i> Exportar todos
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    const table = $('#pacientesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Todos"]],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 7] } // Deshabilitar orden en checkbox y acciones
        ]
    });
    
    // Seleccionar todos los checkboxes
    $('#selectAll').on('click', function() {
        const isChecked = $(this).prop('checked');
        $('.selectRow').prop('checked', isChecked);
    });
    
    // Función para exportar datos
    function exportarDatos(filas, nombreArchivo) {
        const data = [];
        
        // Cabeceras
        data.push(['Expediente', 'Nombre Completo', 'Teléfono', 'Email', 'Prioridad', 'Fecha Registro']);
        
        // Datos
        filas.each(function() {
            const row = $(this);
            data.push([
                row.find('td:eq(1)').text().trim(),
                row.find('td:eq(2)').text().trim().replace(/Con acceso/g, '').trim(),
                row.find('td:eq(3)').text().trim(),
                row.find('td:eq(4)').text().trim(),
                row.find('td:eq(5)').text().trim(),
                row.find('td:eq(6)').text().trim()
            ]);
        });
        
        // Crear hoja de trabajo
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Pacientes');
        
        // Descargar Excel
        XLSX.writeFile(wb, `${nombreArchivo}.xlsx`);
    }
    
    // Exportar todos los datos
    $('#exportTodosBtn').on('click', function() {
        const todasFilas = $('#pacientesTable tbody tr');
        exportarDatos(todasFilas, 'pacientes_todos');
        Swal.fire({
            icon: 'success',
            title: 'Exportado',
            text: 'Todos los pacientes fueron exportados a Excel',
            timer: 2000,
            showConfirmButton: false
        });
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
            Swal.fire({
                icon: 'warning',
                title: 'Seleccione pacientes',
                text: 'Debe seleccionar al menos un paciente para exportar',
                confirmButtonColor: '#f59e0b'
            });
            return;
        }
        
        exportarDatos($(filasSeleccionadas), 'pacientes_seleccionados');
        Swal.fire({
            icon: 'success',
            title: 'Exportado',
            text: `${filasSeleccionadas.length} pacientes exportados`,
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Exportar por rango de fechas
    $('#exportPorFechaBtn').on('click', function() {
        const desde = $('#exportFechaDesde').val();
        const hasta = $('#exportFechaHasta').val();
        
        if (!desde || !hasta) {
            Swal.fire({
                icon: 'warning',
                title: 'Seleccione rango',
                text: 'Debe seleccionar una fecha de inicio y fin',
                confirmButtonColor: '#f59e0b'
            });
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
            Swal.fire({
                icon: 'info',
                title: 'Sin resultados',
                text: 'No hay pacientes en el rango de fechas seleccionado',
                confirmButtonColor: '#667eea'
            });
            return;
        }
        
        exportarDatos($(filasFiltradas), `pacientes_${desde}_a_${hasta}`);
        Swal.fire({
            icon: 'success',
            title: 'Exportado',
            text: `${filasFiltradas.length} pacientes exportados`,
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Exportar a Excel usando DataTables
    $('#exportExcelBtn').on('click', function() {
        table.button('.buttons-excel').trigger();
    });
    
    // Exportar a PDF
    $('#exportPdfBtn').on('click', function() {
        table.button('.buttons-pdf').trigger();
    });
    
    // Imprimir
    $('#printBtn').on('click', function() {
        table.button('.buttons-print').trigger();
    });
    
    // Copiar
    $('#copyBtn').on('click', function() {
        table.button('.buttons-copy').trigger();
        Swal.fire({
            icon: 'success',
            title: 'Copiado',
            text: 'Datos copiados al portapapeles',
            timer: 1500,
            showConfirmButton: false
        });
    });
    
    // Agregar botones de DataTables (ocultos pero funcionales)
    new $.fn.dataTable.Buttons(table, {
        buttons: [
            { extend: 'excel', text: 'Excel', className: 'd-none' },
            { extend: 'pdf', text: 'PDF', className: 'd-none' },
            { extend: 'print', text: 'Imprimir', className: 'd-none' },
            { extend: 'copy', text: 'Copiar', className: 'd-none' }
        ]
    });
    table.buttons().container().appendTo('body').hide();
});
</script>

<!-- Incluir SheetJS para exportar a Excel -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>