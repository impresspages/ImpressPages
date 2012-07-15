<?php if (isset($loginForm)) { ?> 
<?php echo $loginForm->render(); ?>
<?php } ?>

<?php if (isset($passwordResetUrl)) { ?> 
    <br/><a href="<?php echo $passwordResetUrl; ?>"><?php echo $this->esc($this->par('community/user/translations/password_reset')); ?></a> 
<?php } ?>

<?php if (isset($registrationUrl)) { ?> 
    <br/><a href="<?php echo $registrationUrl; ?>"><?php echo $this->esc($this->par('community/user/translations/title_registration')); ?></a> 
<?php } ?>

<?php if (isset($logoutUrl)) { ?> 
    <br/><a href="<?php echo $logoutUrl; ?>"><?php echo $this->esc($this->par('community/user/translations/logout')); ?></a>
<?php } ?>
