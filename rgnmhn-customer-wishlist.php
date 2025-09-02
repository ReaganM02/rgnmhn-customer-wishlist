<?php

/**
 * Plugin Name:       Simple Customer Wishlist by RGNMHN
 * Description:       Give your customers the ability to save products to a personalized wishlist. Fully customizable, lightweight, and optimized for speed. Translation-ready, compatible with tools like Loco Translate.
 * Version:           1.0.0
 * Author:            Reagan Mahinay
 * Author URI:        https://github.com/ReaganM02
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rgnmhn-customer-wishlist
 * Requires at least: 6.1
 * Requires PHP: 7.4
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 */

use ReaganMahinay\RGNCustomerWishlist\InitializePlugin;
use ReaganMahinay\RGNCustomerWishlist\PluginAction;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

define('RGNMHN_CUSTOMER_WISHLIST_VERSION', '1.0.0');
define('RGNMHN_CUSTOMER_WISHLIST_PATH', plugin_dir_path(__FILE__));
define('RGNMHN_CUSTOMER_WISHLIST_URL', plugin_dir_url(__FILE__));
define('RGNMHN_CUSTOMER_WISHLIST_TABLE_NAME', 'rgnmhn_customer_waitlist');
define('RGNMHN_WISHLIST_COOKIE', 'rgnmhn_wishlist');

// Load composer
require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'vendor/autoload.php';

// Helpers
require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'includes/rgnmhn-customer-wishlist-helpers.php';

add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
  $settings_url = admin_url('admin.php?page=rgnmhn-customer-wishlist');
  $settings_link = '<a href="' . esc_url($settings_url) . '">' . esc_html__('Settings', 'rgnmhn-customer-wishlist') . '</a>';
  array_unshift($links, $settings_link);
  return $links;
});




add_action('plugins_loaded', function () {
  InitializePlugin::run();
});
register_activation_hook(__FILE__, [PluginAction::class, 'activate']);
register_deactivation_hook(__FILE__, [PluginAction::class, 'deactivate']);
