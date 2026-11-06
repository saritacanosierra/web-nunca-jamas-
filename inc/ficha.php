<?php
/**
 * Datos de la ficha de un producto.
 */

require_once __DIR__ . '/cards.php';

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
        $item['tabs'] = array(
            array(
                'id' => 'descripcion',
                'label' => 'Descripción',
                'text' => nj_ficha_text(array(
                    isset($item['description']) ? $item['description'] : '',
                    isset($item['composition']) ? $item['composition'] : '',
                    isset($item['note']) ? $item['note'] : '',
                )),
            ),
            array(
                'id' => 'envios',
                'label' => 'Envíos y devoluciones',
                'text' => 'Enviamos a Colombia las prendas que ya están listas. Si la talla no ajusta, escribes y buscamos la pieza que sí le queda.',
            ),
            array(
                'id' => 'cuidados',
                'label' => 'Cuidados',
                'text' => 'Lava la prenda al revés, en agua fría y ciclo suave. No uses blanqueador ni secadora.',
            ),
            array(
                'id' => 'origen',
                'label' => 'Origen',
                'text' => 'Nunca Jamás. Ropa para niñas, con envíos a Colombia.',
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
