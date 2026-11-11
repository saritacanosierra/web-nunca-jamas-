<?php
/**
 * Pedidos de Mi tienda: el período, el estado y los datos del cliente.
 */

if (!function_exists('nj_pedidos_rango')) {
    function nj_pedidos_rango($vista, $fecha) {
        $vista = in_array($vista, array('cola', 'dia', 'mes', 'ano'), true) ? $vista : 'dia';
        $fecha = preg_replace('/[^0-9-]/', '', (string) $fecha);
        $hoy = function_exists('wp_date') ? wp_date('Y-m-d') : date('Y-m-d');
        if ($vista === 'cola') {
            $start = date('Y-m-d', strtotime($hoy . ' 12:00:00 -90 days'));
            return array($start, $hoy, 'cola', '');
        }
        if ($vista === 'ano') {
            if (!preg_match('/^(\d{4})/', $fecha, $found)) {
                $fecha = substr($hoy, 0, 4);
            } else {
                $fecha = $found[1];
            }
            return array($fecha . '-01-01', $fecha . '-12-31', 'ano', $fecha);
        }
        if ($vista === 'mes') {
            if (!preg_match('/^(\d{4})-(\d{2})/', $fecha, $found)) {
                $fecha = substr($hoy, 0, 7);
            } else {
                $fecha = $found[1] . '-' . $found[2];
            }
            $start = $fecha . '-01';
            $end = date('Y-m-t', strtotime($start . ' 12:00:00'));
            return array($start, $end, 'mes', $fecha);
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = $hoy;
        }
        return array($fecha, $fecha, 'dia', $fecha);
    }
}

if (!function_exists('nj_pedidos_estados')) {
    function nj_pedidos_estados() {
        return array(
            'todos' => array('processing', 'on-hold', 'elaborado', 'empacando', 'despacho', 'completed', 'entregado', 'pending', 'cancelled', 'refunded', 'failed'),
            'por-elaborar' => array('processing', 'on-hold'),
            'elaborado' => array('elaborado'),
            'empacado' => array('empacando', 'despacho'),
            'enviado' => array('completed'),
            'entregado' => array('entregado'),
        );
    }
}

if (!function_exists('nj_pedidos_estado_nombre')) {
    function nj_pedidos_estado_nombre($status) {
        $names = array(
            'processing' => 'Pagado',
            'elaborado' => 'Elaborado',
            'empacando' => 'Empacado',
            'despacho' => 'Empacado',
            'completed' => 'Enviado',
            'entregado' => 'Entregado',
            'pending' => 'Pendiente de pago',
            'on-hold' => 'En espera',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            'failed' => 'Fallido',
        );
        return isset($names[$status]) ? $names[$status] : (string) $status;
    }
}

if (!function_exists('nj_pedidos_etapa')) {
    function nj_pedidos_etapa($status, $personalizado) {
        if ($status === 'entregado') {
            return 'entregado';
        }
        if ($status === 'completed') {
            return 'enviado';
        }
        if ($status === 'empacando' || $status === 'despacho') {
            return 'empacado';
        }
        if ($status === 'elaborado') {
            return 'elaborado';
        }
        if (in_array($status, array('processing', 'on-hold'), true)) {
            return 'por-elaborar';
        }
        return 'otro';
    }
}

if (!function_exists('nj_pedidos_etapa_nombre')) {
    function nj_pedidos_etapa_nombre($etapa) {
        $names = array(
            'por-elaborar' => 'Por elaborar',
            'elaborado' => 'Elaborado',
            'empacado' => 'Empacado',
            'enviado' => 'Enviado',
            'entregado' => 'Entregado',
        );
        return isset($names[$etapa]) ? $names[$etapa] : '';
    }
}

if (!function_exists('nj_pedidos_pasos')) {
    function nj_pedidos_pasos($etapa, $personalizado = false) {
        $defs = array(
            array('id' => 'elaborado', 'label' => 'Elaborado', 'action' => 'elaborado'),
            array('id' => 'empacado', 'label' => 'Empacado', 'action' => 'empacando'),
            array('id' => 'enviado', 'label' => 'Enviado', 'action' => 'completed'),
            array('id' => 'entregado', 'label' => 'Entregado', 'action' => 'entregado'),
        );
        $done = array(
            'por-elaborar' => -1,
            'elaborado' => 0,
            'empacado' => 1,
            'enviado' => 2,
            'entregado' => 3,
        );
        $current = isset($done[$etapa]) ? $done[$etapa] : -2;
        $steps = array();
        foreach ($defs as $i => $step) {
            if ($i <= $current) {
                $step['state'] = 'done';
            } elseif ($i === $current + 1) {
                $step['state'] = 'now';
            } else {
                $step['state'] = 'wait';
            }
            $steps[] = $step;
        }
        return $steps;
    }
}

if (!function_exists('nj_pedidos_transportadoras')) {
    function nj_pedidos_transportadoras() {
        return array(
            'servientrega' => 'Servientrega',
            'coordinadora' => 'Coordinadora',
            'interrapidisimo' => 'Interrapidísimo',
            'envia' => 'Envía',
            'deprisa' => 'Deprisa',
            'otra' => 'Otra',
        );
    }
}

if (!function_exists('nj_pedidos_guia_limpia')) {
    function nj_pedidos_guia_limpia($raw) {
        $raw = trim(preg_replace('/\s+/', ' ', (string) $raw));
        $raw = preg_replace('/[^A-Za-z0-9 -]/', '', $raw);
        $largo = function_exists('mb_strlen') ? mb_strlen($raw) : strlen($raw);
        if ($largo < 4 || $largo > 40) {
            return '';
        }
        return $raw;
    }
}

if (!function_exists('nj_pedidos_consulta')) {
    function nj_pedidos_consulta($vista, $fecha, $estado) {
        $estados = nj_pedidos_estados();
        if (!isset($estados[$estado])) {
            $estado = 'todos';
        }
        list($start, $end, $vista, $fecha) = nj_pedidos_rango($vista, $fecha);
        $rows = array();
        if (function_exists('wc_get_orders')) {
            $orders = wc_get_orders(array(
                'limit' => 200,
                'orderby' => 'date',
                'order' => 'DESC',
                'status' => $estados[$estado],
                'date_created' => $start . '...' . $end,
            ));
            if (is_array($orders)) {
                foreach ($orders as $order) {
                    $row = nj_pedidos_fila($order);
                    if (nj_pedidos_en_vista($row, $estado, $vista)) {
                        $rows[] = $row;
                    }
                }
            }
        }
        return array(
            'vista' => $vista,
            'fecha' => $fecha,
            'inicio' => $start,
            'fin' => $end,
            'estado' => $estado,
            'rows' => $rows,
        );
    }
}

if (!function_exists('nj_pedidos_dato')) {
    function nj_pedidos_dato($pairs, $kind) {
        foreach ($pairs as $pair) {
            $key = isset($pair[0]) ? (string) $pair[0] : '';
            $value = isset($pair[1]) ? trim((string) $pair[1]) : '';
            if ($key === '' || $value === '') {
                continue;
            }
            $match = function_exists('nj_prendas_is_kind')
                ? nj_prendas_is_kind($key, $kind)
                : strpos(strtolower($key), $kind === 'color' ? 'color' : 'talla') !== false;
            if (!$match) {
                continue;
            }
            if (function_exists('nj_prendas_value_label')) {
                $value = nj_prendas_value_label($key, $value);
            }
            return trim((string) $value);
        }
        return '';
    }
}

if (!function_exists('nj_pedidos_prenda')) {
    function nj_pedidos_prenda($item) {
        $product = is_object($item) && method_exists($item, 'get_product') ? $item->get_product() : null;
        $parent = null;
        if ($product && method_exists($product, 'is_type') && $product->is_type('variation') && function_exists('wc_get_product')) {
            $parent = wc_get_product($product->get_parent_id());
        }
        $name = $parent && method_exists($parent, 'get_name') ? $parent->get_name() : (is_object($item) && method_exists($item, 'get_name') ? $item->get_name() : '');
        $sku = $product && method_exists($product, 'get_sku') ? (string) $product->get_sku() : '';
        if ($sku === '' && $parent && method_exists($parent, 'get_sku')) {
            $sku = (string) $parent->get_sku();
        }
        $image_id = $product && method_exists($product, 'get_image_id') ? (int) $product->get_image_id() : 0;
        if (!$image_id && $parent && method_exists($parent, 'get_image_id')) {
            $image_id = (int) $parent->get_image_id();
        }
        $image = '';
        if ($image_id && function_exists('wp_get_attachment_image_url')) {
            $found = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
            $image = $found ? $found : '';
        }
        $pairs = array();
        if ($product && method_exists($product, 'get_attributes')) {
            foreach ($product->get_attributes() as $key => $value) {
                if (is_scalar($value) && (string) $value !== '') {
                    $pairs[] = array((string) $key, (string) $value);
                }
            }
        }
        if (is_object($item) && method_exists($item, 'get_meta_data')) {
            foreach ($item->get_meta_data() as $meta) {
                if (!is_object($meta) || !method_exists($meta, 'get_data')) {
                    continue;
                }
                $data = $meta->get_data();
                $key = isset($data['key']) ? (string) $data['key'] : '';
                $value = isset($data['value']) ? $data['value'] : '';
                if ($key === '' || !is_scalar($value) || (string) $value === '' || strpos($key, '_') === 0) {
                    continue;
                }
                $pairs[] = array($key, (string) $value);
            }
        }
        $bordado = is_object($item) && method_exists($item, 'get_meta') ? trim((string) $item->get_meta('Nombre para bordar')) : '';
        return array(
            'name' => (string) $name,
            'sku' => $sku,
            'talla' => nj_pedidos_dato($pairs, 'talla'),
            'color' => nj_pedidos_dato($pairs, 'color'),
            'image' => $image,
            'qty' => is_object($item) && method_exists($item, 'get_quantity') ? (int) $item->get_quantity() : 1,
            'bordado' => $bordado,
        );
    }
}

if (!function_exists('nj_pedidos_fila')) {
    function nj_pedidos_fila($order) {
        $status = $order->get_status();
        $created = $order->get_date_created();
        $stamp = $created ? $created->getTimestamp() : 0;
        $day = $stamp && function_exists('wp_date') ? wp_date('Y-m-d', $stamp) : ($stamp ? date('Y-m-d', $stamp) : '');
        $month = $day !== '' ? substr($day, 0, 7) : '';
        $address = $order->get_formatted_shipping_address();
        if (!$address) {
            $address = $order->get_formatted_billing_address();
        }
        $address = str_replace(array('<br>', '<br/>', '<br />'), ', ', (string) $address);
        $address = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($address)));
        $items = array();
        $bordados = array();
        foreach ($order->get_items() as $item) {
            if (!is_object($item) || !method_exists($item, 'get_name')) {
                continue;
            }
            $line = nj_pedidos_prenda($item);
            $items[] = $line;
            if ($line['bordado'] !== '') {
                $bordados[] = $line['name'] . ': ' . $line['bordado'];
            }
        }
        $personalizado = $bordados || (function_exists('nj_personalizado_order_has') && nj_personalizado_order_has($order));
        $total = function_exists('wc_price') ? wc_price($order->get_total()) : (string) $order->get_total();
        $etapa = nj_pedidos_etapa($status, $personalizado);
        $guia = (string) $order->get_meta('_nj_guia');
        $carrier = (string) $order->get_meta('_nj_transportadora');
        $carriers = nj_pedidos_transportadoras();
        return array(
            'id' => (int) $order->get_id(),
            'number' => (string) $order->get_order_number(),
            'status' => $status,
            'status_name' => nj_pedidos_estado_nombre($status),
            'etapa' => $etapa,
            'etapa_nombre' => nj_pedidos_etapa_nombre($etapa),
            'personalizado' => $personalizado,
            'day' => $day,
            'month' => $month,
            'when' => $stamp && function_exists('wp_date') ? wp_date('j M Y · H:i', $stamp) : '',
            'name' => trim($order->get_formatted_billing_full_name()),
            'email' => (string) $order->get_billing_email(),
            'phone' => (string) $order->get_billing_phone(),
            'cedula' => function_exists('nj_order_cedula') ? nj_order_cedula($order) : '',
            'whatsapp' => function_exists('nj_order_whatsapp') ? nj_order_whatsapp($order) : '',
            'address' => $address,
            'items' => $items,
            'bordados' => $bordados,
            'listo' => (string) $order->get_meta('_nj_listo'),
            'guia' => $guia,
            'transportadora' => isset($carriers[$carrier]) ? $carrier : '',
            'transportadora_nombre' => isset($carriers[$carrier]) ? $carriers[$carrier] : '',
            'total' => $total,
            'amount' => (float) $order->get_total(),
            'paid' => in_array($status, array('processing', 'elaborado', 'empacando', 'despacho', 'completed', 'entregado'), true),
        );
    }
}

if (!function_exists('nj_pedidos_en_vista')) {
    function nj_pedidos_en_vista($row, $estado, $vista) {
        $etapa = isset($row['etapa']) ? $row['etapa'] : 'otro';
        if ($estado === 'por-elaborar') {
            return $etapa === 'por-elaborar';
        }
        if ($estado === 'elaborado') {
            return $etapa === 'elaborado';
        }
        if ($estado === 'empacado') {
            return $etapa === 'empacado';
        }
        if ($estado === 'enviado') {
            return $etapa === 'enviado';
        }
        if ($estado === 'entregado') {
            return $etapa === 'entregado';
        }
        if ($vista === 'cola') {
            return in_array($etapa, array('por-elaborar', 'elaborado', 'empacado', 'enviado'), true);
        }
        return true;
    }
}

if (!function_exists('nj_pedidos_guardar_guia')) {
    function nj_pedidos_guardar_guia($order, $guia, $transportadora) {
        $guia = nj_pedidos_guia_limpia($guia);
        if ($guia === '' || !is_object($order) || !method_exists($order, 'update_meta_data')) {
            return false;
        }
        $carriers = nj_pedidos_transportadoras();
        if (!isset($carriers[$transportadora])) {
            $transportadora = 'otra';
        }
        $order->update_meta_data('_nj_guia', $guia);
        $order->update_meta_data('_nj_transportadora', $transportadora);
        $order->save();
        return true;
    }
}

if (!function_exists('nj_pedidos_guia_bloque')) {
    function nj_pedidos_guia_bloque($order) {
        if (!is_object($order) || !method_exists($order, 'get_meta')) {
            return;
        }
        $guia = nj_pedidos_guia_limpia($order->get_meta('_nj_guia'));
        if ($guia === '') {
            return;
        }
        $carriers = nj_pedidos_transportadoras();
        $key = (string) $order->get_meta('_nj_transportadora');
        $nombre = isset($carriers[$key]) ? $carriers[$key] : '';
        echo '<p class="nj-guia" style="margin:16px 0;padding:12px 14px;border-radius:12px;background:#f3fbf8;color:#14532d;">Tu pedido ya va en camino';
        if ($nombre !== '') {
            echo ' con ' . esc_html($nombre);
        }
        echo '. Guía <strong>' . esc_html($guia) . '</strong>.</p>';
    }
}
add_action('woocommerce_order_details_after_order_table', 'nj_pedidos_guia_bloque', 8);
add_action('woocommerce_email_after_order_table', 'nj_pedidos_guia_bloque', 12);

if (!function_exists('nj_pedidos_marcar')) {
    function nj_pedidos_marcar($order_id, $estado) {
        if (!function_exists('wc_get_order') || !current_user_can('manage_woocommerce')) {
            return false;
        }
        $order = wc_get_order((int) $order_id);
        if (!$order) {
            return false;
        }
        if ($estado === 'elaborado' && $order->has_status(array('processing', 'on-hold'))) {
            $order->update_status('elaborado', 'La tienda marcó el pedido como elaborado.');
            return true;
        }
        if ($estado === 'empacando' && $order->has_status('elaborado')) {
            $order->update_status('empacando', 'La tienda marcó el pedido como empacado.');
            return true;
        }
        if ($estado === 'guia' && $order->has_status('completed')) {
            return nj_pedidos_guardar_guia($order, isset($_POST['nj_guia']) ? wp_unslash($_POST['nj_guia']) : '', isset($_POST['nj_transportadora']) ? sanitize_key(wp_unslash($_POST['nj_transportadora'])) : '');
        }
        if ($estado === 'completed' && $order->has_status(array('empacando', 'despacho'))) {
            $guardada = nj_pedidos_guardar_guia($order, isset($_POST['nj_guia']) ? wp_unslash($_POST['nj_guia']) : '', isset($_POST['nj_transportadora']) ? sanitize_key(wp_unslash($_POST['nj_transportadora'])) : '');
            if (!$guardada) {
                return false;
            }
            $order->update_status('completed', 'La tienda envió el pedido. Guía ' . $order->get_meta('_nj_guia') . '.');
            return true;
        }
        if ($estado === 'entregado' && $order->has_status('completed')) {
            $order->update_status('entregado', 'La tienda marcó el pedido como entregado.');
            return true;
        }
        return false;
    }
}

if (!function_exists('nj_pedidos_save')) {
    function nj_pedidos_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : current_user_can('manage_woocommerce');
        if (!$allowed) {
            wp_die('No tienes permiso para cambiar este pedido.');
        }
        check_admin_referer('nj_pedidos', 'nj_pedidos_nonce');
        $order_id = isset($_POST['pedido']) ? absint($_POST['pedido']) : 0;
        $estado = isset($_POST['nj_estado']) ? sanitize_key(wp_unslash($_POST['nj_estado'])) : '';
        $listo = nj_pedidos_marcar($order_id, $estado);
        $vista = isset($_POST['nj_vista']) ? sanitize_key(wp_unslash($_POST['nj_vista'])) : 'cola';
        $fecha = isset($_POST['nj_fecha']) ? sanitize_text_field(wp_unslash($_POST['nj_fecha'])) : '';
        $filtro = isset($_POST['nj_filtro']) ? sanitize_key(wp_unslash($_POST['nj_filtro'])) : 'todos';
        wp_safe_redirect(add_query_arg(array(
            'page' => 'nj-tienda',
            'updated' => $listo ? ($estado === 'entregado' ? 'entregado' : 'pedido') : (in_array($estado, array('completed', 'guia'), true) ? 'guia' : 'pedido'),
            'nj_vista' => $vista,
            'nj_fecha' => $fecha,
            'nj_filtro' => $filtro,
        ), admin_url('admin.php')) . '#pedidos');
        exit;
    }
}
add_action('admin_post_nj_pedidos', 'nj_pedidos_save');

if (!function_exists('nj_pedidos_demo_product')) {
    function nj_pedidos_demo_product() {
        static $ready = false;
        static $product = null;
        if ($ready) {
            return $product;
        }
        $ready = true;
        if (!function_exists('wc_get_products')) {
            return null;
        }
        $found = wc_get_products(array('status' => 'publish', 'limit' => 6, 'type' => array('simple', 'variable')));
        if (!is_array($found)) {
            return null;
        }
        foreach ($found as $candidate) {
            if (!is_object($candidate) || !method_exists($candidate, 'is_type')) {
                continue;
            }
            if ($candidate->is_type('variable') && method_exists($candidate, 'get_children') && $candidate->get_children() && function_exists('wc_get_product')) {
                $child = wc_get_product($candidate->get_children()[0]);
                if ($child) {
                    $product = $child;
                    return $product;
                }
            }
            if ($candidate->is_type('simple')) {
                $product = $candidate;
                return $product;
            }
        }
        return null;
    }
}

if (!function_exists('nj_pedidos_demo_local')) {
    function nj_pedidos_demo_local() {
        if (!function_exists('get_option') || get_option('nj_pedidos_demo_v1')) {
            return;
        }
        $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
        if (strpos($host, 'nuncajamas.local') === false) {
            return;
        }
        if (!function_exists('wc_create_order') || !current_user_can('manage_woocommerce')) {
            return;
        }
        remove_action('woocommerce_order_status_processing', 'nj_paid_notice');
        remove_action('woocommerce_order_status_empacando', 'nj_pack_notice');
        remove_action('woocommerce_order_status_completed', 'nj_ship_notice');
        remove_action('woocommerce_order_status_processing', 'nj_personalizado_schedule');
        remove_action('woocommerce_order_status_completed', 'nj_personalizado_schedule');
        add_filter('pre_wp_mail', '__return_true');
        $muestras = array(
            array('status' => 'processing', 'nombre' => 'Camila', 'apellido' => 'Restrepo', 'cedula' => '1012345678', 'talla' => '6', 'color' => 'Rosa', 'bordado' => 'Camila', 'guia' => ''),
            array('status' => 'elaborado', 'nombre' => 'Valentina', 'apellido' => 'Ospina', 'cedula' => '1023456789', 'talla' => '8', 'color' => 'Azul', 'bordado' => '', 'guia' => ''),
            array('status' => 'empacando', 'nombre' => 'María José', 'apellido' => 'Baquero', 'cedula' => '1034567890', 'talla' => '4', 'color' => 'Lila', 'bordado' => '', 'guia' => ''),
            array('status' => 'completed', 'nombre' => 'Erika', 'apellido' => 'Ramírez', 'cedula' => '1045678901', 'talla' => '10', 'color' => 'Negro', 'bordado' => '', 'guia' => '9921847751'),
            array('status' => 'entregado', 'nombre' => 'Marisol', 'apellido' => 'Prueba', 'cedula' => '1056789012', 'talla' => '12', 'color' => 'Verde', 'bordado' => '', 'guia' => '7712003488'),
        );
        foreach ($muestras as $muestra) {
            $order = wc_create_order(array('status' => 'pending'));
            if (is_wp_error($order) || !$order) {
                continue;
            }
            $product = nj_pedidos_demo_product();
            if ($product) {
                $order->add_product($product, $muestra['status'] === 'completed' ? 2 : 1);
            } elseif (class_exists('WC_Order_Item_Product')) {
                $item = new WC_Order_Item_Product();
                $item->set_name('Vestido de cuento');
                $item->set_quantity(1);
                $item->set_total(85000);
                $order->add_item($item);
            }
            foreach ($order->get_items() as $item) {
                if ($muestra['talla'] !== '') {
                    $item->add_meta_data('pa_talla', $muestra['talla'], true);
                }
                if ($muestra['color'] !== '') {
                    $item->add_meta_data('pa_color', $muestra['color'], true);
                }
                if ($muestra['bordado'] !== '') {
                    $item->add_meta_data('Nombre para bordar', $muestra['bordado'], true);
                }
                $item->save();
            }
            $address = array(
                'first_name' => 'Prueba ' . $muestra['nombre'],
                'last_name' => $muestra['apellido'],
                'email' => 'prueba+' . sanitize_title($muestra['nombre']) . '@nuncajamas.local',
                'phone' => '3001112233',
                'address_1' => 'Cra 45 # 12-30',
                'city' => 'Medellín',
                'state' => 'ANT',
                'country' => 'CO',
            );
            $order->set_address($address, 'billing');
            $order->set_address($address, 'shipping');
            $order->update_meta_data('_nj_demo', '1');
            $order->update_meta_data('_billing_cedula', $muestra['cedula']);
            if ($muestra['guia'] !== '') {
                $order->update_meta_data('_nj_guia', $muestra['guia']);
                $order->update_meta_data('_nj_transportadora', 'coordinadora');
            }
            $order->set_status($muestra['status'], 'Pedido de prueba del tablero.', true);
            $order->calculate_totals();
            $order->save();
        }
        update_option('nj_pedidos_demo_v1', '1', false);
    }
}
add_action('admin_init', 'nj_pedidos_demo_local');

require_once __DIR__ . '/admin/pedidos.php';
