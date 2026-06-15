<div class="page-header">
    <div class="header-content">
        <h1>
            <i class="fas fa-user-shield me-2"></i>
            Usuarios y Roles
        </h1>
        <p>Gestión de usuarios, permisos y roles del sistema</p>
    </div>
    <div class="header-buttons">
        @if(auth()->check() && auth()->user()->hasPermission('roles.index'))
        <button class="btn-nuevo btn-roles" id="btnGestionarRoles">
            <i class="fas fa-user-tag"></i>
            Gestionar Roles
        </button>
        @endif
        
        @if(auth()->check() && auth()->user()->hasPermission('usuarios.create'))
        <button class="btn-nuevo" id="btnNuevoUsuario">
            <i class="fas fa-plus-circle"></i>
            Nuevo Usuario
        </button>
        @endif
    </div>
</div>