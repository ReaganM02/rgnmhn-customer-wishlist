<?php

namespace ReaganMahinay\RGNCustomerWishlist\Admin;

use ReaganMahinay\RGNCustomerWishlist\GeneralSettingOptions;
use ReaganMahinay\RGNCustomerWishlist\MyAccountOptions;
use ReaganMahinay\RGNCustomerWishlist\ProductOptions;

/**
 * Class Admin
 *
 * Handles the creation of the WordPress admin menu pages
 * and settings pages for the RGN Customer Wishlist plugin.
 *
 * Responsibilities:
 * - Register main admin menu and submenus
 * - Load assets (CSS/JS) for settings pages
 * - Render HTML templates for General, My Account, and Product settings
 *
 * @package Src\Admin
 * @since 1.0.0
 */
class Admin {

	/**
	 * Constructor.
	 *
	 * Initializes the admin hooks as soon as the class is instantiated.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register WordPress admin hooks for this class.
	 *
	 * Currently attaches the menu creation method to `admin_menu`.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'addMenu' ) );
	}

	/**
	 * Adds the main plugin menu and its submenus in the WordPress admin dashboard.
	 *
	 * Creates:
	 * - Main menu: "Wishlist"
	 * - Submenu: "Product Settings"
	 * - Submenu: "My Account"
	 * - Submenu: "General Settings"
	 *
	 * Also hooks each submenu into `load-{page}` to enqueue assets properly.
	 *
	 * @return void
	 */
	public function addMenu() {
		// Main menu (Wishlist).
		$menu = add_menu_page(
			__( 'RGN Customer Wishlist', 'rgnmhn-customer-wishlist' ),
			__( 'Wishlist', 'rgnmhn-customer-wishlist' ),
			'manage_options',
			'rgnmhn-customer-wishlist',
			array( $this, 'productSettingsHTML' ),
			'dashicons-heart',
			60
		);
		add_action( "load-$menu", array( $this, 'assets' ) );

		// Submenu: Product Settings.
		$submenu = add_submenu_page(
			'rgnmhn-customer-wishlist',
			__( 'Wishlist Product Settings', 'rgnmhn-customer-wishlist' ),
			__( 'Product Settings', 'rgnmhn-customer-wishlist' ),
			'manage_options',
			'rgnmhn-customer-wishlist',
			array( $this, 'productSettingsHTML' )
		);
		add_action( "load-$submenu", array( $this, 'assets' ) );

		// Submenu: My Account Settings.
		$submenu = add_submenu_page(
			'rgnmhn-customer-wishlist',
			__( 'Customer Wishlist My Account Settings', 'rgnmhn-customer-wishlist' ),
			__( 'My Account', 'rgnmhn-customer-wishlist' ),
			'manage_options',
			'rgnmhn-wishlist-wishlist-my-account',
			array( $this, 'myAccountSettingsHTML' )
		);
		add_action( "load-$submenu", array( $this, 'assets' ) );

		// Submenu: General Settings.
		$submenu = add_submenu_page(
			'rgnmhn-customer-wishlist',
			__( 'Customer Wishlist General Settings', 'rgnmhn-customer-wishlist' ),
			__( 'General Settings', 'rgnmhn-customer-wishlist' ),
			'manage_options',
			'rgnmhn-customer-wishlist-general-settings',
			array( $this, 'generalSettingsHTML' )
		);
		add_action( "load-$submenu", array( $this, 'assets' ) );
	}

	/**
	 * Render the General Settings admin page.
	 *
	 * Retrieves fields from GeneralSettingOptions
	 * and loads the corresponding template.
	 *
	 * @return void
	 */
	public function generalSettingsHTML() {
		$settings = GeneralSettingOptions::fields();
		require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'admin/templates/general.php';
	}

	/**
	 * Render the My Account Settings admin page.
	 *
	 * Retrieves fields from MyAccountOptions
	 * and loads the corresponding template.
	 *
	 * @return void
	 */
	public function myAccountSettingsHTML() {
		$settings = MyAccountOptions::fields();
		require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'admin/templates/my-account.php';
	}

	/**
	 * Render the Product Settings admin page.
	 *
	 * Retrieves fields from ProductOptions
	 * and loads the corresponding template.
	 *
	 * @return void
	 */
	public function productSettingsHTML() {
		$settings = ProductOptions::fields();
		require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'admin/templates/product-settings.php';
	}

	/**
	 * Enqueue and localize assets for admin settings pages.
	 *
	 * - Removes default WP `forms` style to avoid conflicts
	 * - Enqueues WP color picker
	 * - Enqueues custom plugin CSS/JS
	 * - Localizes the theme color palette (first 6 colors) to be used in JS
	 *
	 * @return void
	 */
	public function assets() {
		wp_dequeue_style( 'forms' );
		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_style( 'rgnmhn-customer-wishlist', $this->getAssetFile( 'css', 'rgnmhn-customer-wishlist.css' ), array(), RGNMHN_CUSTOMER_WISHLIST_VERSION );
		wp_enqueue_script( 'rgnmhn-customer-wishlist-script', $this->getAssetFile( 'js', 'rgnmhn-customer-wishlist.js' ), array( 'wp-color-picker' ), RGNMHN_CUSTOMER_WISHLIST_VERSION, true );

		$themePalette = wp_get_global_settings()['color']['palette']['default'] ?? array();

		$paletteColors = array_map(
			function ( $color ) {
				return $color['color'] ?? '';
			},
			$themePalette
		);

		$paletteColors = array_slice( $paletteColors, 0, 6 );

		wp_localize_script( 'rgnmhn-customer-wishlist-script', 'rgnmhn_theme_palette', $paletteColors );
	}

	/**
	 * Helper method to get the full asset URL for CSS/JS files.
	 *
	 * @param string $type - Either "css" or "js".
	 * @param string $file - File name including extension.
	 * @return string The fully-qualified asset URL
	 */
	private function getAssetFile( string $type, string $file ) {
		return RGNMHN_CUSTOMER_WISHLIST_URL . 'admin/' . $type . '/' . $file;
	}
}
