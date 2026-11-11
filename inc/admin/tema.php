<?php
/**
 * Pantalla de Editar el tema. Los datos viven en inc/tema.php.
 */

if (!function_exists('nj_tema_secreto')) {
    function nj_tema_secreto($name, $filled) {
        $value = $filled && function_exists('nj_pagos_secreto_mascara') ? nj_pagos_secreto_mascara() : '';
        echo '<input type="password" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" autocomplete="new-password">';
    }
}

if (!function_exists('nj_tema_page_select')) {
    function nj_tema_page_select($name, $selected) {
        $pages = function_exists('get_pages') ? get_pages(array(
            'sort_column' => 'post_title',
            'post_status' => 'publish',
        )) : array();
        echo '<select name="' . esc_attr($name) . '">';
        echo '<option value="0">Elige una página</option>';
        foreach ($pages as $page) {
            $mark = function_exists('selected') ? selected((int) $selected, (int) $page->ID, false) : ((int) $selected === (int) $page->ID ? " selected='selected'" : '');
            echo '<option value="' . (int) $page->ID . '"' . $mark . '>' . esc_html($page->post_title) . '</option>';
        }
        echo '</select>';
    }
}

if (!function_exists('nj_tema_menu_count')) {
    function nj_tema_menu_count($total) {
        $total = (int) $total;
        return $total === 1 ? '1 enlace' : $total . ' enlaces';
    }
}

if (!function_exists('nj_tema_link_editor')) {
    function nj_tema_link_editor($name, $links, $add_label, $max = 8) {
        $links = is_array($links) ? $links : array();
        ?>
        <div data-nj-link-list data-nj-link-name="<?php echo esc_attr($name); ?>" data-nj-link-max="<?php echo (int) $max; ?>">
          <?php foreach ($links as $index => $link) : ?>
            <div class="nj-menu-row">
              <input type="text" name="<?php echo esc_attr($name); ?>[<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr(isset($link['label']) ? $link['label'] : ''); ?>" placeholder="Nombre">
              <input type="url" name="<?php echo esc_attr($name); ?>[<?php echo (int) $index; ?>][href]" value="<?php echo esc_attr(isset($link['href']) ? $link['href'] : ''); ?>" placeholder="https://">
              <button type="button" class="button" data-nj-link-remove>Quitar</button>
            </div>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-link-add><?php echo esc_html($add_label); ?></button></p>
        <?php
    }
}

if (!function_exists('nj_tema_pill_editor')) {
    function nj_tema_pill_editor($name, $items, $add_label, $max = 8) {
        $items = is_array($items) ? $items : array();
        ?>
        <div data-nj-pill-list data-nj-pill-name="<?php echo esc_attr($name); ?>" data-nj-pill-max="<?php echo (int) $max; ?>">
          <?php foreach ($items as $item) : ?>
            <div class="nj-menu-row nj-pill-edit">
              <input type="text" name="<?php echo esc_attr($name); ?>[]" value="<?php echo esc_attr($item); ?>">
              <button type="button" class="button" data-nj-pill-remove>Quitar</button>
            </div>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-pill-add><?php echo esc_html($add_label); ?></button></p>
        <?php
    }
}

if (!function_exists('nj_tema_menu_fields')) {
    function nj_tema_menu_fields($name, $item, $key, $with_children = true, $removable = false) {
        $children = isset($item['children']) && is_array($item['children']) ? $item['children'] : array();
        $label = isset($item['label']) ? $item['label'] : '';
        ?>
        <div class="nj-menu-group" data-nj-menu-group<?php echo $removable ? ' data-nj-menu-dept' : ''; ?> data-nj-menu-key="<?php echo esc_attr($key); ?>" data-nj-menu-name="<?php echo esc_attr($name); ?>">
          <button type="button" class="nj-menu-summary" data-nj-menu-toggle aria-expanded="false">
            <span data-nj-menu-title><?php echo esc_html($label !== '' ? $label : 'Menú'); ?></span>
            <span class="nj-menu-count" data-nj-menu-count><?php echo esc_html($with_children ? nj_tema_menu_count(count($children)) : 'Enlace de la barra'); ?></span>
          </button>
          <div class="nj-menu-body">
            <div class="nj-menu-parent">
              <label>Nombre en la barra
                <input type="text" data-nj-menu-label name="<?php echo esc_attr($name); ?>[label]" value="<?php echo esc_attr($label); ?>">
              </label>
              <label>Enlace
                <input type="url" name="<?php echo esc_attr($name); ?>[href]" value="<?php echo esc_attr(isset($item['href']) ? $item['href'] : ''); ?>" placeholder="https://">
              </label>
            </div>
            <?php if ($with_children) : ?>
              <p class="nj-menu-subhead">Enlaces que se abren al tocarlo</p>
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
              <?php if ($removable) : ?>
                <p><button type="button" class="button" data-nj-dept-remove>Quitar este menú</button></p>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
        <?php
    }
}

if (!function_exists('nj_tema_help_list')) {
    function nj_tema_help_list($name, $kind, $rows, $add, $max, $keys = array()) {
        $area = $kind === 'answer' ? '1' : '0';
        $list_kind = $kind === 'line' ? 'line' : 'pair';
        ?>
        <div class="nj-help-list" data-nj-help-list data-nj-help-name="<?php echo esc_attr($name); ?>" data-nj-help-kind="<?php echo esc_attr($list_kind); ?>" data-nj-help-max="<?php echo (int) $max; ?>" data-nj-help-area="<?php echo esc_attr($area); ?>" data-nj-help-keys="<?php echo esc_attr(implode(',', $keys)); ?>">
          <?php foreach ($rows as $index => $row) : ?>
            <?php if ($kind === 'line') : ?>
              <div class="nj-menu-row nj-help-line">
                <input type="text" name="<?php echo esc_attr($name); ?>[<?php echo (int) $index; ?>]" value="<?php echo esc_attr($row); ?>">
                <button type="button" class="button" data-nj-help-remove>Quitar</button>
              </div>
            <?php else : ?>
              <?php
                $left = isset($row[$keys[0]]) ? $row[$keys[0]] : (isset($row[0]) ? $row[0] : '');
                $right = isset($row[$keys[1]]) ? $row[$keys[1]] : (isset($row[1]) ? $row[1] : '');
              ?>
              <div class="nj-help-row">
                <input type="text" name="<?php echo esc_attr($name); ?>[<?php echo (int) $index; ?>][<?php echo esc_attr($keys[0]); ?>]" value="<?php echo esc_attr($left); ?>">
                <?php if ($kind === 'answer') : ?>
                  <textarea rows="3" name="<?php echo esc_attr($name); ?>[<?php echo (int) $index; ?>][<?php echo esc_attr($keys[1]); ?>]"><?php echo esc_textarea($right); ?></textarea>
                <?php else : ?>
                  <input type="text" name="<?php echo esc_attr($name); ?>[<?php echo (int) $index; ?>][<?php echo esc_attr($keys[1]); ?>]" value="<?php echo esc_attr($right); ?>">
                <?php endif; ?>
                <button type="button" class="button" data-nj-help-remove>Quitar</button>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-help-add><?php echo esc_html($add); ?></button></p>
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
        $header = function_exists('nj_tema_header') ? nj_tema_header() : nj_tema_header_defaults();
        $footer = function_exists('nj_tema_footer') ? nj_tema_footer() : nj_tema_footer_defaults();
        $saved = isset($_GET['updated']) && sanitize_key(wp_unslash($_GET['updated'])) === 'tema';
        $show_front = function_exists('get_option') ? get_option('show_on_front', 'posts') : 'posts';
        $notice_on = function_exists('get_option') && get_option('woocommerce_demo_store') === 'yes';
        ?>
        <div class="wrap nj-studio-page">
          <h1>Editar el tema</h1>
          <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p>El tema quedó guardado. Recarga la tienda para verlo.</p></div>
          <?php endif; ?>
          <p class="nj-studio-lead">Aquí están la identidad, el encabezado, el pie, las páginas de ayuda, la portada, WooCommerce, los pagos y el CSS. Las fotos y las prendas siguen en Mi tienda.</p>
          <form method="post" action="<?php echo esc_url(function_exists('admin_url') ? admin_url('admin-post.php') : 'admin-post.php'); ?>">
            <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('nj_tema_save', 'nj_tema_nonce'); } ?>
            <input type="hidden" name="action" value="nj_tema_save">
            <input type="hidden" name="nj_tema_section" value="identidad">
            <div class="nj-studio">
              <nav class="nj-studio-nav" aria-label="Partes del tema">
                <button type="button" data-panel="identidad" class="is-on">Identidad del sitio</button>
                <button type="button" data-panel="encabezado">Encabezado</button>
                <button type="button" data-panel="pie">Pie de página</button>
                <button type="button" data-panel="ayuda">Ayuda</button>
                <button type="button" data-panel="portada">Ajustes de portada</button>
                <button type="button" data-panel="woocommerce">WooCommerce</button>
                <button type="button" data-panel="pagos">Pagos</button>
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
                        <label class="nj-promo-alto">Tamaño del logo
                          <input type="number" min="40" max="200" name="logo_alto" value="<?php echo (int) $settings['logo_alto']; ?>">
                          <small>Alto en el encabezado, de 40 a 200 px. 86 es el tamaño de ahora.</small>
                        </label>
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
                <section class="nj-screen" data-screen="encabezado" id="encabezado">
                  <h2>Encabezado</h2>
                  <p>La barra de arriba, el buscador y los menús. Agrega, cambia o quita cada enlace.</p>
                  <div class="nj-tema-fields">
                    <label>Aviso de la barra
                      <input type="text" name="header[notice]" value="<?php echo esc_attr($header['notice']); ?>" placeholder="Envíos a Colombia">
                    </label>
                    <label>Enlace del aviso
                      <input type="url" name="header[notice_href]" value="<?php echo esc_attr($header['notice_href']); ?>" placeholder="https://">
                    </label>
                    <label>Texto del buscador
                      <input type="text" name="header[search]" value="<?php echo esc_attr($header['search']); ?>">
                    </label>
                  </div>
                  <p class="nj-prenda-note">Si dejas el aviso vacío, la franja no se muestra. Un menú sin nombre se quita al guardar.</p>
                  <div class="nj-menu-bar" aria-label="Menú de la tienda">
                    <span data-nj-dept-bar>
                      <?php foreach ($menu['departments'] as $index => $item) : ?>
                        <button type="button" data-nj-menu-jump="d-<?php echo (int) $index; ?>" aria-expanded="false"><?php echo esc_html($item['label']); ?></button>
                      <?php endforeach; ?>
                    </span>
                    <span class="nj-menu-bar-gap"></span>
                    <button type="button" data-nj-menu-jump="collections" aria-expanded="false"><?php echo esc_html(isset($menu['collections']['label']) ? $menu['collections']['label'] : 'Colecciones'); ?></button>
                    <button type="button" data-nj-menu-jump="newest" aria-expanded="false"><?php echo esc_html(isset($menu['newest']['label']) ? $menu['newest']['label'] : 'Nuevo'); ?></button>
                  </div>
                  <div class="nj-menu-list">
                    <div data-nj-dept-list>
                      <?php foreach ($menu['departments'] as $index => $item) : ?>
                        <?php nj_tema_menu_fields('menu[departments][' . (int) $index . ']', $item, 'd-' . (int) $index, true, true); ?>
                      <?php endforeach; ?>
                    </div>
                    <?php nj_tema_menu_fields('menu[collections]', isset($menu['collections']) ? $menu['collections'] : array(), 'collections', true); ?>
                    <?php nj_tema_menu_fields('menu[newest]', isset($menu['newest']) ? $menu['newest'] : array(), 'newest', false); ?>
                  </div>
                  <p><button type="button" class="button" data-nj-dept-add>Agregar menú</button></p>
                </section>
                <section class="nj-screen" data-screen="pie" id="pie">
                  <h2>Pie de página</h2>
                  <p>La marca, lo que se puede comprar, la ayuda, el contacto, los pagos y la barra legal. Agrega, cambia o quita cada pieza.</p>
                  <div class="nj-tema-fields">
                    <label>Frase de la marca
                      <textarea name="footer[blurb]" rows="3"><?php echo esc_textarea($footer['blurb']); ?></textarea>
                    </label>
                    <label class="nj-check"><input type="checkbox" name="footer[news]" value="1" <?php checked($footer['news'] === '1'); ?>> Mostrar el correo de novedades</label>
                    <label>Título del correo
                      <input type="text" name="footer[news_label]" value="<?php echo esc_attr($footer['news_label']); ?>">
                    </label>
                    <label>Texto dentro del campo
                      <input type="text" name="footer[news_placeholder]" value="<?php echo esc_attr($footer['news_placeholder']); ?>">
                    </label>
                  </div>
                  <div data-nj-foot-columns>
                    <?php foreach ($footer['columns'] as $index => $column) : ?>
                      <div class="nj-menu-group is-open" data-nj-menu-group data-nj-foot-column data-nj-menu-key="col-<?php echo (int) $index; ?>">
                        <button type="button" class="nj-menu-summary" data-nj-menu-toggle aria-expanded="true">
                          <span data-nj-menu-title><?php echo esc_html($column['title']); ?></span>
                          <span class="nj-menu-count">Columna</span>
                        </button>
                        <div class="nj-menu-body">
                          <label>Título
                            <input type="text" data-nj-column-title name="footer[columns][<?php echo (int) $index; ?>][title]" value="<?php echo esc_attr($column['title']); ?>">
                          </label>
                          <?php nj_tema_link_editor('footer[columns][' . (int) $index . '][links]', $column['links'], 'Agregar enlace'); ?>
                          <p><button type="button" class="button" data-nj-column-remove>Quitar columna</button></p>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <p><button type="button" class="button" data-nj-column-add>Agregar columna</button></p>
                  <h3>Contacto</h3>
                  <div class="nj-tema-fields">
                    <label>Correo
                      <input type="email" name="footer[email]" value="<?php echo esc_attr($footer['email']); ?>">
                    </label>
                    <label>WhatsApp
                      <input type="text" name="footer[whatsapp]" value="<?php echo esc_attr($footer['whatsapp']); ?>" placeholder="573001112233" inputmode="numeric">
                    </label>
                    <label>Lugar
                      <input type="text" name="footer[place]" value="<?php echo esc_attr($footer['place']); ?>">
                    </label>
                  </div>
                  <h3>Redes</h3>
                  <?php nj_tema_link_editor('footer[socials]', $footer['socials'], 'Agregar red', 6); ?>
                  <h3>Medios de pago</h3>
                  <?php nj_tema_pill_editor('footer[pays]', $footer['pays'], 'Agregar medio'); ?>
                  <h3>Sellos</h3>
                  <?php nj_tema_pill_editor('footer[trust]', $footer['trust'], 'Agregar sello', 6); ?>
                  <h3>Barra legal</h3>
                  <div class="nj-tema-fields">
                    <label>Texto
                      <input type="text" name="footer[legal]" value="<?php echo esc_attr($footer['legal']); ?>">
                    </label>
                  </div>
                  <p class="nj-prenda-note">El año se escribe solo delante de ese texto.</p>
                  <?php nj_tema_link_editor('footer[legal_links]', $footer['legal_links'], 'Agregar enlace legal', 4); ?>
                </section>
                <section class="nj-screen" data-screen="ayuda" id="ayuda">
                  <h2>Ayuda</h2>
                  <p>Las cuatro páginas van juntas. La foto es una miniatura. Abre una página para cambiar sus textos. Un correo escrito en el texto se vuelve enlace solo.</p>
                  <div class="nj-help-grid">
                  <?php
                    $nj_help_pages = array(
                        'preguntas' => 'Preguntas frecuentes',
                        'envios' => 'Envíos',
                        'cambios' => 'Cambios',
                        'tallas' => 'Guía de tallas',
                    );
                    foreach ($nj_help_pages as $nj_help_key => $nj_help_label) :
                        $nj_help_page = function_exists('nj_pages_help') ? nj_pages_help($nj_help_key) : array();
                        if (!is_array($nj_help_page)) {
                            $nj_help_page = array();
                        }
                        $nj_help_page += array(
                            'lead' => '',
                            'image' => 0,
                            'image_url' => '',
                            'facts' => array(),
                            'steps_title' => '',
                            'steps' => array(),
                            'note' => '',
                            'table' => array(),
                            'items' => array(),
                        );
                        $nj_help_name = 'ayuda[' . $nj_help_key . ']';
                  ?>
                    <article class="nj-help-page">
                      <div class="nj-photo" data-nj-photo>
                          <img alt=""<?php echo $nj_help_page['image_url'] === '' ? ' hidden' : ' src="' . esc_url($nj_help_page['image_url']) . '"'; ?>>
                          <div class="nj-photo-body">
                            <strong>Foto al lado del texto</strong>
                            <input data-nj-id type="hidden" name="<?php echo esc_attr($nj_help_name); ?>[image]" value="<?php echo esc_attr((string) (int) $nj_help_page['image']); ?>">
                            <p class="nj-prenda-actions">
                              <button type="button" class="button button-primary" data-nj-pick>Elegir foto</button>
                              <button type="button" class="button" data-nj-clear>Quitar</button>
                            </p>
                            <p class="nj-prenda-note">Si la quitas, vuelve la foto de la marca.</p>
                          </div>
                        </div>
                      <details>
                        <summary><?php echo esc_html($nj_help_label); ?></summary>
                        <div class="nj-help-fields">
                        <label>Texto de entrada
                          <textarea name="<?php echo esc_attr($nj_help_name); ?>[lead]" rows="2"><?php echo esc_textarea($nj_help_page['lead']); ?></textarea>
                        </label>
                        <?php if ($nj_help_key === 'envios') : ?>
                          <p class="nj-menu-subhead">Datos de arriba</p>
                          <?php nj_tema_help_list($nj_help_name . '[facts]', 'pair', $nj_help_page['facts'], 'Agregar dato', 6, array('label', 'value')); ?>
                        <?php endif; ?>
                        <?php if ($nj_help_key === 'envios' || $nj_help_key === 'cambios') : ?>
                          <label>Título de los pasos
                            <input type="text" name="<?php echo esc_attr($nj_help_name); ?>[steps_title]" value="<?php echo esc_attr($nj_help_page['steps_title']); ?>">
                          </label>
                          <p class="nj-menu-subhead">Pasos</p>
                          <?php nj_tema_help_list($nj_help_name . '[steps]', 'line', $nj_help_page['steps'], 'Agregar paso', 8); ?>
                        <?php endif; ?>
                        <?php if ($nj_help_key === 'tallas') : ?>
                          <p class="nj-menu-subhead">Tabla. La primera fila es el título.</p>
                          <?php nj_tema_help_list($nj_help_name . '[table]', 'pair', $nj_help_page['table'], 'Agregar talla', 12, array('size', 'who')); ?>
                        <?php endif; ?>
                        <?php if ($nj_help_key !== 'preguntas') : ?>
                          <label>Aviso
                            <textarea name="<?php echo esc_attr($nj_help_name); ?>[note]" rows="2"><?php echo esc_textarea($nj_help_page['note']); ?></textarea>
                          </label>
                        <?php endif; ?>
                        <p class="nj-menu-subhead">Preguntas</p>
                        <?php nj_tema_help_list($nj_help_name . '[items]', 'answer', $nj_help_page['items'], 'Agregar pregunta', 12, array('q', 'a')); ?>
                        </div>
                      </details>
                    </article>
                  <?php endforeach; ?>
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
                <section class="nj-screen" data-screen="pagos" id="pagos">
                  <h2>Pagos</h2>
                  <?php
                    $nj_wompi = function_exists('nj_pagos_wompi') ? nj_pagos_wompi() : array();
                    $nj_wompi_on = function_exists('nj_pagos_wompi_on') && nj_pagos_wompi_on();
                    $nj_wompi_test = isset($nj_wompi['testmode']) && $nj_wompi['testmode'] === 'yes';
                    $nj_wompi_ready = function_exists('nj_pagos_wompi_filled') && nj_pagos_wompi_filled($nj_wompi, $nj_wompi_test);
                  ?>
                  <?php if (!$nj_wompi_on) : ?>
                    <p>Activa el plugin Wompi en Plugins. Con él la tienda cobra en pesos por Nequi, tarjeta o PSE.</p>
                  <?php else : ?>
                    <p><?php echo (!empty($nj_wompi['enabled']) && $nj_wompi['enabled'] === 'yes' && $nj_wompi_ready) ? 'Wompi está listo para cobrar.' : 'Falta activar Wompi o completar las cuatro llaves del modo que estás usando.'; ?></p>
                    <label class="nj-check"><input type="checkbox" name="wompi_enabled" value="1" <?php checked(isset($nj_wompi['enabled']) && $nj_wompi['enabled'] === 'yes'); ?>> Cobrar con Wompi</label>
                    <label class="nj-check"><input type="checkbox" name="wompi_testmode" value="1" <?php checked($nj_wompi_test); ?>> Modo de pruebas</label>
                    <p class="nj-prenda-note">Para cobrar de verdad, deja el modo de pruebas apagado y usa las llaves de producción. La llave pública empieza por pub_prod_.</p>
                    <div class="nj-tema-fields">
                      <label>Llave pública
                        <input type="text" name="wompi_public_key" value="<?php echo esc_attr(isset($nj_wompi['public_key']) ? $nj_wompi['public_key'] : ''); ?>" autocomplete="off">
                      </label>
                      <label>Llave privada
                        <?php nj_tema_secreto('wompi_private_key', !empty($nj_wompi['private_key'])); ?>
                      </label>
                      <label>Llave de eventos
                        <?php nj_tema_secreto('wompi_event_key', !empty($nj_wompi['event_secret_key'])); ?>
                      </label>
                      <label>Llave de integridad
                        <?php nj_tema_secreto('wompi_integrity_key', !empty($nj_wompi['integrity_key'])); ?>
                      </label>
                    </div>
                    <h3>Llaves de prueba</h3>
                    <div class="nj-tema-fields">
                      <label>Llave pública de prueba
                        <input type="text" name="wompi_test_public_key" value="<?php echo esc_attr(isset($nj_wompi['test_public_key']) ? $nj_wompi['test_public_key'] : ''); ?>" autocomplete="off">
                      </label>
                      <label>Llave privada de prueba
                        <?php nj_tema_secreto('wompi_test_private_key', !empty($nj_wompi['test_private_key'])); ?>
                      </label>
                      <label>Eventos de prueba
                        <?php nj_tema_secreto('wompi_test_event_key', !empty($nj_wompi['test_event_secret_key'])); ?>
                      </label>
                      <label>Integridad de prueba
                        <?php nj_tema_secreto('wompi_test_integrity_key', !empty($nj_wompi['test_integrity_key'])); ?>
                      </label>
                      <label>Dirección de eventos
                        <input type="text" readonly value="<?php echo esc_attr(home_url('/?wc-api=wc_wompi')); ?>">
                      </label>
                    </div>
                    <p class="nj-prenda-note">Copia esa dirección en el comercio de Wompi, en eventos. Los puntos indican que la llave ya está guardada. Si no los cambias, se conserva.</p>
                  <?php endif; ?>
                  <?php
                    $nj_addi = function_exists('nj_addi_settings') ? nj_addi_settings() : array('enabled' => 'no', 'slug' => '');
                    $nj_addi_plugin = function_exists('nj_addi_plugin_on') && nj_addi_plugin_on();
                    $nj_addi_gateway = $nj_addi_plugin && function_exists('nj_addi_plugin') ? nj_addi_plugin() : array();
                    $nj_addi_has_id = !empty($nj_addi_gateway['prod_client_id']);
                    $nj_addi_has_secret = !empty($nj_addi_gateway['prod_client_secret']);
                  ?>
                  <h3>Addi</h3>
                  <?php if (!$nj_addi_plugin) : ?>
                    <p>En cada ficha de $50.000 o más aparece el pago a cuotas. Para que Addi salga también al pagar, instala el plugin Addi en Plugins y vuelve a guardar aquí.</p>
                  <?php elseif (function_exists('nj_addi_plugin_ready') && nj_addi_plugin_ready()) : ?>
                    <p>Addi está listo. En el checkout aparece junto a Nequi, tarjeta o PSE, y el cliente termina el pago en Addi.</p>
                  <?php else : ?>
                    <p>Falta el ally slug, el Client ID o el Client Secret que Addi te envió. Con los tres, Addi aparece al pagar.</p>
                  <?php endif; ?>
                  <label class="nj-check"><input type="checkbox" name="addi_enabled" value="1" <?php checked($nj_addi['enabled'] === 'yes'); ?>> Mostrar Addi</label>
                  <div class="nj-tema-fields">
                    <label>Ally slug
                      <input type="text" name="addi_slug" value="<?php echo esc_attr($nj_addi['slug']); ?>" autocomplete="off" placeholder="tu-tienda-ecommerce">
                    </label>
                    <?php if ($nj_addi_plugin) : ?>
                      <label>Client ID
                        <input type="text" name="addi_client_id" value="<?php echo esc_attr($nj_addi_has_id ? $nj_addi_gateway['prod_client_id'] : ''); ?>" autocomplete="off">
                      </label>
                      <label>Client Secret
                        <?php nj_tema_secreto('addi_client_secret', $nj_addi_has_secret); ?>
                      </label>
                    <?php endif; ?>
                  </div>
                  <?php if ($nj_addi_plugin) : ?>
                    <label class="nj-check"><input type="checkbox" name="addi_testmode" value="1" <?php checked(isset($nj_addi_gateway['testmode']) && $nj_addi_gateway['testmode'] === 'yes'); ?>> Modo de pruebas de Addi</label>
                    <p class="nj-prenda-note">Los puntos del Client Secret indican que ya está guardado. Si no los cambias, se conserva. El widget de la ficha lo pone el tema.</p>
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
