<?php
/**
 * Arma la tarjeta de producto para la portada y la galería.
 */

require_once __DIR__ . '/shop.php';

if (!function_exists('nj_card_sizes')) {
    function nj_card_sizes($product) {
        if (!is_object($product) || !method_exists($product, 'is_type') || !$product->is_type('variable')) {
            return array();
        }
        $attributes = $product->get_variation_attributes();
        $options = array();
        foreach ($attributes as $name => $values) {
            $key = nj_menu_key(str_replace('pa_', '', $name));
            if (strpos($key, 'talla') !== false || strpos($key, 'size') !== false) {
                $options = $values;
                break;
            }
        }
        $labels = array();
        foreach ($options as $value) {
            $labels[] = (string) $value;
        }
        return $labels;
    }
}

if (!function_exists('nj_card_gallery')) {
    function nj_card_gallery($image, $extra) {
        $gallery = array();
        foreach (array_merge(array($image), $extra) as $url) {
            if ($url && !in_array($url, $gallery, true)) {
                $gallery[] = $url;
            }
        }
        return $gallery;
    }
}

if (!function_exists('nj_card_from_slot')) {
    function nj_card_from_slot($product) {
        $regular = isset($product['regular']) ? $product['regular'] : '';
        $hover = !empty($product['hover']) ? nj_img($product['hover']) : '';
        $extra = array($hover);
        if (!empty($product['gallery'])) {
            foreach ($product['gallery'] as $file) {
                $extra[] = nj_img($file);
            }
        }
        $url = $product['href'];
        if (defined('NJ_PREVIEW')) {
            $url = 'producto.php?ref=' . rawurlencode(nj_menu_key($product['name']));
        }
        return array(
            'url' => $url,
            'id' => 0,
            'variations' => array(),
            'image' => nj_img($product['src']),
            'hover' => $hover,
            'gallery' => nj_card_gallery(nj_img($product['src']), $extra),
            'alt' => $product['alt'],
            'kicker' => isset($product['kicker']) ? $product['kicker'] : 'Colección',
            'name' => $product['name'],
            'price_html' => nj_card_price_html($product['price'], $regular),
            'badge' => isset($product['badge']) ? $product['badge'] : '',
            'sizes' => isset($product['sizes']) ? $product['sizes'] : array(),
            'price' => nj_money($product['price']),
            'regular' => nj_money(isset($product['regular']) ? $product['regular'] : $product['price']),
            'ref' => isset($product['ref']) ? $product['ref'] : '',
            'note' => isset($product['note']) ? $product['note'] : '',
            'description' => isset($product['description']) ? $product['description'] : '',
            'composition' => isset($product['composition']) ? $product['composition'] : '',
        );
    }
}

if (!function_exists('nj_card_image')) {
    function nj_card_image($product) {
        $image = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_single') : '';
        $image_id = $product->get_image_id();
        if (!$image_id || !function_exists('wp_get_attachment_image_url')) {
            return $image;
        }
        $found = wp_get_attachment_image_url($image_id, 'woocommerce_single');
        return $found ? $found : $image;
    }
}

if (!function_exists('nj_card_extra_images')) {
    function nj_card_extra_images($product) {
        $extra = array();
        if (!method_exists($product, 'get_gallery_image_ids') || !function_exists('wp_get_attachment_image_url')) {
            return $extra;
        }
        foreach ($product->get_gallery_image_ids() as $attachment_id) {
            $found = wp_get_attachment_image_url($attachment_id, 'woocommerce_single');
            if ($found) {
                $extra[] = $found;
            }
        }
        return $extra;
    }
}

if (!function_exists('nj_card_kicker')) {
    function nj_card_kicker($product) {
        $terms = get_the_terms($product->get_id(), 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            return $terms[0]->name;
        }
        return 'Tienda';
    }
}

if (!function_exists('nj_card_description')) {
    function nj_card_description($product) {
        $description = '';
        if (method_exists($product, 'get_short_description')) {
            $description = nj_plain_text($product->get_short_description());
        }
        if ($description === '' && method_exists($product, 'get_description')) {
            return nj_plain_text($product->get_description());
        }
        return $description;
    }
}

if (!function_exists('nj_card_variations')) {
    function nj_card_variations($product) {
        $rows = array();
        if (!is_object($product) || !method_exists($product, 'is_type') || !$product->is_type('variable')) {
            return $rows;
        }
        if (!method_exists($product, 'get_available_variations')) {
            return $rows;
        }
        foreach ($product->get_available_variations() as $variation) {
            $size = '';
            $attributes = array();
            if (!empty($variation['attributes']) && is_array($variation['attributes'])) {
                foreach ($variation['attributes'] as $key => $value) {
                    $attributes[$key] = $value;
                    $name = nj_menu_key(str_replace('attribute_pa_', '', (string) $key));
                    if ($size === '' && (strpos($name, 'talla') !== false || strpos($name, 'size') !== false)) {
                        $size = (string) $value;
                    }
                }
                if ($size === '' && count($attributes) === 1) {
                    $size = (string) reset($attributes);
                }
            }
            $rows[] = array(
                'id' => isset($variation['variation_id']) ? (int) $variation['variation_id'] : 0,
                'size' => $size,
                'attributes' => $attributes,
            );
        }
        return $rows;
    }
}

if (!function_exists('nj_card_from_product')) {
    function nj_card_from_product($product) {
        $extra = nj_card_extra_images($product);
        $image = nj_card_image($product);
        $percent = nj_discount_percent($product);
        $regular = method_exists($product, 'get_regular_price') ? (int) round((float) $product->get_regular_price()) : 0;
        $price = method_exists($product, 'get_price') ? (int) round((float) $product->get_price()) : 0;
        return array(
            'url' => get_permalink($product->get_id()),
            'id' => $product->get_id(),
            'variations' => nj_card_variations($product),
            'image' => $image,
            'hover' => isset($extra[0]) ? $extra[0] : '',
            'gallery' => nj_card_gallery($image, $extra),
            'alt' => $product->get_name(),
            'kicker' => nj_card_kicker($product),
            'name' => $product->get_name(),
            'price_html' => $product->get_price_html(),
            'badge' => $percent !== '' ? '-' . $percent . '%' : '',
            'sizes' => nj_card_sizes($product),
            'price' => $price,
            'regular' => $regular,
            'ref' => method_exists($product, 'get_sku') ? (string) $product->get_sku() : '',
            'note' => '',
            'description' => nj_card_description($product),
            'composition' => '',
        );
    }
}
