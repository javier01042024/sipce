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
                </label>
                <div id="permissionsContainer" style="max-height: 250px; overflow-y: auto; padding: 10px; border: 2px solid #e2e8f0; border-radius: 10px;">
                    <div class="text-muted" style="text-align: center; padding: 20px;">Cargando permisos...</div>
                </div>
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