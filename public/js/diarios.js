// ===== DIARIOS: INDEX =====
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // ===== FILTROS =====
    const filtroBusqueda = document.getElementById('filtroBusqueda');
    const filtroUsuario = document.getElementById('filtroUsuario');
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroEmocion = document.getElementById('filtroEmocion');
    const filtroDesde = document.getElementById('filtroDesde');
    const filtroHasta = document.getElementById('filtroHasta');
    const filtroLimpiar = document.getElementById('filtroLimpiar');
    const contador = document.getElementById('filtrosContador');
    const sinResultados = document.getElementById('diariosVacio');
    const grid = document.querySelector('.diarios-grid');
    const cards = Array.prototype.slice.call(document.querySelectorAll('.diario-card'));
    const total = cards.length;
    let temporizadorBusqueda;

    function quitarAcentos(texto) {
        return (texto == null ? '' : String(texto))
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    function parseFecha(valor) {
        if (!valor) return null;
        const partes = String(valor).trim().split(/[T ]/)[0].split('-');
        if (partes.length === 3) {
            const fecha = new Date(Number(partes[0]), Number(partes[1]) - 1, Number(partes[2]));
            return isNaN(fecha.getTime()) ? null : fecha;
        }
        const fecha = new Date(valor);
        return isNaN(fecha.getTime()) ? null : fecha;
    }

    function textoBusqueda(card) {
        return quitarAcentos(
            (card.dataset.nombre || '') + ' ' +
            (card.dataset.contenido || '') + ' ' +
            (card.dataset.fecha || '') + ' ' +
            (card.textContent || '')
        );
    }

    function dentroDeRango(card, desde, hasta) {
        if (!desde && !hasta) return true;
        const fechaCard = parseFecha(card.dataset.fecha);
        if (!fechaCard) return false;
        const ts = fechaCard.getTime();
        const inicioDesde = desde ? parseFecha(desde) : null;
        const finHasta = hasta ? parseFecha(hasta) : null;
        if (inicioDesde && ts < inicioDesde.getTime()) return false;
        if (finHasta && ts > finHasta.getTime()) return false;
        return true;
    }

    function coincideRangoRapido(card, rango) {
        if (!rango) return true;
        const fechaCard = parseFecha(card.dataset.fecha);
        if (!fechaCard) return false;

        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);

        if (rango === 'hoy') {
            return fechaCard.getTime() === hoy.getTime();
        }

        if (rango === 'semana') {
            const unaSemanaAtras = new Date(hoy);
            unaSemanaAtras.setDate(hoy.getDate() - 7);
            return fechaCard.getTime() >= unaSemanaAtras.getTime();
        }

        if (rango === 'mes') {
            const unMesAtras = new Date(hoy);
            unMesAtras.setMonth(hoy.getMonth() - 1);
            return fechaCard.getTime() >= unMesAtras.getTime();
        }

        return true;
    }

    function aplicarFiltros() {
        const consulta = quitarAcentos((filtroBusqueda ? filtroBusqueda.value : '').trim());
        const usuarioSeleccionado = filtroUsuario ? filtroUsuario.value : '';
        const fechaSeleccionada = filtroFecha ? filtroFecha.value : '';
        const emocionSeleccionada = filtroEmocion ? filtroEmocion.value : '';
        const desde = filtroDesde ? filtroDesde.value : '';
        const hasta = filtroHasta ? filtroHasta.value : '';

        let visibles = 0;

        cards.forEach(function(card) {
            let mostrar = true;

            if (consulta && textoBusqueda(card).indexOf(consulta) === -1) {
                mostrar = false;
            }

            if (mostrar && usuarioSeleccionado && card.dataset.usuario !== usuarioSeleccionado) {
                mostrar = false;
            }

            if (mostrar && emocionSeleccionada && card.dataset.emocion !== emocionSeleccionada) {
                mostrar = false;
            }

            if (mostrar && !coincideRangoRapido(card, fechaSeleccionada)) {
                mostrar = false;
            }

            if (mostrar && !dentroDeRango(card, desde, hasta)) {
                mostrar = false;
            }

            card.style.display = mostrar ? '' : 'none';
            if (mostrar) visibles++;
        });

        if (contador) {
            contador.textContent = 'Mostrando ' + visibles + ' de ' + total + ' registros';
        }

        if (grid) {
            grid.style.display = visibles > 0 ? '' : 'none';
        }

        if (sinResultados) {
            sinResultados.style.display = visibles > 0 ? 'none' : 'block';
        }
    }

    function limpiarFiltros() {
        if (filtroBusqueda) filtroBusqueda.value = '';
        if (filtroUsuario) filtroUsuario.value = '';
        if (filtroFecha) filtroFecha.value = '';
        if (filtroEmocion) filtroEmocion.value = '';
        if (filtroDesde) filtroDesde.value = '';
        if (filtroHasta) filtroHasta.value = '';
        aplicarFiltros();
        if (filtroBusqueda) filtroBusqueda.focus();
    }

    if (filtroBusqueda) {
        filtroBusqueda.addEventListener('input', function() {
            clearTimeout(temporizadorBusqueda);
            temporizadorBusqueda = setTimeout(aplicarFiltros, 200);
        });
    }

    [filtroUsuario, filtroFecha, filtroEmocion, filtroDesde, filtroHasta].forEach(function(control) {
        if (control) control.addEventListener('change', aplicarFiltros);
    });

    if (filtroLimpiar) filtroLimpiar.addEventListener('click', limpiarFiltros);

    aplicarFiltros();

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
