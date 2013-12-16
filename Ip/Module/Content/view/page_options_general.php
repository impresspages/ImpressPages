<form id="formGeneral">
    <p class="field">
        <label for="generalButtonTitle">
            <?php _e('Button title', 'ipAdmin')?>
        </label>
        <input id="generalButtonTitle" name="buttonTitle" value="<?php echo esc($element->getButtonTitle()) ?>" />
        <br />
    </p>
    <p class="field">
        <label for="generalVisible">
            <?php _e('Visible', 'ipAdmin')?>
        </label>
        <input id="generalVisible" class="stdModBox" type="checkbox" name="visible" <?php echo $element->isVisible() ? 'checked="yes"' : '' ?> />
        <br />
    </p>
    <p class="field">
        <label for="generalCreatedOn">
            <?php _e('Created on', 'ipAdmin') ?>
        </label>
        <span class="error" id="createdOnError"></span> <input id="generalCreatedOn" name="createdOn"
            value="<?php echo esc(substr($element->getCreatedOn(), 0, 10)) ?>" /><br />
    </p>
    <p class="field">
        <label for="lastModifiedError">
            <?php _e('Last modified', 'ipAdmin') ?>
        </label>
        <span class="error" id="lastModifiedError">
        </span>
        <input id="generalLastModified" name="lastModified" value="<?php echo esc(substr($element->getLastModified(), 0, 10)) ?>" /><br />
    </p>
</form>
