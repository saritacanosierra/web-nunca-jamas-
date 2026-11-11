<?php
/**
 * Texto colocado sobre el diseño de una colección.
 * Quien incluye este archivo define $nj_coleccion_copy.
 */
$nj_coleccion_copy = isset($nj_coleccion_copy) && is_array($nj_coleccion_copy) ? $nj_coleccion_copy : array();
$nj_copy_title = isset($nj_coleccion_copy['title']) ? trim((string) $nj_coleccion_copy['title']) : '';
$nj_copy_text = isset($nj_coleccion_copy['text']) ? trim((string) $nj_coleccion_copy['text']) : '';
$nj_copy_kicker = isset($nj_coleccion_copy['kicker']) ? trim((string) $nj_coleccion_copy['kicker']) : '';
$nj_copy_badge = !empty($nj_coleccion_copy['badge']);
if ($nj_copy_title === '' && $nj_copy_text === '' && $nj_copy_kicker === '' && !$nj_copy_badge) {
    return;
}
?>
<div class="coleccion-hero__copy">
  <?php if ($nj_copy_badge) : ?>
    <p class="coleccion-hero__badge">Nueva colección</p>
  <?php endif; ?>
  <?php if ($nj_copy_kicker !== '') : ?>
    <p class="coleccion-hero__kicker"><?php echo esc_html($nj_copy_kicker); ?></p>
  <?php endif; ?>
  <?php if ($nj_copy_title !== '') : ?>
    <h2><?php echo nl2br(esc_html($nj_copy_title)); ?></h2>
  <?php endif; ?>
  <?php if ($nj_copy_text !== '') : ?>
    <p class="coleccion-hero__text"><?php echo esc_html($nj_copy_text); ?></p>
  <?php endif; ?>
  <?php if (!empty($nj_coleccion_copy['button'])) : ?>
    <a class="btn btn-yellow" href="#prendas">Ver las prendas</a>
  <?php endif; ?>
</div>
