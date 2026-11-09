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
    $nj_help = function_exists('nj_pages_help_current') ? nj_pages_help_current() : null;
    if (is_array($nj_help)) {
        $nj_help_title = get_the_title();
        require __DIR__ . '/ayuda.php';
    } else {
        echo '<h1 class="page-title">' . esc_html(get_the_title()) . '</h1>';
        the_content();
    }
    echo '</div></main>';
}
