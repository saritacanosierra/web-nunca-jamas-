<?php
/**
 * Datos de la galería: vista previa y categorías de WooCommerce.
 */

require_once __DIR__ . '/cards.php';

if (!function_exists('nj_catalog_orders')) {
    function nj_catalog_orders() {
        return array(
            'date' => 'Fecha de lanzamiento',
            'popularity' => 'Popularidad',
            'price' => 'Precio: menor a mayor',
            'price-desc' => 'Precio: mayor a menor',
        );
    }
}

if (!function_exists('nj_catalog_order')) {
    function nj_catalog_order($requested) {
        $orders = nj_catalog_orders();
        if (!isset($orders[$requested])) {
            return 'date';
        }
        return $requested;
    }
}

if (!function_exists('nj_catalog_section')) {
    function nj_catalog_section($section) {
        $catalogs = nj_slots_data()['catalogs'];
        if (!isset($catalogs[$section])) {
            $section = 'linea';
        }
        $catalog = $catalogs[$section];
        $source = !empty($catalog['fallback']) ? $catalog['fallback'] : $section;
        if (empty($catalog['pills']) && isset($catalogs[$source]['pills'])) {
            $catalog['pills'] = $catalogs[$source]['pills'];
        }
        return array($section, $source, $catalog);
    }
}

if (!function_exists('nj_catalog_preview_product')) {
    function nj_catalog_preview_product($product, $section, $catalog) {
        if (!isset($product['alt'])) {
            $product['alt'] = $product['name'];
        }
        if (!isset($product['href'])) {
            $product['href'] = 'galeria.php?seccion=' . rawurlencode($section);
        }
        if (!isset($product['kicker'])) {
            $product['kicker'] = $catalog['group'];
        }
        if (!isset($product['sizes'])) {
            $product['sizes'] = array('2T', '3T', '4T', '5T', '6');
        }
        return $product;
    }
}

if (!function_exists('nj_catalog_preview_items')) {
    function nj_catalog_preview_items($slots, $section, $source, $catalog, $type) {
        $items = array();
        foreach ($slots['catalog_products'] as $product) {
            if ($product['section'] !== $source) {
                continue;
            }
            if ($type !== '' && $product['type'] !== $type) {
                continue;
            }
            $ready = nj_catalog_preview_product($product, $section, $catalog);
            $items[] = nj_card_from_slot($ready);
        }
        return $items;
    }
}

if (!function_exists('nj_catalog_sort')) {
    function nj_catalog_sort($items, $order) {
        if ($order === 'price') {
            usort($items, function ($a, $b) {
                return $a['price'] <=> $b['price'];
            });
            return $items;
        }
        if ($order === 'price-desc') {
            usort($items, function ($a, $b) {
                return $b['price'] <=> $a['price'];
            });
            return $items;
        }
        if ($order === 'popularity') {
            usort($items, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }
        return $items;
    }
}

if (!function_exists('nj_catalog_preview_pills')) {
    function nj_catalog_preview_pills($catalog, $section, $type) {
        $pills = array();
        $active_label = '';
        foreach ($catalog['pills'] as $pill) {
            if ($type === $pill['id']) {
                $active_label = $pill['label'];
            }
            $pills[] = array(
                'label' => $pill['label'],
                'href' => 'galeria.php?seccion=' . rawurlencode($section) . '&tipo=' . rawurlencode($pill['id']),
                'active' => $type === $pill['id'],
            );
        }
        return array($pills, $active_label);
    }
}

if (!function_exists('nj_catalog_preview')) {
    function nj_catalog_preview($section, $type, $order) {
        $slots = nj_slots_data();
        list($section, $source, $catalog) = nj_catalog_section($section);
        $order = nj_catalog_order($order);
        $items = nj_catalog_sort(nj_catalog_preview_items($slots, $section, $source, $catalog, $type), $order);
        list($pills, $active_label) = nj_catalog_preview_pills($catalog, $section, $type);
        $section_href = 'galeria.php?seccion=' . rawurlencode($section);
        $crumbs = array(
            array('label' => 'Inicio', 'href' => nj_home_url()),
            array('label' => $catalog['group'], 'href' => $type === '' ? '' : $section_href),
        );
        if ($active_label !== '') {
            $crumbs[] = array('label' => $active_label, 'href' => '');
        }
        $title = $active_label !== '' ? $active_label : $catalog['title'];
        return array(
            'title' => $title,
            'lead' => $catalog['lead'],
            'crumbs' => $crumbs,
            'pills' => $pills,
            'items' => $items,
            'banner' => $slots['gift_banner'],
            'order' => $order,
            'orders' => nj_catalog_orders(),
            'order_name' => 'orden',
            'action' => 'galeria.php',
            'hidden' => array(
                'seccion' => $section,
                'tipo' => $type,
            ),
        );
    }
}

if (!function_exists('nj_category_term')) {
    function nj_category_term($term_id) {
        if (!function_exists('get_term')) {
            return null;
        }
        return call_user_func('get_term', $term_id, 'product_cat');
    }
}

if (!function_exists('nj_catalog_ancestor_crumbs')) {
    function nj_catalog_ancestor_crumbs($term) {
        $crumbs = array();
        $ancestors = array_reverse(get_ancestors($term->term_id, 'product_cat'));
        foreach ($ancestors as $ancestor_id) {
            $ancestor = nj_category_term($ancestor_id);
            if (!$ancestor || is_wp_error($ancestor)) {
                continue;
            }
            $link = get_term_link($ancestor);
            $crumbs[] = array(
                'label' => $ancestor->name,
                'href' => is_wp_error($link) ? '' : $link,
            );
        }
        return $crumbs;
    }
}

if (!function_exists('nj_catalog_child_terms')) {
    function nj_catalog_child_terms($term) {
        $children = get_terms(array(
            'taxonomy' => 'product_cat',
            'parent' => $term->term_id,
            'hide_empty' => false,
        ));
        if ((!is_wp_error($children) && $children) || !$term->parent) {
            return $children;
        }
        return get_terms(array(
            'taxonomy' => 'product_cat',
            'parent' => $term->parent,
            'hide_empty' => false,
        ));
    }
}

if (!function_exists('nj_catalog_child_pills')) {
    function nj_catalog_child_pills($term) {
        $children = nj_catalog_child_terms($term);
        $pills = array();
        if (is_wp_error($children) || !$children) {
            return $pills;
        }
        foreach ($children as $child) {
            $link = get_term_link($child);
            if (is_wp_error($link)) {
                continue;
            }
            $pills[] = array(
                'label' => $child->name,
                'href' => $link,
                'active' => (int) $child->term_id === (int) $term->term_id,
            );
        }
        return $pills;
    }
}

if (!function_exists('nj_current_product_id')) {
    function nj_current_product_id() {
        if (!function_exists('get_the_ID')) {
            return 0;
        }
        return (int) call_user_func('get_the_ID');
    }
}

if (!function_exists('nj_catalog_shop_items')) {
    function nj_catalog_shop_items() {
        $items = array();
        if (!function_exists('woocommerce_product_loop') || !woocommerce_product_loop()) {
            return $items;
        }
        while (have_posts()) {
            the_post();
            $product_id = nj_current_product_id();
            $product = ($product_id && function_exists('wc_get_product')) ? wc_get_product($product_id) : null;
            if ($product) {
                $items[] = nj_card_from_product($product);
            }
        }
        return $items;
    }
}

if (!function_exists('nj_term_description')) {
    function nj_term_description($term_id) {
        if (!function_exists('term_description')) {
            return '';
        }
        return (string) call_user_func('term_description', $term_id, 'product_cat');
    }
}

if (!function_exists('nj_catalog_request_order')) {
    function nj_catalog_request_order() {
        if (!isset($_GET['orderby'])) {
            return 'date';
        }
        $value = $_GET['orderby'];
        if (function_exists('wp_unslash')) {
            $value = call_user_func('wp_unslash', $value);
        }
        if (function_exists('sanitize_key')) {
            $value = call_user_func('sanitize_key', $value);
        }
        return nj_catalog_order($value);
    }
}

if (!function_exists('nj_catalog_queried_term')) {
    function nj_catalog_queried_term() {
        if (!function_exists('is_product_category') || !call_user_func('is_product_category')) {
            return null;
        }
        if (!function_exists('get_queried_object')) {
            return null;
        }
        return call_user_func('get_queried_object');
    }
}

if (!function_exists('nj_catalog_from_shop')) {
    function nj_catalog_from_shop() {
        $order = nj_catalog_request_order();
        $term = nj_catalog_queried_term();
        $title = function_exists('woocommerce_page_title') ? woocommerce_page_title(false) : 'Tienda';
        $lead = 'Prendas disponibles para estrenar.';
        $action = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : nj_home_url();
        $crumbs = array(array('label' => 'Inicio', 'href' => nj_home_url()));
        $pills = array();
        if ($term && isset($term->term_id)) {
            $text = nj_plain_text(nj_term_description($term->term_id));
            if ($text !== '') {
                $lead = $text;
            }
            $link = get_term_link($term);
            if (!is_wp_error($link)) {
                $action = $link;
            }
            $crumbs = array_merge($crumbs, nj_catalog_ancestor_crumbs($term));
            $crumbs[] = array('label' => $term->name, 'href' => '');
            $pills = nj_catalog_child_pills($term);
        }
        return array(
            'title' => $title,
            'lead' => $lead,
            'crumbs' => $crumbs,
            'pills' => $pills,
            'items' => nj_catalog_shop_items(),
            'banner' => nj_slots_data()['gift_banner'],
            'order' => $order,
            'orders' => nj_catalog_orders(),
            'order_name' => 'orderby',
            'action' => $action,
            'hidden' => array(),
        );
    }
}
