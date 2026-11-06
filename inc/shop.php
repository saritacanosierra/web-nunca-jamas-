<?php
/**
 * Datos de la tienda. Con WooCommerce salen de los productos;
 * sin WordPress, de inc/slots.php para la vista previa.
 */

if (!defined('ABSPATH')) {
    require_once __DIR__ . '/wp-stubs.php';
}

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

