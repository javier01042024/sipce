<div id="modalDiagnostico" class="modal-paciente">
    <div class="modal-paciente-box" style="max-width: 550px;">
        <div class="modal-paciente-header">
            <h2 id="tituloDiagnostico"><i class="fas fa-stethoscope"></i> Nuevo Diagnóstico</h2>
            <button type="button" class="modal-paciente-close" onclick="cerrarModalDiagnostico()">&times;</button>
        </div>
        <div class="modal-paciente-body">
            <form id="formDiagnostico" method="POST">
                @csrf
                <input type="hidden" id="diagnosticoMethod" name="_method" value="POST">
                <input type="hidden" id="diagnosticoId" value="">

                <div class="field" style="margin-bottom: 12px;">
                    <label style="font-weight: 600; color: #475569; font-size: 13px;">Código CIE-10</label>
                    <input type="text" name="codigo_cie" id="diagnosticoCodigo" class="input-field" placeholder="Ej: F32.0">
                </div>

                <div class="field" style="margin-bottom: 12px;">
                    <label style="font-weight: 600; color: #475569; font-size: 13px;">Diagnóstico *</label>
                    <textarea name="diagnostico" id="diagnosticoNombre" class="input-field" rows="3" required placeholder="Descripción del diagnóstico..."></textarea>
                </div>

                <div class="field" style="margin-bottom: 12px;">
                    <label style="font-weight: 600; color: #475569; font-size: 13px;">Observaciones</label>
                    <textarea name="observaciones" id="diagnosticoObs" class="input-field" rows="2" placeholder="Notas adicionales..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <div class="field">
                        <label style="font-weight: 600; color: #475569; font-size: 13px;">Fecha</label>
                        <input type="date" name="fecha" id="diagnosticoFecha" value="{{ date('Y-m-d') }}" class="input-field">
                    </div>
                    <div class="field" style="display:flex;align-items:flex-end;padding-bottom:2px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;color:#475569;font-size:13px;">
                            <input type="checkbox" name="es_principal" id="diagnosticoPrincipal" value="1" style="width:18px;height:18px;accent-color:#11998e;">
                            Diagnóstico principal
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-paciente-footer">
            <button type="button" class="btn-paciente btn-paciente-cancel" onclick="cerrarModalDiagnostico()">Cancelar</button>
            <button type="button" class="btn-paciente btn-paciente-save" onclick="guardarDiagnostico()" style="background: #11998e;"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </div>
</div>

<script>
function abrirModalDiagnostico(pacienteId) {
    var modal = document.getElementById('modalDiagnostico');
    var form = document.getElementById('formDiagnostico');
    form.action = '/pacientes/' + pacienteId + '/diagnosticos';
    document.getElementById('diagnosticoMethod').value = 'POST';
    document.getElementById('diagnosticoId').value = '';
    document.getElementById('tituloDiagnostico').innerHTML = '<i class="fas fa-stethoscope"></i> Nuevo Diagnóstico';
    document.getElementById('diagnosticoCodigo').value = '';
    document.getElementById('diagnosticoNombre').value = '';
    document.getElementById('diagnosticoObs').value = '';
    document.getElementById('diagnosticoFecha').value = '{{ date("Y-m-d") }}';
    document.getElementById('diagnosticoPrincipal').checked = false;
    modal.classList.add('show');
}

function editarDiagnostico(pacienteId, diag) {
    var modal = document.getElementById('modalDiagnostico');
    var form = document.getElementById('formDiagnostico');
    form.action = '/diagnosticos/' + diag.id;
    document.getElementById('diagnosticoMethod').value = 'PUT';
    document.getElementById('diagnosticoId').value = diag.id;
    document.getElementById('tituloDiagnostico').innerHTML = '<i class="fas fa-stethoscope"></i> Editar Diagnóstico';
    document.getElementById('diagnosticoCodigo').value = diag.codigo_cie || '';
    document.getElementById('diagnosticoNombre').value = diag.diagnostico || '';
    document.getElementById('diagnosticoObs').value = diag.observaciones || '';
    document.getElementById('diagnosticoFecha').value = diag.fecha ? diag.fecha.date || diag.fecha : '';
    document.getElementById('diagnosticoPrincipal').checked = diag.es_principal;
    modal.classList.add('show');
}

function cerrarModalDiagnostico() {
    document.getElementById('modalDiagnostico').classList.remove('show');
}

function guardarDiagnostico() {
    if (!document.getElementById('diagnosticoNombre').value.trim()) {
        alert('El campo Diagnóstico es obligatorio.');
        return;
    }
    document.getElementById('formDiagnostico').submit();
}

function eliminarDiagnostico(diagId) {
    if (!confirm('¿Eliminar este diagnóstico?')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/diagnosticos/' + diagId;
    var csrf = document.querySelector('meta[name="csrf-token"]');
    form.innerHTML = '<input type="hidden" name="_token" value="' + (csrf ? csrf.content : '') + '"><input type="hidden" name="_method" value="DELETE">';
    document.body.appendChild(form);
    form.submit();
}
</script>
