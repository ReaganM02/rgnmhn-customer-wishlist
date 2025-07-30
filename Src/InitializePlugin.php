<?php

namespace Src;

use Src\Admin\Admin;
use Src\Frontend\Controller;
use Src\Frontend\Frontend;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class InitializePlugin
{
  public static function run()
  {
    require_once RGN_CUSTOMER_WISHLIST_PATH . 'includes/helpers.php';
    if (is_admin()) {
      new Admin();
    }
    add_action('init', function () {
      new Frontend();
      new Controller();
    });
  }
}
