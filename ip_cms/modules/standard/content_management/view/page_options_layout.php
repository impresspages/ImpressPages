<form id="formLayout">
    <label>layout</label>

    <p class="field">
        <select name="layout">
            <?php foreach ($layouts as $layoutFile => $layoutLabel) { ?>
                <option value="<?php echo $this->esc($layoutFile) ?>" <?php if ($layoutFile == $layout) { ?>selected="selected"<?php } ?>><?php echo $this->esc($layoutLabel) ?></option>
            <?php } ?>
        </select>
        <label for="ipContentManagementTypeSubpage" class="small">
            <?php /* echo $this->escPar('standard/menu_management/admin_translations/redirect_to_subpage') */ ?>
        </label>
        <br/>

        Layout change is immediate. Layout will change even without Publish.

    <p/>
</form>