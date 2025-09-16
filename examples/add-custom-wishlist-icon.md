You can add a **custom wishlist icon** using the filter below. [Reference code](https://github.com/ReaganM02/rgnmhn-customer-wishlist/blob/f4a559088dc7f51323a1b954f955c33d7d1eeb78/includes/rgnmhn-customer-wishlist-helpers.php#L339).
This filter only supports **SVG icons**.
```php
/**
 * Add a custom wishlist icon to single product pages.
 */
add_filter('rgnmhn_customer_wishlist_add_custom_icon_for_single_product', function($newIcon) {

    // Make sure your key is unique
    $key = 'balloon-fill-icon';

    // Replace this with your custom SVG markup
    $svg = 'place-your-svg-icon-here';

    $newIcon[$key] = $svg;

    return $newIcon;

}, 10, 1);