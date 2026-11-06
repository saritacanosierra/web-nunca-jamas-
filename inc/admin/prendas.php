<?php
/**
 * Formulario simple de prendas.
 */

if (!function_exists('nj_prendas_admin_panel')) {
    function nj_prendas_admin_panel() {
        $ready = class_exists('WC_Product_Simple') && function_exists('wc_get_products');
        ?>
        <h2>Prendas</h2>
        <p>Sube la foto, el nombre y el precio. El precio se escribe sin puntos: 32000 es $ 32.000. Si marcas Lo nuevo o Colección, la portada muestra esas prendas. Si no marcas ninguna, siguen saliendo las últimas.</p>
        <?php if (!$ready) : ?>
          <p>La tienda todavía no está lista para recibir prendas.</p>
          <?php
            return;
        endif;
        $terms = nj_prendas_terms();
        ?>
        <form class="nj-prenda-new" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-nj-photo>
          <input type="hidden" name="action" value="nj_prenda_save">
          <input type="hidden" name="prenda_id" value="0">
          <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
          <div class="nj-prenda-new__photo">
            <img alt="" hidden>
            <input data-nj-id type="hidden" name="foto" value="0">
            <button type="button" class="button button-primary" data-nj-pick>Subir foto</button>
          </div>
          <div class="nj-prenda-new__fields">
            <label>Nombre
              <input type="text" name="nombre" required>
            </label>
            <label>Precio
              <input type="text" name="precio" inputmode="numeric" placeholder="32000" required>
            </label>
            <label>Precio anterior, si está en oferta
              <input type="text" name="precio_antes" inputmode="numeric" placeholder="54000">
            </label>
            <label>Parte de la tienda
              <select name="categoria">
                <option value="">Elige una</option>
                <?php foreach ($terms as $term) : ?>
                  <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
                <?php endforeach; ?>
              </select>
            </label>
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
                $variable = $product->is_type('variable');
                $price = (int) round((float) $product->get_price());
                $regular = (int) round((float) $product->get_regular_price());
                $cats = $product->get_category_ids();
                $slug = '';
                if ($cats && function_exists('get_term')) {
                    $term = get_term((int) $cats[0], 'product_cat');
                    if ($term && !is_wp_error($term)) {
                        $slug = $term->slug;
                    }
                }
                ?>
            <form class="nj-prenda-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-nj-photo>
              <input type="hidden" name="action" value="nj_prenda_save">
              <input type="hidden" name="prenda_id" value="<?php echo (int) $id; ?>">
              <?php wp_nonce_field('nj_prenda_save', 'nj_prenda_nonce'); ?>
              <img alt="" src="<?php echo esc_url((string) $src); ?>"<?php echo $src ? '' : ' hidden'; ?>>
              <input data-nj-id type="hidden" name="foto" value="<?php echo (int) $image_id; ?>">
              <button type="button" class="button" data-nj-pick>Cambiar foto</button>
              <label>Nombre
                <input type="text" name="nombre" value="<?php echo esc_attr($product->get_name()); ?>">
              </label>
              <?php if ($variable) : ?>
                <p class="nj-prenda-note">Esta prenda tiene tallas. Aquí cambias la foto, el nombre y si sale en la portada.</p>
              <?php else : ?>
                <label>Precio
                  <input type="text" name="precio" inputmode="numeric" value="<?php echo esc_attr((string) $price); ?>">
                </label>
                <label>Precio anterior
                  <input type="text" name="precio_antes" inputmode="numeric" value="<?php echo $regular > $price ? esc_attr((string) $regular) : ''; ?>">
                </label>
              <?php endif; ?>
              <label>Parte de la tienda
                <select name="categoria">
                  <option value="">Sin cambiar</option>
                  <?php foreach ($terms as $term) : ?>
                    <option value="<?php echo esc_attr($term->slug); ?>"<?php echo $slug === $term->slug ? ' selected' : ''; ?>><?php echo esc_html($term->name); ?></option>
                  <?php endforeach; ?>
                </select>
              </label>
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
