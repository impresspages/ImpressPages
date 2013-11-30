<div class="ip ipHide">
    <div class="ipWidget_ipForm_container"></div>
    <div class="ipgHide">
        <div class="ipaFieldTemplate">
            <a href="#" class="ipaButton ipaFieldMove"><?php _e('Move', 'ipAdmin') ?></a>
            <input type="text" class="ipAdminInput ipaFieldLabel" name="label" value="" />
            <select class="ipaFieldType">
                <?php foreach($fieldTypes as $fieldType) { ?>
                    <option value="<?php echo esc($fieldType['key']); ?>"><?php echo esc($fieldType['title']); ?></option>
                <?php } ?>
            </select>
            <input type="checkbox" class="ipaFieldRequired" />
            <a href="#" class="ipaButton ipaFieldOptions"><?php __('Options', 'ipAdmin') ?></a>
            <a href="#" class="ipaButton ipaFieldRemove"><?php __('Remove', 'ipAdmin') ?></a>
        </div>
        <div class="ipaFieldOptionsPopup"></div>
    </div>
    <a href="#" class="ipAdminButton ipaFieldAdd">Add new</a>
    <div class="ipaOptions">
        <label class="ipAdminLabel"><?php __('Thank you message', 'ipAdmin'); ?></label>
        <textarea class="ipWidgetIpFormSuccess"><?php echo isset($success) ? esc($success, 'textarea') : ''; ?></textarea>
    </div>
</div>