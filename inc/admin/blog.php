<?php
/**
 * Formulario del blog dentro de Mi tienda.
 */

if (!function_exists('nj_blog_admin_fields')) {
    function nj_blog_admin_fields($post) {
        $image_id = ($post && function_exists('get_post_thumbnail_id')) ? (int) get_post_thumbnail_id($post) : 0;
        $src = $image_id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($image_id, 'medium') : '';
        $title = $post && isset($post->post_title) ? $post->post_title : '';
        $excerpt = $post && isset($post->post_excerpt) ? $post->post_excerpt : '';
        $text = $post && isset($post->post_content) ? nj_blog_to_text($post->post_content) : '';
        ?>
        <div class="nj-modal-scroll">
          <div class="nj-modal-top">
            <div class="nj-modal-photo" data-nj-photo>
              <div class="nj-modal-frame">
                <img alt=""<?php echo $src ? ' src="' . esc_url($src) . '"' : ' hidden'; ?>>
                <span class="nj-modal-photo__ph">Foto de la nota</span>
              </div>
              <input data-nj-id type="hidden" name="foto" value="<?php echo (int) $image_id; ?>">
              <button type="button" class="button" data-nj-pick><?php echo $post ? 'Cambiar foto' : 'Subir foto'; ?></button>
            </div>
            <section class="nj-modal-section">
              <h3>La nota</h3>
              <label>Título
                <input type="text" name="titulo" value="<?php echo esc_attr($title); ?>" required>
              </label>
              <label>Resumen
                <textarea name="resumen" rows="3" placeholder="Una o dos frases para la tarjeta."><?php echo esc_textarea($excerpt); ?></textarea>
              </label>
            </section>
          </div>
          <section class="nj-modal-section">
            <h3>Texto</h3>
            <p class="nj-prenda-note">Deja una línea en blanco entre párrafos. Si una línea empieza con ##, sale como subtítulo.</p>
            <label>Contenido
              <textarea name="texto" rows="12" placeholder="Escribe aquí el cuento o la nota."><?php echo esc_textarea($text); ?></textarea>
            </label>
          </section>
          <section class="nj-modal-section">
            <h3>Publicidad</h3>
            <p class="nj-prenda-note">Dos imágenes de la nota. La primera se ve a la mitad del texto y la segunda al final. El enlace es opcional: al tocarla se abre esa página.</p>
            <div class="nj-ad-slots">
              <?php
              $ads = nj_blog_ads($post ? (int) $post->ID : 0);
              $places = array('Dentro de la nota', 'Al final de la nota');
              foreach ($places as $index => $place) :
                  $ad = $ads[$index];
                  $src = $ad['src'];
                  ?>
                <div class="nj-ad-slot" data-nj-photo>
                  <strong><?php echo esc_html($place); ?></strong>
                  <div class="nj-modal-frame">
                    <img alt=""<?php echo $src !== '' ? ' src="' . esc_url($src) . '"' : ' hidden'; ?>>
                    <span class="nj-modal-photo__ph">Subir imagen</span>
                  </div>
                  <input data-nj-id type="hidden" name="publicidad[<?php echo (int) $index; ?>][image]" value="<?php echo (int) $ad['image']; ?>">
                  <label>Enlace
                    <input type="url" name="publicidad[<?php echo (int) $index; ?>][href]" value="<?php echo esc_attr($ad['href']); ?>" placeholder="https://">
                  </label>
                  <p class="nj-prenda-actions">
                    <button type="button" class="button button-primary" data-nj-pick>Subir imagen</button>
                    <button type="button" class="button" data-nj-clear>Quitar</button>
                  </p>
                </div>
              <?php endforeach; ?>
            </div>
          </section>
        </div>
        <?php
    }
}

if (!function_exists('nj_blog_admin_panel')) {
    function nj_blog_admin_panel() {
        $posts = nj_blog_admin_rows('publish');
        $hidden = nj_blog_admin_rows('draft');
        $action = function_exists('admin_url') ? admin_url('admin-post.php') : 'admin-post.php';
        ?>
        <h2>Blog</h2>
        <p>Cada nota se publica en el blog. Escribe el título, un resumen y el texto, y sube la foto que se ve en la tarjeta.</p>
        <div class="nj-prenda-grid">
          <button type="button" class="nj-prenda-tile nj-prenda-tile--new" data-nj-open="nj-nota-nueva">Nueva nota</button>
          <?php foreach ($posts as $post) :
              $id = (int) $post->ID;
              $image_id = function_exists('get_post_thumbnail_id') ? (int) get_post_thumbnail_id($post) : 0;
              $src = $image_id && function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url($image_id, 'medium') : '';
              $date = function_exists('get_the_date') ? get_the_date('', $post) : '';
              ?>
            <button type="button" class="nj-prenda-tile" data-nj-open="nj-nota-<?php echo $id; ?>">
              <?php if ($src) : ?><img alt="" src="<?php echo esc_url($src); ?>"><?php else : ?><span class="nj-prenda-tile__empty">Sin foto</span><?php endif; ?>
              <strong><?php echo esc_html($post->post_title); ?></strong>
              <span><?php echo esc_html($date); ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <dialog class="nj-modal" id="nj-nota-nueva">
          <form class="nj-prenda-card" method="post" action="<?php echo esc_url($action); ?>">
            <div class="nj-modal-bar">
              <strong>Nueva nota</strong>
              <button type="button" class="button" data-nj-close>Cerrar</button>
            </div>
            <input type="hidden" name="action" value="nj_blog_save">
            <input type="hidden" name="nota_id" value="0">
            <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('nj_blog_save', 'nj_blog_nonce'); } ?>
            <?php nj_blog_admin_fields(null); ?>
            <p class="nj-modal-foot"><button type="submit" class="button button-primary">Publicar nota</button></p>
          </form>
        </dialog>
        <?php foreach ($posts as $post) :
            $id = (int) $post->ID;
            $url = function_exists('get_permalink') ? get_permalink($id) : '';
            ?>
          <dialog class="nj-modal" id="nj-nota-<?php echo $id; ?>">
            <form class="nj-prenda-card" method="post" action="<?php echo esc_url($action); ?>">
              <div class="nj-modal-bar">
                <strong><?php echo esc_html($post->post_title); ?></strong>
                <span class="nj-prenda-actions">
                  <?php if ($url) : ?><a class="button" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">Ver nota</a><?php endif; ?>
                  <button type="button" class="button" data-nj-close>Cerrar</button>
                </span>
              </div>
              <input type="hidden" name="action" value="nj_blog_save">
              <input type="hidden" name="nota_id" value="<?php echo $id; ?>">
              <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('nj_blog_save', 'nj_blog_nonce'); } ?>
              <?php nj_blog_admin_fields($post); ?>
              <p class="nj-modal-foot">
                <button type="submit" class="button" name="ocultar" value="1">Ocultar</button>
                <button type="submit" class="button button-primary">Guardar</button>
              </p>
            </form>
          </dialog>
        <?php endforeach; ?>
        <?php if ($hidden) : ?>
          <h3>Notas ocultas</h3>
          <div class="nj-prenda-list">
            <?php foreach ($hidden as $post) : ?>
              <form class="nj-prenda-card" method="post" action="<?php echo esc_url($action); ?>">
                <input type="hidden" name="action" value="nj_blog_save">
                <input type="hidden" name="nota_id" value="<?php echo (int) $post->ID; ?>">
                <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('nj_blog_save', 'nj_blog_nonce'); } ?>
                <strong><?php echo esc_html($post->post_title); ?></strong>
                <button type="submit" class="button" name="mostrar" value="1">Volver a mostrar</button>
              </form>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php
    }
}
