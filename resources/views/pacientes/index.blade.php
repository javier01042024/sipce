@extends('layouts.app')

@section('content')

<div class="header">
    <div>
        <h1>Pacientes</h1>
        <p>Gestión clínica del sistema</p>
    </div>

    <button onclick="openModal()" class="btn-primary">
        <svg viewBox="0 0 24 24" width="18" height="18" stroke="white" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Nuevo
    </button>
</div>

@if(session('success'))
    <div class="alert">{{ session('success') }}</div>
@endif

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Expediente</th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Prioridad</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($pacientes as $paciente)
            <tr>
                <td class="strong">{{ $paciente->numero_expediente }}</td>
                <td>{{ $paciente->nombre_completo }}</td>
                <td>{{ $paciente->telefono }}</td>
                <td>
                    <span class="badge {{ $paciente->prioridad }}">
                        {{ $paciente->prioridad }}
                    </span>
                </td>

                <td class="actions">
                    <a href="{{ route('pacientes.show', $paciente) }}" class="btn-icon blue">
                        <svg viewBox="0 0 24 24">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </a>

                    <a href="{{ route('pacientes.edit', $paciente) }}" class="btn-icon purple">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                        </svg>
                    </a>

                    <form method="POST" action="{{ route('pacientes.destroy', $paciente) }}">
                        @csrf
                        @method('DELETE')

                        <button type="button" class="btn-icon red" onclick="openDeleteModal(this)">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M6 6l1 14h10l1-14"/>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="empty">No hay pacientes</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ================= MODAL CREAR ================= -->
<div id="modal" class="modal">
    <div class="modal-box">

        <div class="modal-header">
            <h2>Nuevo paciente</h2>
            <button onclick="closeModal()" class="close-btn">&times;</button>
        </div>

        <form action="{{ route('pacientes.store') }}" method="POST">
            @csrf

            <div class="grid">
                <input type="number" name="numero_expediente" placeholder="Expediente" class="input" required>
                <input  type="number"  name="cedula_paciente" placeholder="Cédula" class="input" required>
                <input name="nombre_completo" placeholder="Nombre completo" class="input" required>
                <input type="number" name="telefono" placeholder="Teléfono" class="input">
                <input name="email" placeholder="Email" class="input">
                <input type="date" name="fecha_nacimiento" class="input">

                <textarea name="direccion" placeholder="Dirección" class="input full" rows="2"></textarea>
                <textarea name="motivo_consulta" placeholder="Motivo de consulta" class="input full" rows="2"></textarea>
                <textarea name="diagnostico_preliminar" placeholder="Diagnóstico preliminar" class="input full" rows="2"></textarea>

                <select name="prioridad" class="input">
                    <option value="">Prioridad</option>
                    <option value="Alta">Alta</option>
                    <option value="Media">Media</option>
                    <option value="Baja">Baja</option>
                </select>
            </div>

            <div class="actions-modal">
                <button type="button" onclick="closeModal()" class="btn gray">Cancelar</button>
                <button class="btn green">Guardar</button>
            </div>

        </form>
    </div>
</div>

<!-- ================= MODAL DELETE ================= -->
<div id="deleteModal" class="modal">
    <div class="modal-box delete-box">

        <div class="danger-icon">!</div>

        <h3>Eliminar paciente</h3>

        <p>¿Seguro que deseas eliminar este paciente?<br>Esta acción no se puede deshacer.</p>

        <div class="actions-modal">
            <button onclick="closeDeleteModal()" class="btn gray">Cancelar</button>
            <button class="btn red" id="confirmDeleteBtn">Eliminar</button>
        </div>

    </div>
</div>

<!-- ================= CSS ================= -->
<style>
.header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

h1 { font-size:22px; margin:0; color:#0f172a; }
.header p { font-size:13px; color:#64748b; margin:4px 0 0; }

.btn-primary {
    display:flex;
    align-items:center;
    gap:8px;
    background:#2563eb;
    color:white;
    padding:10px 18px;
    border-radius:10px;
    border:none;
    cursor:pointer;
    font-weight:600;
}

.card {
    background:white;
    border-radius:16px;
    box-shadow:0 4px 6px rgba(0,0,0,0.05);
    overflow-x:auto;
}

table { width:100%; border-collapse:collapse; }

th {
    background:#0f172a;
    color:white;
    padding:12px;
    text-align:left;
}

td {
    padding:12px;
    border-bottom:1px solid #e2e8f0;
}

.actions { display:flex; gap:8px; }

.btn-icon {
    width:34px;
    height:34px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    border:none;
    cursor:pointer;
}

.blue { background:#0ea5e9; }
.purple { background:#6366f1; }
.red { background:#ef4444; }

.badge {
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
}

.modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;

    display: none !important;

    background: rgba(0,0,0,0.6) !important;

    z-index: 999999 !important;

    display: flex;
    align-items: center !important;
    justify-content: center !important;
}

/* activo */
.modal.show {
    display: flex !important;
}

/* CAJA DEL MODAL */
.modal-box {
    width: 90% !important;        /* 👈 tamaño base */
    max-width: 600px !important;   /* 👈 tamaño ideal escritorio */
    max-height: 80vh !important;   /* 👈 no ocupa toda la pantalla */

    background: #fff;
    border-radius: 12px !important;

    padding: 20px !important;

    overflow-y: auto !important;

    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.close-btn {
    font-size:28px;
    border:none;
    background:none;
    cursor:pointer;
}

.grid {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
}

.input {
    padding:10px;
    border:1px solid #e2e8f0;
    border-radius:10px;
    width:100%;
}

.full { grid-column:span 2; }

.actions-modal {
    display:flex;
    justify-content:flex-end;
    gap:10px;
    margin-top:20px;
}

.btn {
    padding:10px 16px;
    border-radius:10px;
    border:none;
    cursor:pointer;
}

.gray { background:#f1f5f9; }
.green { background:#16a34a; color:white; }
.red { background:#ef4444; color:white; }

.danger-icon {
    width:60px;
    height:60px;
    background:#ef4444;
    color:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:28px;
    margin:0 auto 20px;
}

.delete-box {
    text-align:center;
    width:420px;
}

@media (max-width:640px) {
    .grid { grid-template-columns:1fr; }
    .full { grid-column:span 1; }
}
</style>

<!-- ================= JS ================= -->
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
    if (formToDelete) formToDelete.submit();
});
</script>

@endsection