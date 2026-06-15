@extends('layouts.app')

@section('content')
<link href="{{ asset('css/citas-create.css') }}" rel="stylesheet">

<div class="citas-form-wrapper">
    <div class="form-header">
        <div>
            <h2>
                <i class="fas fa-calendar-plus me-2"></i>
                @if(isset($reprogramando) && $reprogramando)
                    Reprogramar Cita
                @else
                    Nueva Cita
                @endif
            </h2>
            <p>
                @if(isset($reprogramando) && $reprogramando)
                    La cita anterior ha sido cancelada. Programa una nueva fecha (diferente a la original).
                @else
                    Programación de atención clínica del paciente
                @endif
            </p>
        </div>
        <a href="{{ route('citas.index') }}" class="btn-volver">
            <i class="fas fa-arrow-left me-2"></i>
            Volver al calendario
        </a>
    </div>

    @if(isset($reprogramando) && $reprogramando && $citaOriginal)
        <div class="alert-warning">
            <i class="fas fa-info-circle me-2" style="color: #f59e0b;"></i>
            <strong>Reprogramando cita</strong><br>
            Cita original del paciente <strong>{{ $citaOriginal->paciente->nombre_completo ?? 'N/A' }}</strong> 
            programada para el <strong>{{ $citaOriginal->fecha->format('d/m/Y') }}</strong> fue cancelada.
            <br><small>Motivo: {{ $citaOriginal->motivo_cancelacion ?? 'No especificado' }}</small>
            <br><small class="text-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Importante:</strong> Debes seleccionar una fecha diferente a la original.
            </small>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="form-card">
                <div class="card-header-custom">
                    <h5>
                        <i class="fas fa-calendar-check"></i>
                        @if(isset($reprogramando) && $reprogramando)
                            Reprogramar cita
                        @else
                            Agendado de citas
                        @endif
                    </h5>
                </div>
                <div class="form-section">
                    <form method="POST" action="{{ route('citas.store') }}" id="formCita">
                        @csrf
                        
                        @if(isset($reprogramando) && $reprogramando && $citaOriginal)
                            <input type="hidden" name="cita_original_id" value="{{ $citaOriginal->id }}">
                            <input type="hidden" id="fecha_original" value="{{ $citaOriginal->fecha->format('Y-m-d') }}">
                        @endif

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-user"></i>
                                    Paciente
                                </label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-user-circle input-icon"></i>
                                    <select name="paciente_id" id="paciente_id" class="form-select-custom w-100 input-with-icon" required>
                                        <option value="">Seleccionar paciente...</option>
                                        @foreach($pacientes as $paciente)
                                            <option value="{{ $paciente->id }}" 
                                                {{ (isset($citaOriginal) && $citaOriginal->paciente_id == $paciente->id) ? 'selected' : '' }}
                                                {{ old('paciente_id') == $paciente->id ? 'selected' : '' }}>
                                                {{ $paciente->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('paciente_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i>
                                    Nueva fecha de cita
                                </label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                    <input type="date"
                                           name="fecha"
                                           id="fecha_cita"
                                           class="form-input-custom w-100 input-with-icon"
                                           required
                                           min="{{ isset($citaOriginal) ? date('Y-m-d', strtotime($citaOriginal->fecha->format('Y-m-d') . ' +1 day')) : date('Y-m-d') }}"
                                           value="{{ old('fecha') }}">
                                </div>
                                
                                <div id="cupos-disponibles" class="mt-2" style="font-size: 13px;">
                                    <i class="fas fa-info-circle"></i>
                                    Selecciona un paciente y una fecha para verificar disponibilidad
                                </div>
                                
                                @error('fecha')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-bullseye"></i>
                                    Objetivo terapéutico
                                </label>
                                <div class="textarea-card">
                                    <textarea name="objetivo"
                                              rows="3"
                                              class="textarea-custom w-100"
                                              placeholder="Definir el objetivo principal de la sesión...">{{ old('objetivo', $citaOriginal->objetivo ?? '') }}</textarea>
                                </div>
                                @error('objetivo')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">
                                    <i class="fas fa-tasks"></i>
                                    Planificación de la sesión
                                </label>
                                <div class="textarea-card">
                                    <textarea name="planificacion"
                                              rows="5"
                                              class="textarea-custom w-100"
                                              placeholder="Estrategia, técnicas o enfoque terapéutico que se utilizarán durante la consulta...">{{ old('planificacion', $citaOriginal->planificacion ?? '') }}</textarea>
                                </div>
                                @error('planificacion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="buttons-container">
                            <a href="{{ route('citas.index') }}" class="btn-cancel">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-submit" id="btnSubmit">
                                <i class="fas fa-check-circle"></i>
                                @if(isset($reprogramando) && $reprogramando)
                                    Reprogramar cita
                                @else
                                    Crear cita
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM cargado - iniciando validación");
        
        var pacienteSelect = document.getElementById('paciente_id');
        var fechaInput = document.getElementById('fecha_cita');
        var cuposDiv = document.getElementById('cupos-disponibles');
        var btnSubmit = document.getElementById('btnSubmit');
        var fechaOriginalInput = document.getElementById('fecha_original');
        
        // Verificar si es reprogramación - ¡LÍNEA CORREGIDA!
        var esReprogramacion = @json(isset($reprogramando) && $reprogramando);
        var fechaOriginal = fechaOriginalInput ? fechaOriginalInput.value : null;
        
        if (!pacienteSelect || !fechaInput) {
            console.log("Elementos no encontrados");
            return;
        }
        
        function formatearFecha(fecha) {
            if (!fecha) return '';
            var partes = fecha.split('-');
            return partes[2] + '/' + partes[1] + '/' + partes[0];
        }
        
        function validarCita() {
            var fecha = fechaInput.value;
            var pacienteId = pacienteSelect.value;
            
            if (!fecha || !pacienteId) {
                if (cuposDiv) {
                    cuposDiv.innerHTML = '<div style="background:#f0fdf4; padding:10px; border-radius:8px;">Selecciona paciente y fecha</div>';
                }
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                }
                return;
            }
            
            // VALIDACIÓN: REPROGRAMACIÓN NO PUEDE SER MISMO DÍA
            if (esReprogramacion && fechaOriginal && fecha === fechaOriginal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Fecha no permitida',
                    html: 'No puedes reprogramar la cita para el <strong>mismo dia</strong> de la cita original cancelada.<br><br>' +
                          'Fecha original: <strong>' + formatearFecha(fechaOriginal) + '</strong><br>' +
                          'Por favor, selecciona una fecha diferente.',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Entendido',
                    backdrop: true,
                    allowOutsideClick: false
                });
                
                if (cuposDiv) {
                    cuposDiv.innerHTML = '<div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px;">❌ No puedes seleccionar la misma fecha de la cita cancelada</div>';
                }
                if (btnSubmit) {
                    btnSubmit.disabled = true;
                }
                return;
            }
            
            if (cuposDiv) {
                cuposDiv.innerHTML = '<div style="background:#e0f2fe; padding:10px; border-radius:8px;">Verificando...</div>';
            }
            
            // Construir URL con parámetros
            var url = '/citas/verificar?fecha=' + fecha + '&paciente_id=' + pacienteId;
            if (esReprogramacion && fechaOriginalInput) {
                url += '&cita_original_id=' + fechaOriginalInput.value;
            }
            
            fetch(url)
                .then(function(r) { 
                    return r.json(); 
                })
                .then(function(data) {
                    console.log("Respuesta:", data);
                    
                    // Verificar si es la misma fecha original (respuesta del servidor)
                    if (data.misma_fecha_original) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Fecha no permitida',
                            html: 'No puedes reprogramar la cita para el <strong>mismo dia</strong> de la cita original cancelada.<br><br>' +
                                  'Fecha original: <strong>' + formatearFecha(fechaOriginal) + '</strong>',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'Entendido'
                        });
                        
                        if (cuposDiv) {
                            cuposDiv.innerHTML = '<div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px;">❌ No puedes seleccionar la misma fecha de la cita cancelada</div>';
                        }
                        if (btnSubmit) {
                            btnSubmit.disabled = true;
                        }
                        return;
                    }
                    
                    if (data.tiene_cita) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Paciente ya tiene cita',
                            text: 'Este paciente ya tiene una cita programada para el dia ' + formatearFecha(fecha) + '. No puede tener dos citas el mismo dia.',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'Entendido',
                            backdrop: true,
                            timer: 4000,
                            showConfirmButton: true
                        });
                        
                        if (cuposDiv) {
                            cuposDiv.innerHTML = '<div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px;">❌ Este paciente YA tiene cita para esta fecha</div>';
                        }
                        if (btnSubmit) {
                            btnSubmit.disabled = true;
                        }
                    } 
                    else if (data.cupos_disponibles > 0) {
                        if (cuposDiv) {
                            cuposDiv.innerHTML = '<div style="background:#d1fae5; color:#065f46; padding:10px; border-radius:8px;">✅ Cupos disponibles: ' + data.cupos_disponibles + ' de 4</div>';
                        }
                        if (btnSubmit) {
                            btnSubmit.disabled = false;
                        }
                    } 
                    else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cupos agotados',
                            text: 'No hay cupos disponibles para el dia ' + formatearFecha(fecha) + '. Ya hay ' + data.citas_agendadas + ' de 4 citas agendadas.',
                            confirmButtonColor: '#ef4444',
                            confirmButtonText: 'Entendido',
                            backdrop: true,
                            timer: 4000,
                            showConfirmButton: true
                        });
                        
                        if (cuposDiv) {
                            cuposDiv.innerHTML = '<div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px;">❌ No hay cupos disponibles</div>';
                        }
                        if (btnSubmit) {
                            btnSubmit.disabled = true;
                        }
                    }
                })
                .catch(function(error) {
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexion',
                        text: 'No se pudo verificar la disponibilidad. Intenta nuevamente.',
                        confirmButtonColor: '#ef4444'
                    });
                    if (cuposDiv) {
                        cuposDiv.innerHTML = '<div style="background:#fee2e2; padding:10px;">Error al conectar</div>';
                    }
                });
        }
        
        // Asignar eventos
        pacienteSelect.onchange = validarCita;
        fechaInput.onchange = validarCita;
        
        // Validar al enviar el formulario
        var formCita = document.getElementById('formCita');
        if (formCita) {
            formCita.onsubmit = function(e) {
                // Validación extra antes de enviar
                if (esReprogramacion && fechaOriginal && fechaInput.value === fechaOriginal) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'No se puede reprogramar',
                        text: 'No puedes reprogramar la cita para el mismo dia de la cita original cancelada.',
                        confirmButtonColor: '#ef4444',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
                
                if (btnSubmit.disabled) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No se puede agendar',
                        text: 'Verifica que la fecha tenga cupos disponibles y que el paciente no tenga otra cita ese dia.',
                        confirmButtonColor: '#f59e0b',
                        confirmButtonText: 'Entendido'
                    });
                    return false;
                }
            };
        }
        
        // Validación inicial si ya hay valores seleccionados
        if (fechaInput.value && pacienteSelect.value) {
            validarCita();
        }
        
        console.log("✅ Validacion de citas activada correctamente");
        
        if (esReprogramacion) {
            console.log("Modo reprogramacion activado - Fecha original:", fechaOriginal);
        }
    });
</script>
@endpush
@endpush

@endsection