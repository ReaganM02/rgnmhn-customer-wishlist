<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) {
	?>
	<div class="rgnmhn-p-2 rgnmhn-border-l-2 rgnmhn-border-green-600 rgnmhn-border rgnmhn-bg-green-100 rgnmhn-text-green-700 rgnmhn-mb-4 rgnmhn-shadow rgnmhn-text-base rgnmhn-rounded">
	<?php echo esc_html( __( 'Settings Successfully Saved!', 'rgnmhn-customer-wishlist' ) ); ?>
	</div>
	<?php
}
