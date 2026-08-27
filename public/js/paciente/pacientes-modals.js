/**
 * ============================================
 * SISTEMA DE MODALES DE PACIENTES - SIPCE
 * ============================================
 * Funciones compartidas para los 3 modales:
 * - Adulto (7 pasos)
 * - Adolescente (7 pasos)
 * - Niño (7 pasos)
 * ============================================
 */

// ============================================
// FUNCIONES GENERALES DE MODALES
// ============================================

/**
 * Abre un modal específico por tipo
 * @param {string} tipo - 'adulto', 'adolescente', 'niño'
 */
function abrirModal(tipo) {
    const modalMap = {
        'adulto': 'modalAdulto',
        'adolescente': 'modalAdolescente',
        'niño': 'modalNino'
    };
    
    const modalId = modalMap[tipo];
    if (!modalId) return;
    
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Reiniciar al paso 1 según el tipo
        if (tipo === 'adulto') showStep('adulto', 1);
        else if (tipo === 'adolescente') showStep('adolescente', 1);
        else if (tipo === 'niño') showStep('niño', 1);
    }
}

/**
 * Cierra un modal específico por tipo
 * @param {string} tipo - 'adulto', 'adolescente', 'niño'
 */
function cerrarModal(tipo) {
    const modalMap = {
        'adulto': 'modalAdulto',
        'adolescente': 'modalAdolescente',
        'niño': 'modalNino'
    };
    
    const modalId = modalMap[tipo];
    if (!modalId) return;
    
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        
        // Resetear formulario
        const formMap = {
            'adulto': 'formAdulto',
            'adolescente': 'formAdolescente',
            'niño': 'formNino'
        };
        const formId = formMap[tipo];
        const form = document.getElementById(formId);
        if (form) form.reset();
    }
}

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        ['modalAdulto', 'modalAdolescente', 'modalNino'].forEach(function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal && modal.style.display === 'flex') {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }
});

// Cerrar modal al hacer clic fuera del contenido
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-paciente')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// ============================================
// SISTEMA DE PASOS (STEPS) UNIFICADO
// ============================================

/**
 * Configuración de cada tipo de modal
 */
const STEP_CONFIG = {
    'adulto': {
        totalSteps: 7,
        prefix: 'adulto',
        modalId: 'modalAdulto',
        titles: [
            'Datos Personales',
            'Motivo de Consulta',
            'Historia Familiar y Social',
            'Vida Sexual',
            'Evaluación Emocional y Conductual',
            'Antecedentes',
            'Recursos y Datos del Sistema'
        ]
    },
    'adolescente': {
        totalSteps: 7,
        prefix: 'adolescente',
        prefixCapitalized: 'Adol',
        modalId: 'modalAdolescente',
        titles: [
            'Datos Personales',
            'Datos Familiares',
            'Motivo de Consulta',
            'Historia del Problema',
            'Relaciones, Vida Social y Académica',
            'Salud, Consumo y Evaluación Emocional',
            'Recursos, Fortalezas y Datos del Sistema'
        ]
    },
    'niño': {
        totalSteps: 7,
        prefix: 'niño',
        prefixCapitalized: 'Nino',
        modalId: 'modalNino',
        titles: [
            'Datos Básicos',
            'Datos de los Padres',
            'Motivo de Consulta',
            'Historia Perinatal',
            'Desarrollo Evolutivo',
            'Área Escolar y Conductual',
            'Relaciones Sociales'
        ]
    }
};

// Variable para almacenar el paso actual de cada modal
let currentSteps = {
    'adulto': 1,
    'adolescente': 1,
    'niño': 1
};

/**
 * Muestra un paso específico del modal
 * @param {string} tipo - 'adulto', 'adolescente', 'niño'
 * @param {number} step - Número de paso a mostrar
 */
function showStep(tipo, step) {
    const config = STEP_CONFIG[tipo];
    if (!config) return;
    
    const prefix = config.prefix;
    const totalSteps = config.totalSteps;
    const titles = config.titles;
    
    // Capitalizar para IDs (adulto -> Adulto, adolescente -> Adolescente, niño -> Nino)
    const prefixCapitalized = config.prefixCapitalized || (prefix.charAt(0).toUpperCase() + prefix.slice(1));
    
    // Ocultar todos los pasos
    for (let i = 1; i <= totalSteps; i++) {
        const stepElement = document.getElementById('step' + prefixCapitalized + i);
        if (stepElement) {
            stepElement.style.display = 'none';
        }
    }
    
    // Mostrar el paso actual
    const currentStepElement = document.getElementById('step' + prefixCapitalized + step);
    if (currentStepElement) {
        currentStepElement.style.display = 'block';
    }
    
    // Actualizar barra de progreso
    const progressBar = document.getElementById('progressBar' + prefixCapitalized);
    if (progressBar) {
        progressBar.style.width = (step / totalSteps) * 100 + '%';
    }
    
    // Actualizar etiquetas
    const stepLabel = document.getElementById('stepLabel' + prefixCapitalized);
    if (stepLabel) {
        stepLabel.textContent = 'Paso ' + step + ' de ' + totalSteps;
    }
    
    const stepTitle = document.getElementById('stepTitle' + prefixCapitalized);
    if (stepTitle && titles[step - 1]) {
        stepTitle.textContent = titles[step - 1];
    }
    
    // Mostrar/ocultar botones
    const btnPrev = document.getElementById('btnPrev' + prefixCapitalized);
    const btnNext = document.getElementById('btnNext' + prefixCapitalized);
    const btnSubmit = document.getElementById('btnSubmit' + prefixCapitalized);
    
    if (btnPrev) btnPrev.style.display = step > 1 ? 'inline-flex' : 'none';
    if (btnNext) btnNext.style.display = step < totalSteps ? 'inline-flex' : 'none';
    if (btnSubmit) btnSubmit.style.display = step === totalSteps ? 'inline-flex' : 'none';
    
    // Scroll al inicio del modal
    const modalBody = document.querySelector('#' + (config.modalId || 'modal' + prefixCapitalized) + ' .modal-paciente-body');
    if (modalBody) {
        modalBody.scrollTop = 0;
    }
    
    // Actualizar paso actual
    currentSteps[tipo] = step;
}

/**
 * Avanza al siguiente paso
 * @param {string} tipo - 'adulto', 'adolescente', 'niño'
 */
function nextStep(tipo) {
    const config = STEP_CONFIG[tipo];
    if (!config) return;
    
    const currentStep = currentSteps[tipo] || 1;
    if (currentStep < config.totalSteps) {
        showStep(tipo, currentStep + 1);
    }
}

/**
 * Retrocede al paso anterior
 * @param {string} tipo - 'adulto', 'adolescente', 'niño'
 */
function prevStep(tipo) {
    const currentStep = currentSteps[tipo] || 1;
    if (currentStep > 1) {
        showStep(tipo, currentStep - 1);
    }
}

// ============================================
// FUNCIONES DE COMPATIBILIDAD (llamadas antiguas)
// Estas funciones se mantienen para no romper
// los onclick existentes en los modales
// ============================================

// Adulto
function showStepAdulto(step) { showStep('adulto', step); }
function nextStepAdulto() { nextStep('adulto'); }
function prevStepAdulto() { prevStep('adulto'); }

// Adolescente
function showStepAdol(step) { showStep('adolescente', step); }
function nextStepAdol() { nextStep('adolescente'); }
function prevStepAdol() { prevStep('adolescente'); }

// Niño
function showStepNino(step) { showStep('niño', step); }
function nextStepNino() { nextStep('niño'); }
function prevStepNino() { prevStep('niño'); }

// ============================================
// FORMATEO DE FECHA (DD/MM/AAAA)
// ============================================

/**
 * Formatea automáticamente un input de fecha
 * @param {HTMLInputElement} input - El input de fecha
 */
function formatearFecha(input) {
    let valor = input.value.replace(/\D/g, '');
    if (valor.length > 8) valor = valor.slice(0, 8);
    
    if (valor.length >= 5) {
        valor = valor.slice(0, 2) + '/' + valor.slice(2, 4) + '/' + valor.slice(4);
    } else if (valor.length >= 3) {
        valor = valor.slice(0, 2) + '/' + valor.slice(2);
    }
    
    input.value = valor;
}

// Aliases para compatibilidad con los modales existentes
function formatearFechaAdol(input) { formatearFecha(input); }
function formatearFechaNino(input) { formatearFecha(input); }

// ============================================
// VALIDACIÓN DE EDAD
// ============================================

/**
 * Valida la edad a partir de una fecha de nacimiento
 * @param {string} fechaStr - Fecha en formato DD/MM/AAAA
 * @returns {object} { valido: boolean, edad: number, mensaje: string }
 */
function validarEdad(fechaStr) {
    // Verificar formato
    if (!/^\d{2}\/\d{2}\/\d{4}$/.test(fechaStr)) {
        return { valido: false, edad: 0, mensaje: 'Ingrese una fecha válida en formato DD/MM/AAAA.' };
    }
    
    const partes = fechaStr.split('/');
    const dia = parseInt(partes[0], 10);
    const mes = parseInt(partes[1], 10);
    const anio = parseInt(partes[2], 10);
    
    // Validar mes
    if (mes < 1 || mes > 12) {
        return { valido: false, edad: 0, mensaje: 'El mes debe estar entre 01 y 12.' };
    }
    
    // Validar día
    const diasPorMes = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    if (mes === 2 && ((anio % 4 === 0 && anio % 100 !== 0) || anio % 400 === 0)) {
        diasPorMes[1] = 29;
    }
    
    if (dia < 1 || dia > diasPorMes[mes - 1]) {
        return { valido: false, edad: 0, mensaje: 'El día ingresado no es válido.' };
    }
    
    // Validar año
    const anioActual = new Date().getFullYear();
    if (anio < 1900 || anio > anioActual) {
        return { valido: false, edad: 0, mensaje: 'El año debe estar entre 1900 y ' + anioActual + '.' };
    }
    
    // Calcular edad
    const fechaNac = new Date(anio, mes - 1, dia);
    const hoy = new Date();
    
    if (fechaNac > hoy) {
        return { valido: false, edad: 0, mensaje: 'La fecha de nacimiento no puede ser futura.' };
    }
    
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const diferenciaMeses = hoy.getMonth() - fechaNac.getMonth();
    
    if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    
    return { valido: true, edad: edad, mensaje: '' };
}

// ============================================
// VALIDACIÓN DE CAMPOS REQUERIDOS
// ============================================

/**
 * Valida campos requeridos comunes a todos los formularios
 * @param {string} formId - ID del formulario
 * @param {string} tipo - 'adulto', 'adolescente', 'niño'
 * @returns {boolean}
 */
function validarCamposRequeridos(formId, tipo) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const nombre = form.querySelector('[name="nombre"]');
    const apellido = form.querySelector('[name="apellido"]');
    const fechaNacimiento = form.querySelector('[name="fecha_nacimiento"]');
    const motivoConsulta = form.querySelector('[name="motivo_consulta"]');
    const prioridad = form.querySelector('[name="prioridad"]');
    const estadoId = form.querySelector('[name="estado_id"]');
    const tipoAtencion = form.querySelector('[name="tipo_atencion"]');
    
    // Validar nombre
    if (!nombre || !nombre.value.trim()) {
        alert('Por favor, ingrese el nombre del paciente.');
        if (nombre) nombre.focus();
        showStep(tipo, 1);
        return false;
    }
    
    // Validar apellido
    if (!apellido || !apellido.value.trim()) {
        alert('Por favor, ingrese el apellido del paciente.');
        if (apellido) apellido.focus();
        showStep(tipo, 1);
        return false;
    }
    
    // Validar fecha de nacimiento
    if (!fechaNacimiento || !fechaNacimiento.value.trim()) {
        alert('Por favor, ingrese la fecha de nacimiento.');
        if (fechaNacimiento) fechaNacimiento.focus();
        showStep(tipo, 1);
        return false;
    }
    
    const resultadoEdad = validarEdad(fechaNacimiento.value);
    if (!resultadoEdad.valido) {
        alert(resultadoEdad.mensaje);
        if (fechaNacimiento) fechaNacimiento.focus();
        showStep(tipo, 1);
        return false;
    }
    
    // Validar edad según tipo de paciente
    const edad = resultadoEdad.edad;
    
    if (tipo === 'adulto' && edad < 18) {
        alert('El paciente adulto debe tener al menos 18 años de edad.');
        showStep(tipo, 1);
        return false;
    }
    
    if (tipo === 'adolescente' && (edad < 13 || edad > 17)) {
        alert('El paciente adolescente debe tener entre 13 y 17 años.\n\nEdad calculada: ' + edad + ' años.');
        showStep(tipo, 1);
        return false;
    }
    
    if (tipo === 'niño' && edad > 11) {
        alert('El paciente niño debe tener máximo 11 años.\n\nEdad calculada: ' + edad + ' años.');
        showStep(tipo, 1);
        return false;
    }
    
    if (edad > 120) {
        alert('Ingrese una fecha de nacimiento válida.');
        showStep(tipo, 1);
        return false;
    }
    
    // Validar motivo de consulta
    if (!motivoConsulta || !motivoConsulta.value.trim()) {
        alert('Por favor, ingrese el motivo de consulta.');
        if (motivoConsulta) motivoConsulta.focus();
        showStep(tipo, tipo === 'adulto' ? 3 : 3);
        return false;
    }
    
    // Validar prioridad
    if (!prioridad || !prioridad.value) {
        alert('Por favor, seleccione la prioridad.');
        if (prioridad) prioridad.focus();
        showStep(tipo, STEP_CONFIG[tipo].totalSteps);
        return false;
    }
    
    // Validar estado
    if (!estadoId || !estadoId.value) {
        alert('Por favor, seleccione el estado.');
        if (estadoId) estadoId.focus();
        showStep(tipo, STEP_CONFIG[tipo].totalSteps);
        return false;
    }
    
    // Validar tipo de atención
    if (!tipoAtencion || !tipoAtencion.value) {
        alert('Por favor, seleccione el tipo de atención.');
        if (tipoAtencion) tipoAtencion.focus();
        showStep(tipo, STEP_CONFIG[tipo].totalSteps);
        return false;
    }
    
    return true;
}

// ============================================
// FUNCIONES DE VALIDACIÓN DE FORMULARIOS
// (Compatibilidad con onsubmit existentes)
// ============================================

function validarFormAdulto() {
    return validarCamposRequeridos('formAdulto', 'adulto');
}

function validarFormAdolescente() {
    return validarCamposRequeridos('formAdolescente', 'adolescente');
}

function validarFormNino() {
    return validarCamposRequeridos('formNino', 'niño');
}

// ============================================
// INICIALIZACIÓN AL CARGAR LA PÁGINA
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos los modales en el paso 1
    if (document.getElementById('modalAdulto')) showStep('adulto', 1);
    if (document.getElementById('modalAdolescente')) showStep('adolescente', 1);
    if (document.getElementById('modalNino')) showStep('niño', 1);
});