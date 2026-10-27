<?php
/**
 * Datos de la tienda. Con WooCommerce salen de los productos;
 * sin WordPress, de inc/slots.php para la vista previa.
 */

require_once __DIR__ . '/wp-stubs.php';

if (!function_exists('nj_slots_data')) {
    function nj_slots_data() {
        static $slots = null;
        if ($slots === null) {
            $slots = nj_slots_inside(require __DIR__ . '/slots.php');
        }
        return $slots;
    }
}

if (!function_exists('nj_shop_link')) {
    function nj_shop_link($which = 'shop') {
        $shop = '';
        if (function_exists('wc_get_page_permalink')) {
            $shop = wc_get_page_permalink('shop');
        }
        if ($which === 'cart' && function_exists('wc_get_cart_url')) {
            return wc_get_cart_url();
        }
        if ($which === 'checkout' && function_exists('wc_get_checkout_url')) {
            $checkout = wc_get_checkout_url();
            if ($checkout) {
                return $checkout;
            }
        }
        if ($which === 'account' && function_exists('wc_get_page_permalink')) {
            $account = wc_get_page_permalink('myaccount');
            if ($account) {
                return $account;
            }
        }
        if ($shop) {
            if ($which === 'newest') {
                return add_query_arg('orderby', 'date', $shop);
            }
            return $shop;
        }
        return nj_home_url() . '#prendas';
    }
}

if (!function_exists('nj_cart_count')) {
    function nj_cart_count() {
        if (function_exists('WC')) {
            $woocommerce = WC();
            if (is_object($woocommerce) && isset($woocommerce->cart) && $woocommerce->cart) {
                return (int) $woocommerce->cart->get_cart_contents_count();
            }
        }
        return 0;
    }
}

if (!function_exists('nj_slots_inside')) {
    function nj_slots_inside($value) {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = nj_slots_inside($item);
            }
            return $value;
        }
        if (is_string($value) && preg_match('#^https?://(www\.)?nuncajamasropa\.com#i', $value)) {
            return '';
        }
        return $value;
    }
}

if (!function_exists('nj_catalog_key')) {
    function nj_catalog_key($label) {
        $key = nj_menu_key($label);
        $aliases = array(
            'blusas' => 'ninas',
            'vestidos' => 'ninas',
            'conjuntos' => 'ninas',
            'faldas' => 'ninas',
            'casuales' => 'ninas',
            'fiesta' => 'ninas',
            'pijamas' => 'ninas',
            'escolar' => 'linea',
            'uniformes' => 'linea',
            'denim' => 'linea',
            'lineadeportiva' => 'linea',
            'calzadopersonal' => 'accesorios',
            'mascotas' => 'accesorios',
            'tocados' => 'accesorios',
            'bolsos' => 'accesorios',
            'medias' => 'accesorios',
            'licencias' => 'colecciones',
            'especiales' => 'colecciones',
            'basicos' => 'colecciones',
        );
        if (isset($aliases[$key])) {
            $key = $aliases[$key];
        }
        $catalogs = nj_slots_data()['catalogs'];
        if (isset($catalogs[$key]['fallback']) && $catalogs[$key]['fallback'] && !isset($catalogs[$key]['pills'])) {
            return $catalogs[$key]['fallback'];
        }
        if (isset($catalogs[$key])) {
            return $key;
        }
        return '';
    }
}

if (!function_exists('nj_section_link')) {
    function nj_section_link($label) {
        $key = nj_catalog_key($label);
        if ($key === '') {
            return nj_shop_link('shop');
        }
        if (defined('NJ_PREVIEW')) {
            return 'galeria.php?seccion=' . rawurlencode($key);
        }
        $index = nj_category_index();
        if (isset($index[$key])) {
            $link = get_term_link($index[$key]);
            if (!is_wp_error($link) && $link) {
                return $link;
            }
        }
        return nj_shop_link('shop');
    }
}

if (!function_exists('nj_menu_key')) {
    function nj_menu_key($value) {
        $value = strtolower((string) $value);
        return str_replace(
            array('á', 'é', 'í', 'ó', 'ú', 'ñ', ' '),
            array('a', 'e', 'i', 'o', 'u', 'n', ''),
            $value
        );
    }
}

if (!function_exists('nj_category_index')) {
    function nj_category_index() {
        if (!function_exists('get_terms')) {
            return array();
        }
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => 0,
        ));
        if (is_wp_error($terms) || !$terms) {
            return array();
        }
        $index = array();
        foreach ($terms as $term) {
            if (in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                continue;
            }
            $index[nj_menu_key($term->slug)] = $term;
            $index[nj_menu_key($term->name)] = $term;
        }
        return $index;
    }
}

if (!function_exists('nj_menu_children')) {
    function nj_menu_children($term) {
        $children = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => $term->term_id,
        ));
        if (is_wp_error($children) || !$children) {
            return array();
        }
        $items = array();
        foreach ($children as $child) {
            $link = get_term_link($child);
            if (is_wp_error($link)) {
                continue;
            }
            $items[] = array(
                'label' => $child->name,
                'href' => $link,
            );
        }
        return $items;
    }
}

if (!function_exists('nj_menu_apply')) {
    function nj_menu_apply($item, $index) {
        $term = isset($index[nj_menu_key($item['label'])]) ? $index[nj_menu_key($item['label'])] : null;
        if (!$term) {
            return $item;
        }
        $link = get_term_link($term);
        if (!is_wp_error($link) && $link) {
            $item['href'] = $link;
        }
        $children = nj_menu_children($term);
        if ($children) {
            $item['children'] = $children;
        }
        return $item;
    }
}

if (!function_exists('nj_menu_links')) {
    function nj_menu_links($item) {
        $item['href'] = nj_section_link($item['label']);
        if (!empty($item['children'])) {
            foreach ($item['children'] as $i => $child) {
                $item['children'][$i]['href'] = nj_section_link($child['label']);
            }
        }
        return $item;
    }
}

if (!function_exists('nj_menu')) {
    function nj_menu() {
        $menu = nj_slots_data()['menu'];
        $menu['newest']['href'] = nj_shop_link('newest');
        foreach ($menu['departments'] as $i => $department) {
            $menu['departments'][$i] = nj_menu_links($department);
        }
        $menu['collections'] = nj_menu_links($menu['collections']);
        if (defined('NJ_PREVIEW')) {
            return $menu;
        }
        $index = nj_category_index();
        if (!$index) {
            return $menu;
        }
        foreach ($menu['departments'] as $i => $department) {
            $menu['departments'][$i] = nj_menu_apply($department, $index);
        }
        $menu['collections'] = nj_menu_apply($menu['collections'], $index);
        return $menu;
    }
}

if (!function_exists('nj_nav_items')) {
    function nj_nav_items() {
        $menu = nj_menu();
        $items = array();
        foreach ($menu['departments'] as $department) {
            $items[] = array(
                'label' => $department['label'],
                'href' => $department['href'],
            );
        }
        $items[] = array(
            'label' => $menu['collections']['label'],
            'href' => $menu['collections']['href'],
        );
        $items[] = $menu['newest'];
        return $items;
    }
}

if (!function_exists('nj_discount_percent')) {
    function nj_discount_percent($product) {
        if (!is_object($product) || !method_exists($product, 'is_on_sale') || !$product->is_on_sale()) {
            return '';
        }
        $regular = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ($product->is_type('variable')) {
            $regular = (float) $product->get_variation_regular_price('max');
            $sale = (float) $product->get_variation_sale_price('min');
        }
        if ($regular <= 0 || $sale <= 0 || $sale >= $regular) {
            return '';
        }
        return (string) (int) round((($regular - $sale) / $regular) * 100);
    }
}

if (!function_exists('nj_card_price_html')) {
    function nj_card_price_html($price, $regular) {
        $sale = esc_html($price);
        if ($regular === '') {
            return $sale;
        }
        return '<ins>' . $sale . '</ins><del>' . esc_html($regular) . '</del>';
    }
}

if (!function_exists('nj_money')) {
    function nj_money($value) {
        $digits = preg_replace('/[^\d]/', '', (string) $value);
        return $digits === '' ? 0 : (int) $digits;
    }
}

if (!function_exists('nj_plain_text')) {
    function nj_plain_text($html) {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'));
    }
}

if (!function_exists('nj_shop_ready')) {
    function nj_shop_ready() {
        return function_exists('WC') && is_object(WC());
    }
}

if (!function_exists('nj_menu_for_preview')) {
    function nj_menu_for_preview($menu) {
        foreach ($menu['departments'] as $i => $department) {
            $menu['departments'][$i] = nj_preview_item($department);
        }
        $menu['collections'] = nj_preview_item($menu['collections']);
        return $menu;
    }
}

if (!function_exists('nj_preview_item')) {
    function nj_preview_item($item) {
        $item['href'] = nj_preview_section_href($item['label'], $item['href']);
        if (!empty($item['children'])) {
            foreach ($item['children'] as $i => $child) {
                $item['children'][$i]['href'] = nj_preview_section_href($child['label'], $child['href']);
            }
        }
        return $item;
    }
}

if (!function_exists('nj_preview_section_href')) {
    function nj_preview_section_href($label, $href) {
        $key = nj_menu_key($label);
        $catalogs = nj_slots_data()['catalogs'];
        if (!isset($catalogs[$key])) {
            return $href;
        }
        return 'galeria.php?seccion=' . rawurlencode($key);
    }
}

if (!function_exists('nj_page_url')) {
    function nj_page_url($key) {
        $options = array(
            'preguntas' => 'nj_page_preguntas',
            'envios' => 'nj_page_envios',
            'cambios' => 'nj_page_cambios',
            'tallas' => 'nj_page_tallas',
            'terminos' => 'nj_page_terminos',
            'privacidad' => 'nj_page_privacidad',
        );
        if (!isset($options[$key]) || !function_exists('get_option') || !function_exists('get_permalink')) {
            return nj_home_url();
        }
        $page_id = (int) get_option($options[$key]);
        if (!$page_id) {
            return nj_home_url();
        }
        $url = get_permalink($page_id);
        return $url ? $url : nj_home_url();
    }
}

if (!function_exists('nj_bag_state')) {
    function nj_bag_state() {
        $state = array(
            'lines' => array(),
            'subtotal' => 0,
            'discount' => 0,
            'total' => 0,
            'count' => 0,
            'note' => '',
        );
        if (!function_exists('WC') || !WC() || !WC()->cart) {
            return $state;
        }
        foreach (WC()->cart->get_cart() as $key => $item) {
            $product = isset($item['data']) ? $item['data'] : null;
            if (!$product) {
                continue;
            }
            $name = $product->get_name();
            if ($product->is_type('variation')) {
                $parent = wc_get_product($product->get_parent_id());
                if ($parent) {
                    $name = $parent->get_name();
                }
            }
            $size = '';
            if (!empty($item['variation']) && is_array($item['variation'])) {
                foreach ($item['variation'] as $value) {
                    if ($value !== '') {
                        $size = (string) $value;
                        break;
                    }
                }
            }
            $image = '';
            $image_id = $product->get_image_id();
            if ($image_id && function_exists('wp_get_attachment_image_url')) {
                $found = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
                $image = $found ? $found : '';
            }
            $state['lines'][] = array(
                'key' => $key,
                'name' => $name,
                'image' => $image,
                'url' => get_permalink($item['product_id']),
                'size' => $size,
                'price' => (int) round((float) $product->get_price()),
                'regular' => (int) round((float) $product->get_regular_price()),
                'qty' => (int) $item['quantity'],
            );
        }
        $state['subtotal'] = (int) round((float) WC()->cart->get_subtotal());
        $state['discount'] = (int) round((float) WC()->cart->get_discount_total());
        $state['total'] = (int) round((float) WC()->cart->get_total('edit'));
        $state['count'] = (int) WC()->cart->get_cart_contents_count();
        $state['note'] = isset($GLOBALS['nj_bag_note']) ? (string) $GLOBALS['nj_bag_note'] : '';
        return $state;
    }
}

if (!function_exists('nj_bag_update')) {
    function nj_bag_update() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'nj-bag') || !function_exists('WC') || !WC() || !WC()->cart) {
            wp_send_json(array('error' => true));
        }
        $bag_action = isset($_POST['bag_action']) ? sanitize_key(wp_unslash($_POST['bag_action'])) : '';
        if ($bag_action === 'coupon') {
            $code = isset($_POST['coupon']) ? wc_format_coupon_code(wp_unslash($_POST['coupon'])) : '';
            if ($code !== '') {
                WC()->cart->add_discount($code);
            }
        } else {
            $key = isset($_POST['key']) ? wc_clean(wp_unslash($_POST['key'])) : '';
            $qty = isset($_POST['qty']) ? absint($_POST['qty']) : 0;
            if ($key !== '') {
                if ($qty < 1) {
                    WC()->cart->remove_cart_item($key);
                } else {
                    WC()->cart->set_quantity($key, $qty, true);
                }
            }
        }
        WC()->cart->calculate_totals();
        $texts = array();
        if (function_exists('wc_get_notices')) {
            foreach (array('error', 'success', 'notice') as $type) {
                foreach (wc_get_notices($type) as $notice) {
                    $texts[] = wp_strip_all_tags($notice['notice']);
                }
            }
            wc_clear_notices();
        }
        $GLOBALS['nj_bag_note'] = $texts ? $texts[0] : '';
        if (class_exists('WC_AJAX')) {
            WC_AJAX::get_refreshed_fragments();
        }
        wp_die();
    }
}
add_action('wc_ajax_nj_bag', 'nj_bag_update');
add_action('wc_ajax_nopriv_nj_bag', 'nj_bag_update');

if (!function_exists('nj_cop_payments')) {
    function nj_cop_payments() {
        if (get_option('nj_cod_off') === '1' || !function_exists('get_option')) {
            return;
        }
        $cod = get_option('woocommerce_cod_settings', array());
        if (!is_array($cod)) {
            $cod = array();
        }
        $cod['enabled'] = 'no';
        update_option('woocommerce_cod_settings', $cod);
        update_option('nj_cod_off', '1');
    }
}
add_action('init', 'nj_cop_payments', 30);

if (!function_exists('nj_hide_paypal_for_cop')) {
    function nj_hide_paypal_for_cop($gateways) {
        unset($gateways['cod']);
        if (!function_exists('get_woocommerce_currency') || get_woocommerce_currency() !== 'COP') {
            return $gateways;
        }
        foreach ($gateways as $id => $gateway) {
            if ($id === 'paypal' || strpos($id, 'ppcp') === 0) {
                unset($gateways[$id]);
            }
        }
        return $gateways;
    }
}
add_filter('woocommerce_available_payment_gateways', 'nj_hide_paypal_for_cop');

if (!function_exists('nj_order_line_screen')) {
    function nj_order_line_screen() {
        if (function_exists('is_order_received_page') && is_order_received_page()) {
            return true;
        }
        if (function_exists('is_checkout_pay_page') && is_checkout_pay_page()) {
            return true;
        }
        return function_exists('is_view_order_page') && is_view_order_page();
    }
}

if (!function_exists('nj_order_item_thumb')) {
    function nj_order_item_thumb($name, $item) {
        if (!nj_order_line_screen() || !is_object($item) || !method_exists($item, 'get_product')) {
            return $name;
        }
        $product = $item->get_product();
        if (!$product || !method_exists($product, 'get_image')) {
            return $name;
        }
        $image = $product->get_image('woocommerce_thumbnail', array('class' => 'nj-order-thumb'));
        if ($image === '') {
            return $name;
        }
        return '<span class="nj-order-line">' . $image . '<span class="nj-order-line__name">' . $name . '</span></span>';
    }
}
add_filter('woocommerce_order_item_name', 'nj_order_item_thumb', 10, 2);

if (!function_exists('nj_received_order')) {
    function nj_received_order() {
        if (!function_exists('is_order_received_page') || !is_order_received_page() || !function_exists('wc_get_order')) {
            return false;
        }
        $order_id = absint(get_query_var('order-received'));
        if (!$order_id) {
            return false;
        }
        $order = wc_get_order($order_id);
        return $order ? $order : false;
    }
}

if (!function_exists('nj_wompi_return_approved')) {
    function nj_wompi_return_approved($order) {
        if (empty($_GET['id']) || !function_exists('wp_remote_get')) {
            return false;
        }
        $id = sanitize_text_field(wp_unslash($_GET['id']));
        if (!preg_match('/^[0-9A-Za-z_-]{8,80}$/', $id)) {
            return false;
        }
        $cache_key = 'nj_wompi_tx_' . md5($id);
        $cached = get_transient($cache_key);
        if ($cached === 'yes' || $cached === 'no') {
            return $cached === 'yes';
        }
        $settings = get_option('woocommerce_wompi_settings', array());
        $test = is_array($settings) && isset($settings['testmode']) && $settings['testmode'] === 'yes';
        $url = ($test ? 'https://sandbox.wompi.co/v1/transactions/' : 'https://production.wompi.co/v1/transactions/') . rawurlencode($id);
        $response = wp_remote_get($url, array('timeout' => 8));
        $approved = false;
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $data = is_array($body) && isset($body['data']) && is_array($body['data']) ? $body['data'] : array();
            $status = isset($data['status']) ? (string) $data['status'] : '';
            $reference = isset($data['reference']) ? (string) $data['reference'] : '';
            $approved = $status === 'APPROVED' && ($reference === (string) $order->get_id() || $reference === (string) $order->get_order_number());
        }
        set_transient($cache_key, $approved ? 'yes' : 'no', 10 * MINUTE_IN_SECONDS);
        return $approved;
    }
}

if (!function_exists('nj_order_is_dispatch')) {
    function nj_order_is_dispatch($order) {
        static $cache = array();
        if (!$order || !is_object($order) || !method_exists($order, 'get_id')) {
            return false;
        }
        $id = (int) $order->get_id();
        if (array_key_exists($id, $cache)) {
            return $cache[$id];
        }
        if ($order->has_status(array('cancelled', 'failed', 'refunded', 'checkout-draft'))) {
            $cache[$id] = false;
            return false;
        }
        $paid = (method_exists($order, 'is_paid') && $order->is_paid()) || $order->has_status(array('processing', 'completed', 'empacando'));
        if (!$paid && method_exists($order, 'needs_payment') && $order->needs_payment()) {
            $paid = nj_wompi_return_approved($order);
        }
        $cache[$id] = $paid;
        return $paid;
    }
}

if (!function_exists('nj_despacho_body_class')) {
    function nj_despacho_body_class($classes) {
        $order = nj_received_order();
        if ($order && nj_order_is_dispatch($order)) {
            $classes[] = 'nj-despacho';
        }
        return $classes;
    }
}
add_filter('body_class', 'nj_despacho_body_class');

if (!function_exists('nj_despacho_thankyou_text')) {
    function nj_despacho_thankyou_text($text, $order = false) {
        if ($order && nj_order_is_dispatch($order)) {
            return 'Tu pedido ' . $order->get_order_number() . ' ya está en despacho.';
        }
        return $text;
    }
}
add_filter('woocommerce_thankyou_order_received_text', 'nj_despacho_thankyou_text', 20, 2);

if (!function_exists('nj_hide_paid_invoice')) {
    function nj_hide_paid_invoice($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if ($order && nj_order_is_dispatch($order)) {
            remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
            $method = $order->get_payment_method();
            if ($method && function_exists('WC') && WC()->payment_gateways()) {
                $gateways = WC()->payment_gateways()->payment_gateways();
                if (isset($gateways[$method]) && is_object($gateways[$method])) {
                    remove_action('woocommerce_thankyou_' . $method, array($gateways[$method], 'thankyou_page'));
                }
            }
        }
    }
}
add_action('woocommerce_before_thankyou', 'nj_hide_paid_invoice', 1);

if (!function_exists('nj_order_received_note')) {
    function nj_order_received_note($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        if (nj_order_is_dispatch($order)) {
            echo '<p class="nj-order-dispatch">El pago quedó confirmado. Te avisamos por el correo cuando lo estemos empacando y cuando salga el envío.</p>';
            echo '<a class="nj-order-dispatch__home" href="' . esc_url(nj_shop_link()) . '">Volver a la tienda</a>';
            return;
        }
        if (!method_exists($order, 'needs_payment') || !$order->needs_payment()) {
            return;
        }
        echo '<p class="nj-order-pending">El pedido quedó registrado. Pulsa Pagar para completar el cobro en Wompi.</p>';
    }
}
add_action('woocommerce_thankyou', 'nj_order_received_note', 5);

if (!function_exists('nj_pay_order_button_text')) {
    function nj_pay_order_button_text() {
        return 'Pagar con Wompi';
    }
}
add_filter('woocommerce_pay_order_button_text', 'nj_pay_order_button_text');

if (!function_exists('nj_wompi_description')) {
    function nj_wompi_description($description, $gateway_id) {
        if ($gateway_id !== 'wompi') {
            return $description;
        }
        $plain = trim(wp_strip_all_tags((string) $description));
        if ($plain === '' || $plain === 'Pay via Wompi gateway.' || $plain === 'Pay via Wompi gateway') {
            return 'Pagas en la ventana de Wompi con Nequi, tarjeta o PSE.';
        }
        return $description;
    }
}
add_filter('woocommerce_gateway_description', 'nj_wompi_description', 10, 2);

if (!function_exists('nj_checkout_terms_text')) {
    function nj_checkout_terms_text($text) {
        if (strpos((string) $text, 'I have read and agree') !== false) {
            return 'He leído y acepto los [terms] de la tienda';
        }
        return $text;
    }
}
add_filter('woocommerce_get_terms_and_conditions_checkbox_text', 'nj_checkout_terms_text');

if (!function_exists('nj_checkout_privacy_text')) {
    function nj_checkout_privacy_text($text) {
        if (strpos((string) $text, 'Your personal data will be used') !== false) {
            return 'Tus datos se usan para procesar el pedido, como se explica en la [privacy_policy].';
        }
        return $text;
    }
}
add_filter('woocommerce_get_privacy_policy_text', 'nj_checkout_privacy_text');
