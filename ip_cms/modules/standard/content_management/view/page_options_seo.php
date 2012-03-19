<form id="formSEO">
    <p class="field">
        <label for="seoPageTitle"><?php echo $this->escPar('standard/menu_management/admin_translations/page_title'); ?></label>
        <input id="seoPageTitle" name="pageTitle" value="<?php echo $this->esc($element->getPageTitle()); ?>" /><br />
    </p>
    <p class="field">
        <label for="seoKeywords"><?php echo $this->escPar('standard/menu_management/admin_translations/keywords'); ?></label>
        <textarea id="seoKeywords" name="keywords"><?php echo $this->esc($element->getKeywords()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoDescription"><?php echo $this->escPar('standard/menu_management/admin_translations/description'); ?></label>
        <textarea id="seoDescription" name="description"><?php echo $this->esc($element->getDescription()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoUrl"><?php echo $this->escPar('standard/menu_management/admin_translations/url'); ?></label>
        <input id="seoUrl" name="url" value="<?php echo $this->esc($element->getURL()); ?>" />
        <br />
    </p>
</form>
