<?php
/**
 * Datos de la ficha de un producto.
 */

require_once __DIR__ . '/cards.php';

if (!function_exists('nj_ficha_full')) {
    function nj_ficha_full($src) {
        $src = (string) $src;
        if ($src === '' || !function_exists('attachment_url_to_postid') || !function_exists('wp_get_attachment_image_url')) {
            return $src;
        }
        $id = (int) attachment_url_to_postid($src);
        if ($id < 1) {
            $plain = preg_replace('/-\d+x\d+(?=\.[a-z0-9]+$)/i', '', $src);
            if (is_string($plain) && $plain !== $src) {
                $id = (int) attachment_url_to_postid($plain);
            }
        }
        if ($id < 1) {
            return $src;
        }
        $full = wp_get_attachment_image_url($id, 'full');
        return $full ? $full : $src;
    }
}

if (!function_exists('nj_ficha_frames')) {
    function nj_ficha_frames($gallery) {
        $positions = array('center top', 'center', 'center 35%', 'center bottom');
        $frames = array();
        $seen = array();
        foreach ((array) $gallery as $src) {
            $src = (string) $src;
            if ($src === '' || isset($seen[$src])) {
                continue;
            }
            $seen[$src] = true;
            $frames[] = array(
                'src' => $src,
                'zoom' => nj_ficha_full($src),
                'position' => $positions[count($frames) % count($positions)],
            );
        }
        return $frames;
    }
}

if (!function_exists('nj_ficha_text')) {
    function nj_ficha_text($parts) {
        $lines = array();
        foreach ($parts as $part) {
            $part = trim((string) $part);
            if ($part !== '') {
                $lines[] = $part;
            }
        }
        return implode("\n\n", $lines);
    }
}

if (!function_exists('nj_ficha_from_card')) {
    function nj_ficha_from_card($item) {
        $item['frames'] = nj_ficha_frames(isset($item['gallery']) ? $item['gallery'] : array());
        $id = isset($item['id']) ? (int) $item['id'] : 0;
        $saved = function ($key) use ($id) {
            if ($id < 1 || !function_exists('get_post_meta')) {
                return '';
            }
            $value = get_post_meta($id, $key, true);
            return is_string($value) ? trim($value) : '';
        };
        $description = isset($item['description']) ? trim((string) $item['description']) : '';
        if ($id > 0 && function_exists('wc_get_product')) {
            $product = wc_get_product($id);
            if ($product && method_exists($product, 'get_description') && function_exists('nj_plain_text')) {
                $long = trim(nj_plain_text($product->get_description()));
                if ($long !== '') {
                    $description = $long;
                }
            }
        }
        $envios = $saved('_nj_envios');
        $cuidados = $saved('_nj_cuidados');
        $item['tabs'] = array(
            array(
                'id' => 'descripcion',
                'label' => 'Descripción',
                'text' => $description,
            ),
            array(
                'id' => 'envios',
                'label' => 'Envíos y devoluciones',
                'text' => $envios !== '' ? $envios : 'Enviamos a Colombia las prendas que ya están listas. Si la talla no ajusta, escribes y buscamos la pieza que sí le queda.',
            ),
            array(
                'id' => 'cuidados',
                'label' => 'Cuidados',
                'text' => $cuidados !== '' ? $cuidados : 'Lava la prenda al revés, en agua fría y ciclo suave. No uses blanqueador ni secadora.',
            ),
        );
        return $item;
    }
}

if (!function_exists('nj_compra_guide_html')) {
    function nj_compra_guide_html($sizes = array()) {
        $ages = array(
            '2t' => '2 años',
            '3t' => '3 años',
            '4t' => '4 años',
            '5t' => '5 años',
            '6' => '6 años',
        );
        $labels = array();
        foreach ($sizes as $size) {
            $key = function_exists('nj_menu_key') ? nj_menu_key($size) : strtolower(trim((string) $size));
            if (!isset($ages[$key])) {
                $labels = array();
                break;
            }
            $labels[] = $ages[$key];
        }
        $url = function_exists('nj_page_url') ? nj_page_url('tallas') : '';
        $link = $url ? ' <a href="' . esc_url($url) . '">Guía de tallas</a>' : '';
        if ($labels) {
            $html = '<p class="nj-guide nj-guide--ages">';
            foreach ($labels as $label) {
                $html .= '<span>' . esc_html($label) . '</span>';
            }
            return $html . '</p><p class="nj-guide">Si dudas, elige la mayor.' . $link . '</p>';
        }
        return '<p class="nj-guide">' . esc_html('2T 2 años · 3T 3 años · 4T 4 años · 5T 5 años · 6 6 años. Si dudas, elige la mayor.') . $link . '</p>';
    }
}

if (!function_exists('nj_compra_trust_html')) {
    function nj_compra_trust_html($line = false) {
        if ($line) {
            return '<p class="nj-trust nj-trust--line">Envío a Colombia · Cambio si la talla no queda · Nequi, PSE o tarjeta</p>';
        }
        $html = '<ul class="nj-trust">';
        foreach (array('Envío a Colombia', 'Cambio si la talla no queda', 'Nequi, PSE o tarjeta') as $item) {
            $html .= '<li>' . esc_html($item) . '</li>';
        }
        return $html . '</ul>';
    }
}

if (!function_exists('nj_ficha_locate')) {
    function nj_ficha_locate($key) {
        $slots = nj_slots_data();
        $lists = array($slots['products'], $slots['catalog_products']);
        $match = null;
        foreach ($lists as $list) {
            foreach ($list as $product) {
                if ($match === null) {
                    $match = $product;
                }
                if ($key !== '' && nj_menu_key($product['name']) === $key) {
                    $match = $product;
                    break 2;
                }
            }
        }
        if (!$match) {
            return null;
        }
        if (!isset($match['alt'])) {
            $match['alt'] = $match['name'];
        }
        if (!isset($match['href'])) {
            $match['href'] = nj_home_url();
        }
        return nj_ficha_from_card(nj_card_from_slot($match));
    }
}
