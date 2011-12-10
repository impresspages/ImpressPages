<form id="formGeneral">
    <p class="field">
        <label for="generalButtonTitle">
            <?php $this->escPar('standard/menu_management/admin_translations/button_title')?>
        </label>
        <input id="generalButtonTitle" name="buttonTitle" value="<?php $this->esc($element->getButtonTitle()) ?>" />
        <br />
    </p>
    <p class="field">
        <label for="generalVisible">
            <?php $this->escPar('standard/menu_management/admin_translations/visible')?>
        </label>
        <input id="generalVisible" class="stdModBox" type="checkbox" name="visible" <?php echo $element->getVisible() ? 'checked="yes"' : '' ?> />
        <br />
    </p>
    <p class="field">
        <label for="generalCreatedOn">
            <?php $this->escPar('standard/menu_management/admin_translations/created_on') ?>
        </label>
        <span class="error" id="createdOnError"></span> <input id="generalCreatedOn" name="createdOn"
            value="<?php $this->esc(substr($element->getCreatedOn(), 0, 10)) ?>" /><br />
    </p>
    <p class="field">
        <label for="lastModifiedError">
            <?php $this->escPar('standard/menu_management/admin_translations/last_modified') ?>
        </label>
        <span class="error" id="lastModifiedError">
        </span>
        <input id="generalLastModified" name="lastModified" value="<?php $this->esc(substr($element->getLastModified(), 0, 10)) ?>" /><br />
    </p>
</form>
