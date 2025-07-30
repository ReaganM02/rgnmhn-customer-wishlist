<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<div>
  <button
    type="button"
    id="rgn-add-to-wishlist"
    data-product-id="<?php echo esc_attr($product->get_id()) ?>"
    data-variation-id="0">Add To Wishlist</button>
</div>