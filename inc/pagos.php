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
            if ($value === '' && in_array($option, $secrets, true)) {
                continue;
            }
            $settings[$option] = $value;
        }
        update_option('woocommerce_wompi_settings', $settings);
    }
}
