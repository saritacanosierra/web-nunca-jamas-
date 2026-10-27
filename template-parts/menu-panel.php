<?php
/**
 * Panel de un departamento: "Ver todo" y sus subcategorías.
 * El archivo que lo incluye define $nj_panel con href y children.
 */
if (!isset($nj_panel)) {
    $nj_panel = array(
        'href' => '',
        'children' => array(),
    );
}
if (empty($nj_panel['children'])) {
    return;
}
$nj_panel_align = isset($nj_panel['align']) ? $nj_panel['align'] : '';
$nj_panel_href = isset($nj_panel['href']) ? $nj_panel['href'] : '';
?>
<div class="nav-panel<?php echo $nj_panel_align !== '' ? ' nav-panel--' . $nj_panel_align : ''; ?>">
  <a class="nav-panel__all" href="<?php echo esc_url($nj_panel_href); ?>">Ver todo</a>
  <?php foreach ($nj_panel['children'] as $child) : ?>
    <a class="nav-panel__link" href="<?php echo esc_url($child['href']); ?>"><?php nj_e($child['label']); ?></a>
  <?php endforeach; ?>
</div>
