<?php
require_once dirname(__DIR__) . '/inc/wp-stubs.php';
defined('ABSPATH') || exit;
get_header();
while (have_posts()) {
    the_post();
    $nj_product = function_exists('wc_get_product') ? call_user_func('wc_get_product', nj_current_product_id()) : null;
    if (!$nj_product) {
        continue;
    }
    $GLOBALS['product'] = $nj_product;
    $nj_ficha = nj_ficha_from_card(nj_card_from_product($nj_product));
    require dirname(__DIR__) . '/template-parts/ficha.php';
}
get_footer();
