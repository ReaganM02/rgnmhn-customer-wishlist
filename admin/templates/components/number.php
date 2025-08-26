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
 * } $variables The data passed to this template.
 */
?>
<div class="rgn-block">
  <div>
    <label for="<?php echo esc_attr($variables['id']) ?>" class="rgn-text-base rgn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
  </div>
  <div class="rgn-mt-2">
    <input
      type="number"
      id="<?php echo esc_attr($variables['id']) ?>"
      name="<?php echo esc_attr($variables['id']) ?>"
      value="<?php echo esc_attr($variables['value']) ?>"
      class="rgn-border !rgn-border-zinc-300 rgn-w-full rgn-h-12 !rgn-bg-zinc-50 !rgn-text-zinc-600" />
  </div>
</div>