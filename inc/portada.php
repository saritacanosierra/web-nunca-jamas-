<?php
/**
 * Datos de la portada: hero, pestañas de prendas y estilos.
 */

require_once __DIR__ . '/cards.php';

if (!function_exists('nj_portada_espacios')) {
    function nj_portada_espacios() {
        if (!function_exists('nj_espacios_promo')) {
            require_once __DIR__ . '/espacios.php';
        }
    }
}

if (!function_exists('nj_tab')) {
    function nj_tab($id, $label, $products, $url, $from_wc) {
        $items = array();
        foreach ($products as $product) {
            $items[] = $from_wc ? nj_card_from_product($product) : $product;
        }
        return array(
            'id' => $id,
            'label' => $label,
            'items' => $items,
            'url' => $url,
        );
    }
}

if (!function_exists('nj_home_wc_products')) {
    function nj_home_wc_products($args) {
        if (!function_exists('wc_get_products')) {
            return array();
        }
        return wc_get_products($args);
    }
}

if (!function_exists('nj_home_tab_copy')) {
    function nj_home_tab_copy() {
        nj_portada_espacios();
        $texts = function_exists('nj_espacios_textos') ? nj_espacios_textos() : array();
        return array(
            'nuevo' => isset($texts['tab_nuevo']) && $texts['tab_nuevo'] !== '' ? $texts['tab_nuevo'] : 'Lo nuevo',
            'coleccion' => isset($texts['tab_coleccion']) && $texts['tab_coleccion'] !== '' ? $texts['tab_coleccion'] : 'Colección',
            'mas_nuevo' => isset($texts['mas_nuevo']) && $texts['mas_nuevo'] !== '' ? $texts['mas_nuevo'] : 'Ver lo nuevo',
            'mas_coleccion' => isset($texts['mas_coleccion']) && $texts['mas_coleccion'] !== '' ? $texts['mas_coleccion'] : 'Ver la colección',
        );
    }
}

if (!function_exists('nj_home_coleccion_hijos')) {
    function nj_home_coleccion_hijos() {
        $parent = function_exists('nj_colecciones_padre') ? nj_colecciones_padre() : null;
        if (!$parent || !isset($parent->term_id) || !function_exists('get_terms')) {
            return array();
        }
        $children = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'parent' => (int) $parent->term_id,
        ));
        if (is_wp_error($children) || !is_array($children)) {
            return array();
        }
        $items = array();
        foreach ($children as $child) {
            $link = function_exists('get_term_link') ? get_term_link($child) : '';
            if (is_wp_error($link) || !$link) {
                $link = '';
            }
            $items[] = array(
                'id' => (int) $child->term_id,
                'href' => $link,
                'tiempo' => function_exists('nj_coleccion_tiempo') ? nj_coleccion_tiempo($child->term_id) : (int) $child->term_id,
            );
        }
        usort($items, function ($a, $b) {
            if ($a['tiempo'] === $b['tiempo']) {
                return $b['id'] <=> $a['id'];
            }
            return $b['tiempo'] <=> $a['tiempo'];
        });
        return $items;
    }
}

if (!function_exists('nj_home_coleccion_productos_de')) {
    function nj_home_coleccion_productos_de($term_id) {
        if (!function_exists('get_posts') || !function_exists('wc_get_product')) {
            return array();
        }
        $ids = get_posts(array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 8,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => (int) $term_id,
                'include_children' => false,
            )),
        ));
        $products = array();
        foreach ((array) $ids as $id) {
            $product = wc_get_product((int) $id);
            if ($product) {
                $products[] = $product;
            }
        }
        return $products;
    }
}

if (!function_exists('nj_home_coleccion_vista')) {
    function nj_home_coleccion_vista() {
        $url = function_exists('nj_section_link') ? nj_section_link('Colecciones') : '';
        $hijos = nj_home_coleccion_hijos();
        $ids = array();
        foreach ($hijos as $hijo) {
            $ids[] = $hijo['id'];
        }
        $picked = function_exists('nj_prendas_for_home') ? nj_prendas_for_home('coleccion') : array();
        $chosen = array();
        foreach ($picked as $product) {
            if (!is_object($product) || !method_exists($product, 'get_category_ids')) {
                continue;
            }
            foreach ($product->get_category_ids() as $category_id) {
                if (in_array((int) $category_id, $ids, true)) {
                    $chosen[] = $product;
                    break;
                }
            }
        }
        if ($chosen) {
            return array('products' => array_slice($chosen, 0, 8), 'url' => $url);
        }
        foreach ($hijos as $hijo) {
            $products = nj_home_coleccion_productos_de($hijo['id']);
            if ($products) {
                return array(
                    'products' => $products,
                    'url' => $hijo['href'] !== '' ? $hijo['href'] : $url,
                );
            }
        }
        return array('products' => array(), 'url' => $url);
    }
}

if (!function_exists('nj_home_tabs')) {
    function nj_home_tabs() {
        $copy = nj_home_tab_copy();
        if (!nj_shop_ready()) {
            $new_cards = array();
            $col_cards = array();
            $departments = array('linea', 'cuentos', 'accesorios', 'ninas', 'bebes', 'blusas');
            foreach (nj_slots_data()['products'] as $product) {
                $card = nj_card_from_slot($product);
                $kicker = isset($product['kicker']) && function_exists('nj_menu_key') ? nj_menu_key($product['kicker']) : '';
                if (in_array($kicker, $departments, true)) {
                    $new_cards[] = $card;
                } else {
                    $col_cards[] = $card;
                }
            }
            $url = nj_home_url() . '#prendas';
            $collection = defined('NJ_PREVIEW') ? 'galeria.php?seccion=colecciones' : nj_section_link('Colecciones');
            return array(
                array('id' => 'nuevo', 'label' => $copy['nuevo'], 'more' => $copy['mas_nuevo'], 'items' => array_slice($new_cards, 0, 8), 'url' => $url),
                array('id' => 'coleccion', 'label' => $copy['coleccion'], 'more' => $copy['mas_coleccion'], 'items' => array_slice($col_cards, 0, 8), 'url' => $collection),
            );
        }
        $newest = nj_home_wc_products(array(
            'status' => 'publish',
            'limit' => 8,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        if (function_exists('nj_prendas_for_home')) {
            $picked_new = nj_prendas_for_home('nuevo');
            if ($picked_new) {
                $newest = $picked_new;
            }
        }
        $coleccion = nj_home_coleccion_vista();
        $tabs = array(
            nj_tab('nuevo', $copy['nuevo'], $newest, nj_shop_link('newest'), true),
            nj_tab('coleccion', $copy['coleccion'], $coleccion['products'], $coleccion['url'], true),
        );
        $tabs[0]['more'] = $copy['mas_nuevo'];
        $tabs[1]['more'] = $copy['mas_coleccion'];
        return $tabs;
    }
}

if (!function_exists('nj_cuentos_url')) {
    function nj_cuentos_url() {
        if (function_exists('nj_blog_url') && !defined('NJ_PREVIEW')) {
            return nj_blog_url();
        }
        if (defined('NJ_PREVIEW')) {
            return 'galeria.php?seccion=cuentos';
        }
        $menu = nj_menu();
        foreach ($menu['departments'] as $department) {
            if (nj_menu_key($department['label']) === 'cuentos') {
                return $department['href'];
            }
        }
        return nj_shop_link('shop');
    }
}

if (!function_exists('nj_home_cuentos')) {
    function nj_home_cuentos() {
        nj_portada_espacios();
        return nj_espacios_cuentos();
    }
}

if (!function_exists('nj_style_items')) {
    function nj_style_items() {
        nj_portada_espacios();
        return nj_espacios_styles();
    }
}

if (!function_exists('nj_portada_vista')) {
    function nj_portada_vista() {
        nj_portada_espacios();
        return array(
            'promos' => nj_espacios_promos(),
            'looks' => nj_espacios_looks(),
            'tabs' => nj_home_tabs(),
            'styles' => nj_style_items(),
            'textos' => nj_espacios_textos(),
        );
    }
}
