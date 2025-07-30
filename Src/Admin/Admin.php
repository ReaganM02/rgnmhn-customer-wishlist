<?php

namespace Src\Admin;

class Admin
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('admin_menu', [$this, 'addMenu']);
    add_action('admin_post_rgn_wishlist_save_settings', [$this, 'handleSaveSettings']);
  }

  public function addMenu()
  {
    $menu = add_menu_page(
      __('RGN Customer Wishlist', 'rgn-customer-wishlist'),
      __('Wishlist', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-customer-wishlist',
      [$this, 'HTMLContent'],
      'dashicons-heart',
      60
    );
    add_action("load-$menu", [$this, 'assets']);

    $submenu = add_submenu_page(
      'rgn-customer-wishlist',
      __('RGN Customer Wishlist Settings', 'rgn-customer-wishlist'),
      __('Settings', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-customer-wishlist',
      [$this, 'HTMLContent']
    );
    add_action("load-$submenu", [$this, 'assets']);

    $submenu = add_submenu_page(
      'rgn-customer-wishlist',
      __('RGN Customer Wishlist Wishlist UI', 'rgn-customer-wishlist'),
      __('Wishlist UI', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-wishlist-wishlist-ui',
      [$this, 'HTMLContent']
    );
    add_action("load-$submenu", [$this, 'assets']);
  }

  public function assets()
  {
    wp_enqueue_style('rgn-customer-wishlist', $this->getAssetFile('css', 'rgn-customer-wishlist.css'), [], RGN_CUSTOMER_WISHLIST_VERSION);
  }

  private function getAssetFile(string $type, string $file)
  {
    return RGN_CUSTOMER_WISHLIST_URL . 'admin/' . $type . '/' . $file;
  }

  public function HTMLContent()
  {
    echo TemplateSetup::renderSettingsHTML();
  }

  public function handleSaveSettings()
  {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    check_admin_referer('rgn_wishlist_save_settings_security');

    $allowLoggedInUser = isset($_POST['allow-none-logged-in-user']) ? 'yes' : 'no';
    $cookieExpiryDate = isset($_POST['number-of-days-to-store-cookie']) ? sanitize_text_field($_POST['number-of-days-to-store-cookie']) : 30;

    $data = [
      'allow-none-logged-in-user' => $allowLoggedInUser,
      'number-of-days-to-store-cookie' => $cookieExpiryDate
    ];

    update_option(RGN_CUSTOMER_WISHLIST_SETTINGS, $data);

    $redirectURL = add_query_arg('settings-updated', 'true', wp_get_referer());
    wp_safe_redirect($redirectURL);
    exit;
  }
}
