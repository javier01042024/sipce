<div class="sipce-table-card">
    <div class="sipce-table-header">
        <div>
            <h3>
                <i class="fas fa-users"></i>
                Listado de Usuarios
            </h3>
            <p>Total: {{ $users->count() }} usuarios registrados</p>
        </div>
        <div class="sipce-table-header-actions">
            <div class="sipce-search-box">
                <i class="fas fa-search" style="color: rgba(255,255,255,0.6);"></i>
                <input type="text" class="sipce-search-input" placeholder="Buscar usuario..." id="searchInput">
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="sipce-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Paciente Vinculado</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @forelse($users as $user)
                <tr id="user-row-{{ $user->id }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                <span class="sipce-cell-main">{{ $user->name }}</span>
                                <span class="sipce-cell-sub">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->roles->isNotEmpty())
                            @php 
                                $role = $user->roles->first();
                                $roleSlug = $role->slug ?? 'default';
                                $roleIcon = $role->icon ?? 'user';
                                $palette = [
                                    'admin' => ['#f39c12','#f1c40f'],
                                    'user' => ['var(--sipce-primary)','var(--sipce-primary-dark)'],
                                    'medico' => ['#3498db','#2ecc71'],
                                    'secretaria' => ['#11998e','#38ef7d'],
                                    'moderator' => ['#6366f1','#818cf8'],
                                    'editor' => ['#f59e0b','#fbbf24'],
                                    'paciente' => ['#ec4899','#f472b6'],
                                    'psicologo' => ['#0ea5e9','#38bdf8'],
                                ];
                                if (isset($palette[$roleSlug])) {
                                    $grad = 'linear-gradient(135deg, ' . $palette[$roleSlug][0] . ' 0%, ' . $palette[$roleSlug][1] . ' 100%)';
                                } else {
                                    $hash = crc32($roleSlug);
                                    $colors = ['#ef4444','#f97316','#eab308','#22c55e','#14b8a6','#06b6d4','#3b82f6','#8b5cf6','#ec4899','#f43f5e'];
                                    $c1 = $colors[abs($hash) % count($colors)];
                                    $c2 = $colors[abs($hash >> 4) % count($colors)];
                                    $grad = 'linear-gradient(135deg, ' . $c1 . ' 0%, ' . $c2 . ' 100%)';
                                }
                            @endphp
                            <span class="sipce-badge" style="background: {{ $grad }}; color: white;">
                                <i class="fas fa-{{ $roleIcon }}"></i>
                                {{ $role->name }}
                            </span>
                        @else
                            <span class="sipce-badge sipce-badge-neutral">Sin rol</span>
                        @endif
                    </td>
                    <td>
                        @if($user->paciente)
                            @php
                                $nombrePaciente = 'Sin nombre';
                                if ($user->paciente->detalle) {
                                    $nombrePaciente = $user->paciente->detalle->nombre . ' ' . $user->paciente->detalle->apellido;
                                }
                            @endphp
                            <div style="display: flex; flex-direction: column;">
                                <span class="sipce-cell-main">
                                    <i class="fas fa-link" style="color: var(--sipce-primary); margin-right: 4px;"></i>
                                    {{ $nombrePaciente }}
                                </span>
                                <span class="sipce-cell-sub">Exp: {{ $user->paciente->numero_expediente }}</span>
                            </div>
                        @else
                            <span class="sipce-cell-muted">â€”</span>
                        @endif
                    </td>
                    <td>
                        <span class="sipce-badge {{ $user->email_verified_at ? 'sipce-badge-success' : 'sipce-badge-danger' }}">
                            <i class="fas fa-circle" style="font-size: 6px;"></i>
                            {{ $user->email_verified_at ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <span class="sipce-cell-date">
                            <i class="far fa-clock"></i>
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                        </span>
                    </td>
                    <td>
                        <div class="sipce-actions">
                            @if(auth()->check() && auth()->user()->hasPermission('usuarios.edit'))
                            <button class="sipce-btn-icon sipce-btn-edit btn-editar" 
                                    data-user-id="{{ $user->id }}" 
                                    title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endif
                            
                            @if(auth()->check() && auth()->user()->hasPermission('usuarios.destroy'))
                            <button class="sipce-btn-icon sipce-btn-delete btn-eliminar" 
                                    data-user-id="{{ $user->id }}" 
                                    data-user-name="{{ $user->name }}" 
                                    title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="sipce-empty">
                            <i class="fas fa-users sipce-empty-icon"></i>
                            <p class="sipce-empty-title">No hay usuarios registrados</p>
                            <p class="sipce-empty-text">Crea un nuevo usuario para comenzar</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
