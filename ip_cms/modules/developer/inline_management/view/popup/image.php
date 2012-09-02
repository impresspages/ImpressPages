<div class="ipaImage"></div>
<div class="clear" style="clear: both;"><!-- --></div>
<div class="ipaControls">
    <?php echo $this->escPar('developer/inline_management/admin_translations/image_assignment_type'); ?>
    <select class="ipaType">
        <?php foreach ($types as $key => $type){ ?>
            <option value="<?php echo $this->esc($type['value']) ?>" <?php echo !empty($type['selected']) ? 'selected="selected"' : '' ?>><?php echo $this->esc($type['title']) ?></option>
        <?php } ?>
    </select>
    <a class="ipAdminButton ipaRemove" href="#"><?php echo $this->escPar('developer/inline_management/admin_translations/remove_image'); ?></a>
    <div class="ipaRemoveConfirm"><?php echo $this->escPar('developer/inline_management/admin_translations/remove_image_confirm'); ?></div>
    <br/>
    <a class="ipAdminButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm'); ?></a>
    <a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel'); ?></a>
</div>