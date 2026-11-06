<?php
/**
 * Pantalla de Editar el tema. Los datos viven en inc/tema.php.
 */

if (!function_exists('nj_tema_page_select')) {
    function nj_tema_page_select($name, $selected) {
        $pages = function_exists('get_pages') ? get_pages(array(
            'sort_column' => 'post_title',
            'post_status' => 'publish',
        )) : array();
        echo '<select name="' . esc_attr($name) . '">';
        echo '<option value="0">Elige una página</option>';
        foreach ($pages as $page) {
            echo '<option value="' . (int) $page->ID . '"' . selected((int) $selected, (int) $page->ID, false) . '>' . esc_html($page->post_title) . '</option>';
        }
        echo '</select>';
    }
}

if (!function_exists('nj_tema_menu_fields')) {
    function nj_tema_menu_fields($name, $item) {
        $children = isset($item['children']) && is_array($item['children']) ? $item['children'] : array();
        ?>
        <div class="nj-menu-group" data-nj-menu-group data-nj-menu-name="<?php echo esc_attr($name); ?>">
          <label>Nombre
            <input type="text" name="<?php echo esc_attr($name); ?>[label]" value="<?php echo esc_attr(isset($item['label']) ? $item['label'] : ''); ?>">
          </label>
          <label>Enlace
            <input type="url" name="<?php echo esc_attr($name); ?>[href]" value="<?php echo esc_attr(isset($item['href']) ? $item['href'] : ''); ?>" placeholder="https://">
          </label>
          <div data-nj-menu-children>
            <?php foreach ($children as $index => $child) : ?>
              <div class="nj-menu-row">
                <input type="text" name="<?php echo esc_attr($name); ?>[children][<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr(isset($child['label']) ? $child['label'] : ''); ?>" placeholder="Nombre">
                <input type="url" name="<?php echo esc_attr($name); ?>[children][<?php echo (int) $index; ?>][href]" value="<?php echo esc_attr(isset($child['href']) ? $child['href'] : ''); ?>" placeholder="https://">
                <button type="button" class="button" data-nj-menu-remove>Quitar</button>
              </div>
            <?php endforeach; ?>
          </div>
          <p><button type="button" class="button" data-nj-menu-add>Agregar enlace</button></p>
        </div>
        <?php
    }
}

if (!function_exists('nj_tema_admin_page')) {
    function nj_tema_admin_page() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            return;
        }
        $settings = nj_tema_settings();
        $logo = $settings['logo'] ? nj_tema_logo_url() : '';
        $icon_id = function_exists('get_option') ? (int) get_option('site_icon') : 0;
        $icon = $icon_id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($icon_id, 'full') : '';
        $colors = array(
            'coral' => 'Rosa',
            'teal' => 'Verde',
            'gold' => 'Dorado',
            'ink' => 'Texto',
            'bg' => 'Fondo',
        );
        $menu = function_exists('nj_menu') ? nj_menu() : array('departments' => array(), 'collections' => array(), 'newest' => array());
        $saved = isset($_GET['updated']) && sanitize_key(wp_unslash($_GET['updated'])) === 'tema';
        $show_front = function_exists('get_option') ? get_option('show_on_front', 'posts') : 'posts';
        $notice_on = function_exists('get_option') && get_option('woocommerce_demo_store') === 'yes';
        ?>
        <div class="wrap nj-studio-page">
          <h1>Editar el tema</h1>
          <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p>El tema quedó guardado. Recarga la tienda para verlo.</p></div>
          <?php endif; ?>
          <p class="nj-studio-lead">Aquí está lo mismo que en el personalizador: la identidad, los menús, la portada, WooCommerce y el CSS. Las fotos y las prendas siguen en Mi tienda.</p>
          <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('nj_tema_save', 'nj_tema_nonce'); ?>
            <input type="hidden" name="action" value="nj_tema_save">
            <input type="hidden" name="nj_tema_section" value="identidad">
            <div class="nj-studio">
              <nav class="nj-studio-nav" aria-label="Partes del tema">
                <button type="button" data-panel="identidad" class="is-on">Identidad del sitio</button>
                <button type="button" data-panel="menus">Menús</button>
                <button type="button" data-panel="portada">Ajustes de portada</button>
                <button type="button" data-panel="woocommerce">WooCommerce</button>
                <button type="button" data-panel="css">CSS adicional</button>
              </nav>
              <div class="nj-studio-main">
                <section class="nj-screen is-on" data-screen="identidad" id="identidad">
                  <h2>Identidad del sitio</h2>
                  <div class="nj-tema-fields">
                    <label>Nombre de la tienda
                      <input type="text" name="blogname" value="<?php echo esc_attr(get_option('blogname', 'Nunca Jamás')); ?>">
                    </label>
                    <label>Descripción corta
                      <input type="text" name="blogdescription" value="<?php echo esc_attr(get_option('blogdescription', '')); ?>">
                    </label>
                  </div>
                  <div class="nj-tema-photos">
                    <div class="nj-photo nj-photo--square" data-nj-photo>
                      <img alt=""<?php echo $logo === '' ? ' hidden' : ' src="' . esc_url($logo) . '"'; ?>>
                      <div class="nj-photo-body">
                        <strong>Logo</strong>
                        <input data-nj-id type="hidden" name="logo" value="<?php echo esc_attr((string) $settings['logo']); ?>">
                        <p class="nj-prenda-actions">
                          <button type="button" class="button button-primary" data-nj-pick>Elegir logo</button>
                          <button type="button" class="button" data-nj-clear>Quitar</button>
                        </p>
                      </div>
                    </div>
                    <div class="nj-photo nj-photo--square" data-nj-photo>
                      <img alt=""<?php echo $icon === '' ? ' hidden' : ' src="' . esc_url($icon) . '"'; ?>>
                      <div class="nj-photo-body">
                        <strong>Icono del sitio</strong>
                        <input data-nj-id type="hidden" name="icon" value="<?php echo esc_attr((string) $icon_id); ?>">
                        <p class="nj-prenda-actions">
                          <button type="button" class="button button-primary" data-nj-pick>Elegir icono</button>
                          <button type="button" class="button" data-nj-clear>Quitar</button>
                        </p>
                      </div>
                    </div>
                  </div>
                  <h3>Colores</h3>
                  <div class="nj-tema-colors">
                    <?php foreach ($colors as $key => $label) : ?>
                      <label><?php echo esc_html($label); ?>
                        <input type="color" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($settings[$key]); ?>">
                      </label>
                    <?php endforeach; ?>
                  </div>
                </section>
                <section class="nj-screen" data-screen="menus" id="menus">
                  <h2>Menús</h2>
                  <p>Estos son los enlaces de la barra de arriba. El nombre es lo que se lee. El enlace es a dónde lleva.</p>
                  <?php foreach ($menu['departments'] as $index => $item) : ?>
                    <?php nj_tema_menu_fields('menu[departments][' . (int) $index . ']', $item); ?>
                  <?php endforeach; ?>
                  <h3>Colecciones</h3>
                  <?php nj_tema_menu_fields('menu[collections]', isset($menu['collections']) ? $menu['collections'] : array()); ?>
                  <h3>Nuevo</h3>
                  <div class="nj-tema-fields">
                    <label>Nombre
                      <input type="text" name="menu[newest][label]" value="<?php echo esc_attr(isset($menu['newest']['label']) ? $menu['newest']['label'] : 'Nuevo'); ?>">
                    </label>
                    <label>Enlace
                      <input type="url" name="menu[newest][href]" value="<?php echo esc_attr(isset($menu['newest']['href']) ? $menu['newest']['href'] : ''); ?>" placeholder="Vacío abre lo nuevo de la tienda">
                    </label>
                  </div>
                </section>
                <section class="nj-screen" data-screen="portada" id="portada">
                  <h2>Ajustes de portada</h2>
                  <p>La página que se ve al entrar se arma en Mi tienda. Aquí eliges qué hace WordPress con la entrada del sitio y con las entradas del blog.</p>
                  <div class="nj-tema-choice">
                    <label><input type="radio" name="show_on_front" value="posts" <?php checked($show_front, 'posts'); ?>> Mostrar las entradas</label>
                    <label><input type="radio" name="show_on_front" value="page" <?php checked($show_front, 'page'); ?>> Una página fija</label>
                  </div>
                  <div class="nj-tema-fields">
                    <label>Página de inicio
                      <?php nj_tema_page_select('page_on_front', (int) get_option('page_on_front')); ?>
                    </label>
                    <label>Página de entradas
                      <?php nj_tema_page_select('page_for_posts', (int) get_option('page_for_posts')); ?>
                    </label>
                  </div>
                </section>
                <section class="nj-screen" data-screen="woocommerce" id="woocommerce">
                  <h2>WooCommerce</h2>
                  <?php if (!class_exists('WooCommerce')) : ?>
                    <p>La tienda todavía no está conectada.</p>
                  <?php else : ?>
                    <label class="nj-check"><input type="checkbox" name="store_notice" value="1" <?php checked($notice_on); ?>> Mostrar un aviso arriba de la tienda</label>
                    <div class="nj-tema-fields">
                      <label>Texto del aviso
                        <input type="text" name="store_notice_text" value="<?php echo esc_attr((string) get_option('woocommerce_demo_store_notice', '')); ?>">
                      </label>
                      <label>Productos por fila
                        <input type="number" name="catalog_columns" min="2" max="4" value="<?php echo esc_attr((string) get_option('woocommerce_catalog_columns', 4)); ?>">
                      </label>
                      <label>Filas por página
                        <input type="number" name="catalog_rows" min="1" max="8" value="<?php echo esc_attr((string) get_option('woocommerce_catalog_rows', 4)); ?>">
                      </label>
                      <label>Página de la tienda
                        <?php nj_tema_page_select('wc_page_shop', (int) get_option('woocommerce_shop_page_id')); ?>
                      </label>
                      <label>Página del carrito
                        <?php nj_tema_page_select('wc_page_cart', (int) get_option('woocommerce_cart_page_id')); ?>
                      </label>
                      <label>Página de finalizar compra
                        <?php nj_tema_page_select('wc_page_checkout', (int) get_option('woocommerce_checkout_page_id')); ?>
                      </label>
                      <label>Página de mi cuenta
                        <?php nj_tema_page_select('wc_page_myaccount', (int) get_option('woocommerce_myaccount_page_id')); ?>
                      </label>
                    </div>
                  <?php endif; ?>
                </section>
                <section class="nj-screen" data-screen="css" id="css">
                  <h2>CSS adicional</h2>
                  <p>Reglas extra para afinar el tema. Se aplican después de los colores.</p>
                  <textarea name="extra_css" rows="14" class="nj-tema-css" placeholder=".site-header { }"><?php echo esc_textarea($settings['css']); ?></textarea>
                </section>
              </div>
            </div>
            <p class="nj-studio-save">
              <button type="submit" class="button button-primary">Guardar tema</button>
              <button type="submit" class="button" name="nj_tema_reset" value="1">Volver a los colores y al logo original</button>
            </p>
          </form>
        </div>
        <?php
    }
}
