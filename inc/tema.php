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
        return $settings;
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
        if (!$rules) {
            return;
        }
        echo '<style id="nj-tema">:root{' . implode(';', $rules) . '}</style>';
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
        wp_enqueue_style('nj-tienda', $uri . '/assets/mitienda.css', array('nj-hero-admin'), '1.0.2');
        wp_enqueue_script('nj-hero-admin', $uri . '/assets/hero-admin.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('nj-tienda', $uri . '/assets/mitienda.js', array('jquery', 'nj-hero-admin'), '1.0.2', true);
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
        if (!empty($_POST['nj_tema_reset'])) {
            delete_option('nj_tema');
            wp_safe_redirect(admin_url('admin.php?page=nj-tema&updated=tema'));
            exit;
        }
        $defaults = nj_tema_defaults();
        $logo = isset($_POST['logo']) ? absint(wp_unslash($_POST['logo'])) : 0;
        if ($logo && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($logo)) {
            $logo = 0;
        }
        $saved = array('logo' => $logo);
        foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
            $color = isset($_POST[$key]) ? sanitize_hex_color(wp_unslash($_POST[$key])) : '';
            $saved[$key] = $color ? $color : $defaults[$key];
        }
        update_option('nj_tema', $saved);
        wp_safe_redirect(admin_url('admin.php?page=nj-tema&updated=tema'));
        exit;
    }
}
add_action('admin_post_nj_tema_save', 'nj_tema_admin_save');

require_once __DIR__ . '/admin/tema.php';
