<?php
require dirname(__DIR__) . '/inc/helpers.php';
require_once dirname(__DIR__) . '/inc/portada.php';
$nj = nj_slots_data();
$nj_tabs = nj_home_tabs();
$nj_styles = nj_style_items();
?>
<main>
  <section class="hero" data-hero>
    <?php foreach ($nj['hero'] as $i => $slide) : ?>
      <article class="hero__slide<?php echo $i === 0 ? ' is-active' : ''; ?>" style="--slide-image: url('<?php echo esc_url(nj_img($slide['src'])); ?>')">
        <div class="hero__card">
          <small><?php nj_e($slide['kicker']); ?></small>
          <h1><?php echo esc_html($slide['title']); ?></h1>
          <p><?php nj_e($slide['text']); ?></p>
          <a class="btn btn-red" href="<?php echo esc_url(nj_hero_link($i)); ?>"><?php nj_e($slide['button']); ?></a>
        </div>
        <div class="hero__sale" aria-hidden="true">
          <span class="sale-top"><?php nj_e($slide['sale_top']); ?></span>
          <span class="sale-big"><?php nj_e($slide['sale_big']); ?></span>
          <span class="sale-bottom"><?php nj_e($slide['sale_bottom']); ?></span>
        </div>
      </article>
    <?php endforeach; ?>
    <button class="hero__arrow hero__arrow--prev" type="button" data-prev aria-label="Anterior">
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5 L8 12 L15 19"/></svg>
    </button>
    <button class="hero__arrow hero__arrow--next" type="button" data-next aria-label="Siguiente">
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5 L16 12 L9 19"/></svg>
    </button>
    <div class="hero__dots">
      <?php foreach ($nj['hero'] as $i => $slide) : ?>
        <button type="button" data-dot="<?php echo (int) $i; ?>" class="<?php echo $i === 0 ? 'is-active' : ''; ?>" aria-label="Diapositiva <?php echo (int) $i + 1; ?>"></button>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="section" id="prendas">
    <div class="wrap" data-tabs>
      <div class="section-head">
        <h2>Prendas únicas para cada día</h2>
        <div class="catalog-tabs" role="tablist">
          <?php foreach ($nj_tabs as $index => $tab) : ?>
            <button class="catalog-tab<?php echo $index === 0 ? ' is-active' : ''; ?>" type="button" role="tab" data-tab="<?php echo esc_attr($tab['id']); ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
              <?php nj_e($tab['label']); ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
      <?php foreach ($nj_tabs as $index => $tab) : ?>
        <div class="catalog-panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr($tab['id']); ?>" role="tabpanel">
          <?php if (!$tab['items']) : ?>
            <p class="empty-shop">Todavía no hay prendas en esta vista. En WooCommerce, las ofertas aparecen al ponerles precio de sale.</p>
          <?php else : ?>
            <div class="product-grid">
              <?php foreach ($tab['items'] as $item) : ?>
                <?php require __DIR__ . '/product-card.php'; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <p class="catalog-more"><a class="btn btn-ghost" href="<?php echo esc_url($tab['url']); ?>">Comprar ahora</a></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="promo" aria-label="Campañas">
    <article class="promo-gift">
      <div class="promo-gift__copy">
        <span class="gift-ico" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#161616" stroke-width="1.7"><rect x="3" y="10" width="18" height="10" rx="1.5"/><path d="M3 14h18M12 10v10M12 10c-2.5 0-4-2.2-2.2-3.8S12 8 12 10zm0 0c2.5 0 4-2.2 2.2-3.8S12 8 12 10z"/></svg>
        </span>
        <h2>REGALO<br>PERFECTO</h2>
        <a class="btn btn-yellow" href="<?php echo esc_url(nj_shop_link('shop')); ?>">Comprar ahora</a>
        <p class="fine">Elige talla al comprar. Envío a Colombia sobre las prendas disponibles.</p>
      </div>
      <a class="promo-gift__photo" href="<?php echo esc_url(nj_section_link('Colecciones')); ?>">
        <img data-slot="promo-gift" src="<?php echo esc_url(nj_img($nj['promo']['gift']['src'])); ?>" alt="<?php echo esc_attr($nj['promo']['gift']['alt']); ?>">
      </a>
    </article>

    <article class="promo-collect">
      <img data-slot="promo-collect" src="<?php echo esc_url(nj_img($nj['promo']['collect']['src'])); ?>" alt="<?php echo esc_attr($nj['promo']['collect']['alt']); ?>">
      <div class="promo-collect__card">
        <p class="eyebrow">Nunca</p>
        <h3>COLLECT</h3>
        <a class="btn btn-yellow" href="<?php echo esc_url(nj_shop_link('shop')); ?>">Ver colección</a>
      </div>
    </article>

    <article class="promo-3d">
      <img data-slot="promo-3d" src="<?php echo esc_url(nj_img($nj['promo']['three_d']['src'])); ?>" alt="<?php echo esc_attr($nj['promo']['three_d']['alt']); ?>">
      <div class="promo-3d__copy">
        <p>Gira la prenda</p>
        <h2>EXPERIENCIA<br>3D</h2>
      </div>
      <span class="badge-3d">3D</span>
    </article>
  </section>

  <section class="section styles">
    <div class="wrap">
      <div class="section-head">
        <h2>Compra por categorías</h2>
      </div>
      <div class="style-row">
        <?php foreach ($nj_styles as $style) : ?>
          <a class="style-item" href="<?php echo esc_url($style['href']); ?>">
            <span>
              <img src="<?php echo esc_url($style['image']); ?>" alt="<?php echo esc_attr($style['label']); ?>">
            </span>
            <p><?php nj_e($style['label']); ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php require __DIR__ . '/cuentos.php'; ?>

  <section class="section looks">
    <div class="wrap">
      <div class="look-head">
        <button class="hero__arrow hero__arrow--static" type="button" aria-hidden="true" tabindex="-1">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5 L8 12 L15 19"/></svg>
        </button>
        <h2>Looks inspiradores</h2>
        <button class="hero__arrow hero__arrow--static" type="button" aria-hidden="true" tabindex="-1">
          <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5 L16 12 L9 19"/></svg>
        </button>
      </div>
      <div class="look-grid">
        <?php foreach ($nj['looks'] as $n => $look) : ?>
          <article class="look-card">
            <img data-slot="look-<?php echo (int) $n + 1; ?>" src="<?php echo esc_url(nj_img($look['src'])); ?>" alt="<?php echo esc_attr($look['alt']); ?>">
            <?php foreach ($look['points'] as $point) : ?>
              <button class="hotspot" type="button" style="--point-top: <?php echo esc_attr($point['top']); ?>; --point-left: <?php echo esc_attr($point['left']); ?>" aria-label="<?php echo esc_attr($point['label']); ?>">
                +
                <span class="tip">
                  <strong><?php nj_e($point['label']); ?></strong>
                  <em><?php nj_e($point['price']); ?></em>
                </span>
              </button>
            <?php endforeach; ?>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="caption">
        <strong>Nunca Jamás · ropa para niñas</strong>
        Colecciones soñadas, prendas de línea, accesorios y personalizados para estrenar el cuento.
      </p>
    </div>
  </section>

  <?php require __DIR__ . '/beneficios.php'; ?>
</main>
