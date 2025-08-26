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
  <div class="rgn-block">
    <div>
      <label for="<?php echo esc_attr($variables['id']) ?>" class="rgn-text-base rgn-text-zinc-600"><?php echo esc_html($variables['label']) ?></label>
    </div>
    <div>
      <input
        type="text"
        id="<?php echo esc_attr($variables['id']) ?>"
        name="<?php echo esc_attr($variables['id']) ?>"
        value="<?php echo esc_attr($variables['value']) ?>"
        class="rgn-w-full rgn-border !rgn-border-zinc-300 rgn-h-12 !rgn-text-zinc-600 !rgn-bg-zinc-50" />
    </div>
  </div>
</div>