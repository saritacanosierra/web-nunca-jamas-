<?php
/**
 * Ficha de producto: carrusel de fotos y compra a la derecha.
 */
$nj_frames = $nj_ficha['frames'];
$nj_sizes = isset($nj_ficha['sizes']) ? $nj_ficha['sizes'] : array();
$nj_payload = array(
    'name' => $nj_ficha['name'],
    'image' => $nj_ficha['image'],
    'url' => $nj_ficha['url'],
    'price' => isset($nj_ficha['price']) ? (int) $nj_ficha['price'] : 0,
    'regular' => isset($nj_ficha['regular']) ? (int) $nj_ficha['regular'] : 0,
    'id' => isset($nj_ficha['id']) ? (int) $nj_ficha['id'] : 0,
    'variations' => isset($nj_ficha['variations']) ? $nj_ficha['variations'] : array(),
);
?>
<article class="ficha" data-ficha>
  <script type="application/json" data-ficha-data><?php echo json_encode($nj_payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
  <div class="ficha__stage">
    <div class="ficha__track" data-ficha-track>
      <?php foreach ($nj_frames as $frame) : ?>
        <figure class="ficha__slide" style="--frame-position: <?php echo esc_attr($frame['position']); ?>">
          <img src="<?php echo esc_url($frame['src']); ?>" alt="<?php echo esc_attr($nj_ficha['alt']); ?>">
        </figure>
      <?php endforeach; ?>
    </div>
    <button class="hero__arrow ficha__arrow ficha__arrow--prev" type="button" data-ficha-prev aria-label="Fotos anteriores">
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5 L8 12 L15 19"/></svg>
    </button>
    <button class="hero__arrow ficha__arrow ficha__arrow--next" type="button" data-ficha-next aria-label="Fotos siguientes">
      <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5 L16 12 L9 19"/></svg>
    </button>
    <aside class="ficha__buy">
      <p class="ficha__kicker"><?php nj_e($nj_ficha['kicker']); ?></p>
      <h1><?php nj_e($nj_ficha['name']); ?></h1>
      <p class="ficha__price"><?php echo function_exists('wp_kses_post') ? wp_kses_post($nj_ficha['price_html']) : $nj_ficha['price_html']; ?></p>
      <?php if (!defined('NJ_PREVIEW') && isset($GLOBALS['product']) && $GLOBALS['product']) : ?>
        <div class="ficha__stock">
          <?php
          if (function_exists('wc_get_stock_html')) {
              echo wp_kses_post(wc_get_stock_html($GLOBALS['product']));
          }
          $nj_availability = $GLOBALS['product']->get_availability();
          if (empty($nj_availability['availability']) && !empty($nj_availability['class']) && $nj_availability['class'] === 'in-stock') {
              echo '<p class="stock in-stock">Disponible</p>';
          }
          ?>
        </div>
      <?php endif; ?>
      <?php if ($nj_sizes) : ?>
        <p class="ficha__label">Selecciona una talla</p>
        <div class="ficha__sizes" data-ficha-sizes>
          <?php foreach ($nj_sizes as $size) : ?>
            <button type="button" data-ficha-size-button data-size="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <p class="ficha__alert" data-ficha-alert hidden>Elige una talla para agregarla a la bolsa.</p>
      <button class="ficha__add" type="button" data-ficha-add>Agregar a mi bolsa</button>
      <?php if (!defined('NJ_PREVIEW') && function_exists('woocommerce_template_single_add_to_cart')) : ?>
        <div class="ficha__gateway">
          <?php call_user_func('woocommerce_template_single_add_to_cart'); ?>
        </div>
      <?php endif; ?>
    </aside>
  </div>
  <div class="ficha__perks">
    <p>Elige tu talla</p>
    <p>Envío a Colombia</p>
    <p>Cambios con calma</p>
  </div>
  <div class="wrap ficha__detail" data-ficha-tabs>
    <div class="ficha__tablist" role="tablist">
      <?php foreach ($nj_ficha['tabs'] as $index => $tab) : ?>
        <button type="button" role="tab" data-ficha-tab="<?php echo esc_attr($tab['id']); ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" class="<?php echo $index === 0 ? 'is-active' : ''; ?>"><?php nj_e($tab['label']); ?></button>
      <?php endforeach; ?>
    </div>
    <?php foreach ($nj_ficha['tabs'] as $index => $tab) : ?>
      <div class="ficha__panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ficha-panel="<?php echo esc_attr($tab['id']); ?>">
        <p><?php echo esc_html($tab['text']); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
  <?php require __DIR__ . '/beneficios.php'; ?>
  <div class="ficha-bar">
    <img src="<?php echo esc_url($nj_ficha['image']); ?>" alt="">
    <div class="ficha-bar__copy">
      <strong><?php nj_e($nj_ficha['name']); ?></strong>
      <span><?php echo function_exists('wp_kses_post') ? wp_kses_post($nj_ficha['price_html']) : $nj_ficha['price_html']; ?></span>
    </div>
    <?php if ($nj_sizes) : ?>
      <select data-ficha-size aria-label="Seleccionar talla">
        <option value="">Seleccionar talla</option>
        <?php foreach ($nj_sizes as $size) : ?>
          <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>
    <button type="button" data-ficha-add>Agregar al carrito</button>
  </div>
</article>
