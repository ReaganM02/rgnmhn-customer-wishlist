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

namespace Src;

use Src\Admin\Admin;
use Src\Admin\FormController;
use Src\Frontend\Actions;
use Src\Frontend\MyAccount\WishlistAccountEndpoint;
use Src\Frontend\MyAccount\WishlistAjaxController;
use Src\Frontend\SingleProduct\WishlistProductPage;
use Src\Frontend\SingleProduct\WishlistProductPageController;

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
    }, 10, 3);
  }
}
