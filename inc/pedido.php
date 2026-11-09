<?php
/**
 * Pedido pagado, regreso de Wompi y textos del checkout.
 */

if (!function_exists('nj_order_line_screen')) {
    function nj_order_line_screen() {
        if (function_exists('is_order_received_page') && is_order_received_page()) {
            return true;
        }
        if (function_exists('is_checkout_pay_page') && is_checkout_pay_page()) {
            return true;
        }
        return function_exists('is_view_order_page') && is_view_order_page();
    }
}

if (!function_exists('nj_order_item_thumb')) {
    function nj_order_item_thumb($name, $item) {
        if (!nj_order_line_screen() || !is_object($item) || !method_exists($item, 'get_product')) {
            return $name;
        }
        $product = $item->get_product();
        if (!$product || !method_exists($product, 'get_image')) {
            return $name;
        }
        $image = $product->get_image('woocommerce_thumbnail', array('class' => 'nj-order-thumb'));
        if ($image === '') {
            return $name;
        }
        return '<span class="nj-order-line">' . $image . '<span class="nj-order-line__name">' . $name . '</span></span>';
    }
}
add_filter('woocommerce_order_item_name', 'nj_order_item_thumb', 10, 2);

if (!function_exists('nj_received_order')) {
    function nj_received_order() {
        if (!function_exists('is_order_received_page') || !is_order_received_page() || !function_exists('wc_get_order')) {
            return false;
        }
        $order_id = absint(get_query_var('order-received'));
        if (!$order_id) {
            return false;
        }
        $order = wc_get_order($order_id);
        return $order ? $order : false;
    }
}

if (!function_exists('nj_wompi_return_approved')) {
    function nj_wompi_return_approved($order) {
        if (empty($_GET['id']) || !function_exists('wp_remote_get')) {
            return false;
        }
        $id = sanitize_text_field(wp_unslash($_GET['id']));
        if (!preg_match('/^[0-9A-Za-z_-]{8,80}$/', $id)) {
            return false;
        }
        $cache_key = 'nj_wompi_tx_' . md5($id);
        $cached = get_transient($cache_key);
        if ($cached === 'yes' || $cached === 'no') {
            return $cached === 'yes';
        }
        $settings = get_option('woocommerce_wompi_settings', array());
        $test = is_array($settings) && isset($settings['testmode']) && $settings['testmode'] === 'yes';
        $url = ($test ? 'https://sandbox.wompi.co/v1/transactions/' : 'https://production.wompi.co/v1/transactions/') . rawurlencode($id);
        $response = wp_remote_get($url, array('timeout' => 8));
        $approved = false;
        if (!is_wp_error($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $data = is_array($body) && isset($body['data']) && is_array($body['data']) ? $body['data'] : array();
            $status = isset($data['status']) ? (string) $data['status'] : '';
            $reference = isset($data['reference']) ? (string) $data['reference'] : '';
            $approved = $status === 'APPROVED' && ($reference === (string) $order->get_id() || $reference === (string) $order->get_order_number());
        }
        set_transient($cache_key, $approved ? 'yes' : 'no', 10 * MINUTE_IN_SECONDS);
        return $approved;
    }
}

if (!function_exists('nj_order_is_dispatch')) {
    function nj_order_is_dispatch($order) {
        static $cache = array();
        if (!$order || !is_object($order) || !method_exists($order, 'get_id')) {
            return false;
        }
        $id = (int) $order->get_id();
        if (array_key_exists($id, $cache)) {
            return $cache[$id];
        }
        if ($order->has_status(array('cancelled', 'failed', 'refunded', 'checkout-draft'))) {
            $cache[$id] = false;
            return false;
        }
        $paid = (method_exists($order, 'is_paid') && $order->is_paid()) || $order->has_status(array('processing', 'completed', 'empacando'));
        if (!$paid && method_exists($order, 'needs_payment') && $order->needs_payment()) {
            $paid = nj_wompi_return_approved($order);
        }
        $cache[$id] = $paid;
        return $paid;
    }
}

if (!function_exists('nj_despacho_body_class')) {
    function nj_despacho_body_class($classes) {
        $order = nj_received_order();
        if ($order && nj_order_is_dispatch($order)) {
            $classes[] = 'nj-despacho';
        }
        return $classes;
    }
}
add_filter('body_class', 'nj_despacho_body_class');

if (!function_exists('nj_despacho_thankyou_text')) {
    function nj_despacho_thankyou_text($text, $order = false) {
        if ($order && nj_order_is_dispatch($order)) {
            $number = $order->get_order_number();
            $embroider = function_exists('nj_personalizado_order_has') && nj_personalizado_order_has($order);
            if ($embroider) {
                return 'Qué buena compra. El pedido ' . $number . ' ya es tuyo.';
            }
            return 'Qué buena compra. El pedido ' . $number . ' ya va hacia ti.';
        }
        return $text;
    }
}
add_filter('woocommerce_thankyou_order_received_text', 'nj_despacho_thankyou_text', 20, 2);

if (!function_exists('nj_hide_paid_invoice')) {
    function nj_hide_paid_invoice($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if ($order && nj_order_is_dispatch($order)) {
            remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
            $method = $order->get_payment_method();
            if ($method && function_exists('WC') && WC()->payment_gateways()) {
                $gateways = WC()->payment_gateways()->payment_gateways();
                if (isset($gateways[$method]) && is_object($gateways[$method])) {
                    remove_action('woocommerce_thankyou_' . $method, array($gateways[$method], 'thankyou_page'));
                }
            }
        }
    }
}
add_action('woocommerce_before_thankyou', 'nj_hide_paid_invoice', 1);

if (!function_exists('nj_order_received_note')) {
    function nj_order_received_note($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        if (nj_order_is_dispatch($order)) {
            $embroider = function_exists('nj_personalizado_order_has') && nj_personalizado_order_has($order);
            echo '<div class="nj-thanks">';
            echo '<ol class="nj-thanks__steps">';
            echo '<li class="is-done"><span>1</span><strong>Pago listo</strong><em>Ya es tuyo</em></li>';
            if ($embroider) {
                echo '<li class="is-now"><span>2</span><strong>Bordamos el nombre</strong><em>8 días hábiles</em></li>';
                echo '<li><span>3</span><strong>Sale a tu casa</strong><em>Después de esa fecha</em></li>';
            } else {
                echo '<li class="is-now"><span>2</span><strong>Lo empacamos</strong><em>Con cuidado</em></li>';
                echo '<li><span>3</span><strong>Va hacia ti</strong><em>Te avisamos al salir</em></li>';
            }
            echo '</ol>';
            if ($embroider) {
                echo '<p class="nj-thanks__lead">El pago quedó confirmado. Primero bordamos el nombre y, cuando esté listo, sale hacia tu casa. Te escribimos al correo en cada paso.</p>';
            } else {
                echo '<p class="nj-thanks__lead">El pago quedó confirmado. Lo empacamos y te escribimos al correo en cuanto salga. Ya falta poco para que llegue a tu casa.</p>';
            }
            echo '<a class="nj-order-dispatch__home" href="' . esc_url(nj_shop_link()) . '">Seguir viendo la tienda</a>';
            echo '</div>';
            return;
        }
        if (!method_exists($order, 'needs_payment') || !$order->needs_payment()) {
            return;
        }
        echo '<p class="nj-order-pending">El pedido quedó registrado. Pulsa Pagar para completar el cobro en Wompi.</p>';
    }
}
add_action('woocommerce_thankyou', 'nj_order_received_note', 5);

if (!function_exists('nj_pay_order_button_text')) {
    function nj_pay_order_button_text() {
        return 'Pagar con Wompi';
    }
}
add_filter('woocommerce_pay_order_button_text', 'nj_pay_order_button_text');

if (!function_exists('nj_wompi_description')) {
    function nj_wompi_description($description, $gateway_id) {
        if ($gateway_id !== 'wompi') {
            return $description;
        }
        $plain = trim(wp_strip_all_tags((string) $description));
        if ($plain === '' || $plain === 'Pay via Wompi gateway.' || $plain === 'Pay via Wompi gateway') {
            return 'Pagas en la ventana de Wompi con Nequi, tarjeta o PSE.';
        }
        return $description;
    }
}
add_filter('woocommerce_gateway_description', 'nj_wompi_description', 10, 2);

if (!function_exists('nj_checkout_terms_text')) {
    function nj_checkout_terms_text($text) {
        if (strpos((string) $text, 'I have read and agree') !== false) {
            return 'He leído y acepto los [terms] de la tienda';
        }
        return $text;
    }
}
add_filter('woocommerce_get_terms_and_conditions_checkbox_text', 'nj_checkout_terms_text');

if (!function_exists('nj_checkout_privacy_text')) {
    function nj_checkout_privacy_text($text) {
        if (strpos((string) $text, 'Your personal data will be used') !== false) {
            return 'Tus datos se usan para procesar el pedido, como se explica en la [privacy_policy].';
        }
        return $text;
    }
}
add_filter('woocommerce_get_privacy_policy_text', 'nj_checkout_privacy_text');
