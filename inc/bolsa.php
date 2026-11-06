<?php
/**
 * Bolsa: lee el carrito de WooCommerce y aplica cantidad o cupón.
 */

if (!function_exists('nj_bag_state')) {
    function nj_bag_state() {
        $state = array(
            'lines' => array(),
            'subtotal' => 0,
            'discount' => 0,
            'total' => 0,
            'count' => 0,
            'note' => '',
        );
        if (!function_exists('WC') || !WC() || !WC()->cart) {
            return $state;
        }
        foreach (WC()->cart->get_cart() as $key => $item) {
            $product = isset($item['data']) ? $item['data'] : null;
            if (!$product) {
                continue;
            }
            $name = $product->get_name();
            if ($product->is_type('variation')) {
                $parent = wc_get_product($product->get_parent_id());
                if ($parent) {
                    $name = $parent->get_name();
                }
            }
            $size = '';
            if (!empty($item['variation']) && is_array($item['variation'])) {
                foreach ($item['variation'] as $value) {
                    if ($value !== '') {
                        $size = (string) $value;
                        break;
                    }
                }
            }
            $image = '';
            $image_id = $product->get_image_id();
            if ($image_id && function_exists('wp_get_attachment_image_url')) {
                $found = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
                $image = $found ? $found : '';
            }
            $state['lines'][] = array(
                'key' => $key,
                'name' => $name,
                'image' => $image,
                'url' => get_permalink($item['product_id']),
                'size' => $size,
                'price' => (int) round((float) $product->get_price()),
                'regular' => (int) round((float) $product->get_regular_price()),
                'qty' => (int) $item['quantity'],
            );
        }
        $state['subtotal'] = (int) round((float) WC()->cart->get_subtotal());
        $state['discount'] = (int) round((float) WC()->cart->get_discount_total());
        $state['total'] = (int) round((float) WC()->cart->get_total('edit'));
        $state['count'] = (int) WC()->cart->get_cart_contents_count();
        $state['note'] = isset($GLOBALS['nj_bag_note']) ? (string) $GLOBALS['nj_bag_note'] : '';
        return $state;
    }
}

if (!function_exists('nj_bag_nonce_action')) {
    function nj_bag_nonce_action() {
        return 'nj-bag';
    }
}

if (!function_exists('nj_bag_nonce_valid')) {
    function nj_bag_nonce_valid($nonce) {
        if (!function_exists('wp_verify_nonce') || !is_string($nonce) || $nonce === '') {
            return false;
        }
        return (bool) wp_verify_nonce($nonce, nj_bag_nonce_action());
    }
}

if (!function_exists('nj_bag_update')) {
    function nj_bag_update() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!nj_bag_nonce_valid($nonce) || !function_exists('WC') || !WC() || !WC()->cart) {
            wp_send_json(array('error' => true));
        }
        $bag_action = isset($_POST['bag_action']) ? sanitize_key(wp_unslash($_POST['bag_action'])) : '';
        if ($bag_action === 'coupon') {
            $code = isset($_POST['coupon']) ? wc_format_coupon_code(wp_unslash($_POST['coupon'])) : '';
            if ($code !== '') {
                WC()->cart->add_discount($code);
            }
        } else {
            $key = isset($_POST['key']) ? wc_clean(wp_unslash($_POST['key'])) : '';
            $qty = isset($_POST['qty']) ? absint($_POST['qty']) : 0;
            if ($key !== '') {
                if ($qty < 1) {
                    WC()->cart->remove_cart_item($key);
                } else {
                    WC()->cart->set_quantity($key, $qty, true);
                }
            }
        }
        WC()->cart->calculate_totals();
        $texts = array();
        if (function_exists('wc_get_notices')) {
            foreach (array('error', 'success', 'notice') as $type) {
                foreach (wc_get_notices($type) as $notice) {
                    $texts[] = wp_strip_all_tags($notice['notice']);
                }
            }
            wc_clear_notices();
        }
        $GLOBALS['nj_bag_note'] = $texts ? $texts[0] : '';
        if (class_exists('WC_AJAX')) {
            WC_AJAX::get_refreshed_fragments();
        }
        wp_die();
    }
}
add_action('wc_ajax_nj_bag', 'nj_bag_update');
add_action('wc_ajax_nopriv_nj_bag', 'nj_bag_update');
