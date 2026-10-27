<?php
/**
 * Envío de avisos por la nube de WhatsApp (Meta).
 * El token vive en la base de la tienda, no en este archivo.
 */

if (!function_exists('nj_whatsapp_config')) {
    function nj_whatsapp_config() {
        $saved = get_option('nj_whatsapp', array());
        if (!is_array($saved)) {
            $saved = array();
        }
        return array_merge(array(
            'token' => '',
            'phone_id' => '1248096635538176',
            'waba_id' => '3272962839595437',
            'owner' => '',
        ), $saved);
    }
}

if (!function_exists('nj_whatsapp_param')) {
    function nj_whatsapp_param($text) {
        $text = wp_strip_all_tags((string) $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim((string) $text);
        if ($text === '') {
            $text = '-';
        }
        if (function_exists('mb_substr')) {
            return mb_substr($text, 0, 800);
        }
        return substr($text, 0, 800);
    }
}

if (!function_exists('nj_whatsapp_digits')) {
    function nj_whatsapp_digits($value) {
        $digits = preg_replace('/\D+/', '', (string) $value);
        $digits = (string) $digits;
        if (strlen($digits) === 10) {
            $digits = '57' . $digits;
        }
        return $digits;
    }
}

if (!function_exists('nj_whatsapp_items')) {
    function nj_whatsapp_items($order) {
        $parts = array();
        foreach ($order->get_items() as $item) {
            $parts[] = $item->get_name() . ' x' . $item->get_quantity();
        }
        $text = implode(', ', $parts);
        return $text !== '' ? $text : '-';
    }
}

if (!function_exists('nj_whatsapp_address')) {
    function nj_whatsapp_address($order) {
        $address = $order->get_formatted_shipping_address();
        if (!$address) {
            $address = $order->get_formatted_billing_address();
        }
        return $address ? $address : '-';
    }
}

if (!function_exists('nj_whatsapp_public_home')) {
    function nj_whatsapp_public_home() {
        $home = function_exists('home_url') ? home_url('/') : 'https://nuncajamasropa.com/';
        if (strpos($home, '.local') !== false || strpos($home, 'localhost') !== false) {
            return 'https://nuncajamasropa.com/';
        }
        return $home;
    }
}

if (!function_exists('nj_whatsapp_request')) {
    function nj_whatsapp_request($method, $path, $payload = null) {
        $config = nj_whatsapp_config();
        if ($config['token'] === '') {
            return new WP_Error('nj_wa', 'Falta el token de WhatsApp.');
        }
        $url = 'https://graph.facebook.com/v24.0/' . ltrim($path, '/');
        $args = array(
            'method' => $method,
            'timeout' => 20,
            'headers' => array(
                'Authorization' => 'Bearer ' . $config['token'],
                'Content-Type' => 'application/json',
            ),
        );
        if ($payload !== null) {
            $args['body'] = wp_json_encode($payload);
        }
        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            return $response;
        }
        $code = (int) wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if ($code < 200 || $code >= 300) {
            $message = 'WhatsApp no respondió.';
            if (is_array($body) && isset($body['error']['error_user_msg'])) {
                $message = (string) $body['error']['error_user_msg'];
            } elseif (is_array($body) && isset($body['error']['message'])) {
                $message = (string) $body['error']['message'];
            }
            return new WP_Error('nj_wa', $message);
        }
        return is_array($body) ? $body : array();
    }
}

if (!function_exists('nj_whatsapp_send')) {
    function nj_whatsapp_send($to, $template, $params, $button = '') {
        $to = nj_whatsapp_digits($to);
        if (strlen($to) < 7) {
            return new WP_Error('nj_wa', 'Falta un WhatsApp de destino.');
        }
        $parameters = array();
        foreach ($params as $param) {
            $parameters[] = array(
                'type' => 'text',
                'text' => nj_whatsapp_param($param),
            );
        }
        $components = array();
        if ($parameters) {
            $components[] = array(
                'type' => 'body',
                'parameters' => $parameters,
            );
        }
        if ($button !== '') {
            $components[] = array(
                'type' => 'button',
                'sub_type' => 'url',
                'index' => '0',
                'parameters' => array(
                    array(
                        'type' => 'text',
                        'text' => nj_whatsapp_param($button),
                    ),
                ),
            );
        }
        $config = nj_whatsapp_config();
        return nj_whatsapp_request('POST', rawurlencode($config['phone_id']) . '/messages', array(
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => array(
                'name' => $template,
                'language' => array('code' => 'es'),
                'components' => $components,
            ),
        ));
    }
}

if (!function_exists('nj_whatsapp_note')) {
    function nj_whatsapp_note($order, $result, $who) {
        if (!$order || !method_exists($order, 'add_order_note')) {
            return;
        }
        if (is_wp_error($result)) {
            $order->add_order_note('WhatsApp a ' . $who . ' no salió: ' . $result->get_error_message());
            return;
        }
        $order->add_order_note('WhatsApp enviado a ' . $who . '.');
    }
}

if (!function_exists('nj_whatsapp_paid')) {
    function nj_whatsapp_paid($order) {
        if (!$order) {
            return;
        }
        $config = nj_whatsapp_config();
        if ($config['token'] === '') {
            return;
        }
        $customer = function_exists('nj_order_whatsapp') ? nj_order_whatsapp($order) : '';
        if ($customer !== '') {
            $result = nj_whatsapp_send($customer, 'nj_pago_cliente', array(
                $order->get_billing_first_name() ? $order->get_billing_first_name() : 'hola',
                (string) $order->get_order_number(),
                wp_strip_all_tags($order->get_formatted_order_total()),
                nj_whatsapp_items($order),
            ));
            nj_whatsapp_note($order, $result, 'el cliente');
        }
        if ($config['owner'] === '') {
            $order->add_order_note('WhatsApp de la tienda sin número. El pedido sigue en el correo.');
            return;
        }
        $suffix = (int) $order->get_id() . '.' . nj_empacar_token($order->get_id());
        $result = nj_whatsapp_send($config['owner'], 'nj_pedido_tienda', array(
            (string) $order->get_order_number(),
            trim($order->get_formatted_billing_full_name()),
            $customer !== '' ? $customer : '-',
            nj_whatsapp_address($order),
            nj_whatsapp_items($order),
        ), $suffix);
        nj_whatsapp_note($order, $result, 'la tienda');
    }
}

if (!function_exists('nj_whatsapp_pack')) {
    function nj_whatsapp_pack($order) {
        if (!$order) {
            return;
        }
        $config = nj_whatsapp_config();
        if ($config['token'] === '') {
            return;
        }
        $customer = function_exists('nj_order_whatsapp') ? nj_order_whatsapp($order) : '';
        if ($customer === '') {
            return;
        }
        $result = nj_whatsapp_send($customer, 'nj_empacando_cliente', array(
            $order->get_billing_first_name() ? $order->get_billing_first_name() : 'hola',
            (string) $order->get_order_number(),
        ));
        nj_whatsapp_note($order, $result, 'el cliente');
    }
}

if (!function_exists('nj_whatsapp_templates')) {
    function nj_whatsapp_templates() {
        $home = nj_whatsapp_public_home();
        return array(
            array(
                'name' => 'nj_pago_cliente',
                'language' => 'es',
                'category' => 'UTILITY',
                'components' => array(
                    array(
                        'type' => 'BODY',
                        'text' => 'Hola {{1}}, tu pago quedó confirmado. Comprobante del pedido {{2}} por {{3}}. Prendas: {{4}}. Tu pedido pasó a preparación para despacho y por este WhatsApp seguimos el envío. Gracias.',
                        'example' => array('body_text' => array(array('Ana', '2501', '$ 47.000', 'Blusa x 1'))),
                    ),
                ),
            ),
            array(
                'name' => 'nj_pedido_tienda',
                'language' => 'es',
                'category' => 'UTILITY',
                'components' => array(
                    array(
                        'type' => 'BODY',
                        'text' => 'Tienes un pedido pagado. Número {{1}}. Cliente: {{2}}. WhatsApp: {{3}}. Dirección: {{4}}. Prendas: {{5}}. Pulsa Empacando cuando empieces a alistarlo.',
                        'example' => array('body_text' => array(array('2501', 'Ana Perez', '573001112233', 'Bogota', 'Blusa x 1'))),
                    ),
                    array(
                        'type' => 'BUTTONS',
                        'buttons' => array(
                            array(
                                'type' => 'URL',
                                'text' => 'Empacando',
                                'url' => $home . '?nj_empacar=1&d={{1}}',
                                'example' => array('2501.abc123'),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'name' => 'nj_empacando_cliente',
                'language' => 'es',
                'category' => 'UTILITY',
                'components' => array(
                    array(
                        'type' => 'BODY',
                        'text' => 'Hola {{1}}, estamos empacando tu pedido {{2}}. Te avisamos por este WhatsApp cuando salga el envío. Gracias.',
                        'example' => array('body_text' => array(array('Ana', '2501'))),
                    ),
                ),
            ),
        );
    }
}

if (!function_exists('nj_whatsapp_create_templates')) {
    function nj_whatsapp_create_templates() {
        $config = nj_whatsapp_config();
        $notes = array();
        if ($config['waba_id'] === '' || $config['token'] === '') {
            return array('Falta el token o la cuenta de WhatsApp.');
        }
        foreach (nj_whatsapp_templates() as $template) {
            $result = nj_whatsapp_request('POST', rawurlencode($config['waba_id']) . '/message_templates', $template);
            if (is_wp_error($result)) {
                $notes[] = $template['name'] . ': ' . $result->get_error_message();
                continue;
            }
            $status = isset($result['status']) ? $result['status'] : 'enviada';
            $notes[] = $template['name'] . ': ' . $status;
        }
        return $notes;
    }
}

if (!function_exists('nj_whatsapp_admin_menu')) {
    function nj_whatsapp_admin_menu() {
        if (!function_exists('add_submenu_page')) {
            return;
        }
        add_submenu_page(
            'woocommerce',
            'WhatsApp',
            'WhatsApp',
            'manage_woocommerce',
            'nj-whatsapp',
            'nj_whatsapp_admin_page'
        );
    }
}
add_action('admin_menu', 'nj_whatsapp_admin_menu');

if (!function_exists('nj_whatsapp_admin_page')) {
    function nj_whatsapp_admin_page() {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        $config = nj_whatsapp_config();
        $notice = '';
        if (isset($_POST['nj_whatsapp_save']) && check_admin_referer('nj_whatsapp_save')) {
            $token = isset($_POST['nj_wa_token']) ? trim(sanitize_text_field(wp_unslash($_POST['nj_wa_token']))) : '';
            $config['phone_id'] = isset($_POST['nj_wa_phone']) ? preg_replace('/\D+/', '', wp_unslash($_POST['nj_wa_phone'])) : $config['phone_id'];
            $config['waba_id'] = isset($_POST['nj_wa_waba']) ? preg_replace('/\D+/', '', wp_unslash($_POST['nj_wa_waba'])) : $config['waba_id'];
            $config['owner'] = nj_whatsapp_digits(isset($_POST['nj_wa_owner']) ? wp_unslash($_POST['nj_wa_owner']) : '');
            if ($token !== '') {
                $config['token'] = $token;
            }
            update_option('nj_whatsapp', $config, false);
            $notice = 'WhatsApp guardado.';
        }
        if (isset($_POST['nj_whatsapp_templates']) && check_admin_referer('nj_whatsapp_templates')) {
            $notice = implode(' ', nj_whatsapp_create_templates());
        }
        $config = nj_whatsapp_config();
        echo '<div class="wrap"><h1>WhatsApp</h1>';
        if ($notice !== '') {
            echo '<div class="updated"><p>' . esc_html($notice) . '</p></div>';
        }
        echo '<p>El número que envía es el de Nunca Jamás, +57 310 3580161. En Meta tiene que estar en la nube y verificado. El celular de abajo es donde te llegan los pedidos.</p>';
        echo '<form method="post">';
        wp_nonce_field('nj_whatsapp_save');
        echo '<table class="form-table"><tbody>';
        echo '<tr><th><label for="nj_wa_token">Token permanente</label></th><td><input name="nj_wa_token" id="nj_wa_token" type="password" class="regular-text" autocomplete="off" placeholder="' . ($config['token'] !== '' ? 'Token guardado' : 'Pega el token') . '"><p class="description">Si lo dejas vacío se conserva el que ya está guardado.</p></td></tr>';
        echo '<tr><th><label for="nj_wa_phone">Identificador del número</label></th><td><input name="nj_wa_phone" id="nj_wa_phone" type="text" class="regular-text" value="' . esc_attr($config['phone_id']) . '"></td></tr>';
        echo '<tr><th><label for="nj_wa_waba">Cuenta de WhatsApp</label></th><td><input name="nj_wa_waba" id="nj_wa_waba" type="text" class="regular-text" value="' . esc_attr($config['waba_id']) . '"></td></tr>';
        echo '<tr><th><label for="nj_wa_owner">Tu WhatsApp</label></th><td><input name="nj_wa_owner" id="nj_wa_owner" type="text" class="regular-text" value="' . esc_attr($config['owner']) . '" placeholder="573001112233"></td></tr>';
        echo '</tbody></table>';
        echo '<p><button class="button button-primary" name="nj_whatsapp_save" value="1">Guardar</button></p>';
        echo '</form>';
        echo '<form method="post" style="margin-top:12px">';
        wp_nonce_field('nj_whatsapp_templates');
        echo '<button class="button" name="nj_whatsapp_templates" value="1">Crear plantillas</button>';
        echo '<p class="description">Crea los tres mensajes: comprobante al cliente, pedido a la tienda con el botón Empacando, y aviso de empacando. Meta tiene que aprobarlos.</p>';
        echo '</form></div>';
    }
}
