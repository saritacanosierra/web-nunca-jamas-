<?php
/**
 * Panel de Mi cuenta, historial, contacto y prendas guardadas.
 */

if (!function_exists('nj_cuenta_is_favoritos')) {
    function nj_cuenta_is_favoritos() {
        return isset($_GET['nj_favoritos']) && (string) $_GET['nj_favoritos'] === '1';
    }
}

if (!function_exists('nj_cuenta_favoritos_url')) {
    function nj_cuenta_favoritos_url() {
        return add_query_arg('nj_favoritos', '1', nj_home_url());
    }
}

if (!function_exists('nj_cuenta_orders_url')) {
    function nj_cuenta_orders_url() {
        if (function_exists('wc_get_account_endpoint_url')) {
            $url = wc_get_account_endpoint_url('orders');
            if ($url) {
                return $url;
            }
        }
        return function_exists('nj_shop_link') ? nj_shop_link('account') : nj_home_url();
    }
}

if (!function_exists('nj_cuenta_login_url')) {
    function nj_cuenta_login_url() {
        return function_exists('nj_shop_link') ? nj_shop_link('account') : nj_home_url();
    }
}

if (!function_exists('nj_cuenta_contact')) {
    function nj_cuenta_contact() {
        $footer = function_exists('nj_tema_footer') ? nj_tema_footer() : array();
        $whatsapp = isset($footer['whatsapp']) ? preg_replace('/\D+/', '', (string) $footer['whatsapp']) : '';
        if (strlen($whatsapp) >= 7) {
            return array(
                'href' => 'https://wa.me/' . $whatsapp,
                'external' => true,
            );
        }
        if (function_exists('nj_whatsapp_chat_url')) {
            $chat = nj_whatsapp_chat_url();
            if ($chat) {
                return array('href' => $chat, 'external' => true);
            }
        }
        $email = isset($footer['email']) ? (string) $footer['email'] : '';
        if ($email === '' && function_exists('nj_pages_mail')) {
            $email = nj_pages_mail();
        }
        if ($email !== '') {
            return array('href' => 'mailto:' . $email, 'external' => false);
        }
        $help = function_exists('nj_page_url') ? nj_page_url('preguntas') : nj_home_url();
        return array('href' => $help, 'external' => false);
    }
}

if (!function_exists('nj_cuenta_name')) {
    function nj_cuenta_name() {
        if (!function_exists('is_user_logged_in') || !is_user_logged_in() || !function_exists('wp_get_current_user')) {
            return '';
        }
        $user = wp_get_current_user();
        $name = is_object($user) ? trim((string) $user->first_name) : '';
        if ($name === '' && is_object($user)) {
            $name = trim((string) $user->display_name);
        }
        if ($name === '' || strpos($name, '@') !== false) {
            return '';
        }
        return $name;
    }
}

if (!function_exists('nj_cuenta_saved_ids')) {
    function nj_cuenta_saved_ids() {
        if (!function_exists('is_user_logged_in') || !is_user_logged_in() || !function_exists('get_user_meta')) {
            return array();
        }
        $saved = get_user_meta(get_current_user_id(), '_nj_favoritos', true);
        if (!is_array($saved)) {
            return array();
        }
        $ids = array();
        foreach ($saved as $id) {
            $id = absint($id);
            if ($id && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
            if (count($ids) >= 40) {
                break;
            }
        }
        return $ids;
    }
}

if (!function_exists('nj_cuenta_posted_ids')) {
    function nj_cuenta_posted_ids() {
        $raw = isset($_POST['ids']) ? (array) wp_unslash($_POST['ids']) : array();
        $ids = array();
        foreach ($raw as $id) {
            $id = absint($id);
            if ($id && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
            if (count($ids) >= 40) {
                break;
            }
        }
        return $ids;
    }
}

if (!function_exists('nj_cuenta_store_ids')) {
    function nj_cuenta_store_ids($ids) {
        if (!function_exists('is_user_logged_in') || !is_user_logged_in() || !function_exists('update_user_meta')) {
            return;
        }
        update_user_meta(get_current_user_id(), '_nj_favoritos', array_values($ids));
    }
}

if (!function_exists('nj_cuenta_guardar')) {
    function nj_cuenta_guardar() {
        check_ajax_referer('nj_cuenta', 'nonce');
        $ids = nj_cuenta_posted_ids();
        nj_cuenta_store_ids($ids);
        wp_send_json(array('ok' => true, 'ids' => $ids));
    }
}
add_action('wp_ajax_nj_cuenta_guardar', 'nj_cuenta_guardar');
add_action('wp_ajax_nopriv_nj_cuenta_guardar', 'nj_cuenta_guardar');

if (!function_exists('nj_cuenta_lista')) {
    function nj_cuenta_lista() {
        check_ajax_referer('nj_cuenta', 'nonce');
        $ids = nj_cuenta_posted_ids();
        nj_cuenta_store_ids($ids);
        if (!function_exists('wc_get_product')) {
            wp_die();
        }
        foreach ($ids as $id) {
            $product = wc_get_product($id);
            if (!$product || !method_exists($product, 'get_status') || $product->get_status() !== 'publish') {
                continue;
            }
            $item = nj_card_from_product($product);
            require get_template_directory() . '/template-parts/product-card.php';
        }
        wp_die();
    }
}
add_action('wp_ajax_nj_cuenta_lista', 'nj_cuenta_lista');
add_action('wp_ajax_nopriv_nj_cuenta_lista', 'nj_cuenta_lista');

if (!function_exists('nj_cuenta_template')) {
    function nj_cuenta_template($template) {
        if (!nj_cuenta_is_favoritos()) {
            return $template;
        }
        $file = get_template_directory() . '/favoritos.php';
        return file_exists($file) ? $file : $template;
    }
}
add_filter('template_include', 'nj_cuenta_template');

if (!function_exists('nj_cuenta_title')) {
    function nj_cuenta_title($parts) {
        if (nj_cuenta_is_favoritos() && is_array($parts)) {
            $parts['title'] = 'Mis favoritos';
        }
        return $parts;
    }
}
add_filter('document_title_parts', 'nj_cuenta_title');

if (!function_exists('nj_cuenta_robots')) {
    function nj_cuenta_robots() {
        if (nj_cuenta_is_favoritos()) {
            echo '<meta name="robots" content="noindex">' . "\n";
        }
    }
}
add_action('wp_head', 'nj_cuenta_robots', 1);
