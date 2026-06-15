<div class="card">
    <div class="card-header-custom">
        <h3>
            <i class="fas fa-list"></i>
            Listado de Usuarios
        </h3>
        <div class="search-box">
            <i class="fas fa-search" style="color: rgba(255,255,255,0.6);"></i>
            <input type="text" class="search-input" placeholder="Buscar usuario..." id="searchInput">
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                @forelse($users as $user)
                <tr id="user-row-{{ $user->id }}">
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="user-details">
                                <span class="user-name">{{ $user->name }}</span>
                                <span class="user-email">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->roles->isNotEmpty())
                            @php 
                                $role = $user->roles->first();
                                $roleSlug = $role->slug ?? 'default';
                                $roleIcon = $role->icon ?? 'user';
                            @endphp
                            <span class="badge-rol badge-rol-{{ $roleSlug }}">
                                <i class="fas fa-{{ $roleIcon }} me-1"></i>
                                {{ $role->name }}
                            </span>
                        @else
                            <span class="badge-rol badge-rol-default">
                                Sin rol
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-estado {{ $user->email_verified_at ? 'activo' : 'inactivo' }}">
                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                            {{ $user->email_verified_at ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <i class="far fa-clock me-2" style="color: #64748b;"></i>
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                    </td>
                    <td>
                        <div class="actions">
                            @if(auth()->check() && auth()->user()->hasPermission('usuarios.edit'))
                            <button class="btn-icon edit btn-editar" 
                                    data-user-id="{{ $user->id }}" 
                                    title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            @endif
                            
                            @if(auth()->check() && auth()->user()->hasPermission('usuarios.destroy'))
                            <button class="btn-icon delete btn-eliminar" 
                                    data-user-id="{{ $user->id }}" 
                                    data-user-name="{{ $user->name }}" 
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-row">
                    <td colspan="5" style="text-align: center; padding: 40px;">
                        <i class="fas fa-users" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 15px;"></i>
                        <span style="color: #64748b;">No hay usuarios registrados</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>