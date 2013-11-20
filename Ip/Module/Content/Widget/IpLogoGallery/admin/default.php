<div class="ipWidget_ipLogoGallery_container">
    <a href="#" class="ipAdminButton ipmBrowseButton"><?php _e('Add new', 'ipAdmin') ?></a>
</div>
<div class="ipgHide">
    <div class="ipaLogoTemplate">
        <a href="#" class="ipaButton ipaLogoMove"><?php _e('Move', 'ipAdmin') ?></a>
        <a href="#" class="ipaButton ipaLogoRemove"><?php _e('Remove', 'ipAdmin') ?></a>
        <a href="#" class="ipaButton ipaLogoLink"><?php _e('Link', 'ipAdmin') ?></a>
        <br />
        <br />
        <input type="text" class="ipAdminInput ipaLogoTitle" name="title" value="" />
        <div class="ipaLogo" style="width: 200px;"></div>
    </div>
</div>

<input type="hidden" name="logoWidth" value="<?php echo (int)$logoWidth; ?>" />
<input type="hidden" name="logoHeight" value="<?php echo (int)$logoHeight; ?>" />
