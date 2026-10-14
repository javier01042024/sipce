{{-- Paginación institucional común a todos los PDF (pie de cada página) --}}
<script type="text/php">
if (isset($pdf) && method_exists($pdf, 'get_width')) {
    $font = $fontMetrics->getFont('helvetica', 'normal');
    $w = $pdf->get_width();
    $h = $pdf->get_height();
    $pdf->page_text($w - 152, $h - 30, 'Página {PAGE_NUM} de {PAGE_COUNT}', $font, 7, [0.58, 0.65, 0.72]);
}
</script>
