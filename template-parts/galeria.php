<?php
/**
 * Catálogo. Quien incluye este archivo define $nj_catalog.
 */
$nj_catalog = isset($nj_catalog) && is_array($nj_catalog) ? $nj_catalog : array();
$nj_catalog += array(
    'banner' => array('kicker' => '', 'title' => '', 'text' => '', 'image' => '', 'alt' => ''),
    'title' => '',
    'lead' => '',
    'crumbs' => array(array('href' => '', 'label' => '')),
    'pills' => array(),
    'action' => '',
    'hidden' => array(),
    'order_name' => 'orden',
    'orders' => array(),
    'order' => '',
    'items' => array(),
);
$nj_banners = array();
$nj_es_coleccion = !empty($nj_catalog['vista']) && ($nj_catalog['vista'] === 'una' || $nj_catalog['vista'] === 'mosaico');
$nj_coleccion_banda = isset($nj_catalog['banda']) ? trim((string) $nj_catalog['banda']) : '';
if ($nj_es_coleccion && $nj_coleccion_banda === '') {
    $nj_coleccion_banda = 'Donde los sueños se visten de colores';
}
$nj_coleccion_banda_estilo = ($nj_es_coleccion && function_exists('nj_coleccion_banda_estilo')) ? nj_coleccion_banda_estilo(isset($nj_catalog['banda_ajuste']) ? $nj_catalog['banda_ajuste'] : array()) : '';
if (!$nj_es_coleccion && isset($nj_catalog['banners']) && is_array($nj_catalog['banners'])) {
    $nj_banners = $nj_catalog['banners'];
} elseif (!$nj_es_coleccion && isset($nj_catalog['banner']) && is_array($nj_catalog['banner'])) {
    $nj_banners = array($nj_catalog['banner']);
}
?>
<main class="shop-page">
  <?php foreach ($nj_banners as $nj_banner) : ?>
    <?php $nj_banner_html = isset($nj_banner['html']) ? (string) $nj_banner['html'] : ''; ?>
    <?php if ($nj_banner_html !== '') : ?>
    <section class="gift-banner gift-banner--html">
      <?php echo function_exists('nj_regalo_html') ? nj_regalo_html($nj_banner_html, isset($nj_banner['image']) ? $nj_banner['image'] : '', isset($nj_banner['alt']) ? $nj_banner['alt'] : '') : ''; ?>
    </section>
    <?php else : ?>
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
    <?php endif; ?>
  <?php endforeach; ?>

  <div class="wrap catalog">
    <header class="catalog__head<?php echo !empty($nj_catalog['vista']) && $nj_catalog['vista'] === 'una' ? ' catalog__head--coleccion' : ''; ?>">
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

    <?php if (!empty($nj_catalog['vista']) && $nj_catalog['vista'] === 'una') : ?>
      <?php
      $nj_coleccion_modo = isset($nj_catalog['modo']) ? $nj_catalog['modo'] : '';
      $nj_coleccion_html = isset($nj_catalog['diseno_html']) ? $nj_catalog['diseno_html'] : '';
      $nj_coleccion_foto = isset($nj_catalog['diseno_foto']) ? $nj_catalog['diseno_foto'] : '';
      $nj_coleccion_armada = ($nj_coleccion_modo === 'html' && $nj_coleccion_html !== '') || ($nj_coleccion_modo === 'imagen' && $nj_coleccion_foto !== '');
      $nj_caja = isset($nj_catalog['caja']) && is_array($nj_catalog['caja']) ? $nj_catalog['caja'] : array();
      $nj_caja = function_exists('nj_coleccion_caja') ? nj_coleccion_caja($nj_caja) : array_merge(array(
          'titulo' => '',
          'kicker' => '',
          'texto' => '',
          'x' => 50,
          'y' => 50,
          'size' => 72,
          'small' => 16,
      ), $nj_caja);
      $nj_estilo = '--coleccion-x:' . (int) $nj_caja['x'] . '%;--coleccion-y:' . (int) $nj_caja['y'] . '%;--coleccion-size:' . (int) $nj_caja['size'] . 'px;--coleccion-small:' . (int) $nj_caja['small'] . 'px';
      $nj_texto_propio = trim((string) $nj_caja['titulo']) !== '' || trim((string) $nj_caja['kicker']) !== '' || trim((string) $nj_caja['texto']) !== '';
      ?>
      <?php if ($nj_coleccion_armada && $nj_coleccion_modo === 'imagen') : ?>
        <section class="coleccion-diseno" style="<?php echo esc_attr($nj_estilo); ?>">
          <img src="<?php echo esc_url($nj_coleccion_foto); ?>" alt="<?php echo esc_attr($nj_catalog['title']); ?>">
          <?php if ($nj_texto_propio) :
              $nj_coleccion_copy = array(
                  'title' => $nj_caja['titulo'],
                  'text' => $nj_caja['texto'],
                  'kicker' => $nj_caja['kicker'],
              );
              require __DIR__ . '/coleccion-texto.php';
          endif; ?>
        </section>
      <?php elseif ($nj_coleccion_armada) : ?>
        <section class="coleccion-diseno coleccion-diseno--html" style="<?php echo esc_attr($nj_estilo); ?>">
          <?php echo function_exists('nj_regalo_html') ? nj_regalo_html($nj_coleccion_html, $nj_coleccion_foto, $nj_catalog['title']) : ''; ?>
          <?php if ($nj_texto_propio) :
              $nj_coleccion_copy = array(
                  'title' => $nj_caja['titulo'],
                  'text' => $nj_caja['texto'],
                  'kicker' => $nj_caja['kicker'],
              );
              require __DIR__ . '/coleccion-texto.php';
          endif; ?>
        </section>
      <?php else : ?>
      <section class="coleccion-hero<?php echo empty($nj_catalog['foto']) ? ' is-plain' : ''; ?>" style="<?php echo esc_attr($nj_estilo); ?>">
        <?php if (!empty($nj_catalog['foto'])) : ?>
          <img src="<?php echo esc_url($nj_catalog['foto']); ?>" alt="">
        <?php endif; ?>
        <?php
        $nj_coleccion_copy = array(
            'title' => trim((string) $nj_caja['titulo']) !== '' ? $nj_caja['titulo'] : $nj_catalog['title'],
            'text' => trim((string) $nj_caja['texto']) !== '' ? $nj_caja['texto'] : $nj_catalog['lead'],
            'kicker' => $nj_caja['kicker'],
            'badge' => !empty($nj_catalog['reciente']),
            'button' => true,
        );
        require __DIR__ . '/coleccion-texto.php';
        ?>
      </section>
      <?php endif; ?>
      <?php require __DIR__ . '/coleccion-banda.php'; ?>
    <?php endif; ?>

    <?php if (!empty($nj_catalog['vista']) && $nj_catalog['vista'] === 'mosaico' && !empty($nj_catalog['colecciones'])) : ?>
      <?php require __DIR__ . '/colecciones.php'; ?>
      <?php require __DIR__ . '/coleccion-banda.php'; ?>
    <?php else : ?>

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
      <form class="catalog__find" method="get" action="<?php echo esc_url($nj_catalog['action']); ?>" role="search" data-find>
        <?php foreach ($nj_catalog['hidden'] as $name => $value) : ?>
          <?php if ($name === 'nj' || $value === '') { continue; } ?>
          <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
        <?php endforeach; ?>
        <?php if (!empty($nj_catalog['order'])) : ?>
          <input type="hidden" name="<?php echo esc_attr($nj_catalog['order_name']); ?>" value="<?php echo esc_attr($nj_catalog['order']); ?>">
        <?php endif; ?>
        <input type="search" name="nj" value="<?php echo esc_attr(isset($nj_catalog['busca']) ? $nj_catalog['busca'] : ''); ?>" placeholder="Buscar coincidencias" aria-label="Buscar coincidencias" autocomplete="off">
        <script type="application/json" data-nj-busca-grupos><?php echo json_encode(function_exists('nj_busca_grupos') ? nj_busca_grupos() : array(), JSON_UNESCAPED_UNICODE); ?></script>
      </form>
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

    <div data-catalog-results data-busca-contexto="<?php echo esc_attr($nj_catalog['title']); ?>">
    <?php if ($nj_catalog['items']) : ?>
      <div class="product-grid" id="prendas">
        <?php foreach ($nj_catalog['items'] as $item) : ?>
          <?php require __DIR__ . '/product-card.php'; ?>
        <?php endforeach; ?>
      </div>
      <p class="empty-shop" data-busca-vacio hidden>No hay prendas que coincidan con esa búsqueda.</p>
    <?php else : ?>
      <p class="empty-shop"><?php echo !empty($nj_catalog['busca']) ? 'No hay prendas que coincidan con esa búsqueda.' : 'Todavía no hay prendas en esta vista.'; ?></p>
    <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</main>
