<?php
/**
 * Alta y cambios simples de prendas, sin el editor de WooCommerce.
 */

if (!function_exists('nj_prendas_for_home')) {
    function nj_prendas_for_home($place) {
        if (!function_exists('wc_get_products')) {
            return array();
        }
        $key = $place === 'coleccion' ? '_nj_home_coleccion' : '_nj_home_nuevo';
        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => 8,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_key' => $key,
            'meta_value' => '1',
        ));
        return is_array($products) ? $products : array();
    }
}

if (!function_exists('nj_prendas_money')) {
    function nj_prendas_money($value) {
        if (function_exists('nj_money')) {
            return nj_money($value);
        }
        $digits = preg_replace('/\D/', '', (string) $value);
        return $digits === '' ? 0 : (int) $digits;
    }
}

if (!function_exists('nj_prendas_terms')) {
    function nj_prendas_terms() {
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
            $rows[] = $term;
        }
        return $rows;
    }
}

if (!function_exists('nj_prendas_is_kind')) {
    function nj_prendas_is_kind($name, $kind) {
        $name = function_exists('nj_menu_key') ? nj_menu_key(str_replace('pa_', '', (string) $name)) : strtolower((string) $name);
        if ($kind === 'color') {
            return strpos($name, 'color') !== false || strpos($name, 'colour') !== false;
        }
        return strpos($name, 'talla') !== false || strpos($name, 'size') !== false;
    }
}

if (!function_exists('nj_prendas_value_label')) {
    function nj_prendas_value_label($key, $value) {
        $taxonomy = str_replace('attribute_', '', (string) $key);
        if (strpos($taxonomy, 'pa_') === 0 && function_exists('get_term_by')) {
            $term = get_term_by('slug', (string) $value, $taxonomy);
            if ($term && !is_wp_error($term)) {
                return $term->name;
            }
        }
        return (string) $value;
    }
}

if (!function_exists('nj_prendas_size_slug')) {
    function nj_prendas_size_slug($label) {
        return function_exists('sanitize_title') ? sanitize_title($label) : strtolower($label);
    }
}

if (!function_exists('nj_prendas_size_attribute')) {
    function nj_prendas_size_attribute() {
        $taxonomy = 'pa_talla';
        if (!taxonomy_exists($taxonomy) && function_exists('wc_create_attribute')) {
            if (!wc_attribute_taxonomy_id_by_name('talla')) {
                wc_create_attribute(array(
                    'name' => 'Talla',
                    'slug' => 'talla',
                    'type' => 'select',
                    'order_by' => 'menu_order',
                    'has_archives' => false,
                ));
            }
            delete_transient('wc_attribute_taxonomies');
            register_taxonomy($taxonomy, array('product'), array(
                'hierarchical' => false,
                'show_ui' => false,
                'query_var' => true,
                'rewrite' => false,
            ));
        }
        return $taxonomy;
    }
}

if (!function_exists('nj_prendas_taxonomy')) {
    function nj_prendas_taxonomy($slug, $label) {
        $taxonomy = 'pa_' . $slug;
        if (!taxonomy_exists($taxonomy) && function_exists('wc_create_attribute')) {
            if (!wc_attribute_taxonomy_id_by_name($slug)) {
                wc_create_attribute(array(
                    'name' => $label,
                    'slug' => $slug,
                    'type' => 'select',
                    'order_by' => 'menu_order',
                    'has_archives' => false,
                ));
            }
            delete_transient('wc_attribute_taxonomies');
            register_taxonomy($taxonomy, array('product'), array(
                'hierarchical' => false,
                'show_ui' => false,
                'query_var' => true,
                'rewrite' => false,
            ));
        }
        return $taxonomy;
    }
}

if (!function_exists('nj_prendas_term_id')) {
    function nj_prendas_term_id($taxonomy, $label) {
        $label = trim((string) $label);
        if ($label === '' || !taxonomy_exists($taxonomy)) {
            return 0;
        }
        $slug = nj_prendas_size_slug($label);
        $term = get_term_by('slug', $slug, $taxonomy);
        if ($term && !is_wp_error($term)) {
            if ($term->name !== $label && function_exists('wp_update_term')) {
                wp_update_term($term->term_id, $taxonomy, array('name' => $label));
            }
            return (int) $term->term_id;
        }
        $created = wp_insert_term($label, $taxonomy, array('slug' => $slug));
        if ($created instanceof WP_Error) {
            $data = $created->get_error_data();
            return is_numeric($data) ? (int) $data : 0;
        }
        return is_array($created) && isset($created['term_id']) ? (int) $created['term_id'] : 0;
    }
}

if (!function_exists('nj_prendas_clean_label')) {
    function nj_prendas_clean_label($value) {
        $value = function_exists('sanitize_text_field') ? sanitize_text_field($value) : trim(strip_tags((string) $value));
        return trim($value);
    }
}

if (!function_exists('nj_prendas_posted_rows')) {
    function nj_prendas_posted_rows() {
        $posted = isset($_POST['pieza']) && is_array($_POST['pieza']) ? wp_unslash($_POST['pieza']) : array();
        $named = false;
        foreach ($posted as $pieza) {
            if (is_array($pieza) && nj_prendas_clean_label(isset($pieza['color']) ? $pieza['color'] : '') !== '') {
                $named = true;
            }
        }
        $rows = array();
        foreach ($posted as $pieza) {
            if (!is_array($pieza)) {
                continue;
            }
            $color = nj_prendas_clean_label(isset($pieza['color']) ? $pieza['color'] : '');
            if ($named && $color === '') {
                continue;
            }
            $tallas = isset($pieza['tallas']) && is_array($pieza['tallas']) ? $pieza['tallas'] : array();
            foreach ($tallas as $size) {
                if (!is_array($size)) {
                    continue;
                }
                $talla = nj_prendas_clean_label(isset($size['nombre']) ? $size['nombre'] : '');
                if ($talla === '') {
                    continue;
                }
                $qty = isset($size['qty']) ? (int) $size['qty'] : 0;
                $key = nj_prendas_size_slug($talla) . '|' . nj_prendas_size_slug($color);
                $rows[$key] = array(
                    'talla' => $talla,
                    'color' => $color,
                    'qty' => max(0, min(999, $qty)),
                );
            }
        }
        return array_values($rows);
    }
}

if (!function_exists('nj_prendas_posted_categories')) {
    function nj_prendas_posted_categories() {
        $raw = isset($_POST['categorias']) && is_array($_POST['categorias']) ? wp_unslash($_POST['categorias']) : array();
        $ids = array();
        foreach ($raw as $value) {
            $id = (int) $value;
            if ($id < 1 || !function_exists('get_term')) {
                continue;
            }
            $term = get_term($id, 'product_cat');
            if (!$term instanceof WP_Term) {
                continue;
            }
            if (!in_array($term->slug, array('uncategorized', 'sin-categorizar'), true)) {
                $ids[] = $id;
            }
        }
        return array_values(array_unique($ids));
    }
}

if (!function_exists('nj_prendas_posted_tags')) {
    function nj_prendas_posted_tags() {
        $raw = isset($_POST['etiquetas']) ? sanitize_text_field(wp_unslash($_POST['etiquetas'])) : '';
        $tags = array();
        foreach (explode(',', $raw) as $tag) {
            $tag = trim($tag);
            if ($tag !== '' && !in_array($tag, $tags, true)) {
                $tags[] = $tag;
            }
        }
        return $tags;
    }
}

if (!function_exists('nj_prendas_posted_gallery')) {
    function nj_prendas_posted_gallery($featured) {
        $raw = isset($_POST['galeria']) && is_array($_POST['galeria']) ? wp_unslash($_POST['galeria']) : array();
        $ids = array();
        foreach ($raw as $value) {
            $id = (int) $value;
            if ($id < 1 || $id === (int) $featured || in_array($id, $ids, true)) {
                continue;
            }
            if (function_exists('wp_attachment_is_image') && !wp_attachment_is_image($id)) {
                continue;
            }
            $ids[] = $id;
            if (count($ids) >= 8) {
                break;
            }
        }
        return $ids;
    }
}

if (!function_exists('nj_prendas_attribute_labels')) {
    function nj_prendas_attribute_labels($product, $kind) {
        $labels = array();
        if (!is_object($product) || !method_exists($product, 'get_attributes')) {
            return $labels;
        }
        foreach ($product->get_attributes() as $attr) {
            if (!nj_prendas_is_kind($attr->get_name(), $kind)) {
                continue;
            }
            foreach ($attr->get_options() as $option) {
                if ($attr->get_id() && function_exists('get_term')) {
                    $term = get_term((int) $option, $attr->get_name());
                    $label = $term instanceof WP_Term ? $term->name : '';
                } else {
                    $label = trim((string) $option);
                }
                if ($label !== '' && !in_array($label, $labels, true)) {
                    $labels[] = $label;
                }
            }
        }
        if (!$product->is_type('variable')) {
            return $labels;
        }
        foreach ($product->get_children() as $child_id) {
            $child = wc_get_product($child_id);
            if (!$child instanceof WC_Product) {
                continue;
            }
            foreach ($child->get_attributes() as $key => $value) {
                if (!nj_prendas_is_kind($key, $kind)) {
                    continue;
                }
                $label = nj_prendas_value_label($key, $value);
                if ($label !== '' && !in_array($label, $labels, true)) {
                    $labels[] = $label;
                }
            }
        }
        return $labels;
    }
}

if (!function_exists('nj_prendas_color_groups')) {
    function nj_prendas_color_groups($product) {
        $empty = array(array('color' => '', 'sizes' => array(array('talla' => '', 'qty' => 1))));
        if (!is_object($product) || !method_exists($product, 'is_type') || !$product->is_type('variable')) {
            return $empty;
        }
        $groups = array();
        $order = array();
        foreach ($product->get_children() as $child_id) {
            $child = wc_get_product($child_id);
            if (!$child instanceof WC_Product) {
                continue;
            }
            $talla = '';
            $color = '';
            foreach ($child->get_attributes() as $key => $value) {
                if (nj_prendas_is_kind($key, 'talla')) {
                    $talla = nj_prendas_value_label($key, $value);
                }
                if (nj_prendas_is_kind($key, 'color')) {
                    $color = nj_prendas_value_label($key, $value);
                }
            }
            if ($talla === '') {
                continue;
            }
            if (!isset($groups[$color])) {
                $groups[$color] = array();
                $order[] = $color;
            }
            $qty = $child->managing_stock() ? (int) $child->get_stock_quantity() : ($child->is_in_stock() ? 1 : 0);
            $groups[$color][] = array('talla' => $talla, 'qty' => max(0, $qty));
        }
        if (!$order) {
            return $empty;
        }
        $result = array();
        foreach ($order as $color) {
            $result[] = array('color' => $color, 'sizes' => $groups[$color]);
        }
        return $result;
    }
}

if (!function_exists('nj_prendas_stock_lookup')) {
    function nj_prendas_stock_lookup($product) {
        $map = array();
        if (!is_object($product) || !method_exists($product, 'is_type') || !$product->is_type('variable')) {
            return $map;
        }
        foreach ($product->get_children() as $child_id) {
            $child = wc_get_product($child_id);
            if (!$child instanceof WC_Product) {
                continue;
            }
            $talla = '';
            $color = '';
            foreach ($child->get_attributes() as $key => $value) {
                if (nj_prendas_is_kind($key, 'talla')) {
                    $talla = nj_prendas_value_label($key, $value);
                }
                if (nj_prendas_is_kind($key, 'color')) {
                    $color = nj_prendas_value_label($key, $value);
                }
            }
            if ($talla === '') {
                continue;
            }
            $qty = $child->managing_stock() ? (int) $child->get_stock_quantity() : ($child->is_in_stock() ? 1 : 0);
            $map[nj_prendas_size_slug($talla) . '|' . nj_prendas_size_slug($color)] = $qty;
        }
        return $map;
    }
}

if (!function_exists('nj_prendas_tag_text')) {
    function nj_prendas_tag_text($product) {
        $names = array();
        if (!is_object($product) || !method_exists($product, 'get_tag_ids')) {
            return '';
        }
        foreach ($product->get_tag_ids() as $id) {
            $term = get_term((int) $id, 'product_tag');
            if ($term instanceof WP_Term) {
                $names[] = $term->name;
            }
        }
        return implode(', ', $names);
    }
}

if (!function_exists('nj_prendas_plain')) {
    function nj_prendas_plain($html) {
        $html = str_ireplace(array('<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'), "\n", (string) $html);
        $text = trim(html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES, 'UTF-8'));
        $text = preg_replace("/[ \t]+\n/", "\n", $text);
        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }
}

if (!function_exists('nj_prendas_stock_level')) {
    function nj_prendas_stock_level($product) {
        $map = nj_prendas_stock_lookup($product);
        if ($map) {
            $total = 0;
            foreach ($map as $qty) {
                $total += (int) $qty;
            }
            if ($total < 1) {
                return 'out';
            }
            if ($total <= 3) {
                return 'low';
            }
            return 'ok';
        }
        if (is_object($product) && method_exists($product, 'managing_stock') && $product->managing_stock()) {
            $qty = (int) $product->get_stock_quantity();
            if ($qty < 1) {
                return 'out';
            }
            if ($qty <= 3) {
                return 'low';
            }
            return 'ok';
        }
        if (is_object($product) && method_exists($product, 'is_in_stock') && !$product->is_in_stock()) {
            return 'out';
        }
        return 'ok';
    }
}

if (!function_exists('nj_prendas_prices')) {
    function nj_prendas_prices($product) {
        $price = (int) round((float) $product->get_price());
        $regular = (int) round((float) $product->get_regular_price());
        if ($product->is_type('variable')) {
            $children = $product->get_children();
            $child = $children ? wc_get_product((int) $children[0]) : null;
            if ($child) {
                $price = (int) round((float) $child->get_price());
                $regular = (int) round((float) $child->get_regular_price());
            }
        }
        return array($price, $regular);
    }
}

if (!function_exists('nj_prendas_sync_sizes')) {
    function nj_prendas_sync_sizes($product, $rows, $price, $before) {
        if (!class_exists('WC_Product_Attribute') || !class_exists('WC_Product_Variation') || !$rows) {
            return $product;
        }
        $talla_tax = nj_prendas_taxonomy('talla', 'Talla');
        $color_tax = nj_prendas_taxonomy('color', 'Color');
        $id = $product->get_id();
        if (!$product->is_type('variable')) {
            wp_set_object_terms($id, 'variable', 'product_type');
            clean_post_cache($id);
            $product = wc_get_product($id);
        }
        if (!$product || !$product->is_type('variable')) {
            return $product;
        }
        $talla_ids = array();
        $color_ids = array();
        foreach ($rows as $row) {
            $talla_id = nj_prendas_term_id($talla_tax, $row['talla']);
            if ($talla_id && !in_array($talla_id, $talla_ids, true)) {
                $talla_ids[] = $talla_id;
            }
            if ($row['color'] !== '') {
                $color_id = nj_prendas_term_id($color_tax, $row['color']);
                if ($color_id && !in_array($color_id, $color_ids, true)) {
                    $color_ids[] = $color_id;
                }
            }
        }
        $kept_attributes = array();
        foreach ($product->get_attributes() as $attr) {
            if (nj_prendas_is_kind($attr->get_name(), 'talla') || nj_prendas_is_kind($attr->get_name(), 'color')) {
                continue;
            }
            $kept_attributes[] = $attr;
        }
        $attributes = $kept_attributes;
        $talla_attribute = new WC_Product_Attribute();
        $talla_attribute->set_id((int) wc_attribute_taxonomy_id_by_name('talla'));
        $talla_attribute->set_name($talla_tax);
        $talla_attribute->set_options($talla_ids);
        $talla_attribute->set_position(0);
        $talla_attribute->set_visible(true);
        $talla_attribute->set_variation(true);
        $attributes[] = $talla_attribute;
        if ($color_ids) {
            $color_attribute = new WC_Product_Attribute();
            $color_attribute->set_id((int) wc_attribute_taxonomy_id_by_name('color'));
            $color_attribute->set_name($color_tax);
            $color_attribute->set_options($color_ids);
            $color_attribute->set_position(1);
            $color_attribute->set_visible(true);
            $color_attribute->set_variation(true);
            $attributes[] = $color_attribute;
        }
        $product->set_attributes($attributes);
        $regular = $before > $price && $price > 0 ? $before : $price;
        $sale = $before > $price && $price > 0 ? $price : 0;
        $product->set_regular_price((string) $regular);
        $product->set_sale_price($sale > 0 ? (string) $sale : '');
        $product->save();

        $existing = array();
        foreach ($product->get_children() as $child_id) {
            $child = wc_get_product($child_id);
            if (!$child instanceof WC_Product) {
                continue;
            }
            $talla = '';
            $color = '';
            foreach ($child->get_attributes() as $key => $value) {
                if (nj_prendas_is_kind($key, 'talla')) {
                    $talla = nj_prendas_size_slug(nj_prendas_value_label($key, $value));
                }
                if (nj_prendas_is_kind($key, 'color')) {
                    $color = nj_prendas_size_slug(nj_prendas_value_label($key, $value));
                }
            }
            if ($talla !== '') {
                $existing[$talla . '|' . $color] = $child;
            }
        }
        $keep = array();
        foreach ($rows as $row) {
            $key = nj_prendas_size_slug($row['talla']) . '|' . nj_prendas_size_slug($row['color']);
            $variation = isset($existing[$key]) ? $existing[$key] : new WC_Product_Variation();
            if (!$variation->get_id()) {
                $variation->set_parent_id($product->get_id());
            }
            $variation_attributes = array($talla_tax => nj_prendas_size_slug($row['talla']));
            if ($row['color'] !== '') {
                $variation_attributes[$color_tax] = nj_prendas_size_slug($row['color']);
            }
            $variation->set_attributes($variation_attributes);
            $variation->set_regular_price((string) $regular);
            $variation->set_sale_price($sale > 0 ? (string) $sale : '');
            $variation->set_manage_stock(true);
            $variation->set_stock_quantity((int) $row['qty']);
            $variation->set_stock_status($row['qty'] > 0 ? 'instock' : 'outofstock');
            $variation->set_status('publish');
            $variation->save();
            $keep[] = $variation->get_id();
        }
        foreach ($product->get_children() as $child_id) {
            if (!in_array((int) $child_id, $keep, true)) {
                $child = wc_get_product($child_id);
                if ($child instanceof WC_Product) {
                    $child->delete(true);
                }
            }
        }
        if (class_exists('WC_Product_Variable')) {
            WC_Product_Variable::sync($product->get_id());
        }
        wc_delete_product_transients($product->get_id());
        return wc_get_product($product->get_id());
    }
}

if (!function_exists('nj_prendas_apply')) {
    function nj_prendas_apply($product, $name, $price, $before, $image_id, $categories, $rows, $gallery, $short, $long, $sku, $tags) {
        $product->set_name($name);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_description($long === '' ? '' : wpautop($long));
        $product->set_short_description($short === '' ? '' : wpautop($short));
        try {
            $product->set_sku($sku);
        } catch (Exception $exception) {
            unset($exception);
        }
        if ($image_id > 0) {
            $product->set_image_id($image_id);
        }
        $product->set_gallery_image_ids($gallery);
        $product->set_category_ids($categories);
        $product->save();
        if ($rows) {
            $product = nj_prendas_sync_sizes($product, $rows, $price, $before);
        }
        $id = $product->get_id();
        wp_set_object_terms($id, $tags, 'product_tag');
        update_post_meta($id, '_nj_home_nuevo', empty($_POST['en_nuevo']) ? '0' : '1');
        update_post_meta($id, '_nj_home_coleccion', empty($_POST['en_coleccion']) ? '0' : '1');
        update_post_meta($id, '_nj_personalizado', empty($_POST['personalizado']) ? '0' : '1');
        $nj_plain = function ($key) {
            $value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
            return function_exists('sanitize_textarea_field') ? sanitize_textarea_field($value) : trim(strip_tags((string) $value));
        };
        update_post_meta($id, '_nj_envios', $nj_plain('envios'));
        update_post_meta($id, '_nj_cuidados', $nj_plain('cuidados'));
        $look = array();
        if (isset($_POST['look']) && is_array($_POST['look'])) {
            foreach ($_POST['look'] as $look_id) {
                $look_id = absint($look_id);
                if ($look_id > 0 && $look_id !== (int) $id && !in_array($look_id, $look, true)) {
                    $look[] = $look_id;
                }
            }
        }
        update_post_meta($id, '_nj_look', array_slice($look, 0, 4));
        return $id;
    }
}

if (!function_exists('nj_prendas_admin_save')) {
    function nj_prendas_admin_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed || !current_user_can('edit_products')) {
            wp_die('No tienes permiso para cambiar prendas.');
        }
        check_admin_referer('nj_prenda_save', 'nj_prenda_nonce');
        if (!class_exists('WC_Product_Simple') || !function_exists('wc_get_product')) {
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda-off#prendas'));
            exit;
        }
        $id = isset($_POST['prenda_id']) ? (int) $_POST['prenda_id'] : 0;
        $name = isset($_POST['nombre']) ? sanitize_text_field(wp_unslash($_POST['nombre'])) : '';
        $price = nj_prendas_money(isset($_POST['precio']) ? wp_unslash($_POST['precio']) : '');
        $before = nj_prendas_money(isset($_POST['precio_antes']) ? wp_unslash($_POST['precio_antes']) : '');
        $image_id = isset($_POST['foto']) ? (int) $_POST['foto'] : 0;
        if ($image_id > 0 && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($image_id)) {
            $image_id = 0;
        }
        $categories = nj_prendas_posted_categories();
        $rows = nj_prendas_posted_rows();
        $gallery = nj_prendas_posted_gallery($image_id);
        $short = isset($_POST['descripcion']) ? sanitize_textarea_field(wp_unslash($_POST['descripcion'])) : '';
        $long = isset($_POST['descripcion_larga']) ? sanitize_textarea_field(wp_unslash($_POST['descripcion_larga'])) : '';
        $sku = isset($_POST['sku']) ? sanitize_text_field(wp_unslash($_POST['sku'])) : '';
        $tags = nj_prendas_posted_tags();
        $hide = !empty($_POST['ocultar']);
        $show = !empty($_POST['mostrar']);

        if ($id > 0) {
            $product = wc_get_product($id);
            if (!$product) {
                wp_safe_redirect(admin_url('admin.php?page=nj-tienda#prendas'));
                exit;
            }
            if ($show) {
                $product->set_status('publish');
                $product->save();
                wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda#prendas'));
                exit;
            }
            if ($hide) {
                $product->set_status('draft');
                $product->save();
                wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=oculta#prendas'));
                exit;
            }
            if ($name === '') {
                $name = $product->get_name();
            }
            if (!$rows) {
                wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda-talla#prendas'));
                exit;
            }
            nj_prendas_apply($product, $name, $price, $before, $image_id, $categories, $rows, $gallery, $short, $long, $sku, $tags);
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda#prendas'));
            exit;
        }

        if ($name === '' || $price < 1 || $image_id < 1 || !$rows) {
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda-falta#prendas'));
            exit;
        }
        $product = new WC_Product_Variable();
        nj_prendas_apply($product, $name, $price, $before, $image_id, $categories, $rows, $gallery, $short, $long, $sku, $tags);
        wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda#prendas'));
        exit;
    }
}
add_action('admin_post_nj_prenda_save', 'nj_prendas_admin_save');

if (!function_exists('nj_prendas_home_flags')) {
    function nj_prendas_home_flags($id) {
        return array(
            'nuevo' => get_post_meta($id, '_nj_home_nuevo', true) === '1',
            'coleccion' => get_post_meta($id, '_nj_home_coleccion', true) === '1',
        );
    }
}

require_once __DIR__ . '/admin/prendas.php';
