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

    add_action('wp_ajax_rgn_customer_wishlist_get_data', [$this, 'get']);
    add_action('wp_ajax_nopriv_rgn_customer_wishlist_get_data', [$this, 'get']);
  }

  public function get()
  {
    // Prevent any caching layer from storing the response
    nocache_headers();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);

    check_ajax_referer('rgn_get_customer_wishlist_security', 'security');
    $singleProduct = new WishlistProductPage();

    $productID = $_POST['product-id'] ?? 0;

    if (!$productID) {
      wp_send_json_error('Invalid product ID');
    }

    $product = wc_get_product($productID);

    if ($product->is_type('variable')) {
      $addedIds =  $singleProduct->getAddedVariationIDs($productID);
      wp_send_json_success($addedIds);
    }

    if ($product->is_type('simple')) {
      $isAdded  = $singleProduct->isAdded($productID);
      wp_send_json_success($isAdded);
    }

    wp_send_json_success($product->get_id());
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
