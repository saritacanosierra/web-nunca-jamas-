<?php
define('NJ_PREVIEW', true);
require __DIR__ . '/inc/helpers.php';
require __DIR__ . '/inc/ficha.php';
$nj_ref = isset($_GET['ref']) ? preg_replace('/[^a-z0-9_-]/', '', (string) $_GET['ref']) : '';
$nj_ficha = nj_ficha_locate($nj_ref);
$nj_document_title = $nj_ficha['name'] . ' — Nunca Jamás';
require __DIR__ . '/header.php';
require __DIR__ . '/template-parts/ficha.php';
require __DIR__ . '/footer.php';
