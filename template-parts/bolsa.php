<?php
$nj_bag = function_exists('nj_slots_data') ? nj_slots_data()['bag'] : array('free_shipping' => 250000, 'shipping_cost' => 15000, 'whatsapp' => '');
$nj_checkout = function_exists('nj_shop_link') ? nj_shop_link('checkout') : '#';
?>
<?php
$nj_bag_state = function_exists('nj_bag_state') ? nj_bag_state() : array('lines' => array(), 'subtotal' => 0, 'discount' => 0, 'total' => 0, 'count' => 0, 'note' => '');
?>
<script type="application/json" data-bag-state><?php echo json_encode($nj_bag_state, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
<div class="bag-drawer" data-bag data-free-shipping="<?php echo esc_attr((string) $nj_bag['free_shipping']); ?>" hidden>
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
      <a class="bag-drawer__checkout" href="<?php echo esc_url($nj_checkout); ?>">Pagar</a>
      <?php if (!empty($nj_bag['whatsapp'])) : ?>
        <a class="bag-drawer__whatsapp" href="<?php echo esc_url($nj_bag['whatsapp']); ?>" aria-label="WhatsApp" target="_blank" rel="noopener">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a9 9 0 0 0-7.7 13.5L3 21l4.7-1.2A9 9 0 1 0 12 3zm4.8 12.7c-.2.6-1.2 1.1-1.6 1.1-.4.1-.9.2-2.9-.6-2.4-1-4-3.5-4.1-3.6-.1-.2-1-1.3-1-2.5s.6-1.8.9-2c.2-.2.5-.3.7-.3h.5c.2 0 .4 0 .6.5.2.6.7 2 .8 2.1.1.2.1.3 0 .5-.1.2-.2.3-.3.5l-.2.3c-.1.1-.2.2-.1.4.1.2.6 1 1.3 1.6.9.8 1.6 1 1.8 1.1.2.1.4.1.5-.1.1-.2.6-.7.8-.9.2-.2.3-.2.5-.1.2.1 1.4.7 1.6.8.2.1.4.2.4.3.1.2.1.8-.1 1.4z"/></svg>
        </a>
      <?php endif; ?>
    </div>
  </aside>
</div>
