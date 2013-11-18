<div class="ipaImage"></div>
<div class="clear" style="clear: both;"><!-- --></div>
<div class="ipaControls">
    <?php echo __('Image assignment type', 'ipAdmin') ?>
    <select class="ipaType">
        <?php foreach ($types as $key => $type){ ?>
            <option value="<?php echo $this->esc($type['value']) ?>" <?php echo !empty($type['selected']) ? 'selected="selected"' : '' ?>><?php echo $this->esc($type['title']) ?></option>
        <?php } ?>
    </select>
    <?php if (isset($showRemoveLink) && $showRemoveLink) { ?>
        <a class="ipAdminButton ipaRemove" href="#"><?php echo __('Remove this image', 'ipAdmin') ?></a>
        <div class="ipaRemoveConfirm"><?php echo __('There is no option to undo this action. Parent page image or the default one will be applied to this page. Do you want to proceed?', 'ipAdmin') ?></div>
    <?php } ?>
    <br/>
    <a class="ipAdminButton ipaConfirm" href="#"><?php echo __('Confirm', 'ipAdmin'); ?></a>
    <a class="ipAdminButton ipaCancel" href="#"><?php echo __('Cancel', 'ipAdmin'); ?></a>
</div>