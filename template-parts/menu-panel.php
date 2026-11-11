<?php
/**
 * Panel de un departamento: "Ver todo" y sus subcategorías.
 * El archivo que lo incluye define $nj_panel con href y children.
 */
$nj_panel = isset($nj_panel) && is_array($nj_panel) ? $nj_panel : array(
    'href' => '',
    'children' => array(),
);
if (empty($nj_panel['children'])) {
    return;
}
$nj_panel_align = isset($nj_panel['align']) ? $nj_panel['align'] : '';
$nj_panel_href = isset($nj_panel['href']) ? $nj_panel['href'] : '';
?>
<div class="nav-panel<?php echo $nj_panel_align !== '' ? ' nav-panel--' . $nj_panel_align : ''; ?><?php echo !empty($nj_panel['mosaico']) ? ' nav-panel--colecciones' : ''; ?>">
  <a class="nav-panel__all" href="<?php echo esc_url($nj_panel_href); ?>">Ver todo</a>
  <?php if (!empty($nj_panel['mosaico'])) : ?>
    <div class="nav-colecciones">
      <?php foreach ($nj_panel['children'] as $child) : ?>
        <a class="nav-coleccion is-<?php echo esc_attr(isset($child['spot']) ? $child['spot'] : 'more'); ?><?php echo !empty($child['reciente']) ? ' is-recent' : ''; ?>" href="<?php echo esc_url($child['href']); ?>">
          <?php if (!empty($child['image'])) : ?>
            <img src="<?php echo esc_url($child['image']); ?>" alt="">
          <?php endif; ?>
          <span>
            <?php if (!empty($child['reciente'])) : ?><em>Nueva</em><?php endif; ?>
            <?php nj_e($child['label']); ?>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else : ?>
    <?php foreach ($nj_panel['children'] as $child) : ?>
      <a class="nav-panel__link<?php echo !empty($child['reciente']) ? ' is-recent' : ''; ?>" href="<?php echo esc_url($child['href']); ?>"><?php nj_e($child['label']); ?></a>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
