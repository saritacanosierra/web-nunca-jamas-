<?php
/**
 * Solo la vista previa, cuando WordPress no está cargado.
 * El tema en el sitio no incluye este archivo.
 */

if (defined('ABSPATH')) {
    return;
}

if (!defined('OBJECT')) {
    define('OBJECT', 'OBJECT');
}
if (!defined('OBJECT_K')) {
    define('OBJECT_K', 'OBJECT_K');
}
if (!defined('ARRAY_A')) {
    define('ARRAY_A', 'ARRAY_A');
}
if (!defined('ARRAY_N')) {
    define('ARRAY_N', 'ARRAY_N');
}
if (!defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}
if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}
if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

if (!function_exists('get_current_user_id')) {
    function get_current_user_id() {
        return 0;
    }
}

if (!function_exists('get_user_meta')) {
    function get_user_meta($user_id, $key = '', $single = false) {
        return $single ? '' : array();
    }
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

if (!class_exists('WP_Term')) {
    class WP_Term {
        public $term_id = 0;
        public $name = '';
        public $slug = '';
        public $taxonomy = '';
        public $parent = 0;

        public function __construct($term_id = 0, $name = '', $slug = '', $taxonomy = '') {
            $this->term_id = (int) $term_id;
            $this->name = (string) $name;
            $this->slug = (string) $slug;
            $this->taxonomy = (string) $taxonomy;
        }
    }
}

if (!function_exists('get_term')) {
    function get_term($term, $taxonomy = '', $output = null, $filter = 'raw') {
        if ($term instanceof WP_Term) {
            return $term;
        }
        if (!is_numeric($term) || (int) $term < 1) {
            return new WP_Error('invalid_term', '');
        }
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

        public function get_error_data($code = '') {
            if ($code === '') {
                $code = $this->get_error_code();
            }
            return isset($this->error_data[$code]) ? $this->error_data[$code] : null;
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

if (!function_exists('is_page')) {
    function is_page($page = '') {
        return false;
    }
}

if (!function_exists('is_singular')) {
    function is_singular($post_types = '') {
        return false;
    }
}

if (!function_exists('is_home')) {
    function is_home() {
        return false;
    }
}

if (!function_exists('is_category')) {
    function is_category($category = '') {
        return false;
    }
}

if (!function_exists('is_front_page')) {
    function is_front_page() {
        return false;
    }
}

if (!function_exists('is_shop')) {
    function is_shop() {
        return false;
    }
}

if (!function_exists('is_product')) {
    function is_product() {
        return false;
    }
}

if (!function_exists('is_checkout')) {
    function is_checkout() {
        return false;
    }
}

if (!function_exists('is_cart')) {
    function is_cart() {
        return false;
    }
}

if (!function_exists('is_search')) {
    function is_search() {
        return false;
    }
}

if (!function_exists('is_404')) {
    function is_404() {
        return false;
    }
}

if (!function_exists('is_tax')) {
    function is_tax($taxonomy = '', $term = '') {
        return false;
    }
}

if (!function_exists('is_tag')) {
    function is_tag($tag = '') {
        return false;
    }
}

if (!function_exists('is_author')) {
    function is_author($author = '') {
        return false;
    }
}

if (!function_exists('is_attachment')) {
    function is_attachment($attachment = '') {
        return false;
    }
}

if (!function_exists('is_account_page')) {
    function is_account_page() {
        return false;
    }
}

if (!function_exists('is_order_received_page')) {
    function is_order_received_page() {
        return false;
    }
}

if (!function_exists('is_checkout_pay_page')) {
    function is_checkout_pay_page() {
        return false;
    }
}

if (!function_exists('is_view_order_page')) {
    function is_view_order_page() {
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

if (!function_exists('get_posts')) {
    function get_posts($args = array()) {
        return array();
    }
}

if (!function_exists('get_post')) {
    function get_post($post = null, $output = OBJECT, $filter = 'raw') {
        if ($post instanceof WP_Post) {
            return $post;
        }
        return null;
    }
}

if (!function_exists('wp_insert_post')) {
    function wp_insert_post($postarr, $wp_error = false, $fire_after_hooks = true) {
        return 0;
    }
}

if (!function_exists('wp_update_post')) {
    function wp_update_post($postarr = array(), $wp_error = false, $fire_after_hooks = true) {
        return 0;
    }
}

if (!function_exists('set_post_thumbnail')) {
    function set_post_thumbnail($post, $thumbnail_id) {
        return false;
    }
}

if (!function_exists('delete_post_thumbnail')) {
    function delete_post_thumbnail($post) {
        return false;
    }
}

if (!function_exists('get_post_meta')) {
    function get_post_meta($post_id, $key = '', $single = false) {
        return $single ? '' : array();
    }
}

if (!function_exists('update_post_meta')) {
    function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '') {
        return true;
    }
}

if (!function_exists('get_post_thumbnail_id')) {
    function get_post_thumbnail_id($post = null) {
        return 0;
    }
}

if (!function_exists('sanitize_textarea_field')) {
    function sanitize_textarea_field($str) {
        return trim(strip_tags((string) $str));
    }
}

if (!function_exists('wp_set_post_categories')) {
    function wp_set_post_categories($post_id = 0, $post_categories = array(), $append = false) {
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

if (!function_exists('wc_get_account_endpoint_url')) {
    function wc_get_account_endpoint_url($endpoint, $value = '', $permalink = '') {
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

if (!class_exists('WC_Cart')) {
    class WC_Cart {
        public function get_cart() {
            return array();
        }
        public function get_displayed_subtotal() {
            return 0;
        }
        public function get_subtotal() {
            return 0;
        }
        public function get_discount_total() {
            return 0;
        }
        public function get_total($context = 'view') {
            return 0;
        }
        public function get_cart_contents_count() {
            return 0;
        }
        public function add_discount($code) {
            return false;
        }
        public function remove_cart_item($key) {
            return false;
        }
        public function set_quantity($key, $quantity = 1, $refresh_totals = true) {
            return true;
        }
        public function calculate_totals() {
        }
    }
}

if (!class_exists('WooCommerce')) {
    class WooCommerce {
        /** @var WC_Cart|null */
        public $cart = null;

        public function __construct($cart = null) {
            $this->cart = $cart instanceof WC_Cart ? $cart : null;
        }

        public function payment_gateways() {
            return null;
        }
    }
}

if (!function_exists('WC')) {
    function WC() {
        if (isset($GLOBALS['woocommerce']) && $GLOBALS['woocommerce'] instanceof WooCommerce) {
            return $GLOBALS['woocommerce'];
        }
        return null;
    }
}

if (!function_exists('add_submenu_page')) {
    function add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback = '', $position = null) {
        return '';
    }
}

if (!function_exists('body_class')) {
    function body_class($css_class = '') {
        echo 'class=""';
    }
}

if (!function_exists('wp_body_open')) {
    function wp_body_open() {
    }
}

if (!function_exists('the_title')) {
    function the_title($before = '', $after = '', $echo = true) {
        if ($echo) {
            echo $before . $after;
        }
        return '';
    }
}

if (!function_exists('clean_post_cache')) {
    function clean_post_cache($post) {
    }
}

if (!class_exists('WP_Post')) {
    class WP_Post {
        public $ID = 0;
        public $post_content = '';
        public $post_title = '';
        public $post_excerpt = '';
        public $post_name = '';
        public $post_status = '';
        public $post_type = '';

        public function __construct($id = 0) {
            $this->ID = (int) $id;
        }
    }
}

if (!function_exists('get_page_by_path')) {
    function get_page_by_path($page_path, $output = OBJECT, $post_type = 'page') {
        if ($page_path instanceof WP_Post) {
            return $page_path;
        }
        return null;
    }
}

if (!function_exists('get_page_by_title')) {
    function get_page_by_title($page_title, $output = OBJECT, $post_type = 'page') {
        return null;
    }
}

if (!function_exists('get_post_field')) {
    function get_post_field($field, $post = null, $context = 'display') {
        return '';
    }
}

if (!function_exists('get_query_var')) {
    function get_query_var($query_var, $default = '') {
        return $default;
    }
}

if (!function_exists('get_term_by')) {
    function get_term_by($field, $value, $taxonomy = '', $output = OBJECT, $filter = 'raw') {
        return false;
    }
}

if (!function_exists('get_the_post_thumbnail_url')) {
    function get_the_post_thumbnail_url($post = null, $size = 'post-thumbnail') {
        return '';
    }
}

if (!function_exists('get_transient')) {
    function get_transient($transient) {
        return false;
    }
}

if (!function_exists('set_transient')) {
    function set_transient($transient, $value, $expiration = 0) {
        return true;
    }
}

if (!function_exists('delete_transient')) {
    function delete_transient($transient) {
        return true;
    }
}

if (!function_exists('nocache_headers')) {
    function nocache_headers() {
    }
}

if (!function_exists('paginate_links')) {
    function paginate_links($args = '') {
        return '';
    }
}

if (!function_exists('register_post_status')) {
    function register_post_status($post_status, $args = array()) {
        return $args;
    }
}

if (!function_exists('register_taxonomy')) {
    function register_taxonomy($taxonomy, $object_type, $args = array()) {
        return true;
    }
}

if (!function_exists('remove_filter')) {
    function remove_filter($hook_name, $callback, $priority = 10) {
        return true;
    }
}

if (!function_exists('sanitize_title')) {
    function sanitize_title($title, $fallback_title = '', $context = 'save') {
        $title = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', (string) $title), '-'));
        return $title === '' ? $fallback_title : $title;
    }
}

if (!function_exists('status_header')) {
    function status_header($code, $description = '') {
    }
}

if (!function_exists('taxonomy_exists')) {
    function taxonomy_exists($taxonomy) {
        return false;
    }
}

if (!function_exists('untrailingslashit')) {
    function untrailingslashit($value) {
        return rtrim((string) $value, '/\\');
    }
}

if (!function_exists('user_trailingslashit')) {
    function user_trailingslashit($string, $type_of_url = '') {
        return (string) $string;
    }
}

if (!function_exists('wp_date')) {
    function wp_date($format, $timestamp = null, $timezone = null) {
        return date((string) $format, $timestamp ? (int) $timestamp : time());
    }
}

if (!function_exists('wp_delete_term')) {
    function wp_delete_term($term, $taxonomy, $args = array()) {
        return true;
    }
}

if (!function_exists('wp_doing_ajax')) {
    function wp_doing_ajax() {
        return false;
    }
}

if (!function_exists('wp_get_canonical_url')) {
    function wp_get_canonical_url($post = null) {
        return '';
    }
}

if (!function_exists('wp_get_document_title')) {
    function wp_get_document_title() {
        return '';
    }
}

if (!function_exists('wp_get_post_categories')) {
    function wp_get_post_categories($post_id = 0, $args = array()) {
        return array();
    }
}

if (!function_exists('wp_insert_term')) {
    function wp_insert_term($term, $taxonomy, $args = array()) {
        if (!is_string($term) || $term === '') {
            return new WP_Error('invalid_term', '');
        }
        return array('term_id' => 0, 'term_taxonomy_id' => 0);
    }
}

if (!function_exists('wp_update_term')) {
    function wp_update_term($term_id, $taxonomy, $args = array()) {
        return array('term_id' => (int) $term_id, 'term_taxonomy_id' => 0);
    }
}

if (!function_exists('wp_mail')) {
    function wp_mail($to, $subject, $message, $headers = '', $attachments = array()) {
        return false;
    }
}

if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = array()) {
        return array('response' => array('code' => 0), 'body' => '');
    }
}

if (!function_exists('check_ajax_referer')) {
    /**
     * @return int|false
     */
    function check_ajax_referer($action = -1, $query_arg = false, $die = true) {
        return 1;
    }
}

if (!function_exists('wp_send_json')) {
    function wp_send_json($response, $status_code = null, $flags = 0) {
        echo json_encode($response);
        exit;
    }
}

if (!function_exists('wp_set_object_terms')) {
    function wp_set_object_terms($object_id, $terms, $taxonomy, $append = false) {
        return array();
    }
}

if (!function_exists('wpautop')) {
    function wpautop($text, $br = true) {
        return (string) $text;
    }
}

if (!function_exists('_n_noop')) {
    function _n_noop($singular, $plural, $domain = null) {
        return array($singular, $plural);
    }
}

if (!function_exists('get_woocommerce_currency')) {
    function get_woocommerce_currency() {
        return 'COP';
    }
}

if (!function_exists('wc_attribute_taxonomy_id_by_name')) {
    function wc_attribute_taxonomy_id_by_name($name) {
        return 0;
    }
}

if (!function_exists('wc_clean')) {
    function wc_clean($var) {
        return is_scalar($var) ? trim((string) $var) : $var;
    }
}

if (!function_exists('wc_clear_notices')) {
    function wc_clear_notices() {
    }
}

if (!function_exists('wc_create_attribute')) {
    function wc_create_attribute($args) {
        return 0;
    }
}

if (!function_exists('wc_delete_product_transients')) {
    function wc_delete_product_transients($post_id = 0) {
    }
}

if (!function_exists('wc_format_coupon_code')) {
    function wc_format_coupon_code($value) {
        return trim((string) $value);
    }
}

if (!function_exists('wc_get_checkout_url')) {
    function wc_get_checkout_url() {
        return '/checkout/';
    }
}

if (!function_exists('wc_get_notices')) {
    function wc_get_notices($notice_type = '') {
        return array();
    }
}

if (!class_exists('WC_Order')) {
    class WC_Order {
        public function get_id() {
            return 0;
        }
        public function get_meta($key = '', $single = true) {
            return '';
        }
        public function update_meta_data($key, $value) {
            return $this;
        }
        public function get_items($types = 'line_item') {
            return array();
        }
        public function get_date_paid() {
            return null;
        }
        public function add_order_note($note) {
            return 0;
        }
        public function save() {
            return 0;
        }
        public function has_status($status) {
            return false;
        }
        public function is_paid() {
            return false;
        }
        public function needs_payment() {
            return false;
        }
    }
}

if (!function_exists('wc_get_order')) {
    function wc_get_order($the_order = false) {
        if ($the_order instanceof WC_Order) {
            return $the_order;
        }
        return null;
    }
}

if (!function_exists('wc_get_page_id')) {
    function wc_get_page_id($page) {
        return 0;
    }
}

if (!function_exists('wc_get_product')) {
    function wc_get_product($the_product = false, $deprecated = array()) {
        if ($the_product instanceof WC_Product) {
            return $the_product;
        }
        return null;
    }
}

if (!function_exists('wc_get_stock_html')) {
    function wc_get_stock_html($product = null) {
        return '';
    }
}

if (!function_exists('wc_price')) {
    function wc_price($price, $args = array()) {
        return (string) $price;
    }
}

if (!function_exists('woocommerce_register_additional_checkout_field')) {
    function woocommerce_register_additional_checkout_field($options) {
        return null;
    }
}

if (!class_exists('WC_Product')) {
    class WC_Product {
        public function get_id() {
            return 0;
        }
        public function get_name() {
            return '';
        }
        public function get_price() {
            return '';
        }
        public function get_regular_price() {
            return '';
        }
        public function is_type($type) {
            return false;
        }
        public function get_children() {
            return array();
        }
        public function get_attributes() {
            return array();
        }
        public function set_name($name) {
            return $this;
        }
        public function set_status($status) {
            return $this;
        }
        public function set_catalog_visibility($visibility) {
            return $this;
        }
        public function set_description($description) {
            return $this;
        }
        public function set_short_description($description) {
            return $this;
        }
        public function set_sku($sku) {
            return $this;
        }
        public function set_image_id($image_id) {
            return $this;
        }
        public function set_gallery_image_ids($ids) {
            return $this;
        }
        public function set_category_ids($ids) {
            return $this;
        }
        public function set_attributes($attributes) {
            return $this;
        }
        public function set_regular_price($price) {
            return $this;
        }
        public function set_sale_price($price) {
            return $this;
        }
        public function get_parent_id() {
            return 0;
        }
        public function set_parent_id($parent_id) {
            return $this;
        }
        public function set_manage_stock($manage) {
            return $this;
        }
        public function set_stock_quantity($quantity) {
            return $this;
        }
        public function set_stock_status($status) {
            return $this;
        }
        public function save() {
            return 0;
        }
        public function delete($force = false) {
            return true;
        }
    }
}

if (!class_exists('WC_Product_Simple')) {
    class WC_Product_Simple extends WC_Product {
    }
}

if (!class_exists('WC_Product_Variable')) {
    class WC_Product_Variable extends WC_Product {
        public static function sync($product_id) {
            return $product_id;
        }
    }
}

if (!class_exists('WC_Product_Variation')) {
    class WC_Product_Variation extends WC_Product {
    }
}

if (!class_exists('WC_Product_Attribute')) {
    class WC_Product_Attribute {
        public function set_id($id) {
            return $this;
        }
        public function set_name($name) {
            return $this;
        }
        public function get_name() {
            return '';
        }
        public function set_options($options) {
            return $this;
        }
        public function set_position($position) {
            return $this;
        }
        public function set_visible($visible) {
            return $this;
        }
        public function set_variation($variation) {
            return $this;
        }
    }
}

if (!class_exists('WC_Shipping_Zone')) {
    class WC_Shipping_Zone {
        public function __construct($zone = 0) {
        }
        public function get_shipping_methods($enabled_only = false, $context = 'admin') {
            return array();
        }
        public function add_shipping_method($method) {
            return 0;
        }
        public function set_zone_name($name) {
            return $this;
        }
        public function add_location($code, $type) {
            return $this;
        }
        public function save() {
            return 0;
        }
    }
}

if (!class_exists('WC_Shipping_Zones')) {
    class WC_Shipping_Zones {
        public static function get_zones() {
            return array();
        }
        /**
         * @param int $instance_id
         * @return object|false
         */
        public static function get_shipping_method($instance_id) {
            return false;
        }
    }
}

if (!class_exists('WC_Cache_Helper')) {
    class WC_Cache_Helper {
        public static function get_transient_version($group, $refresh = false) {
            return '1';
        }
    }
}

if (!class_exists('WC_AJAX')) {
    class WC_AJAX {
        public static function get_refreshed_fragments() {
        }
    }
}
