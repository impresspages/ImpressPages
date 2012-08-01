<form onsubmit="return false;">
    <input type="radio" name="logo_type"/><span><?php echo $this->escPar('developer/inline_management/admin_translations/type_text'); ?></span>
    <div class="ipAdminTextManagement">
        Text edit
    </div>
    <input type="radio" name="logo_type" /><span><?php echo $this->escPar('developer/inline_management/admin_translations/type_image'); ?></span>
    <div class="ipAdminImageManagement">
        Image edit
        <div class="ipaImage"></div>
    </div>

</form>

<a class="ipAdminButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm'); ?></a>
<a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel'); ?></a>