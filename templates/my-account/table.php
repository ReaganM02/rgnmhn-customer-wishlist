<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<div>
  <h2 class="rgn-text-center rgn-mb-4"><?php echo esc_attr($data['content-title']) ?></h2>
  <div class="rgn-overflow-x-auto rgn-border rgn-border-slate-200 rgn-shadow-sm">
    <table class="rgn-min-w-full rgn-table-auto rgn-text-base rgn-text-slate-700">
      <caption class="rgn-sr-only"><?php echo esc_attr($data['content-title']) ?></caption>
      <thead class="rgn-sticky rgn-top-0 rgn-z-10 rgn-bg-slate-50 rgn-text-slate-900">
        <tr>
          <th scope="col" class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-left rgn-font-semibold rgn-product-title">
            <?php echo __('Image', 'rgn-customer-wishlist') ?>
          </th>
          <th scope="col" class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-left rgn-font-semibold rgn-product-title">
            <?php echo __('Product', 'rgn-customer-wishlist'); ?>
          </th>
          <th scope="col" class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-left rgn-font-semibold rgn-hidden sm:rgn-table-cell">
            <?php echo __('Date Added', 'rgn-customer-wishlist') ?>
          </th>
          <th scope="col" class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-left rgn-font-semibold rgn-hidden sm:rgn-table-cell">
            <?php echo __('Price', 'rgn-customer-wishlist') ?>
          </th>
          <th scope="col" class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-left rgn-font-semibold">
            <?php echo __('Status', 'rgn-customer-wishlist') ?>
          </th>
          <th scope="col" class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-right rgn-font-semibold">
            <?php echo __('Actions', 'rgn-customer-wishlist') ?>
          </th>
        </tr>
      </thead>
      <tbody class="rgn-divide-y rgn-divide-slate-100 rgn-bg-white">
        <!-- Row -->
        <?php foreach ($data['list'] as $key => $item):
          $product = wc_get_product($item['product_id']);
        ?>
          <tr class="hover:rgn-bg-slate-50">
            <td class="rgn-px-4 rgn-py-3 sm:rgn-px-4">
              <!-- Display thumbnail -->
              <?php echo wp_kses_post($product->get_image()); ?>
            </td>
            <td class="rgn-px-4 rgn-py-3 sm:rgn-px-4">
              <div class="rgn-block">
                <div class="rgn-font-medium rgn-text-slate-900 rgn-w-40">
                  <?php
                  /**
                   * Available filters: 
                   * rgn_wishlist_product_title_args - Modify title attributes
                   * rgn_customer_wishlist_product_title - Modify product title
                   */
                  echo wp_kses_post(rgnFormatWishlistTitle($product));
                  ?>
                </div>
                <?php
                /**
                 * Available filters:
                 * rgn_customer_wishlist_list_attributes_separator - Modify the separator.
                 * rgn_customer_wishlist_list_attributes - Modify how the attributes are being displayed.
                 */
                echo wp_kses_post(rgnFormattedAttributes($product));
                ?>
              </div>
            </td>
            <td class="rgn-hidden sm:rgn-table-cell rgn-whitespace-nowrap rgn-px-4 rgn-py-3 sm:rgn-px-6">
              <?php
              /**
               * Available filters:
               * rgn_customer_wishlist_format_date
               */
              echo wp_kses_post(rgnFormattedWishlistDate($item['date_created']));
              ?>
            </td>
            <td class="rgn-hidden sm:rgn-table-cell rgn-whitespace-nowrap rgn-px-4 rgn-py-3 sm:rgn-px-6">
              <?php echo $product->get_price_html(); ?>
            </td>
            <td class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-stock-status">
              <?php
              /**
               * Available filters: 
               *  rgn_wishlist_stock_status_args - For filtering args like classes, label, status
               *  rgn_wishlist_stock_status_html - Display the stock status HTML 
               */
              echo rgnWishlistStockStatus($product)
              ?>
            </td>
            <td class="rgn-px-4 rgn-py-3 sm:rgn-px-6 rgn-text-right">
              <div class="rgn-inline-flex rgn-gap-2">
                <button
                  type="button"
                  class="
                  rgn-inline-flex 
                  rgn-items-center 
                  rgn-rounded-lg 
                  rgn-border 
                  rgn-border-red-300 
                  rgn-bg-red-100 
                  rgn-px-3 
                  rgn-text-red-400 
                  rgn-py-1.5 
                  rgn-text-xs 
                  rgn-font-medium 
                  hover:rgn-bg-slate-50 
                  focus:rgn-outline-none 
                  focus:rgn-ring-2 
                  focus:rgn-ring-slate-400 
                  rgn-wishlist-delete-btn"
                  data-id="<?php echo esc_attr($product->get_id()) ?>">
                  <?php
                  $label = __('Delete', 'rgn-customer-wishlist');
                  $output = apply_filters('rgn_wishlist_list_delete_text_btn', $label);
                  echo esc_html($output);
                  ?>
                </button>
                <a
                  href="<?php echo esc_url($product->get_permalink()) ?>"
                  class="rgn-inline-flex rgn-items-center rgn-rounded-lg rgn-border rgn-border-slate-300 rgn-px-3 rgn-py-1.5 rgn-text-xs rgn-font-medium hover:rgn-bg-slate-50 focus:rgn-outline-none focus:rgn-ring-2 focus:rgn-ring-slate-400">
                  <?php
                  $label = __('View', 'rgn-customer-wishlist');
                  $output = apply_filters('rgn_wishlist_list_view_text_btn', $label);
                  echo esc_html($output);
                  ?>
                </a>
                <button
                  type="button"
                  class="rgn-inline-flex rgn-items-center rgn-rounded-lg rgn-border rgn-border-blue-500 rgn-px-3 rgn-py-1.5 rgn-text-xs rgn-font-medium rgn-bg-blue-500 rgn-text-white hover:rgn-bg-blue-600 focus:rgn-outline-none focus:rgn-ring-2 focus:rgn-ring-blue-700 rgn-w-max disabled:rgn-opacity-50 disabled:rgn-cursor-not-allowed disabled:rgn-pointer-events-none rgn-add-to-cart"
                  <?php echo !$product->is_purchasable() || (!$product->is_in_stock() && !$product->backorders_allowed()) ? 'disabled="true"' : '' ?>
                  data-id="<?php echo esc_attr($product->get_id()) ?>">
                  <?php
                  $label = __('Add to Cart', 'rgn-customer-wishlist');
                  $output = apply_filters('rgn_wishlist_list_add_to_cart_text_btn', $label);
                  echo esc_html($output);
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