{{-- diarios/partials/filtros.blade.php --}}
<div class="filtros-container">
    <div class="filter-group filtro-busqueda-group">
        <label for="filtroBusqueda"><i class="fas fa-search"></i> Buscar</label>
        <input type="text" id="filtroBusqueda" class="filter-input" placeholder="Paciente, fecha o contenido..." autocomplete="off">
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->hasRole('psicologa'))
    <div class="filter-group">
        <label for="filtroUsuario"><i class="fas fa-user"></i> Paciente</label>
        <select class="filter-input" id="filtroUsuario">
            <option value="">Todos los pacientes</option>
            @foreach($diarios->unique('user_id') as $diario)
                <option value="{{ $diario->user_id }}">
                    {{ $diario->user->name }}
                </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="filter-group">
        <label for="filtroFecha"><i class="far fa-calendar"></i> Rango</label>
        <select class="filter-input" id="filtroFecha">
            <option value="">Todas las fechas</option>
            <option value="hoy">Hoy</option>
            <option value="semana">Esta semana</option>
            <option value="mes">Este mes</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="filtroEmocion"><i class="fas fa-smile"></i> Emoción</label>
        <select class="filter-input" id="filtroEmocion">
            <option value="">Todas las emociones</option>
            <option value="positivo">Positivas</option>
            <option value="neutral">Neutrales</option>
            <option value="negativo">Negativas</option>
        </select>
    </div>

    <div class="filter-group">
        <label for="filtroDesde"><i class="far fa-calendar-alt"></i> Desde</label>
        <input type="date" id="filtroDesde" class="filter-input">
    </div>

    <div class="filter-group">
        <label for="filtroHasta"><i class="far fa-calendar-alt"></i> Hasta</label>
        <input type="date" id="filtroHasta" class="filter-input">
    </div>

    <button type="button" id="filtroLimpiar" class="btn-filter btn-filter-clear">
        <i class="fas fa-eraser"></i> Limpiar
    </button>

    <p class="filtros-info" id="filtrosContador">Mostrando {{ $diarios->count() }} de {{ $diarios->count() }} registros</p>
</div>
