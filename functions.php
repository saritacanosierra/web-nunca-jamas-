<?php
require_once __DIR__ . '/inc/wp-stubs.php';
require_once __DIR__ . '/inc/helpers.php';
require_once get_template_directory() . '/inc/shop.php';
require_once get_template_directory() . '/inc/cards.php';
require_once get_template_directory() . '/inc/catalog.php';
require_once get_template_directory() . '/inc/portada.php';
require_once get_template_directory() . '/inc/ficha.php';
require_once get_template_directory() . '/inc/paginas.php';

function nj_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 600,
        'single_image_width' => 900,
        'product_grid' => array(
            'default_rows' => 4,
            'min_rows' => 1,
            'max_rows' => 8,
            'default_columns' => 4,
            'min_columns' => 2,
            'max_columns' => 4,
        ),
    ));
}
add_action('after_setup_theme', 'nj_setup');

function nj_assets() {
    wp_enqueue_style(
        'nj-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Baloo+2:wght@700;800&family=Montserrat:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    wp_enqueue_style('nj-home', get_template_directory_uri() . '/assets/home.css', array('nj-fonts'), '1.6.0');
    wp_enqueue_script('nj-bolsa', get_template_directory_uri() . '/assets/bolsa.js', array(), '1.1.1', true);
    wp_enqueue_script('nj-tienda', get_template_directory_uri() . '/assets/tienda.js', array('nj-bolsa'), '1.3.2', true);
    if (function_exists('WC') && WC() && function_exists('home_url') && function_exists('add_query_arg')) {
        wp_localize_script('nj-bolsa', 'njShop', array(
            'ajax' => add_query_arg('wc-ajax', 'add_to_cart', home_url('/')),
            'bag' => add_query_arg('wc-ajax', 'nj_bag', home_url('/')),
            'nonce' => wp_create_nonce('nj-bag'),
        ));
    }
    wp_enqueue_script('nj-home', get_template_directory_uri() . '/assets/home.js', array(), '1.2.9', true);
    if (function_exists('WC')) {
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'nj_assets');

function nj_woocommerce_layout() {
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}
add_action('init', 'nj_woocommerce_layout');

function nj_sale_query($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if (!isset($_GET['on_sale']) || $_GET['on_sale'] !== '1') {
        return;
    }
    $ids = wc_get_product_ids_on_sale();
    $query->set('post__in', $ids ? $ids : array(0));
}
add_action('woocommerce_product_query', 'nj_sale_query');

function nj_cart_fragments($fragments) {
    $fragments['span.bag-count'] = '<span class="bag-count">' . esc_html((string) nj_cart_count()) . '</span>';
    $fragments['script[data-bag-state]'] = '<script type="application/json" data-bag-state>' . wp_json_encode(nj_bag_state()) . '</script>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'nj_cart_fragments');
