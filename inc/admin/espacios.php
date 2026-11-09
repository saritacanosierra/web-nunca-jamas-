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
        <p>Son las fotos anchas que giran en Cuentos. Agrega otra, quita la que sobre, cambia la foto y el texto, y guarda.</p>
        <?php nj_espacios_form_open('cuentos'); ?>
        <div class="nj-photo-grid" data-nj-cuento-list>
          <?php foreach (nj_espacios_rows('cuentos') as $index => $row) : ?>
            <div class="nj-photo nj-photo--wide" data-nj-photo data-nj-cuento>
              <?php $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : ''); ?>
              <img alt=""<?php echo $src === '' ? ' hidden' : ' src="' . esc_url($src) . '"'; ?>>
              <div class="nj-photo-body">
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][src]" value="<?php echo esc_attr(isset($row['src']) ? $row['src'] : ''); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][alt]" value="<?php echo esc_attr(isset($row['alt']) ? $row['alt'] : ''); ?>">
                <p class="nj-prenda-actions">
                  <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                  <button type="button" class="button" data-nj-cuento-remove>Quitar</button>
                </p>
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
        <p><button type="button" class="button" data-nj-cuento-add>Agregar cuento</button></p>
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
        <p>Son las fotos grandes del medio de la portada. Agrega otra o quita la que sobre, y guarda.</p>
        <?php nj_espacios_form_open('promo'); ?>
        <div class="nj-photo-grid" data-nj-promo-list>
          <?php foreach (nj_espacios_promos() as $index => $row) :
              $kind = isset($row['kind']) ? $row['kind'] : 'photo';
              $label = isset($names[$kind]) ? $names[$kind] : (isset($row['label']) && $row['label'] !== '' ? $row['label'] : 'Campaña');
              $file = isset($row['file']) ? $row['file'] : '';
              ?>
            <div class="nj-photo" data-nj-photo data-nj-promo>
              <img alt=""<?php echo $row['src'] === '' ? ' hidden' : ' src="' . esc_url($row['src']) . '"'; ?>>
              <div class="nj-photo-body">
                <strong><?php echo esc_html($label); ?></strong>
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][kind]" value="<?php echo esc_attr($kind); ?>">
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][src]" value="<?php echo esc_attr($file); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][alt]" value="<?php echo esc_attr(isset($row['alt']) ? $row['alt'] : ''); ?>">
                <?php if ($kind === 'photo') : ?>
                  <label>Nombre
                    <input type="text" name="espacios[<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr(isset($row['label']) ? $row['label'] : ''); ?>" placeholder="Nombre de la campaña">
                  </label>
                <?php endif; ?>
                <p class="nj-prenda-actions">
                  <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                  <button type="button" class="button" data-nj-promo-remove>Quitar</button>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-promo-add>Agregar campaña</button></p>
        <?php nj_espacios_form_close('Guardar campañas');
    }
}

if (!function_exists('nj_espacios_panel_styles')) {
    function nj_espacios_panel_styles() {
        ?>
        <h2>Categorías</h2>
        <p class="nj-studio-lead">Hay tres lugares distintos. Elige el que quieres cambiar.</p>
        <?php
        $nj_pill_rows = function_exists('nj_catalog_pill_editor_rows') ? nj_catalog_pill_editor_rows() : array();
        $nj_style_rows = nj_espacios_rows('styles');
        $nj_banner_rows = function_exists('nj_catalog_banner_rows') ? nj_catalog_banner_rows() : array();
        ?>
        <div class="nj-cat-map">
          <button type="button" data-nj-cat-jump="circulos">
            <small>Portada</small>
            <strong>Círculos</strong>
            <span>La fila “Compra por categorías”.</span>
          </button>
          <button type="button" data-nj-cat-jump="fila">
            <small>Tienda</small>
            <strong>Pastillas</strong>
            <span>Los nombres sobre los productos.</span>
          </button>
          <button type="button" data-nj-cat-jump="foto">
            <small>Listado</small>
            <strong>Foto ancha</strong>
            <span>La imagen al entrar a una categoría.</span>
          </button>
        </div>

        <section class="nj-cat-block" data-nj-cat-block="circulos">
          <button type="button" class="nj-cat-summary" data-nj-cat-toggle aria-expanded="false">
            <small>Portada</small>
            <strong>Círculos de “Compra por categorías”</strong>
            <span><?php echo count($nj_style_rows); ?> círculos. El nombre y la foto. Al guardar, esa categoría se crea o se elimina en la tienda.</span>
          </button>
          <div class="nj-cat-body">
        <?php nj_espacios_form_open('styles'); ?>
        <div class="nj-style-grid" data-nj-style-list>
          <?php foreach ($nj_style_rows as $index => $row) :
              $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : '');
              ?>
            <div class="nj-style-card" data-nj-photo data-nj-style>
              <div class="nj-style-frame">
                <img alt=""<?php echo $src === '' ? ' hidden' : ' src="' . esc_url($src) . '"'; ?>>
              </div>
              <div class="nj-photo-body">
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][src]" value="<?php echo esc_attr(isset($row['src']) ? $row['src'] : ''); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][term]" value="<?php echo esc_attr((string) (isset($row['term']) ? (int) $row['term'] : 0)); ?>">
                <label>Nombre
                  <input type="text" name="espacios[<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr(isset($row['label']) ? $row['label'] : ''); ?>">
                </label>
                <div class="nj-style-actions">
                  <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                  <button type="button" class="button" data-nj-style-remove>Eliminar</button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-style-add>Agregar círculo</button></p>
        <?php nj_espacios_form_close('Guardar círculos'); ?>
          </div>
        </section>

        <section class="nj-cat-block" data-nj-cat-block="fila">
          <button type="button" class="nj-cat-summary" data-nj-cat-toggle aria-expanded="false">
            <small>Tienda</small>
            <strong>Pastillas sobre los productos</strong>
            <span><?php echo count($nj_pill_rows); ?> nombres. El orden y cuáles se ven. Quitar una la saca de esa fila; los productos siguen en su categoría.</span>
          </button>
          <div class="nj-cat-body">
        <?php nj_espacios_form_open('pills'); ?>
        <div class="nj-pill-list" data-nj-pill-list>
          <?php foreach ($nj_pill_rows as $index => $row) : ?>
            <div class="nj-pill-row" data-nj-pill>
              <label>Nombre
                <input type="text" name="espacios[<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr($row['label']); ?>">
              </label>
              <input type="hidden" name="espacios[<?php echo (int) $index; ?>][term]" value="<?php echo esc_attr((string) (int) $row['term']); ?>">
              <p class="nj-prenda-actions">
                <button type="button" class="button" data-nj-pill-up>Subir</button>
                <button type="button" class="button" data-nj-pill-down>Bajar</button>
                <button type="button" class="button" data-nj-pill-remove>Quitar</button>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-pill-add>Agregar pastilla</button></p>
        <?php nj_espacios_form_close('Guardar pastillas'); ?>
          </div>
        </section>

        <section class="nj-cat-block" data-nj-cat-block="foto">
          <button type="button" class="nj-cat-summary" data-nj-cat-toggle aria-expanded="false">
            <small>Listado</small>
            <strong>Foto ancha de cada categoría</strong>
            <span><?php echo count($nj_banner_rows); ?> fotos. La imagen grande al abrir pijamas, blusas u otra categoría. Si no tiene, se usa la de su categoría padre.</span>
          </button>
          <div class="nj-cat-body">
        <?php if (!$nj_banner_rows) : ?>
          <p>Todavía no hay categorías en la tienda.</p>
        <?php else : ?>
          <form method="post" action="<?php echo esc_url(function_exists('admin_url') ? admin_url('admin-post.php') : 'admin-post.php'); ?>">
            <input type="hidden" name="action" value="nj_catalog_banner">
            <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('nj_catalog_banner', 'nj_catalog_banner_nonce'); } ?>
            <div class="nj-banner-list">
              <?php foreach ($nj_banner_rows as $row) : ?>
                <div class="nj-banner-card" data-nj-photo>
                  <img alt=""<?php echo $row['src'] === '' ? ' hidden' : ' src="' . esc_url($row['src']) . '"'; ?>>
                  <div>
                    <strong><?php echo esc_html($row['label']); ?></strong>
                    <input data-nj-id type="hidden" name="banners[<?php echo (int) $row['id']; ?>]" value="<?php echo esc_attr((string) $row['image']); ?>">
                    <p class="nj-prenda-actions">
                      <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                      <button type="button" class="button" data-nj-clear>Quitar</button>
                    </p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <p class="nj-studio-save"><button type="submit" class="button button-primary">Guardar fotos</button></p>
          </form>
        <?php endif; ?>
          </div>
        </section>
        <?php
    }
}

if (!function_exists('nj_espacios_panel_looks')) {
    function nj_espacios_panel_looks() {
        ?>
        <h2>Looks</h2>
        <p>Sube la foto y arrastra cada punto hasta la prenda. Puedes agregar o quitar un look completo.</p>
        <?php nj_espacios_form_open('looks'); ?>
        <div class="nj-look-grid" data-nj-look-list>
          <?php foreach (nj_espacios_rows('looks') as $index => $row) :
              $src = nj_espacio_url(isset($row['image']) ? $row['image'] : 0, isset($row['src']) ? $row['src'] : '');
              $points = isset($row['points']) && is_array($row['points']) ? $row['points'] : array();
              ?>
            <div class="nj-look" data-nj-look>
              <strong data-nj-look-title>Look <?php echo (int) $index + 1; ?></strong>
              <div class="nj-look-stage" data-nj-stage data-nj-photo>
                <img alt=""<?php echo $src === '' ? ' hidden' : ' src="' . esc_url($src) . '"'; ?>>
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][src]" value="<?php echo esc_attr(isset($row['src']) && strpos((string) $row['src'], '://') === false ? $row['src'] : ''); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][alt]" value="<?php echo esc_attr(isset($row['alt']) ? $row['alt'] : ''); ?>">
                <?php foreach ($points as $point_index => $point) : ?>
                  <button class="nj-look-dot" type="button" data-nj-point="<?php echo (int) $point_index; ?>" style="top: <?php echo esc_attr($point['top']); ?>; left: <?php echo esc_attr($point['left']); ?>"><?php echo (int) $point_index + 1; ?></button>
                <?php endforeach; ?>
              </div>
              <p class="nj-prenda-actions">
                <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                <button type="button" class="button" data-nj-look-remove>Quitar</button>
              </p>
              <div data-nj-point-list>
                <?php foreach ($points as $point_index => $point) : ?>
                  <div class="nj-look-fields" data-nj-point-fields="<?php echo (int) $point_index; ?>">
                    <button type="button" class="nj-look-summary" data-nj-point-toggle>
                      <span data-nj-point-title><?php echo (int) $point_index + 1; ?>. <?php echo esc_html(isset($point['label']) && $point['label'] !== '' ? $point['label'] : 'Sin nombre'); ?></span>
                    </button>
                    <div class="nj-look-body">
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
                  </div>
                <?php endforeach; ?>
              </div>
              <p><button type="button" class="button" data-nj-point-add>Agregar punto</button></p>
            </div>
          <?php endforeach; ?>
        </div>
        <p><button type="button" class="button" data-nj-look-add>Agregar look</button></p>
        <?php nj_espacios_form_close('Guardar looks');
    }
}
