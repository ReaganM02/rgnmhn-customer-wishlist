<?php

namespace ReaganMahinay\RGNCustomerWishlist;

use ReaganMahinay\RGNCustomerWishlist\ProductOptions;
use ReaganMahinay\RGNCustomerWishlist\MyAccountOptions;
use ReaganMahinay\RGNCustomerWishlist\GeneralSettingOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Plugin lifecycle actions (activation/deactivation & setup helpers).
 * 
 * This class centralizes tasks that should run when the plugin is activated
 * or deactivated, including:
 * - Creating the wishlist database table via dbDelta()
 * - Seeding default settings for Product and My Account options
 * - Registering & flushing the My Account wishlist endpoint
 * - Cleaning up data on deactivation (drops table & deletes options)
 * 
 * Security & Safety:
 * - All option keys/values are sanitized with sanitize_text_field().
 * - Deactivation is DESTRUCTIVE: it drops the wishlist table and deletes options.
 * @since 1.0.0
 */
class PluginAction
{
  /**
   * Run on plugin activation.
   *
   * Steps:
   * 1) Create (or update) the wishlist table using dbDelta().
   * 2) Seed default Product settings in the options table.
   * 3) Seed default "My Account" settings in the options table.
   * 4) Register the My Account endpoint and flush rewrite rules.
   *
   *
   * @return void
   * @since  1.0.0
   */
  public static function activate()
  {
    self::createWishlistTable();
    self::saveProductSettingsDefault();
    self::saveMyAccountDefaultSettings();
    self::flushMyAccountWishlistEndpoint();
    self::saveGeneralSettingsOptions();
  }

  /**
   * Register the dynamic My Account endpoint and flush permalinks.
   *
   * Uses the slug from MyAccountOptions::getSlug() and attaches it as a
   * rewrite endpoint for both site root and pages. Then flushes rewrite rules
   * so the endpoint is immediately recognized.
   *
   *
   * @return void
   * @since  1.0.0
   */
  public static function flushMyAccountWishlistEndpoint()
  {
    add_rewrite_endpoint(MyAccountOptions::getSlug(), EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
  }



  /**
   * Persist default "My Account" settings to the options table.
   *
   * Reads the field definitions from MyAccountOptions::fields() and stores
   * default values. Each key and value is
   * sanitized before saving.
   *
   * Option key: RGN_CUSTOMER_WISHLIST_MY_ACCOUNT_SETTINGS
   *
   * @return void
   * @since  1.0.0
   */
  private static function saveMyAccountDefaultSettings()
  {
    $fields = MyAccountOptions::fields();
    $sanitizedData = [];

    foreach ($fields as $key => $field) {
      $sanitizedData[$field['name']] = sanitize_text_field($field['value']);
    }
    add_option(MyAccountOptions::optionKey(), $sanitizedData);
  }

  /**
   * Persist default Product settings to the options table.
   *
   * Reads the field definitions from ProductOptions::fields() and stores:
   * - 'default' when provided
   * - 'selected' when type === 'select'
   * - 'value' as a fallback
   *
   * All keys and values are sanitized.
   *
   * Option key: RGNMHN_CUSTOMER_WISHLIST_SETTINGS
   *
   * @return void
   * @since  1.0.0
   * @see    \Src\ProductOptions::fields()
   */
  private static function saveProductSettingsDefault()
  {
    $fields = ProductOptions::fields();

    $sanitizedData = [];

    foreach ($fields as $key => $field) {
      $rules = $field['rules'];

      switch ($rules['type']) {
        case 'bool':
          $present = $field['checked'] ? 'yes' : 'no';
          $sanitizedData[$field['name']] = $present;
          break;
        case 'init':
          $sanitizedData[$field['name']] = absint($field['value']);
          break;
        case 'color':
          $sanitizedData[$field['name']] = sanitize_hex_color($field['value']);
          break;
        case 'text':
          if (isset($field['default'])) {
            $sanitizedData[$field['name']] = sanitize_text_field($field['default']);
          } else if (isset($field['selected'])) {
            $sanitizedData[$field['name']] = sanitize_text_field($field['selected']);
          } else {
            $sanitizedData[$field['name']]  = sanitize_text_field($field['value']);
          }
          break;
        default:
          $sanitizedData[$field['name']] = sanitize_text_field($field['value']);
          break;
      }
    }
    add_option(ProductOptions::optionKey(), $sanitizedData);
  }

  /**
   * Seed the default value for the "Delete all data on uninstall" setting.
   * 
   * - Stores the canonical key from GeneralSettingOptions.
   * - Sets autoload to 'no' so this flag does not bloat the `alloptions` cache.
   */
  private static function saveGeneralSettingsOptions()
  {
    add_option(GeneralSettingOptions::optionKey(), 'no', '', 'no');
  }

  /**
   * Plugin deactivation callback.
   *
   * Flushes WordPress rewrite rules to remove any custom rewrite endpoints
   * (e.g., the My Account wishlist endpoint) that this plugin registered.
   *
   * @return void
   * @since  1.0.0
   */
  public static function deactivate()
  {
    flush_rewrite_rules();
  }

  private static function createWishlistTable()
  {
    global $wpdb;

    $tableName = $wpdb->prefix . RGNMHN_CUSTOMER_WISHLIST_TABLE_NAME;

    $charsetCollate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE $tableName (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        product_id bigint(20) UNSIGNED NOT NULL,
        date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        token varchar(64) NULL,
        user_id bigint(20) NULL,
        PRIMARY KEY (id),
        KEY product_id (product_id),
        KEY user_id (user_id)
    ) $charsetCollate;";

    dbDelta($sql);
  }
}
