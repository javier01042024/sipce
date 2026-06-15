{{-- resources/views/diarios/index.blade.php --}}
@extends('layouts.app')

@section('content')

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
/* CONTENEDOR PRINCIPAL */
.diario-wrapper {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER COLORIDO */
.diario-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 35px;
    border-radius: 14px;
    margin-bottom: 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h1 {
    font-weight: 700;
    color: white;
    margin: 0;
    font-size: 28px;
}

.header-content p {
    color: rgba(255, 255, 255, 0.9);
    margin: 8px 0 0 0;
    font-size: 15px;
}

.btn-nuevo {
    background: white;
    color: #667eea;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.btn-nuevo:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #764ba2;
}

/* ESTADÍSTICAS RÁPIDAS */
.stats-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
}

.stat-icon.total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon.hoy {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.stat-icon.semana {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-info h4 {
    margin: 0 0 5px 0;
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
}

.stat-info .number {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
}

/* FILTROS */
.filtros-container {
    background: white;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filtro-select {
    flex: 1;
    min-width: 200px;
    padding: 10px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    color: #334155;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
}

.filtro-select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* DIARIOS GRID */
.diarios-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.diario-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    border: 1px solid rgba(102, 126, 234, 0.08);
    transition: all 0.3s;
    animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.diario-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.12);
}

.diario-header-card {
    padding: 20px 25px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-small {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 18px;
}

.user-detalle h4 {
    margin: 0 0 3px 0;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}

.user-detalle .fecha-badge {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
}

.emocion-badge {
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    gap: 5px;
}

.emocion-badge.positivo {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.emocion-badge.neutral {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
}

.emocion-badge.negativo {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.diario-body {
    padding: 25px;
}

.diario-contenido {
    color: #334155;
    line-height: 1.7;
    font-size: 15px;
    margin-bottom: 20px;
}

.diario-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

.diario-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.tag {
    padding: 4px 12px;
    background: #f1f5f9;
    border-radius: 20px;
    font-size: 11px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
}

.diario-actions {
    display: flex;
    gap: 8px;
}

.btn-icon-small {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-icon-small.view {
    background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
}

.btn-icon-small.edit {
    background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
}

.btn-icon-small.delete {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}

.btn-icon-small:hover {
    transform: translateY(-2px);
}

/* EMPTY STATE */
.empty-state {
    text-align: center;
    padding: 60px 30px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
}

.empty-icon {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #1e293b;
    margin-bottom: 10px;
    font-size: 20px;
}

.empty-state p {
    color: #64748b;
    margin-bottom: 25px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .diario-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px 20px;
    }
    
    .stats-container {
        grid-template-columns: 1fr;
    }
    
    .filtros-container {
        flex-direction: column;
    }
    
    .filtro-select {
        width: 100%;
    }
    
    .diario-header-card {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .user-info {
        flex-direction: column;
    }
    
    .diario-footer {
        flex-direction: column;
        gap: 15px;
    }
    
    .diario-actions {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="diario-wrapper">

    <!-- HEADER COLORIDO -->
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

    <!-- ESTADÍSTICAS -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h4>Total registros</h4>
                <div class="number">{{ $diarios->count() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon hoy">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h4>Registros hoy</h4>
                <div class="number">
                    {{ $diarios->where('fecha', date('Y-m-d'))->count() }}
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon semana">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-info">
                <h4>Esta semana</h4>
                <div class="number">
                    {{ $diarios->where('fecha', '>=', date('Y-m-d', strtotime('-7 days')))->count() }}
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
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

    <!-- LISTA DE DIARIOS -->
    @if($diarios->count() > 0)
        <div class="diarios-grid">
            @foreach($diarios as $diario)
                @php
                    $emocion = 'neutral';
                    $emocionIcon = '😐';
                    $emocionTexto = 'Neutral';
                    
                    if(str_contains(strtolower($diario->contenido), 'bien') || 
                       str_contains(strtolower($diario->contenido), 'feliz') ||
                       str_contains(strtolower($diario->contenido), 'contento') ||
                       str_contains(strtolower($diario->contenido), 'alegre') ||
                       str_contains(strtolower($diario->contenido), 'agradecido')) {
                        $emocion = 'positivo';
                        $emocionIcon = '😊';
                        $emocionTexto = 'Positivo';
                    } elseif(str_contains(strtolower($diario->contenido), 'mal') || 
                             str_contains(strtolower($diario->contenido), 'triste') ||
                             str_contains(strtolower($diario->contenido), 'ansioso') ||
                             str_contains(strtolower($diario->contenido), 'deprimido') ||
                             str_contains(strtolower($diario->contenido), 'enojado')) {
                        $emocion = 'negativo';
                        $emocionIcon = '😔';
                        $emocionTexto = 'Negativo';
                    }
                @endphp

                <div class="diario-card" data-usuario="{{ $diario->user_id }}" data-fecha="{{ $diario->fecha }}" data-emocion="{{ $emocion }}" id="diario-row-{{ $diario->id }}">
                    <div class="diario-header-card">
                        <div class="user-info">
                            <div class="user-avatar-small">
                                {{ strtoupper(substr($diario->user->name, 0, 1)) }}
                            </div>
                            <div class="user-detalle">
                                <h4>{{ $diario->user->name }}</h4>
                                <span class="fecha-badge">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="emocion-badge {{ $emocion }}">
                            <span>{{ $emocionIcon }}</span>
                            {{ $emocionTexto }}
                        </div>
                    </div>

                    <div class="diario-body">
                        <div class="diario-contenido">
                            <i class="fas fa-quote-left me-2" style="color: #cbd5e1; font-size: 12px;"></i>
                            {{ Str::limit($diario->contenido, 300) }}
                        </div>

                        <div class="diario-footer">
                            <div class="diario-tags">
                                <span class="tag">
                                    <i class="far fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($diario->created_at)->diffForHumans() }}
                                </span>
                                @if($diario->updated_at != $diario->created_at)
                                    <span class="tag">
                                        <i class="fas fa-edit"></i>
                                        Editado
                                    </span>
                                @endif
                            </div>

                            <div class="diario-actions">
                                <a href="{{ route('diarios.show', $diario) }}" class="btn-icon-small view" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('diarios.edit', $diario) }}" class="btn-icon-small edit" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <button class="btn-icon-small delete btn-eliminar-diario" 
                                        data-id="{{ $diario->id }}" 
                                        data-fecha="{{ \Carbon\Carbon::parse($diario->fecha)->format('d/m/Y') }}" 
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
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
    @endif

</div>

<script>
// Pasar variables de PHP a JavaScript (FUERA de cualquier función)
var sessionSuccess = @json(session('success'));
var sessionError = @json(session('error'));

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // ===== MOSTRAR MENSAJES DE SESIÓN =====
    if (sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: sessionSuccess,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }
    
    if (sessionError) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: sessionError,
            confirmButtonColor: '#667eea'
        });
    }
    
    // ===== FILTROS =====
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroEmocion = document.getElementById('filtroEmocion');
    const cards = document.querySelectorAll('.diario-card');

    function aplicarFiltros() {
        const usuarioSeleccionado = filtroUsuario ? filtroUsuario.value : '';
        const fechaSeleccionada = filtroFecha ? filtroFecha.value : '';
        const emocionSeleccionada = filtroEmocion ? filtroEmocion.value : '';

        cards.forEach(card => {
            let mostrar = true;

            if (filtroUsuario && usuarioSeleccionado && card.dataset.usuario !== usuarioSeleccionado) {
                mostrar = false;
            }

            if (mostrar && fechaSeleccionada) {
                const fechaCard = new Date(card.dataset.fecha);
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);

                if (fechaSeleccionada === 'hoy') {
                    const fechaCardDate = new Date(fechaCard);
                    fechaCardDate.setHours(0, 0, 0, 0);
                    if (fechaCardDate.getTime() !== hoy.getTime()) {
                        mostrar = false;
                    }
                } else if (fechaSeleccionada === 'semana') {
                    const unaSemanaAtras = new Date(hoy);
                    unaSemanaAtras.setDate(hoy.getDate() - 7);
                    if (fechaCard < unaSemanaAtras) {
                        mostrar = false;
                    }
                } else if (fechaSeleccionada === 'mes') {
                    const unMesAtras = new Date(hoy);
                    unMesAtras.setMonth(hoy.getMonth() - 1);
                    if (fechaCard < unMesAtras) {
                        mostrar = false;
                    }
                }
            }

            if (mostrar && emocionSeleccionada && card.dataset.emocion !== emocionSeleccionada) {
                mostrar = false;
            }

            card.style.display = mostrar ? 'block' : 'none';
        });
    }

    if (filtroUsuario) filtroUsuario.addEventListener('change', aplicarFiltros);
    if (filtroFecha) filtroFecha.addEventListener('change', aplicarFiltros);
    if (filtroEmocion) filtroEmocion.addEventListener('change', aplicarFiltros);

    // ===== ELIMINAR DIARIO =====
    document.querySelectorAll('.btn-eliminar-diario').forEach(btn => {
        btn.addEventListener('click', function() {
            const diarioId = this.getAttribute('data-id');
            const fecha = this.getAttribute('data-fecha');
            
            Swal.fire({
                title: '¿Estás seguro?',
                html: 'Se eliminará el registro del <strong>' + fecha + '</strong><br><small style="color: #64748b;">Esta acción no se puede deshacer</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fas fa-trash me-2"></i>Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times me-2"></i>Cancelar',
                reverseButtons: true
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('/diarios/' + diarioId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar');
                        }
                        
                        if (data.success) {
                            const row = document.getElementById('diario-row-' + diarioId);
                            if (row) {
                                row.style.transition = 'all 0.3s';
                                row.style.opacity = '0';
                                row.style.transform = 'scale(0.95)';
                                setTimeout(function() { row.remove(); }, 300);
                            }
                            
                            await Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: data.message || 'El registro ha sido eliminado correctamente',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            setTimeout(function() { window.location.reload(); }, 1500);
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'No se pudo eliminar el registro',
                            confirmButtonColor: '#667eea'
                        });
                    }
                }
            });
        });
    });
});
</script>

@endsection