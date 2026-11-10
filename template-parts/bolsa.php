<?php
$nj_bag = function_exists('nj_slots_data') ? nj_slots_data()['bag'] : array('free_shipping' => 120000, 'shipping_cost' => 15000, 'whatsapp' => '');
$nj_bag['whatsapp'] = function_exists('nj_whatsapp_chat_url') ? nj_whatsapp_chat_url() : '';
$nj_checkout = function_exists('nj_shop_link') ? nj_shop_link('checkout') : '#';
?>
<?php
$nj_bag_state = function_exists('nj_bag_state') ? nj_bag_state() : array('lines' => array(), 'subtotal' => 0, 'discount' => 0, 'total' => 0, 'count' => 0, 'note' => '');
?>
<script type="application/json" data-bag-state><?php echo json_encode($nj_bag_state, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<div class="bag-drawer" data-bag data-free-shipping="<?php echo esc_attr((string) $nj_bag['free_shipping']); ?>" data-shipping-cost="<?php echo esc_attr((string) (isset($nj_bag['shipping_cost']) ? (int) $nj_bag['shipping_cost'] : 15000)); ?>" hidden>
  <button class="bag-drawer__backdrop" type="button" data-bag-close aria-label="Cerrar bolsa"></button>
  <aside class="bag-drawer__panel" aria-label="Mi bolsa">
    <header class="bag-drawer__head">
      <h2>Mi bolsa</h2>
      <button type="button" data-bag-close aria-label="Cerrar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </header>
    <div class="bag-drawer__lines" data-bag-lines></div>
    <p class="bag-drawer__empty" data-bag-empty>Tu bolsa está vacía.</p>
    <div class="bag-drawer__summary">
      <p class="bag-drawer__shipping" data-bag-shipping></p>
      <div class="bag-drawer__meter" aria-hidden="true"><span data-bag-meter></span></div>
      <dl>
        <div><dt>Subtotal</dt><dd data-bag-subtotal>$ 0</dd></div>
        <div><dt>Descuentos</dt><dd data-bag-discount>$ 0</dd></div>
        <div><dt>Total</dt><dd data-bag-total>$ 0</dd></div>
      </dl>
      <button class="bag-drawer__coupon" type="button" data-bag-coupon>Aplicar cupón de descuento</button>
      <form class="bag-drawer__coupon-form" data-bag-coupon-form hidden>
        <input type="text" name="coupon" placeholder="Cupón" aria-label="Cupón">
        <button type="submit">Aplicar</button>
        <p data-bag-coupon-note></p>
      </form>
      <?php echo function_exists('nj_personalizado_warning_html') ? nj_personalizado_warning_html(true) : ''; ?>
      <?php if (function_exists('nj_compra_trust_html')) { echo nj_compra_trust_html(); } ?>
      <a class="bag-drawer__checkout" href="<?php echo esc_url($nj_checkout); ?>">Pagar</a>
      <?php if (!empty($nj_bag['whatsapp'])) : ?>
        <a class="bag-drawer__doubt" href="<?php echo esc_url($nj_bag['whatsapp']); ?>" target="_blank" rel="noopener">¿Dudas de la talla? Escríbenos</a>
      <?php endif; ?>
    </div>
  </aside>
</div>
