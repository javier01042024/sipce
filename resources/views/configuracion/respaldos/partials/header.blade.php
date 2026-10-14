<div class="page-header">
    <div class="header-content">
        <h1>
            <i class="fas fa-database me-2"></i>
            Respaldos del Sistema
        </h1>
        <p>Gestión de copias de seguridad, restauración e importación de datos</p>
    </div>

    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        @if(Auth::user()->hasPermission('respaldos.create'))
        <button class="btn-backup" id="btnBackup">
            <i class="fas fa-plus-circle"></i>
            Crear Respaldo
        </button>
        @endif

        @if(Auth::user()->hasPermission('respaldos.import'))
        <button class="btn-backup" id="btnImportSql">
            <i class="fas fa-upload"></i>
            Importar SQL
        </button>
        @endif
    </div>
</div>