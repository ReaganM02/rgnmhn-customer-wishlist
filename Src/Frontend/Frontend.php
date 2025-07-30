<?php

namespace Src\Frontend;

use Src\Models\WishlistModel;
use WC_Data_Store;
use WC_Product;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class Frontend
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('wp_enqueue_scripts', [$this, 'assets']);
    add_action('woocommerce_after_add_to_cart_form', [$this, 'displayWishlistAfterAddToCartForm'], 35);
    add_action('woocommerce_get_stock_html', [$this, 'displayWishListAfterOutOfStocks'], 10, 2);
    add_action('wp_login', [$this, 'mergeWishlistOnLogIn'], 10, 2);
  }

  public function displayWishlistAfterAddToCartForm()
  {
    $this->renderWishlistHTML();
  }

  /**
   * @param string $html
   * @param Wc_Product $product
   */
  public function displayWishListAfterOutOfStocks(string $html, WC_Product $product)
  {
    if (!$product->is_in_stock()) {
      ob_start();
      $this->renderWishlistHTML();
      $wishlistHTML = ob_get_clean();
      return $html . $wishlistHTML;
    }
    return $html;
  }

  private function renderWishlistHTML()
  {
    global $product;

    if (! is_a($product, 'WC_Product')) {
      return;
    }

    $wishlist = new WishlistModel();

    $identifier = wishListIdentifier();
    $isProductInWishlist = $wishlist->isProductInWishlist($product->get_id(), $identifier);

    $settings = get_option(RGN_CUSTOMER_WISHLIST_SETTINGS, []);

    // Check if guest user is allowed to add wishlist
    if ($settings['allow-none-logged-in-user'] !== 'yes') {
      return;
    }

    // Check if product is already in the wishlist
    if ($isProductInWishlist) {
      ob_start();
      require_once RGN_CUSTOMER_WISHLIST_PATH . 'templates/already-added.php';
      echo ob_get_clean();
    } else {
      ob_start();
      require_once RGN_CUSTOMER_WISHLIST_PATH . 'templates/wishlist.php';
      echo ob_get_clean();
    }
  }

  public function mergeWishlistOnLogIn($userLogIn, $user)
  {
    if (isset($_COOKIE[RGN_WISHLIST_COOKIE])) {
      $token = sanitize_text_field($_COOKIE[RGN_WISHLIST_COOKIE]);
      $wishlist = new WishlistModel();
      $wishlist->mergeGuestWishlist($token, $user->ID);

      // Clear the cookie
      setcookie(RGN_WISHLIST_COOKIE, '', time() - 3600, '/');
    }
  }

  public function assets()
  {
    if (is_product()) {
      wp_enqueue_script('rgn-customer-wishlist', $this->getAssetFile('js', 'rgn-customer-wishlist.js'), ['jquery', 'wc-add-to-cart-variation'], RGN_CUSTOMER_WISHLIST_VERSION, true);
      wp_localize_script('rgn-customer-wishlist', 'rgn_add_customer_wishlist', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('rgn_add_customer_wishlist_security')
      ]);
    }
  }

  private function getAssetFile(string $fileType, string $file)
  {
    return RGN_CUSTOMER_WISHLIST_URL . 'assets/' . $fileType . '/' . $file;
  }
}
