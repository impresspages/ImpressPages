<div class="ipWidget_ipImageGallery_container">
    <a href="#" class="ipAdminButton ipmBrowseButton"><?php _e('Add new', 'ipAdmin') ?></a>
</div>
<div class="ipgHide">
    <div class="ipaImageTemplate">
        <a href="#" class="ipaButton ipaImageMove"><?php _e('Move', 'ipAdmin') ?></a>
        <a href="#" class="ipaButton ipaImageRemove"><?php _e('Remove', 'ipAdmin') ?></a>
        <input type="text" class="ipAdminInput ipaImageTitle" name="title" value="" />
        <div class="ipaImage" style="width: 200px;"></div>
    </div>
</div>

<input type="hidden" name="smallImageWidth" value="<?php echo $this->escPar('Content.widget_image_gallery.width') ?>" />
<input type="hidden" name="smallImageHeight" value="<?php echo $this->escPar('Content.widget_image_gallery.height') ?>" />
