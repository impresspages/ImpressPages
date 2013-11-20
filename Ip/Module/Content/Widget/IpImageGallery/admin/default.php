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

<input type="hidden" name="smallImageWidth" value="<?php echo (int)ipGetOption('Content.widgetImageGalleryWidth') ?>" />
<input type="hidden" name="smallImageHeight" value="<?php echo (int)ipGetOption('Content.widgetImageGalleryHeight') ?>" />
