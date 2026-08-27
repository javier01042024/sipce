/**
 * ============================================
 * SISTEMA UNIFICADO DE ALERTAS - SIPCE
 * ============================================
 * Helper global para SweetAlert2.
 * Estándar de diseño:
 *  - Éxito (registro/actualización/eliminación): toast verde top-end de 3s.
 *  - Error / información / advertencia: modal centrado con botón "Entendido".
 *  - Confirmación de eliminación: modal warning con botón rojo "Sí, eliminar".
 *  - Confirmación de acción: modal question con botón de color por contexto.
 *  - Procesos largos: modal con spinner.
 * ============================================
 */

window.SIPCE_ALERT = (function() {
    'use strict';

    var colors = {
        success: '#11998e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#667eea',
        cancel: '#64748b'
    };

    // ==========================================
    // TOAST DE ÉXITO
    // ==========================================
    function success(message, title, options) {
        var base = {
            icon: 'success',
            title: title || '¡Éxito!',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            background: colors.success,
            color: '#ffffff',
            iconColor: '#ffffff'
        };
        if (message) base.html = message;
        return Swal.fire(Object.assign(base, options || {}));
    }

    // ==========================================
    // MODAL DE ERROR
    // ==========================================
    function error(message, title, options) {
        var base = {
            icon: 'error',
            title: title || 'Error',
            confirmButtonText: 'Entendido',
            confirmButtonColor: colors.error
        };
        if (message) base.html = message;
        return Swal.fire(Object.assign(base, options || {}));
    }

    // ==========================================
    // MODAL DE INFORMACIÓN
    // ==========================================
    function info(message, title, options) {
        var base = {
            icon: 'info',
            title: title || 'Información',
            confirmButtonText: 'Entendido',
            confirmButtonColor: colors.info
        };
        if (message) base.html = message;
        return Swal.fire(Object.assign(base, options || {}));
    }

    // ==========================================
    // MODAL DE ADVERTENCIA
    // ==========================================
    function warning(message, title, options) {
        var base = {
            icon: 'warning',
            title: title || 'Atención',
            confirmButtonText: 'Entendido',
            confirmButtonColor: colors.warning
        };
        if (message) base.html = message;
        return Swal.fire(Object.assign(base, options || {}));
    }

    // ==========================================
    // CONFIRMACIÓN DE ELIMINACIÓN
    // ==========================================
    function confirmDelete(options) {
        var opts = Object.assign({
            title: '¿Estás seguro?',
            text: '',
            html: '',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            icon: 'warning'
        }, options);

        return Swal.fire({
            title: opts.title,
            html: opts.html || opts.text || undefined,
            icon: opts.icon,
            showCancelButton: true,
            confirmButtonColor: colors.error,
            cancelButtonColor: colors.cancel,
            confirmButtonText: '<i class="fas fa-trash me-2"></i>' + opts.confirmText,
            cancelButtonText: '<i class="fas fa-times me-2"></i>' + opts.cancelText,
            reverseButtons: true,
            focusCancel: true
        });
    }

    // ==========================================
    // CONFIRMACIÓN DE ACCIÓN (NO DESTRUCTIVA)
    // ==========================================
    function confirm(options) {
        var opts = Object.assign({
            title: '¿Estás seguro?',
            text: '',
            html: '',
            confirmText: 'Confirmar',
            cancelText: 'Cancelar',
            confirmColor: colors.success,
            icon: 'question'
        }, options);

        return Swal.fire({
            title: opts.title,
            html: opts.html || opts.text || undefined,
            icon: opts.icon,
            showCancelButton: true,
            confirmButtonColor: opts.confirmColor,
            cancelButtonColor: colors.cancel,
            confirmButtonText: '<i class="fas fa-check me-2"></i>' + opts.confirmText,
            cancelButtonText: '<i class="fas fa-times me-2"></i>' + opts.cancelText,
            reverseButtons: true,
            focusCancel: true
        });
    }

    // ==========================================
    // MODAL DE CARGA
    // ==========================================
    function loading(title, text, options) {
        var base = {
            title: title || 'Procesando...',
            text: text || 'Por favor espera',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function() {
                Swal.showLoading();
            }
        };
        return Swal.fire(Object.assign(base, options || {}));
    }

    // ==========================================
    // MOSTRAR MENSAJES FLASH DE SESIÓN
    // ==========================================
    function flash() {
        var s = window.SIPCE_SESSION || {};
        if (s.error) {
            error(s.error);
            return true;
        }
        if (s.success) {
            success(s.success);
            return true;
        }
        if (s.validationErrors && s.validationErrors.length > 0) {
            error(s.validationErrors.join('<br>'), 'Errores de validación');
            return true;
        }
        return false;
    }

    return {
        colors: colors,
        success: success,
        error: error,
        info: info,
        warning: warning,
        confirmDelete: confirmDelete,
        confirm: confirm,
        loading: loading,
        flash: flash
    };
})();

// Auto-mostrar mensajes flash al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    window.SIPCE_ALERT.flash();
});
