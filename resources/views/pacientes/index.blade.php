@extends('layouts.app')

@section('content')

<style>
/* CONTENEDOR PRINCIPAL */
.pacientes-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
}

/* HEADER COLORIDO */
.pacientes-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 35px;
    border-radius: 14px;
    margin-bottom: 35px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pacientes-header h1 {
    font-weight: 700;
    color: white;
    margin: 0;
    font-size: 28px;
}

.pacientes-header p {
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
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.btn-nuevo:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #764ba2;
}

/* ALERTA */
.alert-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-weight: 500;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* CARD Y TABLA */
.card {
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.12);
    border: 1px solid rgba(102, 126, 234, 0.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
}

th {
    color: white;
    font-weight: 600;
    font-size: 14px;
    padding: 16px;
    text-align: left;
}

td {
    padding: 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 14px;
}

tbody tr {
    transition: all 0.2s;
}

tbody tr:hover {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
}

/* BADGES COLORIDOS */
.badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge.Alta {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
    color: white;
}

.badge.Media {
    background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
    color: white;
}

.badge.Baja {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

/* BOTONES DE ACCIÓN */
.actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-icon:hover {
    transform: translateY(-2px);
}

.btn-view {
    background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%);
    box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
}

.btn-edit {
    background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
    box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
}

.btn-icon svg {
    width: 18px;
    height: 18px;
    stroke: white;
    stroke-width: 2;
    fill: none;
}

/* MODAL */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: none;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    z-index: 999999;
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-box {
    width: 90%;
    max-width: 650px;
    max-height: 85vh;
    background: white;
    border-radius: 20px;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(102, 126, 234, 0.25);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
}

.close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 28px;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.modal-body {
    padding: 25px;
    max-height: calc(85vh - 80px);
    overflow-y: auto;
}

/* FORMULARIO EN GRID */
.grid-form {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.input-group {
    position: relative;
}

.input-group.full-width {
    grid-column: span 2;
}

.input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 16px;
    z-index: 10;
}

.input-field {
    width: 100%;
    padding: 12px 12px 12px 42px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s;
    background: white;
}

.input-field:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

textarea.input-field {
    padding: 12px;
    resize: vertical;
}

select.input-field {
    padding-left: 42px;
    cursor: pointer;
}

/* BOTONES MODAL */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 20px 25px;
    border-top: 2px solid #f1f5f9;
}

.btn {
    padding: 12px 24px;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-cancel {
    background: #f1f5f9;
    color: #64748b;
}

.btn-cancel:hover {
    background: #e2e8f0;
}

.btn-save {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
}

.btn-delete-confirm {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-delete-confirm:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

/* MODAL DELETE */
.delete-box {
    text-align: center;
}

.danger-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
    margin: 10px auto 20px;
    box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
}

.delete-box h3 {
    color: #1e293b;
    margin-bottom: 10px;
}

.delete-box p {
    color: #64748b;
    margin-bottom: 30px;
}

.empty-message {
    text-align: center;
    padding: 50px;
    color: #94a3b8;
    font-size: 16px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .pacientes-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px 20px;
    }
    
    .grid-form {
        grid-template-columns: 1fr;
    }
    
    .input-group.full-width {
        grid-column: span 1;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
    
    .card {
        overflow-x: auto;
    }
    
    table {
        min-width: 800px;
    }
}

.strong {
    font-weight: 700;
    color: #667eea;
}
</style>

<div class="pacientes-wrapper">

    <!-- HEADER COLORIDO -->
    <div class="pacientes-header">
        <div>
            <h1>
                <i class="fas fa-users me-2"></i>
                Pacientes
            </h1>
            <p>Gestión clínica del sistema</p>
        </div>

        <button onclick="openModal()" class="btn-nuevo">
            <i class="fas fa-plus-circle"></i>
            Nuevo paciente
        </button>
    </div>

    <!-- ALERTA DE ÉXITO -->
    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- TABLA DE PACIENTES -->
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag me-1"></i> Expediente</th>
                    <th><i class="fas fa-user me-1"></i> Nombre completo</th>
                    <th><i class="fas fa-phone me-1"></i> Teléfono</th>
                    <th><i class="fas fa-flag me-1"></i> Prioridad</th>
                    <th><i class="fas fa-cog me-1"></i> Acciones</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($pacientes as $paciente)
                <tr>
                    <td class="strong">#{{ $paciente->numero_expediente }}</td>
                    <td>
                        <i class="fas fa-user-circle me-2" style="color: #667eea;"></i>
                        {{ $paciente->nombre_completo }}
                    </td>
                    <td>
                        <i class="fas fa-phone-alt me-2" style="color: #10b981;"></i>
                        {{ $paciente->telefono ?? 'No registrado' }}
                    </td>
                    <td>
                        <span class="badge {{ $paciente->prioridad }}">
                            {{ $paciente->prioridad ?? 'No definida' }}
                        </span>
                    </td>

                    <td class="actions">
                        <a href="{{ route('pacientes.show', $paciente) }}" class="btn-icon btn-view" title="Ver detalles">
                            <svg viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>

                        <a href="{{ route('pacientes.edit', $paciente) }}" class="btn-icon btn-edit" title="Editar">
                            <svg viewBox="0 0 24 24">
                                <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
                                <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
                            </svg>
                        </a>

                        <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-icon btn-delete" onclick="openDeleteModal(this)" title="Eliminar">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-message">
                            <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                            <p>No hay pacientes registrados</p>
                            <button onclick="openModal()" class="btn-nuevo" style="margin-top: 15px;">
                                <i class="fas fa-plus-circle"></i>
                                Crear primer paciente
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL CREAR PACIENTE -->
<div id="modal" class="modal">
    <div class="modal-box">
        <div class="modal-header">
            <h2>
                <i class="fas fa-user-plus me-2"></i>
                Nuevo paciente
            </h2>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>

        <form action="{{ route('pacientes.store') }}" method="POST">
            @csrf
            
            <div class="modal-body">
                <div class="grid-form">
                    <div class="input-group">
                        <i class="fas fa-hashtag input-icon"></i>
                        <input type="number" name="numero_expediente" placeholder="Número de expediente" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-id-card input-icon"></i>
                        <input type="number" name="cedula_paciente" placeholder="Cédula" class="input-field" required>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-user input-icon"></i>
                        <input name="nombre_completo" placeholder="Nombre completo" class="input-field" required>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="number" name="telefono" placeholder="Teléfono" class="input-field">
                    </div>

                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" placeholder="Correo electrónico" class="input-field">
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-calendar input-icon"></i>
                        <input type="date" name="fecha_nacimiento" class="input-field">
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <textarea name="direccion" placeholder="Dirección completa" class="input-field" rows="2"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-notes-medical input-icon"></i>
                        <textarea name="motivo_consulta" placeholder="Motivo de consulta" class="input-field" rows="2"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-stethoscope input-icon"></i>
                        <textarea name="diagnostico_preliminar" placeholder="Diagnóstico preliminar" class="input-field" rows="2"></textarea>
                    </div>

                    <div class="input-group full-width">
                        <i class="fas fa-flag input-icon"></i>
                        <select name="prioridad" class="input-field">
                            <option value="">Seleccionar prioridad</option>
                            <option value="Alta">🔴 Alta</option>
                            <option value="Media">🟡 Media</option>
                            <option value="Baja">🟢 Baja</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal()" class="btn btn-cancel">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </button>
                <button type="submit" class="btn btn-save">
                    <i class="fas fa-save me-2"></i>
                    Guardar paciente
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ELIMINAR -->
<div id="deleteModal" class="modal">
    <div class="modal-box" style="max-width: 450px;">
        <div class="modal-body delete-box">
            <div class="danger-icon">!</div>
            <h3>Eliminar paciente</h3>
            <p>¿Estás seguro de que deseas eliminar este paciente?<br>Esta acción no se puede deshacer.</p>
            
            <div class="modal-footer" style="border: none; padding: 0;">
                <button onclick="closeDeleteModal()" class="btn btn-cancel">
                    <i class="fas fa-times me-2"></i>
                    Cancelar
                </button>
                <button class="btn btn-delete-confirm" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FONT AWESOME -->
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endpush

<!-- JAVASCRIPT -->
<script>
let formToDelete = null;

function openModal() {
    document.getElementById('modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('modal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

function openDeleteModal(button) {
    formToDelete = button.closest('form');
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    formToDelete = null;
    document.getElementById('deleteModal').classList.remove('show');
}

document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
    if (formToDelete) {
        formToDelete.submit();
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal();
        closeDeleteModal();
    }
});

// Cerrar modal al hacer clic fuera
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
            closeDeleteModal();
        }
    });
});
</script>

@endsection