<?php
/**
 * Pantalla para el logo y los colores. Los datos viven en inc/tema.php.
 */

if (!function_exists('nj_tema_admin_page')) {
    function nj_tema_admin_page() {
        $allowed = function_exists('nj_tienda_can') ? nj_tienda_can() : (function_exists('current_user_can') && current_user_can('edit_theme_options'));
        if (!$allowed) {
            return;
        }
        $settings = nj_tema_settings();
        $logo = $settings['logo'] ? nj_tema_logo_url() : '';
        $colors = array(
            'coral' => 'Rosa',
            'teal' => 'Verde',
            'gold' => 'Dorado',
            'ink' => 'Texto',
            'bg' => 'Fondo',
        );
        $saved = isset($_GET['updated']) && sanitize_key(wp_unslash($_GET['updated'])) === 'tema';
        ?>
        <div class="wrap nj-studio-page">
          <h1>Editar el tema</h1>
          <?php if ($saved) : ?>
            <div class="notice notice-success is-dismissible"><p>El tema quedó guardado.</p></div>
          <?php endif; ?>
          <p class="nj-studio-lead">Cambia el logo y los colores de la tienda. Las fotos, los banners y las prendas se cambian en Mi tienda.</p>
          <form class="nj-tema-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('nj_tema_save', 'nj_tema_nonce'); ?>
            <input type="hidden" name="action" value="nj_tema_save">
            <div class="nj-photo nj-photo--square" data-nj-photo>
              <img alt=""<?php echo $logo === '' ? ' hidden' : ' src="' . esc_url($logo) . '"'; ?>>
              <div class="nj-photo-body">
                <input data-nj-id type="hidden" name="logo" value="<?php echo esc_attr((string) $settings['logo']); ?>">
                <p class="nj-prenda-actions">
                  <button type="button" class="button button-primary" data-nj-pick>Elegir logo</button>
                  <button type="button" class="button" data-nj-clear>Volver al logo del tema</button>
                </p>
              </div>
            </div>
            <div class="nj-tema-colors">
              <?php foreach ($colors as $key => $label) : ?>
                <label><?php echo esc_html($label); ?>
                  <input type="color" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($settings[$key]); ?>">
                </label>
              <?php endforeach; ?>
            </div>
            <p class="nj-prenda-actions">
              <button type="submit" class="button button-primary">Guardar tema</button>
              <button type="submit" class="button" name="nj_tema_reset" value="1">Volver al tema original</button>
            </p>
          </form>
        </div>
        <?php
    }
}
