<?php
/**
 * Franjas de regalo: el HTML, los lugares y el guardado.
 */

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
            $html = wp_kses($html, nj_regalo_tags());
        }
        $destino = function_exists('nj_section_link') ? nj_section_link('Colecciones') : '';
        if ($destino !== '' && $destino !== '#') {
            $seguro = function_exists('esc_url') ? esc_url($destino) : $destino;
            $html = str_replace(
                array('href="#"', "href='#'", '{{coleccion}}', '{{colecciones}}'),
                array('href="' . $seguro . '"', 'href="' . $seguro . '"', $seguro, $seguro),
                $html
            );
        }
        return $html;
    }
}

if (!function_exists('nj_regalo_places')) {
    function nj_regalo_places($row) {
        $places = array();
        if (is_array($row) && isset($row['categories']) && is_array($row['categories'])) {
            foreach ($row['categories'] as $id) {
                $places[] = (int) $id;
            }
        }
        if (!$places && is_array($row) && isset($row['category'])) {
            $places[] = (int) $row['category'];
        }
        $clean = array();
        foreach ($places as $id) {
            if ($id < 0 || in_array($id, $clean, true)) {
                continue;
            }
            $clean[] = $id;
        }
        return $clean ? $clean : array(0);
    }
}

if (!function_exists('nj_regalo_blank')) {
    function nj_regalo_blank() {
        return array(
            'category' => 0,
            'categories' => array(0),
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
        $banner['categories'] = nj_regalo_places($row);
        $banner['category'] = (int) $banner['categories'][0];
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
                if (in_array($id, nj_regalo_places($row), true)) {
                    $match[] = $row;
                }
            }
            if ($match) {
                return $match;
            }
        }
        $global = array();
        foreach ($rows as $row) {
            if (in_array(0, nj_regalo_places($row), true)) {
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
            $places = array();
            $posted = array();
            if (isset($row['categories']) && is_array($row['categories'])) {
                $posted = $row['categories'];
            } elseif (isset($row['category'])) {
                $posted = array($row['category']);
            }
            foreach ($posted as $place) {
                $place = absint($place);
                if ($place === 0) {
                    $places[] = 0;
                    continue;
                }
                $term = nj_category_term($place);
                if ($term && !is_wp_error($term) && $term->taxonomy === 'product_cat' && !in_array($place, $places, true)) {
                    $places[] = $place;
                }
            }
            $places = array_values(array_unique($places));
            if (!$places) {
                $places = array(0);
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
                'category' => (int) $places[0],
                'categories' => $places,
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
