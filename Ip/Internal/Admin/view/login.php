<div class="loginTitle">
    <h1><?php _e('Login', 'ipAdmin'); ?></h1>
</div>
<div class="ip loginContent">
    <?php echo $loginForm->render(); ?>
    <p class="alternativeLink">
        <a href="<?php echo ipActionUrl(array('sa' => 'Admin.passwordResetForm')); ?>"><?php echo _e('Reset password', 'ipAdmin'); ?></a>
    </p>
</div>
