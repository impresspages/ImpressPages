<div class="ipaImage"></div>
<div class="clear" style="clear: both;"><!-- --></div>
<div class="ipaControls">
    <?php echo $this->escPar('InlineManagement.image_assignment_type'); ?>
    <select class="ipaType">
        <?php foreach ($types as $key => $type){ ?>
            <option value="<?php echo $this->esc($type['value']) ?>" <?php echo !empty($type['selected']) ? 'selected="selected"' : '' ?>><?php echo $this->esc($type['title']) ?></option>
        <?php } ?>
    </select>
    <?php if (isset($showRemoveLink) && $showRemoveLink) { ?>
        <a class="ipAdminButton ipaRemove" href="#"><?php echo $this->escPar('InlineManagement.remove_image'); ?></a>
        <div class="ipaRemoveConfirm"><?php echo $this->escPar('InlineManagement.remove_image_confirm'); ?></div>
    <?php } ?>
    <br/>
    <a class="ipAdminButton ipaConfirm" href="#"><?php echo _esc('Confirm', 'ipAdmin'); ?></a>
    <a class="ipAdminButton ipaCancel" href="#"><?php echo _esc('Cancel', 'ipAdmin'); ?></a>
</div>