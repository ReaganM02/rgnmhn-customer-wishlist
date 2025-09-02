<?php

/**
 * WishlistProductPageController
 *
 * AJAX controller for the single product page Wishlist UI.
 *
 * Actions:
 * - rgn_customer_wishlist_get_data (GET wishlist state for a product)
 * - rgn_add_customer_wishlist     (ADD product to wishlist)
 *
 * Both actions are available to logged-in and guest users.
 * Nonces are required and must match the ones localized on the frontend.
 *
 * Caching:
 * - The `get()` handler explicitly sends no-cache headers to avoid stale UI
 *   when a page-level cache is present. (admin-ajax is typically uncached by
 *   caching plugins, but the headers here provide extra protection.)
 *
 * @package Src\Frontend\SingleProduct
 * @since   1.0.0
 */

namespace ReaganMahinay\RGNCustomerWishlist\Frontend\SingleProduct;

use ReaganMahinay\RGNCustomerWishlist\Models\WishlistModel;
use ReaganMahinay\RGNCustomerWishlist\Frontend\SingleProduct\WishlistProductPage;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class WishlistProductPageController
{
  /**
   * Bootstrap hooks.
   */
  public function __construct()
  {
    $this->hooks();
  }

  /**
   * Register AJAX actions for both logged-in and guests.
   *
   * @return void
   */
  public function hooks()
  {
    // Add to wishlist
    add_action('wp_ajax_rgnmhn_add_customer_wishlist', [$this, 'add']);
    add_action('wp_ajax_nopriv_rgnmhn_add_customer_wishlist', [$this, 'add']);

    // Get wishlist state (for a given product)
    add_action('wp_ajax_rgnmhn_customer_wishlist_get_data', [$this, 'get']);
    add_action('wp_ajax_nopriv_rgnmhn_customer_wishlist_get_data', [$this, 'get']);
  }

  /**
   * AJAX: Get wishlist state for the current product.
   *
   * Security:
   * - Nonce: 'rgnmhn_get_customer_wishlist_security' (sent as 'security')
   *
   * Caching:
   * - Sends no-cache headers to avoid stale responses behind page caches.
   *
   * Response:
   * - variable product: array of added variation IDs
   * - simple product:   yes|no strings (is added)
   * - otherwise:        fallback with product ID (or error)
   *
   * @return void Sends JSON and exits.
   */
  public function get()
  {
    // Prevent any caching layer from storing the response
    nocache_headers();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);

    check_ajax_referer('rgnmhn_get_customer_wishlist_security', 'security');

    // Sanitize product ID (unslash, then cast to int)
    $productID = isset($_POST['product-id']) ? absint(wp_unslash($_POST['product-id'])) : 0;

    if (!$productID) {
      wp_send_json_error(__('Invalid product ID.', 'rgnmhn-customer-wishlist'));
    }

    $product = wc_get_product($productID);

    if (!$product) {
      wp_send_json_error(__('Product not found.', 'rgnmhn-customer-wishlist'));
    }

    $singleProduct = new WishlistProductPage();

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

  /**
   * AJAX: Add a product (any type) to the wishlist for the current identifier.
   *
   * Security:
   * - Nonce: 'rgnmhn_add_customer_wishlist_security' (sent as 'security')
   * - Guests: allowed (uses a custom identifier; ensure the helper is safe)
   *
   * Behavior:
   * - If already in wishlist → error
   * - If success → returns product ID
   *
   * @return void Sends JSON and exits.
   */
  public function add()
  {
    check_ajax_referer('rgnmhn_add_customer_wishlist_security', 'security');

    $productID = isset($_POST['product-id']) ? absint(wp_unslash($_POST['product-id'])) : 0;

    if (!$productID) {
      wp_send_json_error(__('Invalid product ID.', 'rgnmhn-customer-wishlist'));
    }

    $identifier = wishListIdentifier();

    $wishlist = new WishlistModel();

    // Prevent duplicates
    $alreadyAdded = $wishlist->isProductInWishlist($productID, $identifier);

    if ($alreadyAdded) {
      wp_send_json_error(__('Product is already in the wishlist.', 'rgnmhn-customer-wishlist'));
    }

    $added = $wishlist->add($productID, $identifier);

    if ($added) {
      wp_send_json_success($productID);
    }

    wp_send_json_error(__('Failed to add wishlist.', 'rgnmhn-customer-wishlist'));
  }
}
