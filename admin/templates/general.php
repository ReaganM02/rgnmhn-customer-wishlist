<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="rgnmhn-m-6 rgnmhn-w-1/2" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<section class="rgnmhn-p-4 rgnmhn-bg-white rgnmhn-shadow-md rgnmhn-rounded">
	<h1 class="rgnmhn-text-zinc-700 rgnmhn-font-bold rgnmhn-uppercase rgnmhn-tracking-wide rgnmhn-text-lg">
		<?php echo esc_html( __( 'General Settings', 'rgnmhn-customer-wishlist' ) ); ?>
	</h1>
	<div class="rgnmhn-block rgnmhn-space-y-4 rgnmhn-mt-6">
		<?php
		foreach ( $settings as $setting ) {
			rgnmhnCustomerWishlistRenderComponent( $setting['type'] . '.php', $setting );
		}
		?>
	</div>

	<div class="rgnmhn-bg-yellow-100 rgnmhn-border rgnmhn-border-yellow-300 rgnmhn-rounded rgnmhn-p-2 rgnmhn-mt-10 rgnmhn-text-yellow-600" role="alert">
		<strong><?php esc_html_e( 'Warning:', 'rgnmhn-customer-wishlist' ); ?></strong>
		<?php
		echo wp_kses_post(
			sprintf(
			/* translators: 1: opening <strong> tag, 2: closing </strong> tag. */
				__( 'If this checkbox is checked, %1$sall saved settings and default values will be permanently deleted%2$s when the plugin is uninstalled.', 'rgnmhn-customer-wishlist' ),
				'<strong>',
				'</strong>'
			)
		);
		?>
	</div>
	<input type="hidden" name="action" value="rgnmhn_wishlist_general_settings">
	<?php wp_nonce_field( 'rgnmhn_wishlist_general_settings' ); ?>
	<div class="rgnmhn-mt-10 rgnmhn-mb-4">
		<?php rgnmhnCustomerWishlistRenderComponent( 'success.php' ); ?>
		<button class="rgnmhn-bg-blue-600 rgnmhn-text-white rgnmhn-px-10 rgnmhn-py-4 rgnmhn-rounded hover:rgnmhn-bg-blue-700 rgnmhn-text-base rgnmhn-uppercase rgnmhn-font-bold rgnmhn-tracking-wide">
		<?php echo esc_html( __( 'Save Settings', 'rgnmhn-customer-wishlist' ) ); ?>
		</button>
	</div>
	</section>
</form>
