<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}
if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
?>
  <div class="rgn-p-2 rgn-border-l-2 rgn-border-green-600 rgn-border rgn-bg-green-100 rgn-text-green-700 rgn-mb-4 rgn-shadow rgn-text-base rgn-rounded">Settings Successfully Saved!</div>
<?php
}
