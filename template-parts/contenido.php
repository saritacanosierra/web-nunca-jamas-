<?php
/**
 * Checkout, carrito y cuenta. El resto de páginas queda en el marco del tema.
 */
if (!have_posts()) {
    return;
}
while (have_posts()) {
    the_post();
    if (function_exists('is_checkout') && (is_checkout() || is_cart() || is_account_page())) {
        the_content();
        continue;
    }
    echo '<main class="section"><div class="wrap">';
    the_content();
    echo '</div></main>';
}
