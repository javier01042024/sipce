<div class="sipce-table-card">
    <div class="sipce-table-header">
        <div>
            <h3>
                <i class="fas fa-tag"></i>
                Listado de Estados
            </h3>
            <p>Total: {{ $estados->count() }} estados registrados</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sipce-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Citas</th>
                    <th>Pacientes</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estados as $estado)
                <tr>
                    <td><span class="sipce-cell-bold">#{{ $estado->id }}</span></td>
                    <td><span class="sipce-badge sipce-badge-primary">{{ $estado->tipo }}</span></td>
                    <td>{{ Str::limit($estado->descripcion, 40) }}</td>
                    <td>
                        @if($estado->permite_citas)
                            <span class="sipce-indicator sipce-indicator-yes"><i class="fas fa-check-circle"></i></span>
                        @else
                            <span class="sipce-indicator sipce-indicator-no"><i class="fas fa-times-circle"></i></span>
                        @endif
                    </td>
                    <td><span class="sipce-cell-bold">{{ $estado->pacientes_count }}</span></td>
                    <td>
                        <span class="sipce-cell-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $estado->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                    <td>
                        <div class="sipce-actions">
                            <button class="sipce-btn-icon sipce-btn-view" onclick="verEstado({{ $estado->id }})" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="sipce-btn-icon sipce-btn-edit" onclick="editarEstado({{ $estado->id }}, '{{ addslashes($estado->tipo) }}', '{{ addslashes($estado->descripcion) }}', {{ $estado->permite_citas ? 'true' : 'false' }})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="sipce-btn-icon sipce-btn-delete" onclick="confirmarEliminar({{ $estado->id }})" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="sipce-empty">
                            <i class="fas fa-tag sipce-empty-icon"></i>
                            <p class="sipce-empty-title">No hay estados registrados</p>
                            <p class="sipce-empty-text">Crea un nuevo estado para comenzar</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sipce-pagination">
        {{ $estados->links() }}
    </div>
</div>
