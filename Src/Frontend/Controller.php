<?php

namespace Src\Frontend;

use Src\Models\WishlistModel;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class Controller
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
    $productID = isset($_POST['productID']) && !empty($_POST['productID']) ? sanitize_text_field($_POST['productID']) : 0;

    $identifier = wishListIdentifier();

    $wishlist = new WishlistModel();

    $added = $wishlist->add($productID, $identifier);

    wp_send_json_success($added);
  }
}
