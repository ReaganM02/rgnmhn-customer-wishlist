<?php

namespace Src\Frontend\MyAccount;

use Src\Models\WishlistModel;
use WC_Product_Variation;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class WishlistAjaxController
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('wp_ajax_rgn_wishlist_add_to_cart', [$this, 'addToCart']);

    add_action('wp_ajax_rgn_wishlist_delete_item', [$this, 'delete']);
  }

  public function delete()
  {
    check_ajax_referer('rgn_remove_from_wishlist_security', 'security');

    $productID = $_POST['product-id'] ?? 0;

    $wishlist = new WishlistModel();

    $deleted = $wishlist->delete($productID, get_current_user_id());
    if ($deleted) {
      $frontend = new WishlistAccountEndpoint();
      ob_start();
      $content = $frontend->wishlistContent();
      $content = ob_get_clean();
      wp_send_json_success($content);
    }
  }

  public function addToCart()
  {
    check_ajax_referer('rgn_add_to_cart_security', 'security');

    $productID = $_POST['product-id'] ?? 0;

    if (empty($productID)) {
      wp_send_json_error('Failed to add item to the cart.');
    }
    $product = wc_get_product($productID);

    if (!$product->is_purchasable() || (!$product->is_in_stock() && !$product->backorders_allowed())) {
      wp_send_json_error('Product is not purchasable.');
    }

    if ($product->is_type('variation') && $product instanceof WC_Product_Variation) {
      $variationID = $productID;
      $parentID = $product->get_parent_id();
      $attributes = $product->get_variation_attributes();
      WC()->cart->add_to_cart($parentID, 1, $variationID, $attributes);
    } else {
      WC()->cart->add_to_cart($productID, 1);
    }

    wp_send_json_success(200);
  }
}
