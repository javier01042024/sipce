@php use Illuminate\Support\Facades\DB; @endphp

<div class="modal-overlay" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Nuevo Usuario</h2>
            <button type="button" class="close-modal" id="btnCloseModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="userForm">
            @csrf
            <input type="hidden" id="userId">
            <input type="hidden" id="method" value="POST">
            
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user me-2"></i>Nombre Completo
                </label>
                <input type="text" class="form-control" id="name" name="name" required 
                       placeholder="Ingresa el nombre completo">
            </div>
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope me-2"></i>Correo ElectrÃ³nico
                </label>
                <input type="email" class="form-control" id="email" name="email" required 
                       placeholder="ejemplo@correo.com">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock me-2"></i>ContraseÃ±a
                </label>
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="MÃ­nimo 8 caracteres">
                <small class="text-muted" id="passwordHelp" style="display:none;">
                    Dejar en blanco para mantener la contraseÃ±a actual
                </small>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">
                    <i class="fas fa-lock me-2"></i>Confirmar ContraseÃ±a
                </label>
                <input type="password" class="form-control" id="password_confirmation" 
                       name="password_confirmation" placeholder="Repite la contraseÃ±a">
            </div>
            
            <div class="form-group">
                <label for="role">
                    <i class="fas fa-user-tag me-2"></i>Rol
                </label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Selecciona un rol</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8fafc; border-radius: 10px;">
                    <input type="checkbox" id="es_paciente" name="es_paciente" value="1" style="width: 20px; height: 20px;">
                    <label for="es_paciente" style="margin: 0; font-weight: 600; color: #1e293b;">
                        <i class="fas fa-hospital-user me-2" style="color: var(--sipce-primary);"></i>
                        Este usuario es un paciente
                    </label>
                </div>
                <small class="text-muted">Si es paciente, podrÃ¡ acceder al sistema y escribir en su diario</small>
            </div>
            
            <div class="form-group" id="pacienteSelectGroup" style="display: none;">
                <label for="paciente_id">
                    <i class="fas fa-user-circle me-2"></i>Seleccionar Paciente
                </label>
                <select class="form-control" id="paciente_id" name="paciente_id">
                    <option value="">Seleccione un paciente...</option>
                    @foreach($pacientesSinUsuario as $p)
                    <option value="{{ $p->id }}">
                        Exp: {{ $p->numero_expediente }} | {{ DB::table('paciente_adultos')->where('id', $p->paciente_detalle_id)->value('nombre') }} {{ DB::table('paciente_adultos')->where('id', $p->paciente_detalle_id)->value('apellido') }}
                    </option>
                    @endforeach
                </select>
                <small class="text-muted">Solo se muestran pacientes que no tienen usuario asignado</small>
            </div>
            
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-save me-2"></i>
                Guardar Usuario
            </button>
        </form>
    </div>
</div>