<?php
/**
 * Formulario simple de prendas.
 */

if (!function_exists('nj_prendas_admin_categories')) {
    function nj_prendas_admin_categories($selected) {
        $selected = array_map('intval', (array) $selected);
        ?>
        <fieldset class="nj-cats">
          <legend>Categorías</legend>
          <?php foreach (nj_prendas_terms() as $term) : ?>
            <label class="nj-check">
              <input type="checkbox" name="categorias[]" value="<?php echo (int) $term->term_id; ?>"<?php echo in_array((int) $term->term_id, $selected, true) ? ' checked' : ''; ?>>
              <?php echo esc_html($term->name); ?>
            </label>
          <?php endforeach; ?>
        </fieldset>
        <?php
    }
}

if (!function_exists('nj_prendas_admin_attributes')) {
    function nj_prendas_admin_attributes($product) {
        $tallas = $product ? nj_prendas_attribute_labels($product, 'talla') : array('');
        if (!$tallas) {
            $tallas = array('');
        }
        $colores = $product ? nj_prendas_attribute_labels($product, 'color') : array();
        $stock = $product ? nj_prendas_stock_lookup($product) : array();
        $color_labels = $colores ? $colores : array('');
        ?>
        <div data-nj-attrs>
          <div data-nj-attr="talla">
            <p class="nj-prenda-note">Tallas y existencias. Escribe las que tiene esta prenda, por ejemplo 2, 4, 6, 8 y 10.</p>
            <div data-nj-attr-list>
              <?php foreach ($tallas as $talla) : ?>
                <div class="nj-attr-row" data-nj-attr-row>
                  <input type="text" name="talla_nombre[]" value="<?php echo esc_attr($talla); ?>" placeholder="Talla">
                  <div data-nj-stocks>
                    <?php foreach ($color_labels as $color) :
                        $key = nj_prendas_size_slug($talla) . '|' . nj_prendas_size_slug($color);
                        $qty = ($talla !== '' && isset($stock[$key])) ? (int) $stock[$key] : 1;
                        ?>
                      <label class="nj-celda" data-nj-stock>
                        <?php if ($color !== '') : ?><span><?php echo esc_html($color); ?></span><?php endif; ?>
                        <input type="number" min="0" max="999" name="talla_celda[]" value="<?php echo esc_attr((string) $qty); ?>" aria-label="Existencias">
                      </label>
                    <?php endforeach; ?>
                  </div>
                  <button type="button" class="button" data-nj-attr-remove>Quitar</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="button" data-nj-attr-add="talla">Agregar talla</button>
          </div>
          <div data-nj-attr="color">
            <p class="nj-prenda-note">Colores. Con un solo color se elige la talla. Con varios, se elige talla y color.</p>
            <div data-nj-attr-list>
              <?php foreach ($colores as $color) : ?>
                <div class="nj-attr-row" data-nj-attr-row>
                  <input type="text" name="color_nombre[]" value="<?php echo esc_attr($color); ?>" placeholder="Color">
                  <button type="button" class="button" data-nj-attr-remove>Quitar</button>
                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="button" data-nj-attr-add="color">Agregar color</button>
          </div>
        </div>
        <?php
    }
}

if (!function_exists('nj_prendas_admin_gallery')) {
    function nj_prendas_admin_gallery($ids) {
        ?>
        <div class="nj-gallery-block">
          <p class="nj-prenda-note">Otras fotos de esta prenda. En la ficha se ven junto a la foto principal.</p>
          <div class="nj-gallery" data-nj-gallery>
            <?php foreach ((array) $ids as $id) :
                $id = (int) $id;
                $url = $id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($id, 'thumbnail') : '';
                if (!$url) {
                    continue;
                }
                ?>
              <span class="nj-gallery-item" data-nj-gallery-item>
                <img alt="" src="<?php echo esc_url($url); ?>">
                <input type="hidden" name="galeria[]" value="<?php echo (int) $id; ?>">
                <button type="button" data-nj-gallery-remove aria-label="Quitar foto">×</button>
              </span>
            <?php endforeach; ?>
          </div>
          <button type="button" class="button" data-nj-gallery-add>Agregar fotos</button>
        </div>
        <?php
    }
}

if (!function_exists('nj_prendas_admin_fields')) {
    function nj_prendas_admin_fields($product) {
        $image_id = $product ? (int) $product->get_image_id() : 0;
        $src = $image_id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $price = '';
        $regular = 0;
        $flags = array('nuevo' => true, 'coleccion' => true);
        if ($product) {
            list($amount, $regular) = nj_prendas_prices($product);
            $price = (string) $amount;
            $flags = nj_prendas_home_flags($product->get_id());
        }
        ?>
        <div class="nj-modal-scroll">
          <div class="nj-modal-top">
            <div class="nj-modal-photo" data-nj-photo>
              <div class="nj-modal-frame">
                <img alt=""<?php echo $src ? ' src="' . esc_url($src) . '"' : ' hidden'; ?>>
                <span class="nj-modal-photo__ph">Foto principal</span>
              </div>
              <input data-nj-id type="hidden" name="foto" value="<?php echo (int) $image_id; ?>">
              <button type="button" class="button" data-nj-pick><?php echo $product ? 'Cambiar foto' : 'Subir foto'; ?></button>
            </div>
            <section class="nj-modal-section">
              <h3>Datos</h3>
              <label>Nombre
                <input type="text" name="nombre" value="<?php echo esc_attr($product ? $product->get_name() : ''); ?>" required>
              </label>
              <div class="nj-modal-pair">
                <label>Código
                  <input type="text" name="sku" value="<?php echo esc_attr($product ? $product->get_sku() : ''); ?>" placeholder="NJ-001">
                </label>
                <label>Precio
                  <input type="text" name="precio" inputmode="numeric" value="<?php echo esc_attr($price); ?>" placeholder="32000" required>
                </label>
                <label>Precio anterior
                  <input type="text" name="precio_antes" inputmode="numeric" value="<?php echo $regular > (int) $price ? esc_attr((string) $regular) : ''; ?>" placeholder="54000">
                </label>
              </div>
            </section>
          </div>
          <div class="nj-modal-main">
            <section class="nj-modal-section">
              <h3>Textos</h3>
              <div class="nj-modal-texts">
                <label>Descripción corta
                  <textarea name="descripcion" rows="4" placeholder="Tallas, color y una frase para la ficha."><?php echo esc_textarea($product ? nj_prendas_plain($product->get_short_description()) : ''); ?></textarea>
                </label>
                <label>Descripción
                  <textarea name="descripcion_larga" rows="4" placeholder="El texto largo que se lee en la ficha."><?php echo esc_textarea($product ? nj_prendas_plain($product->get_description()) : ''); ?></textarea>
                </label>
              </div>
            </section>
            <section class="nj-modal-section">
              <h3>Dónde se muestra</h3>
              <?php nj_prendas_admin_categories($product ? $product->get_category_ids() : array()); ?>
              <label>Etiquetas
                <input type="text" name="etiquetas" value="<?php echo esc_attr($product ? nj_prendas_tag_text($product) : ''); ?>" placeholder="k-pop, blusa">
              </label>
              <label class="nj-check"><input type="checkbox" name="en_nuevo" value="1"<?php echo $flags['nuevo'] ? ' checked' : ''; ?>> Mostrar en Lo nuevo</label>
              <label class="nj-check"><input type="checkbox" name="en_coleccion" value="1"<?php echo $flags['coleccion'] ? ' checked' : ''; ?>> Mostrar en Colección</label>
            </section>
            <section class="nj-modal-section">
              <h3>Tallas y color</h3>
              <?php nj_prendas_admin_attributes($product); ?>
            </section>
            <section class="nj-modal-section">
              <h3>Más fotos</h3>
              <?php nj_prendas_admin_gallery($product ? $product->get_gallery_image_ids() : array()); ?>
            </section>
          </div>
        </div>
        <?php
    }
}

if (!function_exists('nj_prendas_admin_panel')) {
    function nj_prendas_admin_panel() {
        $ready = class_exists('WC_Product_Simple') && function_exists('wc_get_products');
        ?>
        <h2>Prendas</h2>
        <p>Cada prenda es una tarjeta. Ábrela para editar la foto, los textos, el precio, las categorías, las tallas y el color. El precio se escribe sin puntos: 32000 es $ 32.000.</p>
        <?php if (!$ready) : ?>
          <p>La tienda todavía no está lista para recibir prendas.</p>
          <?php
            return;
        endif;
        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => 100,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
        $used = array();
        foreach ($products as $product) {
            foreach ($product->get_category_ids() as $term_id) {
                $used[(int) $term_id] = true;
            }
        }
        ?>
        <div class="nj-prenda-filters">
          <div class="nj-prenda-filters__cats">
            <button type="button" class="is-on" data-nj-cat-filter="">Todas</button>
            <?php foreach (nj_prendas_terms() as $term) :
                if (!isset($used[(int) $term->term_id])) {
                    continue;
                }
                ?>
              <button type="button" data-nj-cat-filter="<?php echo (int) $term->term_id; ?>"><?php echo esc_html($term->name); ?></button>
            <?php endforeach; ?>
          </div>
          <button type="button" class="nj-prenda-stock" data-nj-stock-filter>Por agotarse</button>
        </div>
        <div class="nj-prenda-grid">
          <button type="button" class="nj-prenda-tile nj-prenda-tile--new" data-nj-open="nj-prenda-nueva">Nueva prenda</button>
          <?php foreach ($products as $product) :
              $id = $product->get_id();
              $image_id = (int) $product->get_image_id();
              $src = $image_id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($image_id, 'medium') : '';
              list($price) = nj_prendas_prices($product);
              $shown = function_exists('wc_price') ? wp_strip_all_tags(wc_price($price)) : ('$ ' . number_format((int) $price, 0, ',', '.'));
              $level = nj_prendas_stock_level($product);
              $cats = implode(',', array_map('intval', $product->get_category_ids()));
              ?>
            <button type="button" class="nj-prenda-tile" data-nj-prenda data-nj-open="nj-prenda-<?php echo (int) $id; ?>" data-nj-cats="<?php echo esc_attr($cats); ?>" data-nj-level="<?php echo esc_attr($level); ?>">
              <?php if ($src) : ?><img alt="" src="<?php echo esc_url($src); ?>"><?php else : ?><span class="nj-prenda-tile__empty">Sin foto</span><?php endif; ?>
              <?php if ($level !== 'ok') : ?><em class="nj-prenda-flag is-<?php echo esc_attr($level); ?>"><?php echo $level === 'out' ? 'Agotada' : 'Por agotarse'; ?></em><?php endif; ?>
              <strong><?php echo esc_html($product->get_name()); ?></strong>
              <span><?php echo esc_html($shown); ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <p class="nj-prenda-none" data-nj-prenda-empty hidden>No hay prendas con este filtro.</p>
        <?php if (!$products) : ?>
          <p>Todavía no hay prendas publicadas.</p>
        <?php endif; ?>
        <dialog class="nj-modal" id="nj-prenda-nueva">
          <form class="nj-prenda-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <div class="nj-modal-bar">
              <strong>Nueva prenda</strong>
              <button type="button" class="button" data-nj-close>Cerrar</button>
            </div>
            <input type="hidden" name="action" value="nj_prenda_save">
            <input type="hidden" name="prenda_id" value="0">
            <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
            <?php nj_prendas_admin_fields(null); ?>
            <p class="nj-modal-foot"><button type="submit" class="button button-primary">Publicar prenda</button></p>
          </form>
        </dialog>
        <?php foreach ($products as $product) :
            $id = $product->get_id();
            ?>
          <dialog class="nj-modal" id="nj-prenda-<?php echo (int) $id; ?>">
            <form class="nj-prenda-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
              <div class="nj-modal-bar">
                <strong><?php echo esc_html($product->get_name()); ?></strong>
                <span class="nj-prenda-actions">
                  <a class="button" href="<?php echo esc_url(get_permalink($id)); ?>" target="_blank" rel="noopener">Ver prenda</a>
                  <button type="button" class="button" data-nj-close>Cerrar</button>
                </span>
              </div>
              <input type="hidden" name="action" value="nj_prenda_save">
              <input type="hidden" name="prenda_id" value="<?php echo (int) $id; ?>">
              <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
              <?php nj_prendas_admin_fields($product); ?>
              <p class="nj-modal-foot">
                <button type="submit" class="button" name="ocultar" value="1">Ocultar</button>
                <button type="submit" class="button button-primary">Guardar</button>
              </p>
            </form>
          </dialog>
            <?php
        endforeach;
        ?>
            <?php
            $hidden = wc_get_products(array(
                'status' => 'draft',
                'limit' => 24,
                'orderby' => 'date',
                'order' => 'DESC',
            ));
            if ($hidden) :
                ?>
          <h3>Prendas ocultas</h3>
          <div class="nj-prenda-list">
                <?php
                foreach ($hidden as $product) :
                    $src = $product->get_image_id() ? wp_get_attachment_image_url($product->get_image_id(), 'medium') : '';
                    ?>
            <form class="nj-prenda-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
              <input type="hidden" name="action" value="nj_prenda_save">
              <input type="hidden" name="prenda_id" value="<?php echo (int) $product->get_id(); ?>">
              <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
              <?php if ($src) : ?><img alt="" src="<?php echo esc_url($src); ?>"><?php endif; ?>
              <strong><?php echo esc_html($product->get_name()); ?></strong>
              <button type="submit" class="button" name="mostrar" value="1">Volver a mostrar</button>
            </form>
                    <?php
                endforeach;
                ?>
          </div>
                <?php
            endif;
    }
}
