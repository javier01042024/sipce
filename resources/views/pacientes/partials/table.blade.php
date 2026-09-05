<div class="sipce-table-card">
    <div class="sipce-table-header">
        <div>
            <h3>
                <i class="fas fa-list"></i>
                Listado de Pacientes
            </h3>
            <p>Total: {{ $pacientes->total() }} pacientes registrados</p>
        </div>
        <div class="sipce-table-header-actions">
            <div class="export-buttons">
                <button id="exportExcelBtn" class="btn-export btn-export-excel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button id="exportPdfBtn" class="btn-export btn-export-pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button id="printBtn" class="btn-export btn-export-print">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button id="copyBtn" class="btn-export btn-export-copy">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
        </div>
    </div>

    <div class="export-filters">
        <div class="filters-body">
            <div class="filter-group">
                <label for="exportFechaDesde"><i class="far fa-calendar-alt"></i> Desde</label>
                <input type="date" id="exportFechaDesde" class="filter-input">
            </div>
            <div class="filter-group">
                <label for="exportFechaHasta"><i class="far fa-calendar-alt"></i> Hasta</label>
                <input type="date" id="exportFechaHasta" class="filter-input">
            </div>
            <button id="exportPorFechaBtn" class="btn-filter">
                <i class="fas fa-filter"></i> Filtrar por fecha
            </button>
        </div>
    </div>

    <div class="table-container">
        <table id="pacientesTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th class="col-checkbox"><input type="checkbox" id="selectAll"></th>
                    <th class="col-expediente">Expediente</th>
                    <th class="col-nombre">Paciente</th>
                    <th class="col-tipo">Tipo</th>
                    <th class="col-atencion">Atención</th>
                    <th class="col-prioridad">Prioridad</th>
                    <th class="col-estado">Estado</th>
                    <th class="col-fecha">Registro</th>
                    <th class="col-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pacientes as $paciente)
                <tr id="row-{{ $paciente->id }}" class="paciente-row">
                    <td class="col-checkbox" onclick="event.stopPropagation();">
                        <input type="checkbox" class="selectRow" value="{{ $paciente->id }}">
                    </td>
                    <td class="col-expediente">
                        <a href="{{ route('pacientes.show', $paciente) }}" class="expediente-link">
                            <span class="expediente-number">#{{ $paciente->numero_expediente }}</span>
                        </a>
                    </td>
                    <td class="col-nombre">
                        @php
                            $nombreCompleto = 'Sin nombre';
                            $inicial = 'P';
                            
                            if ($paciente->detalle) {
                                $nombreCompleto = $paciente->detalle->nombre . ' ' . $paciente->detalle->apellido;
                                $inicial = strtoupper(substr($paciente->detalle->nombre, 0, 1));
                            } elseif ($paciente->paciente_detalle_type && $paciente->paciente_detalle_id) {
                                try {
                                    $detalle = app($paciente->paciente_detalle_type)->find($paciente->paciente_detalle_id);
                                    if ($detalle) {
                                        $nombreCompleto = $detalle->nombre . ' ' . $detalle->apellido;
                                        $inicial = strtoupper(substr($detalle->nombre, 0, 1));
                                    }
                                } catch (\Exception $e) {
                                    // Silencioso
                                }
                            }
                        @endphp
                        <a href="{{ route('pacientes.show', $paciente) }}" class="paciente-nombre-link">
                            <div class="paciente-nombre">
                                <div class="paciente-avatar-small">
                                    {{ $inicial }}
                                </div>
                                <div class="paciente-info-cell">
                                    <span class="nombre-text">{{ $nombreCompleto }}</span>
                                    @if($paciente->user)
                                    <span class="badge-acceso" title="Tiene acceso al sistema">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </td>
                    <td class="col-tipo">
                        @if($paciente->tipo_paciente === 'adulto')
                        <span class="badge-tipo badge-tipo-adulto"><i class="fas fa-user-tie"></i> Adulto</span>
                        @elseif($paciente->tipo_paciente === 'adolescente')
                        <span class="badge-tipo badge-tipo-adolescente"><i class="fas fa-user"></i> Adolescente</span>
                        @else
                        <span class="badge-tipo badge-tipo-nino"><i class="fas fa-child"></i> Niño</span>
                        @endif
                    </td>
                    <td class="col-atencion">
                        @if($paciente->tipo_atencion === 'privado')
                        <span class="badge-atencion badge-privado"><i class="fas fa-building"></i> Privado</span>
                        @else
                        <span class="badge-atencion badge-publico"><i class="fas fa-hospital"></i> Público</span>
                        @endif
                    </td>
                    <td class="col-prioridad">
                        @if($paciente->prioridad === 'urgencia')
                        <span class="badge-pri badge-urgencia"><i class="fas fa-exclamation-circle"></i> Urgencia</span>
                        @elseif($paciente->prioridad === 'alta')
                        <span class="badge-pri badge-alta"><i class="fas fa-arrow-up"></i> Alta</span>
                        @elseif($paciente->prioridad === 'media')
                        <span class="badge-pri badge-media"><i class="fas fa-minus"></i> Media</span>
                        @else
                        <span class="badge-pri badge-baja"><i class="fas fa-arrow-down"></i> Baja</span>
                        @endif
                    </td>
                    <td class="col-estado">
                        @if($paciente->estado)
                        <span class="estado-badge">{{ $paciente->estado->tipo }}</span>
                        @else
                        <span class="no-data">-</span>
                        @endif
                    </td>
                    <td class="col-fecha">
                        {{ $paciente->created_at ? $paciente->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td class="col-acciones">
                        <div class="actions">
                            <a href="{{ route('pacientes.edit', $paciente) }}" class="btn-icon btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST" style="display: inline;" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-icon btn-delete" onclick="confirmarEliminarPaciente(this)" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td>
                        <div class="sipce-empty">
                            <i class="fas fa-user-slash sipce-empty-icon"></i>
                            <p class="sipce-empty-title">No hay pacientes registrados</p>
                            <p class="sipce-empty-text">Comienza agregando un nuevo paciente</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
</div>

<script>
function confirmarEliminarPaciente(button) {
    const form = button.closest('form');
    const row = button.closest('tr');
    const nombreElement = row ? row.querySelector('.nombre-text') : null;
    const nombrePaciente = nombreElement ? nombreElement.textContent.trim() : 'Paciente desconocido';
    const expedienteElement = row ? row.querySelector('.expediente-number') : null;
    const expediente = expedienteElement ? expedienteElement.textContent.trim() : '';

    const detalle = 'Estás a punto de eliminar a <strong>' + nombrePaciente + '</strong>' +
        (expediente ? ' <span style="color:var(--sipce-primary);">(' + expediente + ')</span>' : '') +
        '<br><br><small style="color:#64748b;">Esta acción no se puede deshacer. Se eliminarán todos los datos asociados.</small>';

    SIPCE_ALERT.confirmDelete({
        html: detalle
    }).then(function(result) {
        if (result.isConfirmed) {
            SIPCE_ALERT.loading('Eliminando...', 'Por favor espera');
            form.submit();
        }
    });
}

// Hacer que toda la fila sea clickeable (excepto acciones)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.paciente-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // No redirigir si se hizo clic en checkbox, botón o formulario
            if (e.target.closest('.col-checkbox') || 
                e.target.closest('.col-acciones') || 
                e.target.closest('button') || 
                e.target.closest('form') ||
                e.target.closest('input')) {
                return;
            }
            
            // Buscar el enlace del nombre y navegar
            const link = row.querySelector('.paciente-nombre-link');
            if (link) {
                window.location.href = link.getAttribute('href');
            }
        });
        
        // Efecto hover: cursor pointer
        row.style.cursor = 'pointer';
    });
});
</script>

<style>
    /* Enlaces de paciente */
    .paciente-nombre-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .paciente-nombre-link:hover .nombre-text {
        color: var(--sipce-primary);
        text-decoration: underline;
    }

    .paciente-nombre-link:hover .paciente-avatar-small {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(var(--sipce-primary-rgb), 0.4);
    }

    .expediente-link {
        text-decoration: none;
        color: inherit;
    }

    .expediente-link:hover .expediente-number {
        color: var(--sipce-primary);
        text-decoration: underline;
    }

    /* Fila clickeable */
    .paciente-row {
        transition: background-color 0.2s;
    }

    .paciente-row:hover {
        background-color: #f8fafc !important;
    }

    /* Avatar animación */
    .paciente-avatar-small {
        transition: all 0.2s ease;
    }

    .nombre-text {
        transition: color 0.2s;
    }

    .badge-tipo {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: white;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-tipo-adulto { background: var(--sipce-primary); }
    .badge-tipo-adolescente { background: #f59e0b; }
    .badge-tipo-nino { background: #10b981; }

    .badge-atencion {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-privado { background: #f0f9ff; color: #0ea5e9; border: 1px solid #bae6fd; }
    .badge-publico { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }

    .badge-pri {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: white;
        display: inline-block;
        white-space: nowrap;
        line-height: 1;
    }

    .badge-urgencia { background: #ef4444; }
    .badge-alta { background: #f59e0b; }
    .badge-media { background: var(--sipce-primary); }
    .badge-baja { background: #10b981; }

    .estado-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        color: white;
        background: #64748b;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-acceso {
        background: #11998e;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 10px;
        margin-left: 5px;
    }

    .paciente-nombre {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .paciente-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--sipce-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .paciente-info-cell {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .nombre-text {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    .no-data {
        color: #94a3b8;
        font-size: 13px;
    }

    .expediente-number {
        font-weight: 700;
        color: var(--sipce-primary);
        font-size: 13px;
    }
</style>