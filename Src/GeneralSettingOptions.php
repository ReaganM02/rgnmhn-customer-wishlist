<?php

namespace Src;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class GeneralSettingOptions
{
  private const KEY = 'rgn_customer_wishlist_delete_all_data_on_uninstall';
  private const DEFAULT = 'no';

  /** @var 'yes'|'no'|null */
  private static ?string $option = null;

  public static function optionKey()
  {
    return self::KEY;
  }

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
