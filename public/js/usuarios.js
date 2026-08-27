document.addEventListener('DOMContentLoaded', function() {
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // ===== ELEMENTOS USUARIOS =====
    const searchInput = document.getElementById('searchInput');
    const btnNuevo = document.getElementById('btnNuevoUsuario');
    const userModal = document.getElementById('userModal');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const userForm = document.getElementById('userForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const usersTableBody = document.getElementById('usersTableBody');
    
    // ===== ELEMENTOS PACIENTE =====
    const esPacienteCheckbox = document.getElementById('es_paciente');
    const pacienteSelectGroup = document.getElementById('pacienteSelectGroup');
    const pacienteSelect = document.getElementById('paciente_id');
    
    // ===== ELEMENTOS ROLES =====
    const btnGestionarRoles = document.getElementById('btnGestionarRoles');
    const roleModal = document.getElementById('roleModal');
    const btnCloseRoleModal = document.getElementById('btnCloseRoleModal');
    const roleForm = document.getElementById('roleForm');
    const btnShowCreateRole = document.getElementById('btnShowCreateRole');
    const btnCancelRole = document.getElementById('btnCancelRole');
    const btnSaveRole = document.getElementById('btnSaveRole');
    const rolesList = document.getElementById('rolesList');
    const permissionsContainer = document.getElementById('permissionsContainer');
    
    let availablePermissions = [];
    
    // ===== EVENTO PARA MOSTRAR/OCULTAR SELECTOR DE PACIENTE =====
    if (esPacienteCheckbox) {
        esPacienteCheckbox.addEventListener('change', function() {
            if (this.checked) {
                pacienteSelectGroup.style.display = 'block';
                if (pacienteSelect) pacienteSelect.required = true;
                // Si el rol seleccionado no es 'paciente', sugerir cambiarlo
                const roleSelect = document.getElementById('role');
                const selectedRoleText = roleSelect.options[roleSelect.selectedIndex]?.text;
                if (selectedRoleText && selectedRoleText.toLowerCase() !== 'paciente') {
                    SIPCE_ALERT.confirm({
                        title: '¿Cambiar rol?',
                        text: 'Los pacientes generalmente tienen el rol "paciente". ¿Deseas cambiarlo?',
                        confirmText: 'Sí, cambiar',
                        cancelText: 'No, mantener'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            for(let i = 0; i < roleSelect.options.length; i++) {
                                if (roleSelect.options[i].text.toLowerCase() === 'paciente') {
                                    roleSelect.selectedIndex = i;
                                    break;
                                }
                            }
                        }
                    });
                }
            } else {
                pacienteSelectGroup.style.display = 'none';
                if (pacienteSelect) pacienteSelect.required = false;
                if (pacienteSelect) pacienteSelect.value = '';
            }
        });
    }

    // Auto-fill nombre y email al seleccionar paciente
    if (pacienteSelect) {
        pacienteSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                const nombre = selectedOption.dataset.nombre || '';
                const email = selectedOption.dataset.email || '';
                const nameInput = document.getElementById('name');
                const emailInput = document.getElementById('email');
                if (nameInput && nombre) nameInput.value = nombre;
                if (emailInput && email) emailInput.value = email;
            }
        });
    }
    
    // ===== FUNCIONES USUARIOS =====
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = usersTableBody.querySelectorAll('tr:not(#empty-row)');
            rows.forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    if (btnNuevo) {
        btnNuevo.addEventListener('click', () => {
            loadPacientesSinUsuario();
            openCreateUserModal();
        });
    }
    if (btnCloseModal) btnCloseModal.addEventListener('click', closeUserModal);
    if (userModal) userModal.addEventListener('click', function(e) { if (e.target === this) closeUserModal(); });
    
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', function() {
            openEditUserModal(this.getAttribute('data-user-id'));
        });
    });
    
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteUser(this.getAttribute('data-user-id'), this.getAttribute('data-user-name'));
        });
    });
    
    if (userForm) userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveUser();
    });
    
    function openCreateUserModal() {
        if (modalTitle) modalTitle.textContent = 'Nuevo Usuario';
        if (userForm) userForm.reset();
        document.getElementById('userId').value = '';
        document.getElementById('method').value = 'POST';
        document.getElementById('password').required = true;
        document.getElementById('password_confirmation').required = true;
        document.getElementById('passwordHelp').style.display = 'none';
        
        if (esPacienteCheckbox) esPacienteCheckbox.checked = false;
        if (pacienteSelectGroup) pacienteSelectGroup.style.display = 'none';
        if (pacienteSelect) pacienteSelect.value = '';
        
        updateRoleSelect().then(() => {
            if (userModal) userModal.classList.add('active');
        });
    }
    
    async function openEditUserModal(userId) {
        try {
            const response = await fetch(`/usuarios/${userId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            if (!response.ok) throw new Error('Error al cargar');
            const data = await response.json();
            const user = data.user;
            
            if (modalTitle) modalTitle.textContent = 'Editar Usuario';
            document.getElementById('userId').value = user.id;
            document.getElementById('method').value = 'PUT';
            document.getElementById('name').value = user.name;
            document.getElementById('email').value = user.email;
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
            document.getElementById('password').required = false;
            document.getElementById('password_confirmation').required = false;
            document.getElementById('passwordHelp').style.display = 'block';
            
            await updateRoleSelect();
            
            if (user.roles && user.roles.length > 0) {
                document.getElementById('role').value = user.roles[0].id;
            }
            
            if (user.paciente) {
                if (esPacienteCheckbox) esPacienteCheckbox.checked = true;
                if (pacienteSelectGroup) pacienteSelectGroup.style.display = 'block';
                if (pacienteSelect) {
                    await loadPacientesSinUsuario({
                        id: user.paciente.id,
                        numero_expediente: user.paciente.numero_expediente || '',
                        nombre_completo: user.paciente.nombre_completo || user.name,
                        email: user.paciente.email || user.email
                    });
                    pacienteSelect.value = user.paciente.id;
                }
            } else {
                if (esPacienteCheckbox) esPacienteCheckbox.checked = false;
                if (pacienteSelectGroup) pacienteSelectGroup.style.display = 'none';
            }
            
            if (userModal) userModal.classList.add('active');
        } catch (error) {
            SIPCE_ALERT.error('No se pudo cargar el usuario');
        }
    }
    
    function closeUserModal() {
        if (userModal) userModal.classList.remove('active');
        if (userForm) userForm.reset();
    }
    
    async function loadPacientesSinUsuario(currentPaciente = null) {
        try {
            const response = await fetch('/pacientes/sin-usuario', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            
            if (pacienteSelect) {
                const currentValue = pacienteSelect.value;
                pacienteSelect.innerHTML = '<option value="">Selecciona un paciente...</option>';
                
                // Agregar el paciente actual (el que está vinculado al usuario editado) primero
                if (currentPaciente) {
                    const option = document.createElement('option');
                    option.value = currentPaciente.id;
                    option.textContent = `#${currentPaciente.numero_expediente || ''} - ${currentPaciente.nombre_completo}`;
                    option.dataset.nombre = currentPaciente.nombre_completo;
                    option.dataset.email = currentPaciente.email || '';
                    pacienteSelect.appendChild(option);
                }
                
                if (data.pacientes && data.pacientes.length > 0) {
                    data.pacientes.forEach(paciente => {
                        // No duplicar si ya está el paciente actual
                        if (currentPaciente && paciente.id === currentPaciente.id) return;
                        const option = document.createElement('option');
                        option.value = paciente.id;
                        option.textContent = `#${paciente.numero_expediente} - ${paciente.nombre_completo}`;
                        option.dataset.nombre = paciente.nombre_completo;
                        option.dataset.email = paciente.email || '';
                        pacienteSelect.appendChild(option);
                    });
                } else if (!currentPaciente) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No hay pacientes sin usuario';
                    option.disabled = true;
                    pacienteSelect.appendChild(option);
                }
                if (currentValue) pacienteSelect.value = currentValue;
            }
        } catch (error) {
            console.error('Error al cargar pacientes:', error);
        }
    }
    
    async function saveUser() {
        const userId = document.getElementById('userId').value;
        const method = document.getElementById('method').value;
        const esPaciente = esPacienteCheckbox ? esPacienteCheckbox.checked : false;
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        }
        
        const formData = new FormData(userForm);
        if (method === 'PUT') formData.append('_method', 'PUT');
        
        if (esPaciente && (!pacienteSelect || !pacienteSelect.value)) {
            SIPCE_ALERT.error('Debes seleccionar un paciente para vincular');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Usuario';
            }
            return;
        }
        
        const url = userId ? `/usuarios/${userId}` : '/usuarios';
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error');
            if (data.success) {
                await SIPCE_ALERT.success(data.message);
                closeUserModal();
                window.location.reload();
            }
        } catch (error) {
            SIPCE_ALERT.error(error.message);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Usuario';
            }
        }
    }
    
    async function deleteUser(userId, userName) {
        const result = await SIPCE_ALERT.confirmDelete({
            html: 'Se eliminará a <strong>' + userName + '</strong>'
        });
        
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/usuarios/${userId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message);
                if (data.success) {
                    const row = document.getElementById(`user-row-${userId}`);
                    if (row) { row.style.opacity = '0'; setTimeout(() => row.remove(), 300); }
                    await SIPCE_ALERT.success(data.message, 'Eliminado', { timer: 2000 });
                    setTimeout(() => window.location.reload(), 1500);
                }
            } catch (error) {
                SIPCE_ALERT.error(error.message);
            }
        }
    }
    
    async function updateRoleSelect() {
        try {
            const response = await fetch('/roles', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            
            const roleSelect = document.getElementById('role');
            if (roleSelect) {
                const currentValue = roleSelect.value;
                roleSelect.innerHTML = '<option value="">Selecciona un rol</option>';
                data.roles.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name;
                    roleSelect.appendChild(option);
                });
                if (currentValue) roleSelect.value = currentValue;
            }
        } catch (error) {
            console.error('Error al actualizar selector de roles:', error);
        }
    }
    
    // ===== FUNCIONES ROLES =====
    if (btnGestionarRoles) {
        btnGestionarRoles.addEventListener('click', async () => {
            if (roleModal) roleModal.classList.add('active');
            await loadRoles();
        });
    }
    
    if (btnCloseRoleModal) btnCloseRoleModal.addEventListener('click', closeRoleModal);
    if (roleModal) roleModal.addEventListener('click', function(e) { if (e.target === this) closeRoleModal(); });
    
    if (btnShowCreateRole) btnShowCreateRole.addEventListener('click', () => showRoleForm('create'));
    if (btnCancelRole) btnCancelRole.addEventListener('click', () => {
        if (roleForm) roleForm.style.display = 'none';
        if (btnShowCreateRole) btnShowCreateRole.style.display = 'block';
    });
    
    if (roleForm) roleForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        await saveRole();
    });
    
    const roleNameInput = document.getElementById('roleName');
    if (roleNameInput) {
        roleNameInput.addEventListener('input', function() {
            if (document.getElementById('roleId').value === '') {
                document.getElementById('roleSlug').value = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            }
        });
    }

    // ===== BÚSQUEDA DE PERMISOS =====
    const permSearch = document.getElementById('permSearch');
    if (permSearch) {
        permSearch.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const groups = permissionsContainer.querySelectorAll('.permissions-group');
            groups.forEach(group => {
                const checkboxes = group.querySelectorAll('.permission-checkbox');
                let groupVisible = false;
                checkboxes.forEach(cb => {
                    const text = cb.textContent.toLowerCase();
                    if (!term || text.includes(term)) {
                        cb.classList.remove('hidden');
                        groupVisible = true;
                    } else {
                        cb.classList.add('hidden');
                    }
                });
                group.classList.toggle('hidden', !groupVisible);
            });
            updatePermCount();
        });
    }

    // ===== SELECCIONAR / DESELECCIONAR TODOS =====
    const permSelectAll = document.getElementById('permSelectAll');
    const permDeselectAll = document.getElementById('permDeselectAll');
    if (permSelectAll) {
        permSelectAll.addEventListener('click', () => {
            permissionsContainer.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(cb => cb.checked = true);
            updatePermCount();
        });
    }
    if (permDeselectAll) {
        permDeselectAll.addEventListener('click', () => {
            permissionsContainer.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
            updatePermCount();
        });
    }

    // ===== CONTADOR DE PERMISOS =====
    function updatePermCount() {
        const count = permissionsContainer ? permissionsContainer.querySelectorAll('input[type="checkbox"]:checked').length : 0;
        const badge = document.getElementById('permCount');
        if (badge) badge.textContent = count + ' seleccionado' + (count !== 1 ? 's' : '');
    }
    
    function closeRoleModal() {
        if (roleModal) roleModal.classList.remove('active');
        if (roleForm) roleForm.style.display = 'none';
        if (btnShowCreateRole) btnShowCreateRole.style.display = 'block';
        if (permSearch) permSearch.value = '';
    }
    
    async function loadRoles() {
        try {
            const response = await fetch('/roles', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            availablePermissions = data.availablePermissions;

            if (rolesList) {
                rolesList.innerHTML = data.roles.map(role => {
                    const permCount = role.permissions ? role.permissions.length : 0;
                    const permSample = role.permissions ? role.permissions.slice(0, 3).join(', ') : '';
                    const moreText = permCount > 3 ? ` (+${permCount - 3} más)` : '';
                    return `
                    <div class="role-item">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <strong style="color: #1e293b; font-size: 15px;">
                                    <i class="fas fa-user-tag me-1" style="color: #667eea;"></i>${role.name}
                                </strong>
                                <span style="color: #64748b; font-size: 12px;">${role.users_count} usuario(s)</span>
                            </div>
                            ${role.description ? `<div style="color: #94a3b8; font-size: 12px; margin-top: 2px;">${role.description}</div>` : ''}
                            <div style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                                <span style="background: #ede9fe; color: #667eea; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                                    <i class="fas fa-key me-1"></i>${permCount} permiso(s)
                                </span>
                                ${permSample ? `<span style="color: #94a3b8; font-size: 11px;">${permSample}${moreText}</span>` : ''}
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button class="btn-icon edit" onclick="event.preventDefault(); editRole(${role.id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${role.slug !== 'admin' ? `
                                <button class="btn-icon delete" onclick="event.preventDefault(); deleteRole(${role.id}, '${role.name.replace(/'/g, "\\'")}')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>`;
                }).join('');
            }
        } catch (error) {
            console.error('Error:', error);
            SIPCE_ALERT.error('No se pudieron cargar los roles');
        }
    }
    
    async function showRoleForm(mode, roleId = null) {
        if (roleForm) roleForm.style.display = 'block';
        if (btnShowCreateRole) btnShowCreateRole.style.display = 'none';
        if (permSearch) permSearch.value = '';
        
        if (permissionsContainer) {
            permissionsContainer.innerHTML = '';
            if (availablePermissions && availablePermissions.length > 0) {
                availablePermissions.forEach(group => {
                    const groupDiv = document.createElement('div');
                    groupDiv.className = 'permissions-group';
                    
                    const groupHeader = document.createElement('div');
                    groupHeader.className = 'perm-group-toggle';
                    groupHeader.innerHTML = `
                        <input type="checkbox" class="group-toggle-cb" data-group="${group.group}">
                        <strong class="permissions-group-title" style="flex:1; margin:0;">
                            <i class="fas ${group.icon} me-2" style="color: #667eea;"></i>${group.group}
                        </strong>`;
                    groupDiv.appendChild(groupHeader);
                    
                    const toggleCb = groupHeader.querySelector('.group-toggle-cb');
                    toggleCb.addEventListener('change', function() {
                        groupDiv.querySelectorAll('.permission-checkbox input[type="checkbox"]').forEach(cb => {
                            cb.checked = toggleCb.checked;
                        });
                        updatePermCount();
                    });
                    
                    group.permissions.forEach(perm => {
                        const checkboxDiv = document.createElement('div');
                        checkboxDiv.className = 'permission-checkbox';
                        checkboxDiv.innerHTML = `
                            <label>
                                <input type="checkbox" name="permissions[]" value="${perm.slug}"> ${perm.name}
                            </label>`;
                        const cb = checkboxDiv.querySelector('input[type="checkbox"]');
                        cb.addEventListener('change', () => {
                            const total = groupDiv.querySelectorAll('.permission-checkbox input[type="checkbox"]').length;
                            const checked = groupDiv.querySelectorAll('.permission-checkbox input[type="checkbox"]:checked').length;
                            toggleCb.checked = checked === total;
                            toggleCb.indeterminate = checked > 0 && checked < total;
                            updatePermCount();
                        });
                        groupDiv.appendChild(checkboxDiv);
                    });
                    permissionsContainer.appendChild(groupDiv);
                });
            }
        }
        
        if (mode === 'create') {
            document.getElementById('roleModalTitle').textContent = 'Crear Nuevo Rol';
            document.getElementById('roleId').value = '';
            document.getElementById('roleMethod').value = 'POST';
            document.getElementById('roleName').value = '';
            document.getElementById('roleSlug').value = '';
            document.getElementById('roleDescription').value = '';
            if (btnSaveRole) btnSaveRole.innerHTML = '<i class="fas fa-save me-2"></i>Crear Rol';
            updatePermCount();
        } else if (mode === 'edit' && roleId) {
            try {
                const response = await fetch(`/roles/${roleId}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await response.json();
                const role = data.role;
                
                document.getElementById('roleModalTitle').textContent = 'Editar Rol';
                document.getElementById('roleId').value = role.id;
                document.getElementById('roleMethod').value = 'PUT';
                document.getElementById('roleName').value = role.name;
                document.getElementById('roleSlug').value = role.slug;
                document.getElementById('roleDescription').value = role.description || '';
                if (btnSaveRole) btnSaveRole.innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Rol';
                
                if (role.permissions && role.permissions.length > 0 && permissionsContainer) {
                    permissionsContainer.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                        if (role.permissions.includes(cb.value)) cb.checked = true;
                    });
                    
                    permissionsContainer.querySelectorAll('.permissions-group').forEach(groupDiv => {
                        const total = groupDiv.querySelectorAll('.permission-checkbox input[type="checkbox"]').length;
                        const checked = groupDiv.querySelectorAll('.permission-checkbox input[type="checkbox"]:checked').length;
                        const toggleCb = groupDiv.querySelector('.group-toggle-cb');
                        if (toggleCb) {
                            toggleCb.checked = checked === total && total > 0;
                            toggleCb.indeterminate = checked > 0 && checked < total;
                        }
                    });
                }
                updatePermCount();
            } catch (error) {
                SIPCE_ALERT.error('No se pudo cargar el rol');
            }
        }
    }
    
    window.editRole = function(roleId) { showRoleForm('edit', roleId); };
    
    async function saveRole() {
        const roleId = document.getElementById('roleId').value;
        if (btnSaveRole) {
            btnSaveRole.disabled = true;
            btnSaveRole.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        }
        
        const selectedPermissions = [];
        if (permissionsContainer) {
            permissionsContainer.querySelectorAll('input[name="permissions[]"]:checked').forEach(cb => selectedPermissions.push(cb.value));
        }
        
        const roleData = {
            name: document.getElementById('roleName').value,
            slug: document.getElementById('roleSlug').value,
            description: document.getElementById('roleDescription').value,
            permissions: selectedPermissions
        };
        
        const url = roleId ? `/roles/${roleId}` : '/roles';
        
        try {
            const response = await fetch(url, {
                method: roleId ? 'PUT' : 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(roleData)
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Error al guardar');
            if (data.success) {
                await SIPCE_ALERT.success(data.message);
                if (roleForm) roleForm.style.display = 'none';
                if (btnShowCreateRole) btnShowCreateRole.style.display = 'block';
                await loadRoles();
                await updateRoleSelect();
            }
        } catch (error) {
            SIPCE_ALERT.error(error.message);
        } finally {
            if (btnSaveRole) {
                btnSaveRole.disabled = false;
                btnSaveRole.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Rol';
            }
        }
    }
    
    window.deleteRole = async function(roleId, roleName) {
        const result = await SIPCE_ALERT.confirmDelete({
            html: 'Se eliminará el rol <strong>' + roleName + '</strong>'
        });
        
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/roles/${roleId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message);
                if (data.success) {
                    await SIPCE_ALERT.success(data.message, 'Eliminado');
                    await loadRoles();
                    await updateRoleSelect();
                }
            } catch (error) {
                SIPCE_ALERT.error(error.message);
            }
        }
    };
    
    // Cerrar modales con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeUserModal();
            closeRoleModal();
        }
    });
});