<?php
/**
 * Hero de la portada: diapositivas subidas o armadas en el tema.
 */

if (!function_exists('nj_hero_blank')) {
    function nj_hero_blank() {
        return array(
            'type' => 'image',
            'image' => 0,
            'src' => '',
            'alt' => '',
            'url' => '',
            'eyebrow' => '',
            'title' => '',
            'text' => '',
            'button' => '',
            'color' => '#64bfae',
        );
    }
}

if (!function_exists('nj_hero_plain')) {
    function nj_hero_plain($value) {
        if (function_exists('sanitize_text_field')) {
            return sanitize_text_field($value);
        }
        return trim(strip_tags((string) $value));
    }
}

if (!function_exists('nj_hero_note')) {
    function nj_hero_note($value) {
        if (function_exists('sanitize_textarea_field')) {
            return sanitize_textarea_field($value);
        }
        return trim(strip_tags((string) $value));
    }
}

if (!function_exists('nj_hero_color')) {
    function nj_hero_color($value) {
        $value = trim((string) $value);
        if (function_exists('sanitize_hex_color')) {
            $color = sanitize_hex_color($value);
            if ($color) {
                return $color;
            }
        }
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
            return $value;
        }
        return '#64bfae';
    }
}

if (!function_exists('nj_hero_sanitize_row')) {
    function nj_hero_sanitize_row($row) {
        $slide = nj_hero_blank();
        if (!is_array($row)) {
            return $slide;
        }
        $slide['type'] = (isset($row['type']) && $row['type'] === 'build') ? 'build' : 'image';
        $slide['image'] = isset($row['image']) ? max(0, (int) $row['image']) : 0;
        $src = isset($row['src']) ? basename((string) $row['src']) : '';
        if (preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*\.(jpe?g|png|webp)$/', $src)) {
            $file = get_template_directory() . '/assets/img/' . $src;
            if (is_file($file)) {
                $slide['src'] = $src;
            }
        }
        $slide['alt'] = nj_hero_plain(isset($row['alt']) ? $row['alt'] : '');
        $url = trim((string) (isset($row['url']) ? $row['url'] : ''));
        if (function_exists('esc_url_raw')) {
            $url = esc_url_raw($url);
        }
        $slide['url'] = $url;
        $slide['eyebrow'] = nj_hero_plain(isset($row['eyebrow']) ? $row['eyebrow'] : '');
        $slide['title'] = nj_hero_plain(isset($row['title']) ? $row['title'] : '');
        $slide['text'] = nj_hero_note(isset($row['text']) ? $row['text'] : '');
        $slide['button'] = nj_hero_plain(isset($row['button']) ? $row['button'] : '');
        $slide['color'] = nj_hero_color(isset($row['color']) ? $row['color'] : '');
        if ($slide['image']) {
            $slide['src'] = '';
        }
        return $slide;
    }
}

if (!function_exists('nj_hero_is_usable')) {
    function nj_hero_is_usable($slide) {
        $has_image = !empty($slide['image']) || !empty($slide['src']);
        if (($slide['type'] ?? 'image') === 'image') {
            return $has_image;
        }
        return $slide['title'] !== '' || $slide['text'] !== '' || $slide['eyebrow'] !== '' || $has_image;
    }
}

if (!function_exists('nj_hero_defaults')) {
    function nj_hero_defaults() {
        $rows = array();
        $data = function_exists('nj_slots_data') ? nj_slots_data() : array();
        $hero = isset($data['hero']) && is_array($data['hero']) ? $data['hero'] : array();
        foreach ($hero as $item) {
            $row = nj_hero_blank();
            $row['src'] = isset($item['src']) ? $item['src'] : '';
            $row['alt'] = isset($item['alt']) ? $item['alt'] : '';
            $rows[] = nj_hero_sanitize_row($row);
        }
        return $rows;
    }
}

if (!function_exists('nj_hero_rows')) {
    function nj_hero_rows() {
        $stored = function_exists('get_option') ? get_option('nj_hero', null) : null;
        if (!is_array($stored) || !$stored) {
            return nj_hero_defaults();
        }
        $rows = array();
        foreach ($stored as $row) {
            $rows[] = nj_hero_sanitize_row($row);
        }
        $rows = array_values(array_filter($rows, 'nj_hero_is_usable'));
        return $rows ? $rows : nj_hero_defaults();
    }
}

if (!function_exists('nj_hero_src')) {
    function nj_hero_src($slide) {
        if (!empty($slide['image']) && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url((int) $slide['image'], 'full');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        if (!empty($slide['src'])) {
            return nj_img($slide['src']);
        }
        return '';
    }
}

if (!function_exists('nj_hero_href')) {
    function nj_hero_href($slide) {
        if (!empty($slide['url'])) {
            return $slide['url'];
        }
        if (function_exists('nj_section_link')) {
            return nj_section_link('Colecciones');
        }
        return function_exists('nj_home_url') ? nj_home_url() : '/';
    }
}

if (!function_exists('nj_hero_admin_assets')) {
    function nj_hero_admin_assets($hook) {
        if ($hook !== 'toplevel_page_nj-tienda') {
            return;
        }
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        $uri = get_template_directory_uri();
        wp_enqueue_style('nj-hero-admin', $uri . '/assets/hero-admin.css', array(), '1.0.0');
        wp_enqueue_script('nj-hero-admin', $uri . '/assets/hero-admin.js', array('jquery'), '1.0.0', true);
    }
}
add_action('admin_enqueue_scripts', 'nj_hero_admin_assets');

if (!function_exists('nj_hero_admin_bar')) {
    function nj_hero_admin_bar($bar) {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return;
        }
        if (!function_exists('current_user_can') || !current_user_can('edit_theme_options')) {
            return;
        }
        $bar->add_node(array(
            'id' => 'nj-hero',
            'title' => 'Editar la portada',
            'href' => admin_url('admin.php?page=nj-tienda'),
        ));
    }
}
add_action('admin_bar_menu', 'nj_hero_admin_bar', 80);

if (!function_exists('nj_hero_admin_save')) {
    function nj_hero_admin_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            wp_die('No tienes permiso para editar el banner.');
        }
        check_admin_referer('nj_hero_save', 'nj_hero_nonce');
        if (!empty($_POST['nj_hero_reset'])) {
            delete_option('nj_hero');
            wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=banner#banner'));
            exit;
        }
        $raw = isset($_POST['nj_hero']) && is_array($_POST['nj_hero']) ? $_POST['nj_hero'] : array();
        $slides = array();
        foreach ($raw as $row) {
            if (!is_array($row)) {
                continue;
            }
            $slide = nj_hero_sanitize_row($row);
            if (nj_hero_is_usable($slide)) {
                $slides[] = $slide;
            }
        }
        $slides = array_slice($slides, 0, 6);
        if ($slides) {
            update_option('nj_hero', $slides, true);
        } else {
            delete_option('nj_hero');
        }
        wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=banner#banner'));
        exit;
    }
}
add_action('admin_post_nj_hero_save', 'nj_hero_admin_save');

require_once __DIR__ . '/admin/hero.php';
