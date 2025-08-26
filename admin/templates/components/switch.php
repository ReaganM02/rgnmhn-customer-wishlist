<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<div>
  <input
    type="checkbox"
    id="<?php echo esc_attr($variables['id']) ?>"
    name="<?php echo esc_attr($variables['name']) ?>"
    value="<?php echo esc_attr($variables['value']) ?>" class="!rgn-hidden"
    <?php echo $variables['checked'] ? 'checked="true"' : '' ?> />
  <label
    for="<?php echo esc_attr($variables['id']) ?>"
    class="rgn-cursor-pointer rgn-flex rgn-gap-4 rgn-items-center rgn-select-none">
    <span class="rgn-block rgn-w-8 rgn-h-4 rgn-switch rgn-relative rgn-rounded rgn-transition"></span>
    <span class="rgn-text-base rgn-text-zinc-700">
      <?php echo esc_html($variables['label']) ?>
    </span>
  </label>
</div>