<div class="loginTitle">
    <h1><?php _e('Login', 'ipAdmin') ?></h1>
</div>
<div class="ip">
    <?php echo $loginForm->render() ?>
    <a href="?resetPassword=1"><?php echo _e('Reset password', 'ipAdmin'); ?></a>
</div>
