<div class="loginTitle">
    <h1><?php _e('Login', 'Ip-admin'); ?></h1>
</div>
<div class="ip loginContent">
    <?php echo $loginForm->render(); ?>
    <p class="alternativeLink">
        <a href="<?php echo ipActionUrl(array('sa' => 'Admin.passwordResetForm')); ?>"><?php _e('Reset password', 'Ip-admin'); ?></a>
    </p>
</div>
