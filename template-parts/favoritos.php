<?php
$nj_shop = function_exists('nj_shop_link') ? nj_shop_link('shop') : (function_exists('nj_home_url') ? nj_home_url() : '#');
?>
<main class="favoritos">
  <header class="favoritos__head">
    <h1>Mis favoritos</h1>
    <p>Las prendas que guardas con el corazón quedan en esta lista.</p>
  </header>
  <p class="favoritos__empty" data-favoritos-empty hidden>Todavía no guardas prendas. Entra a la tienda y toca el corazón de la que te guste.</p>
  <p class="favoritos__empty" data-favoritos-missing hidden>Esas prendas ya no están en la tienda.</p>
  <div class="product-grid" data-favoritos-list></div>
  <p class="favoritos__more"><a class="btn btn-ghost" href="<?php echo esc_url($nj_shop); ?>">Seguir viendo la tienda</a></p>
</main>
