<?php
/**
 * Escritorio de Mi cuenta.
 */
defined('ABSPATH') || exit;

$nj_hello = function_exists('nj_cuenta_name') ? nj_cuenta_name() : '';
$nj_cards = function_exists('nj_cuenta_home_cards') ? nj_cuenta_home_cards() : array();
$nj_orders = function_exists('nj_cuenta_recent_orders') ? nj_cuenta_recent_orders() : array();
?>
<section class="nj-cuenta-home">
  <h2><?php echo esc_html($nj_hello !== '' ? 'Hola, ' . $nj_hello : 'Hola'); ?></h2>
  <p>Elige qué quieres revisar.</p>
  <?php if ($nj_cards) : ?>
    <div class="nj-cuenta-cards">
      <?php foreach ($nj_cards as $nj_card) : ?>
        <a class="nj-cuenta-card" href="<?php echo esc_url($nj_card['href']); ?>">
          <strong><?php echo esc_html($nj_card['title']); ?></strong>
          <span><?php echo esc_html($nj_card['text']); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?php if ($nj_orders) : ?>
    <h3>Tus últimos pedidos</h3>
    <ul class="nj-cuenta-orders">
      <?php foreach ($nj_orders as $nj_order) : ?>
        <li>
          <a href="<?php echo esc_url($nj_order['href']); ?>">Pedido <?php echo esc_html($nj_order['number']); ?></a>
          <span><?php echo esc_html(trim($nj_order['date'] . ($nj_order['status'] !== '' ? ' · ' . $nj_order['status'] : ''))); ?></span>
          <strong><?php echo wp_kses_post($nj_order['total']); ?></strong>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</section>
<?php
do_action('woocommerce_account_dashboard');
