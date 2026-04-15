@extends('layouts.app')

@section('content')

<div class="page">

    <!-- CARD PRINCIPAL -->
    <div class="card">

        <!-- HEADER -->
        <div class="card-header">
            <div>
                <h2>Editar Paciente</h2>
                <p>Actualización de información clínica</p>
            </div>
        </div>

        <!-- BODY -->
        <div class="card-body">

            @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('pacientes.update', $paciente) }}"
                  method="POST"
                  class="form-grid">

                @csrf
                @method('PUT')

                <div class="field">
                    <label>Expediente</label>
                    <input name="numero_expediente"
                           value="{{ old('numero_expediente', $paciente->numero_expediente) }}">
                </div>

                <div class="field">
                    <label>Cédula</label>
                    <input name="cedula_paciente"
                           value="{{ old('cedula_paciente', $paciente->cedula_paciente) }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>

                <div class="field">
                    <label>Nombre completo</label>
                    <input name="nombre_completo"
                           value="{{ old('nombre_completo', $paciente->nombre_completo) }}"
                           oninput="this.value=this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g,'')">
                </div>

                <div class="field">
                    <label>Fecha de nacimiento</label>
                    <input type="date"
                           name="fecha_nacimiento"
                           value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento) }}">
                </div>

                <div class="field">
                    <label>Teléfono</label>
                    <input name="telefono"
                           value="{{ old('telefono', $paciente->telefono) }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>

                <div class="field">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $paciente->email) }}">
                </div>

                <div class="field">
                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option value="">Seleccione</option>
                        <option value="Alta" {{ $paciente->prioridad == 'Alta' ? 'selected' : '' }}>Alta</option>
                        <option value="Media" {{ $paciente->prioridad == 'Media' ? 'selected' : '' }}>Media</option>
                        <option value="Baja" {{ $paciente->prioridad == 'Baja' ? 'selected' : '' }}>Baja</option>
                    </select>
                </div>

                <div class="field full">
                    <label>Dirección</label>
                    <textarea name="direccion" rows="2">{{ old('direccion', $paciente->direccion) }}</textarea>
                </div>

                <div class="field full">
                    <label>Motivo de consulta</label>
                    <textarea name="motivo_consulta" rows="3">{{ old('motivo_consulta', $paciente->motivo_consulta) }}</textarea>
                </div>

                <div class="field full">
                    <label>Diagnóstico preliminar</label>
                    <textarea name="diagnostico_preliminar" rows="3">{{ old('diagnostico_preliminar', $paciente->diagnostico_preliminar) }}</textarea>
                </div>

                <!-- BOTONES -->
                <div class="actions">
                    <a href="{{ route('pacientes.index') }}" class="btn gray">
                        Volver
                    </a>

                    <button type="submit" class="btn purple">
                        Actualizar Paciente
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection

<!-- ================= ESTILOS UNIFICADOS ================= -->
<style>

.page {
    max-width: 1100px;
    margin: 0 auto;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    overflow: hidden;
}

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

.card-body {
    padding: 20px;
}

/* ERROR */
.alert-error {
    background: #fee2e2;
    color: #991b1b;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 13px;
}

.alert-error ul {
    margin: 0;
    padding-left: 18px;
}

/* FORM GRID */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

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

/* BOTONES */
.btn {
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13px;
    text-decoration: none;
    border: none;
    cursor: pointer;
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