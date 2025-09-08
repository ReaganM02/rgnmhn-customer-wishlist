<?php

use ReaganMahinay\RGNCustomerWishlist\GeneralSettingOptions;
use ReaganMahinay\RGNCustomerWishlist\MyAccountOptions;
use ReaganMahinay\RGNCustomerWishlist\ProductOptions;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once __DIR__ . '/Src/GeneralSettingOptions.php';
require_once __DIR__ . '/Src/MyAccountOptions.php';
require_once __DIR__ . '/Src/ProductOptions.php';


if ( GeneralSettingOptions::allowDeleteAllDataOnUninstall() === 'yes' ) {
	delete_option( ProductOptions::optionKey() );
	delete_option( MyAccountOptions::optionKey() );
	delete_option( GeneralSettingOptions::optionKey() );

	global $wpdb;
	$tableName = $wpdb->prefix . 'rgnmhn_customer_waitlist';

  // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS `{$tableName}`" );
}
