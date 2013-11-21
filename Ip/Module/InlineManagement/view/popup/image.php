<div class="ipaImage"></div>
<div class="clear" style="clear: both;"><!-- --></div>
<div class="ipaControls">
    <?php _e('Image assignment type', 'ipAdmin') ?>
    <select class="ipaType">
        <?php foreach ($types as $key => $type){ ?>
            <option value="<?php echo ipEsc($type['value']) ?>" <?php echo !empty($type['selected']) ? 'selected="selected"' : '' ?>><?php echo ipEsc($type['title']) ?></option>
        <?php } ?>
    </select>
    <?php if (isset($showRemoveLink) && $showRemoveLink) { ?>
        <a class="ipAdminButton ipaRemove" href="#"><?php _e('Remove this image', 'ipAdmin') ?></a>
        <div class="ipaRemoveConfirm"><?php _e('There is no option to undo this action. Parent page image or the default one will be applied to this page. Do you want to proceed?', 'ipAdmin') ?></div>
    <?php } ?>
    <br/>
    <a class="ipAdminButton ipaConfirm" href="#"><?php _e('Confirm', 'ipAdmin') ?></a>
    <a class="ipAdminButton ipaCancel" href="#"><?php _e('Cancel', 'ipAdmin') ?></a>
</div>