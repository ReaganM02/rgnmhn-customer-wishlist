<?php

/**
 *  FormController: Handles POSTed admin settings for the RGN Customer Wishlist plugin.
 *
 * - Registers admin_post actions for each settings form.
 * - Validates capability + nonces.
 * - Sanitizes/validates incoming values (whitelist by known keys).
 * - Persists options and redirects back with a success flag.
 *
 * @package   Src\Admin
 * @since     1.0.0
 */

namespace Src\Admin;

use Src\GeneralSettingOptions;
use Src\MyAccountOptions;
use Src\ProductOptions;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

/**
 * Class FormController
 *
 * Wires up admin_post handlers and processes the three settings groups:
 * - General settings
 * - My Account settings
 * - Product settings
 */
class FormController
{
  /**
   * Bootstrap hooks on construct.
   */
  public function __construct()
  {
    $this->hooks();
  }

  /**
   * Register admin_post actions for handling settings submissions.
   *
   * @return void
   */
  public function hooks()
  {
    // Product settings submit action.
    add_action('admin_post_rgn_wishlist_save_settings', [$this, 'handleProductSettings']);

    // My Account settings submit action.
    add_action('admin_post_rgn_wishlist_save_my_account', [$this, 'handleMyAccountSettings']);

    // General settings submit action.
    add_action('admin_post_rgn_wishlist_general_settings', [$this, 'handleGeneralSettings']);
  }

  /**
   * Handle saving General Settings (e.g., delete-on-uninstall flag).
   *
   * Security:
   * - Capability check: manage_options
   * - CSRF: check_admin_referer('rgn_wishlist_general_settings')
   *
   * @return void
   */
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

  /**
   * Handle saving My Account settings.
   *
   * Security:
   * - Capability check: manage_options
   * - CSRF: check_admin_referer('rgn_wishlist_save_my_account_security')
   *
   * Validation/Sanitization:
   * - Whitelist by keys defined in MyAccountOptions::fields()
   * - Key-specific sanitization (e.g., slug vs text)
   *
   * @return void
   */
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

  /**
   * Handle saving Product settings.
   *
   * Security:
   * - Capability check: manage_options
   * - CSRF: check_admin_referer('rgn_wishlist_save_settings_security')
   *
   * Validation/Sanitization:
   * - Whitelist by keys defined in ProductOptions::fields()
   * - Per-key sanitization
   *
   * @return void
   */
  public function handleProductSettings()
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

  /**
   * Sanitize data
   */
  private function sanitizeData(string $key)
  {
    if (isset($_POST[$key]) && $_POST[$key] !== '') {
      return sanitize_text_field($_POST[$key]);
    }
    return '';
  }
}
