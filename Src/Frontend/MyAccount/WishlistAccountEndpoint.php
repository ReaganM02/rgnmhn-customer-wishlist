<?php

namespace ReaganMahinay\RGNCustomerWishlist\Frontend\MyAccount;

use ReaganMahinay\RGNCustomerWishlist\Models\WishlistModel;
use ReaganMahinay\RGNCustomerWishlist\MyAccountOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

/**
 * WishlistAccountEndpoint
 *
 * Adds a custom “Wishlist” endpoint to the WooCommerce My Account area.
 * - Registers the endpoint (based on the user-configurable slug).
 * - Inserts a new menu item in the My Account navigation.
 * - Renders the Wishlist page content (empty state or table list).
 * - Enqueues styles/scripts only when on the endpoint page.
 *
 *
 * @since   1.0.0
 */

class WishlistAccountEndpoint
{
  /**
   * User-configured endpoint slug (e.g., 'wishlist').
   *
   * @var string
   */
  private $slug;

  /**
   * Constructor.
   *
   * Initializes the endpoint slug and registers hooks.
   *
   * @return void
   */
  public function __construct()
  {
    // 
    $this->slug = MyAccountOptions::getSlug(); // Expecting a sanitized slug.
    $this->hooks();
  }


  /**
   * Register WordPress/WooCommerce hooks for:
   * - Endpoint registration
   * - Asset enqueueing
   * - Menu injection
   * - Query var registration
   * - Endpoint content callback
   *
   * @return void
   */
  public function hooks()
  {
    add_rewrite_endpoint($this->slug, EP_ROOT | EP_PAGES);

    // Only enqueue front-end assets when appropriate.
    add_action('wp_enqueue_scripts', [$this, 'assets']);

    // Register the menu item in My Account navigation (late priority to place before logout).
    add_action('woocommerce_account_menu_items', [$this, 'addNewMenu'], 99, 1);

    // Add our endpoint to WooCommerce query vars.
    add_action('woocommerce_get_query_vars', [$this, 'addQueryVar']);

    // Render page content when WooCommerce resolves this endpoint.
    add_action('woocommerce_account_' . $this->slug . '_endpoint', [$this, 'pageContent']);

    // Internal hook: outputs the wishlist markup (used inside templates).
    add_action('rgnmhn_wishlist_my_account_content', [$this, 'wishlistContent']);
  }

  /**
   * Register our endpoint slug as a WooCommerce query var.
   *
   * @param  array $vars Existing query vars.
   * @return array        Modified query vars including our slug.
   */
  public function addQueryVar(array $vars)
  {
    $vars[$this->slug] = $this->slug;
    return $vars;
  }


  /**
   * Insert the “Wishlist” item in My Account navigation (before Logout).
   *
   * @param  array $items Key/value of endpoint => label.
   * @return array
   */
  public function addNewMenu(array $items)
  {
    $title = MyAccountOptions::getMenuTitle();

    $logout = $items['customer-logout'];
    unset($items['customer-logout']);
    $items[$this->slug] = $title;
    $items['customer-logout'] = $logout;

    return $items;
  }

  /**
   * Emits the wishlist content (empty state or table) into the template.
   *
   * @return void
   */
  public function wishlistContent()
  {
    $wishlist = new WishlistModel();
    $list = $wishlist->getWishlistEntriesByUserID(get_current_user_id());

    if (empty($list)) {
      $args = [
        'empty-message' => MyAccountOptions::getEmptyContentMessage()
      ];
      echo wp_kses_post(self::renderTemplateOnce('my-account/empty.php', $args));
    } else {
      $args = [
        'content-title' => MyAccountOptions::getContentTitle(),
        'list' => $list
      ];
      echo wp_kses_post(self::renderTemplateOnce('my-account/table.php', $args));
    }
  }

  /**
   * Renders the outer wrapper template for the endpoint page.
   * The template should call `do_action('rgnmhn_wishlist_my_account_content')`
   * where the inner content should appear.
   *
   * @return void
   */
  public function pageContent()
  {
    self::renderTemplateOnce('my-account/wishlist.php');
  }

  /**
  /**
   * Require a template file and make $data available in local scope.
   *
   * @param  string              $path Relative path under plugin `templates/`.
   * @param  array<string,mixed> $data Variables to extract into template scope.
   * @return void
   */
  private static function renderTemplateOnce(string $path, $data = [])
  {
    require_once RGNMHN_CUSTOMER_WISHLIST_PATH . 'templates/' . $path;
  }

  /**
   * Enqueue styles/scripts for the endpoint page only.
   *
   * Loads:
   * - CSS: /assets/css/rgnmhn-my-account-wishlist.css
   * - JS : /assets/js/rgnmhn-customer-wishlist-my-account.js (depends on jQuery & wc-cart-fragments)
   *
   * Localizes:
   * - AJAX endpoint URL
   * - Nonces for add/remove actions
   *
   * @return void
   */
  public function assets()
  {
    global $wp;

    // Only enqueue when on the My Account page AND our endpoint is present in the URL.
    if (is_account_page() && array_key_exists(MyAccountOptions::getSlug(), $wp->query_vars)) {

      // Ensure WooCommerce cart fragments is available (if registered by WC).
      if (wp_script_is('wc-cart-fragments', 'registered')) {
        wp_enqueue_script('wc-cart-fragments');
      }

      wp_enqueue_style('rgnmhn-my-account-wishlist', RGNMHN_CUSTOMER_WISHLIST_URL . 'assets/css/rgnmhn-my-account-wishlist.css', [], RGNMHN_CUSTOMER_WISHLIST_VERSION);
      wp_enqueue_script('rgnmhn-my-account-wishlist-script', RGNMHN_CUSTOMER_WISHLIST_URL . 'assets/js/rgnmhn-customer-wishlist-my-account.js', ['jquery', 'wc-cart-fragments'], RGNMHN_CUSTOMER_WISHLIST_VERSION, true);

      // Provide AJAX URL and nonces to the script.
      wp_localize_script('rgnmhn-my-account-wishlist-script', 'rgnmhn_wishlist_my_account', [
        'url' => admin_url('admin-ajax.php'),
        'nonces' => [
          'add' => wp_create_nonce('rgnmhn_add_to_cart_security'),
          'remove' => wp_create_nonce('rgnmhn_remove_from_wishlist_security')
        ]
      ]);
    }
  }
}
