<div class="empty-state">
    <i class="fas fa-calendar-plus"></i>
    <p>No tienes citas programadas</p>
    @if(Auth::user()->hasPermission('citas.create'))
    <a href="{{ route('citas.create') }}" class="btn-nueva-cita">
        <i class="fas fa-plus-circle"></i>
        Agendar Primera Cita
    </a>
    @endif
</div>