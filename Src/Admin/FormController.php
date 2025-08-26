<?php

namespace Src\Admin;

use Src\GeneralSettingOptions;
use Src\MyAccountOptions;
use Src\ProductOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class FormController
{
  public function __construct()
  {
    $this->hooks();
  }

  public function hooks()
  {
    add_action('admin_post_rgn_wishlist_save_settings', [$this, 'handleSaveSettings']);
    add_action('admin_post_rgn_wishlist_save_my_account', [$this, 'handleMyAccountSettings']);
    add_action('admin_post_rgn_wishlist_general_settings', [$this, 'handleGeneralSettings']);
  }

  public function handleGeneralSettings()
  {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'rgn-customer-wishlist'));
    }

    check_admin_referer('rgn_wishlist_general_settings');

    $value = (isset($_POST['remove-data-on-uninstall'])) && ($_POST['remove-data-on-uninstall'] === 'yes') ? 'yes'  : 'no';

    update_option(GeneralSettingOptions::optionKey(), $value);

    $redirectURL = add_query_arg('settings-updated', 'true', wp_get_referer());
    wp_safe_redirect($redirectURL);
  }

  public function handleMyAccountSettings()
  {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'rgn-customer-wishlist'));
    }

    check_admin_referer('rgn_wishlist_save_my_account_security');

    $settings = MyAccountOptions::fields();
    $sanitizedData = [];

    foreach ($_POST as $key => $value) {
      if (array_key_exists($key, $settings)) {
        if ($key === 'menu-slug') {
          $sanitizedData[$key] = sanitize_title($_POST[$key]);
        } else {
          $sanitizedData[$key] = $this->sanitizeData($key);
        }
      }
    }
    update_option(MyAccountOptions::optionKey(), $sanitizedData);

    $redirectURL = add_query_arg('settings-updated', 'true', wp_get_referer());
    wp_safe_redirect($redirectURL);
  }

  public function handleSaveSettings()
  {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'rgn-customer-wishlist'));
    }

    check_admin_referer('rgn_wishlist_save_settings_security');

    $settingsField = ProductOptions::fields();
    $sanitizedData = [];

    $allowGuestUser = isset($_POST['allow-none-logged-in-user']) ? 'yes' : 'no';
    foreach ($_POST as $key => $value) {
      if (array_key_exists($key, $settingsField)) {
        $sanitizedData[$key] = $this->sanitizeData($key);
      }
    }

    $sanitizedData['allow-none-logged-in-user'] = $allowGuestUser;
    update_option(ProductOptions::optionKey(), $sanitizedData);
    $redirectURL = add_query_arg('settings-updated', 'true', wp_get_referer());
    wp_safe_redirect($redirectURL);
  }

  private function sanitizeData(string $key)
  {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
      return sanitize_text_field($_POST[$key]);
    }
    return '';
  }
}
