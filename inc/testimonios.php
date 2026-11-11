<?php
/**
 * Reseñas de la ficha de Google. La clave y el Place ID se guardan en la tienda.
 */

if (!function_exists('nj_testimonios_settings')) {
    function nj_testimonios_settings() {
        $saved = function_exists('get_option') ? get_option('nj_testimonios', array()) : array();
        if (!is_array($saved)) {
            $saved = array();
        }
        $place = isset($saved['place']) ? (string) $saved['place'] : '';
        if ($place === '') {
            $place = 'ChIJk-HQnykpRI4RfonSKdo2qUI';
        }
        $guardado = isset($saved['manual']) && is_array($saved['manual']);
        return array(
            'enabled' => $guardado ? (isset($saved['enabled']) && $saved['enabled'] === 'yes') : true,
            'place' => $place,
            'key' => isset($saved['key']) ? (string) $saved['key'] : '',
            'guardado' => $guardado,
        );
    }
}

if (!function_exists('nj_testimonios_place_en_texto')) {
    function nj_testimonios_place_en_texto($raw) {
        $raw = rawurldecode((string) $raw);
        if (preg_match('/[?&]placeid=([A-Za-z0-9_-]+)/i', $raw, $found)) {
            return substr($found[1], 0, 180);
        }
        if (preg_match('/\b(ChIJ[A-Za-z0-9_-]{8,})\b/', $raw, $found)) {
            return $found[1];
        }
        return '';
    }
}

if (!function_exists('nj_testimonios_place')) {
    function nj_testimonios_place($raw) {
        $raw = trim((string) $raw);
        $found = nj_testimonios_place_en_texto($raw);
        if ($found !== '') {
            return $found;
        }
        if (!preg_match('#^https://#i', $raw) || !function_exists('wp_remote_get')) {
            $raw = preg_replace('/[^A-Za-z0-9_-]/', '', $raw);
            return substr($raw, 0, 180);
        }
        $url = $raw;
        for ($step = 0; $step < 5; $step++) {
            $response = wp_remote_get($url, array('timeout' => 8, 'redirection' => 0));
            if (is_wp_error($response)) {
                break;
            }
            $location = '';
            if (function_exists('wp_remote_retrieve_header')) {
                $location = (string) wp_remote_retrieve_header($response, 'location');
            } elseif (is_array($response) && isset($response['headers']['location'])) {
                $location = (string) $response['headers']['location'];
            }
            if ($location === '') {
                break;
            }
            $found = nj_testimonios_place_en_texto($location);
            if ($found !== '') {
                return $found;
            }
            $url = $location;
        }
        return '';
    }
}

if (!function_exists('nj_testimonios_https')) {
    function nj_testimonios_https($url) {
        $url = trim((string) $url);
        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        }
        if (!preg_match('#^https://#i', $url)) {
            return '';
        }
        return $url;
    }
}

if (!function_exists('nj_testimonios_aviso')) {
    function nj_testimonios_aviso($message = null) {
        if (!function_exists('get_option')) {
            return '';
        }
        if ($message === null) {
            $saved = get_option('nj_testimonios_aviso', '');
            return is_string($saved) ? $saved : '';
        }
        $message = trim(wp_strip_all_tags((string) $message));
        $message = preg_replace('/AIza[0-9A-Za-z_-]+/', '', $message);
        if (function_exists('mb_substr')) {
            $message = mb_substr($message, 0, 240);
        } else {
            $message = substr($message, 0, 240);
        }
        $current = get_option('nj_testimonios_aviso', '');
        if ($current !== $message) {
            update_option('nj_testimonios_aviso', $message, false);
        }
        return $message;
    }
}

if (!function_exists('nj_testimonios_error_de')) {
    function nj_testimonios_error_de($response) {
        if (is_wp_error($response)) {
            return $response->get_error_message();
        }
        $code = function_exists('wp_remote_retrieve_response_code') ? (int) wp_remote_retrieve_response_code($response) : 0;
        $body = json_decode((string) wp_remote_retrieve_body($response), true);
        $message = '';
        if (is_array($body)) {
            if (isset($body['error']['message'])) {
                $message = (string) $body['error']['message'];
            } elseif (isset($body['error_message'])) {
                $message = (string) $body['error_message'];
            } elseif (isset($body['status']) && $body['status'] !== 'OK') {
                $message = (string) $body['status'];
            }
        }
        $message = trim($message);
        if ($message === '') {
            $message = $code > 0 ? 'HTTP ' . $code : 'Sin respuesta de Google';
        }
        return $message;
    }
}

if (!function_exists('nj_testimonios_pieza')) {
    function nj_testimonios_pieza($name, $text, $rating, $when, $href, $photo) {
        $name = trim(wp_strip_all_tags((string) $name));
        $text = trim(wp_strip_all_tags((string) $text));
        if ($name === '' && $text === '') {
            return null;
        }
        if ($name === '') {
            $name = 'Cliente de Google';
        }
        $rating = (int) $rating;
        if ($rating < 1) {
            $rating = 5;
        }
        if ($rating > 5) {
            $rating = 5;
        }
        return array(
            'name' => $name,
            'text' => $text,
            'rating' => $rating,
            'when' => trim(wp_strip_all_tags((string) $when)),
            'href' => nj_testimonios_https($href),
            'photo' => nj_testimonios_https($photo),
        );
    }
}

if (!function_exists('nj_testimonios_referer')) {
    function nj_testimonios_referer() {
        $home = function_exists('home_url') ? home_url('/') : 'https://nuncajamasropa.com/';
        return $home !== '' ? $home : 'https://nuncajamasropa.com/';
    }
}

if (!function_exists('nj_testimonios_desde_nuevo')) {
    function nj_testimonios_desde_nuevo($body) {
        if (!is_array($body) || empty($body['reviews']) || !is_array($body['reviews'])) {
            return array();
        }
        $items = array();
        foreach ($body['reviews'] as $review) {
            if (!is_array($review)) {
                continue;
            }
            $text = '';
            if (isset($review['text']['text'])) {
                $text = (string) $review['text']['text'];
            } elseif (isset($review['text']) && is_string($review['text'])) {
                $text = $review['text'];
            } elseif (isset($review['originalText']['text'])) {
                $text = (string) $review['originalText']['text'];
            }
            $author = isset($review['authorAttribution']) && is_array($review['authorAttribution']) ? $review['authorAttribution'] : array();
            $item = nj_testimonios_pieza(
                isset($author['displayName']) ? $author['displayName'] : '',
                $text,
                isset($review['rating']) ? $review['rating'] : 5,
                isset($review['relativePublishTimeDescription']) ? $review['relativePublishTimeDescription'] : '',
                isset($author['uri']) ? $author['uri'] : '',
                isset($author['photoUri']) ? $author['photoUri'] : ''
            );
            if ($item) {
                $items[] = $item;
            }
            if (count($items) >= 5) {
                break;
            }
        }
        if (!$items) {
            return array();
        }
        $title = isset($body['displayName']['text']) ? trim(wp_strip_all_tags((string) $body['displayName']['text'])) : '';
        return array(
            'name' => $title,
            'rating' => isset($body['rating']) ? (float) $body['rating'] : 0,
            'total' => isset($body['userRatingCount']) ? (int) $body['userRatingCount'] : count($items),
            'maps' => nj_testimonios_https(isset($body['googleMapsUri']) ? $body['googleMapsUri'] : ''),
            'items' => $items,
        );
    }
}

if (!function_exists('nj_testimonios_desde_clasico')) {
    function nj_testimonios_desde_clasico($body) {
        if (!is_array($body) || empty($body['result']['reviews']) || !is_array($body['result']['reviews'])) {
            return array();
        }
        $result = $body['result'];
        $items = array();
        foreach ($result['reviews'] as $review) {
            if (!is_array($review)) {
                continue;
            }
            $item = nj_testimonios_pieza(
                isset($review['author_name']) ? $review['author_name'] : '',
                isset($review['text']) ? $review['text'] : '',
                isset($review['rating']) ? $review['rating'] : 5,
                isset($review['relative_time_description']) ? $review['relative_time_description'] : '',
                isset($review['author_url']) ? $review['author_url'] : '',
                isset($review['profile_photo_url']) ? $review['profile_photo_url'] : ''
            );
            if ($item) {
                $items[] = $item;
            }
            if (count($items) >= 5) {
                break;
            }
        }
        if (!$items) {
            return array();
        }
        return array(
            'name' => isset($result['name']) ? trim(wp_strip_all_tags((string) $result['name'])) : '',
            'rating' => isset($result['rating']) ? (float) $result['rating'] : 0,
            'total' => isset($result['user_ratings_total']) ? (int) $result['user_ratings_total'] : count($items),
            'maps' => nj_testimonios_https(isset($result['url']) ? $result['url'] : ''),
            'items' => $items,
        );
    }
}

if (!function_exists('nj_testimonios_fetch')) {
    function nj_testimonios_fetch($place, $key) {
        if ($place === '' || $key === '' || !function_exists('wp_remote_get')) {
            return array();
        }
        $referer = nj_testimonios_referer();
        $pedir = function ($with_referer) use ($place, $key, $referer) {
            $headers = array(
                'X-Goog-Api-Key' => $key,
                'X-Goog-FieldMask' => 'displayName,rating,userRatingCount,googleMapsUri,reviews',
            );
            if ($with_referer && $referer !== '') {
                $headers['Referer'] = $referer;
            }
            return wp_remote_get('https://places.googleapis.com/v1/places/' . rawurlencode($place) . '?languageCode=es', array(
                'timeout' => 12,
                'headers' => $headers,
            ));
        };
        $nuevo = $pedir(false);
        if (!is_wp_error($nuevo)) {
            $fallo = nj_testimonios_error_de($nuevo);
            if (stripos($fallo, 'referer') !== false || stripos($fallo, 'referrer') !== false) {
                $nuevo = $pedir(true);
            }
        }
        $paquete = array();
        $aviso = '';
        if (!is_wp_error($nuevo) && function_exists('wp_remote_retrieve_response_code')) {
            $code = (int) wp_remote_retrieve_response_code($nuevo);
            $body = json_decode((string) wp_remote_retrieve_body($nuevo), true);
            if ($code >= 200 && $code < 300) {
                $paquete = nj_testimonios_desde_nuevo($body);
            }
        }
        if (!$paquete) {
            $aviso = nj_testimonios_error_de($nuevo);
            $clasico = wp_remote_get('https://maps.googleapis.com/maps/api/place/details/json?place_id=' . rawurlencode($place) . '&fields=name,rating,user_ratings_total,url,reviews&language=es&reviews_sort=newest&key=' . rawurlencode($key), array(
                'timeout' => 12,
                'headers' => array('Referer' => $referer),
            ));
            if (!is_wp_error($clasico)) {
                $body = json_decode((string) wp_remote_retrieve_body($clasico), true);
                $paquete = nj_testimonios_desde_clasico(is_array($body) ? $body : array());
                if (!$paquete) {
                    $segundo = nj_testimonios_error_de($clasico);
                    if ($segundo !== '' && $segundo !== 'OK' && $segundo !== 'HTTP 200') {
                        $aviso = $segundo;
                    }
                }
            } else {
                $aviso = nj_testimonios_error_de($clasico);
            }
        }
        if ($paquete) {
            nj_testimonios_aviso('');
            return $paquete;
        }
        if ($aviso === '' || $aviso === 'OK' || $aviso === 'HTTP 200') {
            $aviso = 'Google no devolvió reseñas de esta ficha.';
        }
        nj_testimonios_aviso($aviso);
        return array();
    }
}

if (!function_exists('nj_testimonios_base')) {
    function nj_testimonios_base() {
        return array(
            array('name' => 'MT', 'when' => '8 ene 2025', 'text' => 'Ropa de calidad, personal humano, atención. Excelente experiencia de compra.'),
            array('name' => 'Erika Clemencia Ramirez Blandon', 'when' => '27 ene 2024', 'text' => 'Conozco a Lili su creadora y a Sarita su hija quienes están al frente de la tienda, además de ser unas bellas personas.'),
            array('name' => 'Marisol', 'when' => 'Hace 50 semanas', 'text' => 'Me encanta la propuesta que tienen de vestuario para las niñas que les hace ver bellas y sin desfasarse de su edad.'),
            array('name' => 'sandra ospina', 'when' => 'Hace 50 semanas', 'text' => 'Les recomiendo, la tienda de ropa para niños más hermosa que he podido encontrar.'),
            array('name' => 'valentina jaramillo', 'when' => 'Hace 50 semanas', 'text' => 'Me encanta la calidad, el diseño, la atención 10 de 10, y los precios inigualables.'),
            array('name' => 'Marcela Ospina', 'when' => 'Hace 50 semanas', 'text' => 'Hermosa total de mi gusto, muy cumplidas mi hija feliz.'),
            array('name' => 'Juan David Romero', 'when' => '8 ene 2025', 'text' => 'Muy buena calidad, y muy cumplidos, muchas gracias.'),
            array('name' => 'maria José baquero Ospina', 'when' => 'Hace 50 semanas', 'text' => 'La ropa es de una calidad muy buena.'),
            array('name' => 'Andrės Duque', 'when' => '9 dic 2021', 'text' => 'Me gustan mucho sus diseños y la calidad.'),
        );
    }
}

if (!function_exists('nj_testimonios_corte')) {
    function nj_testimonios_corte($value, $limit) {
        $value = function_exists('sanitize_textarea_field') ? sanitize_textarea_field($value) : trim(strip_tags((string) $value));
        if (function_exists('mb_substr')) {
            return trim(mb_substr($value, 0, $limit));
        }
        return trim(substr($value, 0, $limit));
    }
}

if (!function_exists('nj_testimonios_foto')) {
    function nj_testimonios_foto($id) {
        $id = (int) $id;
        if ($id < 1 || !function_exists('wp_get_attachment_image_url')) {
            return '';
        }
        $url = wp_get_attachment_image_url($id, 'thumbnail');
        return is_string($url) ? $url : '';
    }
}

if (!function_exists('nj_testimonios_fila')) {
    function nj_testimonios_fila($row) {
        $row = is_array($row) ? $row : array();
        $name = nj_testimonios_corte(isset($row['name']) ? $row['name'] : '', 80);
        $text = nj_testimonios_corte(isset($row['text']) ? $row['text'] : '', 500);
        if ($name === '' || $text === '') {
            return null;
        }
        $id = isset($row['image_id']) ? (int) $row['image_id'] : 0;
        if ($id > 0 && function_exists('wp_attachment_is_image') && !wp_attachment_is_image($id)) {
            $id = 0;
        }
        $photo = nj_testimonios_foto($id);
        if ($photo === '' && isset($row['photo'])) {
            $photo = nj_testimonios_https($row['photo']);
        }
        return array(
            'name' => $name,
            'text' => $text,
            'when' => nj_testimonios_corte(isset($row['when']) ? $row['when'] : '', 40),
            'rating' => 5,
            'image_id' => $id,
            'photo' => $photo,
            'href' => '',
        );
    }
}

if (!function_exists('nj_testimonios_guardados')) {
    function nj_testimonios_guardados() {
        $saved = function_exists('get_option') ? get_option('nj_testimonios', array()) : array();
        $rows = (is_array($saved) && isset($saved['manual']) && is_array($saved['manual'])) ? $saved['manual'] : nj_testimonios_base();
        $items = array();
        foreach ($rows as $row) {
            $item = nj_testimonios_fila($row);
            if ($item) {
                $items[] = $item;
            }
            if (count($items) >= 20) {
                break;
            }
        }
        return $items;
    }
}

if (!function_exists('nj_testimonios_lista')) {
    function nj_testimonios_lista() {
        $settings = nj_testimonios_settings();
        if (empty($settings['enabled'])) {
            return array();
        }
        $items = nj_testimonios_guardados();
        if (!$items) {
            return array();
        }
        return array(
            'items' => $items,
            'rating' => 5,
            'total' => count($items),
            'maps' => 'https://www.google.com/maps/search/?api=1&query_place_id=' . rawurlencode($settings['place']),
        );
    }
}

if (!function_exists('nj_testimonio_articulo')) {
    function nj_testimonio_articulo($resena) {
        $resena = is_array($resena) ? $resena : array();
        $name = isset($resena['name']) ? (string) $resena['name'] : '';
        $text = isset($resena['text']) ? (string) $resena['text'] : '';
        $when = isset($resena['when']) ? (string) $resena['when'] : '';
        $photo = isset($resena['photo']) ? (string) $resena['photo'] : '';
        if ($name === '' || $text === '') {
            return;
        }
        $letra = function_exists('mb_substr') ? mb_substr($name, 0, 1) : substr($name, 0, 1);
        $colores = array('#1a73e8', '#e37400', '#188038', '#a142f4', '#d93025', '#1967d2', '#c5221f');
        $color = $colores[abs((int) crc32($name)) % count($colores)];
        echo '<article class="testimonio">';
        echo '<header class="testimonio__quien">';
        if ($photo !== '') {
            echo '<img src="' . esc_url($photo) . '" alt="" width="40" height="40" loading="lazy">';
        } else {
            echo '<span class="testimonio__letra" style="background:' . esc_attr($color) . '">' . esc_html($letra) . '</span>';
        }
        echo '<span><strong>' . esc_html($name) . '</strong><small>Opinión en Google</small></span>';
        echo '</header>';
        echo '<p class="testimonio__meta"><span class="testimonio__stars" aria-label="5 de 5">★★★★★</span>';
        if ($when !== '') {
            echo '<small>' . esc_html($when) . '</small>';
        }
        echo '</p>';
        echo '<blockquote>' . esc_html($text) . '</blockquote>';
        echo '</article>';
    }
}

if (!function_exists('nj_testimonios_nota')) {
    function nj_testimonios_nota($class = 'ficha__nota') {
        $lista = nj_testimonios_lista();
        if (empty($lista['items'])) {
            return '';
        }
        $nota = number_format((float) $lista['rating'], 1, ',', '');
        $nota .= ' · ' . (int) $lista['total'] . ' reseñas';
        $label = '<span class="testimonio__stars" aria-label="5 de 5">★★★★★</span> ';
        if (!empty($lista['maps'])) {
            $label .= '<a href="' . esc_url($lista['maps']) . '" rel="noopener noreferrer">' . esc_html($nota) . '</a>';
        } else {
            $label .= '<span>' . esc_html($nota) . '</span>';
        }
        return '<p class="' . esc_attr($class) . '">' . $label . '</p>';
    }
}

if (!function_exists('nj_testimonios_save')) {
    function nj_testimonios_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            wp_die('No tienes permiso para cambiar los testimonios.');
        }
        check_admin_referer('nj_testimonios', 'nj_testimonios_nonce');
        $current = nj_testimonios_settings();
        $enabled = isset($_POST['enabled']) && $_POST['enabled'] === 'yes' ? 'yes' : 'no';
        $posted = isset($_POST['items']) && is_array($_POST['items']) ? wp_unslash($_POST['items']) : array();
        $manual = array();
        foreach ($posted as $row) {
            $item = nj_testimonios_fila(is_array($row) ? $row : array());
            if (!$item) {
                continue;
            }
            $manual[] = array(
                'name' => $item['name'],
                'text' => $item['text'],
                'when' => $item['when'],
                'image_id' => $item['image_id'],
            );
            if (count($manual) >= 20) {
                break;
            }
        }
        update_option('nj_testimonios', array(
            'enabled' => $enabled,
            'place' => $current['place'],
            'key' => $current['key'],
            'manual' => $manual,
        ), false);
        if ($enabled === 'yes' && class_exists('LiteSpeed_Cache_API')) {
            LiteSpeed_Cache_API::purge_all();
        }
        wp_safe_redirect(admin_url('admin.php?page=nj-tienda&updated=testimonios#testimonios'));
        exit;
    }
}
add_action('admin_post_nj_testimonios', 'nj_testimonios_save');

require_once __DIR__ . '/admin/testimonios.php';
