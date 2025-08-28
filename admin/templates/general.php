<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<form class="rgn-m-6 rgn-w-1/2" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post">
  <section class="rgn-p-4 rgn-bg-white rgn-shadow-md rgn-rounded">
    <h1 class="rgn-text-zinc-700 rgn-font-bold rgn-uppercase rgn-tracking-wide rgn-text-lg">
      <?php echo __('General Settings', 'rgn-customer-wishlist'); ?>
    </h1>
    <div class="rgn-block rgn-space-y-4 rgn-mt-6">
      <?php
      foreach ($settings as $setting) {
        renderComponent($setting['type'] . '.php', $setting);
      }
      ?>
    </div>
    <div class="rgn-bg-yellow-100 rgn-border rgn-border-yellow-300 rgn-rounded rgn-p-2 rgn-mt-10 rgn-text-yellow-600" role="alert">
      <strong><?php esc_html_e('Warning:', 'rgn-customer-wishlist'); ?></strong>
      <?php
      /* translators: 1: opening <strong> tag, 2: closing </strong> tag. */
      $message = __(
        'If this checkbox is checked, %1$sall saved settings and default values will be permanently deleted%2$s when the plugin is uninstalled.',
        'rgn-customer-wishlist'
      );

      echo wp_kses_post(sprintf($message, '<strong>', '</strong>'));
      ?>
    </div>
    <input type="hidden" name="action" value="rgn_wishlist_general_settings">
    <?php wp_nonce_field('rgn_wishlist_general_settings'); ?>
    <div class="rgn-mt-10 rgn-mb-4">
      <?php renderComponent('success.php'); ?>
      <button class="rgn-bg-blue-600 rgn-text-white rgn-px-10 rgn-py-4 rgn-rounded hover:rgn-bg-blue-700 rgn-text-base rgn-uppercase rgn-font-bold rgn-tracking-wide">
        <?php echo __('Save Settings', 'rgn-customer-wishlist') ?>
      </button>
    </div>
  </section>
</form>