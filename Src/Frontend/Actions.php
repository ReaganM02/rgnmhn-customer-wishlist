<?php

namespace Src\Frontend;

use Src\Models\WishlistModel;
use Src\MyAccountOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class Actions
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('wp_login', [$this, 'mergeWishlistOnLogIn'], 10, 2);

    $settings = get_option(MyAccountOptions::optionKey(), []);
    $menuSlug = $settings['menu-slug'] ?? 'my-wishlist';

    add_rewrite_endpoint($menuSlug, EP_ROOT | EP_PAGES);

    add_action('update_option_' . MyAccountOptions::optionKey(), [$this, 'onUpdateMyAccountMenuSlug'], 10, 2);
  }

  public function onUpdateMyAccountMenuSlug(array $oldValue, array $newValue)
  {
    if (isset($oldValue['menu-slug']) && isset($newValue['menu-slug'])) {
      if ($oldValue['menu-slug'] !== $newValue['menu-slug']) {
        add_rewrite_endpoint($newValue['menu-slug'], EP_ROOT | EP_PAGES);
        flush_rewrite_rules();
      }
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
}
