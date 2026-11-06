<?php
/**
 * Formularios de las fotos de la portada.
 */

if (!function_exists('nj_espacios_form_open')) {
    function nj_espacios_form_open($group) {
        ?>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="nj_espacios_save">
          <input type="hidden" name="grupo" value="<?php echo esc_attr($group); ?>">
          <?php wp_nonce_field('nj_espacios_save', 'nj_espacios_nonce'); ?>
        <?php
    }
}

if (!function_exists('nj_espacios_form_close')) {
    function nj_espacios_form_close($label) {
        ?>
          <p class="nj-studio-save"><button type="submit" class="button button-primary"><?php echo esc_html($label); ?></button></p>
        </form>
        <?php
    }
}

if (!function_exists('nj_espacios_panel_cuentos')) {
    function nj_espacios_panel_cuentos() {
        ?>
        <h2>Carrusel de cuentos</h2>
        <p>Son las fotos anchas que giran en Cuentos. Cambia la foto y el texto, y guarda.</p>
        <?php nj_espacios_form_open('cuentos'); ?>
        <div class="nj-photo-grid">
          <?php foreach (nj_espacios_rows('cuentos') as $index => $row) : ?>
            <div class="nj-photo nj-photo--wide" data-nj-photo>
              <?php $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : ''); ?>
              <img alt="" src="<?php echo esc_url($src); ?>">
              <div class="nj-photo-body">
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                <label>Texto pequeño
                  <input type="text" name="espacios[<?php echo (int) $index; ?>][kicker]" value="<?php echo esc_attr(isset($row['kicker']) ? $row['kicker'] : ''); ?>">
                </label>
                <label>Título
                  <textarea name="espacios[<?php echo (int) $index; ?>][title]" rows="2"><?php echo esc_textarea(isset($row['title']) ? $row['title'] : ''); ?></textarea>
                </label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php nj_espacios_form_close('Guardar cuentos');
    }
}

if (!function_exists('nj_espacios_panel_promo')) {
    function nj_espacios_panel_promo() {
        $names = array(
            'gift' => 'Regalo perfecto',
            'collect' => 'Collect',
            'three_d' => 'Experiencia 3D',
        );
        ?>
        <h2>Campañas</h2>
        <p>Son las tres fotos grandes del medio de la portada.</p>
        <?php nj_espacios_form_open('promo'); ?>
        <div class="nj-photo-grid">
          <?php foreach ($names as $key => $label) :
              $rows = nj_espacios_rows('promo');
              $row = isset($rows[$key]) ? $rows[$key] : array();
              $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : '');
              ?>
            <div class="nj-photo" data-nj-photo>
              <img alt="" src="<?php echo esc_url($src); ?>">
              <div class="nj-photo-body">
                <strong><?php echo esc_html($label); ?></strong>
                <input data-nj-id type="hidden" name="espacios[<?php echo esc_attr($key); ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php nj_espacios_form_close('Guardar campañas');
    }
}

if (!function_exists('nj_espacios_panel_styles')) {
    function nj_espacios_panel_styles() {
        ?>
        <h2>Categorías</h2>
        <p>Son los círculos de “Compra por categorías”. Sube una foto cuadrada para cada una.</p>
        <?php nj_espacios_form_open('styles'); ?>
        <div class="nj-photo-grid nj-photo-grid--small">
          <?php foreach (nj_espacios_rows('styles') as $index => $row) :
              $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : '');
              ?>
            <div class="nj-photo nj-photo--square" data-nj-photo>
              <img alt="" src="<?php echo esc_url($src); ?>">
              <div class="nj-photo-body">
                <strong><?php echo esc_html(isset($row['label']) ? $row['label'] : ''); ?></strong>
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php nj_espacios_form_close('Guardar categorías');
    }
}

if (!function_exists('nj_espacios_panel_looks')) {
    function nj_espacios_panel_looks() {
        ?>
        <h2>Looks</h2>
        <p>Sube la foto y arrastra cada punto hasta la prenda. Al pulsarlo se ven el nombre, el precio y el enlace.</p>
        <?php nj_espacios_form_open('looks'); ?>
        <div class="nj-look-grid">
          <?php foreach (nj_espacios_rows('looks') as $index => $row) :
              $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : '');
              $points = isset($row['points']) && is_array($row['points']) ? $row['points'] : array();
              ?>
            <div class="nj-look" data-nj-look>
              <strong>Look <?php echo (int) $index + 1; ?></strong>
              <div class="nj-look-stage<?php echo (int) $index === 1 ? ' is-wide' : ''; ?>" data-nj-stage data-nj-photo>
                <img alt="" src="<?php echo esc_url($src); ?>">
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <?php foreach ($points as $point_index => $point) : ?>
                  <button class="nj-look-dot" type="button" data-nj-point="<?php echo (int) $point_index; ?>" style="top: <?php echo esc_attr($point['top']); ?>; left: <?php echo esc_attr($point['left']); ?>"><?php echo (int) $point_index + 1; ?></button>
                <?php endforeach; ?>
              </div>
              <p class="nj-prenda-actions"><button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button></p>
              <div data-nj-point-list>
                <?php foreach ($points as $point_index => $point) : ?>
                  <div class="nj-look-fields" data-nj-point-fields="<?php echo (int) $point_index; ?>">
                    <input data-top type="hidden" name="espacios[<?php echo (int) $index; ?>][points][<?php echo (int) $point_index; ?>][top]" value="<?php echo esc_attr($point['top']); ?>">
                    <input data-left type="hidden" name="espacios[<?php echo (int) $index; ?>][points][<?php echo (int) $point_index; ?>][left]" value="<?php echo esc_attr($point['left']); ?>">
                    <label>Nombre
                      <input type="text" name="espacios[<?php echo (int) $index; ?>][points][<?php echo (int) $point_index; ?>][label]" value="<?php echo esc_attr(isset($point['label']) ? $point['label'] : ''); ?>">
                    </label>
                    <label>Precio
                      <input type="text" name="espacios[<?php echo (int) $index; ?>][points][<?php echo (int) $point_index; ?>][price]" value="<?php echo esc_attr(isset($point['price']) ? $point['price'] : ''); ?>" placeholder="$ 32.000">
                    </label>
                    <label>Enlace
                      <input type="url" name="espacios[<?php echo (int) $index; ?>][points][<?php echo (int) $point_index; ?>][href]" value="<?php echo esc_attr(isset($point['href']) ? $point['href'] : ''); ?>" placeholder="https://">
                    </label>
                    <button type="button" class="button" data-nj-point-remove>Quitar punto</button>
                  </div>
                <?php endforeach; ?>
              </div>
              <p><button type="button" class="button" data-nj-point-add>Agregar punto</button></p>
            </div>
          <?php endforeach; ?>
        </div>
        <?php nj_espacios_form_close('Guardar looks');
    }
}
