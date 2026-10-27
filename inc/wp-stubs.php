<?php
/**
 * Declaraciones mínimas para el editor cuando el archivo se abre fuera de WordPress.
 * Dentro del sitio estas funciones ya existen y aquí no se vuelven a crear.
 */

if (defined('ABSPATH')) {
    return;
}

if (!function_exists('get_template_directory')) {
    function get_template_directory() {
        return dirname(__DIR__);
    }
}

if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri() {
        return '';
    }
}

if (!function_exists('home_url')) {
    function home_url($path = '', $scheme = null) {
        if ($path === '' || $path === '/') {
            return '/';
        }
        return '/' . ltrim((string) $path, '/');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data;
    }
}

if (!function_exists('add_theme_support')) {
    function add_theme_support($feature, ...$args) {
        return null;
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('remove_action')) {
    function remove_action($hook, $callback, $priority = 10) {
        return true;
    }
}

if (!function_exists('do_action')) {
    function do_action($hook, ...$args) {
        return null;
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args) {
        return $value;
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all') {
        return null;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = array(), $ver = false, $in_footer = false) {
        return null;
    }
}

if (!function_exists('wp_head')) {
    function wp_head() {
        return null;
    }
}

if (!function_exists('wp_footer')) {
    function wp_footer() {
        return null;
    }
}

if (!function_exists('get_header')) {
    function get_header($name = null) {
        return null;
    }
}

if (!function_exists('get_footer')) {
    function get_footer($name = null) {
        return null;
    }
}

if (!function_exists('have_posts')) {
    function have_posts() {
        return false;
    }
}

if (!function_exists('the_post')) {
    function the_post() {
        return null;
    }
}

if (!function_exists('the_content')) {
    function the_content($more_link_text = null, $strip_teaser = false) {
        return null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return false;
    }
}

if (!function_exists('add_query_arg')) {
    function add_query_arg(...$args) {
        return '';
    }
}

if (!function_exists('get_terms')) {
    function get_terms($args = array(), $deprecated = '') {
        return array();
    }
}

if (!function_exists('get_term')) {
    function get_term($term, $taxonomy = '', $output = null, $filter = 'raw') {
        return null;
    }
}

if (!function_exists('get_ancestors')) {
    function get_ancestors($object_id = 0, $object_type = '', $resource_type = '') {
        return array();
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return false;
    }
}

if (!function_exists('get_term_link')) {
    function get_term_link($term, $taxonomy = '') {
        return '';
    }
}

if (!function_exists('get_term_meta')) {
    function get_term_meta($term_id, $key = '', $single = false) {
        return $single ? '' : array();
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post = 0, $leavename = false) {
        return '';
    }
}

if (!function_exists('get_the_ID')) {
    function get_the_ID() {
        return 0;
    }
}

if (!function_exists('get_queried_object')) {
    function get_queried_object() {
        return null;
    }
}

if (!function_exists('is_product_category')) {
    function is_product_category($term = '') {
        return false;
    }
}

if (!function_exists('wp_unslash')) {
    function wp_unslash($value) {
        if (is_array($value)) {
            return array_map('wp_unslash', $value);
        }
        return is_string($value) ? stripslashes($value) : $value;
    }
}

if (!function_exists('sanitize_key')) {
    function sanitize_key($key) {
        $key = strtolower((string) $key);
        return preg_replace('/[^a-z0-9_\-]/', '', $key);
    }
}

if (!function_exists('get_the_terms')) {
    function get_the_terms($post, $taxonomy) {
        return array();
    }
}

if (!function_exists('term_description')) {
    function term_description($term = 0, $taxonomy = 'post_tag') {
        return '';
    }
}

if (!function_exists('wp_get_attachment_image_url')) {
    function wp_get_attachment_image_url($attachment_id, $size = 'thumbnail', $icon = false) {
        return '';
    }
}

if (!function_exists('wc_get_page_permalink')) {
    function wc_get_page_permalink($page, $fallback = null) {
        return '';
    }
}

if (!function_exists('wc_get_cart_url')) {
    function wc_get_cart_url() {
        return '';
    }
}

if (!function_exists('wc_get_products')) {
    function wc_get_products($args = array()) {
        return array();
    }
}

if (!function_exists('wc_get_product_ids_on_sale')) {
    function wc_get_product_ids_on_sale() {
        return array();
    }
}

if (!function_exists('wc_placeholder_img_src')) {
    function wc_placeholder_img_src($size = 'woocommerce_thumbnail') {
        return '';
    }
}

if (!function_exists('wc_get_template_part')) {
    function wc_get_template_part($slug, $name = '') {
        return null;
    }
}

if (!function_exists('wc_product_class')) {
    function wc_product_class($class = '', $product = null) {
        return null;
    }
}

if (!function_exists('woocommerce_page_title')) {
    function woocommerce_page_title($echo = true) {
        return '';
    }
}

if (!function_exists('woocommerce_product_loop')) {
    function woocommerce_product_loop() {
        return false;
    }
}

if (!function_exists('woocommerce_product_loop_start')) {
    function woocommerce_product_loop_start($echo = true) {
        return null;
    }
}

if (!function_exists('woocommerce_product_loop_end')) {
    function woocommerce_product_loop_end($echo = true) {
        return null;
    }
}

if (!function_exists('WC')) {
    function WC() {
        return null;
    }
}
