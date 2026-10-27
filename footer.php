<?php require_once __DIR__ . '/inc/helpers.php'; ?>
  <footer class="site-footer">
    <div class="wrap footer-grid">
      <div class="footer-brand">
        <?php require __DIR__ . '/template-parts/marca.php'; ?>
        <p>Ropa de cuento para niñas: colecciones, línea, accesorios y personalizados sobre pedido.</p>
        <form class="news" data-news>
          <input type="email" required placeholder="Tu correo" aria-label="Correo">
          <button type="submit" aria-label="Suscribirme">→</button>
          <p class="form-note"></p>
        </form>
      </div>
      <div>
        <h3>Ayuda</h3>
        <ul>
          <li><a href="<?php echo esc_url(nj_page_url('preguntas')); ?>">Preguntas frecuentes</a></li>
          <li><a href="<?php echo esc_url(nj_page_url('envios')); ?>">Envíos</a></li>
          <li><a href="<?php echo esc_url(nj_page_url('cambios')); ?>">Cambios</a></li>
          <li><a href="<?php echo esc_url(nj_page_url('tallas')); ?>">Guía de tallas</a></li>
        </ul>
      </div>
      <div>
        <h3>La tienda</h3>
        <ul>
          <?php foreach (array_slice(nj_nav_items(), 0, 6) as $item) : ?>
            <li><a href="<?php echo esc_url($item['href']); ?>"><?php nj_e($item['label']); ?></a></li>
          <?php endforeach; ?>
          <li><a href="<?php echo esc_url(nj_shop_link('cart')); ?>">Bolsa</a></li>
          <li><a href="<?php echo esc_url(nj_shop_link('account')); ?>">Mi cuenta</a></li>
        </ul>
      </div>
      <div>
        <h3>Contacto</h3>
        <ul>
          <li><a href="mailto:saritacanosierra@gmail.com">saritacanosierra@gmail.com</a></li>
          <li><a href="<?php echo esc_url(nj_home_url()); ?>">Nunca Jamás</a></li>
          <li>Envíos a Colombia</li>
        </ul>
        <div class="pay-row" aria-label="Medios de pago">
          <span class="pill">VISA</span>
          <span class="pill">Mastercard</span>
          <span class="pill">PSE</span>
          <span class="pill">Contraentrega</span>
        </div>
        <div class="trust-row">
          <span class="pill">Compra segura</span>
          <span class="pill">Hecho para niñas</span>
        </div>
      </div>
    </div>
    <div class="legal">
      <div class="wrap">
        <span>© <?php echo date('Y'); ?> Nunca Jamás. Ropa para niñas.</span>
        <span><a href="<?php echo esc_url(nj_page_url('terminos')); ?>">Términos</a> · <a href="<?php echo esc_url(nj_page_url('privacidad')); ?>">Privacidad</a></span>
      </div>
    </div>
  </footer>
  <?php require __DIR__ . '/template-parts/vista-rapida.php'; ?>
  <?php require __DIR__ . '/template-parts/bolsa.php'; ?>
  <?php if (defined('NJ_PREVIEW')) : ?>
    <script src="assets/bolsa.js?v=1.1.1"></script>
    <script src="assets/tienda.js?v=1.3.2"></script>
    <script src="assets/home.js?v=1.2.9"></script>
  <?php endif; ?>
  <?php if (function_exists('wp_footer')) { wp_footer(); } ?>
</body>
</html>
