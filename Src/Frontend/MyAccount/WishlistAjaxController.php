<?php

namespace ReaganMahinay\RGNCustomerWishlist\Frontend\MyAccount;

use ReaganMahinay\RGNCustomerWishlist\Models\WishlistModel;
use ReaganMahinay\RGNCustomerWishlist\Frontend\MyAccount\WishlistAccountEndpoint;

use WC_Product_Variation;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WishlistAjaxController
 *
 * Handles authenticated AJAX actions for the Wishlist "My Account" page:
 * - Delete wishlist item
 * - Add product (or variation) to the WooCommerce cart
 *
 * SECURITY
 * - All actions require login (uses `wp_ajax_` hooks).
 * - CSRF protected via `check_ajax_referer()` with action-specific nonces.
 * - Product IDs sanitized with `absint()`; POST data unlashed before use.
 *
 * UX/RESPONSES
 * - Returns JSON success/error with clear messages for the frontend.
 * - After deletion, returns the fresh Wishlist HTML (server-rendered) so the
 *   client can repaint the section without a full page reload.
 *
 * @package rgnmhn-customer-wishlist
 * @since   1.0.0
 */
class WishlistAjaxController {

	/**
	 * Bootstrap action hooks.
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register AJAX actions (authenticated only).
	 *
	 * @return void
	 */
	public function hooks() {
		// Add a product/variation to the WooCommerce cart.
		add_action( 'wp_ajax_rgnmhn_wishlist_add_to_cart', array( $this, 'addToCart' ) );

		// Remove an item from the wishlist.
		add_action( 'wp_ajax_rgnmhn_wishlist_delete_item', array( $this, 'delete' ) );
	}

	/**
	 * AJAX: Delete an item from the wishlist for the current user.
	 *
	 * Requires nonce 'rgnmhn_remove_from_wishlist_security' (sent as 'security').
	 *
	 * @return void Sends JSON response and exits.
	 */
	public function delete() {
		check_ajax_referer( 'rgnmhn_remove_from_wishlist_security', 'security' );

		// Sanitize incoming product ID.
		$productID = isset( $_POST['product-id'] ) ? absint( wp_unslash( $_POST['product-id'] ) ) : 0;

		if ( ! $productID ) {
			wp_send_json_error( __( 'Invalid product ID.', 'rgnmhn-customer-wishlist' ) );
		}

		$wishlist = new WishlistModel();

		$deleted = $wishlist->delete( $productID, get_current_user_id() );

		if ( ! $deleted ) {
			wp_send_json_error( __( 'Failed to remove item from wishlist.', 'rgnmhn-customer-wishlist' ) );
		}

		// Re-render the wishlist section HTML to update the UI.
		// Note: wishlistContent() echoes content; use output buffering to capture.
		$frontend = new WishlistAccountEndpoint();
		ob_start();
		$content = $frontend->wishlistContent();
		$content = ob_get_clean();

		wp_send_json_success( $content );
	}

	/**
	 * AJAX: Add a product or variation to the WooCommerce cart.
	 *
	 * Requires nonce 'rgnmhn_add_to_cart_security' (sent as 'security').
	 *
	 * @return void Sends JSON response and exits.
	 */
	public function addToCart() {
		check_ajax_referer( 'rgnmhn_add_to_cart_security', 'security' );

		$productID = isset( $_POST['product-id'] ) ? absint( wp_unslash( $_POST['product-id'] ) ) : 0;

		if ( ! $productID ) {
			wp_send_json_error( __( 'Missing or invalid product.', 'rgnmhn-customer-wishlist' ) );
		}

		$product = wc_get_product( $productID );

		if ( ! $product ) {
			wp_send_json_error( __( 'Product does not exist.', 'rgnmhn-customer-wishlist' ) );
		}

		// Basic purchasability/stock checks.
		if ( ! $product->is_purchasable() || ( ! $product->is_in_stock() && ! $product->backorders_allowed() ) ) {
			wp_send_json_error( __( 'Product is not purchasable or out of stock.', 'rgnmhn-customer-wishlist' ) );
		}

		// Ensure WooCommerce cart object is initialized.
		if ( null === WC()->cart ) {
			wc_load_cart(); // Fallback if needed in some AJAX contexts.
		}

		if ( $product->is_type( 'variation' ) && $product instanceof WC_Product_Variation ) {
			$variationID = $productID;
			$parentID    = $product->get_parent_id();
			$attributes  = $product->get_variation_attributes();
			WC()->cart->add_to_cart( $parentID, 1, $variationID, $attributes );
		} else {
			WC()->cart->add_to_cart( $productID, 1 );
		}

		wp_send_json_success(
			array(
				'message'   => __( 'Added to cart.', 'rgnmhn-customer-wishlist' ),
				'productID' => $productID,
			)
		);
	}
}
