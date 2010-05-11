<?php use_helper('I18n') ?>
<form action="<?php echo url_for('@sf_newsletter_plugin_subscription_subscribe') ?>" method="post">
  <table>
    <?php echo $form ?>
  </table>
  <input type="submit" value="<?php echo __('Subscribe') ?>" />
</form>