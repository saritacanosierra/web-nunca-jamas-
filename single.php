<?php
get_header();
if (function_exists('is_singular') && is_singular('post')) {
    require get_template_directory() . '/template-parts/blog-nota.php';
} else {
    require get_template_directory() . '/template-parts/contenido.php';
}
get_footer();
