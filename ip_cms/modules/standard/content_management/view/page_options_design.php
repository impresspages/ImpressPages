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
            <?php echo $this->par('standard/menu_management/admin_translations/page_layout_instructions') ?>
        <?php } else { ?>
            <?php echo $this->par('standard/menu_management/admin_translations/page_layout_add_layout_instructions') ?>
        <?php } ?>

    <p/>
</form>