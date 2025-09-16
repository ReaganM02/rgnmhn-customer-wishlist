<?php

declare(strict_types=1);

namespace ReaganMahinay\RGNCustomerWishlist;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ProductOptions
 *
 * Handles retrieval, caching, and accessors for the Wishlist product options.
 *
 * Provides access to settings such as:
 * - Guest user allowance and cookie expiry
 * - Icon style, size, and font size
 * - Wishlist labels ("Add" / "Added")
 * - Colors for background, text, and browse link
 * - Wishlist placement on the single product page
 *
 * Options are cached statically to reduce calls to
 * WordPress's get_option() function. Use {@see ProductOptions::refresh()}
 * to reset the cache.
 *
 * @since 1.0.0
 * @package rgnmhn-customer-wishlist
 */
class ProductOptions {

	/**
	 * Cached plugin options array.
	 *
	 * @var array<string, mixed>|null
	 */
	private static $option;

	/**
	 *  Option key for storing this feature's settings in WordPress.
	 *
	 *  @since 1.0.0
	 */
	private const KEY = 'rgnmhn_customer_wishlist_settings';

	/**
	 * Get the key
	 */
	public static function optionKey() {
		return self::KEY;
	}

	/**
	 * Retrieve and cache the product options from the database.
	 *
	 * Falls back to an empty array if no options are stored.
	 *
	 * @since 1.0.0
	 * @return array<string, mixed>
	 */
	private static function getOptions() {
		return self::$option ??= get_option( self::optionKey(), array() );
	}


	/**
	 * Refresh the cached options, forcing the next call to getOptions()
	 * to re-fetch from the database.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function refresh() {
		self::$option = null;
	}

	/**
	 * Retrieve all option fields as stored in the database.
	 *
	 * @since 1.0.0
	 * @return array<string, mixed> Array of option key/value pairs.
	 */
	public static function getAllFields() {
		return self::getOptions();
	}

	/**
	 * Check whether guest users are allowed to add wishlist items.
	 *
	 * Option key: `allow-non-logged-in-user`
	 *
	 * @since 1.0.0
	 * @return bool True if allowed, false otherwise.
	 */
	public static function isGuestUserAllowed() {
		self::getOptions();

		// If not settings is saved, default it to true.
		if ( self::emptyOption() ) {
			return true;
		}

		if ( ! self::emptyOption() && self::has( 'allow-non-logged-in-user' ) && 'yes' === self::$option['allow-non-logged-in-user'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the number of days to store guest wishlist in a cookie.
	 *
	 * Falls back to 30 days if not set.
	 * Option key: `number-of-days-to-store-cookie`
	 *
	 * @since 1.0.0
	 * @return int Days to store guest wishlist cookie.
	 */
	public static function getGuestUserExpiryDate() {
		self::getOptions();
		if (
			! self::emptyOption()
			&& self::has( 'number-of-days-to-store-cookie' )
			&& ! self::isEmpty( 'number-of-days-to-store-cookie' )
		) {
			return (int) self::$option['number-of-days-to-store-cookie'];
		}
		return 30;
	}

	/**
	 * Get the wishlist icon identifier.
	 *
	 * Falls back to "icon-4".
	 * Option key: `wishlist-icon`
	 *
	 * @since 1.0.0
	 * @return string Icon key.
	 */
	public static function getIcon() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'wishlist-icon' ) ) {
			return (string) self::$option['wishlist-icon'];
		}
		return 'icon-4';
	}

	/**
	 * Get the wishlist icon size in pixels.
	 *
	 * Falls back to 20 if not set.
	 * Option key: `wishlist-icon-size`
	 *
	 * @since 1.0.0
	 * @return int Icon size in pixels.
	 */
	public static function getIconSize() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'wishlist-icon-size' ) && ! self::isEmpty( 'wishlist-icon-size' ) ) {
			return (int) self::$option['wishlist-icon-size'];
		}
		return 20;
	}

	/**
	 * Get the font size used for wishlist labels.
	 *
	 * Falls back to 15 if not set.
	 * Option key: `font-size`
	 *
	 * @since 1.0.0
	 * @return int Font size in pixels.
	 */
	public static function getFontSize() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'font-size' ) && ! self::isEmpty( 'font-size' ) ) {
			return (int) self::$option['font-size'];
		}
		return 15;
	}

	/**
	 * Get the label text for "Add to Wishlist".
	 *
	 * Falls back to "Add to Wishlist".
	 * Option key: `wishlist-label`
	 *
	 * @since 1.0.0
	 * @return string Label text.
	 */
	public static function getWishlistLabel() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'wishlist-label' ) ) {
			return (string) self::$option['wishlist-label'];
		}
		return 'Add to Wishlist';
	}

	/**
	 * Get the label text for "Added to Wishlist".
	 *
	 * Falls back to "Added to Wishlist".
	 * Option key: `added-to-wishlist-label`
	 *
	 * @since 1.0.0
	 * @return string Label text.
	 */
	public static function getAddedToWishlistLabel() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'added-to-wishlist-label' ) ) {
			return (string) self::$option['added-to-wishlist-label'];
		}
		return 'Added to Wishlist';
	}

	/**
	 * Get the wishlist button background color.
	 *
	 * Falls back to "#2563eb" (blue).
	 * Option key: `background-color`
	 *
	 * @since 1.0.0
	 * @return string Hex color value.
	 */
	public static function getBackgroundColor() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'background-color' ) ) {
			return (string) self::$option['background-color'];
		}
		return '#2563eb';
	}

	/**
	 * Get the wishlist button text color.
	 *
	 * Falls back to "#fff".
	 * Option key: `text-color`
	 *
	 * @since 1.0.0
	 * @return string Hex color value.
	 */
	public static function getTextColor() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'text-color' ) ) {
			return (string) self::$option['text-color'];
		}
		return '#fff';
	}

	/**
	 * Get the color for "Browse Wishlist" link.
	 *
	 * Falls back to "#52525b" (gray).
	 * Option key: `browse-wishlist-color`
	 *
	 * @since 1.0.0
	 * @return string Hex color value.
	 */
	public static function getBrowseWishlistColor() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'browse-wishlist-color' ) ) {
			return (string) self::$option['browse-wishlist-color'];
		}
		return '#52525b';
	}

	/**
	 * Get the selected placement for wishlist content on product pages.
	 *
	 * Falls back to `woocommerce_after_add_to_cart_form`.
	 * Option key: `wishlist-placement`
	 *
	 * @since 1.0.0
	 * @return string Placement hook name.
	 */
	public static function getSelectedPlacementContent() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'wishlist-placement' ) ) {
			return (string) self::$option['wishlist-placement'];
		}
		return 'woocommerce_after_add_to_cart_form';
	}

	/**
	 * Get Keys and rules
	 */
	public static function getKeysAndRules() {
		$output = array();
		foreach ( self::fields() as $field ) {
			$output[ $field['name'] ] = $field['rules'];
		}
		return $output;
	}

	/**
	 * Define the fields used for product wishlist settings.
	 *
	 * Each field includes attributes such as id, name, type,
	 * label, description, value, and options.
	 *
	 * @since 1.0.0
	 */
	public static function fields() {
		return array(
			'allow-non-logged-in-user'       => array(
				'id'      => 'allow-non-logged-in-user',
				'name'    => 'allow-non-logged-in-user',
				'label'   => __( 'Allow guest users to add wishlist', 'rgnmhn-customer-wishlist' ),
				'type'    => 'switch',
				'desc'    => '',
				'value'   => 'yes',
				'checked' => self::isGuestUserAllowed(),
				'rules'   => array( 'type' => 'bool' ),
			),
			'number-of-days-to-store-cookie' => array(
				'id'    => 'number-of-days-to-store-cookie',
				'name'  => 'number-of-days-to-store-cookie',
				'label' => __( 'For guest customers, How long to keep your wishlist (in days)', 'rgnmhn-customer-wishlist' ),
				'type'  => 'number',
				'desc'  => '',
				'value' => self::getGuestUserExpiryDate(),
				'rules' => array(
					'type' => 'int',
					'min'  => 1,
					'max'  => 30,
				),
			),
			'wishlist-icon'                  => array(
				'id'      => 'wishlist-icon',
				'name'    => 'wishlist-icon',
				'label'   => __( 'Select Wishlist Icon', 'rgnmhn-customer-wishlist' ),
				'type'    => 'radio',
				'options' => rgnmhnCustomerWishlistGetIcons(),
				'default' => self::getIcon(),
				'rules'   => array( 'type' => 'text' ),
			),
			'wishlist-icon-size'             => array(
				'id'    => 'wishlist-icon-size',
				'name'  => 'wishlist-icon-size',
				'label' => __( 'Icon size', 'rgnmhn-customer-wishlist' ),
				'type'  => 'number',
				'value' => self::getIconSize(),
				'rules' => array(
					'type' => 'int',
					'min'  => 1,
					'max'  => 100,
				),
			),
			'font-size'                      => array(
				'id'    => 'font-size',
				'name'  => 'font-size',
				'label' => __( 'Font size', 'rgnmhn-customer-wishlist' ),
				'type'  => 'number',
				'value' => self::getFontSize(),
				'rules' => array(
					'type' => 'int',
					'min'  => 1,
					'max'  => 99,
				),
			),
			'wishlist-label'                 => array(
				'id'    => 'wishlist-label',
				'name'  => 'wishlist-label',
				'type'  => 'text',
				'label' => __( 'Wishlist Label', 'rgnmhn-customer-wishlist' ),
				'value' => self::getWishlistLabel(),
				'rules' => array( 'type' => 'text' ),
			),
			'added-to-wishlist-label'        => array(
				'id'    => 'added-to-wishlist-label',
				'name'  => 'added-to-wishlist-label',
				'type'  => 'text',
				'value' => self::getAddedToWishlistLabel(),
				'label' => __( 'Wishlist is Added Label', 'rgnmhn-customer-wishlist' ),
				'rules' => array( 'type' => 'text' ),
			),
			'background-color'               => array(
				'id'    => 'background-color',
				'name'  => 'background-color',
				'label' => __( 'Background color', 'rgnmhn-customer-wishlist' ),
				'type'  => 'color',
				'value' => self::getBackgroundColor(),
				'rules' => array( 'type' => 'color' ),
			),
			'text-color'                     => array(
				'id'    => 'text-color',
				'name'  => 'text-color',
				'label' => __( 'Text Color', 'rgnmhn-customer-wishlist' ),
				'type'  => 'color',
				'value' => self::getTextColor(),
				'rules' => array( 'type' => 'color' ),
			),
			'browse-wishlist-color'          => array(
				'id'    => 'browse-wishlist-color',
				'name'  => 'browse-wishlist-color',
				'label' => __( 'Browse Wishlist Color', 'rgnmhn-customer-wishlist' ),
				'type'  => 'color',
				'value' => self::getBrowseWishlistColor(),
				'rules' => array( 'type' => 'color' ),
			),
			'wishlist-placement'             => array(
				'id'       => 'wishlist-placement',
				'name'     => 'wishlist-placement',
				'type'     => 'select',
				'label'    => __( 'Select Content Placement', 'rgnmhn-customer-wishlist' ),
				'options'  => array(
					'woocommerce_after_add_to_cart_form'  => __( 'After add to cart form', 'rgnmhn-customer-wishlist' ),
					'woocommerce_before_add_to_cart_form' => __( 'Before add to cart form', 'rgnmhn-customer-wishlist' ),
					'woocommerce_before_add_to_cart_quantity' => __( 'Before add to cart quantity', 'rgnmhn-customer-wishlist' ),
					'woocommerce_after_single_product_summary' => __( 'After single product summary', 'rgnmhn-customer-wishlist' ),
					'use_shortcode'                       => __( 'Use shortcode', 'rgnmhn-customer-wishlist' ),
				),
				'selected' => self::getSelectedPlacementContent(),
				'rules'    => array( 'type' => 'text' ),
			),
		);
	}


	/**
	 * Check if options array is empty.
	 *
	 * @since 1.0.0
	 * @return bool True if empty, false otherwise.
	 */
	private static function emptyOption() {
		return empty( self::$option );
	}

	/**
	 * Check if a specific option key exists but has no value.
	 *
	 * @since 1.0.0
	 * @param string $key Option key.
	 * @return bool True if empty, false otherwise.
	 */
	private static function isEmpty( string $key ) {
		return empty( self::$option[ $key ] );
	}

	/**
	 * Check if a specific key exists in the options array.
	 *
	 * @since 1.0.0
	 * @param string $key Option key.
	 * @return bool True if key exists, false otherwise.
	 */
	private static function has( string $key ) {
		return isset( self::$option[ $key ] );
	}
}
