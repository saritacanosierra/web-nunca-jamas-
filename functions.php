<?php
require_once __DIR__ . '/inc/helpers.php';
require_once get_template_directory() . '/inc/shop.php';
require_once get_template_directory() . '/inc/colecciones.php';
require_once get_template_directory() . '/inc/testimonios.php';
require_once get_template_directory() . '/inc/bolsa.php';
require_once get_template_directory() . '/inc/pagos.php';
require_once get_template_directory() . '/inc/pedido.php';
require_once get_template_directory() . '/inc/pedidos.php';
require_once get_template_directory() . '/inc/personalizado.php';
require_once get_template_directory() . '/inc/cards.php';
require_once get_template_directory() . '/inc/catalog.php';
require_once get_template_directory() . '/inc/portada.php';
require_once get_template_directory() . '/inc/hero.php';
require_once get_template_directory() . '/inc/espacios.php';
require_once get_template_directory() . '/inc/prendas.php';
require_once get_template_directory() . '/inc/mitienda.php';
require_once get_template_directory() . '/inc/tema.php';
require_once get_template_directory() . '/inc/ficha.php';
require_once get_template_directory() . '/inc/paginas.php';
require_once get_template_directory() . '/inc/blog.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/whatsapp.php';
require_once get_template_directory() . '/inc/cuenta.php';
require_once get_template_directory() . '/inc/avisos.php';

function nj_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('header-footer-elementor');
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

function nj_keep_theme_footer() {
    return false;
}
add_filter('hfe_footer_enabled', 'nj_keep_theme_footer');

function nj_release_theme_footer() {
    global $wp_filter;
    if (!isset($wp_filter['get_footer']) || !is_object($wp_filter['get_footer'])) {
        return;
    }
    $drop = array();
    foreach ($wp_filter['get_footer']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            $fn = isset($callback['function']) ? $callback['function'] : null;
            $label = '';
            if (is_string($fn)) {
                $label = $fn;
            } elseif (is_array($fn) && isset($fn[1])) {
                $owner = is_object($fn[0]) ? get_class($fn[0]) : (string) $fn[0];
                $label = $owner . '::' . $fn[1];
            }
            $label = strtolower($label);
            if ($label === '' || (strpos($label, 'hfe') === false && strpos($label, 'header_footer_elementor') === false && strpos($label, 'elementor') === false)) {
                continue;
            }
            $drop[] = array($fn, (int) $priority);
        }
    }
    foreach ($drop as $item) {
        remove_action('get_footer', $item[0], $item[1]);
    }
}
add_action('wp', 'nj_release_theme_footer', 999);
add_action('template_redirect', 'nj_release_theme_footer', 999);

function nj_fuentes_preload() {
    $uri = get_template_directory_uri();
    echo '<link rel="preload" href="' . esc_url($uri . '/assets/fonts/montserrat-latin.woff2') . '" as="font" type="font/woff2" crossorigin>' . "\n";
    echo '<link rel="preload" href="' . esc_url($uri . '/assets/fonts/anton-latin.woff2') . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
add_action('wp_head', 'nj_fuentes_preload', 1);

function nj_assets() {
    $uri = get_template_directory_uri();
    wp_enqueue_style('nj-fonts', $uri . '/assets/fonts/fuentes.css', array(), NJ_VERSION);
    $screen = function_exists('nj_pantalla') ? nj_pantalla() : array();
    wp_enqueue_style('nj-home', $uri . '/assets/home.css', array('nj-fonts'), NJ_VERSION);
    wp_enqueue_style('nj-cuenta', $uri . '/assets/css/cuenta.css', array('nj-home'), NJ_VERSION);
    if (!empty($screen['catalogo'])) {
        wp_enqueue_style('nj-catalogo', $uri . '/assets/css/catalogo.css', array('nj-home'), NJ_VERSION);
    }
    if (!empty($screen['ficha'])) {
        wp_enqueue_style('nj-ficha-font', $uri . '/assets/fonts/vibes.css', array(), NJ_VERSION);
        wp_enqueue_style('nj-ficha', $uri . '/assets/css/ficha.css', array('nj-home'), NJ_VERSION);
    }
    if (!empty($screen['checkout'])) {
        wp_enqueue_style('nj-checkout', $uri . '/assets/css/checkout.css', array('nj-home'), NJ_VERSION);
    }
    if (!empty($screen['pedido'])) {
        wp_enqueue_style('nj-pedido', $uri . '/assets/css/pedido.css', array('nj-home'), NJ_VERSION);
    }
    if (!empty($screen['blog'])) {
        wp_enqueue_style('nj-blog', $uri . '/assets/css/blog.css', array('nj-home'), NJ_VERSION);
    }
    wp_enqueue_script('nj-bolsa', $uri . '/assets/bolsa.js', array(), NJ_VERSION, true);
    if (!empty($screen['tienda'])) {
        wp_enqueue_script('nj-tienda', $uri . '/assets/tienda.js', array('nj-bolsa'), NJ_VERSION, true);
    }
    if (!empty($screen['ficha_js'])) {
        wp_enqueue_script('nj-ficha', $uri . '/assets/ficha.js', array('nj-tienda'), NJ_VERSION, true);
    }
    if (!empty($screen['checkout_js'])) {
        wp_enqueue_script('nj-checkout', $uri . '/assets/checkout.js', array(), NJ_VERSION, true);
    }
    if (function_exists('wp_localize_script') && function_exists('wp_create_nonce') && function_exists('WC') && WC() && function_exists('home_url') && function_exists('add_query_arg')) {
        wp_localize_script('nj-bolsa', 'njShop', array(
            'ajax' => add_query_arg('wc-ajax', 'add_to_cart', home_url('/')),
            'bag' => add_query_arg('wc-ajax', 'nj_bag', home_url('/')),
            'nonce' => wp_create_nonce(nj_bag_nonce_action()),
        ));
    }
    wp_enqueue_script('nj-home', get_template_directory_uri() . '/assets/home.js', array(), NJ_VERSION, true);
    wp_enqueue_script('nj-cuenta', get_template_directory_uri() . '/assets/cuenta.js', array(), NJ_VERSION, true);
    if (function_exists('wp_localize_script') && function_exists('wp_create_nonce') && function_exists('admin_url')) {
        wp_localize_script('nj-cuenta', 'njCuenta', array(
            'ajax' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('nj_cuenta'),
            'logged' => function_exists('is_user_logged_in') && is_user_logged_in(),
            'saved' => function_exists('nj_cuenta_saved_ids') ? nj_cuenta_saved_ids() : array(),
        ));
    }
    if (function_exists('WC')) {
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'nj_assets');

function nj_order_footer_fallback() {
    if (!empty($GLOBALS['nj_theme_footer'])) {
        return;
    }
    $received = function_exists('is_order_received_page') && is_order_received_page();
    $pay = function_exists('is_checkout_pay_page') && is_checkout_pay_page();
    if (!$received && !$pay) {
        return;
    }
    require get_template_directory() . '/template-parts/pie.php';
}
add_action('wp_footer', 'nj_order_footer_fallback', 4);

function nj_store_chrome() {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    require get_template_directory() . '/template-parts/vista-rapida.php';
    require get_template_directory() . '/template-parts/bolsa.php';
    require get_template_directory() . '/template-parts/cuenta.php';
}
add_action('wp_footer', 'nj_store_chrome', 5);

function nj_woocommerce_layout() {
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}
add_action('init', 'nj_woocommerce_layout');

function nj_cart_fragments($fragments) {
    $fragments['span.bag-count'] = '<span class="bag-count">' . esc_html((string) nj_cart_count()) . '</span>';
    $bag = function_exists('wp_json_encode') ? wp_json_encode(nj_bag_state()) : json_encode(nj_bag_state());
    $fragments['script[data-bag-state]'] = '<script type="application/json" data-bag-state>' . $bag . '</script>';
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'nj_cart_fragments');
