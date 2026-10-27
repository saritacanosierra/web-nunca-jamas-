<?php
/**
 * Banner de cuentos, al estilo del hero. Cada imagen abre la vista de Cuentos.
 */
$nj_cuentos = nj_home_cuentos();
$nj_cuentos_url = nj_cuentos_url();
?>
<section class="cuentos-banner" data-cuentos aria-label="Cuentos">
  <?php foreach ($nj_cuentos as $i => $cuento) : ?>
    <a class="cuentos-banner__slide<?php echo $i === 0 ? ' is-active' : ''; ?>" href="<?php echo esc_url($nj_cuentos_url); ?>" style="--slide-image: url('<?php echo esc_url($cuento['image']); ?>')">
      <span class="cuentos-banner__card">
        <small><?php nj_e($cuento['kicker']); ?></small>
        <strong><?php echo esc_html($cuento['title']); ?></strong>
      </span>
    </a>
  <?php endforeach; ?>
  <button class="hero__arrow hero__arrow--prev" type="button" data-cuentos-prev aria-label="Imagen anterior">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5 L8 12 L15 19"/></svg>
  </button>
  <button class="hero__arrow hero__arrow--next" type="button" data-cuentos-next aria-label="Imagen siguiente">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5 L16 12 L9 19"/></svg>
  </button>
  <div class="cuentos-banner__dots">
    <?php foreach ($nj_cuentos as $i => $cuento) : ?>
      <button type="button" data-cuentos-dot="<?php echo (int) $i; ?>" class="<?php echo $i === 0 ? 'is-active' : ''; ?>" aria-label="Imagen <?php echo (int) $i + 1; ?>"></button>
    <?php endforeach; ?>
  </div>
</section>
