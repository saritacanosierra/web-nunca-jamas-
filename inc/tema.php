<?php
/**
 * Logo y colores del tema. La pantalla está en inc/admin/tema.php.
 */

if (!function_exists('nj_tema_defaults')) {
    function nj_tema_defaults() {
        return array(
            'logo' => 0,
            'coral' => '#EAA799',
            'teal' => '#64bfae',
            'gold' => '#f1c36b',
            'ink' => '#000000',
            'bg' => '#ffffff',
            'css' => '',
        );
    }
}

if (!function_exists('nj_tema_settings')) {
    function nj_tema_settings() {
        $defaults = nj_tema_defaults();
        $saved = function_exists('get_option') ? get_option('nj_tema', array()) : array();
        if (!is_array($saved)) {
            $saved = array();
        }
        $settings = $defaults;
        $settings['logo'] = isset($saved['logo']) ? absint($saved['logo']) : 0;
        foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
            $color = isset($saved[$key]) ? sanitize_hex_color($saved[$key]) : '';
            if ($color) {
                $settings[$key] = $color;
            }
        }
        $settings['css'] = isset($saved['css']) ? nj_tema_css_clean($saved['css']) : '';
        return $settings;
    }
}

if (!function_exists('nj_tema_css_clean')) {
    function nj_tema_css_clean($css) {
        $css = str_replace(array('<', '>'), '', (string) $css);
        return substr(trim($css), 0, 5000);
    }
}

if (!function_exists('nj_tema_link')) {
    function nj_tema_link($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        return function_exists('esc_url_raw') ? esc_url_raw($value) : $value;
    }
}

if (!function_exists('nj_tema_menu_item')) {
    function nj_tema_menu_item($row, $with_children) {
        if (!is_array($row)) {
            return null;
        }
        $label = function_exists('sanitize_text_field') ? sanitize_text_field(isset($row['label']) ? $row['label'] : '') : trim(strip_tags((string) (isset($row['label']) ? $row['label'] : '')));
        if ($label === '') {
            return null;
        }
        $href = nj_tema_link(isset($row['href']) ? $row['href'] : '');
        if ($href === '' && function_exists('nj_section_link')) {
            $href = nj_section_link($label);
        }
        $item = array(
            'label' => $label,
            'href' => $href,
        );
        if (!$with_children) {
            return $item;
        }
        $item['children'] = array();
        $children = isset($row['children']) && is_array($row['children']) ? $row['children'] : array();
        foreach ($children as $child) {
            $clean = nj_tema_menu_item($child, false);
            if ($clean) {
                $item['children'][] = $clean;
            }
            if (count($item['children']) >= 8) {
                break;
            }
        }
        return $item;
    }
}

if (!function_exists('nj_tema_menu_ready')) {
    function nj_tema_menu_ready($saved) {
        if (!is_array($saved)) {
            return null;
        }
        $departments = array();
        $raw = isset($saved['departments']) && is_array($saved['departments']) ? $saved['departments'] : array();
        foreach ($raw as $row) {
            $item = nj_tema_menu_item($row, true);
            if ($item) {
                $departments[] = $item;
            }
            if (count($departments) >= 6) {
                break;
            }
        }
        if (!$departments) {
            return null;
        }
        $collections = nj_tema_menu_item(isset($saved['collections']) ? $saved['collections'] : array(), true);
        if (!$collections) {
            $collections = array('label' => 'Colecciones', 'href' => function_exists('nj_section_link') ? nj_section_link('Colecciones') : '', 'children' => array());
        }
        $newest = nj_tema_menu_item(isset($saved['newest']) ? $saved['newest'] : array(), false);
        if (!$newest) {
            $newest = array(
                'label' => 'Nuevo',
                'href' => function_exists('nj_shop_link') ? nj_shop_link('newest') : '',
            );
        }
        return array(
            'departments' => $departments,
            'collections' => $collections,
            'newest' => $newest,
        );
    }
}

if (!function_exists('nj_tema_menu_live')) {
    function nj_tema_menu_live() {
        if (!function_exists('get_option')) {
            return null;
        }
        $saved = get_option('nj_menu', null);
        if (!is_array($saved)) {
            return null;
        }
        return nj_tema_menu_ready($saved);
    }
}

if (!function_exists('nj_tema_logo_url')) {
    function nj_tema_logo_url() {
        $settings = nj_tema_settings();
        if ($settings['logo'] && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($settings['logo'], 'full');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        return nj_img('logo.png');
    }
}

if (!function_exists('nj_tema_css')) {
    function nj_tema_css() {
        $settings = nj_tema_settings();
        $defaults = nj_tema_defaults();
        $vars = array(
            'coral' => '--coral',
            'teal' => '--teal',
            'gold' => '--gold',
            'ink' => '--ink',
            'bg' => '--bg',
        );
        $rules = array();
        foreach ($vars as $key => $name) {
            if (strcasecmp($settings[$key], $defaults[$key]) !== 0) {
                $rules[] = $name . ':' . $settings[$key];
            }
        }
        if ($rules) {
            echo '<style id="nj-tema">:root{' . implode(';', $rules) . '}</style>';
        }
        if ($settings['css'] !== '') {
            echo '<style id="nj-tema-extra">' . $settings['css'] . '</style>';
        }
    }
}
add_action('wp_head', 'nj_tema_css', 20);

if (!function_exists('nj_tema_admin_menu')) {
    function nj_tema_admin_menu() {
        if (!function_exists('add_menu_page')) {
            return;
        }
        add_menu_page(
            'Editar el tema',
            'Editar el tema',
            'edit_theme_options',
            'nj-tema',
            'nj_tema_admin_page',
            'dashicons-admin-customizer',
            3.2
        );
    }
}
add_action('admin_menu', 'nj_tema_admin_menu');

if (!function_exists('nj_tema_admin_assets')) {
    function nj_tema_admin_assets($hook) {
        if ($hook !== 'toplevel_page_nj-tema') {
            return;
        }
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        $uri = get_template_directory_uri();
        wp_enqueue_style('nj-hero-admin', $uri . '/assets/hero-admin.css', array(), '1.0.0');
        wp_enqueue_style('nj-tienda', $uri . '/assets/mitienda.css', array('nj-hero-admin'), '1.0.6');
        wp_enqueue_script('nj-hero-admin', $uri . '/assets/hero-admin.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('nj-tienda', $uri . '/assets/mitienda.js', array('jquery', 'nj-hero-admin'), '1.0.6', true);
    }
}
add_action('admin_enqueue_scripts', 'nj_tema_admin_assets', 20);

if (!function_exists('nj_tema_admin_save')) {
    function nj_tema_admin_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            wp_die('No tienes permiso para editar el tema.');
        }
        check_admin_referer('nj_tema_save', 'nj_tema_nonce');
        $current = nj_tema_settings();
        if (!empty($_POST['nj_tema_reset'])) {
            $defaults = nj_tema_defaults();
            $current['logo'] = 0;
            foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
                $current[$key] = $defaults[$key];
            }
            update_option('nj_tema', $current);
            wp_safe_redirect(admin_url('admin.php?page=nj-tema&updated=tema#identidad'));
            exit;
        }
        $defaults = nj_tema_defaults();
        $logo = isset($_POST['logo']) ? absint(wp_unslash($_POST['logo'])) : 0;
        $icon = isset($_POST['icon']) ? absint(wp_unslash($_POST['icon'])) : 0;
        if (function_exists('wp_attachment_is_image')) {
            if ($logo && !wp_attachment_is_image($logo)) {
                $logo = 0;
            }
            if ($icon && !wp_attachment_is_image($icon)) {
                $icon = 0;
            }
        }
        $saved = array(
            'logo' => $logo,
            'css' => nj_tema_css_clean(isset($_POST['extra_css']) ? wp_unslash($_POST['extra_css']) : ''),
        );
        foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
            $color = isset($_POST[$key]) ? sanitize_hex_color(wp_unslash($_POST[$key])) : '';
            $saved[$key] = $color ? $color : $defaults[$key];
        }
        update_option('nj_tema', $saved);
        update_option('blogname', sanitize_text_field(wp_unslash(isset($_POST['blogname']) ? $_POST['blogname'] : '')));
        update_option('blogdescription', sanitize_text_field(wp_unslash(isset($_POST['blogdescription']) ? $_POST['blogdescription'] : '')));
        update_option('site_icon', $icon);
        $menu = isset($_POST['menu']) && is_array($_POST['menu']) ? wp_unslash($_POST['menu']) : array();
        $ready = nj_tema_menu_ready($menu);
        if ($ready) {
            update_option('nj_menu', $ready);
        } else {
            delete_option('nj_menu');
        }
        $front = isset($_POST['show_on_front']) && $_POST['show_on_front'] === 'page' ? 'page' : 'posts';
        update_option('show_on_front', $front);
        update_option('page_on_front', isset($_POST['page_on_front']) ? absint(wp_unslash($_POST['page_on_front'])) : 0);
        update_option('page_for_posts', isset($_POST['page_for_posts']) ? absint(wp_unslash($_POST['page_for_posts'])) : 0);
        if (class_exists('WooCommerce')) {
            update_option('woocommerce_demo_store', empty($_POST['store_notice']) ? 'no' : 'yes');
            update_option('woocommerce_demo_store_notice', sanitize_text_field(wp_unslash(isset($_POST['store_notice_text']) ? $_POST['store_notice_text'] : '')));
            $columns = isset($_POST['catalog_columns']) ? absint(wp_unslash($_POST['catalog_columns'])) : 4;
            $rows = isset($_POST['catalog_rows']) ? absint(wp_unslash($_POST['catalog_rows'])) : 4;
            update_option('woocommerce_catalog_columns', max(2, min(4, $columns)));
            update_option('woocommerce_catalog_rows', max(1, min(8, $rows)));
            foreach (array('shop', 'cart', 'checkout', 'myaccount') as $page) {
                $id = isset($_POST['wc_page_' . $page]) ? absint(wp_unslash($_POST['wc_page_' . $page])) : 0;
                update_option('woocommerce_' . $page . '_page_id', $id);
            }
        }
        $section = isset($_POST['nj_tema_section']) ? sanitize_key(wp_unslash($_POST['nj_tema_section'])) : 'identidad';
        if (!in_array($section, array('identidad', 'menus', 'portada', 'woocommerce', 'css'), true)) {
            $section = 'identidad';
        }
        wp_safe_redirect(admin_url('admin.php?page=nj-tema&updated=tema#' . $section));
        exit;
    }
}
add_action('admin_post_nj_tema_save', 'nj_tema_admin_save');

require_once __DIR__ . '/admin/tema.php';
