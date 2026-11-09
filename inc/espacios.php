<?php
/**
 * Fotos de la portada que se cambian desde Mi tienda.
 */

if (!function_exists('nj_espacios_defaults')) {
    function nj_espacios_defaults($group) {
        $data = function_exists('nj_slots_data') ? nj_slots_data() : array();
        if ($group === 'promo') {
            return isset($data['promo']) && is_array($data['promo']) ? $data['promo'] : array();
        }
        return isset($data[$group]) && is_array($data[$group]) ? $data[$group] : array();
    }
}

if (!function_exists('nj_espacios_saved')) {
    function nj_espacios_saved() {
        if (!function_exists('get_option')) {
            return array();
        }
        $saved = get_option('nj_espacios', null);
        return is_array($saved) ? $saved : array();
    }
}

if (!function_exists('nj_espacio_url')) {
    function nj_espacio_url($image_id, $file) {
        $image_id = (int) $image_id;
        if ($image_id > 0 && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($image_id, 'full');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        $file = (string) $file;
        if ($file === '') {
            return '';
        }
        return function_exists('nj_img') ? nj_img($file) : $file;
    }
}

if (!function_exists('nj_espacios_merge_row')) {
    function nj_espacios_merge_row($base, $over) {
        $row = is_array($base) ? $base : array();
        if (!is_array($over)) {
            return $row;
        }
        if (!empty($over['image'])) {
            $row['image'] = (int) $over['image'];
        }
        if (!empty($over['term'])) {
            $row['term'] = (int) $over['term'];
        }
        if (!empty($over['src'])) {
            $file = nj_espacios_file($over['src']);
            if ($file !== '') {
                $row['src'] = $file;
            }
        }
        foreach (array('alt', 'kicker', 'title', 'label', 'button', 'note', 'html') as $key) {
            if (isset($over[$key]) && trim((string) $over[$key]) !== '') {
                $row[$key] = (string) $over[$key];
            }
        }
        if (array_key_exists('points', $over) && is_array($over['points'])) {
            $row['points'] = nj_espacios_clean_points($over['points']);
        }
        return $row;
    }
}

if (!function_exists('nj_espacios_rows')) {
    function nj_espacios_rows($group) {
        $defaults = nj_espacios_defaults($group);
        $saved = nj_espacios_saved();
        $over = isset($saved[$group]) && is_array($saved[$group]) ? $saved[$group] : array();
        $rows = array();
        if ($group === 'promo' && array_key_exists('promo', $saved)) {
            foreach ($saved['promo'] as $key => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $kind = isset($row['kind']) ? (string) $row['kind'] : (string) $key;
                $base = isset($defaults[$kind]) && is_array($defaults[$kind]) ? $defaults[$kind] : array();
                $item = nj_espacios_merge_row($base, $row);
                $item['kind'] = $kind;
                if (empty($row['image']) && empty($row['src'])) {
                    $item['src'] = '';
                    $item['image'] = 0;
                }
                $rows[] = $item;
            }
            return $rows;
        }
        if ($group === 'promo') {
            foreach ($defaults as $key => $base) {
                if (!is_array($base)) {
                    continue;
                }
                $base['kind'] = (string) $key;
                $rows[] = $base;
            }
            return $rows;
        }
        if ($group === 'looks' && array_key_exists('looks', $saved) && is_array($saved['looks'])) {
            foreach ($saved['looks'] as $index => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $base = isset($defaults[$index]) && is_array($defaults[$index]) ? $defaults[$index] : array();
                $rows[] = nj_espacios_merge_row($base, $row);
            }
            return $rows;
        }
        if ($group === 'cuentos' && array_key_exists('cuentos', $saved) && is_array($saved['cuentos'])) {
            foreach ($saved['cuentos'] as $index => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $base = isset($defaults[$index]) && is_array($defaults[$index]) ? $defaults[$index] : array();
                $rows[] = nj_espacios_merge_row($base, $row);
            }
            return $rows;
        }
        if ($group === 'styles' && nj_espacios_styles_custom($over)) {
            foreach ($over as $row) {
                if (!is_array($row) || trim((string) (isset($row['label']) ? $row['label'] : '')) === '') {
                    continue;
                }
                $rows[] = nj_espacios_merge_row(array(
                    'src' => isset($row['src']) ? $row['src'] : '',
                    'label' => $row['label'],
                ), $row);
            }
            return $rows;
        }
        foreach ($defaults as $index => $base) {
            $rows[] = nj_espacios_merge_row($base, isset($over[$index]) ? $over[$index] : array());
        }
        return $rows;
    }
}

if (!function_exists('nj_espacios_promo')) {
    function nj_espacios_promo($key) {
        foreach (nj_espacios_promos() as $row) {
            if (isset($row['kind']) && (string) $row['kind'] === (string) $key) {
                return $row;
            }
        }
        return array('src' => '', 'alt' => '', 'kind' => (string) $key);
    }
}

if (!function_exists('nj_espacios_promos')) {
    function nj_espacios_promos() {
        $items = array();
        foreach (nj_espacios_rows('promo') as $key => $row) {
            $kind = isset($row['kind']) ? (string) $row['kind'] : (string) $key;
            $row['kind'] = $kind;
            $row['file'] = isset($row['src']) ? $row['src'] : '';
            $row['src'] = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, $row['file']);
            $html = isset($row['html']) ? trim((string) $row['html']) : '';
            if ($row['src'] === '' && $html === '') {
                continue;
            }
            $items[] = $row;
        }
        return $items;
    }
}

if (!function_exists('nj_espacios_cuentos')) {
    function nj_espacios_cuentos() {
        $items = array();
        foreach (nj_espacios_rows('cuentos') as $row) {
            $items[] = array(
                'image' => nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : ''),
                'alt' => isset($row['alt']) ? $row['alt'] : '',
                'kicker' => isset($row['kicker']) ? $row['kicker'] : '',
                'title' => isset($row['title']) ? $row['title'] : '',
            );
        }
        return $items;
    }
}

if (!function_exists('nj_espacios_styles')) {
    function nj_espacios_styles() {
        $items = array();
        foreach (nj_espacios_rows('styles') as $row) {
            $label = isset($row['label']) ? $row['label'] : '';
            $items[] = array(
                'label' => $label,
                'href' => function_exists('nj_section_link') ? nj_section_link($label) : '',
                'image' => nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : ''),
            );
        }
        return $items;
    }
}

if (!function_exists('nj_espacios_looks')) {
    function nj_espacios_looks() {
        $items = array();
        foreach (nj_espacios_rows('looks') as $row) {
            $row['src'] = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : '');
            if (!isset($row['points']) || !is_array($row['points'])) {
                $row['points'] = array();
            }
            $items[] = $row;
        }
        return $items;
    }
}

if (!function_exists('nj_espacios_clean_image')) {
    function nj_espacios_clean_image($id) {
        $id = (int) $id;
        if ($id < 1) {
            return 0;
        }
        if (function_exists('wp_attachment_is_image') && !wp_attachment_is_image($id)) {
            return 0;
        }
        return $id;
    }
}

if (!function_exists('nj_espacios_percent')) {
    function nj_espacios_percent($value) {
        $value = trim((string) $value);
        if (!preg_match('/^(\d{1,3}(?:\.\d+)?)%$/', $value, $match)) {
            return '';
        }
        $number = (float) $match[1];
        if ($number < 0 || $number > 100) {
            return '';
        }
        return rtrim(rtrim(number_format($number, 1, '.', ''), '0'), '.') . '%';
    }
}

if (!function_exists('nj_espacios_clean_points')) {
    function nj_espacios_clean_points($points) {
        $clean = array();
        if (!is_array($points)) {
            return $clean;
        }
        foreach ($points as $point) {
            if (!is_array($point)) {
                continue;
            }
            $top = nj_espacios_percent(isset($point['top']) ? $point['top'] : '');
            $left = nj_espacios_percent(isset($point['left']) ? $point['left'] : '');
            if ($top === '' || $left === '') {
                continue;
            }
            $href = isset($point['href']) ? trim((string) $point['href']) : '';
            if (function_exists('esc_url_raw')) {
                $href = esc_url_raw($href);
            }
            $clean[] = array(
                'top' => $top,
                'left' => $left,
                'label' => nj_espacios_text(isset($point['label']) ? $point['label'] : ''),
                'price' => nj_espacios_text(isset($point['price']) ? $point['price'] : ''),
                'href' => $href,
            );
            if (count($clean) >= 4) {
                break;
            }
        }
        return $clean;
    }
}

if (!function_exists('nj_espacios_is_list')) {
    function nj_espacios_is_list($rows) {
        if (!is_array($rows) || $rows === array()) {
            return false;
        }
        return array_keys($rows) === range(0, count($rows) - 1);
    }
}

if (!function_exists('nj_espacios_file')) {
    function nj_espacios_file($file) {
        $file = basename((string) $file);
        if (preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.(jpe?g|png|webp)$/', $file)) {
            return $file;
        }
        return '';
    }
}

if (!function_exists('nj_espacios_styles_custom')) {
    function nj_espacios_styles_custom($rows) {
        if (!is_array($rows)) {
            return false;
        }
        foreach ($rows as $row) {
            if (is_array($row) && trim((string) (isset($row['label']) ? $row['label'] : '')) !== '') {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('nj_espacios_category_id')) {
    function nj_espacios_category_id($label, $term_id) {
        $term_id = (int) $term_id;
        $term = null;
        if ($term_id > 0 && function_exists('get_term')) {
            $term = get_term($term_id, 'product_cat');
        }
        if (is_object($term) && !is_wp_error($term) && isset($term->term_id)) {
            return (int) $term->term_id;
        }
        if (!function_exists('nj_category_index') || !function_exists('nj_menu_key')) {
            return 0;
        }
        $index = nj_category_index();
        $key = nj_menu_key($label);
        if ($key !== '' && isset($index[$key])) {
            return (int) $index[$key]->term_id;
        }
        return 0;
    }
}

if (!function_exists('nj_espacios_category_keep')) {
    function nj_espacios_category_keep($label, $term_id) {
        if (!function_exists('taxonomy_exists') || !taxonomy_exists('product_cat')) {
            return 0;
        }
        $found = nj_espacios_category_id($label, $term_id);
        if ($found && function_exists('wp_update_term')) {
            wp_update_term($found, 'product_cat', array('name' => $label));
            return $found;
        }
        if (!function_exists('wp_insert_term')) {
            return 0;
        }
        $created = wp_insert_term($label, 'product_cat');
        if ($created instanceof WP_Error) {
            $data = $created->get_error_data();
            return is_numeric($data) ? (int) $data : (is_array($data) && isset($data['term_exists']) ? (int) $data['term_exists'] : 0);
        }
        return is_array($created) && isset($created['term_id']) ? (int) $created['term_id'] : 0;
    }
}

if (!function_exists('nj_espacios_text')) {
    function nj_espacios_text($value) {
        if (function_exists('sanitize_text_field')) {
            return sanitize_text_field($value);
        }
        return trim(strip_tags((string) $value));
    }
}

if (!function_exists('nj_espacios_admin_save')) {
    function nj_espacios_admin_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            wp_die('No tienes permiso para cambiar estas fotos.');
        }
        check_admin_referer('nj_espacios_save', 'nj_espacios_nonce');
        $group = isset($_POST['grupo']) ? sanitize_key(wp_unslash($_POST['grupo'])) : '';
        $map = array(
            'cuentos' => 'cuentos',
            'promo' => 'campanas',
            'styles' => 'categorias',
            'pills' => 'pills',
            'looks' => 'looks',
        );
        if (!isset($map[$group])) {
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda'));
            exit;
        }
        $raw = isset($_POST['espacios']) && is_array($_POST['espacios']) ? wp_unslash($_POST['espacios']) : array();
        $previous_terms = array();
        if ($group === 'styles') {
            foreach (nj_espacios_rows('styles') as $row) {
                $found = nj_espacios_category_id(isset($row['label']) ? $row['label'] : '', isset($row['term']) ? $row['term'] : 0);
                if ($found) {
                    $previous_terms[] = $found;
                }
            }
        }
        $clean = array();
        foreach ($raw as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            $item = array(
                'image' => nj_espacios_clean_image(isset($row['image']) ? $row['image'] : 0),
            );
            if ($group === 'cuentos') {
                $item['kicker'] = nj_espacios_text(isset($row['kicker']) ? $row['kicker'] : '');
                $item['title'] = function_exists('sanitize_textarea_field') ? sanitize_textarea_field(isset($row['title']) ? $row['title'] : '') : nj_espacios_text(isset($row['title']) ? $row['title'] : '');
                $item['src'] = nj_espacios_file(isset($row['src']) ? $row['src'] : '');
                $item['alt'] = nj_espacios_text(isset($row['alt']) ? $row['alt'] : '');
                if ($item['image'] < 1 && $item['src'] === '' && $item['kicker'] === '' && $item['title'] === '') {
                    continue;
                }
                if (count($clean) >= 8) {
                    continue;
                }
            }
            if ($group === 'looks') {
                $item['points'] = nj_espacios_clean_points(isset($row['points']) ? $row['points'] : array());
                $item['src'] = nj_espacios_file(isset($row['src']) ? $row['src'] : '');
                $item['alt'] = nj_espacios_text(isset($row['alt']) ? $row['alt'] : '');
                if ($item['image'] < 1 && $item['src'] === '') {
                    continue;
                }
                if (count($clean) >= 8) {
                    continue;
                }
            }
            if ($group === 'promo') {
                $kind = isset($row['kind']) ? (string) $row['kind'] : 'photo';
                $item['kind'] = in_array($kind, array('gift', 'collect', 'three_d', 'photo'), true) ? $kind : 'photo';
                $item['src'] = nj_espacios_file(isset($row['src']) ? $row['src'] : '');
                $item['alt'] = nj_espacios_text(isset($row['alt']) ? $row['alt'] : '');
                $item['label'] = nj_espacios_text(isset($row['label']) ? $row['label'] : '');
                if (in_array($item['kind'], array('gift', 'collect', 'three_d'), true)) {
                    $title = isset($row['title']) ? $row['title'] : '';
                    $note = isset($row['note']) ? $row['note'] : '';
                    $item['kicker'] = nj_espacios_text(isset($row['kicker']) ? $row['kicker'] : '');
                    $item['title'] = function_exists('sanitize_textarea_field') ? sanitize_textarea_field($title) : nj_espacios_text($title);
                    $item['button'] = nj_espacios_text(isset($row['button']) ? $row['button'] : '');
                    $item['note'] = function_exists('sanitize_textarea_field') ? sanitize_textarea_field($note) : nj_espacios_text($note);
                }
                $html = isset($row['html']) ? (string) $row['html'] : '';
                if (strlen($html) > 12000) {
                    $html = substr($html, 0, 12000);
                }
                $item['html'] = function_exists('nj_regalo_html') ? nj_regalo_html($html) : '';
                if ($item['image'] < 1 && $item['src'] === '' && $item['html'] === '') {
                    continue;
                }
                if (count($clean) >= 6) {
                    continue;
                }
            }
            if ($group === 'pills') {
                $label = nj_espacios_text(isset($row['label']) ? $row['label'] : '');
                if ($label === '' || count($clean) >= 24) {
                    continue;
                }
                $term = nj_espacios_category_keep($label, isset($row['term']) ? $row['term'] : 0);
                if ($term < 1) {
                    continue;
                }
                $clean[] = array(
                    'label' => $label,
                    'term' => $term,
                );
                continue;
            }
            if ($group === 'styles') {
                $item['label'] = nj_espacios_text(isset($row['label']) ? $row['label'] : '');
                $item['src'] = nj_espacios_file(isset($row['src']) ? $row['src'] : '');
                if ($item['label'] === '') {
                    continue;
                }
                if (count($clean) >= 12) {
                    continue;
                }
                $item['term'] = nj_espacios_category_keep($item['label'], isset($row['term']) ? $row['term'] : 0);
            }
            $clean[$key] = $item;
        }
        if ($group === 'styles' && function_exists('wp_delete_term')) {
            $kept = array();
            foreach ($clean as $item) {
                if (!empty($item['term'])) {
                    $kept[] = (int) $item['term'];
                }
            }
            foreach ($previous_terms as $term_id) {
                if (!in_array((int) $term_id, $kept, true)) {
                    wp_delete_term((int) $term_id, 'product_cat');
                }
            }
            $clean = array_values($clean);
        }
        if ($group === 'cuentos' || $group === 'looks' || $group === 'promo' || $group === 'pills') {
            $clean = array_values($clean);
        }
        $saved = nj_espacios_saved();
        $saved[$group] = $clean;
        update_option('nj_espacios', $saved, true);
        nj_espacios_purge();
        wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=' . $map[$group] . '#' . $map[$group]));
        exit;
    }
}

if (!function_exists('nj_espacios_purge')) {
    function nj_espacios_purge() {
        $urls = array();
        if (function_exists('home_url')) {
            $urls[] = home_url('/');
            $urls[] = home_url('');
        }
        if (function_exists('get_option') && function_exists('get_permalink')) {
            $front = (int) get_option('page_on_front');
            if ($front > 0) {
                $link = get_permalink($front);
                if (is_string($link) && $link !== '') {
                    $urls[] = $link;
                }
            }
        }
        $urls = array_values(array_unique(array_filter($urls)));
        if (class_exists('WPStackCache')) {
            $purge = array('WPStackCache', 'purge');
            if (is_callable($purge)) {
                foreach ($urls as $url) {
                    call_user_func($purge, $url);
                }
            }
        }
        if (function_exists('rocket_clean_home')) {
            rocket_clean_home();
        }
        if (function_exists('litespeed_purge_url')) {
            foreach ($urls as $url) {
                litespeed_purge_url($url);
            }
        }
    }
}
add_action('admin_post_nj_espacios_save', 'nj_espacios_admin_save');

require_once __DIR__ . '/admin/espacios.php';
