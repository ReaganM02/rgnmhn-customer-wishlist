<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

?>
<template id="rgn-template-added-wishlist">
  <div>
    <?php
    /**
     * This hook should only be used to output safe HTML. Developers are responsible for escaping and sanitizing their own output.
     *
     * @since 1.0.0
     */
    do_action('rgn_wishlist_single_product_before_added')
    ?>
    <a href="/my-account/<?php echo esc_attr($data['slug']) ?>" class="rgn-added-to-wishlist">
      <?php echo esc_html($data['label']) ?>
    </a>
    <?php
    /**
     * This hook should only be used to output safe HTML. Developers are responsible for escaping and sanitizing their own output.
     *
     * @since 1.0.0
     */
    do_action('rgn_wishlist_single_product_after_added')
    ?>
  </div>
</template>