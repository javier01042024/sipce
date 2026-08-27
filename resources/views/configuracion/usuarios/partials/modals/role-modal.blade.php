<div class="modal-overlay" id="roleModal">
    <div class="modal-content modal-content-wide">
        <div class="modal-header">
            <h2 id="roleModalTitle">Gestionar Roles</h2>
            <button type="button" class="close-modal" id="btnCloseRoleModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="rolesList" style="margin-bottom: 20px;"></div>
        
        <button class="btn-submit btn-submit-green" id="btnShowCreateRole" style="margin-bottom: 20px;">
            <i class="fas fa-plus-circle me-2"></i>
            Crear Nuevo Rol
        </button>
        
        <form id="roleForm" style="display: none;">
            @csrf
            <input type="hidden" id="roleId">
            <input type="hidden" id="roleMethod" value="POST">
            
            <div class="form-group">
                <label for="roleName">
                    <i class="fas fa-tag me-2"></i>Nombre del Rol
                </label>
                <input type="text" class="form-control" id="roleName" name="name" required 
                       placeholder="Ej: Médico, Secretaria">
            </div>
            
            <div class="form-group">
                <label for="roleSlug">
                    <i class="fas fa-code me-2"></i>Slug
                </label>
                <input type="text" class="form-control" id="roleSlug" name="slug" required 
                       placeholder="Ej: medico, secretaria">
                <small class="text-muted">Solo minúsculas, números y guiones</small>
            </div>
            
            <div class="form-group">
                <label for="roleDescription">
                    <i class="fas fa-info-circle me-2"></i>Descripción
                </label>
                <textarea class="form-control" id="roleDescription" name="description" rows="2" 
                          placeholder="Descripción del rol"></textarea>
            </div>
            
            <div class="form-group">
                <label>
                    <i class="fas fa-lock me-2"></i>Permisos
                    <span id="permCount" class="perm-count-badge">0 seleccionados</span>
                </label>
                
                <div class="perm-toolbar">
                    <input type="text" class="form-control perm-search" id="permSearch" 
                           placeholder="Buscar permiso..." style="flex:1;">
                    <button type="button" class="perm-btn perm-btn-select" id="permSelectAll">
                        <i class="fas fa-check-double me-1"></i> Todos
                    </button>
                    <button type="button" class="perm-btn perm-btn-deselect" id="permDeselectAll">
                        <i class="fas fa-times me-1"></i> Ninguno
                    </button>
                </div>
                
                <div id="permissionsContainer" class="perm-container"></div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn-submit btn-submit-gray" id="btnCancelRole" style="flex: 1;">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn-submit" id="btnSaveRole" style="flex: 2;">
                    <i class="fas fa-save me-2"></i>
                    Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .perm-count-badge {
        background: #667eea;
        color: white;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 10px;
        margin-left: 8px;
        font-weight: 600;
    }
    .perm-toolbar {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
        align-items: center;
    }
    .perm-search {
        font-size: 13px !important;
        padding: 8px 12px !important;
    }
    .perm-btn {
        padding: 7px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }
    .perm-btn-select {
        color: #10b981;
        border-color: #a7f3d0;
    }
    .perm-btn-select:hover {
        background: #ecfdf5;
    }
    .perm-btn-deselect {
        color: #ef4444;
        border-color: #fecaca;
    }
    .perm-btn-deselect:hover {
        background: #fef2f2;
    }
    .perm-container {
        max-height: 300px;
        overflow-y: auto;
        padding: 12px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }
    .perm-container::-webkit-scrollbar {
        width: 6px;
    }
    .perm-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .permissions-group {
        margin-bottom: 12px;
    }
    .permissions-group-title {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        padding: 6px 8px;
        margin-bottom: 4px;
        background: #e2e8f0;
        border-radius: 6px;
    }
    .permission-checkbox {
        display: block;
        padding: 4px 8px;
        margin: 1px 0;
        border-radius: 6px;
        transition: background 0.15s;
    }
    .permission-checkbox:hover {
        background: #e2e8f0;
    }
    .permission-checkbox label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #475569;
        margin: 0;
    }
    .permission-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #667eea;
        cursor: pointer;
    }
    .permission-checkbox.hidden {
        display: none;
    }
    .permissions-group.hidden {
        display: none;
    }
    .perm-group-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 6px 8px;
        border-radius: 6px;
        user-select: none;
    }
    .perm-group-toggle:hover {
        background: #dbeafe;
    }
    .perm-group-toggle input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #667eea;
        cursor: pointer;
    }
</style>
