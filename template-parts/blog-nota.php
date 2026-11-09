<?php
if (!have_posts()) {
    return;
}
while (have_posts()) {
    the_post();
    $nj_post_id = get_the_ID();
    $nj_image = nj_blog_image($nj_post_id);
    $nj_related = nj_blog_related($nj_post_id);
    $nj_note = function_exists('nj_blog_note_parts') ? nj_blog_note_parts($nj_post_id) : array('before' => '', 'after' => '', 'ads' => array());
    ?>
<main class="blog blog-note">
  <article class="wrap">
    <p class="blog-kicker"><a href="<?php echo esc_url(nj_blog_url()); ?>">Cuentos</a></p>
    <h1><?php the_title(); ?></h1>
    <p class="blog-meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(nj_blog_minutes($nj_post_id)); ?></p>
    <?php if ($nj_image !== '') : ?>
      <figure class="blog-hero">
        <img src="<?php echo esc_url($nj_image); ?>" alt="">
      </figure>
    <?php endif; ?>
    <div class="blog-body">
      <?php echo $nj_note['before']; ?>
    </div>
    <?php echo function_exists('nj_blog_ad_html') ? nj_blog_ad_html($nj_note['ads'][0]) : ''; ?>
    <?php if ($nj_note['after'] !== '') : ?>
      <div class="blog-body">
        <?php echo $nj_note['after']; ?>
      </div>
    <?php endif; ?>
    <?php echo function_exists('nj_blog_ad_html') ? nj_blog_ad_html($nj_note['ads'][1]) : ''; ?>
  </article>

  <?php if ($nj_related) : ?>
    <section class="wrap blog-more">
      <h2>Sigue leyendo</h2>
      <div class="blog-grid">
        <?php foreach ($nj_related as $nj_post) : ?>
          <article class="blog-card">
            <a href="<?php echo esc_url(get_permalink($nj_post)); ?>">
              <h3><?php echo esc_html(get_the_title($nj_post)); ?></h3>
              <p><?php echo esc_html(get_the_excerpt($nj_post)); ?></p>
              <small><?php echo esc_html(nj_blog_minutes($nj_post->ID)); ?></small>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>
</main>
    <?php
}
