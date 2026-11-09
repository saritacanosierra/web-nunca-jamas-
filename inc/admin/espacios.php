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

if (!function_exists('nj_espacios_size')) {
    function nj_espacios_size($text) {
        echo '<p class="nj-size"><span>' . esc_html($text) . '</span></p>';
    }
}

if (!function_exists('nj_espacios_panel_cuentos')) {
    function nj_espacios_panel_cuentos() {
        ?>
        <h2>Carrusel de cuentos</h2>
        <p>Son las fotos anchas que giran en Cuentos. Sube cada una a <strong>1920 × 560 px</strong>. El recuadro del título tapa la izquierda, así que deja el dibujo hacia el centro. Agrega otra, quita la que sobre y guarda.</p>
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
                <?php nj_espacios_size('1920 × 560 px'); ?>
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
        <p>Arriba cambias los textos de la portada. Abajo, las fotos. Cada foto va a <strong>1200 × 900 px</strong>. En cada contenedor puedes escribir HTML: la portada usa ese código y la foto entra donde pongas <strong>{{foto}}</strong>. Si el HTML queda vacío, se ven los textos y la foto de siempre.</p>
        <?php nj_espacios_form_open('promo'); ?>
        <div class="nj-promo-texts">
          <?php foreach (nj_espacios_promos() as $index => $row) :
              $kind = isset($row['kind']) ? $row['kind'] : 'photo';
              if (!isset($names[$kind])) {
                  continue;
              }
              $nj_title = isset($row['title']) ? (string) $row['title'] : '';
              $nj_button = isset($row['button']) ? (string) $row['button'] : '';
              $nj_note = isset($row['note']) ? (string) $row['note'] : '';
              $nj_kicker = isset($row['kicker']) ? (string) $row['kicker'] : '';
              if ($kind === 'gift' && trim($nj_title) === '') {
                  $nj_title = "REGALO\nPERFECTO";
              }
              if ($kind === 'gift' && trim($nj_button) === '') {
                  $nj_button = 'Comprar ahora';
              }
              if ($kind === 'gift' && trim($nj_note) === '') {
                  $nj_note = 'Elige talla al comprar. Envío a Colombia sobre las prendas disponibles.';
              }
              if ($kind === 'collect' && trim($nj_kicker) === '') {
                  $nj_kicker = 'Nunca';
              }
              if ($kind === 'collect' && trim($nj_title) === '') {
                  $nj_title = 'COLLECT';
              }
              if ($kind === 'collect' && trim($nj_button) === '') {
                  $nj_button = 'Ver colección';
              }
              if ($kind === 'three_d' && trim($nj_kicker) === '') {
                  $nj_kicker = 'Gira la prenda';
              }
              if ($kind === 'three_d' && trim($nj_title) === '') {
                  $nj_title = "EXPERIENCIA\n3D";
              }
              ?>
            <div class="nj-promo-text" data-nj-promo-copy data-nj-kind="<?php echo esc_attr($kind); ?>">
              <strong><?php echo esc_html($names[$kind]); ?></strong>
              <?php if ($kind !== 'gift') : ?>
                <label>Texto pequeño
                  <input type="text" name="espacios[<?php echo (int) $index; ?>][kicker]" value="<?php echo esc_attr($nj_kicker); ?>">
                </label>
              <?php endif; ?>
              <label>Título
                <textarea name="espacios[<?php echo (int) $index; ?>][title]" rows="2"><?php echo esc_textarea($nj_title); ?></textarea>
              </label>
              <?php if ($kind !== 'three_d') : ?>
                <label>Botón
                  <input type="text" name="espacios[<?php echo (int) $index; ?>][button]" value="<?php echo esc_attr($nj_button); ?>">
                </label>
              <?php endif; ?>
              <?php if ($kind === 'gift') : ?>
                <label>Nota
                  <textarea name="espacios[<?php echo (int) $index; ?>][note]" rows="3"><?php echo esc_textarea($nj_note); ?></textarea>
                </label>
              <?php endif; ?>
              <label class="nj-promo-html">HTML
                <textarea name="espacios[<?php echo (int) $index; ?>][html]" rows="6" spellcheck="false"><?php echo esc_textarea(isset($row['html']) ? $row['html'] : ''); ?></textarea>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="nj-photo-grid" data-nj-promo-list>
          <?php foreach (nj_espacios_promos() as $index => $row) :
              $kind = isset($row['kind']) ? $row['kind'] : 'photo';
              $label = isset($names[$kind]) ? $names[$kind] : (isset($row['label']) && $row['label'] !== '' ? $row['label'] : 'Campaña');
              $file = isset($row['file']) ? $row['file'] : '';
              ?>
            <div class="nj-photo" data-nj-photo data-nj-promo data-nj-kind="<?php echo esc_attr($kind); ?>">
              <img alt=""<?php echo $row['src'] === '' ? ' hidden' : ' src="' . esc_url($row['src']) . '"'; ?>>
              <div class="nj-photo-body">
                <strong><?php echo esc_html($label); ?></strong>
                <?php nj_espacios_size('1200 × 900 px'); ?>
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][kind]" value="<?php echo esc_attr($kind); ?>">
                <input data-nj-id type="hidden" name="espacios[<?php echo (int) $index; ?>][image]" value="<?php echo esc_attr((string) (isset($row['image']) ? (int) $row['image'] : 0)); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][src]" value="<?php echo esc_attr($file); ?>">
                <input type="hidden" name="espacios[<?php echo (int) $index; ?>][alt]" value="<?php echo esc_attr(isset($row['alt']) ? $row['alt'] : ''); ?>">
                <?php if ($kind === 'photo') : ?>
                  <label>Nombre
                    <input type="text" name="espacios[<?php echo (int) $index; ?>][label]" value="<?php echo esc_attr(isset($row['label']) ? $row['label'] : ''); ?>" placeholder="Nombre de la campaña">
                  </label>
                  <label class="nj-promo-html">HTML
                    <textarea name="espacios[<?php echo (int) $index; ?>][html]" rows="6" spellcheck="false"><?php echo esc_textarea(isset($row['html']) ? $row['html'] : ''); ?></textarea>
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

if (!function_exists('nj_espacios_panel_regalo')) {
    function nj_espacios_franja_src($row) {
        $src = '';
        if (!empty($row['image_id']) && function_exists('wp_get_attachment_image_url')) {
            $url = wp_get_attachment_image_url((int) $row['image_id'], 'medium');
            $src = is_string($url) ? $url : '';
        }
        if ($src === '' && !empty($row['image']) && function_exists('nj_img')) {
            $src = nj_img($row['image']);
        }
        return $src;
    }

    function nj_espacios_panel_regalo() {
        $rows = function_exists('nj_regalo_rows') ? nj_regalo_rows() : array(array(
            'category' => 0,
            'kicker' => '',
            'title' => '',
            'text' => '',
            'alt' => '',
            'html' => '',
            'image_id' => 0,
            'image' => '',
        ));
        $categories = function_exists('nj_catalog_banner_rows') ? nj_catalog_banner_rows() : array();
        ?>
        <h2>Franja de la tienda</h2>
        <p>Cada franja se muestra en la categoría que elijas. <strong>Toda la tienda</strong> se ve al entrar a la tienda y en las categorías que todavía no tienen la suya. Si una categoría está dentro de otra, usa la franja de la de afuera hasta que tenga la propia. La foto va a <strong>1200 × 400 px</strong>.</p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <input type="hidden" name="action" value="nj_regalo">
          <?php wp_nonce_field('nj_regalo', 'nj_regalo_nonce'); ?>
          <div class="nj-franja-list" data-nj-franja-list>
            <?php foreach ($rows as $index => $banner) :
                $src = nj_espacios_franja_src($banner);
                $category = isset($banner['category']) ? (int) $banner['category'] : 0;
                ?>
            <div class="nj-franja" data-nj-franja>
              <div class="nj-photo nj-regalo-card" data-nj-photo>
                <img alt=""<?php echo $src === '' ? ' hidden' : ' src="' . esc_url($src) . '"'; ?>>
                <div class="nj-photo-body">
                  <input data-nj-id type="hidden" name="franjas[<?php echo (int) $index; ?>][image_id]" value="<?php echo esc_attr((string) (int) $banner['image_id']); ?>">
                  <?php nj_espacios_size('1200 × 400 px'); ?>
                  <label>Se muestra en
                    <select name="franjas[<?php echo (int) $index; ?>][category]">
                      <option value="0"<?php echo $category === 0 ? ' selected' : ''; ?>>Toda la tienda</option>
                      <?php foreach ($categories as $category_row) : ?>
                        <option value="<?php echo (int) $category_row['id']; ?>"<?php echo $category === (int) $category_row['id'] ? ' selected' : ''; ?>><?php echo esc_html($category_row['label']); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </label>
                  <p class="nj-prenda-actions">
                    <button type="button" class="button button-primary" data-nj-pick>Cambiar foto</button>
                    <button type="button" class="button" data-nj-clear>Quitar foto</button>
                    <button type="button" class="button" data-nj-franja-remove>Quitar franja</button>
                  </p>
                  <label>Texto pequeño
                    <input type="text" name="franjas[<?php echo (int) $index; ?>][kicker]" value="<?php echo esc_attr($banner['kicker']); ?>">
                  </label>
                  <label>Título
                    <textarea name="franjas[<?php echo (int) $index; ?>][title]" rows="2"><?php echo esc_textarea($banner['title']); ?></textarea>
                  </label>
                  <label>Nota
                    <input type="text" name="franjas[<?php echo (int) $index; ?>][text]" value="<?php echo esc_attr($banner['text']); ?>">
                  </label>
                  <label>Texto de la foto
                    <input type="text" name="franjas[<?php echo (int) $index; ?>][alt]" value="<?php echo esc_attr($banner['alt']); ?>">
                  </label>
                </div>
              </div>
              <label class="nj-regalo-html">HTML de la franja
                <textarea name="franjas[<?php echo (int) $index; ?>][html]" rows="6" spellcheck="false" placeholder="<div class=&quot;gift-banner__copy&quot;><p>Texto</p><h2>Título</h2></div>"><?php echo esc_textarea($banner['html']); ?></textarea>
              </label>
            </div>
            <?php endforeach; ?>
          </div>
          <p>Si escribes HTML, la tienda usa ese código. La foto entra donde escribas <strong>{{foto}}</strong>, y también en una imagen del HTML que esté sin dirección. En la tienda esa foto se ajusta al alto de la franja. Si el HTML queda vacío, se ve el diseño con el texto y la foto. Puedes usar div, p, h2, a e img.</p>
          <p><button type="button" class="button" data-nj-franja-add>Agregar franja</button></p>
          <p class="nj-studio-save"><button type="submit" class="button button-primary">Guardar franjas</button></p>
        </form>
        <?php
    }
}

if (!function_exists('nj_espacios_panel_styles')) {
    function nj_espacios_panel_styles() {
        ?>
        <h2>Categorías</h2>
        <p class="nj-studio-lead">Hay dos lugares. Pulsa uno y abajo se abre solo ese. Cambia lo que ves y guarda. La foto al entrar a una categoría se elige en Franja de la tienda.</p>
        <?php
        $nj_pill_rows = function_exists('nj_catalog_pill_editor_rows') ? nj_catalog_pill_editor_rows() : array();
        $nj_style_rows = nj_espacios_rows('styles');
        ?>
        <div class="nj-cat-map" role="tablist">
          <button type="button" class="is-on" data-nj-cat-jump="circulos" aria-selected="true">
            <small>1 · Inicio</small>
            <strong>Círculos con foto</strong>
            <span>La fila de la portada. El nombre crea la categoría.</span>
          </button>
          <button type="button" data-nj-cat-jump="fila" aria-selected="false">
            <small>2 · Tienda</small>
            <strong>Nombres de arriba</strong>
            <span>Las pastillas sobre los productos. El orden en que se ven.</span>
          </button>
        </div>

        <section class="nj-cat-block is-open" data-nj-cat-block="circulos">
          <div class="nj-cat-body">
            <p class="nj-cat-now">Estás editando la portada. <?php echo count($nj_style_rows); ?> círculos. La foto es cuadrada, <strong>400 × 400 px</strong>. Al guardar, el nombre crea o quita esa categoría en la tienda.</p>
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
                <?php nj_espacios_size('400 × 400 px'); ?>
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
          <div class="nj-cat-body">
            <p class="nj-cat-now">Estás editando la tienda. <?php echo count($nj_pill_rows); ?> nombres. Quitar uno lo saca de esa fila; los productos siguen en su categoría.</p>
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
        <?php
    }
}

if (!function_exists('nj_espacios_panel_looks')) {
    function nj_espacios_panel_looks() {
        ?>
        <h2>Looks</h2>
        <p>La foto es vertical, <strong>900 × 1200 px</strong>. Súbela y arrastra cada punto hasta la prenda. Puedes agregar o quitar un look completo.</p>
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
              <?php nj_espacios_size('900 × 1200 px'); ?>
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
