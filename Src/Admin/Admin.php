<?php

namespace Src\Admin;

use Src\GeneralSettingOptions;
use Src\MyAccountOptions;
use Src\ProductOptions;

class Admin
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('admin_menu', [$this, 'addMenu']);
  }

  public function addMenu()
  {
    $menu = add_menu_page(
      __('RGN Customer Wishlist', 'rgn-customer-wishlist'),
      __('Wishlist', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-customer-wishlist',
      [$this, 'ProductSettingsHTML'],
      'dashicons-heart',
      60
    );
    add_action("load-$menu", [$this, 'assets']);

    $submenu = add_submenu_page(
      'rgn-customer-wishlist',
      __('Wishlist Product Settings', 'rgn-customer-wishlist'),
      __('Product Settings', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-customer-wishlist',
      [$this, 'ProductSettingsHTML']
    );
    add_action("load-$submenu", [$this, 'assets']);

    $submenu = add_submenu_page(
      'rgn-customer-wishlist',
      __('Customer Wishlist My Account Settings', 'rgn-customer-wishlist'),
      __('My Account', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-wishlist-wishlist-my-account',
      [$this, 'myAccountSettingsHTML']
    );
    add_action("load-$submenu", [$this, 'assets']);

    $submenu = add_submenu_page(
      'rgn-customer-wishlist',
      __('Customer Wishlist General Settings', 'rgn-customer-wishlist'),
      __('General Settings', 'rgn-customer-wishlist'),
      'manage_options',
      'rgn-customer-wishlist-general-settings',
      [$this, 'generalSettingsHTML']
    );
    add_action("load-$submenu", [$this, 'assets']);
  }


  public function generalSettingsHTML()
  {
    $settings = GeneralSettingOptions::fields();
    require_once RGN_CUSTOMER_WISHLIST_PATH . 'admin/templates/general.php';
  }


  public function myAccountSettingsHTML()
  {
    $settings = MyAccountOptions::fields();
    require_once RGN_CUSTOMER_WISHLIST_PATH . 'admin/templates/my-account.php';
  }


  public function ProductSettingsHTML()
  {
    $settings = ProductOptions::fields();
    require_once RGN_CUSTOMER_WISHLIST_PATH . 'admin/templates/product-settings.php';
  }

  public function assets()
  {
    wp_dequeue_style('forms');
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('rgn-customer-wishlist-petite-vue', RGN_CUSTOMER_WISHLIST_SETTINGS . 'libraries/petite-vue.iife.js', ['jquery', 'wp-color-picker'], RGN_CUSTOMER_WISHLIST_VERSION, true);

    wp_enqueue_style('rgn-customer-wishlist', $this->getAssetFile('css', 'rgn-customer-wishlist.css'), [], RGN_CUSTOMER_WISHLIST_VERSION);
    wp_enqueue_script('rgn-customer-wishlist-script', $this->getAssetFile('js', 'rgn-customer-wishlist.js'), ['wp-color-picker', 'rgn-customer-wishlist-petite-vue'], RGN_CUSTOMER_WISHLIST_VERSION, true);

    $themePalette = wp_get_global_settings()['color']['palette']['default'] ?? [];

    $paletteColors = array_map(function ($color) {
      return $color['color'] ?? '';
    }, $themePalette);

    $paletteColors = array_slice($paletteColors, 0, 6);

    wp_localize_script('rgn-customer-wishlist-script', 'themePalette', $paletteColors);
  }

  private function getAssetFile(string $type, string $file)
  {
    return RGN_CUSTOMER_WISHLIST_URL . 'admin/' . $type . '/' . $file;
  }
}
