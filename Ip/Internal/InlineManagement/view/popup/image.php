<div class="ipsImage"></div>
<div class="clear" style="clear: both;"><!-- --></div>
<div class="ipmControls ipsControls">
    <?php _e('Image assignment type', 'ipAdmin'); ?>
    <select class="ipmType ipsType">
        <?php foreach ($types as $key => $type){ ?>
            <option value="<?php echo esc($type['value']); ?>" <?php echo !empty($type['selected']) ? 'selected="selected"' : ''; ?>><?php echo esc($type['title']); ?></option>
        <?php } ?>
    </select>
    <?php if (isset($showRemoveLink) && $showRemoveLink) { ?>
        <a class="ipAdminButton ipmRemove ipsRemove" href="#"><?php _e('Remove this image', 'ipAdmin'); ?></a>
        <div class="hidden ipsRemoveConfirm"><?php _e('There is no option to undo this action. Parent page image or the default one will be applied to this page. Do you want to proceed?', 'ipAdmin'); ?></div>
    <?php } ?>
    <br/>
    <a class="ipAdminButton ipsConfirm" href="#"><?php _e('Confirm', 'ipAdmin'); ?></a>
    <a class="ipAdminButton ipsCancel" href="#"><?php _e('Cancel', 'ipAdmin'); ?></a>
</div>
