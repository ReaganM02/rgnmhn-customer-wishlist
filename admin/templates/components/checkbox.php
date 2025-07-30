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
 * type: string
 * checked?: boolean
 * } $variables The data passed to this template.
 */
?>
<div class="rgn-flex rgn-justify-between rgn-py-4">
  <div>
    <label for="<?php echo esc_attr($variables['id']) ?>" class="rgn-text-base rgn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
  </div>
  <div>
    <input
      type="<?php echo esc_attr($variables['type']) ?>"
      id="<?php echo esc_attr($variables['id']) ?>"
      name="<?php echo esc_attr($variables['id']) ?>"
      value="<?php echo esc_attr($variables['value']) ?>"
      <?php echo isset($variables['checked']) ? 'checked="check"' : '' ?> />
  </div>
</div>