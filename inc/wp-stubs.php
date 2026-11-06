<?php
/**
 * Solo la vista previa, cuando WordPress no está cargado.
 * El tema en el sitio no incluye este archivo.
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

if (!function_exists('admin_url')) {
    function admin_url($path = '', $scheme = 'admin') {
        $path = ltrim((string) $path, '/');
        return $path === '' ? '/wp-admin/' : '/wp-admin/' . $path;
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

if (!function_exists('wp_localize_script')) {
    function wp_localize_script($handle, $object_name, $l10n) {
        return null;
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action = -1) {
        return '';
    }
}

if (!function_exists('wp_nonce_field')) {
    function wp_nonce_field($action = -1, $name = '_wpnonce', $referer = true, $display = true) {
        return '';
    }
}

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0, $depth = 512) {
        return json_encode($data, $options, $depth);
    }
}

if (!function_exists('wp_remote_request')) {
    function wp_remote_request($url, $args = array()) {
        return array(
            'response' => array('code' => 0),
            'body' => '',
        );
    }
}

if (!function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code($response) {
        return is_array($response) && isset($response['response']['code']) ? (int) $response['response']['code'] : 0;
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response) {
        return is_array($response) && isset($response['body']) ? (string) $response['body'] : '';
    }
}

if (!function_exists('selected')) {
    function selected($selected, $current = true, $display = true) {
        $result = (string) $selected === (string) $current ? " selected='selected'" : '';
        if ($display) {
            echo $result;
        }
        return $result;
    }
}

if (!function_exists('checked')) {
    function checked($checked, $current = true, $display = true) {
        $result = (string) $checked === (string) $current ? " checked='checked'" : '';
        if ($display) {
            echo $result;
        }
        return $result;
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

if (!class_exists('WP_Error')) {
    class WP_Error {
        public $errors = array();
        public $error_data = array();

        public function __construct($code = '', $message = '', $data = '') {
            if ($code === '') {
                return;
            }
            $this->add($code, $message, $data);
        }

        public function add($code, $message, $data = '') {
            $this->errors[$code][] = $message;
            if ($data !== '') {
                $this->error_data[$code] = $data;
            }
        }

        public function get_error_code() {
            $codes = array_keys($this->errors);
            return $codes ? $codes[0] : '';
        }

        public function get_error_message($code = '') {
            if ($code === '') {
                $code = $this->get_error_code();
            }
            if (!isset($this->errors[$code][0])) {
                return '';
            }
            return (string) $this->errors[$code][0];
        }

        public function has_errors() {
            return $this->errors !== array();
        }
    }
}

if (!class_exists('WP_Query')) {
    class WP_Query {
        public $posts = array();
        public $post_count = 0;
        public $max_num_pages = 0;
        public $query_vars = array();

        public function __construct($query = array()) {
            $this->query_vars = is_array($query) ? $query : array();
        }

        public function have_posts() {
            return $this->posts !== array();
        }

        public function get($query_var, $default = '') {
            return isset($this->query_vars[$query_var]) ? $this->query_vars[$query_var] : $default;
        }
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return $thing instanceof WP_Error;
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

if (!function_exists('update_term_meta')) {
    function update_term_meta($term_id, $meta_key, $meta_value, $prev_value = '') {
        return true;
    }
}

if (!function_exists('delete_term_meta')) {
    function delete_term_meta($term_id, $meta_key, $meta_value = '') {
        return true;
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink($post = 0, $leavename = false) {
        return '';
    }
}

if (!function_exists('get_the_title')) {
    function get_the_title($post = 0) {
        if (is_object($post) && isset($post->post_title)) {
            return (string) $post->post_title;
        }
        return '';
    }
}

if (!function_exists('get_the_excerpt')) {
    function get_the_excerpt($post = null) {
        if (is_object($post) && isset($post->post_excerpt)) {
            return (string) $post->post_excerpt;
        }
        return '';
    }
}

if (!function_exists('get_the_date')) {
    function get_the_date($format = '', $post = null) {
        return '';
    }
}

if (!function_exists('get_the_modified_date')) {
    function get_the_modified_date($format = '', $post = null) {
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

if (!function_exists('get_queried_object_id')) {
    function get_queried_object_id() {
        return 0;
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

if (!function_exists('absint')) {
    function absint($maybeint) {
        return abs((int) $maybeint);
    }
}

if (!function_exists('sanitize_hex_color')) {
    function sanitize_hex_color($color) {
        $color = trim((string) $color);
        if ($color === '') {
            return '';
        }
        if (preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color)) {
            return $color;
        }
        return '';
    }
}

if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        return $default;
    }
}

if (!function_exists('update_option')) {
    function update_option($option, $value, $autoload = null) {
        return true;
    }
}

if (!function_exists('delete_option')) {
    function delete_option($option) {
        return true;
    }
}

if (!function_exists('esc_url_raw')) {
    function esc_url_raw($url, $protocols = null) {
        return trim((string) $url);
    }
}

if (!function_exists('esc_textarea')) {
    function esc_textarea($text) {
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags($text, $remove_breaks = false) {
        $text = strip_tags((string) $text);
        if ($remove_breaks) {
            $text = preg_replace('/[\r\n\t ]+/', ' ', $text);
        }
        return trim((string) $text);
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(strip_tags((string) $str));
    }
}

if (!function_exists('add_menu_page')) {
    function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback = '', $icon_url = '', $position = null) {
        return $menu_slug;
    }
}

if (!function_exists('wp_enqueue_media')) {
    function wp_enqueue_media($args = array()) {
        return null;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability, ...$args) {
        return false;
    }
}

if (!function_exists('wp_die')) {
    function wp_die($message = '', $title = '', $args = array()) {
        return null;
    }
}

if (!function_exists('check_admin_referer')) {
    function check_admin_referer($action = -1, $query_arg = '_wpnonce') {
        return 1;
    }
}

if (!function_exists('wp_safe_redirect')) {
    function wp_safe_redirect($location, $status = 302, $x_redirect_by = 'WordPress') {
        return false;
    }
}

if (!function_exists('wp_attachment_is_image')) {
    function wp_attachment_is_image($post = null) {
        return false;
    }
}

if (!function_exists('get_pages')) {
    function get_pages($args = array()) {
        return array();
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
