<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
return [
  'productSettings' => [
    'title' => __('Product Settings', 'rgn-customer-wishlist'),
    'fields' => [
      [
        'id' => 'allow-none-logged-in-user',
        'label' => __('Allow guest users to add wishlist', 'rgn-customer-wishlist'),
        'type' => 'checkbox',
        'desc' => '',
        'value' => 'yes',
        'checked' => true
      ],
      [
        'id' => 'number-of-days-to-store-cookie',
        'label' => __('For guest customers, How long to keep your wishlist (in days)', 'rgn-customer-wishlist'),
        'type' => 'number',
        'desc' => '',
        'value' => 30
      ]
    ]
  ]
];
