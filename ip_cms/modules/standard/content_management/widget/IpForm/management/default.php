<div class="ipWidget_ipForm_container">
</div>
<div class="ipgHide">
    <div class="ipaFieldTemplate">
        <a href="#" class="ipaButton ipaFieldMove"><?php echo $this->escPar('standard/content_management/widget_contact_form/move') ?></a>
        <input type="text" class="ipAdminInput ipaFieldLabel" name="label" value="" />
        <a href="#" class="ipaFieldOptions">Options</a>
        <select class="ipaFieldType" target="_blank">
            <?php foreach($fieldTypes as $fieldType) { ?>
                <option value="<?php echo $this->esc($fieldType['key']); ?>"><?php echo $this->esc($fieldType['title']); ?></option>
            <?php } ?>
        </select>
        <input type="checkbox" class="ipaFieldRequired" />
        <a href="#" class="ipaButton ipaFieldRemove"><?php echo $this->escPar('standard/content_management/widget_contact_form/remove') ?></a>
    </div>
    <div class="ipaOptionsPopup"></div>
</div>
<?php echo $addFieldForm->render(); ?>