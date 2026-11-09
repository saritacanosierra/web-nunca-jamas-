<?php
/**
 * Páginas de ayuda, términos y privacidad, y textos propios de la ficha.
 */

if (!function_exists('nj_pages_mail')) {
    function nj_pages_mail() {
        return 'saritacanosierra@gmail.com';
    }
}

if (!function_exists('nj_pages_money')) {
    function nj_pages_money($amount) {
        return '$' . number_format((int) $amount, 0, ',', '.');
    }
}

if (!function_exists('nj_pages_faq')) {
    function nj_pages_faq() {
        $mail = nj_pages_mail();
        $threshold = function_exists('nj_slots_data') ? (int) nj_slots_data()['bag']['free_shipping'] : 250000;
        $fee_amount = function_exists('nj_slots_data') && isset(nj_slots_data()['bag']['shipping_cost']) ? (int) nj_slots_data()['bag']['shipping_cost'] : 15000;
        $free = nj_pages_money($threshold);
        $fee = nj_pages_money($fee_amount);
        $bordado = function_exists('nj_personalizado_money') && function_exists('nj_personalizado_fee')
            ? nj_personalizado_money(nj_personalizado_fee())
            : nj_pages_money(20000);
        return array(
            array(
                'q' => '¿Cómo compro una prenda?',
                'a' => 'Elige la talla en la ficha y pulsa Agregar a mi bolsa. Desde la bolsa pasas a pagar.',
            ),
            array(
                'q' => '¿Cuánto cuesta el envío?',
                'a' => 'El domicilio es ' . $fee . '. A partir de ' . $free . ' el envío es gratis.',
            ),
            array(
                'q' => '¿A dónde envían?',
                'a' => 'Enviamos a toda Colombia.',
            ),
            array(
                'q' => '¿Cuándo sale mi pedido?',
                'a' => 'Sale después de confirmar el pago. El aviso llega al correo que escribes al pedir.',
            ),
            array(
                'q' => '¿Qué tallas manejan?',
                'a' => 'La ropa va en 2T, 3T, 4T, 5T y 6. Los accesorios van en talla única. Si dudas entre dos, escribe la edad y la prenda a ' . $mail . '.',
            ),
            array(
                'q' => '¿Qué hago si la talla no le queda?',
                'a' => 'Escribe a ' . $mail . ' con el número de pedido y la talla que necesitas. La prenda debe volver sin uso y con sus etiquetas. El cambio se hace con lo que haya en inventario.',
            ),
            array(
                'q' => '¿Cómo puedo pagar?',
                'a' => 'Pagas en pesos con Nequi, tarjeta o PSE. La tienda no pide el número de la tarjeta en su propia pantalla.',
            ),
            array(
                'q' => '¿Puedo bordar el nombre?',
                'a' => 'Si la prenda tiene la opción Personalizado, escribes el nombre, de hasta 12 letras, y eliges Cursiva o Imprenta. El bordado suma ' . esc_html($bordado) . '. Desde el pago cuentan 8 días hábiles de elaboración.',
            ),
            array(
                'q' => '¿Puedo corregir el nombre después de pagar?',
                'a' => 'Revisa el nombre antes de pagar. Después de pagado no se hacen cambios ni devoluciones del bordado.',
            ),
            array(
                'q' => '¿Cómo los contacto?',
                'a' => 'Escríbenos a ' . $mail . '.',
            ),
        );
    }
}

if (!function_exists('nj_pages_faq_html')) {
    function nj_pages_faq_html() {
        $html = '';
        foreach (nj_pages_faq() as $item) {
            $html .= '<h2>' . esc_html($item['q']) . '</h2><p>' . $item['a'] . '</p>';
        }
        return $html;
    }
}

if (!function_exists('nj_pages_mail_html')) {
    function nj_pages_mail_html() {
        $mail = nj_pages_mail();
        return '<a href="mailto:' . esc_attr($mail) . '">' . esc_html($mail) . '</a>';
    }
}

if (!function_exists('nj_pages_help_default')) {
    function nj_pages_help_default($key) {
        $mail = nj_pages_mail();
        $threshold = function_exists('nj_slots_data') ? (int) nj_slots_data()['bag']['free_shipping'] : 250000;
        $fee_amount = function_exists('nj_slots_data') && isset(nj_slots_data()['bag']['shipping_cost']) ? (int) nj_slots_data()['bag']['shipping_cost'] : 15000;
        $free = nj_pages_money($threshold);
        $fee = nj_pages_money($fee_amount);
        if ($key === 'preguntas') {
            return array(
                'lead' => 'Toca la pregunta que te interesa.',
                'items' => nj_pages_faq(),
            );
        }
        if ($key === 'envios') {
            return array(
                'lead' => 'El pedido viaja a la dirección que escribes al pagar, en cualquier ciudad de Colombia.',
                'facts' => array(
                    array('label' => 'Llega a', 'value' => 'Toda Colombia'),
                    array('label' => 'Domicilio', 'value' => $fee),
                    array('label' => 'Envío gratis', 'value' => 'Desde ' . $free),
                ),
                'steps_title' => 'Qué pasa con tu pedido',
                'steps' => array(
                    'Pagas en pesos con Nequi, tarjeta o PSE.',
                    'Te escribimos al correo cuando lo estamos empacando.',
                    'Te escribimos otra vez cuando sale el envío.',
                ),
                'note' => 'Si la prenda lleva nombre bordado, primero se elabora. Desde el pago cuentan 8 días hábiles y después de esa fecha se entrega.',
                'items' => array(
                    array(
                        'q' => '¿Puedo corregir la dirección?',
                        'a' => 'Si el pedido todavía no ha salido, escribe a ' . $mail . ' con el número de pedido y la dirección correcta.',
                    ),
                    array(
                        'q' => '¿El domicilio se cobra por prenda?',
                        'a' => 'No. Es un solo domicilio de ' . $fee . ' por pedido. Desde ' . $free . ' el envío es gratis.',
                    ),
                ),
            );
        }
        if ($key === 'cambios') {
            return array(
                'lead' => 'Si la talla no le queda, el camino es cambiarla por otra que tengamos. El nombre bordado, después de pagado, no se cambia ni se devuelve.',
                'steps_title' => 'Cómo pedir el cambio de talla',
                'steps' => array(
                    'Escribe a ' . $mail . ' con el número de pedido y la talla que necesitas.',
                    'La prenda debe volver sin uso y con sus etiquetas.',
                    'Te proponemos el cambio con la talla que haya en inventario.',
                ),
                'note' => 'Revisa el nombre antes de pagar. Después de pagado no se hacen cambios ni devoluciones del bordado.',
                'items' => array(
                    array(
                        'q' => '¿Devuelven el dinero?',
                        'a' => 'Una talla que no ajusta se cambia por otra pieza disponible. El bordado ya pagado no tiene cambio ni devolución.',
                    ),
                    array(
                        'q' => '¿Hay un teléfono?',
                        'a' => 'El canal es el correo ' . $mail . '. En esta página no hay un teléfono de atención.',
                    ),
                ),
            );
        }
        if ($key === 'tallas') {
            return array(
                'lead' => 'La talla se elige en la ficha, antes de agregar la prenda a la bolsa. La tabla dice para quién orienta cada una.',
                'table' => array(
                    array('Talla', 'Orienta a'),
                    array('2T', 'Cerca de los 2 años'),
                    array('3T', 'Cerca de los 3 años'),
                    array('4T', 'Cerca de los 4 años'),
                    array('5T', 'Cerca de los 5 años'),
                    array('6', 'Cerca de los 6 años'),
                    array('Única', 'Accesorios'),
                ),
                'note' => 'La ficha de cada prenda muestra solo las tallas que esa pieza tiene. Si no aparece, no se puede pedir.',
                'items' => array(
                    array(
                        'q' => '¿Está entre dos tallas?',
                        'a' => 'Elige la mayor. Si quieres confirmar, escribe la edad y la prenda a ' . $mail . ' antes de pagar.',
                    ),
                    array(
                        'q' => '¿Los accesorios llevan talla?',
                        'a' => 'No. Los accesorios se piden en talla única.',
                    ),
                ),
            );
        }
        return null;
    }
}

if (!function_exists('nj_pages_text')) {
    function nj_pages_text($value, $limit) {
        $value = function_exists('sanitize_textarea_field') ? sanitize_textarea_field($value) : trim(strip_tags((string) $value));
        $value = preg_replace("/\s+/", ' ', $value);
        $limit = (int) $limit;
        if ($limit < 1) {
            $limit = 180;
        }
        return function_exists('mb_substr') ? mb_substr($value, 0, $limit) : substr($value, 0, $limit);
    }
}

if (!function_exists('nj_pages_rich')) {
    function nj_pages_rich($text) {
        $safe = esc_html((string) $text);
        $linked = preg_replace('/([A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,})/', '<a href="mailto:$1">$1</a>', $safe);
        return is_string($linked) ? $linked : $safe;
    }
}

if (!function_exists('nj_pages_help_clean')) {
    function nj_pages_help_clean($key, $raw) {
        if (!is_array($raw)) {
            $raw = array();
        }
        $help = array(
            'lead' => nj_pages_text(isset($raw['lead']) ? $raw['lead'] : '', 320),
            'image' => max(0, (int) (isset($raw['image']) ? $raw['image'] : 0)),
            'facts' => array(),
            'steps_title' => nj_pages_text(isset($raw['steps_title']) ? $raw['steps_title'] : '', 80),
            'steps' => array(),
            'note' => nj_pages_text(isset($raw['note']) ? $raw['note'] : '', 400),
            'table' => array(),
            'items' => array(),
        );
        $facts = isset($raw['facts']) && is_array($raw['facts']) ? $raw['facts'] : array();
        foreach ($facts as $fact) {
            if (!is_array($fact)) {
                continue;
            }
            $label = nj_pages_text(isset($fact['label']) ? $fact['label'] : '', 40);
            $value = nj_pages_text(isset($fact['value']) ? $fact['value'] : '', 80);
            if ($label === '' || $value === '') {
                continue;
            }
            $help['facts'][] = array('label' => $label, 'value' => $value);
            if (count($help['facts']) >= 6) {
                break;
            }
        }
        $steps = isset($raw['steps']) && is_array($raw['steps']) ? $raw['steps'] : array();
        foreach ($steps as $step) {
            $line = nj_pages_text($step, 240);
            if ($line === '') {
                continue;
            }
            $help['steps'][] = $line;
            if (count($help['steps']) >= 8) {
                break;
            }
        }
        $table = isset($raw['table']) && is_array($raw['table']) ? $raw['table'] : array();
        foreach ($table as $row) {
            if (!is_array($row)) {
                continue;
            }
            $left = nj_pages_text(isset($row['size']) ? $row['size'] : '', 40);
            $right = nj_pages_text(isset($row['who']) ? $row['who'] : '', 80);
            if ($left === '' || $right === '') {
                continue;
            }
            $help['table'][] = array($left, $right);
            if (count($help['table']) >= 12) {
                break;
            }
        }
        $items = isset($raw['items']) && is_array($raw['items']) ? $raw['items'] : array();
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $question = nj_pages_text(isset($item['q']) ? $item['q'] : '', 140);
            $answer = nj_pages_text(isset($item['a']) ? $item['a'] : '', 500);
            if ($question === '' || $answer === '') {
                continue;
            }
            $help['items'][] = array('q' => $question, 'a' => $answer);
            if (count($help['items']) >= 12) {
                break;
            }
        }
        if ($key !== 'envios') {
            $help['facts'] = array();
        }
        if ($key !== 'tallas') {
            $help['table'] = array();
        }
        if ($key === 'preguntas' || $key === 'tallas') {
            $help['steps_title'] = '';
            $help['steps'] = array();
        }
        if ($key === 'preguntas') {
            $help['note'] = '';
        }
        return $help;
    }
}

if (!function_exists('nj_pages_help')) {
    function nj_pages_help($key) {
        $saved = function_exists('get_option') ? get_option('nj_ayuda', array()) : array();
        if (is_array($saved) && isset($saved[$key]) && is_array($saved[$key])) {
            $help = nj_pages_help_clean($key, $saved[$key]);
        } else {
            $help = nj_pages_help_default($key);
        }
        if (!is_array($help)) {
            return null;
        }
        $help = $help + array(
            'lead' => '',
            'image' => 0,
            'facts' => array(),
            'steps_title' => '',
            'steps' => array(),
            'note' => '',
            'table' => array(),
            'items' => array(),
        );
        $picture = nj_pages_help_picture($key, $help['image']);
        $help['image_url'] = $picture['url'];
        $help['image_position'] = $picture['position'];
        return $help;
    }
}

if (!function_exists('nj_pages_help_picture')) {
    function nj_pages_help_picture($key, $image_id) {
        $image_id = (int) $image_id;
        if ($image_id > 0 && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($image_id, 'large');
            if (is_string($url) && $url !== '') {
                return array('url' => $url, 'position' => 'center');
            }
        }
        $files = array(
            'preguntas' => array('p-vestido.jpg', 'center'),
            'envios' => array('p-blusa.png', 'center'),
            'cambios' => array('p-vestido.jpg', 'center'),
            'tallas' => array('p-blusa.png', 'center'),
        );
        $file = isset($files[$key]) ? $files[$key] : $files['cambios'];
        return array(
            'url' => function_exists('nj_img') ? nj_img($file[0]) : '',
            'position' => $file[1],
        );
    }
}

if (!function_exists('nj_pages_help_store')) {
    function nj_pages_help_store($posted) {
        $keys = array(
            'preguntas' => 'nj_page_preguntas',
            'envios' => 'nj_page_envios',
            'cambios' => 'nj_page_cambios',
            'tallas' => 'nj_page_tallas',
        );
        $clean = array();
        foreach ($keys as $key => $option) {
            $raw = isset($posted[$key]) && is_array($posted[$key]) ? $posted[$key] : array();
            $clean[$key] = nj_pages_help_clean($key, $raw);
        }
        if (function_exists('update_option')) {
            update_option('nj_ayuda', $clean);
        }
        if (!function_exists('wp_update_post') || !function_exists('get_option')) {
            return;
        }
        foreach ($keys as $key => $option) {
            $page_id = (int) get_option($option);
            if ($page_id > 0) {
                wp_update_post(array(
                    'ID' => $page_id,
                    'post_content' => nj_pages_help_html($key),
                ));
            }
        }
    }
}

if (!function_exists('nj_pages_help_html')) {
    function nj_pages_help_html($key) {
        $help = nj_pages_help($key);
        if (!is_array($help)) {
            return '';
        }
        $html = '<p>' . esc_html($help['lead']) . '</p>';
        if (!empty($help['facts'])) {
            foreach ($help['facts'] as $fact) {
                $html .= '<p><strong>' . esc_html($fact['label']) . '.</strong> ' . esc_html($fact['value']) . '</p>';
            }
        }
        if (!empty($help['table'])) {
            $html .= '<table><tbody>';
            foreach ($help['table'] as $index => $row) {
                $tag = $index === 0 ? 'th' : 'td';
                $html .= '<tr><' . $tag . '>' . esc_html($row[0]) . '</' . $tag . '><' . $tag . '>' . esc_html($row[1]) . '</' . $tag . '></tr>';
            }
            $html .= '</tbody></table>';
        }
        if (!empty($help['steps'])) {
            $html .= '<ol>';
            foreach ($help['steps'] as $step) {
                $html .= '<li>' . nj_pages_rich($step) . '</li>';
            }
            $html .= '</ol>';
        }
        if (!empty($help['note'])) {
            $html .= '<p>' . esc_html($help['note']) . '</p>';
        }
        if (!empty($help['items'])) {
            foreach ($help['items'] as $item) {
                $html .= '<h2>' . esc_html($item['q']) . '</h2><p>' . nj_pages_rich($item['a']) . '</p>';
            }
        }
        return $html;
    }
}

if (!function_exists('nj_pages_help_current')) {
    function nj_pages_help_current() {
        if (!function_exists('get_the_ID')) {
            return null;
        }
        $id = (int) get_the_ID();
        $options = array(
            'nj_page_preguntas' => 'preguntas',
            'nj_page_envios' => 'envios',
            'nj_page_cambios' => 'cambios',
            'nj_page_tallas' => 'tallas',
        );
        if ($id > 0 && function_exists('get_option')) {
            foreach ($options as $option => $key) {
                if ($id === (int) get_option($option)) {
                    return nj_pages_help($key);
                }
            }
        }
        $slugs = array(
            'preguntas-frecuentes' => 'preguntas',
            'envios' => 'envios',
            'cambios' => 'cambios',
            'guia-de-tallas' => 'tallas',
        );
        if (function_exists('is_page')) {
            foreach ($slugs as $slug => $key) {
                if (is_page($slug)) {
                    return nj_pages_help($key);
                }
            }
        }
        return null;
    }
}

if (!function_exists('nj_pages_copy')) {
    function nj_pages_copy() {
        $mail = nj_pages_mail();
        return array(
            'preguntas-frecuentes' => array(
                'option' => 'nj_page_preguntas',
                'title' => 'Preguntas frecuentes',
                'html' => nj_pages_help_html('preguntas'),
            ),
            'envios' => array(
                'option' => 'nj_page_envios',
                'title' => 'Envíos',
                'html' => nj_pages_help_html('envios'),
            ),
            'cambios' => array(
                'option' => 'nj_page_cambios',
                'title' => 'Cambios y devoluciones',
                'html' => nj_pages_help_html('cambios'),
            ),
            'guia-de-tallas' => array(
                'option' => 'nj_page_tallas',
                'title' => 'Guía de tallas',
                'html' => nj_pages_help_html('tallas'),
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

if (!function_exists('nj_pages_faq_sync')) {
    function nj_pages_faq_sync() {
        if (!function_exists('get_option') || !function_exists('wp_update_post') || get_option('nj_faq_view') === '2') {
            return;
        }
        $page_id = (int) get_option('nj_page_preguntas');
        if ($page_id < 1 && function_exists('get_page_by_path')) {
            $existing = get_page_by_path('preguntas-frecuentes');
            $page_id = ($existing instanceof WP_Post) ? (int) $existing->ID : 0;
        }
        if ($page_id > 0) {
            wp_update_post(array(
                'ID' => $page_id,
                'post_content' => nj_pages_faq_html(),
            ));
        }
        update_option('nj_faq_view', '2');
    }
}
add_action('init', 'nj_pages_faq_sync', 21);

if (!function_exists('nj_pages_help_sync')) {
    function nj_pages_help_sync() {
        if (!function_exists('get_option') || !function_exists('wp_update_post') || get_option('nj_help_view') === '1') {
            return;
        }
        $pages = array(
            'preguntas-frecuentes' => array('nj_page_preguntas', 'preguntas'),
            'envios' => array('nj_page_envios', 'envios'),
            'cambios' => array('nj_page_cambios', 'cambios'),
            'guia-de-tallas' => array('nj_page_tallas', 'tallas'),
        );
        foreach ($pages as $slug => $page) {
            $page_id = (int) get_option($page[0]);
            if ($page_id < 1 && function_exists('get_page_by_path')) {
                $existing = get_page_by_path($slug);
                $page_id = ($existing instanceof WP_Post) ? (int) $existing->ID : 0;
            }
            if ($page_id > 0) {
                wp_update_post(array(
                    'ID' => $page_id,
                    'post_content' => nj_pages_help_html($page[1]),
                ));
            }
        }
        update_option('nj_help_view', '1');
    }
}
add_action('init', 'nj_pages_help_sync', 22);

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
        $method = false;
        if ($instance) {
            $found = WC_Shipping_Zones::get_shipping_method($instance);
            if (is_object($found)) {
                $method = $found;
            }
        }
        if (is_object($method)) {
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
if (!function_exists('nj_shipping_is_free')) {
    function nj_shipping_is_free($merchandise, $threshold) {
        return (float) $merchandise >= (int) $threshold;
    }
}

if (!function_exists('nj_shipping_threshold_rates')) {
    function nj_shipping_threshold_rates($rates, $package) {
        if (!function_exists('nj_slots_data') || !function_exists('WC') || !WC()->cart) {
            return $rates;
        }
        $threshold = (int) nj_slots_data()['bag']['free_shipping'];
        $total = (float) WC()->cart->get_displayed_subtotal();
        if (nj_shipping_is_free($total, $threshold)) {
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
