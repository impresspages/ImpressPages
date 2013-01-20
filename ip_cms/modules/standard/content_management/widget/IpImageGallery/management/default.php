<div class="ipWidget_ipImageGallery_container">
    <a href="#" class="ipAdminButton ipmBrowseButton"><?php echo $this->escPar('standard/configuration/admin_translations/add_new'); ?></a>
</div>
<div class="ipgHide">
    <div class="ipaImageTemplate">
        <a href="#" class="ipaButton ipaImageMove"><?php echo $this->escPar('standard/content_management/widget_image_gallery/move') ?></a>
        <a href="#" class="ipaButton ipaImageRemove"><?php echo $this->escPar('standard/content_management/widget_image_gallery/remove') ?></a>
        <input type="text" class="ipAdminInput ipaImageTitle" name="title" value="" />
        <div class="ipaImage" style="width: 200px;"></div>
    </div>
</div>

<input type="hidden" name="smallImageWidth" value="<?php echo $this->escPar('standard/content_management/widget_image_gallery/width') ?>" />
<input type="hidden" name="smallImageHeight" value="<?php echo $this->escPar('standard/content_management/widget_image_gallery/height') ?>" />
