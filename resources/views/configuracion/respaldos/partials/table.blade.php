<div class="sipce-table-card">
    <div class="sipce-table-header">
        <div>
            <h3>
                <i class="fas fa-history"></i>
                Historial de Respaldos
            </h3>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sipce-table">
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
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-file-archive" style="color: #11998e; font-size: 18px;"></i>
                            <span class="sipce-cell-main">{{ $backup->nombre }}</span>
                        </div>
                    </td>
                    <td>
                        @php
                            $tipoConfig = match($backup->tipo) {
                                'automatico' => ['icon' => 'fa-robot', 'class' => 'sipce-badge-primary'],
                                'semanal' => ['icon' => 'fa-calendar-week', 'class' => 'sipce-badge-info'],
                                default => ['icon' => 'fa-user', 'class' => 'sipce-badge-neutral'],
                            };
                        @endphp
                        <span class="sipce-badge {{ $tipoConfig['class'] }}">
                            <i class="fas {{ $tipoConfig['icon'] }}"></i>
                            {{ ucfirst($backup->tipo) }}
                        </span>
                    </td>
                    <td><span class="sipce-cell-main">{{ $backup->tamaño }}</span></td>
                    <td>
                        <span class="sipce-cell-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $backup->fecha }}
                        </span>
                    </td>
                    <td>
                        <div class="sipce-actions">
                            <button class="sipce-btn-icon sipce-btn-restore btn-restore" data-filename="{{ $backup->nombre }}" title="Restaurar respaldo">
                                <i class="fas fa-undo-alt"></i>
                            </button>
                            <button class="sipce-btn-icon sipce-btn-download btn-download" data-filename="{{ $backup->nombre }}" title="Descargar respaldo">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="sipce-btn-icon sipce-btn-delete btn-delete" data-filename="{{ $backup->nombre }}" title="Eliminar respaldo">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="sipce-empty">
                            <i class="fas fa-database sipce-empty-icon"></i>
                            <p class="sipce-empty-title">No hay respaldos creados</p>
                            <p class="sipce-empty-text">Haz clic en "Crear Respaldo" para generar tu primera copia de seguridad</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
