{{-- diarios/partials/empty-state.blade.php --}}
<div class="empty-state">
    <div class="empty-icon">
        <i class="fas fa-book"></i>
    </div>
    <h3>
        {{ Auth::id() == 1 ? 'No hay registros en el diario' : 'No tienes registros en tu diario' }}
    </h3>
    <p>
        {{ Auth::id() == 1 ? 'Comienza registrando el primer seguimiento emocional de tus pacientes' : 'Comienza escribiendo tu primer registro para hacer seguimiento de tus emociones' }}
    </p>
    <a href="{{ route('diarios.create') }}" class="btn-nuevo" style="display: inline-flex;">
        <i class="fas fa-plus-circle"></i>
        Crear primer registro
    </a>
</div>
