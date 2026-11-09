<?php
$nj_cuenta_contact = function_exists('nj_cuenta_contact') ? nj_cuenta_contact() : array('href' => '#', 'external' => false);
$nj_cuenta_name = function_exists('nj_cuenta_name') ? nj_cuenta_name() : '';
$nj_cuenta_logged = function_exists('is_user_logged_in') && is_user_logged_in();
$nj_cuenta_login = function_exists('nj_cuenta_login_url') ? nj_cuenta_login_url() : '#';
$nj_cuenta_orders = function_exists('nj_cuenta_orders_url') ? nj_cuenta_orders_url() : $nj_cuenta_login;
$nj_cuenta_saved = function_exists('nj_cuenta_favoritos_url') ? nj_cuenta_favoritos_url() : '#';
$nj_cuenta_logout = function_exists('wp_logout_url') ? wp_logout_url(function_exists('nj_home_url') ? nj_home_url() : '') : '';
?>
<div class="cuenta-sheet" data-cuenta hidden>
  <button class="cuenta-sheet__backdrop" type="button" data-cuenta-close aria-label="Cerrar mi cuenta"></button>
  <aside class="cuenta-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="nj-cuenta-title">
    <button class="cuenta-sheet__close" type="button" data-cuenta-close aria-label="Cerrar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6L6 18"/></svg>
    </button>
    <p class="cuenta-sheet__title" id="nj-cuenta-title">Mi cuenta</p>
    <?php if ($nj_cuenta_name !== '') : ?>
      <p class="cuenta-sheet__hello">Hola, <?php echo esc_html($nj_cuenta_name); ?></p>
    <?php endif; ?>
    <nav class="cuenta-sheet__links" aria-label="Mi cuenta">
      <a href="<?php echo esc_url($nj_cuenta_saved); ?>">Mis Favoritos</a>
      <a href="<?php echo esc_url($nj_cuenta_orders); ?>">Historial de pedidos</a>
      <a href="<?php echo esc_url($nj_cuenta_contact['href']); ?>"<?php echo !empty($nj_cuenta_contact['external']) ? ' target="_blank" rel="noopener"' : ''; ?>>Contáctanos</a>
    </nav>
    <?php if ($nj_cuenta_logged) : ?>
      <a class="cuenta-sheet__enter" href="<?php echo esc_url($nj_cuenta_login); ?>">Mi cuenta</a>
      <?php if ($nj_cuenta_logout !== '') : ?>
        <a class="cuenta-sheet__leave" href="<?php echo esc_url($nj_cuenta_logout); ?>">Salir</a>
      <?php endif; ?>
    <?php else : ?>
      <a class="cuenta-sheet__enter" href="<?php echo esc_url($nj_cuenta_login); ?>">Ingresar</a>
    <?php endif; ?>
  </aside>
</div>
