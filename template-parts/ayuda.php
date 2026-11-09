<?php
$nj_help = isset($nj_help) && is_array($nj_help) ? $nj_help : array();
$nj_help += array(
    'lead' => '',
    'image_url' => '',
    'image_position' => 'center',
    'facts' => array(),
    'steps_title' => '',
    'steps' => array(),
    'note' => '',
    'table' => array(),
    'items' => array(),
);
$nj_help_title = isset($nj_help_title) ? (string) $nj_help_title : '';
$nj_help_positions = array('center');
$nj_help_position = in_array($nj_help['image_position'], $nj_help_positions, true) ? $nj_help['image_position'] : 'center';
?>
<div class="help-split">
  <?php if ($nj_help['image_url'] !== '') : ?>
    <figure class="help-split__photo">
      <img src="<?php echo esc_url($nj_help['image_url']); ?>" alt="<?php echo esc_attr($nj_help_title); ?>" style="object-position: <?php echo esc_attr($nj_help_position); ?>">
    </figure>
  <?php endif; ?>
  <div class="help-split__copy">
    <?php if ($nj_help_title !== '') : ?>
      <h1 class="page-title"><?php echo esc_html($nj_help_title); ?></h1>
    <?php endif; ?>
<?php if ($nj_help['lead'] !== '') : ?>
  <p class="help-lead"><?php echo function_exists('nj_pages_rich') ? nj_pages_rich($nj_help['lead']) : esc_html($nj_help['lead']); ?></p>
<?php endif; ?>
<?php if ($nj_help['facts']) : ?>
  <div class="help-facts">
    <?php foreach ($nj_help['facts'] as $nj_fact) : ?>
      <div>
        <span><?php echo esc_html($nj_fact['label']); ?></span>
        <strong><?php echo esc_html($nj_fact['value']); ?></strong>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php if (count($nj_help['table']) > 1) : ?>
  <table class="size-guide">
    <thead>
      <tr>
        <th><?php echo esc_html($nj_help['table'][0][0]); ?></th>
        <th><?php echo esc_html($nj_help['table'][0][1]); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (array_slice($nj_help['table'], 1) as $nj_row) : ?>
        <tr>
          <td><?php echo esc_html($nj_row[0]); ?></td>
          <td><?php echo esc_html($nj_row[1]); ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<?php if ($nj_help['steps']) : ?>
  <?php if ($nj_help['steps_title'] !== '') : ?>
    <h2 class="help-title"><?php echo esc_html($nj_help['steps_title']); ?></h2>
  <?php endif; ?>
  <ol class="help-steps">
    <?php foreach ($nj_help['steps'] as $nj_step) : ?>
      <li><span><?php echo function_exists('nj_pages_rich') ? nj_pages_rich($nj_step) : esc_html($nj_step); ?></span></li>
    <?php endforeach; ?>
  </ol>
<?php endif; ?>
<?php if ($nj_help['note'] !== '') : ?>
  <p class="help-note"><?php echo function_exists('nj_pages_rich') ? nj_pages_rich($nj_help['note']) : esc_html($nj_help['note']); ?></p>
<?php endif; ?>
<?php if ($nj_help['items']) : ?>
  <div class="faq">
    <?php foreach ($nj_help['items'] as $nj_index => $nj_item) : ?>
      <details<?php echo $nj_index === 0 && !$nj_help['steps'] && !$nj_help['table'] ? ' open' : ''; ?>>
        <summary><?php echo esc_html($nj_item['q']); ?></summary>
        <div class="faq-answer"><p><?php echo function_exists('nj_pages_rich') ? nj_pages_rich($nj_item['a']) : esc_html($nj_item['a']); ?></p></div>
      </details>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
  </div>
</div>
