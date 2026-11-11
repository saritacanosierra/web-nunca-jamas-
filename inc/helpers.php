<?php
/**
 * Rutas de la portada. En la vista previa no hace falta WordPress.
 */

if (!defined('NJ_VERSION')) {
    define('NJ_VERSION', '1.9.86');
}

if (!defined('ABSPATH')) {
    require_once __DIR__ . '/wp-stubs.php';
}

if (!function_exists('nj_img')) {
    function nj_img($file) {
        if (preg_match('#^https?://#i', $file)) {
            return $file;
        }
        if (defined('NJ_PREVIEW') || !function_exists('get_template_directory_uri')) {
            return '/assets/img/' . $file;
        }
        $uri = get_template_directory_uri();
        if ($uri === '') {
            return 'assets/img/' . $file;
        }
        return $uri . '/assets/img/' . $file;
    }
}

if (!function_exists('nj_home_url')) {
    function nj_home_url() {
        if (defined('NJ_PREVIEW')) {
            return 'preview.php';
        }
        if (function_exists('home_url')) {
            $url = home_url('/');
            if ($url !== '') {
                return $url;
            }
        }
        return '/';
    }
}

if (!function_exists('nj_e')) {
    function nj_e($text) {
        echo esc_html($text);
    }
}

if (!function_exists('nj_pantalla')) {
    function nj_pantalla() {
        $screen = array(
            'catalogo' => false,
            'ficha' => false,
            'checkout' => false,
            'pedido' => false,
            'blog' => false,
            'tienda' => false,
            'ficha_js' => false,
            'checkout_js' => false,
        );
        if (defined('NJ_PREVIEW')) {
            $script = isset($_SERVER['SCRIPT_NAME']) ? strtolower(basename($_SERVER['SCRIPT_NAME'])) : '';
            $screen['ficha'] = $script === 'producto.php';
            $screen['ficha_js'] = $screen['ficha'];
            $screen['catalogo'] = true;
            $screen['tienda'] = true;
            return $screen;
        }
        $front = function_exists('is_front_page') && is_front_page();
        $woo = function_exists('is_woocommerce') && is_woocommerce();
        $product = function_exists('is_product') && is_product();
        $account = function_exists('is_account_page') && is_account_page();
        $cart = function_exists('is_cart') && is_cart();
        $search = function_exists('is_search') && is_search();
        $received = function_exists('is_order_received_page') && is_order_received_page();
        $checkout = function_exists('is_checkout') && is_checkout() && !$received;
        $screen['catalogo'] = $front || $woo || $product || $account || $cart || $search;
        $screen['tienda'] = $screen['catalogo'];
        $screen['ficha'] = $product;
        $screen['ficha_js'] = $product;
        $screen['checkout'] = $checkout;
        $screen['checkout_js'] = $checkout;
        $screen['pedido'] = $received;
        $screen['blog'] = function_exists('is_singular') && (is_singular('post') || (function_exists('is_home') && is_home()) || (function_exists('is_category') && is_category()) || (function_exists('is_page') && is_page('blog')));
        return $screen;
    }
}

