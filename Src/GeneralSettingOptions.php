<?php

/**
 * GeneralSettingOptions
 *
 * Manages the single “delete all data on uninstall” setting for the
 * RGN Customer Wishlist plugin. Provides:
 * - A canonical option key
 * - A cached getter (with strict normalization to 'yes'/'no')
 * - A field config array for rendering the admin UI
 * - Convenience helpers (boolean check + cache refresh)
 *
 *
 * @package Src
 * @since   1.0.0
 */

namespace Src;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class GeneralSettingOptions
{
  /** @var string Option key stored in wp_options */
  private const KEY = 'rgn_customer_wishlist_delete_all_data_on_uninstall';

  /** @var 'yes'|'no' Default value when option is missing/invalid */
  private const DEFAULT = 'no';

  /**
   * Static cache for the current option value.
   *
   * @var 'yes'|'no'|null
   */
  private static ?string $option = null;

  /**
   * Return the canonical option key.
   *
   * @return string
   */
  public static function optionKey()
  {
    return self::KEY;
  }

  /**
   * Get the current option value, normalized strictly to 'yes' or 'no'.
   * Uses a static cache to avoid repeated get_option() calls.
   *
   * @return 'yes'|'no'
   */
  private static function getOption()
  {
    if (self::$option !== null) {
      return self::$option;
    }

    $raw = get_option(self::optionKey(), self::DEFAULT);

    if (is_string($raw)) {
      return self::$option = $raw;
    }

    /**
     * If it holds a single item or value
     */
    if (is_scalar($raw)) {
      return self::$option = (string) $raw;
    }

    return self::DEFAULT;
  }

  /**
   * Clear the static cache (call after updating the option).
   *
   * @return void
   */
  public static function refresh(): void
  {
    self::$option = null;
  }

  /**
   * Should we delete all data on uninstall?
   *
   * Reads the stored option and normalizes the value to the canonical
   * 'yes' or 'no'. Any non-'yes' value is treated as 'no'.
   *
   * @return 'yes'|'no'
   */
  public static function allowDeleteAllDataOnUninstall()
  {
    $deleteFlagRaw = get_option(self::KEY, self::DEFAULT);
    $val = is_scalar($deleteFlagRaw) ? (string) $deleteFlagRaw : self::DEFAULT;
    return ($val === 'yes') ? 'yes' : 'no';
  }


  /**
   * Field configuration used by the admin template to render the control.
   * The template should:
   * - Use `checked()` for the checkbox state
   * - Escape attributes/labels properly (esc_attr, esc_html)
   *
   * Example (checkbox/switch UI):
   * <input type="checkbox"
   *        id="<?php echo esc_attr($field['id']); ?>"
   *        name="<?php echo esc_attr($field['name']); ?>"
   *        value="<?php echo esc_attr($field['value']); ?>"
   *        <?php checked($field['checked']); ?> />
   * <label for="<?php echo esc_attr($field['id']); ?>">
   *   <?php echo esc_html($field['label']); ?>
   * </label>
   *
   * @return array<string, array<string, mixed>>
   */
  public static function fields()
  {
    return [
      'remove-data-on-uninstall' => [
        'id' => 'remove-data-on-uninstall',
        'name' => 'remove-data-on-uninstall',
        'type' => 'switch',
        'value' => 'yes',
        'label' => __('Delete all settings on uninstall', 'rgn-customer-wishlist'),
        'checked' => self::getOption() === 'yes' ? true : false
      ]
    ];
  }
}
