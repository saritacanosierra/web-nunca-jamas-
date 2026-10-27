<?php
require_once dirname(__DIR__) . '/inc/wp-stubs.php';
defined('ABSPATH') || exit;
get_header();
$nj_catalog = nj_catalog_from_shop();
require dirname(__DIR__) . '/template-parts/galeria.php';
get_footer();
