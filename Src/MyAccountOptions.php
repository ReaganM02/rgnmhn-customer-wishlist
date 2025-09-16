<?php

namespace ReaganMahinay\RGNCustomerWishlist;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MyAccountOptions
 *
 * Handles retrieval, sanitization, and management of the
 * "My Account" options for the rgnmhn Customer Wishlist plugin.
 *
 * This class provides access to settings such as:
 * - Menu slug
 * - Menu title
 * - Content title
 * - Empty wishlist message
 *
 * Options are cached statically to avoid multiple calls to
 * WordPress's get_option() function. Cache can be refreshed
 * via {@see MyAccountOptions::refresh()}.
 *
 * @since 1.0.0
 * @package Src
 */
final class MyAccountOptions {

	/**
	 *  Option key for storing this feature's settings in WordPress.
	 *
	 *  @since 1.0.0
	 */
	private const KEY = 'rgnmhn_customer_wishlist_my_account_settings';

	/**
	 * Cached plugin options array.
	 *
	 * @var array<string, mixed>|null
	 */
	private static $option = null;

	/**
	 * The option key
	 */
	public static function optionKey() {
		return self::KEY;
	}

	/**
	 * Retrieve and cache the My Account options from the database.
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
	 * Get the slug used for the My Account wishlist menu.
	 *
	 * Falls back to "my-wishlist" if not set.
	 *
	 * @since 1.0.0
	 * @return string Sanitized slug string.
	 */
	public static function getSlug() {
		self::getOptions();
		if ( ! empty( self::$option ) && self::has( 'menu-slug' ) ) {
			return (string) sanitize_title( self::$option['menu-slug'] );
		}
		return 'my-wishlist';
	}

	/**
	 * Get the title of the My Account wishlist menu.
	 *
	 * Falls back to "My Wishlist" if not set.
	 *
	 * @since 1.0.0
	 * @return string Menu title.
	 */
	public static function getMenuTitle() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'menu-title' ) ) {
			return (string) self::$option['menu-title'];
		}
		return 'My Wishlist';
	}

	/**
	 * Get the title displayed in the wishlist content section.
	 *
	 * Falls back to "List of Wishlist" if not set.
	 *
	 * @since 1.0.0
	 * @return string Content title.
	 */
	public static function getContentTitle() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'content-title' ) ) {
			return (string) self::$option['content-title'];
		}
		return 'List of Wishlist';
	}

	/**
	 * Get the message shown when the wishlist is empty.
	 *
	 * Falls back to "Wishlist is empty, add some!" if not set.
	 *
	 * @since 1.0.0
	 * @return string Empty wishlist message.
	 */
	public static function getEmptyContentMessage() {
		self::getOptions();
		if ( ! self::emptyOption() && self::has( 'content-empty-message' ) ) {
			return (string) self::$option['content-empty-message'];
		}
		return 'Wishlist is empty, add some!';
	}


	/**
	 * Check if options are empty.
	 *
	 * @since 1.0.0
	 * @return bool True if empty, false otherwise.
	 */
	private static function emptyOption() {
		return empty( self::$option );
	}

	/**
	 * Check if a specific key exists in the options array.
	 *
	 * @since 1.0.0
	 * @param string $key Option key to check.
	 * @return bool True if key exists, false otherwise.
	 */
	private static function has( string $key ) {
		return isset( self::$option[ $key ] );
	}


	/**
	 * Get field key and rules
	 */
	public static function getKeysAndRules() {
		$output = array();
		foreach ( self::fields() as $field ) {
			$output[ $field['name'] ] = $field['rules'];
		}
		return $output;
	}


	/**
	 * Define the fields used for My Account wishlist settings.
	 *
	 * Each field includes attributes like type, id, name,
	 * current value, label, and description.
	 *
	 * @since 1.0.0
	 */
	public static function fields() {
		return array(
			'menu-title'            => array(
				'type'  => 'text',
				'id'    => 'menu-title',
				'name'  => 'menu-title',
				'value' => self::getMenuTitle(),
				'label' => __( 'Menu Title', 'rgnmhn-customer-wishlist' ),
				'rules' => array( 'type' => 'text' ),
			),
			'menu-slug'             => array(
				'type'        => 'text',
				'id'          => 'menu-slug',
				'name'        => 'menu-slug',
				'value'       => self::getSlug(),
				'label'       => __( 'Menu Slug', 'rgnmhn-customer-wishlist' ),
				'description' => __( 'No spaces are allowed valid slug only.', 'rgnmhn-customer-wishlist' ),
				'rules'       => array( 'type' => 'text' ),
			),
			'content-title'         => array(
				'type'  => 'text',
				'id'    => 'content-title',
				'name'  => 'content-title',
				'value' => self::getContentTitle(),
				'label' => __( 'Content Title', 'rgnmhn-customer-wishlist' ),
				'rules' => array( 'type' => 'text' ),
			),
			'content-empty-message' => array(
				'type'  => 'text',
				'id'    => 'content-empty-message',
				'name'  => 'content-empty-message',
				'value' => self::getEmptyContentMessage(),
				'label' => __( 'Empty wishlist message', 'rgnmhn-customer-wishlist' ),
				'rules' => array( 'type' => 'text' ),
			),
		);
	}
}
