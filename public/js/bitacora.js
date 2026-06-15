document.addEventListener('DOMContentLoaded', function() {
    
    // Exportar bitácora
    const btnExport = document.getElementById('btnExport');
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            const form = document.getElementById('filtrosForm');
            if (form) {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                window.location.href = '/configuracion/bitacora/exportar?' + params.toString();
            }
        });
    }
    
    // Limpiar filtros
    const btnLimpiar = document.getElementById('btnLimpiar');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            const selects = document.querySelectorAll('.filtro-select');
            const inputs = document.querySelectorAll('.filtro-input');
            
            selects.forEach(select => select.selectedIndex = 0);
            inputs.forEach(input => input.value = '');
            
            window.location.href = window.location.pathname;
        });
    }
});