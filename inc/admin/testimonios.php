<?php
/**
 * Opiniones a mano en Mi tienda.
 */

if (!function_exists('nj_testimonios_admin_fila')) {
    function nj_testimonios_admin_fila($row, $index = 0) {
        $row = is_array($row) ? $row : array();
        $slot = is_int($index) ? (string) $index : (string) $index;
        $name = isset($row['name']) ? (string) $row['name'] : '';
        $text = isset($row['text']) ? (string) $row['text'] : '';
        $when = isset($row['when']) ? (string) $row['when'] : '';
        $id = isset($row['image_id']) ? (int) $row['image_id'] : 0;
        $photo = isset($row['photo']) ? (string) $row['photo'] : '';
        ?>
        <article class="nj-testimonio" data-testimonio>
          <div class="nj-photo" data-nj-photo>
            <img alt=""<?php echo $photo === '' ? ' hidden' : ' src="' . esc_url($photo) . '"'; ?>>
            <div class="nj-photo-body">
              <input data-nj-id type="hidden" name="items[<?php echo esc_attr($slot); ?>][image_id]" value="<?php echo $id; ?>">
              <p class="nj-prenda-actions">
                <button type="button" class="button button-primary" data-nj-pick>Subir foto</button>
                <button type="button" class="button" data-nj-clear>Quitar foto</button>
              </p>
            </div>
          </div>
          <div class="nj-testimonio__datos">
            <label>Nombre
              <input type="text" name="items[<?php echo esc_attr($slot); ?>][name]" value="<?php echo esc_attr($name); ?>" maxlength="80">
            </label>
            <label>Fecha
              <input type="text" name="items[<?php echo esc_attr($slot); ?>][when]" value="<?php echo esc_attr($when); ?>" maxlength="40" placeholder="8 ene 2025">
            </label>
            <label>Testimonio
              <textarea name="items[<?php echo esc_attr($slot); ?>][text]" rows="3" maxlength="500"><?php echo esc_textarea($text); ?></textarea>
            </label>
            <p><button type="button" class="button" data-testimonio-remove>Quitar opinión</button></p>
          </div>
        </article>
        <?php
    }
}

if (!function_exists('nj_testimonios_admin_panel')) {
    function nj_testimonios_admin_panel() {
        $settings = function_exists('nj_testimonios_settings') ? nj_testimonios_settings() : array('enabled' => true);
        $rows = function_exists('nj_testimonios_guardados') ? nj_testimonios_guardados() : array();
        ?>
        <h2>Testimonios</h2>
        <p class="nj-studio-lead">Agrégalos a mano. En la portada se ven como una opinión de Google: la foto, el nombre, las estrellas y el texto. Si no hay foto, sale la inicial del nombre.</p>
        <form class="nj-testimonio-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="nj_testimonios">
          <?php wp_nonce_field('nj_testimonios', 'nj_testimonios_nonce'); ?>
          <label class="nj-check"><input type="checkbox" name="enabled" value="yes"<?php echo !empty($settings['enabled']) ? ' checked' : ''; ?>> Mostrar testimonios en la portada</label>
          <div class="nj-testimonio-lista" data-testimonio-lista>
            <?php foreach ($rows as $nj_i => $row) : ?>
              <?php nj_testimonios_admin_fila($row, $nj_i); ?>
            <?php endforeach; ?>
          </div>
          <p><button type="button" class="button" data-testimonio-add>Agregar opinión</button></p>
          <p class="nj-studio-save"><button type="submit" class="button button-primary">Guardar testimonios</button></p>
        </form>
        <template id="nj-testimonio-nuevo">
          <?php nj_testimonios_admin_fila(array('name' => '', 'text' => '', 'when' => '', 'image_id' => 0, 'photo' => ''), '__i__'); ?>
        </template>
        <?php
    }
}
