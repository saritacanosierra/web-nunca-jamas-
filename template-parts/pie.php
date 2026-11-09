<?php
$nj_footer = function_exists('nj_tema_footer') ? nj_tema_footer() : array(
    'blurb' => '',
    'news' => '',
    'news_label' => '',
    'news_placeholder' => 'Tu correo',
    'email' => '',
    'whatsapp' => '',
    'place' => '',
    'legal' => '',
    'columns' => array(),
    'socials' => array(),
    'pays' => array(),
    'trust' => array(),
    'legal_links' => array(),
);
$nj_footer_wa = '';
if ($nj_footer['whatsapp'] !== '') {
    $nj_footer_wa = 'https://wa.me/' . $nj_footer['whatsapp'];
} elseif (function_exists('nj_whatsapp_chat_url')) {
    $nj_footer_wa = nj_whatsapp_chat_url();
}
?>
<footer class="site-footer">
  <div class="wrap footer-grid">
    <div class="footer-brand">
      <?php require __DIR__ . '/marca.php'; ?>
      <?php if ($nj_footer['blurb'] !== '') : ?>
        <p><?php nj_e($nj_footer['blurb']); ?></p>
      <?php endif; ?>
      <?php if ($nj_footer['news'] === '1') : ?>
        <p class="footer-news-label"><?php nj_e($nj_footer['news_label']); ?></p>
        <form class="news" data-news>
          <input type="email" required placeholder="<?php echo esc_attr($nj_footer['news_placeholder']); ?>" aria-label="<?php echo esc_attr($nj_footer['news_label']); ?>">
          <button type="submit" aria-label="Suscribirme">→</button>
          <p class="form-note"></p>
        </form>
      <?php endif; ?>
    </div>
    <?php foreach ($nj_footer['columns'] as $nj_column) : ?>
      <div>
        <h3><?php nj_e($nj_column['title']); ?></h3>
        <ul>
          <?php foreach ($nj_column['links'] as $nj_link) : ?>
            <li><a href="<?php echo esc_url($nj_link['href']); ?>"><?php nj_e($nj_link['label']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
    <?php if ($nj_footer['email'] !== '' || $nj_footer_wa !== '' || $nj_footer['place'] !== '' || $nj_footer['socials'] || $nj_footer['pays'] || $nj_footer['trust']) : ?>
    <div>
      <h3>Contacto</h3>
      <ul>
        <?php if ($nj_footer['email'] !== '') : ?>
          <li><a href="<?php echo esc_url('mailto:' . $nj_footer['email']); ?>"><?php echo esc_html($nj_footer['email']); ?></a></li>
        <?php endif; ?>
        <?php if ($nj_footer_wa !== '') : ?>
          <li><a href="<?php echo esc_url($nj_footer_wa); ?>">WhatsApp</a></li>
        <?php endif; ?>
        <?php if ($nj_footer['place'] !== '') : ?>
          <li><?php nj_e($nj_footer['place']); ?></li>
        <?php endif; ?>
        <?php foreach ($nj_footer['socials'] as $nj_social) : ?>
          <li><a href="<?php echo esc_url($nj_social['href']); ?>"><?php nj_e($nj_social['label']); ?></a></li>
        <?php endforeach; ?>
      </ul>
      <?php if ($nj_footer['pays']) : ?>
        <div class="pay-row" aria-label="Medios de pago">
          <?php foreach ($nj_footer['pays'] as $nj_pay) : ?>
            <span class="pill"><?php echo esc_html($nj_pay); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if ($nj_footer['trust']) : ?>
        <div class="trust-row">
          <?php foreach ($nj_footer['trust'] as $nj_trust) : ?>
            <span class="pill"><?php echo esc_html($nj_trust); ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <div class="legal">
    <div class="wrap">
      <span>© <?php echo esc_html(date('Y')); ?><?php echo $nj_footer['legal'] !== '' ? ' ' . esc_html($nj_footer['legal']) : ''; ?></span>
      <?php if ($nj_footer['legal_links']) : ?>
        <span>
          <?php foreach ($nj_footer['legal_links'] as $nj_index => $nj_legal) : ?>
            <?php if ($nj_index > 0) : ?> · <?php endif; ?>
            <a href="<?php echo esc_url($nj_legal['href']); ?>"><?php nj_e($nj_legal['label']); ?></a>
          <?php endforeach; ?>
        </span>
      <?php endif; ?>
    </div>
  </div>
</footer>
