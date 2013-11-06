<?php echo $this->renderWidget('IpTitle', array('title' => $this->par('User.title_profile'))); ?>
<?php if ($justUpdated) { ?>
    <?php echo $this->renderWidget('IpText', array('text' => $this->par('User.profile_updated'))); ?>
<?php } ?>
<?php echo $this->renderWidget('IpUserProfile'); ?>