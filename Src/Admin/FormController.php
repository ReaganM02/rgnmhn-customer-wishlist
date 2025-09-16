<?php

/**
 *  FormController: Handles POSTed admin settings for the RGN Customer Wishlist plugin.
 *
 * - Registers admin_post actions for each settings form.
 * - Validates capability + nonces.
 * - Sanitizes/validates incoming values (whitelist by known keys).
 * - Persists options and redirects back with a success flag.
 *
 * @since     1.0.0
 */

namespace ReaganMahinay\RGNCustomerWishlist\Admin;

use ReaganMahinay\RGNCustomerWishlist\GeneralSettingOptions;
use ReaganMahinay\RGNCustomerWishlist\MyAccountOptions;
use ReaganMahinay\RGNCustomerWishlist\ProductOptions;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FormController
 *
 * Wires up admin_post handlers and processes the three settings groups:
 * - General settings
 * - My Account settings
 * - Product settings
 */
class FormController {

	/**
	 * Bootstrap hooks on construct.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register admin_post actions for handling settings submissions.
	 *
	 * @return void
	 */
	public function hooks() {
		// Product settings submit action.
		add_action( 'admin_post_rgnmhn_wishlist_save_settings', array( $this, 'handleProductSettings' ) );

		// My Account settings submit action.
		add_action( 'admin_post_rgnmhn_wishlist_save_my_account', array( $this, 'handleMyAccountSettings' ) );

		// General settings submit action.
		add_action( 'admin_post_rgnmhn_wishlist_general_settings', array( $this, 'handleGeneralSettings' ) );
	}

	/**
	 * Handle saving General Settings (e.g., delete-on-uninstall flag).
	 *
	 * Security:
	 * - Capability check: manage_options
	 * - CSRF: check_admin_referer('rgnmhn_wishlist_general_settings')
	 *
	 * @return void
	 */
	public function handleGeneralSettings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have sufficient permissions to access this page.', 'rgnmhn-customer-wishlist' ),
				esc_html__( 'Access denied', 'rgnmhn-customer-wishlist' ),
				array(
					'response'  => 403,
					'back_link' => true,
				)
			);
		}

		check_admin_referer( 'rgnmhn_wishlist_general_settings' );

		$value = ( isset( $_POST['remove-data-on-uninstall'] ) ) && ( 'yes' === $_POST['remove-data-on-uninstall'] ) ? 'yes' : 'no';

		update_option( GeneralSettingOptions::optionKey(), $value );

		$redirectURL = add_query_arg( 'settings-updated', 'true', wp_get_referer() );
		wp_safe_redirect( $redirectURL );
	}

	/**
	 * Handle saving My Account settings.
	 *
	 * Security:
	 * - Capability check: manage_options
	 * - CSRF: check_admin_referer('rgnmhn_wishlist_save_my_account_security')
	 *
	 * Validation/Sanitization:
	 * - Whitelist by keys defined in MyAccountOptions::fields()
	 * - Key-specific sanitization (e.g., slug vs text)
	 *
	 * @return void
	 */
	public function handleMyAccountSettings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have sufficient permissions to access this page.', 'rgnmhn-customer-wishlist' ),
				esc_html__( 'Access denied', 'rgnmhn-customer-wishlist' ),
				array(
					'response'  => 403,
					'back_link' => true,
				)
			);
		}

		check_admin_referer( 'rgnmhn_wishlist_save_my_account_security' );

		$sanitizedData = array();

		foreach ( MyAccountOptions::getKeysAndRules() as $key => $rules ) {
			$raw   = filter_input( INPUT_POST, $key, FILTER_UNSAFE_RAW );
			$value = is_string( $raw ) ? wp_unslash( $raw ) : $raw;

			// Since they are all texts, no need to use switch.
			$sanitizedData[ $key ] = sanitize_text_field( $value );
		}

		update_option( MyAccountOptions::optionKey(), $sanitizedData );

		$redirectURL = add_query_arg( 'settings-updated', 'true', wp_get_referer() );
		wp_safe_redirect( $redirectURL );
	}

	/**
	 * Handle saving Product settings.
	 *
	 * Security:
	 * - Capability check: manage_options
	 * - CSRF: check_admin_referer('rgnmhn_wishlist_save_settings_security')
	 *
	 * Validation/Sanitization:
	 * - Whitelist by keys defined in ProductOptions::fields()
	 * - Per-key sanitization
	 *
	 * @return void
	 */
	public function handleProductSettings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have sufficient permissions to access this page.', 'rgnmhn-customer-wishlist' ),
				esc_html__( 'Access denied', 'rgnmhn-customer-wishlist' ),
				array(
					'response'  => 403,
					'back_link' => true,
				)
			);
		}

		check_admin_referer( 'rgnmhn_wishlist_save_settings_security' );

		$sanitizedData = array();
		foreach ( ProductOptions::getKeysAndRules() as $key => $rules ) {
			$type = $rules['type'];

			if ( 'bool' === $type ) {
				$present               = filter_input( INPUT_POST, $key, FILTER_DEFAULT ) !== null;
				$sanitizedData[ $key ] = $present ? 'yes' : 'no';
				continue;
			}

			$raw = filter_input( INPUT_POST, $key, FILTER_UNSAFE_RAW );

			$value = is_string( $raw ) ? wp_unslash( $raw ) : $raw;

			switch ( $type ) {
				case 'int':
					$n = absint( $value );
					if ( isset( $rules['min'] ) && $n < $rules['min'] ) {
						$n = $rules['min'];
					}
					if ( isset( $rules['max'] ) && $n > $rules['max'] ) {
						$n = $rules['max'];
					}
					$sanitizedData[ $key ] = $n;
					break;
				case 'text':
					$sanitizedData[ $key ] = sanitize_text_field( $value );
					break;
				default:
					$sanitizedData[ $key ] = sanitize_text_field( $value );
					break;
			}
		}
		update_option( ProductOptions::optionKey(), $sanitizedData );
		$redirectURL = add_query_arg( 'settings-updated', 'true', wp_get_referer() );
		wp_safe_redirect( $redirectURL );
	}
}
