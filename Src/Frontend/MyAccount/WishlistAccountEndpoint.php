<?php

namespace Src\Frontend\MyAccount;

use Src\Models\WishlistModel;
use Src\MyAccountOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class WishlistAccountEndpoint
{
  private $slug;
  public function __construct()
  {
    $this->slug = MyAccountOptions::getSlug();
    $this->hooks();
  }

  public function hooks()
  {
    add_rewrite_endpoint($this->slug, EP_ROOT | EP_PAGES);

    add_action('wp_enqueue_scripts', [$this, 'assets']);

    // Register my account slug based on the give user settings
    add_action('woocommerce_account_menu_items', [$this, 'addNewMenu'], 99, 1);

    add_action('woocommerce_get_query_vars', [$this, 'addQueryVar']);

    add_action('woocommerce_account_' . $this->slug . '_endpoint', [$this, 'pageContent']);


    add_action('rgn_wishlist_my_account_content', [$this, 'wishlistContent']);
  }

  public function addQueryVar(array $vars)
  {
    $vars[$this->slug] = $this->slug;
    return $vars;
  }

  public function addNewMenu($items)
  {
    $title = MyAccountOptions::getMenuTitle();

    $logout = $items['customer-logout'];
    unset($items['customer-logout']);
    $items[$this->slug] = $title;
    $items['customer-logout'] = $logout;

    return $items;
  }

  public function wishlistContent()
  {
    $wishlist = new WishlistModel();
    $list = $wishlist->getWishlistEntriesByUserID(get_current_user_id());

    if (empty($list)) {
      $args = [
        'empty-message' => MyAccountOptions::getEmptyContentMessage()
      ];
      echo self::renderTemplateOnce('my-account/empty.php', $args);
    } else {
      $args = [
        'content-title' => MyAccountOptions::getContentTitle(),
        'list' => $list
      ];
      echo self::renderTemplateOnce('my-account/table.php', $args);
    }
  }

  public function pageContent()
  {
    self::renderTemplateOnce('my-account/wishlist.php');
  }

  /**
   * Require a template file and make $data available in local scope.
   *
   *
   * @since 1.0.0
   * @param string               $path Relative path under plugin `templates/`.
   * @param array<string,mixed>  $data Variables to extract into template scope.
   * @return void
   */
  private static function renderTemplateOnce(string $path, $data = [])
  {
    require_once RGN_CUSTOMER_WISHLIST_PATH . 'templates/' . $path;
  }

  public function assets()
  {
    global $wp;
    if (is_account_page() && array_key_exists(MyAccountOptions::getSlug(), $wp->query_vars)) {

      if (wp_script_is('wc-cart-fragments', 'registered')) {
        wp_enqueue_script('wc-cart-fragments');
      }

      wp_enqueue_style('rgn-my-account-wishlist', RGN_CUSTOMER_WISHLIST_URL . 'assets/css/rgn-my-account-wishlist.css', [], RGN_CUSTOMER_WISHLIST_VERSION);
      wp_enqueue_script('rgn-my-account-wishlist-script', RGN_CUSTOMER_WISHLIST_URL . 'assets/js/rgn-customer-wishlist-my-account.js', ['jquery', 'wc-cart-fragments'], RGN_CUSTOMER_WISHLIST_VERSION, true);

      wp_localize_script('rgn-my-account-wishlist-script', 'rgn_wishlist_my_account', [
        'url' => admin_url('admin-ajax.php'),
        'nonces' => [
          'add' => wp_create_nonce('rgn_add_to_cart_security'),
          'remove' => wp_create_nonce('rgn_remove_from_wishlist_security')
        ]
      ]);
    }
  }
}
