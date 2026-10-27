<?php
require_once __DIR__ . '/inc/helpers.php';
$GLOBALS['nj_theme_footer'] = true;
require __DIR__ . '/template-parts/pie.php';
?>
  <?php if (defined('NJ_PREVIEW')) : ?>
  <?php require __DIR__ . '/template-parts/vista-rapida.php'; ?>
  <?php require __DIR__ . '/template-parts/bolsa.php'; ?>
    <script src="assets/bolsa.js?v=1.1.2"></script>
    <script src="assets/tienda.js?v=1.3.3"></script>
    <script src="assets/home.js?v=1.2.9"></script>
  <?php endif; ?>
  <?php if (function_exists('wp_footer')) { wp_footer(); } ?>
</body>
</html>
