<div class="ipWidget_ipForm_container"></div>
<div class="ipgHide">
    <div class="ipaFieldTemplate">
        <a href="#" class="ipaButton ipaFieldMove"><?php _e('Move', 'ipAdmin') ?></a>
        <input type="text" class="ipAdminInput ipaFieldLabel" name="label" value="" />
        <select class="ipaFieldType">
            <?php foreach($fieldTypes as $fieldType) { ?>
                <option value="<?php echo $this->esc($fieldType['key']); ?>"><?php echo $this->esc($fieldType['title']); ?></option>
            <?php } ?>
        </select>
        <input type="checkbox" class="ipaFieldRequired" />
        <a href="#" class="ipaButton ipaFieldOptions"><?php _e('Options', 'ipAdmin') ?></a>
        <a href="#" class="ipaButton ipaFieldRemove"><?php _e('Remove', 'ipAdmin') ?></a>
    </div>
    <div class="ipaFieldOptionsPopup"></div>
</div>
<a href="#" class="ipAdminButton ipaFieldAdd">Add new</a>
<div class="ipaOptions">
    <label class="ipAdminLabel"><?php _e('Thank you message', 'ipAdmin'); ?></label>
    <textarea class="ipWidgetIpFormSuccess"><?php echo isset($success) ? htmlentities($success, (ENT_COMPAT), 'UTF-8') : ''; ?></textarea>
</div>
