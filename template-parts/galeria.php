<?php
/** @var array $nj_catalog */
$nj_banner = $nj_catalog['banner'];
?>
<main class="shop-page">
  <section class="gift-banner">
    <div class="gift-banner__mark" aria-hidden="true">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="10" width="18" height="10" rx="1.5"/><path d="M3 14h18M12 10v10M12 10c-2-4-6-4-6-1s4 1 6 1 6-1 6-1-4-3-6 1z"/></svg>
    </div>
    <div class="gift-banner__copy">
      <p><?php nj_e($nj_banner['kicker']); ?></p>
      <h2><?php echo nl2br(esc_html($nj_banner['title'])); ?></h2>
      <span><?php nj_e($nj_banner['text']); ?></span>
    </div>
    <img src="<?php echo esc_url(nj_img($nj_banner['image'])); ?>" alt="<?php echo esc_attr($nj_banner['alt']); ?>">
  </section>

  <div class="wrap catalog">
    <header class="catalog__head">
      <h1><?php nj_e($nj_catalog['title']); ?></h1>
      <p><?php nj_e($nj_catalog['lead']); ?></p>
      <nav class="crumbs" aria-label="Ruta">
        <a href="<?php echo esc_url($nj_catalog['crumbs'][0]['href']); ?>" aria-label="Inicio">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 11l8-7 8 7v8H4z"/></svg>
        </a>
        <?php foreach ($nj_catalog['crumbs'] as $index => $crumb) : ?>
          <?php if ($index === 0) { continue; } ?>
          <span aria-hidden="true">›</span>
          <?php if ($crumb['href'] === '') : ?>
            <span><?php nj_e($crumb['label']); ?></span>
          <?php else : ?>
            <a href="<?php echo esc_url($crumb['href']); ?>"><?php nj_e($crumb['label']); ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
      </nav>
    </header>

    <?php if ($nj_catalog['pills']) : ?>
      <div class="catalog__pills" data-pills>
        <button class="catalog__arrow" type="button" data-pills-prev aria-label="Anterior">‹</button>
        <div class="catalog__pills-track" data-pills-track>
          <?php foreach ($nj_catalog['pills'] as $pill) : ?>
            <a class="catalog__pill<?php echo !empty($pill['active']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($pill['href']); ?>"><?php nj_e($pill['label']); ?></a>
          <?php endforeach; ?>
        </div>
        <button class="catalog__arrow" type="button" data-pills-next aria-label="Siguiente">›</button>
      </div>
    <?php endif; ?>

    <div class="catalog__tools">
      <button class="catalog__filters" type="button" data-filters aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16M7 12h10M10 17h4"/></svg>
        Filtros
      </button>
      <form class="catalog__sort" method="get" action="<?php echo esc_url($nj_catalog['action']); ?>" data-sort-form>
        <?php foreach ($nj_catalog['hidden'] as $name => $value) : ?>
          <?php if ($value === '') { continue; } ?>
          <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
        <?php endforeach; ?>
        <span>Ordenar por</span>
        <select name="<?php echo esc_attr($nj_catalog['order_name']); ?>" aria-label="Ordenar por">
          <?php foreach ($nj_catalog['orders'] as $value => $label) : ?>
            <option value="<?php echo esc_attr($value); ?>"<?php echo $nj_catalog['order'] === $value ? ' selected' : ''; ?>><?php nj_e($label); ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <?php if ($nj_catalog['pills']) : ?>
      <div class="catalog__panel" data-filters-panel hidden>
        <?php foreach ($nj_catalog['pills'] as $pill) : ?>
          <a class="catalog__pill<?php echo !empty($pill['active']) ? ' is-active' : ''; ?>" href="<?php echo esc_url($pill['href']); ?>"><?php nj_e($pill['label']); ?></a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($nj_catalog['items']) : ?>
      <div class="product-grid">
        <?php foreach ($nj_catalog['items'] as $item) : ?>
          <?php require __DIR__ . '/product-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
      <p class="empty-shop">Todavía no hay prendas en esta vista.</p>
    <?php endif; ?>
  </div>

  <?php require __DIR__ . '/beneficios.php'; ?>
</main>
