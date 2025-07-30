<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

function fieldClasses(string $type)
{
  if ($type === 'text') {
    return 'rgn-h-[40px] rgn-w-[49px]';
  }
}

$values = $data['values'];
?>
<div class="rgn-m-4 rgn-w-[700px] rgn-bg-white rgn-border rgn-border-zinc-200 rgn-p-4 rgn-rounded">
  <h1 class="rgn-text-zinc-900 rgn-text-xl rgn-font-bold"><?php echo __('Wishlist Settings', 'rgn-customer-wishlist') ?></h1>
  <form class="rgn-mt-6" action="<?php echo esc_url(admin_url('admin-post.php')) ?>" method="post">
    <?php
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
    ?>
      <div class="notice notice-success is-dismissible">
        <p><strong>Settings saved successfully!</strong></p>
      </div>
    <?php
    }
    ?>
    <?php foreach ($data['settings'] as $key => $setting): ?>
      <h2 class="rgn-text-base rgn-text-zinc-800 rgn-font-bold"><?php echo $setting['title'] ?></h2>
      <div class="rgn-mt-4 rgn-grid rgn-divide-y">
        <?php foreach ($setting['fields'] as $key => $field): ?>
          <?php
          if ($field['type'] === 'checkbox') {
            renderCheckboxHTML($field);
          }
          if ($field['type'] === 'number') {
            renderNumberHTML($field);
          }
          ?>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <input type="hidden" name="action" value="rgn_wishlist_save_settings">
    <?php wp_nonce_field('rgn_wishlist_save_settings_security'); ?>
    <?php submit_button('Save Settings'); ?>
  </form>
</div>