<form onsubmit="return false;">
    <select class="ipmType">
        <option value="text"><?php echo $this->escPar('developer/inline_management/admin_translations/type_text'); ?></option>
        <option value="image"><?php echo $this->escPar('developer/inline_management/admin_translations/type_image'); ?></option>
    </select>
    <br/><br/>
    <div class="ipmTextManagement">
        <input class="ipmLogoText" type="text" value="" />
        <br/><br/>
        <div class="ipmFontSelect">
            <span>Arial</span>
            <div class="arrow-down"></div>
            <ul>
                <?php if (isset($availableFonts) && is_array($availableFonts)) foreach($availableFonts as $font) { ?>
                    <li><?php echo $this->esc($font) ?></li>
                <?php } ?>
            </ul>
        </div>
        <br/><br/>
        <div class="ipmColorPicker colorPickerSelector"></div>
        <br/><br/>
    </div>
    <div class="ipmImageManagement">
        <div class="ipaImage"></div>
    </div>
</form>
<hr/>
<br/>
<a class="ipAdminButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm'); ?></a>
<a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel'); ?></a>