<form id="formAdvanced">
    <label><?php echo $this->escPar('standard/menu_management/admin_translations/type') ?></label>
    <p class="field">
        <input id="ipContentManagementTypeDefault" class="stdModBox" name="type" value="default" <?php echo $element->getType() == 'default' ? 'checked="checked"' : '' ?> type="radio" />
        <label for="ipContentManagementTypeDefault" class="small">
            <?php echo $this->escPar('standard/menu_management/admin_translations/no_redirect') ?>
        </label>
        <br />
    </p>
    <p class="field">
        <input id="ipContentManagementTypeInactive" class="stdModBox" name="type" value="inactive" <?php echo $element->getType() == 'inactive' ? 'checked="checked"' : '' ?> type="radio" />
        <label for="ipContentManagementTypeInactive" class="small">
            <?php echo $this->escPar('standard/menu_management/admin_translations/inactive') ?>
        </label>
        <br />
    </p>
    <p class="field">
        <input id="ipContentManagementTypeSubpage" class="stdModBox" name="type" value="subpage" <?php echo $element->getType() == 'subpage' ? 'checked="checked"' : '' ?> type="radio" />
        <label for="ipContentManagementTypeSubpage" class="small">
            <?php echo $this->escPar('standard/menu_management/admin_translations/redirect_to_subpage') ?>
        </label>
        <br />

    <p/>

    <san class="error" id="redirectURLError"></san>
    <p class="field">
        <input id="ipContentManagementTypeRedirect" class="stdModBox" name="type" value="redirect" <?php echo ($element->getType() == 'redirect' ? 'checked="checked"' : '' )?> type="radio" />
        <label for="ipContentManagementTypeRedirect" class="small">
            <?php echo $this->escPar('standard/menu_management/admin_translations/redirect_to_external_page') ?>
        </label>
        <br />
        <input autocomlete="off" name="redirectURL" value="<?php echo $element->getRedirectUrl() ?>">
        <span>Please use "Menu Management" tab for internal linkig options</span>
        <br />
    </p>
    <p class="field">
        <label for="ipContentManagementRss">
            <?php echo $this->escPar('standard/menu_management/admin_translations/rss')?>
        </label>
        <input id="ipContentManagementRss" class="stdModBox" type="checkbox" name="rss" <?php echo $element->getRSS() ? 'checked="yes"' : '' ?> />
        <br />
    </p>

</form>
