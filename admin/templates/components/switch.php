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
    value="<?php echo esc_attr($variables['value']) ?>" class="!rgnmhn-hidden"
    <?php echo $variables['checked'] ? 'checked="true"' : '' ?> />
  <label
    for="<?php echo esc_attr($variables['id']) ?>"
    class="rgnmhn-cursor-pointer rgnmhn-flex rgnmhn-gap-4 rgnmhn-items-center rgnmhn-select-none rgnmhn-w-max">
    <span class="rgnmhn-block rgnmhn-w-8 rgnmhn-h-4 rgnmhn-switch rgnmhn-relative rgnmhn-rounded rgnmhn-transition"></span>
    <span class="rgnmhn-text-base rgnmhn-text-zinc-700">
      <?php echo esc_html($variables['label']) ?>
    </span>
  </label>
</div>