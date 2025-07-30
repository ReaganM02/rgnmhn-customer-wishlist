<?php

namespace Src\Admin;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

class TemplateSetup
{
  public static function renderSettingsHTML()
  {
    $config = require_once RGN_CUSTOMER_WISHLIST_PATH  . 'config/settings-config.php';

    ob_start();
    self::getTemplateFile('settings.php', [
      'settings' => $config,
      'values' => get_option(RGN_CUSTOMER_WISHLIST_SETTINGS, [])
    ]);
    return ob_get_clean();
  }
  private static function getTemplateFile(string $file, array $data  = [])
  {
    $file =  RGN_CUSTOMER_WISHLIST_PATH . 'admin/templates/' . $file;
    if (file_exists($file)) {
      require_once $file;
    } else {
      echo 'File does not exist.';
    }
  }
}
