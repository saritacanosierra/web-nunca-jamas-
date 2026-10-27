<?php
/**
 * Datos de la portada: hero, pestañas de prendas y estilos.
 */

require_once __DIR__ . '/cards.php';

if (!function_exists('nj_hero_link')) {
    function nj_hero_link($index) {
        if ($index === 0) {
            return nj_shop_link('sale');
        }
        if ($index === 2) {
            return nj_shop_link('newest');
        }
        return nj_shop_link('shop');
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

if (!function_exists('nj_home_tabs')) {
    function nj_home_tabs() {
        if (!nj_shop_ready()) {
            $cards = array_map('nj_card_from_slot', nj_slots_data()['products']);
            $url = nj_home_url() . '#prendas';
            return array(
                array('id' => 'nuevo', 'label' => 'Lo nuevo', 'items' => $cards, 'url' => $url),
                array('id' => 'coleccion', 'label' => 'Colección', 'items' => $cards, 'url' => $url),
                array('id' => 'sale', 'label' => 'Sale', 'items' => $cards, 'url' => $url),
            );
        }
        $newest = nj_home_wc_products(array(
            'status' => 'publish',
            'limit' => 8,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        $sale_ids = function_exists('wc_get_product_ids_on_sale') ? wc_get_product_ids_on_sale() : array();
        $sale = $sale_ids ? nj_home_wc_products(array(
            'status' => 'publish',
            'limit' => 8,
            'include' => $sale_ids,
        )) : array();
        $collection = nj_home_wc_products(array(
            'status' => 'publish',
            'limit' => 8,
            'category' => array('colecciones'),
        ));
        if (!$collection) {
            $collection = $newest;
        }
        return array(
            nj_tab('nuevo', 'Lo nuevo', $newest, nj_shop_link('newest'), true),
            nj_tab('coleccion', 'Colección', $collection, nj_shop_link('shop'), true),
            nj_tab('sale', 'Sale', $sale, nj_shop_link('sale'), true),
        );
    }
}

if (!function_exists('nj_style_from_terms')) {
    function nj_style_from_terms() {
        if (!function_exists('get_terms')) {
            return array();
        }
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'number' => 8,
        ));
        if (is_wp_error($terms) || !$terms) {
            return array();
        }
        $fallback = nj_slots_data()['styles'];
        $items = array();
        foreach ($terms as $index => $term) {
            if (in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                continue;
            }
            $items[] = nj_style_from_term($term, $fallback, $index);
        }
        return $items;
    }
}

if (!function_exists('nj_style_from_term')) {
    function nj_style_from_term($term, $fallback, $index) {
        $thumb_id = get_term_meta($term->term_id, 'thumbnail_id', true);
        $image = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'woocommerce_thumbnail') : '';
        if (!$image && isset($fallback[$index])) {
            $image = nj_img($fallback[$index]['src']);
        }
        if (!$image && isset($fallback[0])) {
            $image = nj_img($fallback[0]['src']);
        }
        $link = get_term_link($term);
        return array(
            'label' => $term->name,
            'href' => is_wp_error($link) ? nj_shop_link('shop') : $link,
            'image' => $image,
        );
    }
}

if (!function_exists('nj_style_fallback')) {
    function nj_style_fallback() {
        $items = array();
        foreach (nj_slots_data()['styles'] as $style) {
            $items[] = array(
                'label' => $style['label'],
                'href' => nj_shop_link('shop'),
                'image' => nj_img($style['src']),
            );
        }
        return $items;
    }
}

if (!function_exists('nj_cuentos_url')) {
    function nj_cuentos_url() {
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
        $items = array();
        foreach (nj_slots_data()['cuentos'] as $cuento) {
            $cuento['image'] = nj_img($cuento['src']);
            $items[] = $cuento;
        }
        return $items;
    }
}

if (!function_exists('nj_style_items')) {
    function nj_style_items() {
        $items = array();
        foreach (nj_slots_data()['styles'] as $style) {
            $items[] = array(
                'label' => $style['label'],
                'href' => nj_section_link($style['label']),
                'image' => nj_img($style['src']),
            );
        }
        return $items;
    }
}
