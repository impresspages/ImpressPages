<form id="formLayout">
    <p class="field">
        <label for="pageLayout"><?php echo $this->escPar('standard/menu_management/admin_translations/page_layout'); ?></label>
        <?php if (count($layouts) > 1) { ?>
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
        <?php if (!empty($show_confirm_notification)) { ?>
    <p><?php echo $this->par('standard/menu_management/admin_translations/page_design_confirm_notification') ?></p>
    <?php } ?>
    <p><?php echo $this->par('standard/menu_management/admin_translations/page_layout_instructions') ?></p>
    <?php } else { ?>
        <?php echo $this->par('standard/menu_management/admin_translations/page_layout_add_layout_instructions') ?>
    <?php } ?>
    <p/>
    <?php if (!empty($show_submit_button)) { ?>
        <input class="submit" type="submit" value="Save">
    <?php } ?>
</form>