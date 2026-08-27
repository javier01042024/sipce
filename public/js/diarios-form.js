// ===== DIARIOS: FORMULARIO (CREATE / EDIT) =====

// Seleccionar emoción
function selectEmocion(element, tipo, textoBase) {
    // Remover selected de todas las opciones
    document.querySelectorAll('.emocion-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Agregar selected a la opción clickeada
    element.classList.add('selected');
    
    // Agregar texto sugerido al textarea
    const textarea = document.getElementById('contenido');
    if (textarea.value.trim() === '') {
        textarea.value = textoBase + ' ';
    } else {
        textarea.value = textarea.value + '\n\n' + textoBase + ' ';
    }
    
    // Actualizar contador
    updateCharCount();
    
    // Enfocar textarea
    textarea.focus();
}

// Agregar sugerencia
function agregarSugerencia(texto) {
    const textarea = document.getElementById('contenido');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    
    textarea.value = textBefore + texto + textAfter;
    textarea.focus();
    textarea.setSelectionRange(cursorPos + texto.length, cursorPos + texto.length);
    
    // Actualizar contador
    updateCharCount();
}

// Contador de caracteres
function updateCharCount() {
    const textarea = document.getElementById('contenido');
    const counter = document.getElementById('charCount');
    const counterDiv = document.getElementById('charCounter');
    const count = textarea.value.length;
    
    counter.textContent = count;
    
    // Cambiar color según cantidad
    counterDiv.classList.remove('warning', 'danger');
    if (count > 800) {
        counterDiv.classList.add('warning');
    }
    if (count > 950) {
        counterDiv.classList.add('danger');
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('contenido');
    textarea.addEventListener('input', updateCharCount);
    updateCharCount();
    
    // Establecer fecha máxima como hoy
    const fechaInput = document.getElementById('fechaInput');
    if (fechaInput) {
        const today = new Date().toISOString().split('T')[0];
        fechaInput.max = today;
    }
});

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'Enter') {
        document.getElementById('diarioForm').submit();
    }
});
