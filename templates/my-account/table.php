<?php
/**
 * Displays the wishlist section in a user's account.
 *
 * @package rgnmhn-customer-wishlist
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div>
	<h2 class="rgnmhn-text-center rgnmhn-mb-4"><?php echo esc_attr( $data['content-title'] ); ?></h2>
	<div class="rgnmhn-overflow-x-auto rgnmhn-border rgnmhn-border-slate-200 rgnmhn-shadow-sm">
	<table class="rgnmhn-min-w-full rgnmhn-table-auto rgnmhn-text-base rgnmhn-text-slate-700">
		<caption class="rgnmhn-sr-only"><?php echo esc_attr( $data['content-title'] ); ?></caption>
		<thead class="rgnmhn-sticky rgnmhn-top-0 rgnmhn-z-10 rgnmhn-bg-slate-50 rgnmhn-text-slate-900">
		<tr>
			<th scope="col" class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-left rgnmhn-font-semibold rgnmhn-product-title">
			<?php echo esc_html( __( 'Image', 'rgnmhn-customer-wishlist' ) ); ?>
			</th>
			<th scope="col" class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-left rgnmhn-font-semibold rgnmhn-product-title">
			<?php echo esc_html( __( 'Product', 'rgnmhn-customer-wishlist' ) ); ?>
			</th>
			<th scope="col" class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-left rgnmhn-font-semibold rgnmhn-hidden sm:rgnmhn-table-cell">
			<?php echo esc_html( __( 'Date Added', 'rgnmhn-customer-wishlist' ) ); ?>
			</th>
			<th scope="col" class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-left rgnmhn-font-semibold rgnmhn-hidden sm:rgnmhn-table-cell">
			<?php echo esc_html( __( 'Price', 'rgnmhn-customer-wishlist' ) ); ?>
			</th>
			<th scope="col" class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-left rgnmhn-font-semibold">
			<?php echo esc_html( __( 'Status', 'rgnmhn-customer-wishlist' ) ); ?>
			</th>
			<th scope="col" class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-right rgnmhn-font-semibold">
			<?php echo esc_html( __( 'Actions', 'rgnmhn-customer-wishlist' ) ); ?>
			</th>
		</tr>
		</thead>
		<tbody class="rgnmhn-divide-y rgnmhn-divide-slate-100 rgnmhn-bg-white">
		<!-- Row -->
		<?php
		foreach ( $data['list'] as $key => $item ) :
			$product = wc_get_product( $item['product_id'] );
			if ( ! $product ) {
				return;
			}
			?>
			<tr class="hover:rgnmhn-bg-slate-50">
			<td class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-4">
				<!-- Display thumbnail -->
				<?php echo wp_kses_post( $product->get_image() ); ?>
			</td>
			<td class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-4">
				<div class="rgnmhn-block">
				<div class="rgnmhn-font-medium rgnmhn-text-slate-900 rgnmhn-w-40">
					<?php
					/**
					 * Available filters:
					 * rgn_wishlist_product_title_args - Modify title attributes
					 * rgn_customer_wishlist_product_title - Modify product title
					 */
					echo wp_kses_post( rgnmhnCustomerWishlistFormatTitle( $product ) );
					?>
				</div>
				<?php
				/**
				 * Available filters:
				 * rgn_customer_wishlist_list_attributes_separator - Modify the separator.
				 * rgn_customer_wishlist_list_attributes - Modify how the attributes are being displayed.
				 */
				echo wp_kses_post( rgnmhnCustomerWishlistFormattedAttributes( $product ) );
				?>
				</div>
			</td>
			<td class="rgnmhn-hidden sm:rgnmhn-table-cell rgnmhn-whitespace-nowrap rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6">
				<?php
				/**
				 * Available filters:
				 * rgn_customer_wishlist_format_date
				 */
				echo wp_kses_post( rgnmhnCustomerWishlistFormattedDate( $item['date_created'] ) );
				?>
			</td>
			<td class="rgnmhn-hidden sm:rgnmhn-table-cell rgnmhn-whitespace-nowrap rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6">
				<?php echo wp_kses_post( $product->get_price_html() ); ?>
			</td>
			<td class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-stock-status">
				<?php
				/**
				 * Available filters:
				 *  rgn_wishlist_stock_status_args - For filtering args like classes, label, status
				 *  rgn_wishlist_stock_status_html - Display the stock status HTML
				 */
				echo wp_kses_post( rgnmhnCustomerWishlistStockStatus( $product ) )
				?>
			</td>
			<td class="rgnmhn-px-4 rgnmhn-py-3 sm:rgnmhn-px-6 rgnmhn-text-right">
				<div class="rgnmhn-inline-flex rgnmhn-gap-2">
				<button
					type="button"
					class="
					rgnmhn-inline-flex 
					rgnmhn-items-center 
					rgnmhn-rounded-lg 
					rgnmhn-border 
					rgnmhn-border-red-300 
					rgnmhn-bg-red-100 
					rgnmhn-px-3 
					rgnmhn-text-red-400 
					rgnmhn-py-1.5 
					rgnmhn-text-xs 
					rgnmhn-font-medium 
					hover:rgnmhn-bg-slate-50 
					focus:rgnmhn-outline-none 
					focus:rgnmhn-ring-2 
					focus:rgnmhn-ring-slate-400 
					rgnmhn-wishlist-delete-btn"
					data-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<?php
					$label  = __( 'Delete', 'rgnmhn-customer-wishlist' );
					$output = apply_filters( 'rgnmhn_wishlist_list_delete_text_btn', $label );
					echo esc_html( $output );
					?>
				</button>
				<a
					href="<?php echo esc_url( $product->get_permalink() ); ?>"
					class="rgnmhn-inline-flex rgnmhn-items-center rgnmhn-rounded-lg rgnmhn-border rgnmhn-border-slate-300 rgnmhn-px-3 rgnmhn-py-1.5 rgnmhn-text-xs rgnmhn-font-medium hover:rgnmhn-bg-slate-50 focus:rgnmhn-outline-none focus:rgnmhn-ring-2 focus:rgnmhn-ring-slate-400">
					<?php
					$label  = __( 'View', 'rgnmhn-customer-wishlist' );
					$output = apply_filters( 'rgnmhn_wishlist_list_view_text_btn', $label );
					echo esc_html( $output );
					?>
				</a>
				<button
					type="button"
					class="rgnmhn-inline-flex rgnmhn-items-center rgnmhn-rounded-lg rgnmhn-border rgnmhn-border-blue-500 rgnmhn-px-3 rgnmhn-py-1.5 rgnmhn-text-xs rgnmhn-font-medium rgnmhn-bg-blue-500 rgnmhn-text-white hover:rgnmhn-bg-blue-600 focus:rgnmhn-outline-none focus:rgnmhn-ring-2 focus:rgnmhn-ring-blue-700 rgnmhn-w-max disabled:rgnmhn-opacity-50 disabled:rgnmhn-cursor-not-allowed disabled:rgnmhn-pointer-events-none rgnmhn-add-to-cart"
					<?php echo ! $product->is_purchasable() || ( ! $product->is_in_stock() && ! $product->backorders_allowed() ) ? 'disabled="true"' : ''; ?>
					data-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<?php
					$label  = __( 'Add to Cart', 'rgnmhn-customer-wishlist' );
					$output = apply_filters( 'rgnmhn_wishlist_list_add_to_cart_text_btn', $label );
					echo esc_html( $output );
					?>
				</button>
				</div>
			</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	</div>
</div>
