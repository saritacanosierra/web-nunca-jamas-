<?php
/**
 * Páginas de ayuda, términos y privacidad, y textos propios de la ficha.
 */

if (!function_exists('nj_pages_mail')) {
    function nj_pages_mail() {
        return 'saritacanosierra@gmail.com';
    }
}

if (!function_exists('nj_pages_copy')) {
    function nj_pages_copy() {
        $mail = nj_pages_mail();
        $threshold = function_exists('nj_slots_data') ? (int) nj_slots_data()['bag']['free_shipping'] : 250000;
        $fee_amount = function_exists('nj_slots_data') && isset(nj_slots_data()['bag']['shipping_cost']) ? (int) nj_slots_data()['bag']['shipping_cost'] : 15000;
        $free = '$' . number_format($threshold, 0, ',', '.');
        $fee = '$' . number_format($fee_amount, 0, ',', '.');
        return array(
            'preguntas-frecuentes' => array(
                'option' => 'nj_page_preguntas',
                'title' => 'Preguntas frecuentes',
                'html' => '<p>Elige la talla en la ficha y pulsa Agregar a mi bolsa. La bolsa es el carrito de la tienda. Desde ahí pasas a pagar.</p>'
                    . '<p>Enviamos a Colombia. El domicilio es ' . $fee . '. A partir de ' . $free . ' el envío es gratis.</p>'
                    . '<p>Si la talla no ajusta, escribe a <a href="mailto:' . $mail . '">' . $mail . '</a> con el número de pedido.</p>'
                    . '<p>Las tallas de ropa son 2T, 3T, 4T, 5T y 6. Los accesorios van en talla única.</p>',
            ),
            'envios' => array(
                'option' => 'nj_page_envios',
                'title' => 'Envíos',
                'html' => '<p>Enviamos a Colombia. El pedido sale después de confirmar el pago.</p>'
                    . '<p>A partir de ' . $free . ' el envío es gratis. Por debajo de ese valor, el domicilio es ' . $fee . '.</p>'
                    . '<p>El correo de confirmación llega a la dirección que escribes al pedir.</p>',
            ),
            'cambios' => array(
                'option' => 'nj_page_cambios',
                'title' => 'Cambios y devoluciones',
                'html' => '<p>Si la talla no ajusta, escribe a <a href="mailto:' . $mail . '">' . $mail . '</a> con el número de pedido y la talla que necesitas.</p>'
                    . '<p>La prenda debe volver sin uso, con sus etiquetas. Coordinamos el cambio con lo que haya en inventario.</p>'
                    . '<p>No hay un teléfono de atención en esta página. El canal es el correo.</p>',
            ),
            'guia-de-tallas' => array(
                'option' => 'nj_page_tallas',
                'title' => 'Guía de tallas',
                'html' => '<p>La ropa se pide en 2T, 3T, 4T, 5T o 6. Elige una talla en la ficha antes de agregarla a la bolsa.</p>'
                    . '<p>Los accesorios se piden en talla única.</p>'
                    . '<p>Si dudas entre dos tallas, escribe a <a href="mailto:' . $mail . '">' . $mail . '</a> con la edad y la prenda.</p>',
            ),
            'terminos-y-condiciones' => array(
                'option' => 'nj_page_terminos',
                'title' => 'Términos y condiciones',
                'html' => '<p>Nunca Jamás vende ropa y accesorios para niños a través de esta tienda. Los precios están en pesos colombianos.</p>'
                    . '<p>El pedido queda confirmado cuando eliges la talla, completas el checkout y el medio de pago acepta el cobro. Si la prenda no está disponible, te escribimos al correo del pedido.</p>'
                    . '<p>Los envíos y los cambios se rigen por las páginas Envíos y Cambios y devoluciones.</p>',
            ),
            'politica-de-privacidad' => array(
                'option' => 'nj_page_privacidad',
                'title' => 'Política de privacidad',
                'html' => '<p>Usamos el nombre, el correo y la dirección que escribes en el checkout para enviar el pedido y avisarte su estado.</p>'
                    . '<p>El pago lo procesa el medio que eliges en el checkout. Esta tienda no pide el número de la tarjeta en sus propias pantallas.</p>'
                    . '<p>Para pedir la eliminación de esos datos, escribe a <a href="mailto:' . $mail . '">' . $mail . '</a>.</p>',
            ),
        );
    }
}

if (!function_exists('nj_pages_ensure')) {
    function nj_pages_ensure() {
        if (!function_exists('wp_insert_post')) {
            return;
        }
        if (get_option('WPLANG') !== 'es_CO') {
            update_option('WPLANG', 'es_CO');
        }
        nj_pages_shipping_rule();
        nj_pages_store_titles();
        if (get_option('nj_pages_ready') === '1') {
            return;
        }
        foreach (nj_pages_copy() as $slug => $page) {
            $existing = get_page_by_path($slug);
            $page_id = $existing ? (int) $existing->ID : 0;
            if (!$page_id) {
                $page_id = (int) wp_insert_post(array(
                    'post_title' => $page['title'],
                    'post_name' => $slug,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_content' => $page['html'],
                ));
            } elseif (trim((string) $existing->post_content) === '') {
                wp_update_post(array(
                    'ID' => $page_id,
                    'post_content' => $page['html'],
                ));
            }
            if ($page_id) {
                update_option($page['option'], $page_id);
            }
        }
        $terms = (int) get_option('nj_page_terminos');
        $privacy = (int) get_option('nj_page_privacidad');
        if ($terms && function_exists('update_option')) {
            update_option('woocommerce_terms_page_id', $terms);
        }
        if ($privacy) {
            update_option('wp_page_for_privacy_policy', $privacy);
        }
        nj_pages_shipping();
        nj_pages_product_copy();
        update_option('nj_pages_ready', '1');
    }
}
add_action('init', 'nj_pages_ensure', 20);

if (!function_exists('nj_pages_store_titles')) {
    function nj_pages_store_titles() {
        if (get_option('nj_store_titles') === '1' || !function_exists('wc_get_page_id') || !function_exists('wp_update_post')) {
            return;
        }
        $titles = array(
            'shop' => 'Tienda',
            'cart' => 'Carrito',
            'checkout' => 'Finalizar compra',
            'myaccount' => 'Mi cuenta',
        );
        foreach ($titles as $key => $title) {
            $page_id = (int) wc_get_page_id($key);
            if ($page_id > 0) {
                wp_update_post(array(
                    'ID' => $page_id,
                    'post_title' => $title,
                ));
            }
        }
        update_option('nj_store_titles', '1');
    }
}

if (!function_exists('nj_pages_shipping_rule')) {
    function nj_pages_shipping_rule() {
        if (!class_exists('WC_Shipping_Zones') || !function_exists('nj_slots_data')) {
            return;
        }
        $bag = nj_slots_data()['bag'];
        $threshold = (string) (int) $bag['free_shipping'];
        $cost = (string) (int) (isset($bag['shipping_cost']) ? $bag['shipping_cost'] : 15000);
        if (get_option('nj_shipping_min') === $threshold && get_option('nj_shipping_cost') === $cost && get_option('nj_shipping_rule') === '1') {
            return;
        }
        $done = false;
        $has_flat = false;
        foreach (WC_Shipping_Zones::get_zones() as $zone_data) {
            $matches = false;
            foreach ($zone_data['zone_locations'] as $location) {
                $type = is_object($location) ? (isset($location->type) ? $location->type : '') : '';
                $code = is_object($location) ? (isset($location->code) ? $location->code : '') : '';
                if ($type === 'country' && $code === 'CO') {
                    $matches = true;
                }
            }
            if (!$matches || empty($zone_data['zone_id'])) {
                continue;
            }
            $zone = new WC_Shipping_Zone($zone_data['zone_id']);
            $has_flat = false;
            $has_free = false;
            foreach ($zone->get_shipping_methods() as $method) {
                $live = WC_Shipping_Zones::get_shipping_method($method->get_instance_id());
                if (!$live) {
                    continue;
                }
                if ($live->id === 'free_shipping') {
                    $has_free = true;
                    $key = $live->get_instance_option_key();
                    $settings = get_option($key, array());
                    if (!is_array($settings)) {
                        $settings = array();
                    }
                    $settings['title'] = 'Envio gratis';
                    $settings['requires'] = 'min_amount';
                    $settings['min_amount'] = $threshold;
                    if (!isset($settings['ignore_discounts'])) {
                        $settings['ignore_discounts'] = 'no';
                    }
                    update_option($key, $settings);
                    $done = true;
                }
                if ($live->id === 'flat_rate') {
                    $has_flat = true;
                    $key = $live->get_instance_option_key();
                    $settings = get_option($key, array());
                    if (!is_array($settings)) {
                        $settings = array();
                    }
                    $settings['title'] = 'Envio a Colombia';
                    $settings['tax_status'] = isset($settings['tax_status']) ? $settings['tax_status'] : 'none';
                    $settings['cost'] = $cost;
                    $settings['type'] = isset($settings['type']) ? $settings['type'] : 'class';
                    update_option($key, $settings);
                    $done = true;
                }
            }
            if (!$has_free) {
                $instance = $zone->add_shipping_method('free_shipping');
                $free = WC_Shipping_Zones::get_shipping_method($instance);
                if ($free) {
                    update_option($free->get_instance_option_key(), array(
                        'title' => 'Envio gratis',
                        'requires' => 'min_amount',
                        'min_amount' => $threshold,
                        'ignore_discounts' => 'no',
                    ));
                    $done = true;
                }
            }
            if (!$has_flat) {
                $instance = $zone->add_shipping_method('flat_rate');
                $flat = WC_Shipping_Zones::get_shipping_method($instance);
                if ($flat) {
                    update_option($flat->get_instance_option_key(), array(
                        'title' => 'Envio a Colombia',
                        'tax_status' => 'none',
                        'cost' => $cost,
                        'type' => 'class',
                    ));
                }
            }
        }
        if ($done || $has_flat) {
            update_option('nj_shipping_rule', '1');
            update_option('nj_shipping_min', $threshold);
            update_option('nj_shipping_cost', $cost);
            if (function_exists('nj_pages_copy') && function_exists('wp_update_post')) {
                foreach (nj_pages_copy() as $page) {
                    if (!in_array($page['option'], array('nj_page_preguntas', 'nj_page_envios'), true)) {
                        continue;
                    }
                    $page_id = (int) get_option($page['option']);
                    if ($page_id) {
                        wp_update_post(array(
                            'ID' => $page_id,
                            'post_content' => $page['html'],
                        ));
                    }
                }
            }
            if (class_exists('WC_Cache_Helper')) {
                WC_Cache_Helper::get_transient_version('shipping', true);
            }
        }
    }
}

if (!function_exists('nj_pages_shipping')) {
    function nj_pages_shipping() {
        if (!class_exists('WC_Shipping_Zones')) {
            return;
        }
        $threshold = function_exists('nj_slots_data') ? (string) (int) nj_slots_data()['bag']['free_shipping'] : '250000';
        foreach (WC_Shipping_Zones::get_zones() as $zone) {
            foreach ($zone['zone_locations'] as $location) {
                if ($location->type === 'country' && $location->code === 'CO') {
                    return;
                }
            }
        }
        $zone = new WC_Shipping_Zone();
        $zone->set_zone_name('Colombia');
        $zone->add_location('CO', 'country');
        $zone->save();
        $instance = $zone->add_shipping_method('free_shipping');
        $method = WC_Shipping_Zones::get_shipping_method($instance);
        if ($method) {
            update_option($method->get_instance_option_key(), array(
                'title' => 'Envio gratis',
                'requires' => 'min_amount',
                'min_amount' => $threshold,
                'ignore_discounts' => 'no',
            ));
        }
    }
}

if (!function_exists('nj_pages_product_copy')) {
    function nj_pages_product_copy() {
        if (!function_exists('wc_get_products') || !function_exists('nj_slots_data')) {
            return;
        }
        $by_name = array();
        foreach (nj_slots_data()['products'] as $product) {
            $by_name[$product['name']] = $product;
        }
        $catalog = wc_get_products(array(
            'status' => 'publish',
            'limit' => 40,
            'type' => 'variable',
        ));
        foreach ($catalog as $product) {
            if (strpos((string) $product->get_sku(), 'nj-') !== 0) {
                continue;
            }
            $short = trim(wp_strip_all_tags($product->get_short_description()));
            if ($short !== '' && $short !== $product->get_name()) {
                continue;
            }
            $slot = isset($by_name[$product->get_name()]) ? $by_name[$product->get_name()] : null;
            if ($slot && !empty($slot['description'])) {
                $html = '<p>' . esc_html($slot['description']) . '</p>';
                if (!empty($slot['composition'])) {
                    $html .= '<p>' . esc_html($slot['composition']) . '</p>';
                }
            } else {
                $html = '<p>Prenda de Nunca Jamás. Elige la talla y la agregamos a tu bolsa. Enviamos a Colombia.</p>';
            }
            $product->set_short_description($html);
            $product->set_description($html);
            $product->save();
        }
    }
}

if (!function_exists('nj_shipping_label')) {
    function nj_shipping_label($label) {
        if ($label === 'Envio a Colombia') {
            return 'Envío a Colombia';
        }
        if ($label === 'Envio gratis') {
            return 'Envío gratis';
        }
        return $label;
    }
}
if (!function_exists('nj_shipping_threshold_rates')) {
    function nj_shipping_threshold_rates($rates, $package) {
        if (!function_exists('nj_slots_data') || !function_exists('WC') || !WC()->cart) {
            return $rates;
        }
        $threshold = (int) nj_slots_data()['bag']['free_shipping'];
        $total = (float) WC()->cart->get_displayed_subtotal();
        if ($total >= $threshold) {
            $free = array();
            foreach ($rates as $id => $rate) {
                if ($rate->get_method_id() === 'free_shipping') {
                    $free[$id] = $rate;
                }
            }
            return $free ? $free : $rates;
        }
        foreach ($rates as $id => $rate) {
            if ($rate->get_method_id() === 'free_shipping') {
                unset($rates[$id]);
            }
        }
        return $rates;
    }
}
if (function_exists('add_filter')) {
    add_filter('woocommerce_shipping_rate_label', 'nj_shipping_label');
    add_filter('woocommerce_package_rates', 'nj_shipping_threshold_rates', 20, 2);
}
