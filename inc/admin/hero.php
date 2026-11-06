<?php
/**
 * Pantalla del banner. Los datos viven en inc/hero.php.
 */

if (!function_exists('nj_hero_admin_card')) {
    function nj_hero_admin_card($slide, $index) {
        $type = $slide['type'] === 'build' ? 'build' : 'image';
        $src = nj_hero_src($slide);
        $name = 'nj_hero[' . $index . ']';
        ?>
        <article class="nj-hero-slide is-<?php echo esc_attr($type); ?>">
          <header class="nj-hero-slide__bar">
            <strong class="nj-hero-index"><?php echo esc_html((string) (is_numeric($index) ? ((int) $index + 1) : '')); ?></strong>
            <div class="nj-hero-modes" role="group" aria-label="Modo de la diapositiva">
              <button type="button" class="nj-hero-mode" data-mode="image">Subir imagen</button>
              <button type="button" class="nj-hero-mode" data-mode="build">Construir</button>
            </div>
            <div class="nj-hero-order">
              <button type="button" class="button nj-hero-up">Arriba</button>
              <button type="button" class="button nj-hero-down">Abajo</button>
              <button type="button" class="button nj-hero-remove">Quitar</button>
            </div>
          </header>
          <div class="nj-hero-canvas" style="<?php echo esc_attr('--hero-bg:' . $slide['color']); ?>">
            <img class="nj-hero-photo" alt=""<?php echo $src === '' ? ' hidden' : ' src="' . esc_url($src) . '"'; ?>>
            <div class="nj-hero-compose">
              <small data-preview="eyebrow"><?php echo esc_html($slide['eyebrow']); ?></small>
              <strong data-preview="title"><?php echo esc_html($slide['title']); ?></strong>
              <span data-preview="text"><?php echo esc_html($slide['text']); ?></span>
              <em data-preview="button"><?php echo esc_html($slide['button']); ?></em>
            </div>
            <p class="nj-hero-empty">Elige una imagen de 2560 × 1200 px</p>
          </div>
          <div class="nj-hero-fields">
            <input data-field="type" type="hidden" name="<?php echo esc_attr($name); ?>[type]" value="<?php echo esc_attr($type); ?>">
            <input data-field="image" type="hidden" name="<?php echo esc_attr($name); ?>[image]" value="<?php echo esc_attr((string) $slide['image']); ?>">
            <input data-field="src" type="hidden" name="<?php echo esc_attr($name); ?>[src]" value="<?php echo esc_attr($slide['src']); ?>">
            <p class="nj-hero-image-tools">
              <button type="button" class="button button-primary nj-hero-pick">Elegir imagen</button>
              <button type="button" class="button nj-hero-clear">Quitar imagen</button>
              <span class="nj-hero-measure">2560 × 1200 px, proporción 32:15. JPG o PNG.</span>
            </p>
            <label>Texto alternativo
              <input data-field="alt" type="text" name="<?php echo esc_attr($name); ?>[alt]" value="<?php echo esc_attr($slide['alt']); ?>">
            </label>
            <label>Enlace
              <input data-field="url" type="url" name="<?php echo esc_attr($name); ?>[url]" value="<?php echo esc_attr($slide['url']); ?>" placeholder="Vacío abre Colecciones">
            </label>
            <div class="nj-hero-build-fields">
              <label>Color de fondo
                <input data-field="color" type="color" name="<?php echo esc_attr($name); ?>[color]" value="<?php echo esc_attr($slide['color']); ?>">
              </label>
              <label>Encabezado
                <input data-field="eyebrow" type="text" name="<?php echo esc_attr($name); ?>[eyebrow]" value="<?php echo esc_attr($slide['eyebrow']); ?>">
              </label>
              <label>Título
                <input data-field="title" type="text" name="<?php echo esc_attr($name); ?>[title]" value="<?php echo esc_attr($slide['title']); ?>">
              </label>
              <label>Texto
                <textarea data-field="text" name="<?php echo esc_attr($name); ?>[text]" rows="3"><?php echo esc_textarea($slide['text']); ?></textarea>
              </label>
              <label>Botón
                <input data-field="button" type="text" name="<?php echo esc_attr($name); ?>[button]" value="<?php echo esc_attr($slide['button']); ?>" placeholder="Descubre tu favorita">
              </label>
            </div>
          </div>
        </article>
        <?php
    }
}

if (!function_exists('nj_hero_admin_page')) {
    function nj_hero_admin_page() {
        if (!function_exists('nj_tienda_can') || !nj_tienda_can()) {
            if (!current_user_can('edit_theme_options')) {
                return;
            }
        }
        $slides = nj_hero_rows();
        ?>
        <div class="nj-hero-admin">
          <?php if (isset($_GET['updated']) && $_GET['updated'] === 'banner') : ?>
            <div class="notice notice-success is-dismissible"><p>El banner quedó guardado. Recarga la portada para verlo.</p></div>
          <?php endif; ?>
          <p class="nj-hero-lead">Cada diapositiva puede ser una imagen terminada o un recuadro que armas aquí: color, textos y una foto. La medida de la imagen es 2560 × 1200 px.</p>
          <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="nj_hero_save">
            <?php wp_nonce_field('nj_hero_save', 'nj_hero_nonce'); ?>
            <div class="nj-hero-list" data-hero-list>
              <?php foreach ($slides as $index => $slide) : ?>
                <?php nj_hero_admin_card($slide, $index); ?>
              <?php endforeach; ?>
            </div>
            <p class="nj-hero-actions">
              <button type="button" class="button button-secondary" data-hero-add>Agregar diapositiva</button>
              <button type="submit" class="button button-primary">Guardar banner</button>
              <button type="submit" class="button" name="nj_hero_reset" value="1">Volver a los banners del tema</button>
              <a class="nj-hero-view" href="<?php echo esc_url(home_url('/')); ?>">Ver la portada</a>
            </p>
          </form>
          <template id="nj-hero-template">
            <?php nj_hero_admin_card(nj_hero_blank(), '__i__'); ?>
          </template>
        </div>
        <?php
    }
}
