<?php
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/shop.php';
require_once __DIR__ . '/inc/cards.php';
require_once __DIR__ . '/inc/catalog.php';
require_once __DIR__ . '/inc/portada.php';
require_once __DIR__ . '/inc/ficha.php';
$nj_menu = nj_menu();
$nj_search_action = nj_home_url();
?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if (defined('NJ_PREVIEW')) : ?>
  <title><?php echo esc_html(isset($nj_document_title) ? $nj_document_title : 'Inicio — Nunca Jamás'); ?></title>
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Baloo+2:wght@700;800&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <?php if (defined('NJ_PREVIEW')) : ?>
    <link rel="stylesheet" href="assets/home.css?v=1.6.0">
  <?php endif; ?>
  <?php if (function_exists('wp_head')) { wp_head(); } ?>
</head>
<body <?php echo function_exists('body_class') ? '' : 'class="nj-home"'; ?><?php if (function_exists('body_class')) { body_class('nj-home'); } ?>>
<?php if (function_exists('wp_body_open')) { wp_body_open(); } ?>
  <header class="site-header">
    <div class="header-inner">
      <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-label="Abrir menú">
        <span></span>
      </button>
      <nav class="main-nav" aria-label="Departamentos">
        <?php foreach ($nj_menu['departments'] as $item) : ?>
          <div class="nav-item">
            <a class="nav-link" href="<?php echo esc_url($item['href']); ?>"><?php nj_e($item['label']); ?></a>
            <?php if (!empty($item['children'])) : ?>
              <?php $nj_panel = $item; require __DIR__ . '/template-parts/menu-panel.php'; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <a class="nav-link nav-link--sale" href="<?php echo esc_url($nj_menu['sale']['href']); ?>"><?php nj_e($nj_menu['sale']['label']); ?></a>
      </nav>
      <?php require __DIR__ . '/template-parts/marca.php'; ?>
      <div class="header-tools">
        <div class="nav-item nav-item--collections">
          <a class="nav-link" href="<?php echo esc_url($nj_menu['collections']['href']); ?>">
            <?php nj_e($nj_menu['collections']['label']); ?>
            <span class="nav-caret" aria-hidden="true"></span>
          </a>
          <?php
          $nj_panel = $nj_menu['collections'];
          $nj_panel['align'] = 'end';
          require __DIR__ . '/template-parts/menu-panel.php';
          ?>
        </div>
        <a class="nav-newest" href="<?php echo esc_url($nj_menu['newest']['href']); ?>"><?php nj_e($nj_menu['newest']['label']); ?></a>
        <button class="icon-btn" type="button" data-search-open aria-expanded="false" aria-label="Buscar">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4 4"/></svg>
        </button>
        <a class="icon-btn" href="<?php echo esc_url(nj_shop_link('account')); ?>" aria-label="Mi cuenta">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3.2"/><path d="M5 19c1.4-3 3.8-4.5 7-4.5S17.6 16 19 19"/></svg>
        </a>
        <button class="icon-btn" type="button" data-bag-open aria-label="Bolsa">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 8h12l-1 12H7L6 8z"/><path d="M9 8V7a3 3 0 0 1 6 0v1"/></svg>
          <span class="bag-count"><?php echo esc_html((string) nj_cart_count()); ?></span>
        </button>
      </div>
    </div>
    <div class="search-bar" data-search>
      <form action="<?php echo esc_url($nj_search_action); ?>" method="get" role="search">
        <input type="search" name="s" placeholder="Buscar prendas, cuentos o accesorios" aria-label="Buscar">
        <input type="hidden" name="post_type" value="product">
        <button type="submit">Buscar</button>
      </form>
    </div>
    <nav class="nav-drawer" data-drawer aria-label="Menú móvil">
      <?php foreach ($nj_menu['departments'] as $item) : ?>
        <a href="<?php echo esc_url($item['href']); ?>"><?php nj_e($item['label']); ?></a>
        <?php foreach ($item['children'] as $child) : ?>
          <a class="nav-drawer__child" href="<?php echo esc_url($child['href']); ?>"><?php nj_e($child['label']); ?></a>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <a class="nav-link--sale" href="<?php echo esc_url($nj_menu['sale']['href']); ?>"><?php nj_e($nj_menu['sale']['label']); ?></a>
      <a href="<?php echo esc_url($nj_menu['collections']['href']); ?>"><?php nj_e($nj_menu['collections']['label']); ?></a>
      <?php foreach ($nj_menu['collections']['children'] as $child) : ?>
        <a class="nav-drawer__child" href="<?php echo esc_url($child['href']); ?>"><?php nj_e($child['label']); ?></a>
      <?php endforeach; ?>
      <a href="<?php echo esc_url($nj_menu['newest']['href']); ?>"><?php nj_e($nj_menu['newest']['label']); ?></a>
      <button type="button" data-bag-open>Bolsa</button>
      <a href="<?php echo esc_url(nj_shop_link('account')); ?>">Mi cuenta</a>
    </nav>
  </header>
