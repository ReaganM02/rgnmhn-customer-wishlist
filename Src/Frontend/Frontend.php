<?php

namespace Src\Frontend;

use Src\Models\WishlistModel;
use Src\MyAccountOptions;
use WC_Data_Store;
use WC_Product;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class Frontend
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('wp_enqueue_scripts', [$this, 'assets']);
    add_action('woocommerce_after_add_to_cart_form', [$this, 'displayWishlistAfterAddToCartForm'], 35);
    add_action('woocommerce_get_stock_html', [$this, 'displayWishListAfterOutOfStocks'], 10, 2);
    add_action('woocommerce_available_variation', [$this, 'wishlistInVariation'], 10, 3);

    add_action('woocommerce_account_menu_items', [$this, 'addMyAccountMenu'], 99, 1);
    add_action('woocommerce_account_' . MyAccountOptions::getSlug() . '_endpoint', [$this, 'myAccountWishlistContent']);

    // Wishlist table
    add_action('rgn_wishlist_my_account_table', [$this, 'myAccountWishlistTableList']);
    add_action('rgn_wishlist_my_account_content', [$this, 'myAccountContent']);
  }

  /**
   * Displays the my account content
   */
  public function myAccountContent()
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


  public function wishlistInVariation($variationData, $product, $variation)
  {
    $variationData['rgn-wishlist'] = $this->wishlistHTMLContent($variationData['variation_id']);
    return $variationData;
  }

  public function myAccountWishlistContent()
  {
    $wishlist = new WishlistModel();
    $list = $wishlist->getWishlistEntriesByUserID(get_current_user_id());
    $args = [
      'list' => $list,
      'content-title' => MyAccountOptions::getContentTitle(),
      'content-empty-message' => MyAccountOptions::getEmptyContentMessage()
    ];
    echo $this->renderTemplate('my-account/wishlist.php', $args);
  }


  public function displayWishlistAfterAddToCartForm()
  {
    echo $this->wishlistHTMLContent(get_the_ID());
  }

  public function addMyAccountMenu($items)
  {
    $slug = MyAccountOptions::getSlug();
    $title = MyAccountOptions::getMenuTitle();

    $logout = $items['customer-logout'];
    unset($items['customer-logout']);
    $items[$slug] = $title;
    $items['customer-logout'] = $logout;

    return $items;
  }



  /**
   * @param string $html
   * @param Wc_Product $product
   */
  public function displayWishListAfterOutOfStocks(string $html, WC_Product $product)
  {
    if (!$product->is_in_stock()) {
      ob_start();
      $this->wishlistHTMLContent(get_the_ID());
      $wishlistHTML = ob_get_clean();
      return $html . $wishlistHTML;
    }
    return $html;
  }

  public function wishlistHTMLContent(int $productID)
  {
    $settings = get_option(RGN_CUSTOMER_WISHLIST_SETTINGS, []);
    $icon = getIcons()[$settings['wishlist-icon']];

    $wishlistText = $settings['wishlist-label'];
    $alreadyAddedLabel = $settings['added-to-wishlist-label'];

    $wishlist = new WishlistModel();
    $identifier = wishListIdentifier();
    $isProductInWishlist = $wishlist->isProductInWishlist($productID, $identifier);

    $myAccountOptions = get_option(RGN_CUSTOMER_WISHLIST_MY_ACCOUNT_SETTINGS, []);
    $slug = $myAccountOptions['menu-slug'];

    return  $this->renderTemplate('wishlist.php', [
      'icon' => $icon,
      'product-id' => $productID,
      'wishlist-label' => $wishlistText,
      'is-product-in-wishlist' => $isProductInWishlist,
      'already-added-label' => $alreadyAddedLabel,
      'slug' => $slug
    ]);
  }

  private function renderTemplate(string $templatePath, $data = [])
  {
    ob_start();
    require RGN_CUSTOMER_WISHLIST_PATH . 'templates/' . $templatePath;
    return ob_get_clean();
  }

  private static function renderTemplateOnce(string $templatePath, $data = [])
  {
    ob_start();
    require RGN_CUSTOMER_WISHLIST_PATH . 'templates/' . $templatePath;
    return (string) ob_get_clean();
  }

  public function assets()
  {
    if (is_product()) {
      wp_enqueue_script('rgn-customer-wishlist-petite-vue', RGN_CUSTOMER_WISHLIST_URL . 'libraries/petite-vue.iife.js', ['jquery'], RGN_CUSTOMER_WISHLIST_VERSION, true);
      wp_enqueue_script('rgn-customer-wishlist', $this->getAssetFile('js', 'rgn-customer-wishlist.js'), ['jquery', 'wc-add-to-cart-variation', 'rgn-customer-wishlist-petite-vue'], RGN_CUSTOMER_WISHLIST_VERSION, true);

      wp_localize_script('rgn-customer-wishlist', 'rgn_add_customer_wishlist', [
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('rgn_add_customer_wishlist_security'),
      ]);

      wp_register_style('rgn-customer-wishlist-inline', false, [], RGN_CUSTOMER_WISHLIST_VERSION);
      wp_enqueue_style('rgn-customer-wishlist-inline', '', [], RGN_CUSTOMER_WISHLIST_VERSION);
      wp_add_inline_style('rgn-customer-wishlist-inline', $this->loadInlineCSS());

      wp_enqueue_style('rgn-customer-wishlist-style', $this->getAssetFile('css', 'rgn-customer-wishlist.css'), ['rgn-customer-wishlist-inline'], RGN_CUSTOMER_WISHLIST_VERSION);
    }

    global $wp;
    if (is_account_page() && array_key_exists(MyAccountOptions::getSlug(), $wp->query_vars)) {

      if (wp_script_is('wc-cart-fragments', 'registered')) {
        wp_enqueue_script('wc-cart-fragments');
      }

      wp_enqueue_style('rgn-my-account-wishlist', $this->getAssetFile('css', 'rgn-my-account-wishlist.css'), [], RGN_CUSTOMER_WISHLIST_VERSION);
      wp_enqueue_script('rgn-customer-wishlist-petite-vue', RGN_CUSTOMER_WISHLIST_URL . 'libraries/petite-vue.iife.js', [], RGN_CUSTOMER_WISHLIST_VERSION, true);
      wp_enqueue_script('rgn-my-account-wishlist-script', $this->getAssetFile('js', 'rgn-customer-wishlist-my-account.js'), ['jquery', 'rgn-customer-wishlist-petite-vue', 'wc-cart-fragments'], RGN_CUSTOMER_WISHLIST_VERSION, true);

      wp_localize_script('rgn-my-account-wishlist-script', 'rgn_wishlist_my_account', [
        'url' => admin_url('admin-ajax.php'),
        'nonces' => [
          'add-to-cart' => wp_create_nonce('rgn_add_to_cart_security'),
          'remove-from-wishlist' => wp_create_nonce('rgn_remove_from_wishlist_security')
        ]
      ]);
    }
  }

  public function loadInlineCSS()
  {
    $settings = get_option(RGN_CUSTOMER_WISHLIST_SETTINGS, []);
    $backgroundColor = isset($settings['background-color']) ? esc_attr($settings['background-color']) : '#000';
    $color = isset($settings['text-color']) ? esc_attr($settings['text-color']) : '#fff';
    $fontSize = isset($settings['font-size']) && $settings['font-size'] !== '' ? esc_attr($settings['font-size']) : 16;
    $svgSize = isset($settings['wishlist-icon-size']) ? esc_attr($settings['wishlist-icon-size']) : 20;

    return "
      :root {
        --rgn-wishlist-bg-color: $backgroundColor;
        --rgn-wishlist-text-color:  $color;
        --rgn-wishlist-font-size: $fontSize" . "px;
        --rgn-wishlist-svg-size: $svgSize" . "px;
        --rgn-wishlist-remove-text-color: " . $settings['remove-text-color'] . ";
        --rgn-wishlist-browse-wishlist: " . $settings['browse-wishlist-color'] . ";
      }
    ";
  }

  private function getAssetFile(string $fileType, string $file)
  {
    return RGN_CUSTOMER_WISHLIST_URL . 'assets/' . $fileType . '/' . $file;
  }
}
