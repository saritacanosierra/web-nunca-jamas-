<?php
/**
 * Reseñas de Google en la portada. Quien incluye este archivo define $nj_testimonios.
 */
$nj_testimonios = isset($nj_testimonios) && is_array($nj_testimonios) ? $nj_testimonios : array();
$nj_resenas = isset($nj_testimonios['items']) && is_array($nj_testimonios['items']) ? $nj_testimonios['items'] : array();
if (!$nj_resenas) {
    return;
}
$nj_maps = isset($nj_testimonios['maps']) ? $nj_testimonios['maps'] : '';
$nj_nota = '';
if (!empty($nj_testimonios['rating'])) {
    $nj_nota = number_format((float) $nj_testimonios['rating'], 1, ',', '');
    if (!empty($nj_testimonios['total'])) {
        $nj_nota .= ' · ' . (int) $nj_testimonios['total'] . ' reseñas';
    }
}
?>
<section class="section testimonios" aria-label="Testimonios">
  <div class="wrap">
    <div class="section-head">
      <h2>Lo que dicen en Google</h2>
      <?php if ($nj_nota !== '') : ?>
        <p><?php echo esc_html($nj_nota); ?></p>
      <?php endif; ?>
    </div>
    <div class="testimonios-carrusel" data-voces>
      <button class="hero__arrow hero__arrow--static" type="button" data-voces-prev aria-label="Reseñas anteriores">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 5 L8 12 L15 19"/></svg>
      </button>
      <div class="testimonio-row" tabindex="0" aria-label="Reseñas">
        <?php foreach ($nj_resenas as $nj_resena) : ?>
          <?php if (function_exists('nj_testimonio_articulo')) { nj_testimonio_articulo($nj_resena); } ?>
        <?php endforeach; ?>
      </div>
      <button class="hero__arrow hero__arrow--static" type="button" data-voces-next aria-label="Reseñas siguientes">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 5 L16 12 L9 19"/></svg>
      </button>
    </div>
    <?php if ($nj_maps !== '') : ?>
      <p class="testimonio-fuente"><a href="<?php echo esc_url($nj_maps); ?>" rel="noopener noreferrer">Reseñas de Google</a></p>
    <?php else : ?>
      <p class="testimonio-fuente">Reseñas de Google</p>
    <?php endif; ?>
  </div>
</section>
