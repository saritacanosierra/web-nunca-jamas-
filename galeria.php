<?php
define('NJ_PREVIEW', true);
$nj_section = isset($_GET['seccion']) ? preg_replace('/[^a-z0-9_-]/', '', $_GET['seccion']) : 'linea';
$nj_tipo = isset($_GET['tipo']) ? preg_replace('/[^a-z0-9_-]/', '', $_GET['tipo']) : '';
$nj_orden = isset($_GET['orden']) ? preg_replace('/[^a-z-]/', '', $_GET['orden']) : 'date';
require __DIR__ . '/header.php';
$nj_catalog = nj_catalog_preview($nj_section, $nj_tipo, $nj_orden);
require __DIR__ . '/template-parts/galeria.php';
require __DIR__ . '/footer.php';
