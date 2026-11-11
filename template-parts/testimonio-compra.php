<?php
/**
 * Prueba de compra junto al botón: la nota y dos opiniones.
 * Quien incluye este archivo define $nj_compra_modo en nota o muestra.
 */
$nj_compra_modo = isset($nj_compra_modo) ? (string) $nj_compra_modo : 'nota';
$nj_testimonios = isset($nj_testimonios) && is_array($nj_testimonios) ? $nj_testimonios : array();
if ($nj_compra_modo === 'nota') {
    echo function_exists('nj_testimonios_nota') ? nj_testimonios_nota('ficha__nota') : '';
    return;
}
$nj_resenas = isset($nj_testimonios['items']) && is_array($nj_testimonios['items']) ? array_slice($nj_testimonios['items'], 0, 2) : array();
if (!$nj_resenas) {
    return;
}
$nj_maps = isset($nj_testimonios['maps']) ? (string) $nj_testimonios['maps'] : '';
?>
<div class="ficha__voces">
  <?php foreach ($nj_resenas as $nj_resena) : ?>
    <?php if (function_exists('nj_testimonio_articulo')) { nj_testimonio_articulo($nj_resena); } ?>
  <?php endforeach; ?>
  <?php if ($nj_maps !== '') : ?>
    <p class="testimonio-fuente"><a href="<?php echo esc_url($nj_maps); ?>" rel="noopener noreferrer">Reseñas de Google</a></p>
  <?php endif; ?>
</div>
