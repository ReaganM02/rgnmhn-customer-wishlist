<?php
/**
 * Display if wishlist content is empty.
 *
 * @package rgnmhn-customer-wishlist
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div>
	<h4 class="rgnmhn-text-center"><?php echo esc_attr( $data['empty-message'] ); ?></h4>
	<?php
	$args = array(
		'classes' => array( 'rgnmhn-bg-blue-500', 'rgnmhn-px-4', 'rgnmhn-py-2', 'rgnmhn-text-white', 'rgnmhn-block', 'rgnmhn-w-max', 'rgnmhn-m-auto', 'rgnmhn-mt-4' ),
		'link'    => '/shop',
		'text'    => __( 'Go to Shop', 'rgnmhn-customer-wishlist' ),
	);

	$args = apply_filters( 'rgnmhn_customer_wishlist_empty_link_args', $args );

	// Sanitize classes and add a fallback '' if it returns nothing.
	$classes = array_map(
		function ( $c ) {
			return sanitize_html_class( $c, '' );
		},
		(array) $args['classes']
	);

	$classes = array_filter( $classes );
	$classes = implode( ' ', $classes );


	$html = sprintf( '<a class="%s" href="%s">%s</a>', esc_attr( $classes ), esc_url( $args['link'] ), esc_html( $args['text'] ) );
	echo wp_kses_post( apply_filters( 'rgnmhn_customer_wishlist_empty_link_html', $html, $args ) );
	?>
</div>
