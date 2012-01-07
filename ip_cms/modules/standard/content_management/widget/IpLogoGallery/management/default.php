<div class="ipWidget_ipLogoGallery_container">
    <div class="ipaUpload"></div>
</div>
<div class="ipgHide">
    <div class="ipaLogoTemplate">
        <a href="#" class="ipaButton ipaLogoMove"><?php echo $this->escPar('standard/content_management/widget_logo_gallery/move') ?></a>
        <a href="#" class="ipaButton ipaLogoRemove"><?php echo $this->escPar('standard/content_management/widget_logo_gallery/remove') ?></a>
        <input type="text" class="ipAdminInput ipaLogoTitle" name="title" value="" />
        <div class="ipaLogo" style="width: 200px;"></div>
    </div>
</div>

<input type="hidden" name="logoWidth" value="<?php echo (int)$logoWidth; ?>" />
<input type="hidden" name="logoHeight" value="<?php echo (int)$logoHeight; ?>" />
