// ===== ESTADOS: INDEX =====

function abrirModalCrear() {
    document.getElementById('modalCrear').classList.add('active');
}
function cerrarModalCrear() {
    document.getElementById('modalCrear').classList.remove('active');
}

function editarEstado(id, tipo, descripcion, permite_citas) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_tipo').value = tipo;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('edit_permite_citas').checked = permite_citas;
    document.getElementById('formEditar').action = URL_BASE + '/' + id;
    document.getElementById('modalEditar').classList.add('active');
}
function cerrarModalEditar() {
    document.getElementById('modalEditar').classList.remove('active');
}

function verEstado(id) {
    fetch(URL_BASE + '/' + id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(e => {
        document.getElementById('verContenido').innerHTML = 
            '<div class="estado-detalle-header">' +
                '<div class="estado-detalle-avatar">🏷️</div>' +
                '<div class="estado-detalle-info"><h4>' + e.tipo + '</h4><span>ID: #' + e.id + '</span></div>' +
            '</div>' +
            '<div class="estado-detalle-grid">' +
                '<div class="estado-detalle-item estado-detalle-descripcion">' +
                    '<div class="label">Descripción</div><div class="value">' + e.descripcion + '</div>' +
                '</div>' +
                '<div class="estado-detalle-item">' +
                    '<div class="label">Permite Citas</div>' +
                    '<div class="value">' + (e.permite_citas ? '<span style="color:#11998e">✓ Sí</span>' : '<span style="color:#ef4444">✗ No</span>') + '</div>' +
                '</div>' +
                '<div class="estado-detalle-item">' +
                    '<div class="label">Pacientes</div>' +
                    '<div class="value"><strong>' + (e.pacientes_count || 0) + '</strong></div>' +
                '</div>' +
            '</div>';
        document.getElementById('modalVer').classList.add('active');
    });
}
function cerrarModalVer() {
    document.getElementById('modalVer').classList.remove('active');
}

function confirmarEliminar(id) {
    document.getElementById('formEliminar').action = URL_BASE + '/' + id;
    SIPCE_ALERT.confirmDelete({
        title: '¿Eliminar este estado?',
        text: 'Si hay pacientes asignados, no se podrá eliminar.'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('formEliminar').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.estado-modal-overlay').forEach(function(m) {
                m.classList.remove('active');
            });
        }
    });

    document.querySelectorAll('.estado-modal-overlay').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });
    });
});
