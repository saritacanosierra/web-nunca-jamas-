<?php
/**
 * Cobro en pesos: sin contraentrega y sin PayPal.
 */

if (!function_exists('nj_cop_payments')) {
    function nj_cop_payments() {
        if (get_option('nj_cod_off') === '1' || !function_exists('get_option')) {
            return;
        }
        $cod = get_option('woocommerce_cod_settings', array());
        if (!is_array($cod)) {
            $cod = array();
        }
        $cod['enabled'] = 'no';
        update_option('woocommerce_cod_settings', $cod);
        update_option('nj_cod_off', '1');
    }
}
add_action('init', 'nj_cop_payments', 30);

if (!function_exists('nj_hide_paypal_for_cop')) {
    function nj_hide_paypal_for_cop($gateways) {
        unset($gateways['cod']);
        if (!function_exists('get_woocommerce_currency') || get_woocommerce_currency() !== 'COP') {
            return $gateways;
        }
        foreach ($gateways as $id => $gateway) {
            if ($id === 'paypal' || strpos($id, 'ppcp') === 0) {
                unset($gateways[$id]);
            }
        }
        return $gateways;
    }
}
add_filter('woocommerce_available_payment_gateways', 'nj_hide_paypal_for_cop');

if (!function_exists('nj_wompi_cop')) {
    function nj_wompi_cop($currencies) {
        if (!is_array($currencies) || $currencies === array()) {
            return array('COP');
        }
        return $currencies;
    }
}
add_filter('wc_wompi_supported_currencies', 'nj_wompi_cop');

if (!function_exists('nj_pagos_wompi')) {
    function nj_pagos_wompi() {
        $settings = function_exists('get_option') ? get_option('woocommerce_wompi_settings', array()) : array();
        return is_array($settings) ? $settings : array();
    }
}

if (!function_exists('nj_pagos_wompi_on')) {
    function nj_pagos_wompi_on() {
        return class_exists('WompiPortalPagos_Gateway');
    }
}

if (!function_exists('nj_pagos_wompi_filled')) {
    function nj_pagos_wompi_filled($settings, $test) {
        $keys = $test
            ? array('test_public_key', 'test_private_key', 'test_event_secret_key', 'test_integrity_key')
            : array('public_key', 'private_key', 'event_secret_key', 'integrity_key');
        foreach ($keys as $key) {
            if (empty($settings[$key])) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('nj_pagos_secreto_mascara')) {
    function nj_pagos_secreto_mascara() {
        return '••••••••••••••••';
    }
}

if (!function_exists('nj_pagos_secreto_vacio')) {
    function nj_pagos_secreto_vacio($value) {
        $value = trim((string) $value);
        if ($value === '' || $value === nj_pagos_secreto_mascara()) {
            return true;
        }
        return preg_match('/^[•]+$/u', $value) === 1;
    }
}

if (!function_exists('nj_pagos_save_wompi')) {
    function nj_pagos_save_wompi() {
        if (!nj_pagos_wompi_on() || !current_user_can('manage_woocommerce')) {
            return;
        }
        $settings = nj_pagos_wompi();
        $settings['enabled'] = empty($_POST['wompi_enabled']) ? 'no' : 'yes';
        $settings['testmode'] = empty($_POST['wompi_testmode']) ? 'no' : 'yes';
        if (empty($settings['title'])) {
            $settings['title'] = 'Nequi, tarjeta o PSE';
        }
        if (empty($settings['description'])) {
            $settings['description'] = 'Pagas en la ventana de Wompi.';
        }
        $fields = array(
            'wompi_public_key' => 'public_key',
            'wompi_private_key' => 'private_key',
            'wompi_event_key' => 'event_secret_key',
            'wompi_integrity_key' => 'integrity_key',
            'wompi_test_public_key' => 'test_public_key',
            'wompi_test_private_key' => 'test_private_key',
            'wompi_test_event_key' => 'test_event_secret_key',
            'wompi_test_integrity_key' => 'test_integrity_key',
        );
        $secrets = array('private_key', 'event_secret_key', 'integrity_key', 'test_private_key', 'test_event_secret_key', 'test_integrity_key');
        foreach ($fields as $post => $option) {
            $value = isset($_POST[$post]) ? trim(sanitize_text_field(wp_unslash($_POST[$post]))) : '';
            if (in_array($option, $secrets, true) && nj_pagos_secreto_vacio($value)) {
                continue;
            }
            $settings[$option] = $value;
        }
        update_option('woocommerce_wompi_settings', $settings);
    }
}

if (!function_exists('nj_addi_settings')) {
    function nj_addi_settings() {
        $stored = function_exists('get_option') ? get_option('nj_addi', array()) : array();
        $stored = is_array($stored) ? $stored : array();
        return array(
            'enabled' => (isset($stored['enabled']) && $stored['enabled'] === 'yes') ? 'yes' : 'no',
            'slug' => nj_addi_slug(isset($stored['slug']) ? $stored['slug'] : ''),
        );
    }
}

if (!function_exists('nj_addi_slug')) {
    function nj_addi_slug($value) {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9_-]/', '', $value);
        if (!is_string($value)) {
            return '';
        }
        if (strlen($value) > 80) {
            return substr($value, 0, 80);
        }
        return $value;
    }
}

if (!function_exists('nj_addi_ready')) {
    function nj_addi_ready() {
        $settings = nj_addi_settings();
        return $settings['enabled'] === 'yes' && $settings['slug'] !== '';
    }
}

if (!function_exists('nj_addi_plugin_on')) {
    function nj_addi_plugin_on() {
        return class_exists('WC_Addi_Gateway');
    }
}

if (!function_exists('nj_addi_plugin')) {
    function nj_addi_plugin() {
        $settings = function_exists('get_option') ? get_option('woocommerce_addi_settings', array()) : array();
        return is_array($settings) ? $settings : array();
    }
}

if (!function_exists('nj_addi_plugin_ready')) {
    function nj_addi_plugin_ready() {
        if (!nj_addi_plugin_on() || !nj_addi_ready()) {
            return false;
        }
        $plugin = nj_addi_plugin();
        return !empty($plugin['prod_client_id']) && !empty($plugin['prod_client_secret']);
    }
}

if (!function_exists('nj_addi_save')) {
    function nj_addi_save() {
        if (!current_user_can('manage_woocommerce') && !current_user_can('edit_theme_options')) {
            return;
        }
        $slug = nj_addi_slug(isset($_POST['addi_slug']) ? wp_unslash($_POST['addi_slug']) : '');
        $enabled = empty($_POST['addi_enabled']) ? 'no' : 'yes';
        update_option('nj_addi', array(
            'enabled' => $enabled,
            'slug' => $slug,
        ));
        if (!nj_addi_plugin_on() || !current_user_can('manage_woocommerce')) {
            return;
        }
        $plugin = nj_addi_plugin();
        $plugin['enabled'] = $enabled;
        $plugin['widget_slug'] = $slug;
        $plugin['widget_enabled'] = 'no';
        $plugin['testmode'] = empty($_POST['addi_testmode']) ? 'no' : 'yes';
        $client = isset($_POST['addi_client_id']) ? trim(sanitize_text_field(wp_unslash($_POST['addi_client_id']))) : '';
        if ($client !== '') {
            $plugin['prod_client_id'] = $client;
        }
        $secret = isset($_POST['addi_client_secret']) ? trim(sanitize_text_field(wp_unslash($_POST['addi_client_secret']))) : '';
        if (!nj_pagos_secreto_vacio($secret)) {
            $plugin['prod_client_secret'] = $secret;
        }
        update_option('woocommerce_addi_settings', $plugin);
    }
}

if (!function_exists('nj_addi_assets')) {
    function nj_addi_assets() {
        if (!nj_addi_ready() || (function_exists('is_admin') && is_admin())) {
            return;
        }
        wp_enqueue_script('nj-addi', 'https://s3.amazonaws.com/widgets.addi.com/bundle.min.js', array(), null, true);
    }
}
add_action('wp_enqueue_scripts', 'nj_addi_assets', 30);

if (!function_exists('nj_addi_widget')) {
    function nj_addi_widget($price) {
        $price = (int) $price;
        if (!nj_addi_ready() || $price < 50000) {
            return '';
        }
        $slug = nj_addi_settings()['slug'];
        return '<div class="ficha__addi"><addi-widget price="' . esc_attr((string) $price) . '" ally-slug="' . esc_attr($slug) . '"></addi-widget></div>';
    }
}
