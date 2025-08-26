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
echo '<pre>';
// print_r(wp_get_global_settings()['color']['palette']['default']);
echo '</pre>';

$themeColorPalette = wp_get_global_settings()['color']['palette']['default'];
?>
<div>
  <div class="rgn-mb-2">
    <label for="<?php echo esc_attr($variables['id']) ?>" class="rgn-text-base rgn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
  </div>
  <div class="rgn-flex rgn-gap-4">
    <div>
      <input
        type="text"
        name="<?php echo esc_attr($variables['id']) ?>"
        value="<?php echo esc_attr($variables['value']) ?>"
        class="rgn-wishlist-color-picker"
        data-alpha="true" />
    </div>
  </div>
</div>