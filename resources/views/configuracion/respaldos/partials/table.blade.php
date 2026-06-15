<div class="card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-history"></i>
            Historial de Respaldos
        </h3>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Archivo</th>
                    <th>Tipo</th>
                    <th>Tamaño</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                <tr>
                    <td>
                        <i class="fas fa-file-archive me-2" style="color: #11998e;"></i>
                        {{ $backup->nombre }}
                    </td>
                    <td>
                        <span class="badge-tipo {{ $backup->tipo }}">
                            <i class="fas {{ $backup->tipo == 'automatico' ? 'fa-robot' : ($backup->tipo == 'semanal' ? 'fa-calendar-week' : 'fa-user') }} me-1"></i>
                            {{ ucfirst($backup->tipo) }}
                        </span>
                    </td>
                    <td>{{ $backup->tamaño }}</div>
                    <td>
                        <i class="far fa-calendar-alt me-2"></i>
                        {{ $backup->fecha }}
                    </div>
                    <td>
                        <div class="actions">
                            <button class="btn-icon restore btn-restore" data-filename="{{ $backup->nombre }}" title="Restaurar">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                            <button class="btn-icon download btn-download" data-filename="{{ $backup->nombre }}" title="Descargar">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="btn-icon delete btn-delete" data-filename="{{ $backup->nombre }}" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 50px;">
                        <i class="fas fa-database" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                        <span style="color: #64748b;">No hay respaldos creados</span>
                        <p style="color: #94a3b8; font-size: 13px;">Haz clic en "Crear Respaldo" para generar tu primera copia de seguridad</p>
                    </div>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>