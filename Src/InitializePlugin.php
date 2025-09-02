<?php

/**
 * Bootstrap: wires up admin UI and front-end controllers for the wishlist plugin.
 *
 * This file defines the InitializePlugin class, a small bootstrapper that:
 *  - Loads admin screens (settings/forms) only in wp-admin
 *  - Defers front-end controller registration to the 'init' hook
 *  - Requires a shared hooks.php file for global actions/filters
 *  - Listens to option updates and refreshes cached settings
 *
 * @since   1.0.0
 */

namespace ReaganMahinay\RGNCustomerWishlist;

use ReaganMahinay\RGNCustomerWishlist\Admin\Admin;
use ReaganMahinay\RGNCustomerWishlist\Admin\FormController;
use ReaganMahinay\RGNCustomerWishlist\Frontend\Actions;
use ReaganMahinay\RGNCustomerWishlist\Frontend\MyAccount\WishlistAccountEndpoint;
use ReaganMahinay\RGNCustomerWishlist\Frontend\MyAccount\WishlistAjaxController;
use ReaganMahinay\RGNCustomerWishlist\Frontend\SingleProduct\WishlistProductPage;
use ReaganMahinay\RGNCustomerWishlist\Frontend\SingleProduct\WishlistProductPageController;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class InitializePlugin
{
  public static function run()
  {
    if (is_admin()) {
      new FormController();
      new Admin();
    }

    add_action('init', function () {
      // Single product page
      new WishlistProductPage();
      new WishlistProductPageController();

      // My Account
      new WishlistAccountEndpoint();
      new WishlistAjaxController();

      // Actions
      new Actions();
    });

    /**
     * In case there are settings update, ensure to return the fresh data.
     */
    add_action('updated_option', function ($option, $old, $new) {
      if ($option === ProductOptions::optionKey()) {
        ProductOptions::refresh();
      }
      if ($option === MyAccountOptions::optionKey()) {
        MyAccountOptions::refresh();
      }
      if ($option === GeneralSettingOptions::optionKey()) {
        GeneralSettingOptions::refresh();
      }
    }, 10, 3);
  }
}
