<?php
/**
 * Rutas de la portada. En la vista previa no hace falta WordPress.
 */

require_once __DIR__ . '/wp-stubs.php';

if (!function_exists('nj_img')) {
    function nj_img($file) {
        if (preg_match('#^https?://#i', $file)) {
            return $file;
        }
        if (defined('NJ_PREVIEW') || !function_exists('get_template_directory_uri')) {
            return '/assets/img/' . $file;
        }
        $uri = get_template_directory_uri();
        if ($uri === '') {
            return 'assets/img/' . $file;
        }
        return $uri . '/assets/img/' . $file;
    }
}

if (!function_exists('nj_home_url')) {
    function nj_home_url() {
        if (defined('NJ_PREVIEW')) {
            return 'preview.php';
        }
        if (function_exists('home_url')) {
            $url = home_url('/');
            if ($url !== '') {
                return $url;
            }
        }
        return '/';
    }
}

if (!function_exists('nj_e')) {
    function nj_e($text) {
        echo esc_html($text);
    }
}

