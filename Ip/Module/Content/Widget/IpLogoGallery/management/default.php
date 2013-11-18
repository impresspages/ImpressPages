<div class="ipWidget_ipLogoGallery_container">
    <a href="#" class="ipAdminButton ipmBrowseButton"><?php echo __('Add new', 'ipAdmin'); ?></a>
</div>
<div class="ipgHide">
    <div class="ipaLogoTemplate">
        <a href="#" class="ipaButton ipaLogoMove"><?php echo __('Move', 'ipAdmin') ?></a>
        <a href="#" class="ipaButton ipaLogoRemove"><?php echo __('Remove', 'ipAdmin') ?></a>
        <a href="#" class="ipaButton ipaLogoLink"><?php echo __('Link', 'ipAdmin') ?></a>
        <br />
        <br />
        <input type="text" class="ipAdminInput ipaLogoTitle" name="title" value="" />
        <div class="ipaLogo" style="width: 200px;"></div>
    </div>
</div>

<input type="hidden" name="logoWidth" value="<?php echo (int)$logoWidth; ?>" />
<input type="hidden" name="logoHeight" value="<?php echo (int)$logoHeight; ?>" />
