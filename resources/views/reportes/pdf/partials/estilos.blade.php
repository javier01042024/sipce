<style>
    /* ============================================================
       PLANTILLA INSTITUCIONAL SIPCE — hoja de estilos común para
       TODOS los PDF del sistema (reportes y expediente clínico).
       Un solo diseño para garantizar una apariencia uniforme.
       ============================================================ */
    @page { margin: 90px 26px 60px 26px; }
    * { box-sizing: border-box; }
    body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 9px; line-height: 1.45; margin: 0; }

    /* Encabezado institucional (se repite en cada página) */
    .institucional { text-align: center; border-bottom: 3px solid #1F3864; padding-bottom: 7px; margin-bottom: 4px; }
    .institucional .org { font-size: 11px; font-weight: bold; color: #1F3864; letter-spacing: 0.6px; margin: 0; }
    .institucional .sub { font-size: 8.5px; color: #334155; margin: 2px 0 0 0; }
    .institucional .prog { font-size: 8.5px; font-weight: bold; color: #1F3864; margin: 2px 0 0 0; }

    /* Título y metadatos */
    .titulo { text-align: center; font-size: 14px; font-weight: bold; color: #1F3864; margin: 11px 0 3px 0; letter-spacing: 1px; text-transform: uppercase; }
    .meta { text-align: center; font-size: 8px; color: #64748b; font-style: italic; margin: 0 0 3px 0; }
    .filtros { font-size: 8px; color: #1F4E78; font-style: italic; margin: 0 0 9px 0; text-align: center; }
    .resumen { font-size: 9px; font-weight: bold; color: #1F3864; margin: 10px 0 6px 0; padding: 6px 10px; background: #eff3fa; border-left: 3px solid #1F3864; }

    /* Tablas de datos (las del encabezado institucional y firmas quedan exentas) */
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    thead { display: table-header-group; }
    thead th { background: #1F3864; color: #ffffff; padding: 6px 5px; text-align: center; font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; border: 0.5px solid #16294a; }
    tbody td { padding: 4px 5px; border: 0.5px solid #cbd5e1; font-size: 8px; vertical-align: top; word-wrap: break-word; }
    tbody tr:nth-child(even) td { background: #eff3fa; }
    .center { text-align: center; }
    .totales { margin-bottom: 10px; }
    .totales td { border: none; padding-top: 6px; }

    /* Las tablas institucional y de firmas no son tablas de datos */
    table.institucional,
    table.institucional td,
    table.firmas,
    table.firmas td { border: none; background: transparent; padding: 0; }
    table.institucional tbody tr:nth-child(even) td,
    table.firmas tbody tr:nth-child(even) td { background: transparent; }

    /* Firmas */
    .firmas { margin-top: 26px; width: 100%; page-break-inside: avoid; }
    .firmas td { padding: 34px 10px 0 10px; text-align: center; font-size: 8px; color: #475569; border: none; }
    .firma-line { border-top: 1px solid #475569; padding-top: 4px; margin: 0 18px; }

    .page-break { page-break-before: always; }
</style>
