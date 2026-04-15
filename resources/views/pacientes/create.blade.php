@extends('layouts.app')

@section('content')

<!-- ALERTAS -->
@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="page">

    <!-- CARD -->
    <div class="card">

        <!-- HEADER -->
        <div class="card-header">
            <div>
                <h2>Registrar Paciente</h2>
                <p>Módulo clínico SIPCE</p>
            </div>
        </div>

        <!-- BODY -->
        <div class="card-body">

            <form action="{{ route('pacientes.store') }}" method="POST" class="form-grid">
                @csrf

                <div class="field">
                    <label>Expediente</label>
                    <input name="numero_expediente" value="{{ old('numero_expediente') }}">
                </div>

                <div class="field">
                    <label>Cédula</label>
                    <input name="cedula_paciente"
                           value="{{ old('cedula_paciente') }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>

                <div class="field">
                    <label>Nombre completo</label>
                    <input name="nombre_completo"
                           value="{{ old('nombre_completo') }}"
                           oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')">
                </div>

                <div class="field">
                    <label>Teléfono</label>
                    <input name="telefono"
                           value="{{ old('telefono') }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>

                <div class="field">
                    <label>Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}">
                </div>

                <div class="field full">
                    <label>Dirección</label>
                    <textarea name="direccion" rows="2">{{ old('direccion') }}</textarea>
                </div>

                <div class="field full">
                    <label>Motivo de consulta</label>
                    <textarea name="motivo_consulta" rows="3">{{ old('motivo_consulta') }}</textarea>
                </div>

                <div class="field full">
                    <label>Diagnóstico preliminar</label>
                    <textarea name="diagnostico_preliminar" rows="3">{{ old('diagnostico_preliminar') }}</textarea>
                </div>

                <div class="field">
                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option value="">Seleccione</option>
                        <option value="Alta">Alta</option>
                        <option value="Media">Media</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>

                <!-- BOTONES -->
                <div class="actions">
                    <a href="{{ route('pacientes.index') }}" class="btn gray">
                        Volver
                    </a>

                    <button type="submit" class="btn purple">
                        Guardar Paciente
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

@endsection

<!-- ================= ESTILOS UNIFICADOS ================= -->
<style>

/* PAGE */
.page {
    max-width: 1100px;
    margin: 0 auto;
}

/* CARD */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    overflow: hidden;
}

/* HEADER */
.card-header {
    background: #6366f1;
    color: white;
    padding: 18px 20px;
}

.card-header h2 {
    margin: 0;
    font-size: 20px;
}

.card-header p {
    margin: 0;
    font-size: 13px;
    opacity: 0.85;
}

/* BODY */
.card-body {
    padding: 20px;
}

/* ALERTAS */
.alert-success {
    background: #dcfce7;
    color: #166534;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin: 15px auto;
    max-width: 1100px;
    border-left: 4px solid #22c55e;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 13px;
    margin: 15px auto;
    max-width: 1100px;
    border-left: 4px solid #ef4444;
}

.alert-error ul {
    margin: 0;
    padding-left: 18px;
}

/* FORM */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

/* FIELD */
.field {
    display: flex;
    flex-direction: column;
}

.field label {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #334155;
}

.field input,
.field select,
.field textarea {
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    font-size: 14px;
    outline: none;
    transition: 0.2s;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

.full {
    grid-column: span 2;
}

/* ACTIONS */
.actions {
    grid-column: span 2;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 10px;
}

/* BUTTONS */
.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.gray {
    background: #e2e8f0;
    color: #334155;
}

.gray:hover {
    background: #cbd5e1;
}

.purple {
    background: #6366f1;
    color: white;
}

.purple:hover {
    background: #4f46e5;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .full, .actions {
        grid-column: span 1;
    }

    .actions {
        flex-direction: column;
    }
}

</style>