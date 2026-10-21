/**
 * Planes de Tratamiento - Listado con DataTables
 * Buscador general + filtros de estado y rango de fechas de inicio.
 */
(function () {
    'use strict';

    function quitarAcentos(texto) {
        return (texto == null ? '' : String(texto))
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase();
    }

    function fechaATimestamp(valor) {
        if (!valor) return null;
        var partes = String(valor).trim().split('/');
        if (partes.length === 3) {
            var ts = new Date(Number(partes[2]), Number(partes[1]) - 1, Number(partes[0]));
            return isNaN(ts.getTime()) ? null : ts.getTime();
        }
        var tsIso = Date.parse(valor);
        return isNaN(tsIso) ? null : tsIso;
    }

    $(function () {
        var $tabla = $('#tratamientoTable');
        if (!$tabla.length || typeof $.fn.DataTable === 'undefined') return;

        var $estado = $('#filtroEstado');
        var $desde = $('#filtroDesde');
        var $hasta = $('#filtroHasta');

        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            if (settings.nTable.id !== 'tratamientoTable') return true;

            var estado = $estado.val();
            if (estado && quitarAcentos(data[2]) !== estado) return false;

            var desde = $desde.val();
            var hasta = $hasta.val();
            if (desde || hasta) {
                var ts = fechaATimestamp(data[3]);
                if (ts === null) return false;
                if (desde && ts < Date.parse(desde)) return false;
                if (hasta && ts > Date.parse(hasta) + 86399999) return false;
            }

            return true;
        });

        var table = $tabla.DataTable({
            language: {
                search: 'Buscar:',
                searchPlaceholder: 'Buscar...',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Mostrando 0 a 0 de 0 registros',
                infoFiltered: '(filtrado de _MAX_ registros totales)',
                zeroRecords: 'No se encontraron resultados',
                emptyTable: 'No hay datos disponibles',
                paginate: { first: 'Primero', last: 'Último', next: 'Siguiente', previous: 'Anterior' }
            },
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'Todos']],
            order: [],
            columnDefs: [
                { orderable: false, searchable: false, targets: [5] },
                {
                    targets: '_all',
                    render: function (data, type) {
                        if (type === 'filter') return quitarAcentos(data);
                        return data;
                    }
                }
            ],
            dom: 'frtip'
        });

        function aplicarFiltros() {
            table.draw();
        }

        $estado.on('change', aplicarFiltros);
        $desde.on('change', aplicarFiltros);
        $hasta.on('change', aplicarFiltros);

        $('#filtroLimpiar').on('click', function () {
            $estado.val('');
            $desde.val('');
            $hasta.val('');
            table.search('');
            table.columns().search('');
            table.draw();
        });
    });
})();
