<div class="loginTitle">
    <h1><?php _e('Password reset', 'Ip-admin'); ?></h1>
</div>
<div class="ip loginContent">
    <?php echo $passwordResetForm->render(); ?>
    <p class="alternativeLink">
        <a href="<?php echo ipFileUrl('admin'); ?>"><?php _e('Login', 'Ip-admin'); ?></a>
    </p>
</div>
