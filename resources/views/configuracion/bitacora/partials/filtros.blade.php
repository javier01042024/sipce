<div class="filtros-card">
    <form id="filtrosForm" method="GET" action="{{ route('configuracion.bitacora.index') }}" style="display: contents;">
        
        <div class="filtro-group">
            <label><i class="fas fa-user me-1"></i> Usuario</label>
            <select name="usuario" class="filtro-select">
                <option value="">Todos los usuarios</option>
                @foreach($usuarios as $usuario)
                <option value="{{ $usuario->id }}" {{ request('usuario') == $usuario->id ? 'selected' : '' }}>
                    {{ $usuario->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-tasks me-1"></i> Acción</label>
            <select name="accion" class="filtro-select">
                <option value="">Todas las acciones</option>
                @foreach($acciones as $opcion)
                <option value="{{ $opcion }}" {{ request('accion') == $opcion ? 'selected' : '' }}>
                    {{ $opcion }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-folder me-1"></i> Tabla</label>
            <select name="tabla" class="filtro-select">
                <option value="">Todas las tablas</option>
                @foreach($tablas as $opcion)
                <option value="{{ $opcion }}" {{ request('tabla') == $opcion ? 'selected' : '' }}>
                    {{ ucfirst($opcion) }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-calendar me-1"></i> Desde</label>
            <input type="date" name="desde" class="filtro-input" value="{{ request('desde') }}">
        </div>

        <div class="filtro-group">
            <label><i class="fas fa-calendar me-1"></i> Hasta</label>
            <input type="date" name="hasta" class="filtro-input" value="{{ request('hasta') }}">
        </div>

        <button type="submit" class="btn-filtrar">
            <i class="fas fa-filter me-1"></i>
            Filtrar
        </button>

        <button type="button" id="btnLimpiar" class="btn-limpiar">
            <i class="fas fa-times me-1"></i>
            Limpiar
        </button>
    </form>
</div>