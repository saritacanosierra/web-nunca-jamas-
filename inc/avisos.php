<?php
/**
 * Avisos del pedido pagado y el paso de empacando.
 */

if (!function_exists('nj_sanitize_whatsapp')) {
    function nj_sanitize_whatsapp($value) {
        $value = trim((string) $value);
        $value = preg_replace('/[^0-9+]/', '', $value);
        if ($value === null) {
            return '';
        }
        if (strpos($value, '+') > 0) {
            $value = str_replace('+', '', $value);
        }
        return $value;
    }
}

if (!function_exists('nj_validate_whatsapp')) {
    function nj_validate_whatsapp($value) {
        $value = nj_sanitize_whatsapp($value);
        if ($value === '') {
            return;
        }
        $digits = preg_replace('/\D+/', '', $value);
        if (strlen((string) $digits) < 7 || strlen((string) $digits) > 15) {
            return new WP_Error('nj_whatsapp', 'Escribe un WhatsApp con indicativo, por ejemplo 573001112233.');
        }
    }
}

if (!function_exists('nj_register_whatsapp_field')) {
    function nj_register_whatsapp_field() {
        if (!function_exists('woocommerce_register_additional_checkout_field')) {
            return;
        }
        woocommerce_register_additional_checkout_field(array(
            'id' => 'nj/whatsapp',
            'label' => 'WhatsApp',
            'optionalLabel' => 'WhatsApp para avisarte el envío',
            'location' => 'contact',
            'type' => 'text',
            'required' => false,
            'attributes' => array(
                'autocomplete' => 'tel',
                'title' => 'Un número de WhatsApp',
            ),
            'sanitize_callback' => 'nj_sanitize_whatsapp',
            'validate_callback' => 'nj_validate_whatsapp',
        ));
    }
}
nj_register_whatsapp_field();

if (!function_exists('nj_classic_whatsapp_field')) {
    function nj_classic_whatsapp_field($fields) {
        $fields['billing']['billing_whatsapp'] = array(
            'type' => 'tel',
            'label' => 'WhatsApp',
            'placeholder' => '573001112233',
            'required' => false,
            'priority' => 25,
            'class' => array('form-row-wide'),
        );
        return $fields;
    }
}
add_filter('woocommerce_checkout_fields', 'nj_classic_whatsapp_field');

if (!function_exists('nj_save_classic_whatsapp')) {
    function nj_save_classic_whatsapp($order_id) {
        if (empty($_POST['billing_whatsapp']) || !function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }
        $order->update_meta_data('_wc_other/nj/whatsapp', nj_sanitize_whatsapp(wp_unslash($_POST['billing_whatsapp'])));
        $order->save();
    }
}
add_action('woocommerce_checkout_update_order_meta', 'nj_save_classic_whatsapp');

if (!function_exists('nj_order_whatsapp')) {
    function nj_order_whatsapp($order) {
        $value = $order->get_meta('_wc_other/nj/whatsapp', true);
        if (!$value) {
            $value = $order->get_meta('billing_whatsapp', true);
        }
        return nj_sanitize_whatsapp($value);
    }
}

if (!function_exists('nj_register_empacando_status')) {
    function nj_register_empacando_status($statuses) {
        $next = array();
        foreach ($statuses as $key => $label) {
            $next[$key] = $label;
            if ($key === 'wc-processing') {
                $next['wc-empacando'] = 'Empacando';
            }
        }
        if (!isset($next['wc-empacando'])) {
            $next['wc-empacando'] = 'Empacando';
        }
        return $next;
    }
}
add_filter('wc_order_statuses', 'nj_register_empacando_status');

if (!function_exists('nj_empacando_is_paid')) {
    function nj_empacando_is_paid($statuses) {
        $statuses[] = 'empacando';
        return $statuses;
    }
}
add_filter('woocommerce_order_is_paid_statuses', 'nj_empacando_is_paid');

if (!function_exists('nj_register_empacando_post_status')) {
    function nj_register_empacando_post_status() {
        if (!function_exists('register_post_status')) {
            return;
        }
        register_post_status('wc-empacando', array(
            'label' => 'Empacando',
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Empacando <span class="count">(%s)</span>', 'Empacando <span class="count">(%s)</span>'),
        ));
    }
}
add_action('init', 'nj_register_empacando_post_status');

if (!function_exists('nj_empacar_token')) {
    function nj_empacar_token($order_id) {
        return hash_hmac('sha256', 'empacar|' . (int) $order_id, wp_salt('auth'));
    }
}

if (!function_exists('nj_empacar_url')) {
    function nj_empacar_url($order_id) {
        return add_query_arg(array(
            'nj_empacar' => '1',
            'pedido' => (int) $order_id,
            'token' => nj_empacar_token($order_id),
        ), home_url('/'));
    }
}

if (!function_exists('nj_mail_html_type')) {
    function nj_mail_html_type() {
        return 'text/html';
    }
}

if (!function_exists('nj_send_html_mail')) {
    function nj_send_html_mail($to, $subject, $html) {
        if (!$to || !function_exists('wp_mail')) {
            return;
        }
        add_filter('wp_mail_content_type', 'nj_mail_html_type');
        wp_mail($to, $subject, $html);
        remove_filter('wp_mail_content_type', 'nj_mail_html_type');
    }
}

if (!function_exists('nj_order_lines_html')) {
    function nj_order_lines_html($order) {
        $rows = '';
        foreach ($order->get_items() as $item) {
            $rows .= '<tr><td style="padding:8px 0;border-bottom:1px solid #f3ece8">' . esc_html($item->get_name()) . ' × ' . esc_html((string) $item->get_quantity()) . '</td><td style="padding:8px 0;border-bottom:1px solid #f3ece8;text-align:right">' . wp_kses_post($order->get_formatted_line_subtotal($item)) . '</td></tr>';
        }
        $rows .= '<tr><td style="padding:12px 0;font-weight:800">Total</td><td style="padding:12px 0;text-align:right;font-weight:800">' . wp_kses_post($order->get_formatted_order_total()) . '</td></tr>';
        return '<table style="width:100%;border-collapse:collapse">' . $rows . '</table>';
    }
}

if (!function_exists('nj_mail_card')) {
    function nj_mail_card($title, $body) {
        return '<div style="font-family:Montserrat,Arial,sans-serif;background:#faf6f3;padding:24px;color:#000"><div style="max-width:560px;margin:0 auto;background:#fff;border-radius:20px;padding:24px"><h1 style="font-size:24px;margin:0 0 12px">' . esc_html($title) . '</h1>' . $body . '</div></div>';
    }
}

if (!function_exists('nj_paid_notice')) {
    function nj_paid_notice($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order || $order->get_meta('_nj_paid_notice') === '1') {
            return;
        }
        $order->update_meta_data('_nj_paid_notice', '1');
        $order->save();

        $name = $order->get_billing_first_name();
        $hello = $name ? 'Hola ' . $name . ',' : 'Hola,';
        $whatsapp = nj_order_whatsapp($order);
        $customer = '<p>' . esc_html($hello) . '</p>';
        $customer .= '<p>Tu pago quedó confirmado. Este es el comprobante del pedido ' . esc_html((string) $order->get_order_number()) . '.</p>';
        $customer .= nj_order_lines_html($order);
        $customer .= '<p>Tu pedido pasó a preparación para despacho. Por este correo te vamos contando cuando lo estemos empacando y cuando salga el envío.</p>';
        if ($whatsapp !== '') {
            $customer .= '<p>También dejaste este WhatsApp: ' . esc_html($whatsapp) . '. El mismo comprobante sale por ahí.</p>';
        }
        nj_send_html_mail($order->get_billing_email(), 'Pago confirmado, pedido ' . $order->get_order_number(), nj_mail_card('Pago confirmado', $customer));

        $address = $order->get_formatted_shipping_address();
        if (!$address) {
            $address = $order->get_formatted_billing_address();
        }
        $store = '<p>Pedido ' . esc_html((string) $order->get_order_number()) . ' pagado. Hay que despacharlo.</p>';
        $store .= nj_order_lines_html($order);
        $store .= '<p><strong>' . esc_html(trim($order->get_formatted_billing_full_name())) . '</strong><br>' . esc_html($order->get_billing_email()) . '</p>';
        if ($whatsapp !== '') {
            $store .= '<p>WhatsApp del cliente: ' . esc_html($whatsapp) . '</p>';
        }
        $store .= '<p>' . wp_kses_post($address) . '</p>';
        $store .= '<p><a href="' . esc_url(nj_empacar_url($order->get_id())) . '" style="display:inline-block;background:#000;color:#fff;padding:14px 22px;border-radius:999px;text-decoration:none;font-weight:700">Empacando</a></p>';
        $store .= '<p>Al pulsarlo, el cliente recibe el aviso de que su pedido se está empacando.</p>';
        $admin = function_exists('get_option') ? get_option('admin_email') : '';
        nj_send_html_mail($admin, 'Pedido pagado ' . $order->get_order_number(), nj_mail_card('Pedido para despachar', $store));
        if (function_exists('nj_whatsapp_paid')) {
            nj_whatsapp_paid($order);
        }
    }
}
add_action('woocommerce_order_status_processing', 'nj_paid_notice');

if (!function_exists('nj_pack_notice')) {
    function nj_pack_notice($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order || $order->get_meta('_nj_pack_notice') === '1') {
            return;
        }
        $order->update_meta_data('_nj_pack_notice', '1');
        $order->save();
        $name = $order->get_billing_first_name();
        $hello = $name ? 'Hola ' . $name . ',' : 'Hola,';
        $body = '<p>' . esc_html($hello) . '</p>';
        $body .= '<p>Estamos empacando tu pedido ' . esc_html((string) $order->get_order_number()) . '. Te avisamos por este correo cuando salga el envío.</p>';
        nj_send_html_mail($order->get_billing_email(), 'Estamos empacando tu pedido ' . $order->get_order_number(), nj_mail_card('Empacando tu pedido', $body));
        if (function_exists('nj_whatsapp_pack')) {
            nj_whatsapp_pack($order);
        }
    }
}
add_action('woocommerce_order_status_empacando', 'nj_pack_notice');

if (!function_exists('nj_order_action_empacando')) {
    function nj_order_action_empacando($actions) {
        $actions['nj_empacando'] = 'Marcar empacando';
        return $actions;
    }
}
add_filter('woocommerce_order_actions', 'nj_order_action_empacando');

if (!function_exists('nj_run_order_action_empacando')) {
    function nj_run_order_action_empacando($order) {
        if ($order && method_exists($order, 'update_status') && !$order->has_status('empacando')) {
            $order->update_status('empacando', 'La tienda marcó el pedido como empacando.');
        }
    }
}
add_action('woocommerce_order_action_nj_empacando', 'nj_run_order_action_empacando');

if (!function_exists('nj_empacar_screen')) {
    function nj_empacar_screen($title, $message) {
        if (function_exists('status_header')) {
            status_header(200);
        }
        if (function_exists('nocache_headers')) {
            nocache_headers();
        }
        echo '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . esc_html($title) . '</title></head><body style="margin:0;font-family:Montserrat,Arial,sans-serif;background:#faf6f3;color:#000"><main style="max-width:520px;margin:48px auto;padding:28px;background:#fff;border-radius:20px"><h1 style="font-size:28px;margin:0 0 12px">' . esc_html($title) . '</h1><p style="font-size:16px;line-height:1.5">' . esc_html($message) . '</p></main></body></html>';
        exit;
    }
}

if (!function_exists('nj_empacar_request')) {
    function nj_empacar_request() {
        if (!isset($_GET['nj_empacar']) || !function_exists('wc_get_order')) {
            return;
        }
        $order_id = isset($_GET['pedido']) ? absint($_GET['pedido']) : 0;
        $token = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
        if (isset($_GET['d'])) {
            $packed = sanitize_text_field(wp_unslash($_GET['d']));
            $dot = strpos($packed, '.');
            if ($dot !== false) {
                $order_id = absint(substr($packed, 0, $dot));
                $token = substr($packed, $dot + 1);
            }
        }
        $order = $order_id ? wc_get_order($order_id) : false;
        $expected = $order_id ? nj_empacar_token($order_id) : '';
        if (!$order || $expected === '' || strlen($expected) !== strlen($token) || !hash_equals($expected, $token)) {
            nj_empacar_screen('Enlace no válido', 'Este enlace no corresponde a un pedido de la tienda.');
        }
        if ($order->has_status('empacando')) {
            nj_empacar_screen('Ya está empacando', 'Este pedido ya estaba marcado. El cliente ya tiene el aviso.');
        }
        $order->update_status('empacando', 'La tienda marcó el pedido como empacando.');
        nj_empacar_screen('Empacando', 'Listo. El cliente recibió el aviso de que su pedido se está empacando.');
    }
}
add_action('template_redirect', 'nj_empacar_request');
