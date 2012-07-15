<?php echo $this->renderWidget('IpTitle', array('title' => $this->par('community/user/translations/title_profile'))); ?>
<?php if ($justUpdated) { ?>
    <?php echo $this->renderWidget('IpText', array('text' => $this->par('community/user/translations/profile_updated'))); ?>
<?php } ?>
<?php echo $this->renderWidget('IpUserProfile'); ?>