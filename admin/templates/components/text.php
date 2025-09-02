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
<div>
  <div class="rgnmhn-block">
    <div>
      <label for="<?php echo esc_attr($variables['id']) ?>" class="rgnmhn-text-base rgnmhn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
    </div>
    <div>
      <input
        type="text"
        id="<?php echo esc_attr($variables['id']) ?>"
        name="<?php echo esc_attr($variables['id']) ?>"
        value="<?php echo esc_attr($variables['value']) ?>"
        class="rgnmhn-w-full rgnmhn-border !rgnmhn-border-zinc-300 rgnmhn-h-12 !rgnmhn-text-zinc-600 !rgnmhn-bg-zinc-50" />
    </div>
  </div>
</div>