<?php
/**
 * Pantalla única para cambiar la portada y las prendas.
 */

if (!function_exists('nj_tienda_can')) {
    function nj_tienda_can() {
        if (!function_exists('current_user_can')) {
            return false;
        }
        return current_user_can('edit_theme_options') || current_user_can('manage_woocommerce');
    }
}

if (!function_exists('nj_tienda_admin_menu')) {
    function nj_tienda_admin_menu() {
        if (!function_exists('add_menu_page')) {
            return;
        }
        add_menu_page(
            'Mi tienda',
            'Mi tienda',
            'edit_theme_options',
            'nj-tienda',
            'nj_tienda_admin_page',
            'dashicons-store',
            3
        );
    }
}
add_action('admin_menu', 'nj_tienda_admin_menu');

if (!function_exists('nj_tienda_admin_assets')) {
    function nj_tienda_admin_assets($hook) {
        if ($hook !== 'toplevel_page_nj-tienda') {
            return;
        }
        if (function_exists('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        $uri = get_template_directory_uri();
        wp_enqueue_style('nj-tienda', $uri . '/assets/mitienda.css', array('nj-hero-admin'), '1.1.24');
        wp_enqueue_script('nj-tienda', $uri . '/assets/mitienda.js', array('jquery', 'nj-hero-admin'), '1.1.24', true);
    }
}
add_action('admin_enqueue_scripts', 'nj_tienda_admin_assets', 20);

if (!function_exists('nj_tienda_notice')) {
    function nj_tienda_notice() {
        $code = isset($_GET['updated']) ? sanitize_key(wp_unslash($_GET['updated'])) : '';
        $messages = array(
            'campanas' => 'Las campañas quedaron guardadas.',
            'cuentos' => 'El carrusel de cuentos quedó guardado.',
            'categorias' => 'Las categorías quedaron guardadas.',
            'pills' => 'La fila de la tienda quedó guardada.',
            'regalo' => 'Las franjas quedaron guardadas.',
            'looks' => 'Los looks quedaron guardados.',
            'prenda' => 'La prenda quedó guardada.',
            'oculta' => 'La prenda ya no se muestra en la tienda.',
            'prenda-falta' => 'Para publicar una prenda hacen falta la foto, el nombre, el precio y al menos una talla.',
            'prenda-talla' => 'Marca al menos una talla y cuántas hay, para que se pueda comprar.',
            'prenda-off' => 'La tienda todavía no puede recibir prendas.',
            'blog' => 'La nota quedó publicada en el blog.',
            'blog-oculta' => 'La nota ya no se muestra en el blog.',
            'blog-falta' => 'Para publicar una nota hacen falta el título y el texto.',
            'whatsapp' => 'WhatsApp quedó guardado. El comprobante sale cuando un pedido se paga.',
            'whatsapp-plantillas' => 'Las plantillas se enviaron a Meta. Meta tiene que aprobarlas.',
        );
        if (!isset($messages[$code])) {
            return;
        }
        $error = $code === 'prenda-falta' || $code === 'prenda-off' || $code === 'prenda-talla' || $code === 'blog-falta';
        echo '<div class="notice notice-' . ($error ? 'error' : 'success') . ' is-dismissible"><p>' . esc_html($messages[$code]) . '</p></div>';
    }
}

if (!function_exists('nj_tienda_admin_page')) {
    function nj_tienda_admin_page() {
        if (!nj_tienda_can()) {
            return;
        }
        ?>
        <div class="wrap nj-studio-page">
          <h1>Mi tienda</h1>
          <?php nj_tienda_notice(); ?>
          <p class="nj-studio-lead">Elige qué quieres cambiar, sube la foto o la prenda y pulsa Guardar. No hace falta entrar a otra pantalla.</p>
          <div class="nj-studio">
            <nav class="nj-studio-nav" aria-label="Espacios de la portada">
              <button type="button" data-panel="banner" class="is-on">Banner principal</button>
              <button type="button" data-panel="cuentos">Carrusel de cuentos</button>
              <button type="button" data-panel="campanas">Campañas</button>
              <button type="button" data-panel="regalo">Franja de la tienda</button>
              <button type="button" data-panel="categorias">Categorías</button>
              <button type="button" data-panel="looks">Looks</button>
              <button type="button" data-panel="prendas">Prendas</button>
              <button type="button" data-panel="blog">Blog</button>
              <button type="button" data-panel="whatsapp">WhatsApp</button>
            </nav>
            <div class="nj-studio-main">
              <section class="nj-screen is-on" data-screen="banner" id="banner">
                <h2>Banner principal</h2>
                <?php nj_hero_admin_page(); ?>
              </section>
              <section class="nj-screen" data-screen="cuentos" id="cuentos">
                <?php nj_espacios_panel_cuentos(); ?>
              </section>
              <section class="nj-screen" data-screen="campanas" id="campanas">
                <?php nj_espacios_panel_promo(); ?>
              </section>
              <section class="nj-screen" data-screen="regalo" id="regalo">
                <?php if (function_exists('nj_espacios_panel_regalo')) { nj_espacios_panel_regalo(); } ?>
              </section>
              <section class="nj-screen" data-screen="categorias" id="categorias">
                <?php nj_espacios_panel_styles(); ?>
              </section>
              <section class="nj-screen" data-screen="looks" id="looks">
                <?php nj_espacios_panel_looks(); ?>
              </section>
              <section class="nj-screen" data-screen="prendas" id="prendas">
                <?php nj_prendas_admin_panel(); ?>
              </section>
              <section class="nj-screen" data-screen="blog" id="blog">
                <?php if (function_exists('nj_blog_admin_panel')) { nj_blog_admin_panel(); } ?>
              </section>
              <section class="nj-screen" data-screen="whatsapp" id="whatsapp">
                <?php if (function_exists('nj_whatsapp_panel')) { nj_whatsapp_panel(); } ?>
              </section>
            </div>
          </div>
        </div>
        <?php
    }
}
