document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // SISTEMA DE PESTAÑAS
    // ==========================================
    
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabPanels = document.querySelectorAll('.tab-panel');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Desactivar todos los botones y paneles
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanels.forEach(panel => panel.classList.remove('active'));
            
            // Activar el botón y panel seleccionado
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // ==========================================
    // TOGGLE FORMULARIOS
    // ==========================================
    
    // Toggle formulario de nota
    window.toggleFormNota = function() {
        const formContainer = document.getElementById('formNotaContainer');
        if (formContainer) {
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                formContainer.style.display = 'none';
            }
        }
    };
    
    // Toggle formulario de diario
    window.toggleFormDiario = function() {
        const formContainer = document.getElementById('formDiarioContainer');
        if (formContainer) {
            if (formContainer.style.display === 'none' || formContainer.style.display === '') {
                formContainer.style.display = 'block';
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                formContainer.style.display = 'none';
            }
        }
    };
    
});