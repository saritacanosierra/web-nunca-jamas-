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
