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
<div class="rgnmhn-block">
  <div>
    <label for="<?php echo esc_attr($variables['id']) ?>" class="rgnmhn-text-base rgnmhn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
  </div>
  <div class="rgnmhn-mt-2">
    <input
      type="number"
      id="<?php echo esc_attr($variables['id']) ?>"
      name="<?php echo esc_attr($variables['id']) ?>"
      value="<?php echo esc_attr($variables['value']) ?>"
      class="rgnmhn-border !rgnmhn-border-zinc-300 rgnmhn-w-full rgnmhn-h-12 !rgnmhn-bg-zinc-50 !rgnmhn-text-zinc-600" />
  </div>
</div>