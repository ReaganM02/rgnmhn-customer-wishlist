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
  <div class="rgnmhn-mb-2">
    <label for="<?php echo esc_attr($variables['id']) ?>" class="rgnmhn-text-base rgnmhn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
  </div>
  <div class="rgnmhn-flex rgnmhn-gap-4">
    <div>
      <input
        type="text"
        name="<?php echo esc_attr($variables['id']) ?>"
        value="<?php echo esc_attr($variables['value']) ?>"
        class="rgnmhn-wishlist-color-picker"
        data-alpha="true" />
    </div>
  </div>
</div>