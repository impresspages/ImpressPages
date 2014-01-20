<div class="loginTitle">
    <h1><?php _e('Password reset', 'ipAdmin') ?></h1>
</div>
<div class="ip">
    <?php echo $resetPasswordForm->render() ?>
    <a href="<?php echo ipFileUrl('admin'); ?>"><?php echo _e('Login', 'ipAdmin'); ?></a>
</div>
