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
$nj_perks = function_exists('nj_tema_footer_perks') ? nj_tema_footer_perks() : array();
$nj_groups = function_exists('nj_tema_footer_groups') ? nj_tema_footer_groups() : array();
$nj_perk_icon = function ($icon) {
    if ($icon === 'pay') {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8"/><path d="M12 7v10M9.5 9.5c.4-1 1.2-1.5 2.5-1.5 1.6 0 2.5.8 2.5 2s-.8 1.8-2.5 2-2.6.8-2.6 2.1 1 2.1 2.6 2.1c1.3 0 2.2-.5 2.6-1.5"/></svg>';
    }
    if ($icon === 'change') {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7 7H4v3"/><path d="M4 7a8 8 0 0 1 13.5-2"/><path d="M17 17h3v-3"/><path d="M20 17a8 8 0 0 1-13.5 2"/></svg>';
    }
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 7h11v8H3z"/><path d="M14 10h4l3 3v2h-7"/><circle cx="7" cy="17.5" r="1.5"/><circle cx="17" cy="17.5" r="1.5"/></svg>';
};
?>
<footer class="site-footer">
  <?php if ($nj_perks) : ?>
    <div class="footer-perks">
      <div class="wrap">
        <?php foreach ($nj_perks as $nj_perk) : ?>
          <?php $nj_perk_tag = $nj_perk['href'] !== '' ? 'a' : 'div'; ?>
          <<?php echo $nj_perk_tag; ?> class="footer-perk"<?php echo $nj_perk['href'] !== '' ? ' href="' . esc_url($nj_perk['href']) . '"' : ''; ?>>
            <span class="footer-perk__icon" aria-hidden="true"><?php echo $nj_perk_icon($nj_perk['icon']); ?></span>
            <span>
              <strong><?php echo esc_html($nj_perk['title']); ?></strong>
              <em><?php echo esc_html($nj_perk['text']); ?></em>
            </span>
          </<?php echo $nj_perk_tag; ?>>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  <div class="wrap footer-grid">
    <div class="footer-brand">
      <?php require __DIR__ . '/marca.php'; ?>
      <?php if ($nj_footer['blurb'] !== '') : ?>
        <p class="footer-blurb"><?php nj_e($nj_footer['blurb']); ?></p>
      <?php endif; ?>
      <?php if ($nj_footer['news'] === '1') : ?>
        <p class="footer-news-label"><?php nj_e($nj_footer['news_label']); ?></p>
        <form class="news" data-news>
          <div class="news__row">
            <input type="email" required placeholder="<?php echo esc_attr($nj_footer['news_placeholder']); ?>" aria-label="<?php echo esc_attr($nj_footer['news_label']); ?>">
            <button type="submit">Enviar</button>
          </div>
          <p class="form-note"></p>
        </form>
      <?php endif; ?>
      <?php if ($nj_footer['socials']) : ?>
        <div class="socials">
          <?php foreach ($nj_footer['socials'] as $nj_social) : ?>
            <a href="<?php echo esc_url($nj_social['href']); ?>"><?php nj_e($nj_social['label']); ?></a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <?php foreach ($nj_groups as $nj_column) : ?>
      <div>
        <h3><?php nj_e($nj_column['title']); ?></h3>
        <ul>
          <?php foreach ($nj_column['links'] as $nj_link) : ?>
            <li><a href="<?php echo esc_url($nj_link['href']); ?>"><?php nj_e($nj_link['label']); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endforeach; ?>
    <?php if ($nj_footer['email'] !== '' || $nj_footer_wa !== '' || $nj_footer['place'] !== '') : ?>
    <div>
      <h3><?php echo esc_html(function_exists('nj_espacio_texto') && nj_espacio_texto('contacto') !== '' ? nj_espacio_texto('contacto') : 'Contáctanos'); ?></h3>
      <ul class="footer-contact">
        <?php if ($nj_footer['email'] !== '') : ?>
          <li><a href="<?php echo esc_url('mailto:' . $nj_footer['email']); ?>"><?php echo esc_html($nj_footer['email']); ?></a></li>
        <?php endif; ?>
        <?php if ($nj_footer_wa !== '') : ?>
          <li><a href="<?php echo esc_url($nj_footer_wa); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
        <?php endif; ?>
        <?php if ($nj_footer['place'] !== '') : ?>
          <li><?php nj_e($nj_footer['place']); ?></li>
        <?php endif; ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
  <div class="legal">
    <div class="wrap">
      <?php if ($nj_footer['pays']) : ?>
        <div class="pay-row" aria-label="Medios de pago">
          <?php foreach ($nj_footer['pays'] as $nj_pay) : ?>
            <span><?php echo esc_html($nj_pay); ?></span>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <span>© <?php echo esc_html(date('Y')); ?><?php echo $nj_footer['legal'] !== '' ? ' ' . esc_html($nj_footer['legal']) : ''; ?></span>
      <?php endif; ?>
      <span class="legal__links">
        <?php if ($nj_footer['pays']) : ?>
          <span class="legal__copy">© <?php echo esc_html(date('Y')); ?><?php echo $nj_footer['legal'] !== '' ? ' ' . esc_html($nj_footer['legal']) : ''; ?></span>
        <?php endif; ?>
        <?php foreach ($nj_footer['legal_links'] as $nj_legal) : ?>
          <a href="<?php echo esc_url($nj_legal['href']); ?>"><?php nj_e($nj_legal['label']); ?></a>
        <?php endforeach; ?>
      </span>
    </div>
  </div>
</footer>
