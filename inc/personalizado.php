<?php
/**
 * Bordado del nombre: la prenda lo ofrece si tiene el check, suma 20000
 * y, desde el pago, cuenta 8 días hábiles de elaboración.
 */

if (!function_exists('nj_personalizado_fee')) {
    function nj_personalizado_fee() {
        return 20000;
    }
}

if (!function_exists('nj_personalizado_warning_html')) {
    function nj_personalizado_warning_html($hidden = false) {
        $hidden_attr = $hidden ? ' data-bag-embroider hidden' : '';
        return '<p class="nj-embroider-alert"' . $hidden_attr . ' role="note"><strong>Aviso importante.</strong> Revisa el nombre con cuidado antes de pagar. Después de pagado no se hacen cambios ni devoluciones.</p>';
    }
}

if (!function_exists('nj_personalizado_fonts')) {
    function nj_personalizado_fonts() {
        return array(
            'cursiva' => 'Cursiva',
            'imprenta' => 'Imprenta',
        );
    }
}

if (!function_exists('nj_personalizado_on')) {
    function nj_personalizado_on($product_id) {
        $product_id = (int) $product_id;
        if ($product_id < 1 || !function_exists('get_post_meta')) {
            return false;
        }
        $product = null;
        if (function_exists('wc_get_product')) {
            $found = wc_get_product($product_id);
            if (is_object($found)) {
                $product = $found;
            }
        }
        if ($product instanceof WC_Product && $product->is_type('variation')) {
            $product_id = (int) $product->get_parent_id();
        }
        return get_post_meta($product_id, '_nj_personalizado', true) === '1';
    }
}

if (!function_exists('nj_personalizado_name')) {
    function nj_personalizado_name($value) {
        $value = trim(wp_strip_all_tags((string) $value));
        $value = preg_replace("/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ '\\-]/u", '', $value);
        $value = preg_replace('/\s+/u', ' ', (string) $value);
        if (function_exists('mb_substr')) {
            $value = mb_substr($value, 0, 12);
        } else {
            $value = substr($value, 0, 12);
        }
        return trim((string) $value);
    }
}

if (!function_exists('nj_personalizado_font')) {
    function nj_personalizado_font($value) {
        $fonts = nj_personalizado_fonts();
        $value = function_exists('sanitize_key') ? sanitize_key($value) : strtolower((string) $value);
        return isset($fonts[$value]) ? $value : 'cursiva';
    }
}

if (!function_exists('nj_personalizado_money')) {
    function nj_personalizado_money($value) {
        return '$ ' . number_format((int) $value, 0, ',', '.');
    }
}

if (!function_exists('nj_personalizado_cart_data')) {
    function nj_personalizado_cart_data($data, $product_id) {
        if (!nj_personalizado_on($product_id) || empty($_POST['nj_nombre'])) {
            return $data;
        }
        $name = nj_personalizado_name(wp_unslash($_POST['nj_nombre']));
        if ($name === '') {
            return $data;
        }
        $data['nj_nombre'] = $name;
        $data['nj_fuente'] = nj_personalizado_font(isset($_POST['nj_fuente']) ? wp_unslash($_POST['nj_fuente']) : '');
        return $data;
    }
}
add_filter('woocommerce_add_cart_item_data', 'nj_personalizado_cart_data', 10, 2);

if (!function_exists('nj_personalizado_totals')) {
    function nj_personalizado_totals($cart) {
        $ajax = function_exists('wp_doing_ajax') ? wp_doing_ajax() : (defined('DOING_AJAX') && DOING_AJAX);
        if (!is_object($cart) || (function_exists('is_admin') && is_admin() && !$ajax)) {
            return;
        }
        foreach ($cart->get_cart() as $item) {
            if (empty($item['nj_nombre']) || empty($item['data']) || !function_exists('wc_get_product')) {
                continue;
            }
            $fresh = wc_get_product($item['data']->get_id());
            if (!$fresh) {
                continue;
            }
            $item['data']->set_price((float) $fresh->get_price() + nj_personalizado_fee());
            $regular = (float) $fresh->get_regular_price();
            if ($regular > 0) {
                $item['data']->set_regular_price($regular + nj_personalizado_fee());
            }
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'nj_personalizado_totals', 20);

if (!function_exists('nj_personalizado_item_data')) {
    function nj_personalizado_item_data($rows, $item) {
        if (empty($item['nj_nombre'])) {
            return $rows;
        }
        $fonts = nj_personalizado_fonts();
        $font = isset($item['nj_fuente']) ? $item['nj_fuente'] : 'cursiva';
        $rows[] = array('key' => 'Nombre para bordar', 'value' => $item['nj_nombre']);
        $rows[] = array('key' => 'Fuente', 'value' => isset($fonts[$font]) ? $fonts[$font] : 'Cursiva');
        return $rows;
    }
}
add_filter('woocommerce_get_item_data', 'nj_personalizado_item_data', 10, 2);

if (!function_exists('nj_personalizado_line_item')) {
    function nj_personalizado_line_item($item, $key, $values) {
        if (empty($values['nj_nombre'])) {
            return;
        }
        $fonts = nj_personalizado_fonts();
        $font = isset($values['nj_fuente']) ? $values['nj_fuente'] : 'cursiva';
        $item->add_meta_data('Nombre para bordar', $values['nj_nombre'], true);
        $item->add_meta_data('Fuente', isset($fonts[$font]) ? $fonts[$font] : 'Cursiva', true);
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'nj_personalizado_line_item', 10, 3);

if (!function_exists('nj_personalizado_line_note')) {
    function nj_personalizado_line_note($item) {
        if (empty($item['nj_nombre'])) {
            return '';
        }
        $fonts = nj_personalizado_fonts();
        $font = isset($item['nj_fuente']) ? $item['nj_fuente'] : 'cursiva';
        $label = isset($fonts[$font]) ? $fonts[$font] : 'Cursiva';
        return 'Bordado: ' . $item['nj_nombre'] . ' · ' . $label;
    }
}

if (!function_exists('nj_personalizado_order_has')) {
    function nj_personalizado_order_has($order) {
        if (!is_object($order) || !method_exists($order, 'get_items')) {
            return false;
        }
        foreach ($order->get_items() as $item) {
            if (method_exists($item, 'get_meta') && $item->get_meta('Nombre para bordar') !== '') {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('nj_personalizado_ready')) {
    function nj_personalizado_ready($from) {
        $zone = new DateTimeZone('America/Bogota');
        if ($from instanceof DateTimeInterface) {
            $date = DateTime::createFromInterface($from);
        } else {
            $date = new DateTime('now', $zone);
        }
        $date->setTimezone($zone);
        $added = 0;
        while ($added < 8) {
            $date->modify('+1 day');
            if ((int) $date->format('N') < 6) {
                $added++;
            }
        }
        return $date->format('Y-m-d');
    }
}

if (!function_exists('nj_personalizado_date_label')) {
    function nj_personalizado_date_label($ymd) {
        $stamp = strtotime((string) $ymd . ' 12:00:00');
        if (!$stamp) {
            return '';
        }
        if (function_exists('wp_date')) {
            return wp_date('j \d\e F \d\e Y', $stamp);
        }
        return date('j/m/Y', $stamp);
    }
}

if (!function_exists('nj_personalizado_schedule')) {
    function nj_personalizado_schedule($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order instanceof WC_Order || $order->get_meta('_nj_listo') || !nj_personalizado_order_has($order)) {
            return;
        }
        $from = method_exists($order, 'get_date_paid') ? $order->get_date_paid() : null;
        $ready = nj_personalizado_ready($from);
        $order->update_meta_data('_nj_listo', $ready);
        $order->add_order_note('El bordado queda listo el ' . nj_personalizado_date_label($ready) . '. Después de esa fecha se entrega.');
        $order->save();
    }
}
add_action('woocommerce_order_status_processing', 'nj_personalizado_schedule');
add_action('woocommerce_order_status_completed', 'nj_personalizado_schedule');

if (!function_exists('nj_personalizado_thankyou')) {
    function nj_personalizado_thankyou($order_id) {
        if (!function_exists('wc_get_order')) {
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order instanceof WC_Order || !nj_personalizado_order_has($order)) {
            return;
        }
        $paid = function_exists('nj_order_is_dispatch') && nj_order_is_dispatch($order);
        if ($paid) {
            nj_personalizado_schedule($order_id);
            $order = wc_get_order($order_id);
            if (!$order instanceof WC_Order) {
                return;
            }
            $label = nj_personalizado_date_label($order->get_meta('_nj_listo'));
            echo '<p class="nj-order-embroider">El nombre se borda en 8 días hábiles. Queda listo el ' . esc_html($label) . ' y después de esa fecha sale hacia tu casa.</p>';
            return;
        }
        echo '<p class="nj-order-embroider">Cuando el pago quede confirmado empiezan 8 días hábiles para bordar el nombre. Después de esa fecha se entrega.</p>';
    }
}
add_action('woocommerce_thankyou', 'nj_personalizado_thankyou', 6);

if (!function_exists('nj_personalizado_cart_has')) {
    function nj_personalizado_cart_has() {
        $wc = function_exists('WC') ? WC() : null;
        $cart = ($wc instanceof WooCommerce && $wc->cart instanceof WC_Cart) ? $wc->cart : null;
        if (!$cart instanceof WC_Cart) {
            return false;
        }
        foreach ($cart->get_cart() as $item) {
            if (!empty($item['nj_nombre'])) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('nj_personalizado_checkout_notice')) {
    function nj_personalizado_checkout_notice() {
        static $shown = false;
        if ($shown || !nj_personalizado_cart_has()) {
            return '';
        }
        $shown = true;
        return nj_personalizado_warning_html();
    }
}

if (!function_exists('nj_personalizado_checkout_block')) {
    function nj_personalizado_checkout_block($content, $block) {
        if (!is_array($block) || empty($block['blockName']) || $block['blockName'] !== 'woocommerce/checkout') {
            return $content;
        }
        $notice = nj_personalizado_checkout_notice();
        return $notice === '' ? $content : $notice . $content;
    }
}
add_filter('render_block', 'nj_personalizado_checkout_block', 10, 2);

if (!function_exists('nj_personalizado_checkout_classic')) {
    function nj_personalizado_checkout_classic() {
        echo nj_personalizado_checkout_notice();
    }
}
add_action('woocommerce_before_checkout_form', 'nj_personalizado_checkout_classic', 4);

if (!function_exists('nj_personalizado_assets')) {
    function nj_personalizado_assets() {
        if (!function_exists('is_product') || !is_product() || !nj_personalizado_on(get_queried_object_id())) {
            return;
        }
        wp_enqueue_style('nj-ficha-font', get_template_directory_uri() . '/assets/fonts/vibes.css', array(), NJ_VERSION);
    }
}
add_action('wp_enqueue_scripts', 'nj_personalizado_assets', 20);
