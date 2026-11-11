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

if (!function_exists('nj_colecciones_padre')) {
    function nj_colecciones_padre() {
        if (!function_exists('nj_category_index')) {
            return null;
        }
        $index = nj_category_index();
        return isset($index['colecciones']) ? $index['colecciones'] : null;
    }
}

if (!function_exists('nj_coleccion_tiempo')) {
    function nj_coleccion_tiempo($term_id) {
        $term_id = (int) $term_id;
        if (!function_exists('get_posts')) {
            return $term_id;
        }
        $posts = get_posts(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $term_id,
                'include_children' => false,
            )),
        ));
        if ($posts) {
            $date = get_post_field('post_date', $posts[0]);
            $time = strtotime((string) $date);
            if ($time) {
                return $time;
            }
        }
        return $term_id;
    }
}

if (!function_exists('nj_coleccion_foto')) {
    function nj_coleccion_foto($term_id) {
        $term_id = (int) $term_id;
        $thumb = function_exists('get_term_meta') ? (int) get_term_meta($term_id, 'thumbnail_id', true) : 0;
        if ($thumb > 0 && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($thumb, 'large');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        if (!function_exists('get_posts')) {
            return '';
        }
        $posts = get_posts(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $term_id,
                'include_children' => false,
            )),
        ));
        if ($posts && function_exists('get_the_post_thumbnail_url')) {
            $url = get_the_post_thumbnail_url($posts[0], 'large');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        return '';
    }
}

if (!function_exists('nj_colecciones_puntos')) {
    function nj_colecciones_puntos($items) {
        if (!$items) {
            return array();
        }
        $items[0]['reciente'] = true;
        foreach ($items as $index => $item) {
            if ($index === 0) {
                $items[$index]['spot'] = 'recent';
            } elseif ($index === 1) {
                $items[$index]['spot'] = 'side';
            } elseif ($index < 4) {
                $items[$index]['spot'] = 'mid';
            } else {
                $items[$index]['spot'] = 'more';
            }
        }
        return $items;
    }
}

if (!function_exists('nj_colecciones_preview')) {
    function nj_colecciones_preview() {
        $slots = nj_slots_data();
        $catalog = isset($slots['catalogs']['colecciones']) ? $slots['catalogs']['colecciones'] : array();
        $pills = isset($catalog['pills']) ? $catalog['pills'] : array();
        $peso = array('especiales' => 3, 'cuentos' => 2, 'basicos' => 1);
        $groups = array();
        foreach ($pills as $pill) {
            $groups[$pill['id']] = array(
                'id' => $pill['id'],
                'label' => $pill['label'],
                'href' => 'galeria.php?seccion=colecciones&tipo=' . rawurlencode($pill['id']),
                'image' => '',
                'lead' => '',
                'reciente' => false,
                'todas' => 'galeria.php?seccion=colecciones',
                'peso' => isset($peso[$pill['id']]) ? $peso[$pill['id']] : 0,
            );
        }
        foreach ($slots['catalog_products'] as $product) {
            if (!isset($product['section']) || $product['section'] !== 'colecciones') {
                continue;
            }
            $type = isset($product['type']) ? $product['type'] : '';
            if (!isset($groups[$type]) || $groups[$type]['image'] !== '') {
                continue;
            }
            $groups[$type]['image'] = function_exists('nj_img') ? nj_img($product['src']) : $product['src'];
        }
        $items = array_values($groups);
        usort($items, function ($a, $b) {
            return $b['peso'] <=> $a['peso'];
        });
        return nj_colecciones_puntos($items);
    }
}

if (!function_exists('nj_colecciones_lista')) {
    function nj_colecciones_lista() {
        if (defined('NJ_PREVIEW') || !function_exists('get_terms')) {
            return nj_colecciones_preview();
        }
        $parent = nj_colecciones_padre();
        if (!$parent || !isset($parent->term_id)) {
            return array();
        }
        $parent_link = get_term_link($parent);
        if (is_wp_error($parent_link) || !$parent_link) {
            $parent_link = '';
        }
        $children = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => (int) $parent->term_id,
        ));
        if (is_wp_error($children) || !$children) {
            return nj_colecciones_desde_menu($parent);
        }
        $items = array();
        foreach ($children as $child) {
            $link = get_term_link($child);
            if (is_wp_error($link) || !$link) {
                continue;
            }
            $lead = function_exists('term_description') ? term_description($child->term_id, 'product_cat') : '';
            $lead = trim(wp_strip_all_tags((string) $lead));
            $items[] = array(
                'id' => (int) $child->term_id,
                'label' => $child->name,
                'href' => $link,
                'image' => nj_coleccion_foto($child->term_id),
                'lead' => $lead,
                'reciente' => false,
                'todas' => $parent_link,
                'tiempo' => nj_coleccion_tiempo($child->term_id),
            );
        }
        usort($items, function ($a, $b) {
            if ($a['tiempo'] === $b['tiempo']) {
                return $b['id'] <=> $a['id'];
            }
            return $b['tiempo'] <=> $a['tiempo'];
        });
        return nj_colecciones_puntos($items);
    }
}

if (!function_exists('nj_coleccion_term_de_enlace')) {
    function nj_coleccion_term_de_enlace($href, $label) {
        $slug = '';
        $path = function_exists('wp_parse_url') ? wp_parse_url((string) $href, PHP_URL_PATH) : parse_url((string) $href, PHP_URL_PATH);
        if (is_string($path) && $path !== '') {
            $slug = basename(trim($path, '/'));
        }
        if ($slug !== '' && $slug !== 'colecciones' && function_exists('get_term_by')) {
            $term = get_term_by('slug', $slug, 'product_cat');
            if ($term && !is_wp_error($term)) {
                return $term;
            }
        }
        $label = trim((string) $label);
        if ($label !== '' && function_exists('get_term_by')) {
            $term = get_term_by('name', $label, 'product_cat');
            if ($term && !is_wp_error($term)) {
                return $term;
            }
        }
        return null;
    }
}

if (!function_exists('nj_colecciones_desde_menu')) {
    function nj_colecciones_desde_menu($parent) {
        $children = array();
        if (!defined('NJ_PREVIEW') && function_exists('nj_tema_menu_live')) {
            $saved = nj_tema_menu_live();
            if (is_array($saved) && !empty($saved['collections']['children']) && is_array($saved['collections']['children'])) {
                $children = $saved['collections']['children'];
            }
        }
        if (!$children && function_exists('nj_slots_data')) {
            $menu = nj_slots_data()['menu'];
            if (!empty($menu['collections']['children']) && is_array($menu['collections']['children'])) {
                $children = $menu['collections']['children'];
            }
        }
        $parent_id = ($parent && isset($parent->term_id)) ? (int) $parent->term_id : 0;
        $parent_link = '';
        if ($parent_id && function_exists('get_term_link')) {
            $link = get_term_link($parent);
            if (!is_wp_error($link) && $link) {
                $parent_link = $link;
            }
        }
        $items = array();
        $seen = array();
        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }
            $term = nj_coleccion_term_de_enlace(isset($child['href']) ? $child['href'] : '', isset($child['label']) ? $child['label'] : '');
            if (!$term || (int) $term->term_id === $parent_id || isset($seen[(int) $term->term_id])) {
                continue;
            }
            $seen[(int) $term->term_id] = true;
            $link = function_exists('get_term_link') ? get_term_link($term) : '';
            if (is_wp_error($link) || !$link) {
                $link = isset($child['href']) ? $child['href'] : '';
            }
            $items[] = array(
                'id' => (int) $term->term_id,
                'label' => $term->name,
                'href' => $link,
                'image' => nj_coleccion_foto($term->term_id),
                'lead' => '',
                'reciente' => false,
                'todas' => $parent_link,
                'tiempo' => nj_coleccion_tiempo($term->term_id),
            );
        }
        usort($items, function ($a, $b) {
            if ($a['tiempo'] === $b['tiempo']) {
                return $b['id'] <=> $a['id'];
            }
            return $b['tiempo'] <=> $a['tiempo'];
        });
        return nj_colecciones_puntos($items);
    }
}

if (!function_exists('nj_colecciones_menu')) {
    function nj_colecciones_menu($menu) {
        if (!is_array($menu) || empty($menu['collections']) || !is_array($menu['collections'])) {
            return $menu;
        }
        $lista = nj_colecciones_lista();
        if (!$lista) {
            return $menu;
        }
        $children = array();
        foreach ($lista as $item) {
            $children[] = array(
                'label' => $item['label'],
                'href' => $item['href'],
                'reciente' => !empty($item['reciente']),
                'image' => isset($item['image']) ? $item['image'] : '',
                'spot' => isset($item['spot']) ? $item['spot'] : 'more',
            );
        }
        $menu['collections']['mosaico'] = true;
        $menu['collections']['children'] = $children;
        if (!empty($lista[0]['todas'])) {
            $menu['collections']['href'] = $lista[0]['todas'];
        }
        return $menu;
    }
}

if (!function_exists('nj_menu_vistas')) {
    function nj_menu_vistas() {
        return array(
            'bebes' => array('Pijamas', 'Conjuntos', 'Tutus', 'Personalizados', 'Accesorios', 'Baby showers'),
            'ninas' => array('Pijamas', 'Vestidos', 'Blusas', 'Faldas', 'Pantalones', 'Shorts', 'Accesorios', 'Vestidos de baño', 'Escolar'),
        );
    }
}

if (!function_exists('nj_term_por_nombre')) {
    function nj_term_por_nombre($label) {
        if (!function_exists('get_terms')) {
            return null;
        }
        $key = nj_menu_key($label);
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));
        if (is_wp_error($terms) || !$terms) {
            return null;
        }
        foreach ($terms as $term) {
            if (nj_menu_key($term->name) === $key || nj_menu_key($term->slug) === $key) {
                return $term;
            }
        }
        return null;
    }
}

if (!function_exists('nj_vista_enlace')) {
    function nj_vista_enlace($label, $padre) {
        $term = nj_term_por_nombre($label);
        if ($term && function_exists('get_term_link')) {
            $link = get_term_link($term);
            if (!is_wp_error($link) && $link) {
                return $link;
            }
        }
        $base = nj_section_link($padre);
        $sep = strpos($base, '?') === false ? '?' : '&';
        return $base . $sep . 'nj=' . rawurlencode($label);
    }
}

if (!function_exists('nj_menu_aplicar_vistas')) {
    function nj_menu_aplicar_vistas($menu) {
        if (empty($menu['departments']) || !is_array($menu['departments'])) {
            return $menu;
        }
        $vistas = nj_menu_vistas();
        foreach ($menu['departments'] as $i => $department) {
            $key = nj_menu_key(isset($department['label']) ? $department['label'] : '');
            if (!isset($vistas[$key])) {
                continue;
            }
            $children = array();
            foreach ($vistas[$key] as $label) {
                $children[] = array(
                    'label' => $label,
                    'href' => nj_vista_enlace($label, $department['label']),
                );
            }
            $menu['departments'][$i]['children'] = $children;
        }
        return $menu;
    }
}

if (!function_exists('nj_menu')) {
    function nj_menu() {
        if (!defined('NJ_PREVIEW') && function_exists('nj_tema_menu_live')) {
            $saved = nj_tema_menu_live();
            if (is_array($saved)) {
                return nj_colecciones_menu(nj_menu_aplicar_vistas($saved));
            }
        }
        $menu = nj_slots_data()['menu'];
        $menu['newest']['href'] = nj_shop_link('newest');
        foreach ($menu['departments'] as $i => $department) {
            $menu['departments'][$i] = nj_menu_links($department);
        }
        $menu['collections'] = nj_menu_links($menu['collections']);
        if (defined('NJ_PREVIEW')) {
            return nj_colecciones_menu(nj_menu_aplicar_vistas($menu));
        }
        $index = nj_category_index();
        if (!$index) {
            return nj_colecciones_menu(nj_menu_aplicar_vistas($menu));
        }
        foreach ($menu['departments'] as $i => $department) {
            $menu['departments'][$i] = nj_menu_apply($department, $index);
        }
        $menu['collections'] = nj_menu_apply($menu['collections'], $index);
        return nj_colecciones_menu(nj_menu_aplicar_vistas($menu));
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

