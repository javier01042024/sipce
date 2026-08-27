{{-- diarios/partials/header.blade.php --}}
<div class="diario-header">
    <div class="header-content">
        <h1>
            <i class="fas fa-book-open me-2"></i>
            {{ Auth::id() == 1 ? 'Diario de Pacientes' : 'Mi Diario Personal' }}
        </h1>
        <p>
            {{ Auth::id() == 1 ? 'Registro diario emocional y seguimiento terapéutico' : 'Registro diario de mis pensamientos y emociones' }}
        </p>
    </div>

    <a href="{{ route('diarios.create') }}" class="btn-nuevo">
        <i class="fas fa-plus-circle"></i>
        Nuevo registro
    </a>
</div>
