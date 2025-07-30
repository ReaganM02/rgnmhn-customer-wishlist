<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}


function wishListIdentifier()
{
  if (is_user_logged_in()) {
    return [
      'type' => 'user',
      'id' => get_current_user_id(),
    ];
  } else {
    $token = '';
    if (isset($_COOKIE[RGN_WISHLIST_COOKIE])) {
      $token = sanitize_text_field($_COOKIE[RGN_WISHLIST_COOKIE]);
    }

    if (empty($token)) {
      $token = wp_generate_uuid4();

      $setting = get_option(RGN_CUSTOMER_WISHLIST_SETTINGS, []);
      $days = !empty($setting) ? $setting['number-of-days-to-store-cookie'] : 30;
      wc_setcookie(RGN_WISHLIST_COOKIE, $token, time() + (int) $days * DAY_IN_SECONDS);
    }
    return [
      'type' => 'token',
      'id' => $token
    ];
  }
}

/**
 * Get the variation ID that matches the default attributes.
 *
 * @param array $defaultAttributes The default attributes of the variable product.
 * @param array $availableVariations The list of available variations (each variation is an associative array).
 * @return int|null Returns the variation ID if a match is found, otherwise null.
 */
function getDefaultVariationID(array $defaultAttributes, array $availableVariations): ?int
{
  foreach ($availableVariations as $variation) {
    $match = true;

    foreach ($defaultAttributes as $attributeName => $defaultValue) {
      $attributeKey = 'attribute_' . $attributeName;
      if (!isset($variation['attributes'][$attributeKey]) || $variation['attributes'][$attributeKey] !== $defaultValue) {
        $match = false;
        break;
      }
    }

    if ($match) {
      return (int) $variation['variation_id'];
    }
  }

  return null;
}

function renderCheckboxHTML(array $data = [])
{
  ob_start();
  getAdminComponent('checkbox.php', $data);
  $html = ob_get_clean();
  echo $html;
}

function renderNumberHTML(array $data = [])
{
  ob_start();
  getAdminComponent('number.php', $data);
  $html = ob_get_clean();
  echo $html;
}

function getAdminComponent(string $fileName, array $variables = [])
{
  $path = RGN_CUSTOMER_WISHLIST_PATH . 'admin/templates/components/' . $fileName;
  if (file_exists($path)) {
    include $path;
  } else {
    echo "<!-- Component not found: {$fileName} -->";
  }
}
