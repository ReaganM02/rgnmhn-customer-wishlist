<?php
/**
 * This file is a template for the "Wishlist" section in a user's account area within the "rgnmhn-customer-wishlist" WordPress plugin. It outputs a div container for the wishlist and triggers the 'rgnmhn_wishlist_my_account_content' action hook to display wishlist content dynamically.
 *
 * @package rgnmhn-customer-wishlist
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// 02Aug1997M
?>
<div id="rgnmhn-my-account-wishlist">
	<?php do_action( 'rgnmhn_wishlist_my_account_content' ); ?>
</div>
