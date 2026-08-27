{{-- configuracion/estados/partials/modals/show.blade.php --}}
<div id="modalVer" class="estado-modal-overlay">
    <div class="estado-modal">
        <div class="estado-modal-header">
            <h3>👁️ Detalles</h3>
            <button type="button" class="estado-modal-close" onclick="cerrarModalVer()">×</button>
        </div>
        <div class="estado-modal-body" id="verContenido">
            <p style="text-align:center;color:#94a3b8;">Cargando...</p>
        </div>
        <div class="estado-modal-footer">
            <button type="button" class="estado-btn estado-btn-cancelar" onclick="cerrarModalVer()">Cerrar</button>
        </div>
    </div>
</div>
