<?php
/**
 * Logo y colores del tema. La pantalla está en inc/admin/tema.php.
 */

if (!function_exists('nj_tema_defaults')) {
    function nj_tema_defaults() {
        return array(
            'logo' => 0,
            'coral' => '#EAA799',
            'teal' => '#64bfae',
            'gold' => '#f1c36b',
            'ink' => '#000000',
            'bg' => '#ffffff',
            'css' => '',
        );
    }
}

if (!function_exists('nj_tema_settings')) {
    function nj_tema_settings() {
        $defaults = nj_tema_defaults();
        $saved = function_exists('get_option') ? get_option('nj_tema', array()) : array();
        if (!is_array($saved)) {
            $saved = array();
        }
        $settings = $defaults;
        $settings['logo'] = isset($saved['logo']) ? (function_exists('absint') ? absint($saved['logo']) : abs((int) $saved['logo'])) : 0;
        foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
            $color = isset($saved[$key]) ? (function_exists('sanitize_hex_color') ? sanitize_hex_color($saved[$key]) : '') : '';
            if ($color) {
                $settings[$key] = $color;
            }
        }
        $settings['css'] = isset($saved['css']) ? nj_tema_css_clean($saved['css']) : '';
        return $settings;
    }
}

if (!function_exists('nj_tema_css_clean')) {
    function nj_tema_css_clean($css) {
        $css = str_replace(array('<', '>'), '', (string) $css);
        return substr(trim($css), 0, 5000);
    }
}

if (!function_exists('nj_tema_link')) {
    function nj_tema_link($value) {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        return function_exists('esc_url_raw') ? esc_url_raw($value) : $value;
    }
}

if (!function_exists('nj_tema_plain')) {
    function nj_tema_plain($value, $limit = 180) {
        $value = function_exists('sanitize_text_field') ? sanitize_text_field($value) : trim(strip_tags((string) $value));
        $limit = (int) $limit;
        if ($limit < 1) {
            $limit = 180;
        }
        return function_exists('mb_substr') ? mb_substr($value, 0, $limit) : substr($value, 0, $limit);
    }
}

if (!function_exists('nj_tema_pair')) {
    function nj_tema_pair($row) {
        if (!is_array($row)) {
            return null;
        }
        $label = nj_tema_plain(isset($row['label']) ? $row['label'] : '', 80);
        $href = nj_tema_link(isset($row['href']) ? $row['href'] : '');
        if ($label === '' || $href === '') {
            return null;
        }
        return array(
            'label' => $label,
            'href' => $href,
        );
    }
}

if (!function_exists('nj_tema_pairs')) {
    function nj_tema_pairs($rows, $max) {
        $clean = array();
        if (!is_array($rows)) {
            return $clean;
        }
        foreach ($rows as $row) {
            $pair = nj_tema_pair($row);
            if ($pair) {
                $clean[] = $pair;
            }
            if (count($clean) >= (int) $max) {
                break;
            }
        }
        return $clean;
    }
}

if (!function_exists('nj_tema_pills')) {
    function nj_tema_pills($rows, $max) {
        $clean = array();
        if (!is_array($rows)) {
            return $clean;
        }
        foreach ($rows as $row) {
            $label = nj_tema_plain($row, 24);
            if ($label === '') {
                continue;
            }
            $clean[] = $label;
            if (count($clean) >= (int) $max) {
                break;
            }
        }
        return $clean;
    }
}

if (!function_exists('nj_tema_header_defaults')) {
    function nj_tema_header_defaults() {
        return array(
            'notice' => 'Envíos a Colombia',
            'notice_href' => function_exists('nj_page_url') ? nj_page_url('envios') : '',
            'search' => 'Buscar prendas, cuentos o accesorios',
        );
    }
}

if (!function_exists('nj_tema_footer_defaults')) {
    function nj_tema_footer_defaults() {
        $page = function ($key) {
            return function_exists('nj_page_url') ? nj_page_url($key) : '';
        };
        $shop = array();
        if (function_exists('nj_nav_items')) {
            foreach (array_slice(nj_nav_items(), 0, 5) as $item) {
                if (!empty($item['label']) && !empty($item['href'])) {
                    $shop[] = array(
                        'label' => $item['label'],
                        'href' => $item['href'],
                    );
                }
            }
        }
        $shop[] = array(
            'label' => 'Cuentos',
            'href' => function_exists('nj_blog_url') ? nj_blog_url() : (function_exists('nj_home_url') ? nj_home_url() : ''),
        );
        $shop[] = array(
            'label' => 'Bolsa',
            'href' => function_exists('nj_shop_link') ? nj_shop_link('cart') : '',
        );
        $shop[] = array(
            'label' => 'Mi cuenta',
            'href' => function_exists('nj_shop_link') ? nj_shop_link('account') : '',
        );
        return array(
            'blurb' => 'Ropa de cuento para niñas: colecciones, línea, accesorios y personalizados sobre pedido.',
            'news' => '1',
            'news_label' => 'Novedades en tu correo',
            'news_placeholder' => 'Tu correo',
            'email' => 'saritacanosierra@gmail.com',
            'whatsapp' => '',
            'place' => 'Envíos a Colombia',
            'legal' => 'Nunca Jamás. Ropa para niñas.',
            'columns' => array(
                array(
                    'title' => 'Comprar',
                    'links' => $shop,
                ),
                array(
                    'title' => 'Ayuda',
                    'links' => array(
                        array('label' => 'Preguntas frecuentes', 'href' => $page('preguntas')),
                        array('label' => 'Envíos', 'href' => $page('envios')),
                        array('label' => 'Cambios', 'href' => $page('cambios')),
                        array('label' => 'Guía de tallas', 'href' => $page('tallas')),
                    ),
                ),
            ),
            'socials' => array(),
            'pays' => array('VISA', 'Mastercard', 'PSE', 'Nequi'),
            'trust' => array('Compra segura', 'Hecho para niñas', 'Cambios con calma'),
            'legal_links' => array(
                array('label' => 'Términos', 'href' => $page('terminos')),
                array('label' => 'Privacidad', 'href' => $page('privacidad')),
            ),
        );
    }
}

if (!function_exists('nj_tema_header_ready')) {
    function nj_tema_header_ready($raw) {
        $defaults = nj_tema_header_defaults();
        if (!is_array($raw)) {
            $raw = array();
        }
        $search = nj_tema_plain(isset($raw['search']) ? $raw['search'] : '', 80);
        return array(
            'notice' => nj_tema_plain(isset($raw['notice']) ? $raw['notice'] : '', 120),
            'notice_href' => nj_tema_link(isset($raw['notice_href']) ? $raw['notice_href'] : ''),
            'search' => $search !== '' ? $search : $defaults['search'],
        );
    }
}

if (!function_exists('nj_tema_footer_ready')) {
    function nj_tema_footer_ready($raw) {
        if (!is_array($raw)) {
            $raw = array();
        }
        $email = trim((string) (isset($raw['email']) ? $raw['email'] : ''));
        if (function_exists('sanitize_email')) {
            $email = sanitize_email($email);
        }
        if ($email !== '' && strpos($email, '@') === false) {
            $email = '';
        }
        $whatsapp = preg_replace('/\D+/', '', (string) (isset($raw['whatsapp']) ? $raw['whatsapp'] : ''));
        if (!is_string($whatsapp) || strlen($whatsapp) < 7 || strlen($whatsapp) > 15) {
            $whatsapp = '';
        }
        $columns = array();
        $raw_columns = isset($raw['columns']) && is_array($raw['columns']) ? $raw['columns'] : array();
        foreach ($raw_columns as $column) {
            if (!is_array($column)) {
                continue;
            }
            $title = nj_tema_plain(isset($column['title']) ? $column['title'] : '', 40);
            $links = nj_tema_pairs(isset($column['links']) ? $column['links'] : array(), 8);
            if ($title === '' || !$links) {
                continue;
            }
            $columns[] = array(
                'title' => $title,
                'links' => $links,
            );
            if (count($columns) >= 4) {
                break;
            }
        }
        $news_label = nj_tema_plain(isset($raw['news_label']) ? $raw['news_label'] : '', 80);
        $news_placeholder = nj_tema_plain(isset($raw['news_placeholder']) ? $raw['news_placeholder'] : '', 40);
        return array(
            'blurb' => nj_tema_plain(isset($raw['blurb']) ? $raw['blurb'] : '', 240),
            'news' => empty($raw['news']) ? '' : '1',
            'news_label' => $news_label !== '' ? $news_label : 'Novedades en tu correo',
            'news_placeholder' => $news_placeholder !== '' ? $news_placeholder : 'Tu correo',
            'email' => $email,
            'whatsapp' => $whatsapp,
            'place' => nj_tema_plain(isset($raw['place']) ? $raw['place'] : '', 80),
            'legal' => nj_tema_plain(isset($raw['legal']) ? $raw['legal'] : '', 160),
            'columns' => $columns,
            'socials' => nj_tema_pairs(isset($raw['socials']) ? $raw['socials'] : array(), 6),
            'pays' => nj_tema_pills(isset($raw['pays']) ? $raw['pays'] : array(), 8),
            'trust' => nj_tema_pills(isset($raw['trust']) ? $raw['trust'] : array(), 6),
            'legal_links' => nj_tema_pairs(isset($raw['legal_links']) ? $raw['legal_links'] : array(), 4),
        );
    }
}

if (!function_exists('nj_tema_stored')) {
    function nj_tema_stored($key) {
        if (!function_exists('get_option')) {
            return null;
        }
        $saved = get_option('nj_tema', array());
        if (!is_array($saved) || !isset($saved[$key]) || !is_array($saved[$key])) {
            return null;
        }
        return $saved[$key];
    }
}

if (!function_exists('nj_tema_header')) {
    function nj_tema_header() {
        $saved = nj_tema_stored('header');
        if ($saved === null) {
            return nj_tema_header_defaults();
        }
        return nj_tema_header_ready($saved);
    }
}

if (!function_exists('nj_tema_footer_groups')) {
    function nj_tema_footer_groups() {
        $page = function ($key) {
            return function_exists('nj_page_url') ? nj_page_url($key) : '';
        };
        $home = function_exists('nj_home_url') ? nj_home_url() : '';
        $menu = function_exists('nj_menu') ? nj_menu() : array();
        $pages = array(
            array('label' => 'Inicio', 'href' => $home),
        );
        if (!empty($menu['newest']['label']) && !empty($menu['newest']['href'])) {
            $pages[] = array(
                'label' => $menu['newest']['label'],
                'href' => $menu['newest']['href'],
            );
        }
        $pages[] = array(
            'label' => 'Cuentos',
            'href' => function_exists('nj_blog_url') ? nj_blog_url() : $home,
        );
        $pages[] = array(
            'label' => 'Bolsa',
            'href' => function_exists('nj_shop_link') ? nj_shop_link('cart') : $home,
        );
        $pages[] = array(
            'label' => 'Mi cuenta',
            'href' => function_exists('nj_shop_link') ? nj_shop_link('account') : $home,
        );
        $categories = array();
        if (!empty($menu['departments']) && is_array($menu['departments'])) {
            foreach ($menu['departments'] as $department) {
                if (empty($department['label']) || empty($department['href'])) {
                    continue;
                }
                $categories[] = array(
                    'label' => $department['label'],
                    'href' => $department['href'],
                );
            }
        }
        if (!empty($menu['collections']['label']) && !empty($menu['collections']['href'])) {
            $categories[] = array(
                'label' => $menu['collections']['label'],
                'href' => $menu['collections']['href'],
            );
        }
        return array(
            array('title' => 'Páginas', 'links' => $pages),
            array('title' => 'Categorías', 'links' => $categories),
            array(
                'title' => 'Ayuda',
                'links' => array(
                    array('label' => 'Preguntas frecuentes', 'href' => $page('preguntas')),
                    array('label' => 'Envíos', 'href' => $page('envios')),
                    array('label' => 'Cambios', 'href' => $page('cambios')),
                    array('label' => 'Guía de tallas', 'href' => $page('tallas')),
                ),
            ),
        );
    }
}

if (!function_exists('nj_tema_footer_perks')) {
    function nj_tema_footer_perks() {
        $threshold = 120000;
        if (function_exists('nj_slots_data')) {
            $bag = nj_slots_data()['bag'];
            if (isset($bag['free_shipping'])) {
                $threshold = (int) $bag['free_shipping'];
            }
        }
        $money = function_exists('nj_pages_money')
            ? nj_pages_money($threshold)
            : '$' . number_format($threshold, 0, ',', '.');
        $changes = function_exists('nj_page_url') ? nj_page_url('cambios') : '';
        return array(
            array(
                'icon' => 'truck',
                'title' => 'Envío gratis',
                'text' => 'Desde ' . $money,
                'href' => function_exists('nj_page_url') ? nj_page_url('envios') : '',
            ),
            array(
                'icon' => 'pay',
                'title' => 'Pago en pesos',
                'text' => 'Nequi, tarjeta o PSE',
                'href' => '',
            ),
            array(
                'icon' => 'change',
                'title' => 'Cambios con calma',
                'text' => 'Si la talla no queda',
                'href' => $changes,
            ),
        );
    }
}

if (!function_exists('nj_tema_footer')) {
    function nj_tema_footer() {
        $saved = nj_tema_stored('footer');
        if ($saved === null) {
            return nj_tema_footer_defaults();
        }
        return nj_tema_footer_ready($saved);
    }
}

if (!function_exists('nj_tema_menu_item')) {
    function nj_tema_menu_item($row, $with_children) {
        if (!is_array($row)) {
            return null;
        }
        $label = function_exists('sanitize_text_field') ? sanitize_text_field(isset($row['label']) ? $row['label'] : '') : trim(strip_tags((string) (isset($row['label']) ? $row['label'] : '')));
        if ($label === '') {
            return null;
        }
        $href = nj_tema_link(isset($row['href']) ? $row['href'] : '');
        if ($href === '' && function_exists('nj_section_link')) {
            $href = nj_section_link($label);
        }
        $item = array(
            'label' => $label,
            'href' => $href,
        );
        if (!$with_children) {
            return $item;
        }
        $item['children'] = array();
        $children = isset($row['children']) && is_array($row['children']) ? $row['children'] : array();
        foreach ($children as $child) {
            $clean = nj_tema_menu_item($child, false);
            if ($clean) {
                $item['children'][] = $clean;
            }
            if (count($item['children']) >= 8) {
                break;
            }
        }
        return $item;
    }
}

if (!function_exists('nj_tema_menu_ready')) {
    function nj_tema_menu_ready($saved) {
        if (!is_array($saved)) {
            return null;
        }
        $departments = array();
        $raw = isset($saved['departments']) && is_array($saved['departments']) ? $saved['departments'] : array();
        foreach ($raw as $row) {
            $item = nj_tema_menu_item($row, true);
            if ($item) {
                $departments[] = $item;
            }
            if (count($departments) >= 6) {
                break;
            }
        }
        $collections = nj_tema_menu_item(isset($saved['collections']) ? $saved['collections'] : array(), true);
        if (!$collections) {
            $collections = array('label' => 'Colecciones', 'href' => function_exists('nj_section_link') ? nj_section_link('Colecciones') : '', 'children' => array());
        }
        $newest = nj_tema_menu_item(isset($saved['newest']) ? $saved['newest'] : array(), false);
        if (!$newest) {
            $newest = array(
                'label' => 'Nuevo',
                'href' => function_exists('nj_shop_link') ? nj_shop_link('newest') : '',
            );
        }
        return array(
            'departments' => $departments,
            'collections' => $collections,
            'newest' => $newest,
        );
    }
}

if (!function_exists('nj_tema_menu_live')) {
    function nj_tema_menu_live() {
        if (!function_exists('get_option')) {
            return null;
        }
        $saved = get_option('nj_menu', null);
        if (!is_array($saved)) {
            return null;
        }
        return nj_tema_menu_ready($saved);
    }
}

if (!function_exists('nj_tema_logo_url')) {
    function nj_tema_logo_url() {
        $settings = nj_tema_settings();
        if ($settings['logo'] && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url($settings['logo'], 'full');
            if (is_string($url) && $url !== '') {
                return $url;
            }
        }
        return nj_img('logo.png');
    }
}

if (!function_exists('nj_tema_css')) {
    function nj_tema_css() {
        $settings = nj_tema_settings();
        $defaults = nj_tema_defaults();
        $vars = array(
            'coral' => '--coral',
            'teal' => '--teal',
            'gold' => '--gold',
            'ink' => '--ink',
            'bg' => '--bg',
        );
        $rules = array();
        foreach ($vars as $key => $name) {
            if (strcasecmp($settings[$key], $defaults[$key]) !== 0) {
                $rules[] = $name . ':' . $settings[$key];
            }
        }
        if ($rules) {
            echo '<style id="nj-tema">:root{' . implode(';', $rules) . '}</style>';
        }
        if ($settings['css'] !== '') {
            echo '<style id="nj-tema-extra">' . $settings['css'] . '</style>';
        }
    }
}
add_action('wp_head', 'nj_tema_css', 20);

if (!function_exists('nj_tema_admin_menu')) {
    function nj_tema_admin_menu() {
        if (!function_exists('add_menu_page')) {
            return;
        }
        add_menu_page(
            'Editar el tema',
            'Editar el tema',
            'edit_theme_options',
            'nj-tema',
            'nj_tema_admin_page',
            'dashicons-admin-customizer',
            3.2
        );
    }
}
add_action('admin_menu', 'nj_tema_admin_menu');

if (!function_exists('nj_tema_admin_assets')) {
    function nj_tema_admin_assets($hook) {
        if ($hook !== 'toplevel_page_nj-tema') {
            return;
        }
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        $uri = get_template_directory_uri();
        wp_enqueue_style('nj-hero-admin', $uri . '/assets/hero-admin.css', array(), '1.0.0');
        wp_enqueue_style('nj-tienda', $uri . '/assets/mitienda.css', array('nj-hero-admin'), '1.1.14');
        wp_enqueue_script('nj-hero-admin', $uri . '/assets/hero-admin.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('nj-tienda', $uri . '/assets/mitienda.js', array('jquery', 'nj-hero-admin'), '1.1.14', true);
    }
}
add_action('admin_enqueue_scripts', 'nj_tema_admin_assets', 20);

if (!function_exists('nj_tema_admin_save')) {
    function nj_tema_admin_save() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            wp_die('No tienes permiso para editar el tema.');
        }
        check_admin_referer('nj_tema_save', 'nj_tema_nonce');
        $current = nj_tema_settings();
        if (!empty($_POST['nj_tema_reset'])) {
            $raw = get_option('nj_tema', array());
            if (!is_array($raw)) {
                $raw = array();
            }
            $defaults = nj_tema_defaults();
            $raw['logo'] = 0;
            foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
                $raw[$key] = $defaults[$key];
            }
            update_option('nj_tema', $raw);
            wp_safe_redirect(admin_url('admin.php?page=nj-tema&updated=tema#identidad'));
            exit;
        }
        $defaults = nj_tema_defaults();
        $logo = isset($_POST['logo']) ? absint(wp_unslash($_POST['logo'])) : 0;
        $icon = isset($_POST['icon']) ? absint(wp_unslash($_POST['icon'])) : 0;
        if (function_exists('wp_attachment_is_image')) {
            if ($logo && !wp_attachment_is_image($logo)) {
                $logo = 0;
            }
            if ($icon && !wp_attachment_is_image($icon)) {
                $icon = 0;
            }
        }
        $saved = array(
            'logo' => $logo,
            'css' => nj_tema_css_clean(isset($_POST['extra_css']) ? wp_unslash($_POST['extra_css']) : ''),
        );
        foreach (array('coral', 'teal', 'gold', 'ink', 'bg') as $key) {
            $posted = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
            $color = function_exists('sanitize_hex_color') ? sanitize_hex_color($posted) : '';
            $saved[$key] = $color ? $color : $defaults[$key];
        }
        $header = isset($_POST['header']) && is_array($_POST['header']) ? wp_unslash($_POST['header']) : array();
        $footer = isset($_POST['footer']) && is_array($_POST['footer']) ? wp_unslash($_POST['footer']) : array();
        $saved['header'] = nj_tema_header_ready($header);
        $saved['footer'] = nj_tema_footer_ready($footer);
        update_option('nj_tema', $saved);
        if (function_exists('nj_pages_help_store') && isset($_POST['ayuda']) && is_array($_POST['ayuda'])) {
            nj_pages_help_store(wp_unslash($_POST['ayuda']));
        }
        update_option('blogname', sanitize_text_field(wp_unslash(isset($_POST['blogname']) ? $_POST['blogname'] : '')));
        update_option('blogdescription', sanitize_text_field(wp_unslash(isset($_POST['blogdescription']) ? $_POST['blogdescription'] : '')));
        update_option('site_icon', $icon);
        $menu = isset($_POST['menu']) && is_array($_POST['menu']) ? wp_unslash($_POST['menu']) : null;
        if (is_array($menu)) {
            $ready = nj_tema_menu_ready($menu);
            if ($ready) {
                update_option('nj_menu', $ready);
            }
        }
        $front = isset($_POST['show_on_front']) && $_POST['show_on_front'] === 'page' ? 'page' : 'posts';
        update_option('show_on_front', $front);
        update_option('page_on_front', isset($_POST['page_on_front']) ? absint(wp_unslash($_POST['page_on_front'])) : 0);
        update_option('page_for_posts', isset($_POST['page_for_posts']) ? absint(wp_unslash($_POST['page_for_posts'])) : 0);
        if (class_exists('WooCommerce')) {
            update_option('woocommerce_demo_store', empty($_POST['store_notice']) ? 'no' : 'yes');
            update_option('woocommerce_demo_store_notice', sanitize_text_field(wp_unslash(isset($_POST['store_notice_text']) ? $_POST['store_notice_text'] : '')));
            $columns = isset($_POST['catalog_columns']) ? absint(wp_unslash($_POST['catalog_columns'])) : 4;
            $rows = isset($_POST['catalog_rows']) ? absint(wp_unslash($_POST['catalog_rows'])) : 4;
            update_option('woocommerce_catalog_columns', max(2, min(4, $columns)));
            update_option('woocommerce_catalog_rows', max(1, min(8, $rows)));
            foreach (array('shop', 'cart', 'checkout', 'myaccount') as $page) {
                $id = isset($_POST['wc_page_' . $page]) ? absint(wp_unslash($_POST['wc_page_' . $page])) : 0;
                update_option('woocommerce_' . $page . '_page_id', $id);
            }
            if (function_exists('nj_pagos_save_wompi')) {
                nj_pagos_save_wompi();
            }
        }
        $section = isset($_POST['nj_tema_section']) ? sanitize_key(wp_unslash($_POST['nj_tema_section'])) : 'identidad';
        if ($section === 'menus') {
            $section = 'encabezado';
        }
        if (!in_array($section, array('identidad', 'encabezado', 'pie', 'ayuda', 'portada', 'woocommerce', 'pagos', 'css'), true)) {
            $section = 'identidad';
        }
        wp_safe_redirect(admin_url('admin.php?page=nj-tema&updated=tema#' . $section));
        exit;
    }
}
add_action('admin_post_nj_tema_save', 'nj_tema_admin_save');

require_once __DIR__ . '/admin/tema.php';
