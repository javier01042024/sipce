document.addEventListener('DOMContentLoaded', function() {
    
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