<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form class="rgnmhn-m-6 rgnmhn-w-1/2" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<section class="rgnmhn-p-4 rgnmhn-bg-white rgnmhn-shadow-md rgnmhn-rounded">
	<h1 class="rgnmhn-text-zinc-700 rgnmhn-font-bold rgnmhn-uppercase rgnmhn-tracking-wide rgnmhn-text-lg">
		<?php echo esc_html( __( 'Product Settings', 'rgnmhn-customer-wishlist' ) ); ?>
	</h1>
	<div class="rgnmhn-block rgnmhn-space-y-4 rgnmhn-mt-6">
		<?php foreach ( $settings as $key => $setting ) : ?>
		<div>
			<?php
			rgnmhnCustomerWishlistRenderComponent( $setting['type'] . '.php', $setting );
			?>
		</div>
		<?php endforeach; ?>
	</div>
	<div class="rgnmhn-mt-2">
		<div>
		<?php echo esc_html( __( 'Single Product Shortcode:', 'rgnmhn-customer-wishlist' ) ); ?>
		</div>
		<pre class="rgnmhn-bg-zinc-100 rgnmhn-w-max rgnmhn-italic rgnmhn-px-4 rgnmhn-py-2">rgnmhn_customer_wishlist_single_product</pre>
	</div>
	<input type="hidden" name="action" value="rgnmhn_wishlist_save_settings">
	<?php wp_nonce_field( 'rgnmhn_wishlist_save_settings_security' ); ?>
	<div class="rgnmhn-mt-10 rgnmhn-mb-4">
		<?php rgnmhnCustomerWishlistRenderComponent( 'success.php' ); ?>
		<button class="rgnmhn-bg-blue-600 rgnmhn-text-white rgnmhn-px-10 rgnmhn-py-4 rgnmhn-rounded hover:rgnmhn-bg-blue-700 rgnmhn-text-base rgnmhn-uppercase rgnmhn-font-bold rgnmhn-tracking-wide">
		<?php echo esc_html( __( 'Save Settings', 'rgnmhn-customer-wishlist' ) ); ?>
		</button>
	</div>
	</section>
</form>
