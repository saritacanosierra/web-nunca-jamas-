<?php
$query = function_exists('nj_blog_query') ? nj_blog_query() : null;
$posts = ($query && !empty($query->posts)) ? $query->posts : array();
$featured = $posts ? array_shift($posts) : null;
?>
<main class="blog">
  <header class="blog-mast">
    <div class="wrap">
      <p class="blog-kicker">Cuentos</p>
      <h1>Historias para leer juntos</h1>
      <p>Cuentos y notas sobre por qué leer en la niñez acompaña el día, la noche y la forma de nombrar lo que se siente.</p>
    </div>
  </header>

  <div class="wrap">
    <?php if (!$featured) : ?>
      <p class="blog-empty">Todavía no hay cuentos publicados.</p>
    <?php else : ?>
      <article class="blog-feature">
        <a class="blog-feature__media" href="<?php echo esc_url(get_permalink($featured)); ?>">
          <?php $nj_image = nj_blog_image($featured->ID); ?>
          <?php if ($nj_image !== '') : ?>
            <img src="<?php echo esc_url($nj_image); ?>" alt="">
          <?php else : ?>
            <span>Para leer esta noche</span>
          <?php endif; ?>
        </a>
        <div class="blog-feature__copy">
          <p class="blog-kicker">El más reciente</p>
          <h2><a href="<?php echo esc_url(get_permalink($featured)); ?>"><?php echo esc_html(get_the_title($featured)); ?></a></h2>
          <p><?php echo esc_html(function_exists('get_the_excerpt') ? get_the_excerpt($featured) : (isset($featured->post_excerpt) ? $featured->post_excerpt : '')); ?></p>
          <p class="blog-meta"><?php echo esc_html(get_the_date('', $featured)); ?> · <?php echo esc_html(nj_blog_minutes($featured->ID)); ?></p>
          <a class="btn btn-yellow" href="<?php echo esc_url(get_permalink($featured)); ?>">Leer el cuento</a>
        </div>
      </article>

      <?php if ($posts) : ?>
        <div class="blog-grid">
          <?php foreach ($posts as $nj_post) : ?>
            <article class="blog-card">
              <a href="<?php echo esc_url(get_permalink($nj_post)); ?>">
                <?php $nj_card_image = nj_blog_image($nj_post->ID); ?>
                <span class="blog-card__media">
                  <?php if ($nj_card_image !== '') : ?>
                    <img src="<?php echo esc_url($nj_card_image); ?>" alt="">
                  <?php endif; ?>
                </span>
                <h2><?php echo esc_html(get_the_title($nj_post)); ?></h2>
                <p><?php echo esc_html(function_exists('get_the_excerpt') ? get_the_excerpt($nj_post) : (isset($nj_post->post_excerpt) ? $nj_post->post_excerpt : '')); ?></p>
                <small><?php echo esc_html(get_the_date('', $nj_post)); ?> · <?php echo esc_html(nj_blog_minutes($nj_post->ID)); ?></small>
              </a>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if ($query->max_num_pages > 1 && function_exists('paginate_links')) : ?>
        <nav class="blog-pages" aria-label="Páginas de cuentos">
          <?php
          echo paginate_links(array(
              'total' => $query->max_num_pages,
              'current' => max(1, $query->get('paged')),
              'prev_text' => 'Anterior',
              'next_text' => 'Siguiente',
          ));
          ?>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</main>
