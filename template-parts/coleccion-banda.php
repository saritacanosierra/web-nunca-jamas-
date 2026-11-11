<?php
/**
 * Frase que corre en una colección. Quien incluye este archivo define $nj_coleccion_banda.
 */
$nj_coleccion_banda = isset($nj_coleccion_banda) ? trim((string) $nj_coleccion_banda) : '';
if ($nj_coleccion_banda === '') {
    return;
}
$nj_coleccion_banda_estilo = isset($nj_coleccion_banda_estilo) ? trim((string) $nj_coleccion_banda_estilo) : '';
?>
<div class="coleccion-banda"<?php echo $nj_coleccion_banda_estilo !== '' ? ' style="' . esc_attr($nj_coleccion_banda_estilo) . '"' : ''; ?>>
  <div class="coleccion-banda__track">
    <div>
      <?php for ($nj_banda_vez = 0; $nj_banda_vez < 6; $nj_banda_vez++) : ?>
        <span><?php nj_e($nj_coleccion_banda); ?></span>
      <?php endfor; ?>
    </div>
    <div aria-hidden="true">
      <?php for ($nj_banda_vez = 0; $nj_banda_vez < 6; $nj_banda_vez++) : ?>
        <span><?php nj_e($nj_coleccion_banda); ?></span>
      <?php endfor; ?>
    </div>
  </div>
</div>
