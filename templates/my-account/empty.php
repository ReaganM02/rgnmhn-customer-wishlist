<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<div>
  <h4 class="rgn-text-center"><?php echo esc_attr($data['empty-message']) ?></h4>
  <?php
  $args = [
    'classes' => ['rgn-bg-blue-500', 'rgn-px-4', 'rgn-py-2', 'rgn-text-white', 'rgn-block', 'rgn-w-max', 'rgn-m-auto', 'rgn-mt-4'],
    'link' => '/shop',
    'text' => __('Go to Shop', 'rgn-customer-wishlist')
  ];

  $args = apply_filters('rgn_customer_wishlist_empty_link_args', $args);

  // Sanitize classes and add a fallback '' if it returns nothing.
  $classes = array_map(function ($class) {
    return sanitize_html_class($class, '');
  }, (array) $args['classes']);

  $classes = array_filter($classes);
  $classes = implode(' ', $classes);


  $html = sprintf('<a class="%s" href="%s">%s</a>', esc_attr($classes), esc_url($args['link']), esc_html($args['text']));
  echo wp_kses_post(apply_filters('rgn_customer_wishlist_empty_link_html', $html, $args));
  ?>
</div>