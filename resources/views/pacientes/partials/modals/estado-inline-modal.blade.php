<div id="modalEstadoInline" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.5);z-index:9999999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:white;border-radius:16px;max-width:400px;width:95%;box-shadow:0 25px 60px rgba(0,0,0,0.3);overflow:hidden;">
        <div style="background:linear-gradient(135deg,var(--sipce-primary),var(--sipce-primary-dark));padding:16px 20px;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;color:white;font-size:16px;"><i class="fas fa-plus-circle"></i> Nuevo Estado</h3>
            <button type="button" onclick="cerrarModalEstado()" style="background:none;border:none;color:white;font-size:22px;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px;">
            <div style="margin-bottom:12px;">
                <label style="display:block;font-weight:600;color:#475569;font-size:13px;margin-bottom:4px;">Tipo *</label>
                <input type="text" id="estadoTipo" placeholder="Ej: Activo, Inactivo, Seguimiento..." style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-weight:600;color:#475569;font-size:13px;margin-bottom:4px;">Descripción *</label>
                <input type="text" id="estadoDescripcion" placeholder="Breve descripción del estado" style="width:100%;padding:10px 14px;border:2px solid #e2e8f0;border-radius:10px;font-size:14px;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;color:#475569;font-size:13px;">
                    <input type="checkbox" id="estadoPermiteCitas" style="width:18px;height:18px;accent-color:#11998e;">
                    Permite agendar citas
                </label>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="cerrarModalEstado()" style="padding:8px 18px;background:#f1f5f9;color:#64748b;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px;">Cancelar</button>
                <button type="button" id="btnGuardarEstado" onclick="guardarEstadoInline()" style="padding:8px 18px;background:linear-gradient(135deg,var(--sipce-primary),var(--sipce-primary-dark));color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:13px;">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
            <div id="estadoInlineError" style="display:none;margin-top:10px;padding:8px 12px;background:#fef2f2;color:#dc2626;border-radius:8px;font-size:13px;"></div>
        </div>
    </div>
</div>

<script>
function abrirModalEstado() {
    document.getElementById('estadoTipo').value = '';
    document.getElementById('estadoDescripcion').value = '';
    document.getElementById('estadoPermiteCitas').checked = false;
    document.getElementById('estadoInlineError').style.display = 'none';
    document.getElementById('modalEstadoInline').style.display = 'flex';
}

function cerrarModalEstado() {
    document.getElementById('modalEstadoInline').style.display = 'none';
}

function guardarEstadoInline() {
    var tipo = document.getElementById('estadoTipo').value.trim();
    var desc = document.getElementById('estadoDescripcion').value.trim();
    var errDiv = document.getElementById('estadoInlineError');
    var btn = document.getElementById('btnGuardarEstado');

    if (!tipo || !desc) {
        errDiv.textContent = 'Tipo y descripción son obligatorios.';
        errDiv.style.display = 'block';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

    fetch('{{ route("configuracion.estados.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            tipo: tipo,
            descripcion: desc,
            permite_citas: document.getElementById('estadoPermiteCitas').checked ? 1 : 0,
        }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var selects = document.querySelectorAll('select[name="estado_id"]');
            selects.forEach(function(sel) {
                var opt = document.createElement('option');
                opt.value = data.estado.id;
                opt.textContent = data.estado.tipo;
                sel.appendChild(opt);
            });
            selects.forEach(function(sel) {
                sel.value = data.estado.id;
            });
            cerrarModalEstado();
        } else {
            var msg = '';
            if (data.errors) {
                for (var k in data.errors) { msg += data.errors[k][0] + ' '; }
            } else {
                msg = data.message || 'Error al guardar.';
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar';
    })
    .catch(function() {
        errDiv.textContent = 'Error de conexión.';
        errDiv.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar';
    });
}
</script>
