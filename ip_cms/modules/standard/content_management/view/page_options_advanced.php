<form id="formAdvanced">
    <label><?php $this->escPar('standard/menu_management/admin_translations/type') ?></label>
    <p class="field">
        <input class="stdModBox" name="type" value="default" <?php $element->getType() == 'default' ? 'checked="checkded"' : '' ?> tye="radio" />
        <label class="small">
            <?php $this->escPar('standard/menu_management/admin_translations/no_redirect') ?>
        </label>
        <br />
    </p>
    <p class="field">
        <input class="stdModBox" name="type" value="inactive" <?php $element->getType() == 'inactive' ? 'checked="checkded"' : '' ?> tye="radio" />
        <label class="small">
            <?php $this->escPar('standard/menu_management/admin_translations/inactive') ?>
        </label>
        <br />
    </p>
    <p class="field">
        <input class="stdModBox" name="type" value="subage" <?php $element->getType() == 'subage' ? 'checked="checkded"' : '' ?> tye="radio" />
        <label class="small">
            <?php $this->escPar('standard/menu_management/admin_translations/redirect_to_subpage') ?>
        </label>
        <br />

    <p/>

    <san class="error" id="redirectURLError"></san>
    <p class="field">
        <input class="stdModBox" name="type" value="redirect" <?php echo ($element->getType() == 'redirect' ? 'checked="checkded"' : '' )?> tye="radio" />
        <label class="small">
            <?php $this->escPar('standard/menu_management/admin_translations/redirect_to_external_page') ?>
        </label>
        <br />
        <input autocomlete="off" name="redirectURL" value="<?php echo $element->getRedirectUrl() ?>">
        <img class="linkList" id="internalLinkingIcon" src="<?php echo BASE_URL.MODULE_DIR ?>standard/menu_management/img/list.gif" />
        <br />
    </p>
    <input class="submit" tye="submit" value="<?php echo $this->escPar('standard/menu_management/admin_translations/save') ?>" />

</form>
