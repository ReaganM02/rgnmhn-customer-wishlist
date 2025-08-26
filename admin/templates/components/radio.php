<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
/**
 * Template for displaying a component.
 *
 * @var array{
 * id: string,
 * name: string,
 * value: mixed,
 * label: string,
 * type: string,
 * options: array
 * default: string
 * } $variables The data passed to this template.
 */
?>
<div class="">
  <div>
    <label for="<?php echo esc_attr($variables['id']) ?>" class="rgn-text-base rgn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
  </div>
  <div class="rgn-flex rgn-gap-2">
    <?php foreach ($variables['options'] as $key => $value): ?>
      <div class="rgn-shadow rgn-border">
        <input
          type="radio"
          name="<?php echo esc_attr($variables['id']) ?>"
          id="<?php echo esc_attr($key) ?>"
          class="!rgn-hidden"
          <?php echo $variables['default'] === $key ? 'checked="checked"' : '' ?>
          value="<?php echo esc_attr($key) ?>" />
        <label for="<?php echo esc_attr($key) ?>" class="rgn-cursor-pointer rgn-w-8 rgn-block rgn-p-2 peer-checked:rgn-bg-zinc-200"><?php echo sanitizeSvg($value) ?></label>
      </div>
    <?php endforeach; ?>
  </div>
</div>