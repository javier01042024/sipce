// ===== DIARIOS: INDEX =====
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // ===== FILTROS =====
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroEmocion = document.getElementById('filtroEmocion');
    const cards = document.querySelectorAll('.diario-card');

    function aplicarFiltros() {
        const usuarioSeleccionado = filtroUsuario ? filtroUsuario.value : '';
        const fechaSeleccionada = filtroFecha ? filtroFecha.value : '';
        const emocionSeleccionada = filtroEmocion ? filtroEmocion.value : '';

        cards.forEach(card => {
            let mostrar = true;

            if (filtroUsuario && usuarioSeleccionado && card.dataset.usuario !== usuarioSeleccionado) {
                mostrar = false;
            }

            if (mostrar && fechaSeleccionada) {
                const fechaCard = new Date(card.dataset.fecha);
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);

                if (fechaSeleccionada === 'hoy') {
                    const fechaCardDate = new Date(fechaCard);
                    fechaCardDate.setHours(0, 0, 0, 0);
                    if (fechaCardDate.getTime() !== hoy.getTime()) {
                        mostrar = false;
                    }
                } else if (fechaSeleccionada === 'semana') {
                    const unaSemanaAtras = new Date(hoy);
                    unaSemanaAtras.setDate(hoy.getDate() - 7);
                    if (fechaCard < unaSemanaAtras) {
                        mostrar = false;
                    }
                } else if (fechaSeleccionada === 'mes') {
                    const unMesAtras = new Date(hoy);
                    unMesAtras.setMonth(hoy.getMonth() - 1);
                    if (fechaCard < unMesAtras) {
                        mostrar = false;
                    }
                }
            }

            if (mostrar && emocionSeleccionada && card.dataset.emocion !== emocionSeleccionada) {
                mostrar = false;
            }

            card.style.display = mostrar ? 'block' : 'none';
        });
    }

    if (filtroUsuario) filtroUsuario.addEventListener('change', aplicarFiltros);
    if (filtroFecha) filtroFecha.addEventListener('change', aplicarFiltros);
    if (filtroEmocion) filtroEmocion.addEventListener('change', aplicarFiltros);

    // ===== ELIMINAR DIARIO =====
    document.querySelectorAll('.btn-eliminar-diario').forEach(btn => {
        btn.addEventListener('click', function() {
            const diarioId = this.getAttribute('data-id');
            const fecha = this.getAttribute('data-fecha');
            
            SIPCE_ALERT.confirmDelete({
                html: 'Se eliminará el registro del <strong>' + fecha + '</strong><br><small style="color: #64748b;">Esta acción no se puede deshacer</small>'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('/diarios/' + diarioId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Error al eliminar');
                        }
                        
                        if (data.success) {
                            const row = document.getElementById('diario-row-' + diarioId);
                            if (row) {
                                row.style.transition = 'all 0.3s';
                                row.style.opacity = '0';
                                row.style.transform = 'scale(0.95)';
                                setTimeout(function() { row.remove(); }, 300);
                            }
                            
                            await SIPCE_ALERT.success(data.message || 'El registro ha sido eliminado correctamente', '¡Eliminado!', { timer: 2000 });
                            
                            setTimeout(function() { window.location.reload(); }, 1500);
                        }
                    } catch (error) {
                        SIPCE_ALERT.error(error.message || 'No se pudo eliminar el registro');
                    }
                }
            });
        });
    });
});
