<?php

/**
 * Actions
 *
 * Front-end lifecycle hooks for the Wishlist feature:
 * - Merges a guest wishlist into the user's account on login.
 * - Registers the My Account endpoint (based on the saved slug).
 * - Reacts to slug changes by adding the new endpoint and flushing rules.
 *
 *
 * @package Src\Frontend
 * @since   1.0.0
 */


namespace Src\Frontend;

use Src\Models\WishlistModel;
use Src\MyAccountOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class Actions
{
  /**
   * Bootstrap hooks on construct.
   */
  public function __construct()
  {
    $this->hooks();
  }

  /**
   * Register WordPress hooks for:
   * - Login merge (guest -> user wishlist)
   * - Registering rewrite endpoint on 'init'
   * - Responding to slug updates (option change)
   *
   * @return void
   */
  public function hooks()
  {
    // When a user logs in, merge any guest wishlist into their account.
    add_action('wp_login', [$this, 'mergeWishlistOnLogIn'], 10, 2);

    // When the My Account settings option changes, check if the slug changed.
    // If it did, add the new endpoint and flush the rules (once).
    add_action('update_option_' . MyAccountOptions::optionKey(), [$this, 'onUpdateMyAccountMenuSlug'], 10, 2);
  }

  /**
   * React when My Account settings change. If the menu slug changed,
   * register the new endpoint and flush rewrite rules to activate it.
   *
   * @param array $oldValue Previous settings array.
   * @param array $newValue New settings array.
   * @return void
   */
  public function onUpdateMyAccountMenuSlug(array $oldValue, array $newValue)
  {
    if (isset($oldValue['menu-slug']) && isset($newValue['menu-slug'])) {
      if ($oldValue['menu-slug'] !== $newValue['menu-slug']) {
        add_rewrite_endpoint($newValue['menu-slug'], EP_ROOT | EP_PAGES);

        /**
         * Flushing is required to make the new endpoint active.
         * This is intentionally done only when the slug actually changes,
         * as flush_rewrite_rules() is expensive.
         */
        flush_rewrite_rules();
      }
    }
  }

  /**
   * On successful login, merge any guest wishlist (cookie-based) into the user account.
   *
   * Hook: wp_login
   * @param string  $user_login The user's login name.
   * @param \WP_User $user       The WP_User object.
   * @return void
   */
  public function mergeWishlistOnLogIn($userLogIn, $user)
  {
    // Only proceed if the guest token cookie exists in this browser.
    if (isset($_COOKIE[RGN_WISHLIST_COOKIE])) {
      $token = sanitize_text_field(wp_unslash($_COOKIE[RGN_WISHLIST_COOKIE]));

      $wishlist = new WishlistModel();
      $wishlist->mergeGuestWishlist($token, $user->ID);

      // Clear the cookie after merging to avoid duplicate merges later.
      // Use secure/httponly flags where possible.
      $cookie_args = [
        'expires'  => time() - HOUR_IN_SECONDS,
        'path'     => defined('COOKIEPATH') ? COOKIEPATH : '/',
        'domain'   => defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '',
        'secure'   => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax',
      ];

      // PHP 7.3+ array signature:
      @setcookie(RGN_WISHLIST_COOKIE, '', $cookie_args);
    }
  }
}
