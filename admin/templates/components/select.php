<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<div>
  <label for="<?php echo esc_attr($variables['id']) ?>" class="rgn-text-base rgn-text-zinc-600 rgn-block"><?php echo esc_html($variables['label']) ?></label>
  <select name="<?php echo esc_attr($variables['name']) ?>" id="<?php echo esc_attr($variables['id']) ?>" class="rgn-h-12 rgn-w-full">
    <?php foreach ($variables['options'] as $value => $label): ?>
      <option value="<?php echo esc_attr($value) ?>" <?php echo $value === $variables['selected'] ? 'selected' : '' ?>><?php echo esc_html($label) ?></option>
    <?php endforeach; ?>
  </select>
</div>