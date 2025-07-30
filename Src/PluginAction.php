<?php

namespace Src;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class PluginAction
{
  public static function activate()
  {
    self::createWishlistTable();
  }

  public static function deactivate()
  {
    global $wpdb;
    $tableName = $wpdb->prefix . RGN_CUSTOMER_WISHLIST_TABLE_NAME;
    $wpdb->query("DROP TABLE IF EXISTS $tableName");

    delete_option(RGN_CUSTOMER_WISHLIST_SETTINGS);
  }

  private static function createWishlistTable()
  {
    global $wpdb;

    $tableName = $wpdb->prefix . RGN_CUSTOMER_WISHLIST_TABLE_NAME;

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
