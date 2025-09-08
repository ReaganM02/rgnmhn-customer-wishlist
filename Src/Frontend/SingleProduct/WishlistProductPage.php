<?php

namespace ReaganMahinay\RGNCustomerWishlist\Frontend\SingleProduct;

use ReaganMahinay\RGNCustomerWishlist\Models\WishlistModel;
use ReaganMahinay\RGNCustomerWishlist\MyAccountOptions;
use ReaganMahinay\RGNCustomerWishlist\ProductOptions;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WishlistProductPage
 *
 * Handles single product page wishlist UI:
 * - Registers hooks and/or shortcode (based on settings)
 * - Enqueues assets on single product pages
 * - Renders "Add to wishlist" / "Added" template and base wrapper template
 *
 * # Hooks registered
 * - `wp_enqueue_scripts` to enqueue scripts/styles
 * - A product-page action hook (filter-controlled via `rgnmhn_wishlist_placement_product_page`)
 * - Shortcode `rgnmhn_wishlist_single_product` when "use_shortcode" is selected
 *
 * # Filters
 * - `rgnmhn_wishlist_placement_product_page` (string|false): set the WC single product hook name
 * - `rgnmhn_wishlist_placement_product_page_priority` (int): set hook priority
 *
 * @since 1.0.0
 * @package rgnmhn-customer-wishlist
 */
class WishlistProductPage {

	/**
	 * Bootstrap the class by wiring up hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->hooks();
	}

	/**
	 * Register WordPress hooks and shortcode conditionally.
	 *
	 * - Always enqueues assets via `wp_enqueue_scripts`.
	 * - If placement is not "use_shortcode", attaches `displayWishlistContent()` to the chosen product hook.
	 * - Otherwise registers `[rgnmhn_wishlist_single_product]` shortcode.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

		if ( ProductOptions::getSelectedPlacementContent() !== 'use_shortcode' ) {
			if ( self::hookToUse() ) {
				add_action( self::hookToUse(), array( $this, 'displayWishlistContent' ), self::hookPriority() );
			}
		} else {
			add_shortcode( 'rgnmhn_customer_wishlist_single_product', array( $this, 'shortCode' ) );
		}
	}

	/**
	 * Shortcode callback for `[rgnmhn_wishlist_single_product]`.
	 *
	 * Outputs wishlist UI only on single product pages.
	 * Uses output buffering to return the rendered markup.
	 *
	 * @since 1.0.0
	 * @return string Rendered HTML or empty string if not on a product page.
	 */
	public function shortCode() {
		if ( ! is_product() ) {
			return '';
		}

		ob_start();
		$this->displayWishlistContent( get_the_ID() );
		return ob_get_clean();
	}

	/**
	 * Determine which single product hook to use for rendering.
	 *
	 * Default is the value from `ProductOptions::getSelectedPlacementContent()`.
	 * Developers may override via the `rgnmhn_wishlist_placement_product_page` filter.
	 *
	 * @since 1.0.0
	 * @return string|false Hook name or false to disable hook placement.
	 */
	private static function hookToUse() {
		$defaultHook = ProductOptions::getSelectedPlacementContent();
		/**
		 * Filter: rgnmhn_wishlist_placement_product_page
		 * - Return any WC single product hook (string) to reposition the output.
		 * - Return false to disable hook placement.
		 */
		return apply_filters( 'rgnmhn_wishlist_placement_product_page', $defaultHook );
	}

	/**
	 * Priority for the selected single product hook.
	 *
	 * Developers may override via `rgnmhn_wishlist_placement_product_page_priority`.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	private static function hookPriority() {
		$defaultPriority = 35;
		/**
		 * Filter: rgnmhn_wishlist_placement_product_page_priority
		 * - Adjust the priority of the chosen hook
		 */
		return apply_filters( 'rgnmhn_wishlist_placement_product_page_priority', $defaultPriority );
	}

	/**
	 * Echoes the combined wishlist UI.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function displayWishlistContent() {
		$this->getWishlistContent();
	}

	/**
	 * Renders all wishlist sections in order:
	 * - "Added to wishlist" UI template
	 * - "Add to wishlist" UI template
	 * - Base wrapper template
	 *
	 * Uses output buffering around each section to avoid partial echo on errors.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function getWishlistContent() {
		if ( ! is_user_logged_in() && ! ProductOptions::isGuestUserAllowed() ) {
			return '';
		}
		ob_start();
		$this->getAddedWishlistContent();
		echo ob_get_clean();

		ob_start();
		$this->getAddToWishlistContent();
		echo ob_get_clean();

		ob_start();
		self::getTemplateOnce( 'single-product/wishlist.php' );
		echo ob_get_clean();
	}

	/**
	 * Resolve "already added" variation IDs for a given product.
	 *
	 * - If a variation ID is passed, finds the parent product and returns its children IDs.
	 * - If a variable product ID is passed, returns its children IDs.
	 * - Otherwise returns an empty array.
	 * - Then filters those IDs by what's present in the wishlist storage.
	 *
	 * @since 1.0.0
	 * @param int $productID A product or variation ID.
	 * @return int[] List of variation IDs that are in the wishlist.
	 */
	public function getAddedVariationIDs( int $productID ) {
		$product = wc_get_product( $productID );

		if ( ! $product ) {
			return array();
		}

		$variationIDs = array();

		// If product is variation it means that the given product ID is a variation ID.
		if ( $product->is_type( 'variation' ) ) {
			$productParentID = $product->get_parent_id();
			if ( $productParentID > 0 ) {
				$parentProduct = wc_get_product( $productParentID );
				if ( $parentProduct && $parentProduct->is_type( 'variable' ) ) {
					$variationIDs = $parentProduct->get_children();
				}
			}
		} elseif ( $product->is_type( 'variable' ) ) {
			$variationIDs = $product->get_children();
		} else {
			$variationIDs = array();
		}

		if ( ! empty( $variationIDs ) ) {
			$wishlist = new WishlistModel();
			$ids      = $wishlist->getProductIDs( $variationIDs );
			return $ids;
		}

		return array();
	}


	/**
	 * Render the "Add to wishlist" template.
	 *
	 * Passes icon and label to the template.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function getAddToWishlistContent() {
		$icon = ProductOptions::getIcon();
		$args = array(
			'icon'  => rgnmhnCustomerWishlistGetIcons()[ $icon ],
			'label' => ProductOptions::getWishlistLabel(),
		);
		self::getTemplateOnce( 'single-product/add.php', $args );
	}


	/**
	 * Render the "Added to wishlist" template
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function getAddedWishlistContent() {
		$args = array(
			'slug'  => MyAccountOptions::getSlug(),
			'label' => ProductOptions::getAddedToWishlistLabel(),
		);
		self::getTemplateOnce( 'single-product/added.php', $args );
	}

	/**
	 * Require a template file and make $data available in local scope.
	 *
	 * @since 1.0.0
	 * @param string              $path Relative path under plugin `templates/`.
	 * @param array<string,mixed> $data Variables to extract into template scope.
	 * @return void
	 */
	private static function getTemplateOnce( string $path, $data = array() ) {
		require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'templates/' . $path;
	}

	/**
	 * Get the current product's type string (e.g., "simple", "variable", "variation").
	 *
	 * @since 1.0.0
	 * @return string
	 */
	private function getProductType() {
		$product = wc_get_product( get_the_ID() );
		return $product->get_type();
	}

	/**
	 * Whether the current product ID is already in the wishlist for the current identifier.
	 *
	 * @param int $productID - The product ID.
	 * @since 1.0.0
	 * @return "yes"|"no"
	 */
	public function isAdded( int $productID ) {
		$wishlist   = new WishlistModel();
		$identifier = wishListIdentifier();
		$isAdded    = $wishlist->isProductInWishlist( $productID, $identifier );
		if ( empty( $isAdded ) ) {
			return 'no';
		}
		return 'yes';
	}

	/**
	 * Enqueue frontend assets on single product pages.
	 *
	 * Localizes:
	 * - `url` (admin-ajax)
	 * - `nonce` values for add actions
	 * - `product_id`, `added_ids`, `product_type`, `is_added`
	 *
	 * Registers an inline CSS handle used to inject CSS variables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function assets() {
		// $path = RGNMHN_CUSTOMER_WISHLIST_PATH . 'assets/js/rgnmhn-customer-wishlist-single-product.js';
		if ( is_product() ) {
			wp_enqueue_script(
				'rgnmhn-customer-wishlist-single-product',
				RGNMHN_CUSTOMER_WISHLIST_URL . 'assets/js/rgnmhn-customer-wishlist-single-product.js',
				array( 'jquery', 'wc-add-to-cart-variation' ),
				RGNMHN_CUSTOMER_WISHLIST_VERSION,
				true
			);

			wp_localize_script(
				'rgnmhn-customer-wishlist-single-product',
				'rgnmhn_wishlist_single_product',
				array(
					'url'          => admin_url( 'admin-ajax.php' ),
					'nonce'        => array(
						'add' => wp_create_nonce( 'rgnmhn_add_customer_wishlist_security' ),
						'get' => wp_create_nonce( 'rgnmhn_get_customer_wishlist_security' ),
					),
					'product_id'   => get_the_ID(),
					'added_ids'    => $this->getAddedVariationIDs( get_the_ID() ),
					'product_type' => $this->getProductType(),
					'is_added'     => $this->isAdded( get_the_ID() ),
				)
			);

			wp_register_style(
				'rgnmhn-customer-wishlist-inline',
				false,
				array(),
				RGNMHN_CUSTOMER_WISHLIST_VERSION
			);
			wp_enqueue_style(
				'rgnmhn-customer-wishlist-inline',
				'',
				array(),
				RGNMHN_CUSTOMER_WISHLIST_VERSION
			);
			wp_add_inline_style( 'rgnmhn-customer-wishlist-inline', self::inlineCss() );
			wp_enqueue_style(
				'rgnmhn-customer-wishlist-single-product',
				RGNMHN_CUSTOMER_WISHLIST_URL . 'assets/css/rgnmhn-customer-wishlist.css',
				array( 'rgnmhn-customer-wishlist-inline' ),
				RGNMHN_CUSTOMER_WISHLIST_VERSION
			);
		}
	}

	/**
	 * Build CSS variables for styling the wishlist UI.
	 *
	 * @since 1.0.0
	 * @return string A CSS string injected via `wp_add_inline_style`.
	 */
	public static function inlineCss() {
		$backgroundColor     = sanitize_hex_color( ProductOptions::getBackgroundColor() );
		$color               = sanitize_hex_color( ProductOptions::getTextColor() );
		$fontSize            = absint( ProductOptions::getFontSize() );
		$svgSize             = absint( ProductOptions::getIconSize() );
		$browseWishlistColor = sanitize_hex_color( ProductOptions::getBrowseWishlistColor() );

		return "
      :root {
        --rgnmhn-wishlist-bg-color: $backgroundColor;
        --rgnmhn-wishlist-text-color:  $color;
        --rgnmhn-wishlist-font-size: $fontSize" . "px;
        --rgnmhn-wishlist-svg-size: $svgSize" . "px;
        --rgnmhn-wishlist-browse-wishlist: $browseWishlistColor;
      }
    ";
	}
}
