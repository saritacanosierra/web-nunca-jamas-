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
            'banners' => function_exists('nj_regalo_visible') ? nj_regalo_visible(null) : array($slots['gift_banner']),
            'banner' => function_exists('nj_regalo_banner') ? nj_regalo_banner() : $slots['gift_banner'],
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

if (!function_exists('nj_catalog_pill_saved')) {
    function nj_catalog_pill_saved() {
        if (!function_exists('nj_espacios_saved')) {
            return null;
        }
        $saved = nj_espacios_saved();
        if (!is_array($saved) || !array_key_exists('pills', $saved) || !is_array($saved['pills'])) {
            return null;
        }
        return $saved['pills'];
    }
}

if (!function_exists('nj_catalog_pill_editor_rows')) {
    function nj_catalog_pill_editor_rows() {
        $saved = nj_catalog_pill_saved();
        if (is_array($saved)) {
            $rows = array();
            foreach ($saved as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $rows[] = array(
                    'label' => isset($row['label']) ? (string) $row['label'] : '',
                    'term' => isset($row['term']) ? (int) $row['term'] : 0,
                );
            }
            return $rows;
        }
        if (!function_exists('get_terms')) {
            return array();
        }
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'orderby' => 'name',
        ));
        if (is_wp_error($terms) || !is_array($terms)) {
            return array();
        }
        $rows = array();
        foreach ($terms as $term) {
            if (in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                continue;
            }
            $rows[] = array(
                'label' => $term->name,
                'term' => (int) $term->term_id,
            );
        }
        return $rows;
    }
}

if (!function_exists('nj_catalog_category_pills')) {
    function nj_catalog_category_pills($current_id) {
        $shop = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : nj_home_url();
        $pills = array(array(
            'label' => 'Todas',
            'href' => $shop,
            'active' => $current_id < 1,
        ));
        $saved = nj_catalog_pill_saved();
        if (is_array($saved)) {
            foreach ($saved as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $term = nj_category_term(isset($row['term']) ? (int) $row['term'] : 0);
                if (!$term || is_wp_error($term)) {
                    continue;
                }
                $link = get_term_link($term);
                if (is_wp_error($link)) {
                    continue;
                }
                $pills[] = array(
                    'label' => $term->name,
                    'href' => $link,
                    'active' => (int) $term->term_id === (int) $current_id,
                );
            }
            return $pills;
        }
        if (!function_exists('get_terms')) {
            return $pills;
        }
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'orderby' => 'name',
        ));
        if (is_wp_error($terms) || !is_array($terms)) {
            return $pills;
        }
        foreach ($terms as $term) {
            if (in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                continue;
            }
            $link = get_term_link($term);
            if (is_wp_error($link)) {
                continue;
            }
            $pills[] = array(
                'label' => $term->name,
                'href' => $link,
                'active' => (int) $term->term_id === (int) $current_id,
            );
        }
        return $pills;
    }
}

if (!function_exists('nj_catalog_banner_label')) {
    function nj_catalog_banner_label($term) {
        $names = array($term->name);
        if (function_exists('get_ancestors')) {
            foreach (get_ancestors($term->term_id, 'product_cat') as $ancestor_id) {
                $parent = nj_category_term($ancestor_id);
                if ($parent && !is_wp_error($parent)) {
                    array_unshift($names, $parent->name);
                }
            }
        }
        return implode(' / ', $names);
    }
}

if (!function_exists('nj_regalo_tags')) {
    function nj_regalo_tags() {
        $box = array('class' => true, 'style' => true);
        return array(
            'div' => $box,
            'span' => $box,
            'p' => $box,
            'h2' => $box,
            'h3' => $box,
            'strong' => array('class' => true),
            'em' => array('class' => true),
            'br' => array(),
            'a' => array('href' => true, 'class' => true, 'style' => true, 'target' => true, 'rel' => true),
            'img' => array('src' => true, 'alt' => true, 'class' => true, 'style' => true, 'width' => true, 'height' => true),
        );
    }
}

if (!function_exists('nj_regalo_html')) {
    function nj_regalo_html($html, $image = '', $alt = '') {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }
        $image = trim((string) $image);
        $alt = trim((string) $alt);
        if ($image !== '') {
            $src = function_exists('esc_url') ? esc_url($image) : $image;
            $label = function_exists('esc_attr') ? esc_attr($alt) : $alt;
            $tag = '<img src="' . $src . '" alt="' . $label . '">';
            $html = str_replace('{{foto}}', $tag, $html);
            $html = preg_replace_callback('/<img\b([^>]*)>/i', function ($match) use ($src, $label) {
                $attrs = $match[1];
                $has = preg_match('/\bsrc\s*=\s*(["\'])([^"\']*)\1/i', $attrs, $found);
                $current = $has ? trim($found[2]) : '';
                $ready = $current !== '' && (preg_match('#^https?://#i', $current) || strpos($current, '/') === 0);
                if ($ready) {
                    return $match[0];
                }
                if ($has) {
                    $attrs = preg_replace('/\bsrc\s*=\s*(["\'])([^"\']*)\1/i', 'src="' . $src . '"', $attrs, 1);
                } else {
                    $attrs = ' src="' . $src . '"' . $attrs;
                }
                if ($label !== '' && !preg_match('/\balt\s*=/i', $attrs)) {
                    $attrs .= ' alt="' . $label . '"';
                }
                return '<img' . $attrs . '>';
            }, $html);
        }
        if (function_exists('wp_kses')) {
            return wp_kses($html, nj_regalo_tags());
        }
        return $html;
    }
}

if (!function_exists('nj_regalo_blank')) {
    function nj_regalo_blank() {
        return array(
            'category' => 0,
            'kicker' => '',
            'title' => '',
            'text' => '',
            'alt' => '',
            'image_id' => 0,
            'image' => '',
            'html' => '',
        );
    }
}

if (!function_exists('nj_regalo_saved_list')) {
    function nj_regalo_saved_list() {
        if (!function_exists('get_option')) {
            return array();
        }
        $saved = get_option('nj_regalo', array());
        if (!is_array($saved)) {
            return array();
        }
        if (isset($saved['strips']) && is_array($saved['strips'])) {
            return $saved['strips'];
        }
        if (isset($saved['kicker']) || isset($saved['title']) || isset($saved['html']) || isset($saved['image_id'])) {
            $saved['category'] = 0;
            $defaults = nj_slots_data()['gift_banner'];
            foreach (array('kicker', 'title', 'text', 'alt') as $key) {
                if ((!isset($saved[$key]) || trim((string) $saved[$key]) === '') && !empty($defaults[$key])) {
                    $saved[$key] = $defaults[$key];
                }
            }
            if (empty($saved['image_id']) && !empty($defaults['image'])) {
                $saved['image'] = $defaults['image'];
            }
            return array($saved);
        }
        return array();
    }
}

if (!function_exists('nj_regalo_normalize')) {
    function nj_regalo_normalize($row) {
        $banner = nj_regalo_blank();
        if (!is_array($row)) {
            return $banner;
        }
        $banner['category'] = isset($row['category']) ? (int) $row['category'] : 0;
        foreach (array('kicker', 'title', 'text', 'alt') as $key) {
            if (isset($row[$key])) {
                $banner[$key] = (string) $row[$key];
            }
        }
        $banner['html'] = nj_regalo_html(isset($row['html']) ? $row['html'] : '');
        $image_id = isset($row['image_id']) ? (int) $row['image_id'] : 0;
        if ($image_id > 0 && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($image_id, 'large');
            if (is_string($url) && $url !== '') {
                $banner['image'] = $url;
                $banner['image_id'] = $image_id;
            }
        }
        if ($banner['image'] === '' && !empty($row['image'])) {
            $banner['image'] = (string) $row['image'];
        }
        return $banner;
    }
}

if (!function_exists('nj_regalo_rows')) {
    function nj_regalo_rows() {
        $saved = nj_regalo_saved_list();
        if (!$saved) {
            $one = nj_regalo_blank();
            $defaults = nj_slots_data()['gift_banner'];
            foreach (array('kicker', 'title', 'text', 'alt', 'image') as $key) {
                if (!empty($defaults[$key])) {
                    $one[$key] = $defaults[$key];
                }
            }
            return array($one);
        }
        $rows = array();
        foreach ($saved as $row) {
            $rows[] = nj_regalo_normalize($row);
        }
        return $rows ? $rows : array(nj_regalo_blank());
    }
}

if (!function_exists('nj_regalo_banner')) {
    function nj_regalo_banner() {
        $rows = nj_regalo_rows();
        return $rows[0];
    }
}

if (!function_exists('nj_regalo_visible')) {
    function nj_regalo_visible($term) {
        $rows = nj_regalo_rows();
        $wanted = array();
        if ($term && isset($term->term_id)) {
            $wanted[] = (int) $term->term_id;
            if (function_exists('get_ancestors')) {
                foreach (get_ancestors($term->term_id, 'product_cat') as $ancestor_id) {
                    $wanted[] = (int) $ancestor_id;
                }
            }
        }
        foreach ($wanted as $id) {
            $match = array();
            foreach ($rows as $row) {
                if ((int) $row['category'] === $id) {
                    $match[] = $row;
                }
            }
            if ($match) {
                return $match;
            }
        }
        $global = array();
        foreach ($rows as $row) {
            if ((int) $row['category'] === 0) {
                $global[] = $row;
            }
        }
        return $global;
    }
}

if (!function_exists('nj_regalo_save')) {
    function nj_regalo_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            wp_die('No tienes permiso para cambiar esta franja.');
        }
        check_admin_referer('nj_regalo', 'nj_regalo_nonce');
        $clip = function ($value, $limit) {
            $value = function_exists('sanitize_textarea_field') ? sanitize_textarea_field($value) : trim(strip_tags((string) $value));
            if (function_exists('mb_substr')) {
                return mb_substr($value, 0, $limit);
            }
            return substr($value, 0, $limit);
        };
        $raw = isset($_POST['franjas']) && is_array($_POST['franjas']) ? wp_unslash($_POST['franjas']) : array();
        $strips = array();
        foreach ($raw as $row) {
            if (!is_array($row) || count($strips) >= 12) {
                continue;
            }
            $category = isset($row['category']) ? absint($row['category']) : 0;
            if ($category) {
                $term = nj_category_term($category);
                if (!$term || is_wp_error($term) || $term->taxonomy !== 'product_cat') {
                    $category = 0;
                }
            }
            $image_id = isset($row['image_id']) ? absint($row['image_id']) : 0;
            if ($image_id && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($image_id)) {
                $image_id = 0;
            }
            $html = isset($row['html']) ? (string) $row['html'] : '';
            if (strlen($html) > 12000) {
                $html = substr($html, 0, 12000);
            }
            $strips[] = array(
                'category' => $category,
                'kicker' => $clip(isset($row['kicker']) ? $row['kicker'] : '', 80),
                'title' => $clip(isset($row['title']) ? $row['title'] : '', 160),
                'text' => $clip(isset($row['text']) ? $row['text'] : '', 240),
                'alt' => $clip(isset($row['alt']) ? $row['alt'] : '', 140),
                'image_id' => $image_id,
                'html' => nj_regalo_html($html),
            );
        }
        if (!$strips) {
            $strips[] = nj_regalo_blank();
        }
        update_option('nj_regalo', array('strips' => $strips), false);
        wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=regalo#regalo'));
        exit;
    }
}
add_action('admin_post_nj_regalo', 'nj_regalo_save');

if (!function_exists('nj_catalog_banner')) {
    function nj_catalog_banner($term) {
        if (function_exists('nj_regalo_visible')) {
            return nj_regalo_visible($term);
        }
        return array(nj_slots_data()['gift_banner']);
    }
}

if (!function_exists('nj_catalog_banner_rows')) {
    function nj_catalog_banner_rows() {
        if (!function_exists('get_terms')) {
            return array();
        }
        $terms = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));
        if (is_wp_error($terms) || !is_array($terms)) {
            return array();
        }
        $rows = array();
        foreach ($terms as $term) {
            if (in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                continue;
            }
            $rows[] = array(
                'id' => (int) $term->term_id,
                'label' => nj_catalog_banner_label($term),
            );
        }
        usort($rows, function ($left, $right) {
            return strcasecmp($left['label'], $right['label']);
        });
        return $rows;
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
        }
        $pills = nj_catalog_category_pills($term && isset($term->term_id) ? (int) $term->term_id : 0);
        $banners = nj_catalog_banner($term);
        return array(
            'title' => $title,
            'lead' => $lead,
            'crumbs' => $crumbs,
            'pills' => $pills,
            'items' => nj_catalog_shop_items(),
            'banners' => $banners,
            'banner' => $banners ? $banners[0] : array(),
            'order' => $order,
            'orders' => nj_catalog_orders(),
            'order_name' => 'orderby',
            'action' => $action,
            'hidden' => array(),
        );
    }
}
