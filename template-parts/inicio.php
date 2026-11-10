<?php
require dirname(__DIR__) . '/inc/helpers.php';
require_once dirname(__DIR__) . '/inc/portada.php';
$nj_portada = nj_portada_vista();
$nj_promos = isset($nj_portada['promos']) ? $nj_portada['promos'] : array();
$nj_looks = $nj_portada['looks'];
$nj_tabs = $nj_portada['tabs'];
$nj_styles = $nj_portada['styles'];
?>
<main>
  <h1 class="seo-title">Nunca Jamás, ropa de cuento para niñas</h1>
  <?php require __DIR__ . '/hero.php'; ?>

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
            <p class="empty-shop">Todavía no hay prendas en esta vista.</p>
          <?php else : ?>
            <div class="product-grid">
              <?php foreach ($tab['items'] as $item) : ?>
                <?php require __DIR__ . '/product-card.php'; ?>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <p class="catalog-more"><a class="btn btn-ghost" href="<?php echo esc_url($tab['url']); ?>"><?php echo $tab['id'] === 'coleccion' ? 'Ver la colección' : 'Ver lo nuevo'; ?></a></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <?php if ($nj_promos) : ?>
  <section class="promo" aria-label="Campañas">
    <?php foreach ($nj_promos as $nj_promo) :
        $nj_kind = isset($nj_promo['kind']) ? $nj_promo['kind'] : 'photo';
        ?>
      <?php
        $nj_promo_html = isset($nj_promo['html']) ? trim((string) $nj_promo['html']) : '';
        $nj_promo_alt = isset($nj_promo['alt']) ? $nj_promo['alt'] : '';
      ?>
      <?php if ($nj_promo_html !== '') : ?>
        <article class="promo-html">
          <?php echo function_exists('nj_regalo_html') ? nj_regalo_html($nj_promo_html, isset($nj_promo['src']) ? $nj_promo['src'] : '', $nj_promo_alt) : ''; ?>
        </article>
      <?php elseif ($nj_kind === 'gift') : ?>
        <article class="promo-gift">
          <div class="promo-gift__copy">
            <span class="gift-ico" aria-hidden="true">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#161616" stroke-width="1.7"><rect x="3" y="10" width="18" height="10" rx="1.5"/><path d="M3 14h18M12 10v10M12 10c-2.5 0-4-2.2-2.2-3.8S12 8 12 10zm0 0c2.5 0 4-2.2 2.2-3.8S12 8 12 10z"/></svg>
            </span>
            <?php
            $nj_gift_title = isset($nj_promo['title']) && trim((string) $nj_promo['title']) !== '' ? $nj_promo['title'] : "REGALO\nPERFECTO";
            $nj_gift_button = isset($nj_promo['button']) && trim((string) $nj_promo['button']) !== '' ? $nj_promo['button'] : 'Comprar ahora';
            $nj_gift_note = isset($nj_promo['note']) && trim((string) $nj_promo['note']) !== '' ? $nj_promo['note'] : 'Elige talla al comprar. Envío a Colombia sobre las prendas disponibles.';
            ?>
            <h2><?php echo nl2br(esc_html($nj_gift_title)); ?></h2>
            <a class="btn btn-yellow" href="<?php echo esc_url(nj_promo_href($nj_promo)); ?>"><?php echo esc_html($nj_gift_button); ?></a>
            <p class="fine"><?php echo esc_html($nj_gift_note); ?></p>
          </div>
          <a class="promo-gift__photo" href="<?php echo esc_url(nj_promo_href($nj_promo)); ?>">
            <img data-slot="promo-gift" src="<?php echo esc_url($nj_promo['src']); ?>" alt="<?php echo esc_attr($nj_promo['alt']); ?>">
          </a>
        </article>
      <?php elseif ($nj_kind === 'collect') : ?>
        <article class="promo-collect">
          <img data-slot="promo-collect" src="<?php echo esc_url($nj_promo['src']); ?>" alt="<?php echo esc_attr($nj_promo['alt']); ?>">
          <div class="promo-collect__card">
            <?php
            $nj_collect_kicker = isset($nj_promo['kicker']) && trim((string) $nj_promo['kicker']) !== '' ? $nj_promo['kicker'] : 'Nunca';
            $nj_collect_title = isset($nj_promo['title']) && trim((string) $nj_promo['title']) !== '' ? $nj_promo['title'] : 'COLLECT';
            $nj_collect_button = isset($nj_promo['button']) && trim((string) $nj_promo['button']) !== '' ? $nj_promo['button'] : 'Ver colección';
            ?>
            <p class="eyebrow"><?php echo esc_html($nj_collect_kicker); ?></p>
            <h3><?php echo esc_html($nj_collect_title); ?></h3>
            <a class="btn btn-yellow" href="<?php echo esc_url(nj_promo_href($nj_promo)); ?>"><?php echo esc_html($nj_collect_button); ?></a>
          </div>
        </article>
      <?php elseif ($nj_kind === 'three_d') : ?>
        <article class="promo-3d">
          <img data-slot="promo-3d" src="<?php echo esc_url($nj_promo['src']); ?>" alt="<?php echo esc_attr($nj_promo['alt']); ?>">
          <div class="promo-3d__copy">
            <?php
            $nj_3d_kicker = isset($nj_promo['kicker']) && trim((string) $nj_promo['kicker']) !== '' ? $nj_promo['kicker'] : 'Gira la prenda';
            $nj_3d_title = isset($nj_promo['title']) && trim((string) $nj_promo['title']) !== '' ? $nj_promo['title'] : "EXPERIENCIA\n3D";
            ?>
            <p><?php echo esc_html($nj_3d_kicker); ?></p>
            <h2><?php echo nl2br(esc_html($nj_3d_title)); ?></h2>
            <a class="btn btn-yellow" href="<?php echo esc_url(nj_promo_href($nj_promo)); ?>">Ver la prenda</a>
          </div>
          <span class="badge-3d">3D</span>
        </article>
      <?php else : ?>
        <article class="promo-extra">
          <a href="<?php echo esc_url(nj_promo_href($nj_promo)); ?>">
            <img src="<?php echo esc_url($nj_promo['src']); ?>" alt="<?php echo esc_attr(isset($nj_promo['alt']) ? $nj_promo['alt'] : ''); ?>">
            <?php if (!empty($nj_promo['label'])) : ?>
              <span><?php echo esc_html($nj_promo['label']); ?></span>
            <?php endif; ?>
          </a>
        </article>
      <?php endif; ?>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>

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

  <?php if ($nj_looks) : ?>
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
        <?php foreach ($nj_looks as $n => $look) :
            if (!isset($look['src']) || $look['src'] === '') {
                continue;
            }
            ?>
          <article class="look-card">
            <img data-slot="look-<?php echo (int) $n + 1; ?>" src="<?php echo esc_url(nj_img($look['src'])); ?>" alt="<?php echo esc_attr($look['alt']); ?>">
            <?php foreach ($look['points'] as $point) :
                $nj_point_href = isset($point['href']) ? $point['href'] : '';
                ?>
              <?php if ($nj_point_href !== '') : ?>
                <a class="hotspot" href="<?php echo esc_url($nj_point_href); ?>" style="--point-top: <?php echo esc_attr($point['top']); ?>; --point-left: <?php echo esc_attr($point['left']); ?>" aria-label="<?php echo esc_attr($point['label']); ?>">
              <?php else : ?>
                <button class="hotspot" type="button" style="--point-top: <?php echo esc_attr($point['top']); ?>; --point-left: <?php echo esc_attr($point['left']); ?>" aria-label="<?php echo esc_attr($point['label']); ?>">
              <?php endif; ?>
                +
                <span class="tip">
                  <strong><?php nj_e($point['label']); ?></strong>
                  <em><?php nj_e($point['price']); ?></em>
                  <?php if ($nj_point_href !== '') : ?>
                    <span><?php echo esc_html(preg_replace('#^https?://#', '', $nj_point_href)); ?></span>
                  <?php endif; ?>
                </span>
              <?php if ($nj_point_href !== '') : ?>
                </a>
              <?php else : ?>
                </button>
              <?php endif; ?>
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
  <?php endif; ?>
</main>
