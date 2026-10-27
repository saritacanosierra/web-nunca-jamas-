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
            if ($which === 'sale') {
                return add_query_arg('on_sale', '1', $shop);
            }
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
        $menu['sale']['href'] = nj_shop_link('sale');
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
        $items[] = $menu['sale'];
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
