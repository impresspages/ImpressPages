<?php echo $this->renderWidget('IpTitle', array('title' => $this->par('community/user/translations/title_login'))); ?>
<?php echo $this->renderWidget('IpUserLogin'); ?>
<?php if (isset($passwordResetLink)){ ?>
<a href="<?php echo $passwordResetLink; ?>"><?php echo $this->esc($this->par('community/user/translations/password_reset')); ?></a>
<?php }?>
<?php if (isset($registrationLink)){ ?>
<a href="<?php echo $registrationLink; ?>"><?php echo $this->esc($this->par('community/user/translations/title_registration')); ?></a>
<?php }?>