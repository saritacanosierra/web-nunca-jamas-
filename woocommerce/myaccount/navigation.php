<?php
/**
 * Menú de Mi cuenta.
 */
defined('ABSPATH') || exit;

$nj_items = function_exists('wc_get_account_menu_items') ? wc_get_account_menu_items() : array();
?>
<nav class="woocommerce-MyAccount-navigation" aria-label="Mi cuenta">
  <ul>
    <?php foreach ($nj_items as $nj_endpoint => $nj_label) : ?>
      <li class="<?php echo function_exists('wc_get_account_menu_item_classes') ? esc_attr(wc_get_account_menu_item_classes($nj_endpoint)) : ''; ?>">
        <a href="<?php echo esc_url(function_exists('wc_get_account_endpoint_url') ? wc_get_account_endpoint_url($nj_endpoint) : ''); ?>"><?php echo esc_html($nj_label); ?></a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
