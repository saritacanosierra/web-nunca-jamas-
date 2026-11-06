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
