<?php
/**
 * Show add to wishlist button in product page.
 *
 * @package rgnmhn-customer-wishlist
 */

// Exit.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<template id="rgnmhn-template-add-wishlist">
	<div>
	<?php
	/**
	 * Fires before the "Add to Wishlist" button on single product template.
	 *
	 * This hook should only be used to output safe HTML. Developers are responsible for escaping and sanitizing their own output.
	 *
	 * @since 1.0.0
	 */
	do_action( 'rgnmhn_wishlist_single_product_before_button' )
	?>
	<button class="rgnmhn-add-to-wishlist">
		<span class="rgnmhn-wishlist-icon">
		<?php echo wp_kses( $data['icon'], rgnmhnCustomerWishlistAllowedSVGTag() ); ?>
		</span>
		<?php if ( ! empty( $data['label'] ) ) : ?>
		<span class="rgnmhn-add-to-wishlist-label">
			<?php echo esc_html( $data['label'] ); ?>
		</span>
		<?php endif; ?>
	</button>
	<?php
	/**
	 * Fires after the "Add to Wishlist" button on single product template.
	 *
	 * This hook should only be used to output safe HTML. Developers are responsible for escaping and sanitizing their own output.
	 *
	 * @since 1.0.0
	 */
	do_action( 'rgnmhn_wishlist_single_product_after_button' )
	?>
	</div>
</template>
