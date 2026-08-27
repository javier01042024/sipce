{{-- diarios/partials/filtros.blade.php --}}
<div class="filtros-container">
    @if(Auth::id() == 1)
    <select class="filtro-select" id="filtroUsuario">
        <option value="">Todos los usuarios</option>
        @foreach($diarios->unique('user_id') as $diario)
            <option value="{{ $diario->user_id }}">
                {{ $diario->user->name }}
            </option>
        @endforeach
    </select>
    @endif

    <select class="filtro-select" id="filtroFecha">
        <option value="">Todas las fechas</option>
        <option value="hoy">Hoy</option>
        <option value="semana">Esta semana</option>
        <option value="mes">Este mes</option>
    </select>

    <select class="filtro-select" id="filtroEmocion">
        <option value="">Todas las emociones</option>
        <option value="positivo">Positivas</option>
        <option value="neutral">Neutrales</option>
        <option value="negativo">Negativas</option>
    </select>
</div>
