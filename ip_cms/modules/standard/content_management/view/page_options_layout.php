<form id="formLayout">
    <p class="field">
        <label for="pageLayout"><?php echo $this->escPar('standard/menu_management/admin_translations/page_layout'); ?></label>
        <select name="layout" id="pageLayout">
            <option value="" <?php if (!$layout) { ?>selected="selected" <?php } ?>>
                <?php echo $this->escPar('standard/menu_management/admin_translations/default_zone_layout_option', array('layout' => $defaultLayout)) ?>
            </option>
            <?php foreach ($layouts as $layoutFile) { ?>
                <option value="<?php echo $this->esc($layoutFile) ?>" <?php if ($layoutFile == $layout) { ?>selected="selected"<?php } ?>>
                    <?php echo $this->escPar('standard/menu_management/admin_translations/custom_page_layout_option', array('layout' => $layoutFile)) ?>
                </option>
            <?php } ?>
        </select>
        <label for="ipContentManagementTypeSubpage" class="small">
            <?php /* echo $this->escPar('standard/menu_management/admin_translations/redirect_to_subpage') */ ?>
        </label>
        <br/>
        <br/>
        Layout change is immediate. Layout will change even without Publish.

    <p/>
</form>