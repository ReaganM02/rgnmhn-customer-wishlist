<?php

namespace Src\Frontend\SingleProduct;

use Src\Models\WishlistModel;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class WishlistProductPageController
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('wp_ajax_rgn_add_customer_wishlist', [$this, 'add']);
    add_action('wp_ajax_nopriv_rgn_add_customer_wishlist', [$this, 'add']);
  }

  public function add()
  {
    check_ajax_referer('rgn_add_customer_wishlist_security', 'security');

    $productID = sanitize_text_field($_POST['product-id']) ?? 0;

    $identifier = wishListIdentifier();

    $wishlist = new WishlistModel();

    $alreadyAdded = $wishlist->isProductInWishlist($productID, $identifier);

    if ($alreadyAdded) {
      wp_send_json_error('Product is already in the wishlist.');
    }

    $added = $wishlist->add($productID, $identifier);
    if ($added) {
      wp_send_json_success($productID);
    }
    error_log('Failed to add wishlist');
    wp_send_json_error('Failed to add wishlist.');
  }
}
