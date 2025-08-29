
![RGN Customer Wishlist Screenshot](http://reagandev.com/wp-content/uploads/2025/08/screenshot-sample-wide-3.png)


# RGN Customer Wishlist
Give your customers the ability to save products to a personalized wishlist. Fully customizable, lightweight, and optimized for speed. Translation-ready, compatible with tools like Loco Translate.


## Features

- **Guest Wishlist Support** – Visitors can save products to their wishlist without creating an account.  
- **Customizable Icons** – Choose from default icons or upload add your own via hooks.  
- **Flexible Labels & Styles** – Personalize button text and colors to match your store’s design.  
- **Dynamic Placement** – Display the wishlist button in multiple positions or embed it anywhere using a shortcode.  
- **Customizable Wishlist Page** – Easily edit the menu title, slug, page title, and empty list message.  
- **Full Wishlist Management** – Users can remove items, view their wishlist, or add products (including variations) directly to the cart.  
- **Translation Ready** – Fully compatible with localization tools and tested with the **Loco Translate** plugin.  

#### List of Wishlist Content Page
![List of Wishlist Content Page](http://reagandev.com/wp-content/uploads/2025/08/added-list-content.jpg)

#### Single Product Page Settings
![Product Settings](http://reagandev.com/wp-content/uploads/2025/08/product-settings.jpg)

#### Wishlist Content Page Settings
![My Account Settings](http://reagandev.com/wp-content/uploads/2025/08/my-account-settings.jpg)

## Available Hooks

### Filters:

- `rgn_single_product_wishlist_icon`  
  Easily add your own SVG icon using the filter. Once added, it will automatically appear in the product settings icon list, ready to be selected.

  **Parameters:**  
  - `$newIcon` *(array)* – An associative array of available icons. **Key:** Unique identifier (string). **Value:** SVG markup (string).
  
    **Return:** *(array)*  - The added svg icon.

- `rgn_wishlist_list_delete_text_btn`
  Change text label of delete button in wishlist content.

  **Parameters**
  - `$label` *(string)* - Button label.

    **Return** *(string)* - New button label.

- `rgn_wishlist_list_view_text_btn`.
  Change text label of view button in wishlist content.

  **Parameters**
  - `$label` *(string)* - Button label.

    **Return** *(string)* - New button label.

- `rgn_wishlist_list_add_to_cart_text_btn`.
  Change text label of add to cart button in wishlist content.

  **Parameters**
  - `$label` *(string)* - Button label.

    **Return** *(string)* - New button label.

Example Usage
```php
add_filter('rgn_single_product_wishlist_icon', function($newIcon) {
    // NOTE: the 'key' must be unique.
    $newIcon['unique-key'] = '<svg>...</svg>';
    return $newIcon;
}, 10, 1);  
```