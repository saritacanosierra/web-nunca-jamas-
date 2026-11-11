<dialog class="quick-view" data-quick-view aria-labelledby="quick-view-title">
  <div class="quick-view__panel">
    <button class="quick-view__close" type="button" data-quick-close aria-label="Cerrar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
    <div class="quick-view__gallery">
      <div class="quick-view__thumbs" data-quick-thumbs></div>
      <img class="quick-view__photo" data-quick-photo alt="">
    </div>
    <div class="quick-view__info">
      <p class="quick-view__meta" data-quick-meta></p>
      <h2 id="quick-view-title" data-quick-title></h2>
      <p class="price" data-quick-price></p>
      <?php echo function_exists('nj_testimonios_nota') ? nj_testimonios_nota('quick-view__nota') : ''; ?>
      <?php if (function_exists('nj_addi_ready') && nj_addi_ready()) : ?>
        <div class="ficha__addi" data-addi-quick data-addi-slug="<?php echo esc_attr(nj_addi_settings()['slug']); ?>" hidden></div>
      <?php endif; ?>
      <p class="quick-view__hint" data-quick-hint>Agrega al carrito seleccionando tu talla</p>
      <div class="quick-view__sizes" data-quick-sizes></div>
      <?php if (function_exists('nj_compra_guide_html')) { echo nj_compra_guide_html(); } ?>
      <?php if (function_exists('nj_compra_trust_html')) { echo nj_compra_trust_html(); } ?>
      <p class="quick-view__note" data-quick-note></p>
      <h3 data-quick-heading>Descripción</h3>
      <p data-quick-text></p>
      <p data-quick-composition></p>
      <a class="quick-view__buy" data-quick-buy href="">¡Compra ya!</a>
    </div>
  </div>
</dialog>
