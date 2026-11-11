<?php
/**
 * Mosaico de colecciones. Quien incluye este archivo define $nj_catalog['colecciones'].
 */
$nj_colecciones = isset($nj_catalog['colecciones']) && is_array($nj_catalog['colecciones']) ? $nj_catalog['colecciones'] : array();
$nj_colecciones_clase = 'colecciones-mosaico';
if (count($nj_colecciones) === 1) {
    $nj_colecciones_clase .= ' is-una';
} elseif (count($nj_colecciones) === 2) {
    $nj_colecciones_clase .= ' is-dos';
}
?>
<div class="<?php echo esc_attr($nj_colecciones_clase); ?>">
  <?php foreach ($nj_colecciones as $nj_coleccion) : ?>
    <a class="coleccion-card is-<?php echo esc_attr(isset($nj_coleccion['spot']) ? $nj_coleccion['spot'] : 'more'); ?><?php echo !empty($nj_coleccion['reciente']) ? ' is-recent' : ''; ?>" href="<?php echo esc_url($nj_coleccion['href']); ?>">
      <?php if (!empty($nj_coleccion['image'])) : ?>
        <img src="<?php echo esc_url($nj_coleccion['image']); ?>" alt="<?php echo esc_attr($nj_coleccion['label']); ?>">
      <?php endif; ?>
      <span class="coleccion-card__copy">
        <?php if (!empty($nj_coleccion['reciente'])) : ?>
          <span class="coleccion-card__badge">Nueva colección</span>
        <?php endif; ?>
        <span class="coleccion-card__name"><?php echo esc_html($nj_coleccion['label']); ?></span>
      </span>
    </a>
  <?php endforeach; ?>
</div>
