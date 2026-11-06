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

if (!function_exists('nj_prendas_apply')) {
    function nj_prendas_apply($product, $name, $price, $before, $image_id, $category) {
        $product->set_name($name);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_stock_status('instock');
        if ($image_id > 0) {
            $product->set_image_id($image_id);
        }
        if (!$product->is_type('variable')) {
            if ($before > $price && $price > 0) {
                $product->set_regular_price((string) $before);
                $product->set_sale_price((string) $price);
            } elseif ($price > 0) {
                $product->set_regular_price((string) $price);
                $product->set_sale_price('');
            }
        }
        if ($category !== '' && function_exists('get_term_by')) {
            $term = get_term_by('slug', $category, 'product_cat');
            if ($term && !is_wp_error($term)) {
                $product->set_category_ids(array((int) $term->term_id));
            }
        }
        $product->save();
        $id = $product->get_id();
        update_post_meta($id, '_nj_home_nuevo', empty($_POST['en_nuevo']) ? '0' : '1');
        update_post_meta($id, '_nj_home_coleccion', empty($_POST['en_coleccion']) ? '0' : '1');
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
        $category = isset($_POST['categoria']) ? sanitize_title(wp_unslash($_POST['categoria'])) : '';
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
            nj_prendas_apply($product, $name, $price, $before, $image_id, $category);
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda#prendas'));
            exit;
        }

        if ($name === '' || $price < 1 || $image_id < 1) {
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=prenda-falta#prendas'));
            exit;
        }
        $product = new WC_Product_Simple();
        nj_prendas_apply($product, $name, $price, $before, $image_id, $category);
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
