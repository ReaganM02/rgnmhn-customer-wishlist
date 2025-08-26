<?php

/**
 * Plugin Name:       RGN Product Customer Wishlist
 * Description:      Test
 * Version:           1.0.0
 * Author:            Reagan Mahinay
 * Author URI:        https://github.com/ReaganM02
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       rgn-customer-wishlist
 * Requires at least: 5.2
 * Requires PHP: 7.0
 * Domain Path:       /languages
 * Requires Plugins: woocommerce
 */

use Src\InitializePlugin;
use Src\PluginAction;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

define('RGN_CUSTOMER_WISHLIST_VERSION', '1.0.0');
define('RGN_CUSTOMER_WISHLIST_PATH', plugin_dir_path(__FILE__));
define('RGN_CUSTOMER_WISHLIST_URL', plugin_dir_url(__FILE__));
define('RGN_CUSTOMER_WISHLIST_TABLE_NAME', 'rgn_customer_waitlist');
define('RGN_CUSTOMER_WISHLIST_SETTINGS', 'rgn_customer_wishlist_settings');
define('RGN_CUSTOMER_WISHLIST_MY_ACCOUNT_SETTINGS', 'rgn_customer_wishlist_my_account_settings');
define('RGN_WISHLIST_COOKIE', 'rgn_wishlist');

// Load composer
require_once RGN_CUSTOMER_WISHLIST_PATH . 'vendor/autoload.php';


require_once RGN_CUSTOMER_WISHLIST_PATH . 'includes/helpers.php';

add_action('plugins_loaded', [InitializePlugin::class, 'run']);
register_activation_hook(__FILE__, [PluginAction::class, 'activate']);
register_deactivation_hook(__FILE__, [PluginAction::class, 'deactivate']);
