<?php

use Src\GeneralSettingOptions;
use Src\MyAccountOptions;
use Src\ProductOptions;

if (! defined('WP_UNINSTALL_PLUGIN')) {
  exit;
}

require_once __DIR__ . '/Src/GeneralSettingOptions.php';
require_once __DIR__ . '/Src/MyAccountOptions.php';
require_once __DIR__ . '/Src/ProductOptions.php';


if (GeneralSettingOptions::allowDeleteAllDataOnUninstall() === 'yes') {
  delete_option(ProductOptions::optionKey());
  delete_option(MyAccountOptions::optionKey());
  delete_option(GeneralSettingOptions::optionKey());

  global $wpdb;
  $tableName = $wpdb->prefix . 'rgn_customer_waitlist';
  $wpdb->query("DROP TABLE IF EXISTS `{$tableName}`");
}
