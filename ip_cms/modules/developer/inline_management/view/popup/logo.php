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
                <li>Arial,Arial,Helvetica,sans-serif</li>
                <li>Arial Black,Arial Black,Gadget,sans-serif</li>
                <li>Comic Sans MS,Comic Sans MS,cursive</li>
                <li>Courier New,Courier New,Courier,monospace</li>
                <li>Georgia,Georgia,serif</li>
                <li>Impact,Charcoal,sans-serif</li>
                <li>Lucida Console,Monaco,monospace</li>
                <li>Lucida Sans Unicode,Lucida Grande,sans-serif</li>
                <li>Palatino Linotype,Book Antiqua,Palatino,serif</li>
                <li>Tahoma,Geneva,sans-serif</li>
                <li>Times New Roman,Times,serif</li>
                <li>Trebuchet MS,Helvetica,sans-serif</li>
                <li>Verdana,Geneva,sans-serif</li>
                <li>Gill Sans,Geneva,sans-serif</li>
            </ul>
        </div>
        <br/><br/>
        <div class="ipmColorPicker colorPickerSelector"></div>
    </div>
    <br/><br/>
    <div class="ipmImageManagement">
        <div class="ipaImage"></div>
    </div>
</form>
<hr/>
<br/>
<a class="ipAdminButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm'); ?></a>
<a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel'); ?></a>