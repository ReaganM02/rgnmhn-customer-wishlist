<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<template id="rgn-template-add-wishlist">
  <div>
    <?php
    /**
     * Fires before the "Add to Wishlist" button on single product template.
     *
     * This hook should only be used to output safe HTML. Developers are responsible for escaping and sanitizing their own output.
     *
     * @since 1.0.0
     */
    do_action('rgn_wishlist_single_product_before_button')
    ?>
    <button class="rgn-add-to-wishlist">
      <span class="rgn-wishlist-icon">
        <?php echo sanitizeSvg($data['icon']); ?>
      </span>
      <?php if (!empty($data['label'])): ?>
        <span class="rgn-add-to-wishlist-label">
          <?php echo esc_html($data['label']) ?>
        </span>
      <?php endif; ?>
    </button>
    <?php
    /**
     * Fires after the "Add to Wishlist" button on single product template.
     *
     * This hook should only be used to output safe HTML. Developers are responsible for escaping and sanitizing their own output.
     *
     * @since 1.0.0
     */
    do_action('rgn_wishlist_single_product_after_button')
    ?>
  </div>
</template>