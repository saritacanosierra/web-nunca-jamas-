<?php
require_once dirname(__DIR__) . '/inc/wp-stubs.php';
defined('ABSPATH') || exit;
global $product;
if (empty($product) || !$product->is_visible()) {
    return;
}
$card = nj_card_from_product($product);
?>
<li <?php wc_product_class('', $product); ?>>
  <?php
  $item = $card;
  require get_template_directory() . '/template-parts/product-card.php';
  ?>
</li>
