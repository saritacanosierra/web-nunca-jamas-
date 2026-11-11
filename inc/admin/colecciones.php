<?php
/**
 * Colecciones en Mi tienda: crear, montar prendas, banda y diseño.
 */

if (!function_exists('nj_colecciones_admin_fila')) {
    function nj_colecciones_admin_fila($id, $label) {
        $pieza = function_exists('nj_coleccion_pieza') ? nj_coleccion_pieza($id) : array(
            'modo' => 'imagen',
            'image_id' => 0,
            'image' => '',
            'html' => '',
            'banda' => '',
            'banda_ajuste' => array(),
        );
        $caja = function_exists('nj_coleccion_caja') ? nj_coleccion_caja($pieza) : array(
            'titulo' => '',
            'kicker' => '',
            'texto' => '',
            'x' => 50,
            'y' => 50,
            'size' => 72,
            'small' => 16,
        );
        $prendas = array();
        if ($id > 0 && function_exists('nj_coleccion_prendas_ids')) {
            foreach (nj_coleccion_prendas_ids($id) as $product_id) {
                $name = function_exists('get_the_title') ? get_the_title($product_id) : '';
                if ($name === '') {
                    continue;
                }
                $prendas[] = array(
                    'id' => (int) $product_id,
                    'name' => $name,
                );
            }
        }
        return array(
            'id' => (int) $id,
            'label' => $label,
            'modo' => $pieza['modo'],
            'image_id' => (int) $pieza['image_id'],
            'image' => $pieza['image'],
            'html' => $pieza['html'],
            'banda' => $pieza['banda'],
            'banda_ajuste' => isset($pieza['banda_ajuste']) ? $pieza['banda_ajuste'] : array(),
            'titulo' => $caja['titulo'],
            'kicker' => $caja['kicker'],
            'texto' => $caja['texto'],
            'x' => $caja['x'],
            'y' => $caja['y'],
            'size' => $caja['size'],
            'small' => $caja['small'],
            'prendas' => $prendas,
        );
    }
}

if (!function_exists('nj_colecciones_admin_form')) {
    function nj_colecciones_admin_form($row) {
        $imagen = $row['modo'] !== 'html';
        $banda = function_exists('nj_coleccion_banda_ajuste') ? nj_coleccion_banda_ajuste(isset($row['banda_ajuste']) ? $row['banda_ajuste'] : array()) : array(
            'vel' => 5,
            'fondo' => 'solido',
            'color' => '#17324d',
            'color_b' => '#64bfae',
            'rumbo' => 'horizontal',
            'tinta' => '#ffffff',
        );
        $degradado = $banda['fondo'] === 'degradado';
        ?>
        <form class="nj-coleccion" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" data-coleccion>
          <input type="hidden" name="action" value="nj_coleccion">
          <input type="hidden" name="coleccion_id" value="<?php echo (int) $row['id']; ?>">
          <?php wp_nonce_field('nj_coleccion', 'nj_coleccion_nonce'); ?>
          <h3><?php echo $row['id'] > 0 ? esc_html($row['label']) : 'Nueva colección'; ?></h3>
          <div class="nj-coleccion-par">
            <label>Nombre
              <input type="text" name="nombre" value="<?php echo esc_attr($row['label']); ?>" required>
            </label>
            <label>Banda de texto
              <input type="text" name="banda" value="<?php echo esc_attr($row['banda']); ?>" maxlength="160" placeholder="Donde los sueños se visten de colores">
            </label>
          </div>
          <fieldset class="nj-banda-ajuste" data-banda-ajuste>
            <legend>Movimiento y fondo de la banda</legend>
            <div class="nj-banda-ajuste__grid">
              <label>Velocidad
                <input type="number" name="banda_vel" min="1" max="10" value="<?php echo (int) $banda['vel']; ?>">
                <small>1 es lenta y 10 es rápida. 5 es el ritmo de ahora.</small>
              </label>
              <label>Color de la frase
                <input type="color" name="banda_tinta" value="<?php echo esc_attr($banda['tinta']); ?>">
              </label>
              <div class="nj-banda-fondo">
                <span>Fondo</span>
                <label class="nj-check"><input type="radio" name="banda_fondo" value="solido" data-banda-fondo<?php echo $degradado ? '' : ' checked'; ?>> Sólido</label>
                <label class="nj-check"><input type="radio" name="banda_fondo" value="degradado" data-banda-fondo<?php echo $degradado ? ' checked' : ''; ?>> Degradado</label>
              </div>
            </div>
            <div class="nj-banda-ajuste__grid">
              <label>Color
                <input type="color" name="banda_color" value="<?php echo esc_attr($banda['color']); ?>">
              </label>
              <label data-banda-extra<?php echo $degradado ? '' : ' hidden'; ?>>Segundo color
                <input type="color" name="banda_color_b" value="<?php echo esc_attr($banda['color_b']); ?>">
              </label>
              <label data-banda-extra<?php echo $degradado ? '' : ' hidden'; ?>>Dirección
                <select name="banda_rumbo">
                  <option value="horizontal"<?php echo $banda['rumbo'] === 'horizontal' ? ' selected' : ''; ?>>Horizontal</option>
                  <option value="vertical"<?php echo $banda['rumbo'] === 'vertical' ? ' selected' : ''; ?>>Vertical</option>
                  <option value="diagonal"<?php echo $banda['rumbo'] === 'diagonal' ? ' selected' : ''; ?>>Diagonal</option>
                </select>
              </label>
            </div>
          </fieldset>
          <fieldset class="nj-coleccion-modo">
            <legend>Contenedor del diseño</legend>
            <label class="nj-check"><input type="radio" name="modo" value="imagen" data-coleccion-modo<?php echo $imagen ? ' checked' : ''; ?>> Subir una imagen</label>
            <label class="nj-check"><input type="radio" name="modo" value="html" data-coleccion-modo<?php echo $imagen ? '' : ' checked'; ?>> Escribir HTML</label>
          </fieldset>
          <div data-coleccion-panel="imagen"<?php echo $imagen ? '' : ' hidden'; ?>>
            <div class="nj-photo nj-photo--wide" data-nj-photo>
              <img alt=""<?php echo $row['image'] === '' ? ' hidden' : ' src="' . esc_url($row['image']) . '"'; ?>>
              <div class="nj-photo-body">
                <input data-nj-id type="hidden" name="image_id" value="<?php echo (int) $row['image_id']; ?>">
                <p class="nj-prenda-actions">
                  <button type="button" class="button button-primary" data-nj-pick>Subir imagen</button>
                  <button type="button" class="button" data-nj-clear>Quitar imagen</button>
                </p>
              </div>
            </div>
          </div>
          <div data-coleccion-panel="html"<?php echo $imagen ? ' hidden' : ''; ?>>
            <label>HTML
              <textarea name="html" rows="8" spellcheck="false" placeholder="<h2>La colección</h2><p>El texto del diseño.</p>"><?php echo esc_textarea($row['html']); ?></textarea>
            </label>
            <p>Puedes usar div, p, h2, h3, a e img. Si también hay una imagen, entra donde escribas <strong>{{foto}}</strong>.</p>
          </div>
          <?php
          $nj_lineas = preg_split("/\r\n|\n|\r/", (string) $row['titulo']);
          $nj_chip = isset($nj_lineas[0]) ? trim($nj_lineas[0]) : '';
          if ($nj_chip === '') {
              $nj_chip = $row['label'] !== '' ? $row['label'] : 'Texto';
          }
          ?>
          <div class="nj-promo-text" data-nj-promo-copy>
            <label>Texto pequeño
              <input type="text" name="caja[kicker]" value="<?php echo esc_attr($row['kicker']); ?>">
            </label>
            <label>Título
              <textarea name="caja[title]" rows="2"><?php echo esc_textarea($row['titulo']); ?></textarea>
            </label>
            <label>Texto
              <textarea name="caja[texto]" rows="2"><?php echo esc_textarea($row['texto']); ?></textarea>
            </label>
            <div class="nj-promo-stage" data-nj-promo-stage>
              <button type="button" class="nj-promo-chip" data-nj-promo-chip style="left:<?php echo (int) $row['x']; ?>%;top:<?php echo (int) $row['y']; ?>%;"><?php echo esc_html($nj_chip); ?></button>
            </div>
            <div class="nj-promo-place">
              <label>Horizontal
                <input type="number" min="0" max="100" name="caja[x]" value="<?php echo (int) $row['x']; ?>">
              </label>
              <label>Vertical
                <input type="number" min="0" max="100" name="caja[y]" value="<?php echo (int) $row['y']; ?>">
              </label>
              <label>Tamaño del título
                <input type="number" min="18" max="96" name="caja[size]" value="<?php echo (int) $row['size']; ?>">
              </label>
              <label>Tamaño del texto
                <input type="number" min="10" max="32" name="caja[small]" value="<?php echo (int) $row['small']; ?>">
              </label>
            </div>
            <p class="nj-prenda-note">Arrastra el recuadro negro hasta el punto donde quieres el texto. El punto que queda es el centro.</p>
          </div>
          <div class="nj-look-search" data-look-search data-look-max="40" data-look-name="prendas[]">
            <label>Prendas de esta colección
              <input type="search" data-look-filter placeholder="Buscar la prenda" autocomplete="off">
            </label>
            <div class="nj-look-results" data-look-results hidden></div>
            <div class="nj-look-chosen" data-look-chosen>
              <?php foreach ($row['prendas'] as $prenda) : ?>
                <label class="nj-look-chip">
                  <input type="checkbox" name="prendas[]" value="<?php echo (int) $prenda['id']; ?>" checked>
                  <?php echo esc_html($prenda['name']); ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>
          <p class="nj-studio-save"><button type="submit" class="button button-primary"><?php echo $row['id'] > 0 ? 'Guardar colección' : 'Crear colección'; ?></button></p>
        </form>
        <?php
    }
}

if (!function_exists('nj_colecciones_admin_panel')) {
    function nj_colecciones_admin_panel() {
        $rows = array();
        $seen = array();
        $lista = function_exists('nj_colecciones_lista') ? nj_colecciones_lista() : array();
        foreach ((array) $lista as $item) {
            $id = isset($item['id']) ? (int) $item['id'] : 0;
            if ($id < 1 || isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $row = nj_colecciones_admin_fila($id, isset($item['label']) ? $item['label'] : '');
            $row['reciente'] = !empty($item['reciente']);
            $rows[] = $row;
        }
        ?>
        <h2>Colecciones</h2>
        <p class="nj-studio-lead">Estas son las que ya están creadas. Pulsa una para cambiarla, o Nueva para armar otra. En la banda cambias la frase, la velocidad y el fondo, sólido o degradado. El contenedor es una imagen o HTML, y el recuadro negro coloca el texto.</p>
        <div class="nj-coleccion-lista">
          <button type="button" data-coleccion-open="nueva">
            <small>Agregar</small>
            <strong>Nueva colección</strong>
            <span>Nombre, prendas y diseño.</span>
          </button>
          <?php foreach ($rows as $row) : ?>
            <button type="button" data-coleccion-open="<?php echo (int) $row['id']; ?>">
              <small><?php echo !empty($row['reciente']) ? 'Nueva' : 'Creada'; ?></small>
              <strong><?php echo esc_html($row['label']); ?></strong>
              <?php
              $nj_banda_corta = $row['banda'];
              if (function_exists('mb_strlen') && mb_strlen($nj_banda_corta) > 42) {
                  $nj_banda_corta = rtrim(mb_substr($nj_banda_corta, 0, 42)) . '…';
              }
              ?>
              <span><?php echo count($row['prendas']) === 1 ? '1 prenda' : count($row['prendas']) . ' prendas'; ?><?php echo $nj_banda_corta !== '' ? ' · ' . esc_html($nj_banda_corta) : ''; ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <div class="nj-coleccion-editor" data-coleccion-editor="nueva" hidden>
          <?php nj_colecciones_admin_form(nj_colecciones_admin_fila(0, '')); ?>
        </div>
        <?php foreach ($rows as $row) : ?>
          <div class="nj-coleccion-editor" data-coleccion-editor="<?php echo (int) $row['id']; ?>" hidden>
            <?php nj_colecciones_admin_form($row); ?>
          </div>
        <?php endforeach; ?>
        <?php
    }
}
