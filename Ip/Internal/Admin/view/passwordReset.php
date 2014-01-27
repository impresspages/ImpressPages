<div class="loginTitle">
    <h1><?php _e('Password reset', 'ipAdmin'); ?></h1>
</div>
<div class="ip loginContent">
    <?php echo $passwordResetForm->render(); ?>
    <p class="alternativeLink">
        <a href="<?php echo ipFileUrl('admin'); ?>"><?php echo _e('Login', 'ipAdmin'); ?></a>
    </p>
</div>
