<?php
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/shop.php';
require_once __DIR__ . '/inc/tema.php';
$GLOBALS['nj_theme_footer'] = true;
require __DIR__ . '/template-parts/pie.php';
?>
  <?php if (defined('NJ_PREVIEW')) : ?>
  <?php require __DIR__ . '/template-parts/vista-rapida.php'; ?>
  <?php require __DIR__ . '/template-parts/bolsa.php'; ?>
  <?php require __DIR__ . '/template-parts/cuenta.php'; ?>
    <?php $nj_pantalla = function_exists('nj_pantalla') ? nj_pantalla() : array('tienda' => true); ?>
    <script src="assets/bolsa.js?v=<?php echo rawurlencode(NJ_VERSION); ?>"></script>
    <?php if (!empty($nj_pantalla['tienda'])) : ?>
    <script src="assets/tienda.js?v=<?php echo rawurlencode(NJ_VERSION); ?>"></script>
    <?php endif; ?>
    <?php if (!empty($nj_pantalla['ficha_js'])) : ?>
    <script src="assets/ficha.js?v=<?php echo rawurlencode(NJ_VERSION); ?>"></script>
    <?php endif; ?>
    <script src="assets/home.js?v=<?php echo rawurlencode(NJ_VERSION); ?>"></script>
    <script src="assets/cuenta.js?v=<?php echo rawurlencode(NJ_VERSION); ?>"></script>
  <?php endif; ?>
  <?php if (function_exists('wp_footer')) { wp_footer(); } ?>
</body>
</html>
