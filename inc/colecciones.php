<?php
/**
 * Lo que se edita de una colección: la banda de texto, el diseño y sus prendas.
 */

if (!function_exists('nj_coleccion_pieza')) {
    function nj_coleccion_pieza($term_id) {
        $term_id = (int) $term_id;
        $modo = function_exists('get_term_meta') ? (string) get_term_meta($term_id, 'nj_modo', true) : '';
        $modo = $modo === 'html' ? 'html' : 'imagen';
        $image_id = function_exists('get_term_meta') ? (int) get_term_meta($term_id, 'nj_imagen', true) : 0;
        $image = '';
        if ($image_id > 0 && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($image_id, 'full');
            $image = is_string($url) ? $url : '';
        }
        $html = function_exists('get_term_meta') ? (string) get_term_meta($term_id, 'nj_html', true) : '';
        $banda = function_exists('get_term_meta') ? (string) get_term_meta($term_id, 'nj_banda', true) : '';
        $meta = function ($key) use ($term_id) {
            return function_exists('get_term_meta') ? (string) get_term_meta($term_id, $key, true) : '';
        };
        return array(
            'modo' => $modo,
            'image_id' => $image_id,
            'image' => $image,
            'html' => $html,
            'banda' => $banda,
            'banda_ajuste' => nj_coleccion_banda_ajuste(array(
                'vel' => $meta('nj_banda_vel'),
                'fondo' => $meta('nj_banda_fondo'),
                'color' => $meta('nj_banda_color'),
                'color_b' => $meta('nj_banda_color_b'),
                'rumbo' => $meta('nj_banda_rumbo'),
                'tinta' => $meta('nj_banda_tinta'),
            )),
            'titulo' => $meta('nj_titulo'),
            'kicker' => $meta('nj_kicker'),
            'texto' => $meta('nj_texto'),
            'x' => $meta('nj_x'),
            'y' => $meta('nj_y'),
            'size' => $meta('nj_size'),
            'small' => $meta('nj_small'),
        );
    }
}

if (!function_exists('nj_coleccion_numero')) {
    function nj_coleccion_numero($value, $min, $max, $fallback) {
        if ($value === '' || $value === null) {
            return $fallback;
        }
        $number = (int) $value;
        if ($number < $min) {
            return $min;
        }
        if ($number > $max) {
            return $max;
        }
        return $number;
    }
}

if (!function_exists('nj_coleccion_caja')) {
    function nj_coleccion_caja($pieza) {
        $pieza = is_array($pieza) ? $pieza : array();
        return array(
            'titulo' => isset($pieza['titulo']) ? (string) $pieza['titulo'] : '',
            'kicker' => isset($pieza['kicker']) ? (string) $pieza['kicker'] : '',
            'texto' => isset($pieza['texto']) ? (string) $pieza['texto'] : '',
            'x' => nj_coleccion_numero(isset($pieza['x']) ? $pieza['x'] : '', 0, 100, 50),
            'y' => nj_coleccion_numero(isset($pieza['y']) ? $pieza['y'] : '', 0, 100, 50),
            'size' => nj_coleccion_numero(isset($pieza['size']) ? $pieza['size'] : '', 18, 96, 72),
            'small' => nj_coleccion_numero(isset($pieza['small']) ? $pieza['small'] : '', 10, 32, 16),
        );
    }
}

if (!function_exists('nj_coleccion_color')) {
    function nj_coleccion_color($value, $fallback) {
        $value = strtolower(trim((string) $value));
        if (preg_match('/^#([0-9a-f]{6})$/', $value)) {
            return $value;
        }
        if (preg_match('/^#([0-9a-f]{3})$/', $value, $found)) {
            $short = $found[1];
            return '#' . $short[0] . $short[0] . $short[1] . $short[1] . $short[2] . $short[2];
        }
        return $fallback;
    }
}

if (!function_exists('nj_coleccion_banda_ajuste')) {
    function nj_coleccion_banda_ajuste($raw) {
        $raw = is_array($raw) ? $raw : array();
        $fondo = isset($raw['fondo']) && $raw['fondo'] === 'degradado' ? 'degradado' : 'solido';
        $rumbo = isset($raw['rumbo']) ? (string) $raw['rumbo'] : 'horizontal';
        if (!in_array($rumbo, array('horizontal', 'vertical', 'diagonal'), true)) {
            $rumbo = 'horizontal';
        }
        return array(
            'vel' => nj_coleccion_numero(isset($raw['vel']) ? $raw['vel'] : '', 1, 10, 5),
            'fondo' => $fondo,
            'color' => nj_coleccion_color(isset($raw['color']) ? $raw['color'] : '', '#17324d'),
            'color_b' => nj_coleccion_color(isset($raw['color_b']) ? $raw['color_b'] : '', '#64bfae'),
            'rumbo' => $rumbo,
            'tinta' => nj_coleccion_color(isset($raw['tinta']) ? $raw['tinta'] : '', '#ffffff'),
        );
    }
}

if (!function_exists('nj_coleccion_banda_segundos')) {
    function nj_coleccion_banda_segundos($vel) {
        $mapa = array(1 => 50, 2 => 42, 3 => 36, 4 => 30, 5 => 28, 6 => 22, 7 => 16, 8 => 12, 9 => 9, 10 => 6);
        $vel = nj_coleccion_numero($vel, 1, 10, 5);
        return $mapa[$vel];
    }
}

if (!function_exists('nj_coleccion_banda_estilo')) {
    function nj_coleccion_banda_estilo($raw) {
        $ajuste = nj_coleccion_banda_ajuste($raw);
        $fondo = $ajuste['color'];
        if ($ajuste['fondo'] === 'degradado') {
            $grados = array('horizontal' => 90, 'vertical' => 180, 'diagonal' => 135);
            $fondo = 'linear-gradient(' . $grados[$ajuste['rumbo']] . 'deg,' . $ajuste['color'] . ',' . $ajuste['color_b'] . ')';
        }
        return '--banda-tiempo:' . nj_coleccion_banda_segundos($ajuste['vel']) . 's;background:' . $fondo . ';color:' . $ajuste['tinta'];
    }
}

if (!function_exists('nj_coleccion_estilo')) {
    function nj_coleccion_estilo($caja) {
        $caja = nj_coleccion_caja($caja);
        return '--coleccion-x:' . $caja['x'] . '%;--coleccion-y:' . $caja['y'] . '%;--coleccion-size:' . $caja['size'] . 'px;--coleccion-small:' . $caja['small'] . 'px';
    }
}

if (!function_exists('nj_coleccion_prendas_ids')) {
    function nj_coleccion_prendas_ids($term_id) {
        if (!function_exists('get_posts')) {
            return array();
        }
        $ids = get_posts(array(
            'post_type' => 'product',
            'post_status' => array('publish', 'private', 'draft'),
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'title',
            'order' => 'ASC',
            'tax_query' => array(array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => (int) $term_id,
                'include_children' => false,
            )),
        ));
        $clean = array();
        foreach ((array) $ids as $id) {
            $clean[] = (int) $id;
        }
        return $clean;
    }
}

if (!function_exists('nj_coleccion_padre_listo')) {
    function nj_coleccion_padre_listo() {
        $parent = function_exists('nj_colecciones_padre') ? nj_colecciones_padre() : null;
        if ($parent && isset($parent->term_id)) {
            return $parent;
        }
        if (!function_exists('wp_insert_term')) {
            return null;
        }
        $made = wp_insert_term('Colecciones', 'product_cat', array('slug' => 'colecciones'));
        $id = 0;
        if (is_array($made) && isset($made['term_id'])) {
            $id = (int) $made['term_id'];
        } elseif (function_exists('get_term_by')) {
            $found = get_term_by('slug', 'colecciones', 'product_cat');
            if ($found && !is_wp_error($found)) {
                $id = (int) $found->term_id;
            }
        }
        if ($id < 1 || !function_exists('get_term')) {
            return null;
        }
        $term = get_term($id, 'product_cat');
        if (!$term || is_wp_error($term)) {
            return null;
        }
        return $term;
    }
}

if (!function_exists('nj_coleccion_guardar_pieza')) {
    function nj_coleccion_guardar_pieza($term_id, $banda, $modo, $html, $image_id, $caja, $banda_ajuste = array()) {
        $term_id = (int) $term_id;
        if ($term_id < 1 || !function_exists('update_term_meta')) {
            return;
        }
        $caja = nj_coleccion_caja($caja);
        $banda_ajuste = nj_coleccion_banda_ajuste($banda_ajuste);
        update_term_meta($term_id, 'nj_banda', $banda);
        update_term_meta($term_id, 'nj_banda_vel', (string) $banda_ajuste['vel']);
        update_term_meta($term_id, 'nj_banda_fondo', $banda_ajuste['fondo']);
        update_term_meta($term_id, 'nj_banda_color', $banda_ajuste['color']);
        update_term_meta($term_id, 'nj_banda_color_b', $banda_ajuste['color_b']);
        update_term_meta($term_id, 'nj_banda_rumbo', $banda_ajuste['rumbo']);
        update_term_meta($term_id, 'nj_banda_tinta', $banda_ajuste['tinta']);
        update_term_meta($term_id, 'nj_modo', $modo === 'html' ? 'html' : 'imagen');
        update_term_meta($term_id, 'nj_html', $html);
        update_term_meta($term_id, 'nj_imagen', (int) $image_id);
        update_term_meta($term_id, 'nj_titulo', $caja['titulo']);
        update_term_meta($term_id, 'nj_kicker', $caja['kicker']);
        update_term_meta($term_id, 'nj_texto', $caja['texto']);
        update_term_meta($term_id, 'nj_x', (string) $caja['x']);
        update_term_meta($term_id, 'nj_y', (string) $caja['y']);
        update_term_meta($term_id, 'nj_size', (string) $caja['size']);
        update_term_meta($term_id, 'nj_small', (string) $caja['small']);
        if ($image_id > 0) {
            update_term_meta($term_id, 'thumbnail_id', (int) $image_id);
        }
    }
}

if (!function_exists('nj_coleccion_montar_prendas')) {
    function nj_coleccion_montar_prendas($term_id, $wanted) {
        $term_id = (int) $term_id;
        $current = nj_coleccion_prendas_ids($term_id);
        $keep = array();
        foreach ((array) $wanted as $id) {
            $id = (int) $id;
            if ($id < 1 || isset($keep[$id])) {
                continue;
            }
            if (function_exists('get_post_type') && get_post_type($id) !== 'product') {
                continue;
            }
            $keep[$id] = $id;
            if (count($keep) >= 40) {
                break;
            }
        }
        $wanted = array_values($keep);
        foreach ($current as $id) {
            if (!in_array($id, $wanted, true) && function_exists('wp_remove_object_terms')) {
                wp_remove_object_terms($id, $term_id, 'product_cat');
            }
        }
        foreach ($wanted as $id) {
            if (!in_array($id, $current, true) && function_exists('wp_set_object_terms')) {
                wp_set_object_terms($id, array($term_id), 'product_cat', true);
            }
        }
    }
}

if (!function_exists('nj_coleccion_save')) {
    function nj_coleccion_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('manage_woocommerce'));
        if (!$allowed) {
            wp_die('No tienes permiso para cambiar las colecciones.');
        }
        check_admin_referer('nj_coleccion', 'nj_coleccion_nonce');
        $nombre = isset($_POST['nombre']) ? wp_unslash($_POST['nombre']) : '';
        $nombre = function_exists('sanitize_text_field') ? sanitize_text_field($nombre) : trim(strip_tags((string) $nombre));
        $banda = isset($_POST['banda']) ? wp_unslash($_POST['banda']) : '';
        $banda = function_exists('sanitize_text_field') ? sanitize_text_field($banda) : trim(strip_tags((string) $banda));
        if (function_exists('mb_substr')) {
            $banda = mb_substr($banda, 0, 160);
        } else {
            $banda = substr($banda, 0, 160);
        }
        $modo = isset($_POST['modo']) && $_POST['modo'] === 'html' ? 'html' : 'imagen';
        $html = isset($_POST['html']) ? (string) wp_unslash($_POST['html']) : '';
        if (strlen($html) > 20000) {
            $html = substr($html, 0, 20000);
        }
        $html = function_exists('nj_regalo_html') ? nj_regalo_html($html) : '';
        $image_id = isset($_POST['image_id']) ? absint($_POST['image_id']) : 0;
        if ($image_id && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($image_id)) {
            $image_id = 0;
        }
        $posted_caja = isset($_POST['caja']) && is_array($_POST['caja']) ? wp_unslash($_POST['caja']) : array();
        $clean = function ($value, $limit) {
            $value = function_exists('sanitize_textarea_field') ? sanitize_textarea_field($value) : trim(strip_tags((string) $value));
            if (function_exists('mb_substr')) {
                return mb_substr($value, 0, $limit);
            }
            return substr($value, 0, $limit);
        };
        $caja = array(
            'titulo' => $clean(isset($posted_caja['title']) ? $posted_caja['title'] : '', 160),
            'kicker' => $clean(isset($posted_caja['kicker']) ? $posted_caja['kicker'] : '', 80),
            'texto' => $clean(isset($posted_caja['texto']) ? $posted_caja['texto'] : '', 240),
            'x' => isset($posted_caja['x']) ? $posted_caja['x'] : '',
            'y' => isset($posted_caja['y']) ? $posted_caja['y'] : '',
            'size' => isset($posted_caja['size']) ? $posted_caja['size'] : '',
            'small' => isset($posted_caja['small']) ? $posted_caja['small'] : '',
        );
        $banda_ajuste = array(
            'vel' => isset($_POST['banda_vel']) ? wp_unslash($_POST['banda_vel']) : '',
            'fondo' => isset($_POST['banda_fondo']) ? wp_unslash($_POST['banda_fondo']) : '',
            'color' => isset($_POST['banda_color']) ? wp_unslash($_POST['banda_color']) : '',
            'color_b' => isset($_POST['banda_color_b']) ? wp_unslash($_POST['banda_color_b']) : '',
            'rumbo' => isset($_POST['banda_rumbo']) ? wp_unslash($_POST['banda_rumbo']) : '',
            'tinta' => isset($_POST['banda_tinta']) ? wp_unslash($_POST['banda_tinta']) : '',
        );
        $posted = isset($_POST['prendas']) && is_array($_POST['prendas']) ? wp_unslash($_POST['prendas']) : array();
        $id = isset($_POST['coleccion_id']) ? absint($_POST['coleccion_id']) : 0;
        $parent = nj_coleccion_padre_listo();
        if (!$parent || !isset($parent->term_id)) {
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=coleccion-off#colecciones'));
            exit;
        }
        if ($nombre === '') {
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=coleccion-falta#colecciones'));
            exit;
        }
        if ($id > 0) {
            $term = function_exists('get_term') ? get_term($id, 'product_cat') : null;
            if (!$term || is_wp_error($term) || !isset($term->taxonomy) || $term->taxonomy !== 'product_cat') {
                wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=coleccion-falta#colecciones'));
                exit;
            }
            if ($term->name !== $nombre && function_exists('wp_update_term')) {
                $updated = wp_update_term($id, 'product_cat', array('name' => $nombre));
                if (is_wp_error($updated)) {
                    wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=coleccion-existe#colecciones'));
                    exit;
                }
            }
        } else {
            $made = wp_insert_term($nombre, 'product_cat', array('parent' => (int) $parent->term_id));
            if (is_wp_error($made) || !is_array($made) || empty($made['term_id'])) {
                wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=coleccion-existe#colecciones'));
                exit;
            }
            $id = (int) $made['term_id'];
        }
        nj_coleccion_guardar_pieza($id, $banda, $modo, $html, $image_id, $caja, $banda_ajuste);
        nj_coleccion_montar_prendas($id, $posted);
        wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=coleccion#colecciones'));
        exit;
    }
}
if (function_exists('add_action')) {
    add_action('admin_post_nj_coleccion', 'nj_coleccion_save');
}

require_once __DIR__ . '/admin/colecciones.php';
