<?php
// Exit if accessed directly.

use ReaganMahinay\RGNCustomerWishlist\ProductOptions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Wishlist stock status
 *
 *  @param WC_Product $product - The product object.
 */
function rgnmhnCustomerWishlistStockStatus( WC_Product $product ) {
	$status = $product->get_stock_status();

	$args = array(
		'status'  => $status,
		'classes' => array( 'stock', $status ),
		'label'   => '',
	);

	if ( 'onbackorder' === $status ) {
		$args['label'] = __( 'On backorder', 'rgnmhn-customer-wishlist' );
	} elseif ( 'instock' === $status ) {
		$args['label'] = __( 'In Stock', 'rgnmhn-customer-wishlist' );
	} else {
		$args['label'] = __( 'Out of Stock', 'rgnmhn-customer-wishlist' );
	}

	/**
	 * Allow developers to adjust args
	 *
	 * @param array $args
	 * @param Wc_Product $product
	 */
	$args = (array) apply_filters( 'rgnmhn_customer_wishlist_stock_status_args', $args, $product );

	// Sanitize classes if not empty.
	$sanitizedClasses = '';
	if ( ! empty( $args['classes'] ) ) {
		$classes          = array_map(
			function ( $class ) {
				return sanitize_html_class( $class );
			},
			$args['classes']
		);
		$sanitizedClasses = implode( ' ', array_filter( $classes ) );
	}

	$html = sprintf( '<div class="%s">%s</div>', esc_attr( (string) $sanitizedClasses ), esc_html( (string) $args['label'] ) );

	/**
	 * Filter the final stock status HTML
	 *
	 * @param string     $html    The rendered HTML.
	 * @param array      $args    The arguments array.
	 * @param WC_Product $product Product object.
	 */
	return apply_filters( 'rgnmhn_customer_wishlist_stock_status_html', $html, $args, $product );
}
/**
 * Formatted data
 *
 * @param string $date - Date.
 */
function rgnmhnCustomerWishlistFormattedDate( string $date ) {
	$dateFormat    = get_option( 'date_format', 'F j, Y' );
	$formattedDate = wp_date( $dateFormat, strtotime( $date ) );

	$output = apply_filters( 'rgnmhn_customer_wishlist_format_date', $formattedDate, $date );
	return $output;
}

/**
 * Title format
 *
 * @param WC_Product $product - Product object.
 */
function rgnmhnCustomerWishlistFormatTitle( WC_Product $product ) {
	$args = array(
		'title' => $product->get_title(),
		'url'   => $product->get_permalink(),
		'class' => array( 'rgnmhn-text-[16px]' ),
		'attrs' => array(),
	);

	$args = apply_filters( 'rgnmhn_customer_wishlist_product_title_args', $args, $product );

	$classStr = implode( ' ', array_map( 'strval', (array) $args['class'] ) );

	$allowedAttributeKeys = array( 'id', 'title', 'aria-label', 'rel', 'target' );

	$attrHTML = '';

	foreach ( (array) $args['attrs'] as $key => $value ) {
		if ( in_array( $key, $allowedAttributeKeys, true ) || str_starts_with( $key, 'data-' ) ) {
			$attrHTML .= ' ' . esc_attr( $key ) . '="' . esc_attr( (string) $value ) . '"';
		}
	}

	$html = sprintf(
		'<a class="%s" href="%s" %s>%s</a>',
		esc_attr( $classStr ),
		esc_url( $args['url'] ),
		$attrHTML,
		esc_html( $args['title'] )
	);

	$output = apply_filters( 'rgnmhn_customer_wishlist_product_title', $html, $args, $product );
	return (string) $output;
}

/**
 * Build a formatted attribute string for a product.
 *
 * @param WC_Product $product The product (variation or variable parent).
 * @return string The formatted attributes (can include HTML via filters).
 */
function rgnmhnCustomerWishlistFormattedAttributes( WC_Product $product ) {

	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	if ( ! $product->is_type( 'variation' ) ) {
		return '';
	}

	$parent = wc_get_product( $product->get_parent_id() );

	if ( ! $parent instanceof WC_Product_Variable ) {
		return '';
	}

	$variationsAttrs = $parent->get_variation_attributes();

	if ( empty( $variationsAttrs ) ) {
		return '';
	}

	$pairs = array();

	foreach ( $variationsAttrs as $key => $slug ) {
		$taxonomy = str_replace( 'attribute_', '', $key );
		$label    = wc_attribute_label( $taxonomy, $parent );
		$value    = $product->get_attribute( $taxonomy );
		if ( '' === $value ) {
			continue;
		}

		$pairs[] = array(
			'taxonomy' => $taxonomy,
			'label'    => $label,
			'value'    => $value,
		);
	}

	if ( ! $pairs ) {
		return '';
	}
	$lines = array();
	foreach ( $pairs as $p ) {
		$lines[] = $p['label'] . ' - ' . $p['value'];
	}

	$separator = apply_filters( 'rgnmhn_customer_wishlist_list_attributes_separator', ', ' );

	$default = implode( $separator, $lines );

	/**
	 * Filters the wishlist attributes string before output.
	 *
	 * Use this to completely change formatting or inject HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string                $output  Default output (e.g., "Color - Blue, Size - M").
	 * @param WC_Product            $product The current product object.
	 * @param WC_Product_Variable   $parent  The parent variable product (if any).
	 * @param string[]              $lines   List of "Label - Value" strings.
	 */
	$output = apply_filters( 'rgnmhn_customer_wishlist_list_attributes', $default, $product, $parent, $pairs );

	return (string) $output;
}

/**
 * Returns the wishlist identifier for the current user or guest.
 *
 * @return array Associative array with 'type' (user|token) and 'id'.
 */
function wishListIdentifier() {
	if ( is_user_logged_in() ) {
		return array(
			'type' => 'user',
			'id'   => get_current_user_id(),
		);
	} else {
		$token = '';
		if ( isset( $_COOKIE[ RGNMHN_WISHLIST_COOKIE ] ) ) {
			$token = sanitize_text_field( wp_unslash( $_COOKIE[ RGNMHN_WISHLIST_COOKIE ] ) );
		}

		if ( empty( $token ) ) {
			$token = wp_generate_uuid4();

			$days = ProductOptions::getGuestUserExpiryDate();
			wc_setcookie( RGNMHN_WISHLIST_COOKIE, $token, time() + (int) $days * DAY_IN_SECONDS );
		}
		return array(
			'type' => 'token',
			'id'   => $token,
		);
	}
}

/**
 * Get the variation ID that matches the default attributes.
 *
 * @param array $defaultAttributes The default attributes of the variable product.
 * @param array $availableVariations The list of available variations (each variation is an associative array).
 * @return int|null Returns the variation ID if a match is found, otherwise null.
 */
function rgnmhnCustomerWishlistGetDefaultVariationID( array $defaultAttributes, array $availableVariations ): ?int {
	foreach ( $availableVariations as $variation ) {
		$match = true;

		foreach ( $defaultAttributes as $attributeName => $defaultValue ) {
			$attributeKey = 'attribute_' . $attributeName;
			if ( ! isset( $variation['attributes'][ $attributeKey ] ) || $variation['attributes'][ $attributeKey ] !== $defaultValue ) {
				$match = false;
				break;
			}
		}

		if ( $match ) {
			return (int) $variation['variation_id'];
		}
	}

	return null;
}

/**
 * Renders a component template file and extracts variables for use in the template.
 *
 * @param string $fileName  The template file name.
 * @param array  $variables Variables to extract and use in the template.
 */
function rgnmhnCustomerWishlistRenderComponent( string $fileName, array $variables = array() ) {
	$path = RGNMHN_CUSTOMER_WISHLIST_PATH . 'admin/templates/components/' . $fileName;
	if ( file_exists( $path ) ) {
		include $path;
	} else {
		echo esc_html( 'File not found.' );
	}
}

/**
 * Allowed SVG Tags
 */
function rgnmhnCustomerWishlistAllowedSVGTag() {
	$allowedSVGTags = array(
		'svg'    => array(
			'xmlns'               => true,
			'viewbox'             => true,
			'width'               => true,
			'height'              => true,
			'fill'                => true,
			'stroke'              => true,
			'stroke-width'        => true,
			'class'               => true,
			'style'               => true,
			'preserveAspectRatio' => true,
		),
		'path'   => array(
			'd'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'style'        => true,
			'class'        => true,
		),
		'g'      => array(
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'style'        => true,
			'class'        => true,
		),
		'circle' => array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
			'style'        => true,
			'class'        => true,
		),
	);
	return $allowedSVGTags;
}

/**
 * Wishlist Icons
 */
function rgnmhnCustomerWishlistGetIcons() {
	$icons = array(
		'icon-1' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>
                </svg>',
		'icon-2' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z"/>
                </svg>',
		'icon-3' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
                </svg>',
		'icon-4' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
                </svg>',
		'icon-5' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                  <path d="M11.315 10.014a.5.5 0 0 1 .548.736A4.5 4.5 0 0 1 7.965 13a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .548-.736h.005l.017.005.067.015.252.055c.215.046.515.108.857.169.693.124 1.522.242 2.152.242s1.46-.118 2.152-.242a27 27 0 0 0 1.109-.224l.067-.015.017-.004.005-.002zM4.756 4.566c.763-1.424 4.02-.12.952 3.434-4.496-1.596-2.35-4.298-.952-3.434m6.488 0c1.398-.864 3.544 1.838-.952 3.434-3.067-3.554.19-4.858.952-3.434"/>
                </svg>',
		'icon-6' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M4.756 4.566c.763-1.424 4.02-.12.952 3.434-4.496-1.596-2.35-4.298-.952-3.434m6.559 5.448a.5.5 0 0 1 .548.736A4.5 4.5 0 0 1 7.965 13a4.5 4.5 0 0 1-3.898-2.25.5.5 0 0 1 .548-.736h.005l.017.005.067.015.252.055c.215.046.515.108.857.169.693.124 1.522.242 2.152.242s1.46-.118 2.152-.242a27 27 0 0 0 1.109-.224l.067-.015.017-.004.005-.002zm-.07-5.448c1.397-.864 3.543 1.838-.953 3.434-3.067-3.554.19-4.858.952-3.434z"/>
                </svg>',
		'icon-7' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"/>
                </svg>',
		'icon-8' => '<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                </svg>',
	);

	$additionalIcon = (array) apply_filters( 'rgnmhn_customer_wishlist_add_custom_icon_for_single_product', array() );
	$icons          = $icons + $additionalIcon;
	return $icons;
}
