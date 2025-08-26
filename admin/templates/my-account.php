<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
?>
<form class="rgn-m-6 rgn-w-1/2" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post">
  <section class="rgn-p-4 rgn-bg-white rgn-shadow-md rgn-rounded">
    <h1 class="rgn-text-zinc-700 rgn-font-bold rgn-uppercase rgn-tracking-wide rgn-text-lg">
      <?php echo __('My Account Settings', 'rgn-customer-wishlist'); ?>
    </h1>
    <div class="rgn-block rgn-space-y-4 rgn-mt-6">
      <?php foreach ($settings as $key => $setting): ?>
        <div>
          <?php
          renderComponent($setting['type'] . '.php', $setting); ?>
        </div>
      <?php endforeach; ?>
    </div>
    <input type="hidden" name="action" value="rgn_wishlist_save_my_account">
    <?php wp_nonce_field('rgn_wishlist_save_my_account_security'); ?>
    <div class="rgn-mt-10 rgn-mb-4">
      <?php renderComponent('success.php'); ?>
      <button class="rgn-bg-blue-600 rgn-text-white rgn-px-10 rgn-py-4 rgn-rounded hover:rgn-bg-blue-700 rgn-text-base rgn-uppercase rgn-font-bold rgn-tracking-wide">
        <?php echo __('Save Settings', 'rgn-customer-wishlist') ?>
      </button>
    </div>
  </section>
</form>