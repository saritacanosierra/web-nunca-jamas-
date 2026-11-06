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

if (!function_exists('nj_prendas_admin_panel')) {
    function nj_prendas_admin_panel() {
        $ready = class_exists('WC_Product_Simple') && function_exists('wc_get_products');
        ?>
        <h2>Prendas</h2>
        <p>Aquí se llena lo mismo que en el producto de WooCommerce: descripción, descripción corta, código, precio, categorías, etiquetas, fotos, color y tallas con existencias. Al guardar queda almacenado en la prenda. El precio se escribe sin puntos: 32000 es $ 32.000.</p>
        <?php if (!$ready) : ?>
          <p>La tienda todavía no está lista para recibir prendas.</p>
          <?php
            return;
        endif;
        ?>
        <form class="nj-prenda-new" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="nj_prenda_save">
          <input type="hidden" name="prenda_id" value="0">
          <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
          <div class="nj-prenda-new__photo" data-nj-photo>
            <img alt="" hidden>
            <input data-nj-id type="hidden" name="foto" value="0">
            <button type="button" class="button button-primary" data-nj-pick>Subir foto</button>
          </div>
          <div class="nj-prenda-new__fields">
            <label>Nombre
              <input type="text" name="nombre" required>
            </label>
            <label>Descripción
              <textarea name="descripcion_larga" rows="6" placeholder="El texto largo que se lee en la ficha."></textarea>
            </label>
            <label>Descripción corta
              <textarea name="descripcion" rows="3" placeholder="Tallas, color y una frase para la ficha."></textarea>
            </label>
            <label>Código
              <input type="text" name="sku" placeholder="NJ-001">
            </label>
            <label>Precio
              <input type="text" name="precio" inputmode="numeric" placeholder="32000" required>
            </label>
            <label>Precio anterior, si está en oferta
              <input type="text" name="precio_antes" inputmode="numeric" placeholder="54000">
            </label>
            <?php nj_prendas_admin_categories(array()); ?>
            <label>Etiquetas
              <input type="text" name="etiquetas" placeholder="k-pop, blusa">
            </label>
            <?php nj_prendas_admin_attributes(null); ?>
            <?php nj_prendas_admin_gallery(array()); ?>
            <label class="nj-check"><input type="checkbox" name="en_nuevo" value="1" checked> Mostrar en Lo nuevo</label>
            <label class="nj-check"><input type="checkbox" name="en_coleccion" value="1" checked> Mostrar en Colección</label>
            <button type="submit" class="button button-primary">Publicar prenda</button>
          </div>
        </form>
        <div class="nj-prenda-list">
          <?php
            $products = wc_get_products(array(
                'status' => 'publish',
                'limit' => 48,
                'orderby' => 'date',
                'order' => 'DESC',
            ));
            if (!$products) :
                ?>
            <p>Todavía no hay prendas publicadas.</p>
                <?php
            endif;
            foreach ($products as $product) :
                $id = $product->get_id();
                $flags = nj_prendas_home_flags($id);
                $image_id = (int) $product->get_image_id();
                $src = $image_id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($image_id, 'medium') : '';
                list($price, $regular) = nj_prendas_prices($product);
                ?>
            <form class="nj-prenda-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
              <input type="hidden" name="action" value="nj_prenda_save">
              <input type="hidden" name="prenda_id" value="<?php echo (int) $id; ?>">
              <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
              <div data-nj-photo>
                <img alt="" src="<?php echo esc_url((string) $src); ?>"<?php echo $src ? '' : ' hidden'; ?>>
                <input data-nj-id type="hidden" name="foto" value="<?php echo (int) $image_id; ?>">
                <button type="button" class="button" data-nj-pick>Cambiar foto</button>
              </div>
              <p class="nj-prenda-actions"><a class="button" href="<?php echo esc_url(get_permalink($id)); ?>" target="_blank" rel="noopener">Ver prenda</a></p>
              <label>Nombre
                <input type="text" name="nombre" value="<?php echo esc_attr($product->get_name()); ?>">
              </label>
              <label>Descripción
                <textarea name="descripcion_larga" rows="6"><?php echo esc_textarea(nj_prendas_plain($product->get_description())); ?></textarea>
              </label>
              <label>Descripción corta
                <textarea name="descripcion" rows="3"><?php echo esc_textarea(nj_prendas_plain($product->get_short_description())); ?></textarea>
              </label>
              <label>Código
                <input type="text" name="sku" value="<?php echo esc_attr($product->get_sku()); ?>">
              </label>
              <label>Precio
                <input type="text" name="precio" inputmode="numeric" value="<?php echo esc_attr((string) $price); ?>">
              </label>
              <label>Precio anterior
                <input type="text" name="precio_antes" inputmode="numeric" value="<?php echo $regular > $price ? esc_attr((string) $regular) : ''; ?>">
              </label>
              <?php nj_prendas_admin_categories($product->get_category_ids()); ?>
              <label>Etiquetas
                <input type="text" name="etiquetas" value="<?php echo esc_attr(nj_prendas_tag_text($product)); ?>">
              </label>
              <?php nj_prendas_admin_attributes($product); ?>
              <?php nj_prendas_admin_gallery($product->get_gallery_image_ids()); ?>
              <label class="nj-check"><input type="checkbox" name="en_nuevo" value="1"<?php echo $flags['nuevo'] ? ' checked' : ''; ?>> Lo nuevo</label>
              <label class="nj-check"><input type="checkbox" name="en_coleccion" value="1"<?php echo $flags['coleccion'] ? ' checked' : ''; ?>> Colección</label>
              <p class="nj-prenda-actions">
                <button type="submit" class="button button-primary">Guardar</button>
                <button type="submit" class="button nj-prenda-hide" name="ocultar" value="1">Ocultar</button>
              </p>
            </form>
                <?php
            endforeach;
            ?>
        </div>
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
