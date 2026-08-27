<div class="sipce-table-card">
    <div class="sipce-table-header">
        <div>
            <h3>
                <i class="fas fa-clock"></i>
                Registro de Actividades
            </h3>
        </div>
        <div class="sipce-table-header-actions">
            <span class="sipce-count-badge">
                Mostrando {{ $registros->firstItem() ?? 0 }} - {{ $registros->lastItem() ?? 0 }} de {{ $registros->total() }}
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sipce-table">
            <thead>
                <tr>
                    <th>Acción</th>
                    <th>Usuario</th>
                    <th>Tabla</th>
                    <th>Registro</th>
                    <th>Descripción</th>
                    <th>IP</th>
                    <th>Fecha/Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="action-icon {{ $registro->accion }}">
                                <i class="fas 
                                    @if($registro->accion == 'INSERT') fa-plus
                                    @elseif($registro->accion == 'UPDATE') fa-edit
                                    @elseif($registro->accion == 'DELETE') fa-trash
                                    @else fa-sign-in-alt
                                    @endif
                                "></i>
                            </div>
                            <span class="sipce-badge {{ $registro->accion == 'INSERT' ? 'sipce-badge-success' : ($registro->accion == 'UPDATE' ? 'sipce-badge-warning' : 'sipce-badge-danger') }}">
                                {{ $registro->accion }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="sipce-cell-main">{{ $registro->usuario_nombre ?? 'Sistema' }}</span>
                        <span class="sipce-cell-sub">ID: {{ $registro->usuario_id ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="sipce-badge sipce-badge-info">{{ ucfirst($registro->tabla_afectada) }}</span>
                    </td>
                    <td>
                        <span class="sipce-cell-bold">{{ $registro->registro_id ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @if($registro->datos_viejos)
                            <span style="color: #ef4444; font-size: 12px;">
                                <i class="fas fa-arrow-left"></i> {{ Str::limit($registro->datos_viejos, 50) }}
                            </span>
                        @endif
                        @if($registro->datos_nuevos)
                            @if($registro->datos_viejos)<br>@endif
                            <span style="color: #10b981; font-size: 12px;">
                                <i class="fas fa-arrow-right"></i> {{ Str::limit($registro->datos_nuevos, 50) }}
                            </span>
                        @endif
                        @if(!$registro->datos_viejos && !$registro->datos_nuevos)
                            <span class="sipce-cell-muted">Sin detalles</span>
                        @endif
                    </td>
                    <td>
                        <span class="sipce-cell-code">{{ $registro->ip ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="sipce-cell-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $registro->fecha_hora ? $registro->fecha_hora->format('d/m/Y H:i:s') : 'N/A' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="sipce-empty">
                            <i class="fas fa-history sipce-empty-icon"></i>
                            <p class="sipce-empty-title">No hay registros en la bitácora</p>
                            <p class="sipce-empty-text">Los eventos del sistema aparecerán aquí automáticamente</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="sipce-pagination">
        {{ $registros->appends(request()->query())->links() }}
    </div>
</div>
