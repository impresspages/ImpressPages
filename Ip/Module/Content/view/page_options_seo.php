<form id="formSEO">
    <p class="field">
        <label for="seoPageTitle"><?php _e('Meta title', 'ipAdmin'); ?></label>
        <input id="seoPageTitle" name="pageTitle" value="<?php echo esc($element->getPageTitle()); ?>" /><br />
    </p>
    <p class="field">
        <label for="seoKeywords"><?php _e('Meta keywords', 'ipAdmin'); ?></label>
        <textarea id="seoKeywords" name="keywords"><?php echo esc($element->getKeywords()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoDescription"><?php _e('Meta description', 'ipAdmin'); ?></label>
        <textarea id="seoDescription" name="description"><?php echo esc($element->getDescription()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoUrl"><?php _e('URL', 'ipAdmin'); ?></label>
        <input id="seoUrl" name="url" value="<?php echo esc($element->getURL()); ?>" />
        <br />
    </p>
</form>
