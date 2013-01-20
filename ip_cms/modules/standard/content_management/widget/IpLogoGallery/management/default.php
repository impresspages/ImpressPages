<div class="ipWidget_ipLogoGallery_container">
    <a href="#" class="ipAdminButton ipmBrowseButton"><?php echo $this->escPar('standard/configuration/admin_translations/add_new'); ?></a>
</div>
<div class="ipgHide">
    <div class="ipaLogoTemplate">
        <a href="#" class="ipaButton ipaLogoMove"><?php echo $this->escPar('standard/content_management/widget_logo_gallery/move') ?></a>
        <a href="#" class="ipaButton ipaLogoRemove"><?php echo $this->escPar('standard/content_management/widget_logo_gallery/remove') ?></a>
        <a href="#" class="ipaButton ipaLogoLink">Link</a>
        <br />
        <br />
        <input type="text" class="ipAdminInput ipaLogoTitle" name="title" value="" />
        <div class="ipaLogo" style="width: 200px;"></div>
    </div>
</div>

<input type="hidden" name="logoWidth" value="<?php echo (int)$logoWidth; ?>" />
<input type="hidden" name="logoHeight" value="<?php echo (int)$logoHeight; ?>" />
