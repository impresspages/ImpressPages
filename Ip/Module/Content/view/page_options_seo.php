<form id="formSEO">
    <p class="field">
        <label for="seoPageTitle"><?php echo _esc('Meta title', 'ipAdmin'); ?></label>
        <input id="seoPageTitle" name="pageTitle" value="<?php echo $this->esc($element->getPageTitle()); ?>" /><br />
    </p>
    <p class="field">
        <label for="seoKeywords"><?php echo _esc('Meta keywords', 'ipAdmin'); ?></label>
        <textarea id="seoKeywords" name="keywords"><?php echo $this->esc($element->getKeywords()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoDescription"><?php echo _esc('Meta description', 'ipAdmin'); ?></label>
        <textarea id="seoDescription" name="description"><?php echo $this->esc($element->getDescription()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoUrl"><?php echo _esc('URL', 'ipAdmin'); ?></label>
        <input id="seoUrl" name="url" value="<?php echo $this->esc($element->getURL()); ?>" />
        <br />
    </p>
</form>
