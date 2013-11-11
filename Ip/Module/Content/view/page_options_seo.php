<form id="formSEO">
    <p class="field">
        <label for="seoPageTitle"><?php echo $this->escPar('Pages.page_title'); ?></label>
        <input id="seoPageTitle" name="pageTitle" value="<?php echo $this->esc($element->getPageTitle()); ?>" /><br />
    </p>
    <p class="field">
        <label for="seoKeywords"><?php echo $this->escPar('Pages.keywords'); ?></label>
        <textarea id="seoKeywords" name="keywords"><?php echo $this->esc($element->getKeywords()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoDescription"><?php echo $this->escPar('Pages.description'); ?></label>
        <textarea id="seoDescription" name="description"><?php echo $this->esc($element->getDescription()); ?></textarea>
        <br />
    </p>
    <p class="field">
        <label for="seoUrl"><?php echo $this->escPar('Pages.url'); ?></label>
        <input id="seoUrl" name="url" value="<?php echo $this->esc($element->getURL()); ?>" />
        <br />
    </p>
</form>
