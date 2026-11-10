<?php
/**
 * Tarjeta de producto. Quien incluye este archivo define $item.
 */
$item = isset($item) && is_array($item) ? $item : array();
$item += array(
    'name' => '',
    'kicker' => '',
    'price_html' => '',
    'url' => '',
    'image' => '',
    'alt' => '',
);
$nj_sizes = isset($item['sizes']) ? $item['sizes'] : array();
$nj_gallery = !empty($item['gallery']) ? $item['gallery'] : array($item['image']);
$nj_quick = array(
    'name' => $item['name'],
    'kicker' => $item['kicker'],
    'ref' => isset($item['ref']) ? $item['ref'] : '',
    'price_html' => $item['price_html'],
    'url' => $item['url'],
    'image' => $item['image'],
    'gallery' => array_values($nj_gallery),
    'sizes' => array_values($nj_sizes),
    'price' => isset($item['price']) ? (int) $item['price'] : 0,
    'regular' => isset($item['regular']) ? (int) $item['regular'] : 0,
    'id' => isset($item['id']) ? (int) $item['id'] : 0,
    'variations' => isset($item['variations']) ? $item['variations'] : array(),
    'note' => isset($item['note']) ? $item['note'] : '',
    'personalizado' => !empty($item['personalizado']),
    'description' => isset($item['description']) ? $item['description'] : '',
    'composition' => isset($item['composition']) ? $item['composition'] : '',
);
?>
<article class="product-card">
  <script type="application/json" class="product-card__data"><?php echo json_encode($nj_quick, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
  <div class="product-card__media">
    <a class="product-card__photos" href="<?php echo esc_url($item['url']); ?>">
      <img class="product-card__photo" src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['alt']); ?>">
      <?php if (!empty($item['hover'])) : ?>
        <img class="product-card__hover" src="<?php echo esc_url($item['hover']); ?>" alt="" loading="lazy" decoding="async">
      <?php endif; ?>
    </a>
    <?php if (!empty($item['badge'])) : ?>
      <span class="badge-sale"><?php echo esc_html($item['badge']); ?></span>
    <?php endif; ?>
    <div class="product-card__tools">
      <button class="product-card__icon" type="button" data-save aria-pressed="false" aria-label="Guardar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 19s-7-4.4-7-9a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 4.6-7 9-7 9z"/></svg>
      </button>
      <button class="product-card__icon" type="button" data-quick-view-open aria-label="Vista rápida">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/><circle cx="12" cy="12" r="2.5"/></svg>
      </button>
    </div>
    <?php if ($nj_sizes) : ?>
      <div class="product-card__sizes">
        <?php foreach ($nj_sizes as $size) : ?>
          <button type="button"><?php echo esc_html($size); ?></button>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
  <a class="product-card__info" href="<?php echo esc_url($item['url']); ?>">
    <p class="product-kicker"><?php echo esc_html($item['kicker']); ?></p>
    <h3><?php echo esc_html($item['name']); ?></h3>
    <p class="price"><?php echo function_exists('wp_kses_post') ? wp_kses_post($item['price_html']) : $item['price_html']; ?></p>
  </a>
</article>
