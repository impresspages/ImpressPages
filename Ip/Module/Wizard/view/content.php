<?php if ($tip_dragWidget) { ?>
<div id="ipAdminWizardTip-dragWidget" class="ipAdminWizardTip">
    <a href="#" class="ipaClose"></a>
    <span class="ipaArrowTop"></span>
    <p><?php echo __('Drag this widget to your page.', 'ipAdmin') ?></p>
</div>
<?php } ?>

<?php if ($tip_dropWidget) { ?>
<div id="ipAdminWizardTip-dropWidget" class="ipAdminWizardTip">
    <a href="#" class="ipaClose"></a>
    <span class="ipaArrowBottom"></span>
    <p><?php echo __('Release it here.', 'ipAdmin') ?></p>
</div>
<?php } ?>

<?php if ($tip_changeWidgetContent) { ?>
<div id="ipAdminWizardTip-changeWidgetContent" class="ipAdminWizardTip">
    <a href="#" class="ipaClose"></a>
    <span class="ipaArrowBottom"></span>
    <p><?php echo __('Add or change something.', 'ipAdmin') ?></p>
</div>
<?php } ?>

<?php if ($tip_confirmWidget) { ?>
<div id="ipAdminWizardTip-confirmWidget" class="ipAdminWizardTip">
    <a href="#" class="ipaClose"></a>
    <span class="ipaArrowTop"></span>
    <p><?php echo __('Confirm your changes.', 'ipAdmin') ?></p>
</div>
<?php } ?>

<?php if ($tip_publish) { ?>
<div id="ipAdminWizardTip-publish" class="ipAdminWizardTip">
    <a href="#" class="ipaClose"></a>
    <span class="ipaArrowTop"></span>
    <p><?php echo _esc('Changes are not visible until you press "Publish".', 'ipAdmin') ?></p>
</div>
<?php } ?>
