<?php
if (!function_exists('nj_hero_rows')) {
    require_once dirname(__DIR__) . '/inc/hero.php';
}
$nj_hero = array_values(array_filter(nj_hero_rows(), 'nj_hero_is_usable'));
if (!$nj_hero) {
    return;
}
?>
<section class="hero hero--banners<?php echo $nj_hero[0]['type'] === 'build' ? ' is-build' : ''; ?>" data-hero>
  <?php foreach ($nj_hero as $i => $slide) :
      $src = nj_hero_src($slide);
      $build = $slide['type'] === 'build';
      $copy_only = $build && $src === '';
      ?>
    <a class="hero__slide<?php echo $i === 0 ? ' is-active' : ''; ?><?php echo $build ? ' hero__slide--build' : ''; ?><?php echo $copy_only ? ' is-copy-only' : ''; ?>" href="<?php echo esc_url(nj_hero_href($slide)); ?>"<?php echo $build ? ' style="' . esc_attr('--hero-bg:' . $slide['color']) . '"' : ''; ?>>
      <?php if ($build) : ?>
        <span class="hero__build">
          <span class="hero__build-copy">
            <?php if ($slide['eyebrow'] !== '') : ?>
              <small><?php echo esc_html($slide['eyebrow']); ?></small>
            <?php endif; ?>
            <?php if ($slide['title'] !== '') : ?>
              <strong><?php echo esc_html($slide['title']); ?></strong>
            <?php endif; ?>
            <?php if ($slide['text'] !== '') : ?>
              <span class="hero__build-text"><?php echo esc_html($slide['text']); ?></span>
            <?php endif; ?>
            <?php if ($slide['button'] !== '') : ?>
              <span class="hero__build-btn"><?php echo esc_html($slide['button']); ?></span>
            <?php endif; ?>
          </span>
          <?php if ($src !== '') : ?>
            <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($slide['alt']); ?>"<?php echo $i === 0 ? ' fetchpriority="high" decoding="async"' : ' loading="lazy" decoding="async"'; ?>>
          <?php endif; ?>
        </span>
      <?php else : ?>
        <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($slide['alt']); ?>"<?php echo $i === 0 ? ' fetchpriority="high" decoding="async"' : ' loading="lazy" decoding="async"'; ?>>
      <?php endif; ?>
    </a>
  <?php endforeach; ?>
  <button class="hero__arrow hero__arrow--prev" type="button" data-prev aria-label="Anterior">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 5 L8 12 L15 19"/></svg>
  </button>
  <button class="hero__arrow hero__arrow--next" type="button" data-next aria-label="Siguiente">
    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5 L16 12 L9 19"/></svg>
  </button>
  <div class="hero__dots">
    <?php foreach ($nj_hero as $i => $slide) : ?>
      <button type="button" data-dot="<?php echo (int) $i; ?>" class="<?php echo $i === 0 ? 'is-active' : ''; ?>" aria-label="Diapositiva <?php echo (int) $i + 1; ?>"></button>
    <?php endforeach; ?>
  </div>
</section>
