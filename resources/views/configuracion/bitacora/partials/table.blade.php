<div class="card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-clock"></i>
            Registro de Actividades
        </h3>
        <span style="font-size: 12px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px;">
            Mostrando {{ $registros->firstItem() ?? 0 }} - {{ $registros->lastItem() ?? 0 }} de {{ $registros->total() }}
        </span>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Acción</th>
                    <th>Usuario</th>
                    <th>Tabla</th>
                    <th>Registro ID</th>
                    <th>Descripción</th>
                    <th>IP</th>
                    <th>Fecha/Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $registro)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <div class="action-icon {{ $registro->accion }}">
                                <i class="fas 
                                    @if($registro->accion == 'INSERT') fa-plus
                                    @elseif($registro->accion == 'UPDATE') fa-edit
                                    @elseif($registro->accion == 'DELETE') fa-trash
                                    @else fa-sign-in-alt
                                    @endif
                                "></i>
                            </div>
                            <span>{{ $registro->accion }}</span>
                        </div>
                    </div>
                    <td>
                        <strong>{{ $registro->usuario_nombre ?? 'Sistema' }}</strong><br>
                        <small style="color: #64748b;">ID: {{ $registro->usuario_id ?? 'N/A' }}</small>
                    </div>
                    <td>{{ ucfirst($registro->tabla_afectada) }}</div>
                    <td>{{ $registro->registro_id ?? 'N/A' }}</div>
                    <td>
                        @if($registro->datos_viejos)
                            <span style="color: #ef4444;"><i class="fas fa-arrow-left"></i> {{ $registro->datos_viejos }}</span>
                        @endif
                        @if($registro->datos_nuevos)
                            @if($registro->datos_viejos)
                            <br>
                            @endif
                            <span style="color: #10b981;"><i class="fas fa-arrow-right"></i> {{ $registro->datos_nuevos }}</span>
                        @endif
                        @if(!$registro->datos_viejos && !$registro->datos_nuevos)
                            <span style="color: #64748b;">Sin detalles</span>
                        @endif
                    </div>
                    <td>
                        <code style="font-size: 11px;">{{ $registro->ip ?? 'N/A' }}</code>
                    </div>
                    <td>
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ $registro->fecha_hora ? $registro->fecha_hora->format('d/m/Y H:i:s') : 'N/A' }}
                    </div>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 50px;">
                        <i class="fas fa-database" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px; display: block;"></i>
                        <span style="color: #64748b;">No hay registros en la bitácora</span>
                        <p style="color: #94a3b8; font-size: 13px;">Los eventos del sistema aparecerán aquí automáticamente</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $registros->appends(request()->query())->links() }}
    </div>
</div>