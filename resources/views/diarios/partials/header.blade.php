{{-- diarios/partials/header.blade.php --}}
@php
    $u = Auth::user();
    $esLector = $u->isAdmin() || $u->hasRole('psicologa');
@endphp
<div class="diario-header">
    <div class="header-content">
        <h1>
            <i class="fas fa-book-open me-2"></i>
            {{ $esLector ? 'Diario de Pacientes' : 'Mi Diario Personal' }}
        </h1>
        <p>
            {{ $esLector ? 'Registro diario emocional y seguimiento terapéutico' : 'Registro diario de mis pensamientos y emociones' }}
        </p>
    </div>

    @if($u->hasPermission('diarios.create'))
    <a href="{{ route('diarios.create') }}" class="btn-nuevo">
        <i class="fas fa-plus-circle"></i>
        Nuevo registro
    </a>
    @endif
</div>
