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

if (!function_exists('nj_home_tabs')) {
    function nj_home_tabs() {
        if (!nj_shop_ready()) {
            $cards = array_map('nj_card_from_slot', nj_slots_data()['products']);
            $url = nj_home_url() . '#prendas';
            return array(
                array('id' => 'nuevo', 'label' => 'Lo nuevo', 'items' => $cards, 'url' => $url),
                array('id' => 'coleccion', 'label' => 'Colección', 'items' => $cards, 'url' => $url),
            );
        }
        $newest = nj_home_wc_products(array(
            'status' => 'publish',
            'limit' => 8,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        $collection = nj_home_wc_products(array(
            'status' => 'publish',
            'limit' => 8,
            'category' => array('colecciones'),
        ));
        if (function_exists('nj_prendas_for_home')) {
            $picked_new = nj_prendas_for_home('nuevo');
            $picked_col = nj_prendas_for_home('coleccion');
            if ($picked_new) {
                $newest = $picked_new;
            }
            if ($picked_col) {
                $collection = $picked_col;
            }
        }
        if (!$collection) {
            $collection = $newest;
        }
        return array(
            nj_tab('nuevo', 'Lo nuevo', $newest, nj_shop_link('newest'), true),
            nj_tab('coleccion', 'Colección', $collection, nj_shop_link('shop'), true),
        );
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
        );
    }
}
